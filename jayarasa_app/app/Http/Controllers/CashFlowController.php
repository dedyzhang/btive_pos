<?php

namespace App\Http\Controllers;

use App\Models\CashFlowAccount;
use App\Models\CashFlowTransaction;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class CashFlowController extends Controller
{
    /**
     * Display Cash Flow Dashboard.
     */
    public function index() : View
    {
        // 0. Pre-seed default categories if empty
        if (\App\Models\CashFlowCategory::count() === 0) {
            \App\Models\CashFlowCategory::create(['name' => 'Bahan Baku', 'type' => 'expense']);
            \App\Models\CashFlowCategory::create(['name' => 'Operasional', 'type' => 'expense']);
            \App\Models\CashFlowCategory::create(['name' => 'Gaji Karyawan', 'type' => 'expense']);
            \App\Models\CashFlowCategory::create(['name' => 'Sewa Tempat', 'type' => 'expense']);
            \App\Models\CashFlowCategory::create(['name' => 'Lain-lain', 'type' => 'expense']);
            
            \App\Models\CashFlowCategory::create(['name' => 'Penjualan Toko (POS)', 'type' => 'income']);
            \App\Models\CashFlowCategory::create(['name' => 'Pemasukan Lainnya', 'type' => 'income']);
        }

        $incomeCategories = \App\Models\CashFlowCategory::where('type', 'income')->get();
        $expenseCategories = \App\Models\CashFlowCategory::where('type', 'expense')->get();

        // Historical Item Names for Autocomplete datalist recommendation list
        $historicalItemNames = CashFlowTransaction::whereNotNull('items')
            ->get()
            ->flatMap(function($trx) {
                return collect($trx->items)->pluck('name');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        // 1. Get all accounts with dynamic current balance calculation
        $accounts = CashFlowAccount::all()->map(function ($account) {
            $income = $account->transactions()->where('type', 'income')->sum('amount');
            $expense = $account->transactions()->where('type', 'expense')->sum('amount');
            
            $transferIn = CashFlowTransaction::where('type', 'transfer')
                ->where('destination_account_id', $account->uuid)
                ->sum('amount');
                
            $transferOut = CashFlowTransaction::where('type', 'transfer')
                ->where('account_id', $account->uuid)
                ->sum('amount');

            $account->current_balance = $account->initial_balance + $income - $expense + $transferIn - $transferOut;
            return $account;
        });

        // 2. Cumulative balance across all accounts
        $totalBalance = $accounts->sum('current_balance');

        // 3. Monthly Income and Expense
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');

        $monthlyIncome = CashFlowTransaction::where('type', 'income')
            ->whereBetween('transaction_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->sum('amount');

        $monthlyExpense = CashFlowTransaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->sum('amount');

        // 4. Recent Transactions List
        $transactions = CashFlowTransaction::with(['account', 'destinationAccount', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Day-by-Day Sales Reconciliation (Past 15 days)
        $reconciliations = [];
        for ($i = 0; $i < 15; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            // Total paid sales of the day
            $totalSales = Transactions::where('status', 'paid')
                ->whereBetween('paid_at', [$date . ' 00:00:00', $date . ' 23:59:59'])
                ->sum('total');

            // Total reconciled income in cash flow (including operational expenses and drawer cash)
            $reconTransactions = CashFlowTransaction::where('is_sales_reconciliation', true)
                ->where('reconciliation_date', $date)
                ->get();
            $totalReconciled = $reconTransactions->sum('amount') + $reconTransactions->sum('operational_expense') + $reconTransactions->sum('cash_drawer_amount');

            $reconciliations[] = (object) [
                'date' => $date,
                'formatted_date' => date('d M Y', strtotime($date)),
                'total_sales' => $totalSales,
                'total_reconciled' => $totalReconciled,
                'status' => ($totalSales == 0 && $totalReconciled == 0) 
                    ? 'empty' 
                    : ($totalSales == $totalReconciled ? 'match' : ($totalReconciled > 0 ? 'mismatch' : 'unreconciled')),
                'variance' => $totalSales - $totalReconciled
            ];
        }

        // 6. Gather all purchased items for price comparison & trends
        $expenseTransactions = CashFlowTransaction::where('type', 'expense')
            ->whereNotNull('items')
            ->get();

        $groupedItems = [];
        foreach ($expenseTransactions as $trx) {
            $items = $trx->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (empty($item['name'])) continue;
                    
                    // Normalize item name for case-insensitive matching
                    $key = strtolower(trim($item['name']));
                    
                    if (!isset($groupedItems[$key])) {
                        $groupedItems[$key] = [
                            'display_name' => trim($item['name']),
                            'history' => []
                        ];
                    }
                    
                    $groupedItems[$key]['history'][] = (object) [
                        'date' => $trx->transaction_date,
                        'formatted_date' => date('d M Y', strtotime($trx->transaction_date)),
                        'purchase_place' => $trx->purchase_place ?: 'Tidak Diketahui',
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'total' => $item['total'],
                        'reference' => $trx->reference ?: '-'
                    ];
                }
            }
        }

        // Sort items by display name, and for each item sort history by price (cheapest first) for comparison
        ksort($groupedItems);
        $priceComparisons = [];
        foreach ($groupedItems as $key => $data) {
            $history = $data['history'];
            
            // Sort by unit price ascending for comparison
            usort($history, function($a, $b) {
                return $a->price <=> $b->price;
            });
            
            // Also get chronological history (latest first) for trend tracking
            $chronoHistory = $data['history'];
            usort($chronoHistory, function($a, $b) {
                return strcmp($b->date, $a->date);
            });

            $priceComparisons[] = (object) [
                'display_name' => $data['display_name'],
                'cheapest_price' => count($history) > 0 ? $history[0]->price : 0,
                'cheapest_place' => count($history) > 0 ? $history[0]->purchase_place : '-',
                'latest_price' => count($chronoHistory) > 0 ? $chronoHistory[0]->price : 0,
                'history' => $history,
                'chrono_history' => $chronoHistory
            ];
        }

        return view('cashflow.index', compact(
            'accounts', 
            'totalBalance', 
            'monthlyIncome', 
            'monthlyExpense', 
            'transactions',
            'reconciliations',
            'priceComparisons',
            'incomeCategories',
            'expenseCategories',
            'historicalItemNames'
        ));
    }

    /**
     * Store new Account.
     */
    public function storeAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7'
        ]);

        CashFlowAccount::create([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'initial_balance' => $request->initial_balance,
            'description' => $request->description,
            'icon' => $request->icon ?: 'fa-wallet',
            'color' => $request->color ?: '#6366f1'
        ]);

        return redirect()->route('cashflow.index')->with('success', 'Akun keuangan berhasil ditambahkan.');
    }

    /**
     * Update Account.
     */
    public function updateAccount(Request $request, String $uuid)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7'
        ]);

        $account = CashFlowAccount::findOrFail($uuid);
        $account->update([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'initial_balance' => $request->initial_balance,
            'description' => $request->description,
            'icon' => $request->icon ?: 'fa-wallet',
            'color' => $request->color ?: '#6366f1'
        ]);

        return redirect()->route('cashflow.index')->with('success', 'Akun keuangan berhasil diperbarui.');
    }

    /**
     * Delete Account.
     */
    public function destroyAccount(String $uuid)
    {
        $account = CashFlowAccount::findOrFail($uuid);
        $account->delete();

        return redirect()->route('cashflow.index')->with('success', 'Akun keuangan berhasil dihapus.');
    }

    /**
     * Store a new Cash Flow Transaction.
     */
    public function storeTransaction(Request $request)
    {
        $isSalesReconciliation = $request->has('is_sales_reconciliation') && $request->is_sales_reconciliation == 1;

        $request->validate([
            'account_id' => $isSalesReconciliation ? 'nullable|exists:cash_flow_accounts,uuid' : 'required|exists:cash_flow_accounts,uuid',
            'destination_account_id' => 'required_if:type,transfer|nullable|exists:cash_flow_accounts,uuid|different:account_id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'operational_expense' => 'nullable|numeric|min:0',
            'cash_drawer_amount' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
            'purchase_place' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:cash_flow_categories,uuid',
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.qty' => 'required_with:items|numeric|min:0.01',
            'items.*.price' => 'required_with:items|numeric|min:0'
        ]);

        $reconciliationDate = $isSalesReconciliation ? $request->reconciliation_date : null;
        $operationalExpense = $isSalesReconciliation ? ($request->operational_expense ?: 0) : 0;
        $cashDrawerAmount = $isSalesReconciliation ? ($request->cash_drawer_amount ?: 0) : 0;

        // If it's a sales reconciliation, validate the date
        if ($isSalesReconciliation && !$reconciliationDate) {
            return redirect()->back()->withErrors(['reconciliation_date' => 'Tanggal rekonsiliasi wajib diisi jika opsi rekonsiliasi aktif.'])->withInput();
        }

        // Process shopping items if type is expense
        $items = null;
        if ($request->type === 'expense' && $request->has('items') && is_array($request->items)) {
            $filteredItems = [];
            foreach ($request->items as $item) {
                if (!empty($item['name']) && isset($item['qty']) && isset($item['price'])) {
                    $qty = (float) $item['qty'];
                    $price = (int) $item['price'];
                    $filteredItems[] = [
                        'name' => $item['name'],
                        'qty' => $qty,
                        'price' => $price,
                        'total' => $qty * $price
                    ];
                }
            }
            if (count($filteredItems) > 0) {
                $items = $filteredItems;
            }
        }

        $accountSplits = $request->input('account_splits', []);
        $activeSplits = array_filter($accountSplits, function($val) {
            return (int) $val > 0;
        });

        if ($isSalesReconciliation && count($activeSplits) > 0) {
            // Validate that the sum of splits equals the amount
            $sumSplits = array_sum($activeSplits);
            if ($sumSplits != $request->amount) {
                return redirect()->back()->withErrors(['amount' => 'Total pembagian akun (' . number_format($sumSplits, 0, ',', '.') . ') harus sama dengan Nominal Transaksi (' . number_format($request->amount, 0, ',', '.') . ').'])->withInput();
            }

            // Create separate transactions for each split
            $first = true;
            foreach ($activeSplits as $accUuid => $splitAmount) {
                CashFlowTransaction::create([
                    'account_id' => $accUuid,
                    'type' => $request->type,
                    'amount' => $splitAmount,
                    // Store operational expense and cash drawer money only on the first transaction to avoid duplication
                    'operational_expense' => $first ? $operationalExpense : 0,
                    'cash_drawer_amount' => $first ? $cashDrawerAmount : 0,
                    'transaction_date' => $request->transaction_date,
                    'description' => $request->description ?: 'Rekonsiliasi Omzet Penjualan (Split)',
                    'reference' => $request->reference,
                    'is_sales_reconciliation' => $isSalesReconciliation,
                    'reconciliation_date' => $reconciliationDate,
                    'category_id' => $request->category_id
                ]);
                $first = false;
            }
        } else {
            // Normal transaction or single reconciliation
            if ($isSalesReconciliation && !$request->account_id) {
                return redirect()->back()->withErrors(['account_id' => 'Akun keuangan wajib dipilih jika pembagian nominal kosong.'])->withInput();
            }

            CashFlowTransaction::create([
                'account_id' => $request->account_id,
                'destination_account_id' => $request->type === 'transfer' ? $request->destination_account_id : null,
                'type' => $request->type,
                'amount' => $request->amount,
                'operational_expense' => $operationalExpense,
                'cash_drawer_amount' => $cashDrawerAmount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'reference' => $request->reference,
                'is_sales_reconciliation' => $isSalesReconciliation,
                'reconciliation_date' => $reconciliationDate,
                'items' => $items,
                'purchase_place' => $request->purchase_place,
                'category_id' => $request->category_id
            ]);
        }

        return redirect()->route('cashflow.index')->with('success', 'Transaksi kas berhasil dicatat.');
    }

    /**
     * Update an existing Transaction.
     */
    public function updateTransaction(Request $request, String $uuid)
    {
        $transaction = CashFlowTransaction::findOrFail($uuid);
        $isSalesReconciliation = $request->has('is_sales_reconciliation') && $request->is_sales_reconciliation == 1;

        $request->validate([
            'account_id' => $isSalesReconciliation ? 'nullable|exists:cash_flow_accounts,uuid' : 'required|exists:cash_flow_accounts,uuid',
            'destination_account_id' => 'required_if:type,transfer|nullable|exists:cash_flow_accounts,uuid|different:account_id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'operational_expense' => 'nullable|numeric|min:0',
            'cash_drawer_amount' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string',
            'purchase_place' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:cash_flow_categories,uuid',
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.qty' => 'required_with:items|numeric|min:0.01',
            'items.*.price' => 'required_with:items|numeric|min:0'
        ]);

        $reconciliationDate = $isSalesReconciliation ? $request->reconciliation_date : null;
        $operationalExpense = $isSalesReconciliation ? ($request->operational_expense ?: 0) : 0;
        $cashDrawerAmount = $isSalesReconciliation ? ($request->cash_drawer_amount ?: 0) : 0;

        // If it's a sales reconciliation, validate the date
        if ($isSalesReconciliation && !$reconciliationDate) {
            return redirect()->back()->withErrors(['reconciliation_date' => 'Tanggal rekonsiliasi wajib diisi jika opsi rekonsiliasi aktif.'])->withInput();
        }

        // Process shopping items if type is expense
        $items = null;
        if ($request->type === 'expense' && $request->has('items') && is_array($request->items)) {
            $filteredItems = [];
            foreach ($request->items as $item) {
                if (!empty($item['name']) && isset($item['qty']) && isset($item['price'])) {
                    $qty = (float) $item['qty'];
                    $price = (int) $item['price'];
                    $filteredItems[] = [
                        'name' => $item['name'],
                        'qty' => $qty,
                        'price' => $price,
                        'total' => $qty * $price
                    ];
                }
            }
            if (count($filteredItems) > 0) {
                $items = $filteredItems;
            }
        }

        $transaction->update([
            'account_id' => $request->account_id ?: $transaction->account_id,
            'destination_account_id' => $request->type === 'transfer' ? $request->destination_account_id : null,
            'type' => $request->type,
            'amount' => $request->amount,
            'operational_expense' => $operationalExpense,
            'cash_drawer_amount' => $cashDrawerAmount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'reference' => $request->reference,
            'is_sales_reconciliation' => $isSalesReconciliation,
            'reconciliation_date' => $reconciliationDate,
            'items' => $items,
            'purchase_place' => $request->purchase_place,
            'category_id' => $request->category_id
        ]);

        return redirect()->route('cashflow.index')->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    /**
     * Delete a Transaction.
     */
    public function destroyTransaction(String $uuid)
    {
        $transaction = CashFlowTransaction::findOrFail($uuid);
        $transaction->delete();

        return redirect()->route('cashflow.index')->with('success', 'Transaksi kas berhasil dihapus.');
    }

    /**
     * REST Endpoint to get actual sales omzet for a specific date (AJAX).
     */
    public function getSalesDataByDate(Request $request) : JsonResponse
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->date;

        // Calculate total actual sales of the day (status 'paid')
        $totalSales = Transactions::where('status', 'paid')
            ->whereBetween('paid_at', [$date . ' 00:00:00', $date . ' 23:59:59'])
            ->sum('total');

        // Calculate total registered reconciled income for this date (including operational expenses and drawer cash)
        $reconTransactions = CashFlowTransaction::where('is_sales_reconciliation', true)
            ->where('reconciliation_date', $date)
            ->get();
        $totalReconciled = $reconTransactions->sum('amount') + $reconTransactions->sum('operational_expense') + $reconTransactions->sum('cash_drawer_amount');

        return response()->json([
            'success' => true,
            'date' => $date,
            'total_sales' => (int) $totalSales,
            'total_reconciled' => (int) $totalReconciled,
            'remaining_to_reconcile' => (int) ($totalSales - $totalReconciled)
        ]);
    }

    /**
     * Store new transaction category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense'
        ]);

        \App\Models\CashFlowCategory::create([
            'name' => $request->name,
            'type' => $request->type
        ]);

        return redirect()->route('cashflow.index')->with('success', 'Kategori kas berhasil ditambahkan.');
    }

    /**
     * Delete transaction category.
     */
    public function destroyCategory(String $uuid)
    {
        $category = \App\Models\CashFlowCategory::findOrFail($uuid);
        $category->delete();

        return redirect()->route('cashflow.index')->with('success', 'Kategori kas berhasil dihapus.');
    }
}
