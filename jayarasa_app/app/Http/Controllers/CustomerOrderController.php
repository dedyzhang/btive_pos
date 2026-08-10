<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Tables;
use App\Models\Transactions;
use App\Models\TransactionDetails;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    /**
     * Show the menu page for customer self-ordering.
     */
    public function index(string $table_uuid)
    {
        $table = Tables::where('uuid', $table_uuid)->firstOrFail();
        
        // Fetch categories with active products
        $categories = Categories::with(['products' => function($q) {
            $q->where('is_active', 1)->orderBy('created_at', 'asc');
        }])->orderBy('sort', 'asc')->get();
        
        $products = Products::where('is_active', 1)->orderBy('created_at', 'asc')->get();

        // Best seller products (based on paid transactions)
        $bestSellerProductIds = TransactionDetails::whereHas('order', function($q) {
                $q->where('status', 'paid');
            })
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->pluck('product_id')
            ->toArray();

        // Settings (restaurant info)
        $setting = Settings::whereIn('jenis', ['restaurant_settings', 'restaurant_logo'])->get();
        $restaurant_setting = $setting->firstWhere('jenis', 'restaurant_settings');
        $restaurant_logo = $setting->firstWhere('jenis', 'restaurant_logo');

        $imgPath = ($restaurant_logo && $restaurant_logo->nilai) ? asset('storage/' . $restaurant_logo->nilai) : "";
        $resSetting = [];
        if ($restaurant_setting && $restaurant_setting->nilai) {
            $resSetting = @unserialize($restaurant_setting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurant_setting->nilai)) ?: [];
            }
        }
        $resName = $resSetting['name'] ?? 'Restaurant';
        $resLocation = $resSetting['location'] ?? '';
        $accentColor = $resSetting['accent_color'] ?? '#2b66ff';

        return view('customer_order.index', compact(
            'table', 'categories', 'products', 'bestSellerProductIds', 'imgPath', 'resName', 'resLocation', 'accentColor'
        ));
    }

    /**
     * Submit order from customer.
     */
    public function submit(string $table_uuid, Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,uuid',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.note' => 'nullable|string|max:255',
        ]);

        $table = Tables::where('uuid', $table_uuid)->firstOrFail();

        // Fetch fallback user (admin) since user_id is NOT NULL in transactions
        $fallbackUser = User::where('role', 'admin')->first() ?: User::first();
        if (!$fallbackUser) {
            return response()->json(['success' => false, 'message' => 'Sistem belum siap. Silakan hubungi kasir.'], 500);
        }

        // Generate invoice number
        $invoiceNumber = 'QR-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

        DB::beginTransaction();
        try {
            // 1. Create Transaction Shell
            $transaction = Transactions::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $fallbackUser->uuid,
                'table_id' => $table->uuid,
                'customer_name' => $request->customer_name . ' (Self)',
                'order_type' => 'dine_in',
                'status' => 'process', // Send straight to active cashier list
                'kitchen_status' => 'cooking', // Send straight to kitchen queue
            ]);

            // 2. Add transaction details and compute subtotal
            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $product = Products::where('uuid', $itemData['product_id'])->firstOrFail();
                $itemSubtotal = $product->price * $itemData['qty'];
                $subtotal += $itemSubtotal;

                TransactionDetails::create([
                    'order_id' => $transaction->uuid,
                    'product_id' => $product->uuid,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $itemData['qty'],
                    'note' => $itemData['note'] ?? null,
                    'subtotal' => $itemSubtotal,
                    'is_kitchen_done' => false
                ]);
            }

            // 3. Compute tax and total
            $tax_setting = Settings::where('jenis', 'payment_tax')->first();
            $tax = ($tax_setting && $tax_setting->nilai > 0) ? ($subtotal * $tax_setting->nilai) / 100 : 0;
            $total = $subtotal + $tax;

            // 4. Update transaction totals
            $transaction->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dikirim ke dapur!',
                'redirect' => route('customer.order.status', $transaction->uuid)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show tracking status page for customers.
     */
    public function status(string $transaction_uuid)
    {
        $transaction = Transactions::with(['table', 'orderItem'])->where('uuid', $transaction_uuid)->firstOrFail();

        // Settings (restaurant info)
        $setting = Settings::whereIn('jenis', ['restaurant_settings', 'restaurant_logo'])->get();
        $restaurant_setting = $setting->firstWhere('jenis', 'restaurant_settings');
        $restaurant_logo = $setting->firstWhere('jenis', 'restaurant_logo');

        $imgPath = ($restaurant_logo && $restaurant_logo->nilai) ? asset('storage/' . $restaurant_logo->nilai) : "";
        $resSetting = [];
        if ($restaurant_setting && $restaurant_setting->nilai) {
            $resSetting = @unserialize($restaurant_setting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurant_setting->nilai)) ?: [];
            }
        }
        $resName = $resSetting['name'] ?? 'Restaurant';
        $accentColor = $resSetting['accent_color'] ?? '#2b66ff';

        return view('customer_order.status', compact('transaction', 'imgPath', 'resName', 'accentColor'));
    }

    /**
     * Retrieve live status updates for polling.
     */
    public function liveStatus(string $transaction_uuid)
    {
        $transaction = Transactions::where('uuid', $transaction_uuid)->firstOrFail();

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'kitchen_status' => $transaction->kitchen_status,
            'updated_at' => $transaction->updated_at->toIso8601String(),
        ]);
    }
}
