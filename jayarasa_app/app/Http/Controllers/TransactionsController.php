<?php

namespace App\Http\Controllers;

use App\Models\ProductRecipe;
use App\Models\Products;
use App\Models\Settings;
use App\Models\SupplyItem;
use App\Models\TransactionDetails;
use App\Models\Transactions;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\GdEscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class TransactionsController extends Controller
{
    public function create() {
        $user = Auth::user();
        $user_id = $user->uuid;
        $invoice_number = date('Ymd').rand('1000','9999');

        $newTransaction = Transactions::create([
            'invoice_number' => $invoice_number,
            'user_id' => $user_id,
            'status' => 'active'
        ]);
        
        $transaction = Transactions::with('table','orderItem')->findOrFail($newTransaction->uuid);

        return response()->json(['success'=> true, 'transaction' => $transaction]);
    }
    public function show(String $uuid) {
        $transaction = Transactions::with('table','orderItem','user')->findOrFail($uuid);
        $product = Products::where('is_active',1)->get();

        return response()->json(["success" => true,'transaction' => $transaction,'product' => $product]);
    }

    /**
     * Delete Transaction
     */
    public function delete(Request $request, String $uuid) {
        $transaction = Transactions::findOrFail($uuid);
        $user = Auth::user();

        // Check if transaction is processed (status is not 'active' or has been submitted/processed before)
        if ($transaction->status !== 'active' || $transaction->subtotal !== null) {
            // Cashiers/non-admins must provide admin password approval
            if ($user->role !== 'admin') {
                if (!$request->admin_password) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Persetujuan Admin diperlukan untuk menghapus transaksi terproses.'
                    ], 422);
                }

                // Verify admin password
                $admins = User::where('role', 'admin')->get();
                $isApproved = false;
                foreach ($admins as $admin) {
                    if (\Illuminate\Support\Facades\Hash::check($request->admin_password, $admin->password)) {
                        $isApproved = true;
                        break;
                    }
                }

                if (!$isApproved) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password Admin salah! Penghapusan transaksi membutuhkan persetujuan Admin yang valid.'
                    ], 422);
                }
            }
        }

        $transaction->orderItem()->delete();
        $transaction->delete();
        return response()->json(['success' => true]);
    }
    // Update Transaction
    public function update(String $uuid) {
        $transaction = Transactions::with('table','orderItem','user')->findOrFail($uuid);
        $product = Products::where('is_active',1)->get();
        $transaction->update([
            'status' => 'active',
            'kitchen_status' => 'pending'
        ]);
        return response()->json(['success' => true, 'transaction' => $transaction, 'product' => $product]);
    }

    /**
     * Create Order per transaction
     */
    public function createOrder(String $uuid,Request $request) {
        $transaction = Transactions::findOrFail($uuid);
        
        if ($request->is_manual) {
            // Item manual: tidak perlu produk di database, simpan product_id = null
            $productName = $request->custom_name ?: 'Item Manual';
            $price = floatval($request->custom_price ?: 0);
            $qty = intval($request->qty ?: 1);
            
            $orderItem = TransactionDetails::create([
                'order_id'     => $transaction->uuid,
                'product_id'   => null,
                'product_name' => $productName,
                'price'        => $price,
                'qty'          => $qty,
                'note'         => $request->description,
                'subtotal'     => $price * $qty
            ]);
            
            return response()->json(['success' => true, 'product' => null, 'orderItem' => $orderItem]);
        }
        
        $product = Products::findOrFail($request->idProduct);
        
        $orderItem = TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $request->idProduct,
            'product_name' => $product->name,
            'price' => $product->price,
            'qty' => $request->qty,
            'note' => $request->description,
            'subtotal' => $product->price * $request->qty
        ]);

        return response()->json(['success' => true,'product' => $product, 'orderItem' => $orderItem]);
    }
    /**
     * Decrement Order per transaction
     */
    public function decrementOrder(String $uuid) {
        $orderItem = TransactionDetails::findOrFail($uuid);
        // product_id bisa null untuk item manual
        $product = $orderItem->product_id ? Products::find($orderItem->product_id) : null;
    
        if($orderItem->qty > 1) {
            $orderItem->qty -= 1;
            $orderItem->subtotal = $orderItem->price * $orderItem->qty;
            $orderItem->save();
            return response()->json(['success' => true,'product' => $product, 'orderItem' => $orderItem]);
        } else {
            $orderItem->delete();
            return response()->json(['success' => true,'product' => $product, 'orderItem' => null]);
        }

    }
    /**
     * Increment Order per transaction
     */
    public function incrementOrder(String $uuid) {
        $orderItem = TransactionDetails::findOrFail($uuid);
        // product_id bisa null untuk item manual
        $product = $orderItem->product_id ? Products::find($orderItem->product_id) : null;

        $orderItem->qty += 1;
        $orderItem->subtotal = $orderItem->price * $orderItem->qty;
        $orderItem->save();

        return response()->json(['success' => true,'product' => $product, 'orderItem' => $orderItem]);
    }
    /**
     * Change Qty Order per transaction
     */
    public function changeQtyOrder(String $uuid, Request $request) {
        $orderItem = TransactionDetails::findOrFail($uuid);
        // product_id bisa null untuk item manual
        $product = $orderItem->product_id ? Products::find($orderItem->product_id) : null;
        $oldTotal = $orderItem->subtotal;
        $orderItem->qty = $request->qty;
        $orderItem->subtotal = $orderItem->price * $orderItem->qty;
        $orderItem->save();

        return response()->json(['success' => true,'product' => $product, 'orderItem' => $orderItem, 'oldSubtotal' => $oldTotal]);
    }
    /**
     * Change Table Order per transaction
     */
    public function changeTableOrder(String $uuid, Request $request) {
        $transaction = Transactions::with('table')->findOrFail($uuid);
        $transaction->update([
            'table_id' => $request->table_id
        ]);
        $orderItem = Transactions::with('table')->findOrFail($uuid);

        return response()->json(['success' => true,'orderItem' => $orderItem]);
    }
    /**
     * Change Order Type per transaction
     */
    public function changeOrderType(String $uuid, Request $request) {
        $transaction = Transactions::with('table')->findOrFail($uuid);
        $transaction->update([
            'order_type' => $request->order
        ]);
        return response()->json(['success' => true]);
    }
    /**
     * Change Name Order per transaction
     */
    public function changeNameOrder(String $uuid, Request $request) {
        $transaction = Transactions::findOrFail($uuid);
        $transaction->update([
            'customer_name' => $request->name
        ]);
        return response()->json(['success' => true]);
    }
    /**
     * Get Note Order per transaction
     */
    public function getNoteOrder(String $uuid) {
        $orderItem = TransactionDetails::findOrFail($uuid);
        return response()->json(['success' => true, 'note' => $orderItem->note]);
    }
    /**
     * Change Note Order per transaction
     */
    public function changeNoteOrder(String $uuid, Request $request) {
        $orderItem = TransactionDetails::findOrFail($uuid);
        $orderItem->update([
            'note' => $request->note
        ]);
        return response()->json(['success' => true]);
    }
    /**
     * Change product name and/or price on an order item.
     * Subtotal is recalculated from new price × current qty.
     */
    public function changeOrderDetail(String $uuid, Request $request) {
        $orderItem = TransactionDetails::findOrFail($uuid);

        if ($request->has('product_name') && $request->product_name !== '') {
            $orderItem->product_name = $request->product_name;
        }

        if ($request->has('price') && is_numeric($request->price) && $request->price >= 0) {
            $newPrice    = (int) $request->price;
            $orderItem->price    = $newPrice;
            $orderItem->subtotal = $newPrice * $orderItem->qty;
        }

        $orderItem->save();

        return response()->json([
            'success'   => true,
            'orderItem' => $orderItem,
        ]);
    }
    /**
     * Delete Order per transaction
     */
    public function deleteOrder(String $uuid) {
        $orderItem = TransactionDetails::findOrFail($uuid);
        $subtotal = $orderItem->subtotal;
        $orderItem->delete();
        return response()->json(['success' => true, 'subtotal' => $subtotal]);
    }
    /**
     * Submit Order per transaction
     */
    public function submitTransaction(String $uuid) {
        $transaction = Transactions::with('orderItem')->findOrFail($uuid);

        if (!$transaction->order_type) {
            return response()->json(['success' => false, 'message' => 'Silakan pilih tipe pesanan (Take Away atau Dine In) terlebih dahulu!']);
        }

        if(count($transaction->orderItem) < 1) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat submit transaksi tanpa order']);
        } else {
            $total = 0;
            foreach($transaction->orderItem as $item) {
                $total += $item->subtotal;
            }
            // Update total and status transaction
            $tax_setting = Settings::where('jenis','payment_tax')->first();
            $tax = $tax_setting && $tax_setting->nilai != null && $tax_setting->nilai > 0 ? ($total * $tax_setting->nilai) / 100 : 0;
            $totalwithtax = $total + $tax;
            
            // Capture before the update: this endpoint has no guard of its own, so a
            // double-click or a retried request would otherwise deduct ingredients — and
            // fire the "new order" push — twice for the same order.
            $isFirstSubmit = $transaction->status === 'active';

            $transaction->update([
                'status' => 'process',
                'kitchen_status' => 'cooking',
                'subtotal' => $total,
                'tax' => $tax,
                'total' => $totalwithtax
            ]);

            if ($isFirstSubmit) {
                $this->deductRecipeStock($transaction);
                app(FcmService::class)->notifyNewOrder($transaction);
            }
        }
        return response()->json(['success' => true]);
    }
    /**
     * Deduct recipe ingredients from supply stock for every menu item in the order.
     *
     * Runs when the order reaches the kitchen (not at payment), because that's when the
     * ingredients are physically used — an order cancelled after cooking has still consumed
     * them. Stock is allowed to go negative on purpose: refusing to submit an order because
     * a number says zero would block service, and a negative figure is itself a useful signal
     * that a purchase went unrecorded.
     */
    private function deductRecipeStock(Transactions $transaction): void
    {
        try {
            $productIds = $transaction->orderItem->pluck('product_id')->filter()->unique();

            if ($productIds->isEmpty()) {
                return; // manual items only — nothing has a recipe
            }

            $recipesByProduct = ProductRecipe::whereIn('product_id', $productIds)->get()->groupBy('product_id');

            if ($recipesByProduct->isEmpty()) {
                return; // no recipes defined yet
            }

            // Aggregate first so an ingredient shared by several menu items is written once.
            $deductions = [];
            foreach ($transaction->orderItem as $orderItem) {
                if (!$orderItem->product_id) {
                    continue;
                }
                foreach ($recipesByProduct[$orderItem->product_id] ?? [] as $recipe) {
                    $used = (float) $recipe->qty * (float) $orderItem->qty;
                    $deductions[$recipe->supply_item_id] = ($deductions[$recipe->supply_item_id] ?? 0) + $used;
                }
            }

            foreach ($deductions as $supplyItemId => $qty) {
                // decrement() is atomic, so concurrent orders can't lose an update.
                SupplyItem::where('uuid', $supplyItemId)->decrement('stock', $qty);
            }
        } catch (\Throwable $e) {
            // Stock bookkeeping must never block getting an order to the kitchen.
            Log::error('Recipe stock deduction failed for transaction ' . $transaction->uuid . ': ' . $e->getMessage());
        }
    }

    /**
     * Payment Transaction
     */
    public function paymentTransaction(String $uuid) {
        $transaction = Transactions::with('orderItem')->findOrFail($uuid);
        $product = Products::where('is_active',1)->get();
        $setting = Settings::whereIn('jenis',['restaurant_settings','restaurant_logo'])->get();
        $user_login = User::findOrFail($transaction->user_id);
        $restaurant_setting = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_settings';
        });
        $restaurant_logo = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_logo';
        });
        if($restaurant_logo && $restaurant_logo->nilai) {
            $imgPath = $restaurant_logo->nilai;
        } else {
            $imgPath = "";
        }
        
        if($restaurant_setting && $restaurant_setting->nilai) {
            $resSetting = @unserialize($restaurant_setting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurant_setting->nilai)) ?: array();
            }
        } else {
            $resSetting = array();
        }

        $resName = $resSetting && $resSetting['name'] ? $resSetting['name'] : '';
        $resLocation = $resSetting && $resSetting['location'] ? $resSetting['location'] : '';

        return View('transaction.payment',compact('transaction','product','imgPath','resName','resLocation'));
    }
    /**
     * Proceed Payment Transaction
     */
    public function proceedPaymentTransaction(String $uuid) {
        $transaction = Transactions::with('orderItem')->findOrFail($uuid);
        $transaction->update([
            'status' => 'payment',
            'kitchen_status' => 'cooking'
        ]);
        return response()->json(['success' => true, 'transaction' => $transaction]);
    }
    /**
     * Payment Transaction Discount
     */
    public function paymentTransactionDiscount(String $uuid, Request $request) {
        $transaction = Transactions::findOrFail($uuid);
        $total = $transaction->subtotal;
        $discount = $request->discount;
        // Update total and status transaction
        $tax_setting = Settings::where('jenis','payment_tax')->first();
        $tax = $tax_setting && $tax_setting->nilai != null && $tax_setting->nilai > 0 ? ($total * $tax_setting->nilai) / 100 : 0;
        $total = $total + $tax;

        $total = $total - $discount;

        $transaction->update([
            'discount' => $request->discount,
            'tax' => $tax,
            'total' => $total
        ]);
        return response()->json(['success' => true,'message' => 'Discount applied successfully', 'total' => $total,'discount' => $request->discount, 'total_formatted' => number_format($total, 0, ',', '.'), 'discount_formatted' => number_format($request->discount, 0, ',', '.'), 'tax_formatted' => number_format($tax, 0, ',', '.')]);
    }

    /**
     * Print Check Receipt
     */
    public function printCheckReceipt(String $uuid) {
        $transaction = Transactions::with(['orderItem.product.category', 'table'])->findOrFail($uuid);
        $uncategorizedSort = Settings::uncategorizedSort();
        $transaction->setRelation('orderItem', $transaction->orderItem->sortBy(function ($item) use ($uncategorizedSort) {
            return $item->product && $item->product->category
                ? $item->product->category->sort
                : $uncategorizedSort;
        })->values());
        $setting = Settings::whereIn('jenis',['restaurant_settings','restaurant_logo'])->get();
        $user_login = User::findOrFail($transaction->user_id);
        $restaurant_setting = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_settings';
        });
        $restaurant_logo = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_logo';
        });
        if($restaurant_logo && $restaurant_logo->nilai) {
            $logoUrl = asset('storage/'.$restaurant_logo->nilai);
        } else {
            $logoUrl = "";
        }
        
        if($restaurant_setting && $restaurant_setting->nilai) {
            $resSetting = @unserialize($restaurant_setting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurant_setting->nilai)) ?: array();
            }
        } else {
            $resSetting = array();
        }

        $resName = $resSetting && $resSetting['name'] ? $resSetting['name'] : '';
        $resLocation = $resSetting && $resSetting['location'] ? $resSetting['location'] : '';

        return response()->json([
            'success' => true, 
            'transaction' => $transaction,
            'restaurant' => [
                'name' => $resName,
                'location' => $resLocation,
                'logo' => $logoUrl
            ],
            'user' => $user_login->name
        ]);
        // $connector = new WindowsPrintConnector("POS-58");
        // $printer = new Printer($connector);

        
        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->setTextSize(2,2);
        // $printer->text("CHECK \n");
        // $printer->text($transaction->table && $transaction->table->name ? $transaction->table->name . "\n" : '' . "\n");
        // $printer->setTextSize(1,1);
        // $printer->setJustification(Printer::JUSTIFY_LEFT);
        // $printer->text("\n");
        // $printer->text("Date: " . date('Y-m-d H:i:s') . "\n");
        // $printer->text("--------------------------------\n");
        
        // $printer->text("Invoice Number: " . $transaction->invoice_number . "\n");
        // $printer->text("Customer Name: " . ($transaction->customer_name ?? '-') . "\n");
        // $printer->text("Order Type: " . ($transaction->order_type ?? '-') . "\n");
        // $printer->text("--------------------------------\n");
        // foreach($transaction->orderItem as $item) {
        //     $printer->text($item->product_name ."\n");
        //     if($item->note) {
        //         $printer->text("* Note: " . $item->note . "\n");
        //     }
        //     $price = "Rp " . number_format($item->subtotal, 0, ',', '.');
        //     $priceLine = str_pad($item->qty . " x Rp " . number_format($item->price, 0, ',', '.'), 32 - strlen($price), " ") . $price;
        //     $printer->text($priceLine . "\n");
            
        // }
        // $printer->text("--------------------------------\n");
        // $subtotal = "Rp " . number_format($transaction->subtotal, 0, ',', '.');
        // $printer->text(str_pad("Subtotal: ", 32 - strlen($subtotal), " ") . $subtotal . "\n");
        // $tax = "Rp " . number_format($transaction->tax, 0, ',', '.');
        // $printer->text(str_pad("Tax: ", 32 - strlen($tax), " ") . $tax . "\n");
        // $discount = "Rp " . number_format($transaction->discount, 0, ',', '.');
        // $printer->text(str_pad("Discount: ", 32 - strlen($discount), " ") . $discount . "\n");
        // $total = "Rp " . number_format($transaction->total, 0, ',', '.');
        // $totalLine = str_pad("Total: ", 32 - strlen($total), " ") . $total;
        // $printer->text($totalLine . "\n");

        // $printer->cut();

        // /* Close printer */
        // $printer->close();

        // return response()->json(['success' => true, 'message' => 'Check receipt printed successfully']);
    }
    /**
     * Print Check Receipt No Price
     */
    public function printCheckReceiptNoPrice(String $uuid) {
        $transaction = Transactions::with(['orderItem.product.category', 'table'])->findOrFail($uuid);
        $uncategorizedSort = Settings::uncategorizedSort();
        $transaction->setRelation('orderItem', $transaction->orderItem->sortBy(function ($item) use ($uncategorizedSort) {
            return $item->product && $item->product->category
                ? $item->product->category->sort
                : $uncategorizedSort;
        })->values());
        $setting = Settings::whereIn('jenis',['restaurant_settings','restaurant_logo'])->get();
        $user_login = User::findOrFail($transaction->user_id);
        $restaurant_setting = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_settings';
        });
        $restaurant_logo = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_logo';
        });
        if($restaurant_logo && $restaurant_logo->nilai) {
            $logoUrl = asset('storage/'.$restaurant_logo->nilai);
        } else {
            $logoUrl = "";
        }
        
        if($restaurant_setting && $restaurant_setting->nilai) {
            $resSetting = @unserialize($restaurant_setting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurant_setting->nilai)) ?: array();
            }
        } else {
            $resSetting = array();
        }

        $resName = $resSetting && $resSetting['name'] ? $resSetting['name'] : '';
        $resLocation = $resSetting && $resSetting['location'] ? $resSetting['location'] : '';

        return response()->json([
            'success' => true, 
            'transaction' => $transaction,
            'restaurant' => [
                'name' => $resName,
                'location' => $resLocation,
                'logo' => $logoUrl
            ],
            'user' => $user_login->name
        ]);
        // $connector = new WindowsPrintConnector("POS-58");
        // $printer = new Printer($connector);

        
        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->setTextSize(2,2);
        // $printer->text("CHECK \n");
        // $printer->text($transaction->table && $transaction->table->name ? $transaction->table->name . "\n" : '' . "\n");
        // $printer->setTextSize(1,1);
        // $printer->setJustification(Printer::JUSTIFY_LEFT);
        // $printer->text("\n");
        // $printer->text("Date: " . date('Y-m-d H:i:s') . "\n");
        // $printer->text("--------------------------------\n");
        
        // $printer->text("Invoice Number: " . $transaction->invoice_number . "\n");
        // $printer->text("Customer Name: " . ($transaction->customer_name ?? '-') . "\n");
        // $printer->text("Order Type: " . ($transaction->order_type ?? '-') . "\n");
        // $printer->text("--------------------------------\n");
        // foreach($transaction->orderItem as $item) {
        //     $printer->text($item->product_name ."\n");
        //     if($item->note) {
        //         $printer->text("* Note: " . $item->note . "\n");
        //     }
        //     $priceLine = str_pad("Qty: ", 32 - strlen($item->qty), " ") . $item->qty;
        //     $printer->text($priceLine . "\n");
            
        // }

        // $printer->cut();

        // /* Close printer */
        // $printer->close();

        // return response()->json(['success' => true, 'message' => 'Check receipt printed successfully']);
    }

    /**
     * Finalize Payment
     */
    public function finalizePayment(String $uuid, Request $request) {
        $transaction = Transactions::findOrFail($uuid);

        $transaction->update([
            'paid_method' => $request->method,
            'total_paid' => $request->amount,
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s')
        ]);

        app(FcmService::class)->notifyPaymentSuccess($transaction);

        return response()->json(['success' => true, "message" => "Successfully Updated Payment","transaction" => $transaction]);
    }

    public function printReceipt(String $uuid) {
        $transaction = Transactions::with(['orderItem.product.category', 'table'])->findOrFail($uuid);
        $uncategorizedSort = Settings::uncategorizedSort();
        $transaction->setRelation('orderItem', $transaction->orderItem->sortBy(function ($item) use ($uncategorizedSort) {
            return $item->product && $item->product->category
                ? $item->product->category->sort
                : $uncategorizedSort;
        })->values());
        $setting = Settings::whereIn('jenis',['restaurant_settings','restaurant_logo'])->get();
        $user_login = User::findOrFail($transaction->user_id);
        $restaurant_setting = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_settings';
        });
        $restaurant_logo = $setting->first(function($elem) {
            return $elem->jenis == 'restaurant_logo';
        });
        if($restaurant_logo && $restaurant_logo->nilai) {
            $logoUrl = asset('storage/'.$restaurant_logo->nilai);
        } else {
            $logoUrl = "";
        }
        
        if($restaurant_setting && $restaurant_setting->nilai) {
            $resSetting = @unserialize($restaurant_setting->nilai);
            if ($resSetting === false) {
                $resSetting = @unserialize(stripslashes($restaurant_setting->nilai)) ?: array();
            }
        } else {
            $resSetting = array();
        }

        $resName = $resSetting && $resSetting['name'] ? $resSetting['name'] : '';
        $resLocation = $resSetting && $resSetting['location'] ? $resSetting['location'] : '';

        // echo $imgPath;
        
        // $connector = new WindowsPrintConnector("POS-58");
        // $printer = new Printer($connector);

        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->setTextSize(2,2);
        // $img = EscposImage::load($imgPath,false);
        // $printer->bitImage($img,Printer::IMG_DEFAULT);
        // $printer->text(strtoupper($resName)."\n");
        // $printer->setTextSize(1,1);
        // $printer->text($resLocation."\n");
        // $printer->setJustification(Printer::JUSTIFY_LEFT);
        // $printer->text("===============================\n");
        // $printer->text("\n");
        // $printer->text("Paid Date: " . $transaction->paid_at . "\n");
        // $printer->text("Invoice Number: #" . $transaction->invoice_number . "\n");
        // $printer->text("Customer Name: " . ($transaction->customer_name ?? '-') . "\n");
        // $printer->text("Order Type: " . ($transaction->order_type ?? '-') . "\n");
        // $printer->text($transaction->table && $transaction->table->name ? "Table: ".$transaction->table->name . "\n" : 'Table: ' . "\n");
        // $printer->text("Kasir: " . $user_login->name. "\n");
        // $printer->text("--------------------------------\n");
        // foreach($transaction->orderItem as $item) {
        //     $printer->text($item->product_name ."\n");
        //     if($item->note) {
        //         $printer->text("* Note: " . $item->note . "\n");
        //     }
        //     $price = "Rp " . number_format($item->subtotal, 0, ',', '.');
        //     $priceLine = str_pad($item->qty . " x Rp " . number_format($item->price, 0, ',', '.'), 32 - strlen($price), " ") . $price;
        //     $printer->text($priceLine . "\n");
            
        // }
        // $printer->text("--------------------------------\n");
        // $subtotal = "Rp " . number_format($transaction->subtotal, 0, ',', '.');
        // $printer->text(str_pad("Subtotal: ", 32 - strlen($subtotal), " ") . $subtotal . "\n");
        // $tax = "Rp " . number_format($transaction->tax, 0, ',', '.');
        // $printer->text(str_pad("Tax: ", 32 - strlen($tax), " ") . $tax . "\n");
        // $discount = "Rp " . number_format($transaction->discount, 0, ',', '.');
        // $printer->text(str_pad("Discount: ", 32 - strlen($discount), " ") . $discount . "\n");
        // $total = "Rp " . number_format($transaction->total, 0, ',', '.');
        // $totalLine = str_pad("Total: ", 32 - strlen($total), " ") . $total;
        // $printer->text($totalLine . "\n");
        // $printer->text("--------------------------------\n");
        // $paid = "Rp " . number_format($transaction->total_paid, 0, ',', '.');
        // $printer->text(str_pad("Paid: ", 32 - strlen($paid), " ") . $paid . "\n");
        // $changeCalc = $transaction->total_paid - $transaction->total;
        // $change = "Rp " . number_format($changeCalc, 0, ',', '.');
        // $printer->text(str_pad("Change: ", 32 - strlen($change), " ") . $change . "\n");
        // $printer->text("===============================\n");
        // $printer->text("\n");
        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->text("Terima kasih \n");
        // $printer->text("Atas Kunjungan Anda \n");

        // $printer->cut();

        // /* Close printer */
        // $printer->close();

        return response()->json(['success' => true, 'message' => 'Check receipt printed successfully','transaction' => $transaction,'restaurant' => ['name' => $resName, 'location' => $resLocation, 'logo' => $logoUrl],'user' => $user_login->name]);
    }
    // test Print
    // public function testPrint() {
    //         $connector = new WindowsPrintConnector("POS-58");

    //         // 2. For a network printer with a specific IP address and port:
    //         // $connector = new NetworkPrintConnector("192.168.1.100", 9100);

    //         // 3. For testing by writing to a file:
    //         // $connector = new FilePrintConnector("/tmp/test.bin");

    //         $printer = new Printer($connector);

    //         /* Print a "Hello World" receipt */
    //         $printer->text("Hello World!\n");
    //         $printer->cut();

    //         /* Close printer */
    //         $printer->close();

    //         return "Receipt printed successfully.";
    // }

    /**
     * Live Updates polling endpoint.
     * Returns the current list of active/process transactions as JSON.
     * Frontend polls this every 3 seconds via setInterval — no SSE needed,
     * works on Windows with single-threaded php artisan serve.
     */
    public function getLiveUpdates(Request $request)
    {
        $transactions = Transactions::with(['table', 'orderItem'])
            ->whereIn('status', ['active', 'process', 'payment'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success'      => true,
            'time'         => now()->toIso8601String(),
            'transactions' => $transactions,
        ]);
    }
}