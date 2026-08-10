<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\TransactionDetails;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $startDate = $request->query('start_date', $today);
        $endDate = $request->query('end_date', $today);

        // Standard dates for comparison or calculations
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();

        // === FILTERED RANGE METRICS ===
        $rangeTransactions = Transactions::with(['orderItem.product.category', 'table', 'user'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('paid_at', 'desc')
            ->get();

        $todayRevenue = $rangeTransactions->sum('total');
        $todayTransactionCount = $rangeTransactions->count();
        $todayItemsSold = $rangeTransactions->sum(function ($tx) {
            return $tx->orderItem->sum('qty');
        });
        $todayCostPrice = $rangeTransactions->sum(function ($tx) {
            return $tx->orderItem->sum(function ($item) {
                return $item->qty * ($item->product->cost_price ?? 0);
            });
        });
        $todayGrossProfit = $todayRevenue - $todayCostPrice;
        $todayDiscount = $rangeTransactions->sum('discount');
        $todayTax = $rangeTransactions->sum('tax');

        // Let's get the previous period for comparison (same duration)
        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $daysDiff = $startCarbon->diffInDays($endCarbon) + 1;

        $prevStartDate = $startCarbon->copy()->subDays($daysDiff)->format('Y-m-d');
        $prevEndDate = $startCarbon->copy()->subDay()->format('Y-m-d');

        $prevTransactions = Transactions::where('status', 'paid')
            ->whereBetween('paid_at', [$prevStartDate . ' 00:00:00', $prevEndDate . ' 23:59:59'])
            ->get();

        $yesterdayRevenue = $prevTransactions->sum('total');
        $yesterdayTransactionCount = $prevTransactions->count();
        $prevCostPrice = $prevTransactions->sum(function ($tx) {
            return $tx->orderItem->sum(function ($item) {
                return $item->qty * ($item->product->cost_price ?? 0);
            });
        });
        $yesterdayGrossProfit = $yesterdayRevenue - $prevCostPrice;
        $prevItemsSold = $prevTransactions->sum(function ($tx) {
            return $tx->orderItem->sum('qty');
        });

        // === PERCENTAGE CHANGES ===
        $revenueChange = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        $profitChange = $yesterdayGrossProfit > 0
            ? round((($todayGrossProfit - $yesterdayGrossProfit) / $yesterdayGrossProfit) * 100, 1)
            : ($todayGrossProfit > 0 ? 100 : 0);

        $txCountChange = $yesterdayTransactionCount > 0
            ? round((($todayTransactionCount - $yesterdayTransactionCount) / $yesterdayTransactionCount) * 100, 1)
            : ($todayTransactionCount > 0 ? 100 : 0);

        $itemsChange = $prevItemsSold > 0
            ? round((($todayItemsSold - $prevItemsSold) / $prevItemsSold) * 100, 1)
            : ($todayItemsSold > 0 ? 100 : 0);

        // === 7-DAY TREND DATA ===
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::parse($endDate)->subDays($i);
            $dayRevenue = Transactions::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('total');
            $weeklyTrend[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('D, d M'),
                'revenue' => (float) $dayRevenue,
            ];
        }

        // === TOP 5 BEST-SELLING PRODUCTS IN RANGE ===
        $topProducts = TransactionDetails::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->selectRaw('product_name, product_id, SUM(qty) as total_qty, SUM(qty * price) as total_sales')
            ->groupBy('product_name', 'product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // === PAYMENT METHOD BREAKDOWN IN RANGE ===
        $paymentMethods = [];
        foreach ($rangeTransactions as $tx) {
            $method = $tx->paid_method ? strtoupper(trim($tx->paid_method)) : 'CASH';
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'total' => 0];
            }
            $paymentMethods[$method]['count']++;
            $paymentMethods[$method]['total'] += $tx->total;
        }

        // === RECENT 5 TRANSACTIONS ===
        $recentTransactions = $rangeTransactions->take(5);

        // === MONTHLY SUMMARY ===
        $monthlyTransactions = Transactions::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth->format('Y-m-d') . ' 00:00:00', Carbon::now()->format('Y-m-d') . ' 23:59:59'])
            ->get();

        $monthlyRevenue = $monthlyTransactions->sum('total');
        $monthlyTransactionCount = $monthlyTransactions->count();
        $daysElapsed = max(1, Carbon::now()->diffInDays($startOfMonth) + 1);
        $monthlyAvgPerDay = round($monthlyRevenue / $daysElapsed);

        // === ACTIVE ORDERS COUNT (live) ===
        $activeOrders = Transactions::whereIn('status', ['active', 'process', 'payment'])->count();

        // === TOTAL PRODUCTS ===
        $totalProducts = Products::where('is_active', 1)->count();

        // === CATEGORY BREAKDOWN IN RANGE ===
        $categorySales = TransactionDetails::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->join('products', 'transaction_details.product_id', '=', 'products.uuid')
            ->join('categories', 'products.category_id', '=', 'categories.uuid')
            ->selectRaw('categories.nama as category_name, SUM(transaction_details.qty * transaction_details.price) as total_sales')
            ->groupBy('categories.nama')
            ->get();

        // === HOURLY peak hours in range ===
        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $rawHourly = Transactions::where('status', 'paid')
                ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->selectRaw('strftime("%H", paid_at) as hour, SUM(total) as revenue')
                ->groupBy('hour')
                ->get();
        } else {
            $rawHourly = Transactions::where('status', 'paid')
                ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->selectRaw('HOUR(paid_at) as hour, SUM(total) as revenue')
                ->groupBy('hour')
                ->get();
        }

        $hourlyData = array_fill(0, 24, 0);
        foreach ($rawHourly as $rh) {
            $h = (int) $rh->hour;
            if ($h >= 0 && $h < 24) {
                $hourlyData[$h] = (float) $rh->revenue;
            }
        }

        // === LOW STOCK ITEMS ===
        $lowStockProducts = Products::with('category')
            ->where('is_active', 1)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        // === TODAY'S TRANSACTIONS FOR BILLING QUEUES ON DASHBOARD ===
        $todayTransactions = Transactions::with(['orderItem.product.category', 'table', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.index', compact(
            'todayRevenue', 'todayGrossProfit', 'todayTransactionCount', 'todayItemsSold',
            'todayDiscount', 'todayTax', 'todayCostPrice',
            'revenueChange', 'profitChange', 'txCountChange', 'itemsChange',
            'weeklyTrend', 'topProducts', 'paymentMethods', 'recentTransactions',
            'monthlyRevenue', 'monthlyTransactionCount', 'monthlyAvgPerDay',
            'activeOrders', 'totalProducts',
            'startDate', 'endDate', 'categorySales', 'hourlyData', 'lowStockProducts', 'todayTransactions'
        ));
    }
}

