<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Transactions;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index() {

        $transactions = Transactions::with('table')->whereDate('created_at',today())->orderBy('created_at','desc')->get();
    
        return view('activity.index',compact('transactions'));
    }

    public function history() {
        $transactions = Transactions::where('status','paid')->orderBy('created_at','desc')->get();
        return view('activity.history',compact('transactions'));
    }

    public function report() {
        return view('activity.report');
    }

    public function reportShow(Request $request, String $date) {
        $startDate = $request->query('start_date', $date);
        $endDate = $request->query('end_date', $date);

        $transactions = Transactions::with(['table', 'orderItem.product.category'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('paid_at', 'desc')
            ->get();

        $reportSummary = $this->getReportSummaryData($transactions, $startDate, $endDate);

        return response()->json(['transaction' => $transactions, 'summary' => $reportSummary]);
    }

    public function exportExcel(Request $request) {
        $date = $request->query('date', date('Y-m-d'));
        $startDate = $request->query('start_date', $date);
        $endDate = $request->query('end_date', $date);

        $transactions = Transactions::with(['table', 'orderItem.product.category'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('paid_at', 'desc')
            ->get();

        $reportSummary = $this->getReportSummaryData($transactions, $startDate, $endDate);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SalesReportExport($transactions, $reportSummary),
            'Laporan_Penjualan_' . ($startDate === $endDate ? $startDate : $startDate . '_to_' . $endDate) . '.xlsx'
        );
    }

    private function getReportSummaryData($transactions, $startDate, $endDate) {
        $uncategorizedSort = Settings::uncategorizedSort();
        $reportSummary = array();
        $reportSummary['date'] = $startDate === $endDate ? $startDate : $startDate . ' s/d ' . $endDate;
        $reportSummary['start_date'] = $startDate;
        $reportSummary['end_date'] = $endDate;
        $reportSummary['total_transactions'] = $transactions->count();

        // Cost Price Count
        $reportSummary['total_cost_price'] = $transactions->sum(function($transaction) {
            return $transaction->orderItem->sum(function($orderItem) {
                if ($orderItem->product) {
                    return $orderItem->qty * $orderItem->product->cost_price;
                } else {
                    return 0;
                }
            });
        });
        $reportSummary['operasional_cost'] = (50 / 100) * $reportSummary['total_cost_price'];
        $reportSummary['total_items'] = $transactions->sum(function($transaction) {
            return $transaction->orderItem->sum('qty');
        });
        $reportSummary['total_revenue'] = $transactions->sum('total');
        $reportSummary['laba_kotor'] = $reportSummary['total_revenue'] - $reportSummary['total_cost_price'];
        $reportSummary['laba_bersih'] = $reportSummary['laba_kotor'] - $reportSummary['operasional_cost'];

        // Discount and Tax Count
        $reportSummary['total_discount'] = $transactions->sum('discount');
        $reportSummary['total_tax'] = $transactions->sum('tax');

        // Get all order item from transactions
        $reportSummary['items'] = array();
        foreach($transactions as $transaction) {
            foreach($transaction->orderItem as $orderItem) {
                $item = array();
                $item['product_id'] = $orderItem->product_id;
                $item['name'] = $orderItem->product_name;
                $item['qty'] = $orderItem->qty;

                // Safely handle cost_price for manual item where product is null
                $cost_price = 0;
                if ($orderItem->product) {
                    $cost_price = $orderItem->product->cost_price ?? 0;
                }

                $categorySort = $uncategorizedSort;
                $categoryName = 'Uncategorized';
                if ($orderItem->product && $orderItem->product->category) {
                    $categorySort = $orderItem->product->category->sort ?? $uncategorizedSort;
                    $categoryName = $orderItem->product->category->nama ?? 'Uncategorized';
                }

                $item['category_sort'] = $categorySort;
                $item['category_name'] = $categoryName;
                $item['cost_price'] = $cost_price;
                $item['price'] = $orderItem->price;
                $item['cost_price_total'] = $orderItem->qty * $cost_price;
                $item['total'] = $orderItem->qty * $orderItem->price;
                $item['profit'] = $item['total'] - $item['cost_price_total'];
                
                // Use a unique key for manual item to prevent duplicate key collision or null key issue
                $itemKey = $orderItem->product_id ?: 'manual_' . md5($orderItem->product_name);
                
                if(isset($reportSummary['items'][$itemKey])) {
                    $reportSummary['items'][$itemKey]['qty'] += $orderItem->qty;
                    $reportSummary['items'][$itemKey]['total'] += $orderItem->qty * $orderItem->price;
                    $reportSummary['items'][$itemKey]['cost_price_total'] += $orderItem->qty * $cost_price;
                    $reportSummary['items'][$itemKey]['profit'] += $item['profit'];
                } else {
                    $reportSummary['items'][$itemKey] = $item;
                }
            }
        }

        // Sort items by category_sort ascending, then by qty descending
        uasort($reportSummary['items'], function($a, $b) {
            if ($a['category_sort'] !== $b['category_sort']) {
                return $a['category_sort'] <=> $b['category_sort'];
            }
            return $b['qty'] <=> $a['qty'];
        });

        // Get all category breakdown
        $reportSummary['categories'] = array();
        foreach($transactions as $transaction) {
            foreach($transaction->orderItem as $orderItem) {
                if ($orderItem->product && $orderItem->product->category) {
                    $catName = $orderItem->product->category->nama;
                    $catColor = $orderItem->product->category->color ?? 'blue';
                } else {
                    $catName = 'Uncategorized';
                    $catColor = 'gray';
                }
                
                $itemTotal = $orderItem->qty * $orderItem->price;
                if(isset($reportSummary['categories'][$catName])) {
                    $reportSummary['categories'][$catName]['total'] += $itemTotal;
                    $reportSummary['categories'][$catName]['qty'] += $orderItem->qty;
                } else {
                    $reportSummary['categories'][$catName] = array();
                    $reportSummary['categories'][$catName]['name'] = $catName;
                    $reportSummary['categories'][$catName]['color'] = $catColor;
                    $reportSummary['categories'][$catName]['total'] = $itemTotal;
                    $reportSummary['categories'][$catName]['qty'] = $orderItem->qty;
                }
            }
        }

        // Get all payment method from transactions
        $reportSummary['payment_methods'] = array();
        foreach($transactions as $transaction) {
            $paymentMethod = $transaction->paid_method ? strtoupper(trim($transaction->paid_method)) : 'CASH';
            if(isset($reportSummary['payment_methods'][$paymentMethod])) {  
                $reportSummary['payment_methods'][$paymentMethod]['total_transaction'] += 1;
                $reportSummary['payment_methods'][$paymentMethod]['total'] += $transaction->total;
            } else {
                $reportSummary['payment_methods'][$paymentMethod] = array();
                $reportSummary['payment_methods'][$paymentMethod]['total_transaction'] = 1;
                $reportSummary['payment_methods'][$paymentMethod]['total'] = $transaction->total;
            }
        }

        // Get all order type from transactions
        $reportSummary['order_types'] = array();
        foreach($transactions as $transaction) {
            $orderType = $transaction->order_type;
            if(isset($reportSummary['order_types'][$orderType])) {  
                $reportSummary['order_types'][$orderType]['total_transaction'] += 1;
                $reportSummary['order_types'][$orderType]['total'] += $transaction->total;
            } else {
                $reportSummary['order_types'][$orderType] = array();
                $reportSummary['order_types'][$orderType]['total_transaction'] = 1;
                $reportSummary['order_types'][$orderType]['total'] = $transaction->total;
            }
        }

        return $reportSummary;
    }
}
