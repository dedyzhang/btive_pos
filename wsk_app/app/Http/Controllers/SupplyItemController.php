<?php

namespace App\Http\Controllers;

use App\Models\SupplyItem;
use Illuminate\Http\Request;

class SupplyItemController extends Controller
{
    /**
     * Master data page for kitchen supplies (admin).
     */
    public function index()
    {
        $supplyItems = SupplyItem::orderBy('sort', 'asc')->orderBy('name', 'asc')->get();
        $purchaseStats = SupplyItem::purchaseStats();

        return view('supply_item.index', compact('supplyItems', 'purchaseStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:30',
            'purchase_unit' => 'nullable|string|max:30',
            'purchase_conversion' => 'nullable|numeric|min:0.0001',
            'unit_price' => 'nullable|integer|min:0',
            'stock' => 'nullable|numeric',
            'par_level' => 'nullable|numeric|min:0',
        ]);

        SupplyItem::create([
            'name' => $request->name,
            'unit' => $request->unit,
            // A conversion only means anything paired with a purchase unit — drop one if
            // the other is missing so the item doesn't end up half-configured.
            'purchase_unit' => $request->purchase_conversion ? $request->purchase_unit : null,
            'purchase_conversion' => $request->purchase_unit ? $request->purchase_conversion : null,
            'unit_price' => $request->unit_price ?: 0,
            'stock' => $request->stock ?: 0,
            'par_level' => $request->par_level ?: 0,
            'is_active' => true,
            'sort' => (SupplyItem::max('sort') ?? 0) + 1,
        ]);

        return redirect()->back()->with('success_supply', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, String $uuid)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:30',
            'purchase_unit' => 'nullable|string|max:30',
            'purchase_conversion' => 'nullable|numeric|min:0.0001',
            'unit_price' => 'nullable|integer|min:0',
            'stock' => 'nullable|numeric',
            'par_level' => 'nullable|numeric|min:0',
        ]);

        $supplyItem = SupplyItem::findOrFail($uuid);

        $supplyItem->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'purchase_unit' => $request->purchase_conversion ? $request->purchase_unit : null,
            'purchase_conversion' => $request->purchase_unit ? $request->purchase_conversion : null,
            'unit_price' => $request->unit_price ?? $supplyItem->unit_price,
            'par_level' => $request->par_level ?? $supplyItem->par_level,
            // Lets an admin correct stock after a stock-opname; auto-deduction keeps it
            // moving day to day, but a manual reset is still the escape hatch when it drifts.
            'stock' => $request->stock ?? $supplyItem->stock,
        ]);

        return redirect()->back()->with('success_supply', 'Barang berhasil diperbarui.');
    }

    /**
     * Soft-hide an item from the shopping checklist without breaking past requests
     * (which keep their own name/unit snapshot).
     */
    public function toggleActive(String $uuid)
    {
        $supplyItem = SupplyItem::findOrFail($uuid);
        $supplyItem->update(['is_active' => !$supplyItem->is_active]);

        return redirect()->back()->with('success_supply', 'Status barang diperbarui.');
    }

    public function destroy(String $uuid)
    {
        $supplyItem = SupplyItem::findOrFail($uuid);

        // Past requests reference this row, so refuse a hard delete and point at deactivation.
        if ($supplyItem->purchaseRequestItems()->exists()) {
            return redirect()->back()->withErrors([
                'delete' => 'Barang ini sudah dipakai di riwayat pengajuan. Nonaktifkan saja agar riwayat tetap utuh.',
            ]);
        }

        $supplyItem->delete();

        return redirect()->back()->with('success_supply', 'Barang dihapus.');
    }
}
