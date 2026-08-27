<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\SupplyItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $supplyItems = SupplyItem::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $opnames = StockOpname::with(['items.supplyItem', 'user'])
            ->orderBy('opname_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
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

        return view('stock_opname.index', compact('supplyItems', 'opnames', 'resName'));
    }

    /**
     * Record a physical count: store the variance against what the system believed, then
     * reset stock to the counted figure so the running number starts from reality again.
     */
    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.supply_item_id' => 'required|exists:supply_items,uuid',
            'items.*.actual_stock' => 'nullable|numeric',
        ], [
            'items.required' => 'Isi minimal satu hitungan fisik.',
        ]);

        // Only rows the user actually filled in are counted — leaving an item blank means
        // "not counted this time", which must not be read as "zero on hand".
        $countedRows = array_filter(
            $request->items,
            fn ($row) => isset($row['actual_stock']) && $row['actual_stock'] !== ''
        );

        if (empty($countedRows)) {
            return redirect()->back()
                ->withErrors(['items' => 'Isi minimal satu hitungan fisik. Kolom kosong dianggap tidak dihitung.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $opname = StockOpname::create([
                'user_id' => Auth::id(),
                'opname_date' => $request->opname_date,
                'note' => $request->note,
            ]);

            foreach ($countedRows as $row) {
                $supplyItem = SupplyItem::findOrFail($row['supply_item_id']);

                $systemStock = (float) $supplyItem->stock;
                $actualStock = (float) $row['actual_stock'];

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->uuid,
                    'supply_item_id' => $supplyItem->uuid,
                    'system_stock' => $systemStock,
                    'actual_stock' => $actualStock,
                    // Negative = short (waste/shrinkage), positive = more than expected.
                    'variance' => $actualStock - $systemStock,
                    'item_name' => $supplyItem->name,
                    'unit' => $supplyItem->unit,
                ]);

                $supplyItem->update(['stock' => $actualStock]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['items' => 'Gagal menyimpan opname: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('stock-opname.index')
            ->with('success_opname', 'Stock opname tersimpan. Stok sistem sudah disesuaikan ke hitungan fisik.');
    }

    /**
     * Deleting an opname only removes the record and its variance history — stock is left
     * as-is, since later counts and sales have already moved on from that point.
     */
    public function destroy(String $uuid)
    {
        StockOpname::findOrFail($uuid)->delete();

        return redirect()->back()->with('success_opname', 'Riwayat opname dihapus. Stok saat ini tidak diubah.');
    }
}
