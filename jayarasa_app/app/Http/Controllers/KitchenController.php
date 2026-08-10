<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Render the main kitchen queue view.
     */
    public function index()
    {
        return view('kitchen.queue');
    }

    /**
     * Return active and completed transactions without price details as JSON.
     */
    public function getLiveUpdates()
    {
        $cooking = Transactions::with(['table', 'orderItem'])
            ->whereDate('created_at', today())
            ->where('kitchen_status', 'cooking')
            ->orderBy('created_at', 'asc')
            ->get();

        $completed = Transactions::with(['table', 'orderItem'])
            ->whereDate('created_at', today())
            ->where('kitchen_status', 'ready')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $sanitize = function ($txs) {
            return $txs->map(function ($tx) {
                return [
                    'uuid' => $tx->uuid,
                    'invoice_number' => $tx->invoice_number,
                    'customer_name' => $tx->customer_name,
                    'order_type' => $tx->order_type,
                    'status' => $tx->status,
                    'kitchen_status' => $tx->kitchen_status,
                    'created_at' => $tx->created_at->toIso8601String(),
                    'updated_at' => $tx->updated_at->toIso8601String(),
                    'table' => $tx->table ? [
                        'name' => $tx->table->name,
                    ] : null,
                    'order_item' => $tx->orderItem->map(function ($item) {
                        return [
                            'uuid' => $item->uuid,
                            'product_name' => $item->product_name,
                            'qty' => $item->qty,
                            'note' => $item->note,
                            'is_kitchen_done' => (bool) $item->is_kitchen_done,
                        ];
                    }),
                ];
            });
        };

        return response()->json([
            'success'   => true,
            'time'      => now()->toIso8601String(),
            'cooking'   => $sanitize($cooking),
            'completed' => $sanitize($completed),
        ]);
    }

    /**
     * Update the kitchen status of a transaction.
     */
    public function updateStatus(Request $request, string $uuid)
    {
        $request->validate([
            'status' => 'required|in:cooking,ready',
        ]);

        $transaction = Transactions::with('orderItem')->findOrFail($uuid);
        $transaction->update([
            'kitchen_status' => $request->status,
        ]);

        // Cascade to update all item checklist states
        $isDone = $request->status === 'ready';
        $transaction->orderItem()->update([
            'is_kitchen_done' => $isDone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status antrean berhasil diperbarui.',
        ]);
    }

    /**
     * Update the status of an individual item in the queue.
     */
    public function updateItemStatus(Request $request, string $itemUuid)
    {
        $request->validate([
            'is_kitchen_done' => 'required|boolean',
        ]);

        $item = \App\Models\TransactionDetails::findOrFail($itemUuid);
        $item->update([
            'is_kitchen_done' => $request->is_kitchen_done,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status item berhasil diperbarui.',
        ]);
    }
}
