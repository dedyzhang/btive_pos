<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Settings;
use App\Models\SupplyItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    /**
     * Shopping-list page: the checklist of supplies to buy, plus recent requests.
     */
    public function index()
    {
        $supplyItems = SupplyItem::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Shows staff when each item was last bought and how fast it usually runs out,
        // so they can judge what needs restocking without counting anything.
        $purchaseStats = SupplyItem::purchaseStats();

        $requests = PurchaseRequest::with(['items', 'user', 'purchaser'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        // Used as the header of the WhatsApp message.
        $resName = 'Restoran';
        $restaurantSetting = Settings::where('jenis', 'restaurant_settings')->first();
        if ($restaurantSetting && $restaurantSetting->nilai) {
            $resSetting = @unserialize($restaurantSetting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurantSetting->nilai)) ?: [];
            }
            $resName = $resSetting['name'] ?? 'Restoran';
        }

        return view('purchase_request.index', compact('supplyItems', 'requests', 'resName', 'purchaseStats'));
    }

    /**
     * Submit a new shopping list (cashier/kitchen).
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.supply_item_id' => 'required|exists:supply_items,uuid',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.note' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Pilih minimal satu barang yang perlu dibeli.',
        ]);

        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::create([
                'user_id' => Auth::id(),
                'request_date' => $request->request_date,
                'status' => 'pending',
                'note' => $request->note,
            ]);

            foreach ($request->items as $item) {
                $supplyItem = SupplyItem::findOrFail($item['supply_item_id']);

                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->uuid,
                    'supply_item_id' => $supplyItem->uuid,
                    'qty' => $item['qty'],
                    // Snapshot so past lists stay readable if the master item is renamed later.
                    'item_name' => $supplyItem->name,
                    'unit' => $supplyItem->unit,
                    'note' => $item['note'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['items' => 'Gagal menyimpan pengajuan: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('purchase-request.index')->with('success_request', 'Pengajuan belanja berhasil dibuat.');
    }

    /**
     * Mark a request as purchased. Stamping purchased_at is what feeds the per-item purchase
     * history (last bought / how often), and the qty bumps each item's stock directly — the
     * quick path for purchases that aren't (yet, or ever) logged as a Cash Flow expense.
     * A purchase recorded via Cash Flow instead closes the request the same way, through
     * CashFlowController::applyPurchaseSideEffects().
     * Admin-only (enforced by the route's permission middleware).
     *
     * The actual qty bought often differs from what was requested (different pack size,
     * out of stock, etc.), so this is the one point where the admin can correct it —
     * the corrected figure is what purchaseStats() then reads as "last purchased qty",
     * and what actually gets added to stock.
     */
    public function markPurchased(Request $request, String $uuid)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($uuid);

        if ($purchaseRequest->status !== 'pending') {
            return redirect()->back()->withErrors([
                'status' => 'Pengajuan ini sudah diproses sebelumnya (status: ' . $purchaseRequest->status . ').',
            ]);
        }

        $request->validate([
            'items' => 'nullable|array',
            'items.*.uuid' => 'required_with:items|exists:purchase_request_items,uuid',
            'items.*.qty' => 'required_with:items|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $submittedQty = collect($request->input('items', []))->keyBy('uuid');

            foreach ($purchaseRequest->items as $item) {
                $qty = $submittedQty->has($item->uuid)
                    ? (float) $submittedQty[$item->uuid]['qty']
                    : (float) $item->qty;

                if ($submittedQty->has($item->uuid)) {
                    $item->update(['qty' => $qty]);
                }

                SupplyItem::where('uuid', $item->supply_item_id)->increment('stock', $qty);
            }

            $purchaseRequest->update([
                'status' => 'purchased',
                'purchased_at' => now(),
                'purchased_by' => Auth::id(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['status' => 'Gagal memproses: ' . $e->getMessage()]);
        }

        return redirect()->back()->with('success_request', 'Pengajuan ditandai sudah dibeli. Stok barang bertambah sesuai jumlah yang dikonfirmasi.');
    }

    /**
     * Cancel a pending request (admin-only, same as markPurchased).
     */
    public function cancel(String $uuid)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($uuid);

        if ($purchaseRequest->status !== 'pending') {
            return redirect()->back()->withErrors([
                'status' => 'Hanya pengajuan berstatus pending yang bisa dibatalkan.',
            ]);
        }

        $purchaseRequest->update(['status' => 'cancelled']);

        return redirect()->back()->with('success_request', 'Pengajuan dibatalkan.');
    }

    /**
     * Delete a request entirely (admin-only). Deleting a purchased request also removes it
     * from the item's purchase history, which is the intended way to undo a wrong entry —
     * and since markPurchased() added its qty to stock, that qty is reversed here too, so a
     * mistaken entry doesn't leave phantom stock behind.
     */
    public function destroy(String $uuid)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($uuid);

        DB::transaction(function () use ($purchaseRequest) {
            if ($purchaseRequest->status === 'purchased') {
                foreach ($purchaseRequest->items as $item) {
                    SupplyItem::where('uuid', $item->supply_item_id)->decrement('stock', (float) $item->qty);
                }
            }

            $purchaseRequest->delete();
        });

        return redirect()->back()->with('success_request', 'Pengajuan dihapus.');
    }
}
