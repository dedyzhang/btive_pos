@extends('layout.index')
@section('title', 'Cash Flow')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold uppercase">CASH FLOW</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100">
                <i class="fas fa-calendar-days text-lg text-blue-400"></i>
            </div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <style>
        .account-card:hover {
            border-color: var(--hover-border-color) !important;
        }
    </style>
    <div class="container-place w-full p-6 flex flex-col gap-6">

        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 text-sm text-fg-success-strong rounded-2xl bg-success-soft border border-emerald-100 shadow-sm animate-fade-in" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check-circle text-emerald-500 text-lg"></i>
                <p><span class="font-bold me-1">Sukses!</span> {{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="flex flex-col p-4 text-sm text-red-700 rounded-2xl bg-red-50 border border-red-100 shadow-sm" role="alert">
                <div class="flex items-center mb-1">
                    <i class="me-2 fas fa-exclamation-triangle text-red-500 text-lg"></i>
                    <span class="font-bold">Mohon perbaiki kesalahan berikut:</span>
                </div>
                <ul class="list-disc list-inside ps-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. Top Section: Metrik Keuangan Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Metric 1: Total Saldo Kumulatif -->
            <div class="bg-gradient-to-br from-brand via-indigo-600 to-indigo-700 p-6 rounded-3xl text-white shadow-xl shadow-brand/10 hover:shadow-brand/20 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-wallet text-9xl"></i>
                </div>
                <p class="text-xs uppercase tracking-widest text-blue-100 font-semibold opacity-95 mb-1">Total Saldo Kumulatif</p>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight drop-shadow-sm font-mono mt-1">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </h2>
                <p class="text-[10px] text-blue-200 mt-2 font-medium">Saldo gabungan seluruh akun keuangan</p>
            </div>

            <!-- Metric 2: Pemasukan Bulan Ini -->
            <div id="btn-show-income-breakdown" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-lg shadow-gray-100/50 flex flex-col justify-between hover:border-emerald-100 transition-all duration-200 relative overflow-hidden group cursor-pointer hover:shadow-xl hover:shadow-emerald-100">
                <div class="absolute right-6 top-6 w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">Pemasukan Bulan Ini</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-emerald-600 tracking-tight font-mono mt-1">
                        Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                    </h2>
                </div>
                <p class="text-[10px] text-gray-400 mt-3 font-semibold">Total kas masuk sejak {{ date('01 M Y') }}</p>
            </div>

            <!-- Metric 3: Pengeluaran Bulan Ini -->
            <div id="btn-show-expense-breakdown" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-lg shadow-gray-100/50 flex flex-col justify-between hover:border-rose-100 transition-all duration-200 relative overflow-hidden group cursor-pointer hover:shadow-xl hover:shadow-rose-100">
                <div class="absolute right-6 top-6 w-10 h-10 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">Pengeluaran Bulan Ini</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-rose-600 tracking-tight font-mono mt-1">
                        Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                    </h2>
                </div>
                <p class="text-[10px] text-gray-400 mt-3 font-semibold">Total kas keluar sejak {{ date('01 M Y') }}</p>
            </div>
        </div>

        <!-- 2. Grid Akun Keuangan -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Daftar Akun Keuangan</h2>
                    <p class="text-xs text-gray-400 font-medium">Rekening bank dan tempat penyimpanan kas operasional</p>
                </div>
                <button type="button" class="btn-add-account px-4 py-2 bg-brand hover:bg-brand-dark text-white rounded-xl text-xs font-bold shadow-lg shadow-brand/10 transition-all flex items-center gap-2 cursor-pointer border-none outline-none">
                    <i class="fas fa-plus"></i> Tambah Akun
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($accounts as $account)
                    <div class="account-card bg-white p-5 rounded-2xl border-2 border-transparent hover:shadow-lg transition-all duration-200 relative overflow-hidden group cursor-pointer" data-uuid="{{ $account->uuid }}" data-name="{{ $account->name }}" data-color="{{ $account->color ?: '#6366f1' }}" style="--hover-border-color: {{ $account->color ?: '#6366f1' }}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-md transition-all duration-200" style="background-color: {{ $account->color ?: '#6366f1' }}15; color: {{ $account->color ?: '#6366f1' }}">
                                <i class="fas {{ $account->icon ?: 'fa-wallet' }}"></i>
                            </div>
                            <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" class="btn-edit-account w-7 h-7 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg flex items-center justify-center text-xs cursor-pointer border-none" 
                                    data-uuid="{{ $account->uuid }}" 
                                    data-name="{{ $account->name }}" 
                                    data-number="{{ $account->account_number }}" 
                                    data-balance="{{ $account->initial_balance }}" 
                                    data-desc="{{ $account->description }}"
                                    data-icon="{{ $account->icon ?: 'fa-wallet' }}"
                                    data-color="{{ $account->color ?: '#6366f1' }}">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <form method="POST" action="{{ route('cashflow.accounts.destroy', $account->uuid) }}" class="inline-block form-delete-account">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-delete-account-trigger w-7 h-7 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg flex items-center justify-center text-xs cursor-pointer border-none">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-bold text-gray-800 text-sm leading-snug truncate">{{ $account->name }}</h3>
                            <p class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase mt-0.5">
                                {{ $account->account_number ?: 'TIPE KAS FISIK' }}
                            </p>
                            
                            <div class="mt-4 border-t border-gray-50 pt-3">
                                <span class="text-[10px] text-gray-400 font-bold uppercase block leading-none">Saldo Sekarang</span>
                                <span class="text-lg font-bold text-gray-800 font-mono tracking-tight leading-snug">
                                    Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Tab Section: Mutasi Buku Besar & Rekonsiliasi Harian -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden mt-2">
            <!-- Tab Headers -->
            <div class="flex border-b border-gray-100 bg-gray-50/50 p-1.5 sm:p-2 shrink-0">
                <button type="button" class="tab-btn px-3 py-2.5 sm:px-6 sm:py-3 text-[10px] sm:text-xs rounded-xl transition-all cursor-pointer border-none outline-none bg-white text-brand shadow-sm flex flex-col sm:flex-row items-center justify-center gap-1.5 sm:gap-2 grow sm:grow-0" data-target="#tab-ledger">
                    <i class="fas fa-receipt text-base sm:text-xs"></i> 
                    <span class="hidden sm:inline">Buku Besar / Mutasi Kas</span>
                    <span class="inline sm:hidden text-[9px] leading-tight text-center font-bold">Buku Besar</span>
                </button>
                <button type="button" class="tab-btn px-3 py-2.5 sm:px-6 sm:py-3 text-[10px] sm:text-xs text-gray-500 rounded-xl transition-all hover:bg-gray-100/50 cursor-pointer border-none outline-none flex flex-col sm:flex-row items-center justify-center gap-1.5 sm:gap-2 grow sm:grow-0" data-target="#tab-reconciliation">
                    <i class="fas fa-scale-balanced text-base sm:text-xs"></i> 
                    <span class="hidden sm:inline">Rekonsiliasi Penjualan Harian</span>
                    <span class="inline sm:hidden text-[9px] leading-tight text-center font-bold">Rekonsiliasi</span>
                </button>
                <button type="button" class="tab-btn px-3 py-2.5 sm:px-6 sm:py-3 text-[10px] sm:text-xs text-gray-500 rounded-xl transition-all hover:bg-gray-100/50 cursor-pointer border-none outline-none flex flex-col sm:flex-row items-center justify-center gap-1.5 sm:gap-2 grow sm:grow-0" data-target="#tab-price-comparison">
                    <i class="fas fa-chart-line text-base sm:text-xs"></i> 
                    <span class="hidden sm:inline">Perbandingan Harga Barang</span>
                    <span class="inline sm:hidden text-[9px] leading-tight text-center font-bold">Analisis Harga</span>
                </button>
            </div>

            <!-- Tab Content 1: Mutasi Kas -->
            <div id="tab-ledger" class="tab-content p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-gray-800 text-base">Riwayat Mutasi Kas</h3>
                            <span id="active-filter-badge" class="hidden items-center gap-1.5 px-2.5 py-1 bg-brand/10 text-brand text-[10px] font-bold rounded-full border border-brand/20">
                                <i class="fas fa-filter text-[9px]"></i> Akun: <span id="active-filter-name"></span>
                                <button type="button" id="btn-clear-account-filter" class="hover:text-red-600 font-bold ml-1 cursor-pointer bg-transparent border-none outline-none">
                                    <i class="fas fa-circle-xmark text-sm leading-none align-middle"></i>
                                </button>
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Catatan lengkap seluruh transaksi uang masuk dan uang keluar</p>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table id="table-ledger-data" class="w-full text-sm text-left text-gray-600">
                        <thead class="bg-gray-50 text-xs text-gray-400 uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 rounded-s-xl">Tanggal</th>
                                <th class="px-6 py-4">Akun Keuangan</th>
                                <th class="px-6 py-4">Tipe</th>
                                <th class="px-6 py-4">Keterangan / Deskripsi</th>
                                <th class="px-6 py-4 text-right">Nominal</th>
                                <th class="px-6 py-4 text-center rounded-e-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transactions as $trx)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        <div class="flex flex-col">
                                            <span>{{ date('d M Y', strtotime($trx->transaction_date)) }}</span>
                                            <span class="text-[10px] text-gray-400 font-normal mt-0.5">{{ date('H:i', strtotime($trx->created_at)) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700">
                                        @if($trx->type === 'transfer')
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-semibold text-gray-700">{{ $trx->account->name }}</span>
                                                <i class="fas fa-arrow-right text-[10px] text-indigo-400"></i>
                                                <span class="font-semibold text-indigo-600">{{ $trx->destinationAccount ? $trx->destinationAccount->name : 'Akun Tujuan' }}</span>
                                            </div>
                                        @else
                                            {{ $trx->account->name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 items-start">
                                            <div class="flex flex-wrap gap-1 items-center">
                                                @if($trx->type === 'income')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 bg-emerald-50 text-emerald-700 text-[9px] sm:text-[10px] font-bold rounded-full">
                                                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-emerald-500"></span>
                                                        <span class="hidden sm:inline">Uang Masuk</span>
                                                        <span class="inline sm:hidden"><i class="fas fa-arrow-down text-[8px]"></i> Masuk</span>
                                                    </span>
                                                    @if($trx->is_sales_reconciliation)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-blue-50 text-blue-700 text-[9px] font-bold rounded border border-blue-100" title="Rekonsiliasi omzet {{ $trx->reconciliation_date }}">
                                                            <i class="fas fa-arrows-spin text-[8px] animate-spin"></i>
                                                            <span class="hidden sm:inline">Rekon Sales</span>
                                                        </span>
                                                    @endif
                                                @elseif($trx->type === 'expense')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 bg-rose-50 text-rose-700 text-[9px] sm:text-[10px] font-bold rounded-full">
                                                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-rose-500"></span>
                                                        <span class="hidden sm:inline">Uang Keluar</span>
                                                        <span class="inline sm:hidden"><i class="fas fa-arrow-up text-[8px]"></i> Keluar</span>
                                                    </span>
                                                    @if($trx->items && count($trx->items) > 0)
                                                        <button type="button" class="btn-view-items inline-flex items-center gap-1 px-1.5 py-0.5 bg-indigo-50 text-indigo-700 text-[9px] font-bold rounded border border-indigo-100 cursor-pointer" 
                                                            data-items="{{ json_encode($trx->items) }}" 
                                                            data-place="{{ $trx->purchase_place ?: 'Tidak Diketahui' }}"
                                                            data-reference="{{ $trx->reference ?: '-' }}" 
                                                            data-date="{{ date('d M Y', strtotime($trx->transaction_date)) }}" 
                                                            data-amount="{{ number_format($trx->amount, 0, ',', '.') }}"
                                                            title="Detail Belanja">
                                                            <i class="fas fa-basket-shopping text-[8px]"></i> 
                                                            <span>{{ count($trx->items) }}<span class="hidden sm:inline"> Barang</span></span>
                                                        </button>
                                                    @endif
                                                @elseif($trx->type === 'transfer')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 bg-indigo-50 text-indigo-700 text-[9px] sm:text-[10px] font-bold rounded-full">
                                                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-indigo-500"></span>
                                                        <span class="hidden sm:inline">Transfer Kas</span>
                                                        <span class="inline sm:hidden"><i class="fas fa-arrow-right-arrow-left text-[8px]"></i> Transfer</span>
                                                    </span>
                                                @endif
                                            </div>
                                            @if($trx->category)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-indigo-50/60 text-indigo-700 text-[9px] font-bold rounded border border-indigo-100/60 animate-fade-in" title="Kategori: {{ $trx->category->name }}">
                                                    <i class="fas fa-tag text-[8px]"></i> {{ $trx->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 max-w-[200px] truncate" title="{{ $trx->description }}">
                                        {{ $trx->description ?: '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-right font-bold font-mono text-sm {{ $trx->type === 'income' ? 'text-emerald-600' : ($trx->type === 'expense' ? 'text-rose-600' : 'text-indigo-600') }}">
                                        {{ $trx->type === 'income' ? '+' : ($trx->type === 'expense' ? '-' : '⇄') }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Edit Button -->
                                            <button type="button" class="btn-edit-transaction w-7 h-7 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg flex items-center justify-center cursor-pointer border-none outline-none"
                                                data-uuid="{{ $trx->uuid }}"
                                                data-type="{{ $trx->type }}"
                                                data-account="{{ $trx->account_id }}"
                                                data-destination="{{ $trx->destination_account_id }}"
                                                data-category="{{ $trx->category_id }}"
                                                data-amount="{{ $trx->amount }}"
                                                data-date="{{ $trx->transaction_date }}"
                                                data-reference="{{ $trx->reference }}"
                                                data-description="{{ $trx->description }}"
                                                data-is-reconciliation="{{ $trx->is_sales_reconciliation ? '1' : '0' }}"
                                                data-reconciliation-date="{{ $trx->reconciliation_date }}"
                                                data-operational-expense="{{ $trx->operational_expense }}"
                                                data-cash-drawer-amount="{{ $trx->cash_drawer_amount }}"
                                                data-purchase-place="{{ $trx->purchase_place }}"
                                                data-purchase-request-id="{{ $trx->purchase_request_id }}"
                                                data-items="{{ json_encode($trx->items) }}"
                                                title="Edit Transaksi">
                                                <i class="fas fa-pen-to-square"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <form method="POST" action="{{ route('cashflow.transactions.destroy', $trx->uuid) }}" class="inline-block form-delete-transaction m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-delete-transaction-trigger w-7 h-7 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg flex items-center justify-center cursor-pointer border-none outline-none">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content 2: Rekonsiliasi Penjualan Harian -->
            <div id="tab-reconciliation" class="tab-content p-6 hidden">
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 text-base">Rekonsiliasi & Cross-Check Penjualan Harian</h3>
                    <p class="text-xs text-gray-400 font-medium">Bandingkan total omzet riil kasir dengan total uang masuk yang dicatat di buku kas</p>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full text-xs text-left text-gray-600">
                        <thead class="bg-gray-50 text-[10px] text-gray-400 uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 rounded-s-xl">Tanggal Penjualan</th>
                                <th class="px-6 py-4 text-right">Total Omzet Kasir (Riil)</th>
                                <th class="px-6 py-4 text-right">Kas Masuk Terdaftar (Cash Flow)</th>
                                <th class="px-6 py-4 text-right">Selisih (*Variance*)</th>
                                <th class="px-6 py-4 text-center">Status Match</th>
                                <th class="px-6 py-4 text-center rounded-e-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($reconciliations as $recon)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $recon->formatted_date }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold font-mono text-gray-700">
                                        Rp {{ number_format($recon->total_sales, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold font-mono text-indigo-600">
                                        Rp {{ number_format($recon->total_reconciled, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold font-mono {{ $recon->variance == 0 ? 'text-gray-400' : ($recon->variance > 0 ? 'text-amber-600' : 'text-rose-600') }}">
                                        Rp {{ number_format($recon->variance, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($recon->status === 'empty')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-400 text-[10px] font-semibold rounded">
                                                Tidak Ada Penjualan
                                            </span>
                                        @elseif($recon->status === 'match')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-100">
                                                <i class="fas fa-check-circle"></i> Cocok (Match)
                                            </span>
                                        @elseif($recon->status === 'mismatch')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-full border border-rose-100">
                                                <i class="fas fa-times-circle"></i> Mismatch / Selisih
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-100">
                                                <i class="fas fa-triangle-exclamation"></i> Belum Direkon
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($recon->status !== 'empty' && $recon->status !== 'match')
                                            <button type="button" class="btn-quick-reconcile px-3 py-1.5 bg-brand hover:bg-brand-dark text-white rounded-lg text-[10px] font-bold shadow-md shadow-brand/10 transition-all cursor-pointer border-none outline-none" 
                                                data-date="{{ $recon->date }}" 
                                                data-sales="{{ $recon->total_sales }}" 
                                                data-variance="{{ $recon->variance }}">
                                                <i class="fas fa-scale-balanced me-1"></i> Rekonsiliasi
                                            </button>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content 3: Perbandingan Harga Barang -->
            <div id="tab-price-comparison" class="tab-content p-6 hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Perbandingan & Analisis Harga Barang</h3>
                        <p class="text-xs text-gray-400 font-medium">Bandingkan harga pembelian barang operasional antar toko/merchant untuk menemukan harga termurah.</p>
                    </div>
                    <div class="relative w-full max-w-xs">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" id="search-price-items" placeholder="Cari nama barang..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-brand bg-gray-50/30">
                    </div>
                </div>

                @if(count($priceComparisons) === 0)
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-xl mb-3">
                            <i class="fas fa-basket-shopping"></i>
                        </div>
                        <h4 class="font-bold text-gray-700 text-sm">Belum Ada Riwayat Belanja</h4>
                        <p class="text-xs text-gray-400 max-w-xs mt-1">Catat transaksi Uang Keluar dengan rincian barang belanjaan untuk mulai merekam perbandingan harga.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="comparison-items-grid">
                        @foreach($priceComparisons as $comp)
                            <div class="comparison-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-200" data-name="{{ strtolower($comp->display_name) }}">
                                <div>
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-bold text-gray-800 text-sm capitalize flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-brand"></span>
                                            {{ $comp->display_name }}
                                        </h4>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2 bg-gray-50/50 p-3.5 rounded-xl border border-gray-100/50">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-400 font-medium">Harga Termurah:</span>
                                            <span class="font-bold text-emerald-600 font-mono">Rp {{ number_format($comp->cheapest_price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[10px] -mt-1 border-b border-gray-100 pb-2">
                                            <span class="text-gray-400 font-medium">Toko Termurah:</span>
                                            <span class="font-semibold text-gray-600 capitalize"><i class="fas fa-store text-emerald-500/80 me-1"></i>{{ $comp->cheapest_place }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs pt-1.5">
                                            <span class="text-gray-400 font-medium">Harga Terakhir:</span>
                                            <span class="font-bold text-indigo-600 font-mono">Rp {{ number_format($comp->latest_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-50 flex justify-end">
                                    <button type="button" class="btn-expand-comparison px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-lg border border-indigo-100 cursor-pointer flex items-center gap-1.5 transition-all" data-comp-data="{{ json_encode($comp) }}">
                                        <i class="fas fa-chart-line"></i> Riwayat & Perbandingan
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL 1: Tambah Akun Keuangan -->
    <div id="modal-account" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white shrink-0">
                <h3 class="text-base font-bold text-gray-800" id="account-modal-title">Tambah Akun Keuangan</h3>
                <button type="button" class="close-account-modal text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex justify-center items-center cursor-pointer border-none">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('cashflow.accounts.store') }}" id="form-account">
                @csrf
                <div class="p-5 flex flex-col gap-4">
                    <input type="hidden" name="_method" id="account-method" value="POST">
                    
                    <div class="form-group flex flex-col gap-1">
                        <label for="account_name" class="text-xs font-bold text-gray-600">Nama Akun Keuangan</label>
                        <input type="text" name="name" id="account_name" placeholder="Misal: Cash / Laci Toko, Bank Mandiri" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-brand" required>
                    </div>

                    <div class="form-group flex flex-col gap-1">
                        <label for="account_number" class="text-xs font-bold text-gray-600">Nomor Rekening / Keterangan (Opsional)</label>
                        <input type="text" name="account_number" id="account_number" placeholder="Misal: 1234567890" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-brand">
                    </div>

                    <div class="form-group flex flex-col gap-1">
                        <label for="account_balance" class="text-xs font-bold text-gray-600">Saldo Awal (Rp)</label>
                        <input type="number" name="initial_balance" id="account_balance" placeholder="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-brand" required min="0">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group flex flex-col gap-1">
                            <label for="account_icon" class="text-xs font-bold text-gray-600">Ikon Representasi</label>
                            <select name="icon" id="account_icon" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-brand" required>
                                <option value="fa-wallet">Wallet (Tunai/Laci)</option>
                                <option value="fa-building-columns">Columns (Bank/Transfer)</option>
                                <option value="fa-credit-card">Credit Card (Kartu Kredit/Debit)</option>
                                <option value="fa-money-bill-transfer">Bill Transfer (Kas Kecil)</option>
                                <option value="fa-coins">Coins (Receh/Tunai)</option>
                                <option value="fa-piggy-bank">Piggy Bank (Tabungan)</option>
                            </select>
                        </div>

                        <div class="form-group flex flex-col gap-1">
                            <label for="account_color" class="text-xs font-bold text-gray-600">Warna Identitas (Highlight)</label>
                            <div class="flex items-center gap-2 mt-0.5">
                                <input type="color" name="color" id="account_color" value="#6366f1" class="w-10 h-10 border border-gray-200 rounded-xl p-0.5 cursor-pointer bg-white">
                                <span class="text-[10px] text-gray-400 font-bold font-mono" id="account_color_label">#6366F1</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group flex flex-col gap-1">
                        <label for="account_description" class="text-xs font-bold text-gray-600">Deskripsi singkat (Opsional)</label>
                        <textarea name="description" id="account_description" rows="3" placeholder="Tulis catatan singkat di sini..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-brand"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 shrink-0">
                    <button type="button" class="close-account-modal px-4 py-2 bg-white text-gray-500 hover:bg-gray-100 rounded-xl text-xs font-bold transition-all border border-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand hover:bg-brand-dark text-white rounded-xl text-xs font-bold shadow-lg shadow-brand/10 transition-all cursor-pointer border-none" id="btn-save-account">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Catat Transaksi Kas & Rekonsiliasi -->
    <div id="modal-transaction" tabindex="-1" class="fixed inset-0 z-50 hidden flex sm:items-center items-stretch justify-center sm:p-4 p-0 bg-black/40 backdrop-blur-sm" style="zoom: 1.1125;">
        <div class="relative w-full max-w-2xl bg-white sm:rounded-3xl rounded-none shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col sm:max-h-[90vh] max-h-screen sm:h-auto h-full">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Catat Transaksi Buku Kas</h3>
                <button type="button" class="close-transaction-modal text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex justify-center items-center cursor-pointer border-none">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('cashflow.transactions.store') }}" id="form-transaction" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <div class="p-5 overflow-y-auto flex-grow sm:max-h-[60vh] h-full flex flex-col gap-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group flex flex-col gap-1">
                            <label for="trx_type" class="text-sm font-bold text-gray-600">Jenis Transaksi</label>
                            <select name="type" id="trx_type" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand" required>
                                <option value="income">Uang Masuk / Pemasukan</option>
                                <option value="expense">Uang Keluar / Pengeluaran</option>
                                <option value="transfer">Transfer Antar Rekening</option>
                            </select>
                        </div>

                        <div class="form-group flex flex-col gap-1" id="container-trx-account">
                            <label for="trx_account" class="text-sm font-bold text-gray-600" id="label-trx-account">Simpan Ke Akun</label>
                            <select name="account_id" id="trx_account" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand" required>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->uuid }}">{{ $acc->name }} (Saldo: Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group flex flex-col gap-1 hidden animate-fade-in" id="section-transfer-destination">
                        <label for="trx_destination_account" class="text-sm font-bold text-gray-600">Transfer Ke Akun (Tujuan)</label>
                        <select name="destination_account_id" id="trx_destination_account" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand">
                            <!-- Dinamis via JS -->
                        </select>
                    </div>

                    <div class="form-group flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <label for="trx_category" class="text-sm font-bold text-gray-600">Kategori Transaksi</label>
                            <button type="button" id="btn-manage-categories" class="text-xs text-brand hover:text-brand-dark font-bold flex items-center gap-1 cursor-pointer bg-transparent border-none outline-none">
                                <i class="fas fa-gear"></i> Kelola Kategori
                            </button>
                        </div>
                        <select name="category_id" id="trx_category" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand">
                            <!-- Dinamis via JS -->
                        </select>
                    </div>

                    <!-- Rekonsiliasi Penjualan Harian Section (Only for Uang Masuk) -->
                    <div id="section-reconciliation-toggle" class="bg-indigo-50/60 p-4 rounded-2xl border border-indigo-100 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-indigo-950">Rekonsiliasi Dengan Penjualan Harian</h4>
                                <p class="text-xs text-indigo-600 font-medium">Bandingkan dan cocokkan kas masuk dengan omzet harian POS</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="is_sales_reconciliation" id="is_sales_reconciliation" value="1" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                            </label>
                        </div>

                        <!-- Datepicker & AJAX Checker UI -->
                        <div id="section-reconciliation-date" class="hidden flex-col gap-2.5 border-t border-indigo-100/50 pt-3">
                            <div class="form-group flex flex-col gap-1">
                                <label for="reconciliation_date" class="text-xs font-bold text-indigo-900">Pilih Tanggal Penjualan</label>
                                <input type="date" name="reconciliation_date" id="reconciliation_date" class="w-full px-4 py-2 border border-indigo-200 text-sm rounded-xl focus:outline-none focus:border-brand max-w-[200px]">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group flex flex-col gap-1">
                                    <label for="operational_expense" class="text-xs font-bold text-indigo-900">Biaya Operasional (Rp)</label>
                                    <input type="number" name="operational_expense" id="operational_expense" placeholder="0" class="w-full px-4 py-2 border border-indigo-200 text-sm rounded-xl focus:outline-none focus:border-brand" min="0">
                                </div>
                                <div class="form-group flex flex-col gap-1">
                                    <label for="cash_drawer_amount" class="text-xs font-bold text-indigo-900">Uang di Laci Kasir (Rp)</label>
                                    <input type="number" name="cash_drawer_amount" id="cash_drawer_amount" placeholder="0" class="w-full px-4 py-2 border border-indigo-200 text-sm rounded-xl focus:outline-none focus:border-brand" min="0">
                                </div>
                            </div>
                            <p class="text-[10px] text-indigo-500 mt-0.5">Biaya operasional dan sisa uang di laci kasir akan memotong langsung nominal omzet penjualan yang disetor.</p>

                            <!-- Live AJAX Data Display -->
                            <div id="reconciliation-live-status" class="bg-white p-3 rounded-xl border border-indigo-100 hidden">
                                <div class="grid grid-cols-2 gap-2 text-[10px] text-gray-500 mb-2">
                                    <div>Omzet Riil Kasir: <span class="font-bold text-gray-800 block text-xs font-mono" id="label-actual-sales">Rp 0</span></div>
                                    <div>Kas Masuk Terbayar: <span class="font-bold text-indigo-600 block text-xs font-mono" id="label-reconciled-sales">Rp 0</span></div>
                                </div>
                                <div class="flex items-center justify-between border-t border-gray-50 pt-2">
                                    <div class="text-[9px] font-semibold text-gray-400">
                                        Selisih yang belum dicatat: <span class="font-mono text-gray-700 block text-xs font-bold" id="label-remaining-sales">Rp 0</span>
                                    </div>
                                    <button type="button" id="btn-copy-sales" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[9px] font-bold rounded-lg border border-indigo-100 cursor-pointer flex items-center gap-1 transition-all">
                                        <i class="fas fa-copy"></i> Salin Nominal
                                    </button>
                                </div>
                                <!-- Visual Match / Mismatch status badge -->
                                <div class="mt-2.5 pt-2 border-t border-gray-50 flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wide">Status Kecocokan</span>
                                    <span id="badge-match-status" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded font-bold text-[9px]">
                                        Checking...
                                    </span>
                                </div>
                            </div>

                            <!-- Pembagian Akun Rekonsiliasi (Split-Reconciliation UI) -->
                            <div id="section-reconciliation-splits" class="hidden flex-col gap-2.5 border-t border-indigo-100/50 pt-3">
                                <label class="text-xs font-bold text-indigo-900 block mb-1">Pembagian Uang Masuk ke Akun Keuangan</label>
                                <div class="flex flex-col gap-2 bg-white p-3 rounded-2xl border border-indigo-50">
                                    @foreach($accounts as $acc)
                                        <div class="flex items-center justify-between gap-3 py-1.5 border-b border-gray-50 last:border-b-0">
                                            <span class="text-sm font-bold text-gray-700 truncate max-w-[180px]">{{ $acc->name }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-400 font-bold">Rp</span>
                                                <input type="number" name="account_splits[{{ $acc->uuid }}]" data-uuid="{{ $acc->uuid }}" placeholder="0" class="split-amount-input w-28 px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg text-right font-mono font-bold focus:outline-none focus:border-brand" min="0" value="0">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex items-center justify-between text-xs font-bold text-indigo-900 bg-indigo-50/50 p-2.5 rounded-xl border border-indigo-100/30">
                                    <span>Total Pembagian:</span>
                                    <span id="label-split-total" class="font-mono text-sm text-indigo-700">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Barang Belanja Section (Only for Uang Keluar) -->
                    <div id="section-shopping-items" class="bg-rose-50/60 p-4 rounded-2xl border border-rose-100 flex flex-col gap-3 hidden animate-fade-in">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <div>
                                <h4 class="text-sm font-bold text-rose-950">Daftar Rincian Barang Belanja</h4>
                                <p class="text-xs text-rose-600 font-medium">Tulis rincian barang yang Anda beli (Opsional)</p>
                            </div>
                            <button type="button" id="btn-add-shopping-item" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/15 transition-all flex items-center justify-center gap-1.5 cursor-pointer border-none outline-none sm:w-auto w-full">
                                <i class="fas fa-plus"></i> Tambah Barang
                            </button>
                        </div>

                        <!-- Tempat Pembelian / Toko -->
                        <div class="form-group flex flex-col gap-1">
                            <label for="purchase_place" class="text-xs font-bold text-rose-950 uppercase tracking-wider">Tempat Pembelian / Nama Toko (Opsional)</label>
                            <input type="text" name="purchase_place" id="purchase_place" placeholder="Misal: Toko Makmur, Indomaret, Superindo" class="w-full px-3.5 py-2 bg-white rounded-xl border border-rose-200 text-sm focus:outline-none focus:border-rose-400 font-semibold text-gray-700">
                        </div>

                        <!-- Tutup Pengajuan Belanja -->
                        <div class="form-group flex flex-col gap-1">
                            <label for="trx_purchase_request" class="text-xs font-bold text-rose-950 uppercase tracking-wider">Tutup Pengajuan Belanja (Opsional)</label>
                            <select name="purchase_request_id" id="trx_purchase_request" class="w-full px-3.5 py-2 bg-white rounded-xl border border-rose-200 text-sm focus:outline-none focus:border-rose-400 font-semibold text-gray-700">
                                <option value="">-- Tidak terkait pengajuan --</option>
                                @foreach($pendingPurchaseRequests as $pr)
                                    <option value="{{ $pr->uuid }}">{{ $pr->request_date->format('d M Y') }} &mdash; {{ $pr->items->pluck('item_name')->implode(', ') }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-rose-500">Pilih kalau pembelian ini memenuhi pengajuan yang masih pending &mdash; rincian barang otomatis terisi & pengajuan ditandai selesai.</p>
                        </div>

                        <!-- Modern Table-like Container -->
                        <div class="border border-rose-100 rounded-xl bg-white shadow-sm mt-1">
                            <!-- Table Header -->
                            <div class="hidden sm:grid grid-cols-12 gap-2 bg-rose-50/50 px-3 py-2 border-b border-rose-100 text-xs font-bold text-rose-950 uppercase tracking-wider">
                                <div class="col-span-6">Nama Barang</div>
                                <div class="col-span-2 text-center">Jumlah</div>
                                <div class="col-span-3 text-right">Harga Satuan</div>
                                <div class="col-span-1 text-center">Aksi</div>
                            </div>

                            <!-- Dynamic Items Container (Table Body) -->
                            <div id="shopping-items-list" class="flex flex-col divide-y divide-rose-50">
                                <!-- Rows will be dynamically appended here -->
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-rose-100/50 pt-3 text-xs font-bold text-rose-900">
                            <div>
                                <span>Total Item Belanja:</span>
                                <span id="label-shopping-total" class="font-mono text-sm block mt-0.5">Rp 0</span>
                            </div>
                            <button type="button" id="btn-add-shopping-item-bottom" class="px-3 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/15 transition-all flex items-center justify-center gap-1.5 cursor-pointer border-none outline-none sm:w-auto w-full">
                                <i class="fas fa-plus"></i> Tambah Barang
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="form-group flex flex-col gap-1">
                            <label for="trx_amount" class="text-sm font-bold text-gray-600">Nominal Transaksi (Rp)</label>
                            <input type="number" name="amount" id="trx_amount" placeholder="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand" required min="1">
                        </div>

                        <div class="form-group flex flex-col gap-1">
                            <label for="trx_date" class="text-sm font-bold text-gray-600">Tanggal Kas</label>
                            <input type="date" name="transaction_date" id="trx_date" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group flex flex-col gap-1">
                        <label for="trx_reference" class="text-sm font-bold text-gray-600">No Referensi / Invoice Pendukung (Opsional)</label>
                        <input type="text" name="reference" id="trx_reference" placeholder="Misal: INV-8089, BCA-TRX-102" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand">
                    </div>

                    <div class="form-group flex flex-col gap-1">
                        <label for="trx_description" class="text-sm font-bold text-gray-600">Deskripsi singkat (Opsional)</label>
                        <textarea name="description" id="trx_description" rows="2" placeholder="Detail pengeluaran atau rincian kas..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 shrink-0 sm:flex-row flex-col-reverse sm:w-auto w-full">
                    <button type="button" class="close-transaction-modal px-5 py-2.5 bg-white text-gray-500 hover:bg-gray-100 rounded-xl text-sm font-bold transition-all border border-gray-200 cursor-pointer sm:w-auto w-full text-center">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-600/10 transition-all cursor-pointer border-none sm:w-auto w-full text-center">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Detail Belanja Uang Keluar -->
    <div id="modal-shopping-details" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-800"><i class="fas fa-basket-shopping text-indigo-600 me-1.5"></i>Rincian Barang Belanja</h3>
                    <p class="text-[10px] text-gray-400 font-medium">
                        Tanggal: <span id="detail-date" class="font-bold text-gray-600"></span> | 
                        Ref: <span id="detail-ref" class="font-bold text-gray-600"></span>
                    </p>
                    <p class="text-[10px] text-gray-400 font-medium mt-0.5">
                        Toko: <span id="detail-place" class="font-bold text-indigo-600"></span>
                    </p>
                </div>
                <button type="button" class="close-shopping-modal text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex justify-center items-center cursor-pointer border-none">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="p-5 overflow-y-auto flex-grow">
                <table class="w-full text-xs text-left text-gray-600">
                    <thead class="bg-gray-50 text-[10px] text-gray-400 uppercase font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-2.5 rounded-s-lg">Nama Barang</th>
                            <th class="px-4 py-2.5 text-center">Qty</th>
                            <th class="px-4 py-2.5 text-right">Harga</th>
                            <th class="px-4 py-2.5 text-right rounded-e-lg">Total</th>
                        </tr>
                    </thead>
                    <tbody id="shopping-details-body" class="divide-y divide-gray-50 font-medium">
                        <!-- Dynamic items will be added here via JS -->
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between shrink-0">
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase block leading-none">Total Belanja</span>
                    <span id="detail-total-amount" class="text-base font-extrabold text-rose-600 font-mono">Rp 0</span>
                </div>
                <button type="button" class="close-shopping-modal px-4 py-2 bg-white text-gray-500 hover:bg-gray-100 rounded-xl text-xs font-bold transition-all border border-gray-200 cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Detail Perbandingan Harga Barang -->
    <div id="modal-price-comparison-details" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-800"><i class="fas fa-scale-balanced text-indigo-600 me-1.5"></i>Perbandingan Harga: <span id="comp-detail-item-name" class="capitalize"></span></h3>
                    <p class="text-[10px] text-gray-400 font-medium">Bandingkan harga antar toko (Termurah) dan lacak tren harga dari waktu ke waktu</p>
                </div>
                <button type="button" class="close-comp-modal text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex justify-center items-center cursor-pointer border-none">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            
            <!-- Sub tabs inside Comparison Modal -->
            <div class="flex border-b border-gray-100 bg-gray-50/50 p-2 shrink-0">
                <button type="button" class="sub-tab-btn px-4 py-2 font-bold text-[10px] rounded-lg transition-all cursor-pointer border-none outline-none bg-white text-indigo-600 shadow-sm" data-target="#sub-tab-cheapest">
                    <i class="fas fa-store me-1"></i> Perbandingan Toko (Termurah)
                </button>
                <button type="button" class="sub-tab-btn px-4 py-2 font-semibold text-[10px] text-gray-500 rounded-lg transition-all hover:bg-gray-100/50 cursor-pointer border-none outline-none" data-target="#sub-tab-trends">
                    <i class="fas fa-clock-rotate-left me-1"></i> Riwayat Tren Harga (Terbaru)
                </button>
            </div>

            <div class="p-5 overflow-y-auto flex-grow max-h-[50vh]">
                <!-- Cheapest purchases table -->
                <div id="sub-tab-cheapest" class="sub-tab-content">
                    <table class="w-full text-xs text-left text-gray-600">
                        <thead class="bg-gray-50 text-[9px] text-gray-400 uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-3 py-2 rounded-s-lg">Toko / Merchant</th>
                                <th class="px-3 py-2 text-right">Harga Satuan</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right rounded-e-lg">Total</th>
                            </tr>
                        </thead>
                        <tbody id="comp-cheapest-body" class="divide-y divide-gray-50 font-medium">
                            <!-- Dynamic rows via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Chronological history table -->
                <div id="sub-tab-trends" class="sub-tab-content hidden">
                    <table class="w-full text-xs text-left text-gray-600">
                        <thead class="bg-gray-50 text-[9px] text-gray-400 uppercase font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-3 py-2 rounded-s-lg">Tanggal</th>
                                <th class="px-3 py-2">Toko</th>
                                <th class="px-3 py-2 text-right font-semibold">Harga</th>
                                <th class="px-3 py-2 text-center rounded-e-lg">Qty</th>
                            </tr>
                        </thead>
                        <tbody id="comp-trends-body" class="divide-y divide-gray-50 font-medium">
                            <!-- Dynamic rows via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
                <button type="button" class="close-comp-modal px-4 py-2 bg-white text-gray-500 hover:bg-gray-100 rounded-xl text-xs font-bold transition-all border border-gray-200 cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 5: Kelola Kategori Kas -->
    <div id="modal-manage-categories" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-800"><i class="fas fa-gear text-brand me-1.5"></i>Kelola Kategori Kas</h3>
                    <p class="text-[10px] text-gray-400 font-medium">Tambah atau hapus klasifikasi transaksi kasir Anda</p>
                </div>
                <button type="button" class="close-category-modal text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex justify-center items-center cursor-pointer border-none">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            
            <div class="p-5 overflow-y-auto flex-grow flex flex-col gap-6">
                <!-- Kategori Pemasukan -->
                <div class="flex flex-col gap-3">
                    <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Kategori Pemasukan
                    </h4>
                    
                    <form method="POST" action="{{ route('cashflow.categories.store') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="type" value="income">
                        <input type="text" name="name" placeholder="Tambah kategori pemasukan baru..." class="flex-grow px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand" required>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/10 transition-all flex items-center justify-center cursor-pointer border-none"><i class="fas fa-plus"></i></button>
                    </form>
                    
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($incomeCategories as $cat)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-100">
                                {{ $cat->name }}
                                <button type="button" class="btn-delete-category hover:text-red-600 font-bold ml-0.5 cursor-pointer bg-transparent border-none outline-none text-[11px]" data-uuid="{{ $cat->uuid }}" data-name="{{ $cat->name }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endforeach
                        @if(count($incomeCategories) === 0)
                            <span class="text-xs text-gray-400 italic">Belum ada kategori pemasukan.</span>
                        @endif
                    </div>
                </div>

                <!-- Kategori Pengeluaran -->
                <div class="flex flex-col gap-3 border-t border-gray-100 pt-4">
                    <h4 class="text-xs font-bold text-rose-950 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Kategori Pengeluaran
                    </h4>
                    
                    <form method="POST" action="{{ route('cashflow.categories.store') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="type" value="expense">
                        <input type="text" name="name" placeholder="Tambah kategori pengeluaran baru..." class="flex-grow px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand" required>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/10 transition-all flex items-center justify-center cursor-pointer border-none"><i class="fas fa-plus"></i></button>
                    </form>
                    
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($expenseCategories as $cat)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 text-[10px] font-bold rounded-full border border-rose-100">
                                {{ $cat->name }}
                                <button type="button" class="btn-delete-category hover:text-red-600 font-bold ml-0.5 cursor-pointer bg-transparent border-none outline-none text-[11px]" data-uuid="{{ $cat->uuid }}" data-name="{{ $cat->name }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endforeach
                        @if(count($expenseCategories) === 0)
                            <span class="text-xs text-gray-400 italic">Belum ada kategori pengeluaran.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end shrink-0">
                <button type="button" class="close-category-modal px-4 py-2 bg-white text-gray-500 hover:bg-gray-100 rounded-xl text-xs font-bold transition-all border border-gray-200 cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Hidden global delete category form -->
    <form id="form-delete-category" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Datalist untuk Autocomplete Rekomendasi Barang Belanja -->
    <datalist id="historical-items-list">
        @foreach($historicalItemNames as $name)
            <option value="{{ $name }}">
        @endforeach
    </datalist>

    <!-- JAVASCRIPT LOGIC & LIBRARIES -->
    <script type="module">
        $(document).ready(function() {
            // Categories Data & Historical Items Autocomplete
            const incomeCategories = @json($incomeCategories);
            const expenseCategories = @json($expenseCategories);
            const historicalItems = @json($historicalItemNames);
            const activeSupplyItems = @json($activeSupplyItems);
            const pendingPurchaseRequestsData = @json($pendingPurchaseRequestsData);

            function escapeHtml(str) {
                return String(str).replace(/[&<>"']/g, function(c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            function populateCategories(type, selectedCategoryId = null) {
                const categorySelect = $('#trx_category');
                categorySelect.empty();
                
                const categories = (type === 'income') ? incomeCategories : expenseCategories;
                
                if (categories.length === 0) {
                    categorySelect.append('<option value="">-- Tidak ada kategori --</option>');
                } else {
                    categorySelect.append('<option value="">-- Pilih Kategori (Opsional) --</option>');
                    categories.forEach(function(cat) {
                        const selected = (selectedCategoryId === cat.uuid) ? 'selected' : '';
                        categorySelect.append(`<option value="${cat.uuid}" ${selected}>${cat.name}</option>`);
                    });
                }
            }

            // Open Category Modal
            $('#btn-manage-categories').on('click', function(e) {
                e.preventDefault();
                $('#modal-manage-categories').removeClass('hidden');
            });

            // Close Category Modal
            $('.close-category-modal').on('click', function() {
                $('#modal-manage-categories').addClass('hidden');
            });

            // Delete Category click handler
            $('.btn-delete-category').on('click', function(e) {
                e.preventDefault();
                const uuid = $(this).data('uuid');
                const name = $(this).data('name');
                let deleteUrl = "{{ route('cashflow.categories.destroy', ':uuid') }}";
                deleteUrl = deleteUrl.replace(':uuid', uuid);
                
                cConfirm("Hapus Kategori", `Apakah Anda yakin ingin menghapus kategori "${name}"? Kategori pada transaksi terkait akan menjadi kosong.`, function() {
                    $('#form-delete-category').attr('action', deleteUrl).submit();
                });
            });

            // Initialize Ledger DataTable
            $('#table-ledger-data').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthChange": false,
                "order": [],
                "language": {
                    "search": "Cari mutasi:",
                    "zeroRecords": "Tidak ditemukan mutasi kas.",
                    "paginate": {
                        "next": "<i class='fas fa-chevron-right text-[10px]'></i>",
                        "previous": "<i class='fas fa-chevron-left text-[10px]'></i>"
                    }
                }
            });

            // Tab Switcher Logic
            $('.tab-btn').on('click', function() {
                const target = $(this).data('target');
                
                // Active button styling
                $('.tab-btn').removeClass('bg-white text-brand shadow-sm font-bold').addClass('text-gray-500 font-semibold hover:bg-gray-100/50');
                $(this).removeClass('text-gray-500 font-semibold hover:bg-gray-100/50').addClass('bg-white text-brand shadow-sm font-bold');
                
                // Hide all tabs & show target
                $('.tab-content').addClass('hidden');
                $(target).removeClass('hidden');
            });

            // Account Color Label Live Updater
            $('#account_color').on('input change', function() {
                $('#account_color_label').text($(this).val().toUpperCase());
            });

            // Account Modal Controls (Add)
            $('.btn-add-account').on('click', function() {
                $('#account-modal-title').text('Tambah Akun Keuangan');
                $('#account-method').val('POST');
                $('#form-account').attr('action', "{{ route('cashflow.accounts.store') }}");
                $('#account_name').val('');
                $('#account_number').val('');
                $('#account_balance').val('0').prop('readonly', false);
                $('#account_description').val('');
                $('#account_icon').val('fa-wallet');
                $('#account_color').val('#6366f1').trigger('change');
                $('#modal-account').removeClass('hidden');
            });

            // Account Modal Controls (Edit)
            $('.btn-edit-account').on('click', function() {
                const uuid = $(this).data('uuid');
                const name = $(this).data('name');
                const number = $(this).data('number');
                const balance = $(this).data('balance');
                const desc = $(this).data('desc');
                const icon = $(this).data('icon') || 'fa-wallet';
                const color = $(this).data('color') || '#6366f1';

                $('#account-modal-title').text('Edit Akun Keuangan');
                $('#account-method').val('PUT');
                
                let updateUrl = "{{ route('cashflow.accounts.update', ':uuid') }}";
                updateUrl = updateUrl.replace(':uuid', uuid);
                $('#form-account').attr('action', updateUrl);

                $('#account_name').val(name);
                $('#account_number').val(number);
                $('#account_balance').val(balance).prop('readonly', true); // Saldo awal terkunci saat edit
                $('#account_description').val(desc);
                $('#account_icon').val(icon);
                $('#account_color').val(color).trigger('change');
                
                $('#modal-account').removeClass('hidden');
            });

            // Delete Account Warning
            $('.btn-delete-account-trigger').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                cConfirm("Peringatan Keras", "Menghapus akun keuangan akan menghapus semua riwayat transaksi kas yang terkait secara permanen! Lanjutkan?", function() {
                    form.submit();
                });
            });

            // Close Account Modal
            $('.close-account-modal').on('click', function() {
                $('#modal-account').addClass('hidden');
            });

            // Transaction Modal Controls (Add)
            $('.btn-add-transaction').on('click', function() {
                resetTransactionForm();
                $('#modal-transaction h3').text('Catat Transaksi Buku Kas');
                $('#form-transaction').attr('action', "{{ route('cashflow.transactions.store') }}");
                $('#form-transaction').find('input[name="_method"]').remove();
                $('#modal-transaction').removeClass('hidden');
            });

            // Close Transaction Modal
            $('.close-transaction-modal').on('click', function() {
                $('#modal-transaction').addClass('hidden');
            });

            // Quick Reconcile Button from daily list
            $('.btn-quick-reconcile').on('click', function() {
                resetTransactionForm();
                $('#modal-transaction h3').text('Catat Transaksi Buku Kas');
                $('#form-transaction').attr('action', "{{ route('cashflow.transactions.store') }}");
                $('#form-transaction').find('input[name="_method"]').remove();
                const date = $(this).data('date');
                const variance = $(this).data('variance');

                // Fill reconciliation info
                $('#trx_type').val('income').trigger('change');
                $('#is_sales_reconciliation').prop('checked', true).trigger('change');
                $('#reconciliation_date').val(date).trigger('change');
                $('#trx_amount').val(variance); // Pre-fill with missing sales amount to make it a perfect match
                $('#trx_date').val(date);
                $('#trx_description').val('Rekonsiliasi Pendapatan Penjualan Harian POS Tanggal ' + date);

                // Pre-select "Penjualan Toko (POS)" category if it exists
                const posCategory = incomeCategories.find(cat => cat.name.includes('POS') || cat.name.includes('Penjualan'));
                if (posCategory) {
                    populateCategories('income', posCategory.uuid);
                }

                $('#modal-transaction').removeClass('hidden');
            });

            // Delete Transaction Warning
            $(document).on('click', '.btn-delete-transaction-trigger', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                cConfirm("Konfirmasi Hapus", "Apakah Anda yakin ingin menghapus catatan transaksi kas ini secara permanen?", function() {
                    form.submit();
                });
            });

            // Edit Transaction click handler (delegated)
            $(document).on('click', '.btn-edit-transaction', function() {
                resetTransactionForm();
                
                const btn = $(this).closest('.btn-edit-transaction');
                const uuid = btn.data('uuid');
                const type = btn.data('type');
                const account = btn.data('account');
                const destination = btn.data('destination');
                const category = btn.data('category');
                const amount = btn.data('amount');
                const date = btn.data('date');
                const reference = btn.data('reference');
                const description = btn.data('description');
                const isReconciliation = btn.data('is-reconciliation') == '1';
                const reconciliationDate = btn.data('reconciliation-date');
                const operationalExpense = btn.data('operational-expense');
                const cashDrawerAmount = btn.data('cash-drawer-amount');
                const purchasePlace = btn.data('purchase-place');
                const purchaseRequestId = btn.data('purchase-request-id');

                function decodeHtmlEntities(str) {
                    if (!str) return '';
                    const txt = document.createElement("textarea");
                    txt.innerHTML = str;
                    return txt.value;
                }

                // 1. Change title and action URL
                $('#modal-transaction h3').text('Edit Transaksi Buku Kas');
                let updateUrl = "{{ route('cashflow.transactions.update', ':uuid') }}";
                updateUrl = updateUrl.replace(':uuid', uuid);
                $('#form-transaction').attr('action', updateUrl);
                
                // Prepend PUT method override
                $('#form-transaction').find('input[name="_method"]').remove();
                $('#form-transaction').prepend('<input type="hidden" name="_method" value="PUT">');

                // 2. Set basic values
                $('#trx_type').val(type).trigger('change');
                
                if (type === 'transfer') {
                    $('#trx_account').val(account).trigger('change');
                    updateTransferDestinations();
                    $('#trx_destination_account').val(destination);
                } else {
                    $('#trx_account').val(account).trigger('change');
                }

                // 3. Set category
                populateCategories(type, category);

                // 4. Set amount, date, reference, description
                $('#trx_amount').val(amount);
                $('#trx_date').val(date);
                $('#trx_reference').val(reference);
                $('#trx_description').val(description);

                // 5. Handle reconciliation
                if (type === 'income') {
                    if (isReconciliation) {
                        $('#is_sales_reconciliation').prop('checked', true).trigger('change');
                        $('#reconciliation_date').val(reconciliationDate).trigger('change');
                        $('#operational_expense').val(operationalExpense);
                        $('#cash_drawer_amount').val(cashDrawerAmount);
                    } else {
                        $('#is_sales_reconciliation').prop('checked', false).trigger('change');
                    }
                }

                // 6. Handle shopping items (for expense)
                if (type === 'expense') {
                    $('#purchase_place').val(purchasePlace);
                    // Set without triggering 'change' — that handler wipes and re-fills the item
                    // rows, which would clobber the ones we're about to load from data-items.
                    $('#trx_purchase_request').val(purchaseRequestId || '');

                    let itemsList = [];
                    let parsedItems = btn.attr('data-items') || btn.data('items');
                    
                    if (typeof parsedItems === 'string') {
                        parsedItems = decodeHtmlEntities(parsedItems);
                    }
                    
                    // Defensively parse JSON string (handles potential double-encoding)
                    while (typeof parsedItems === 'string' && parsedItems.trim() !== '') {
                        try {
                            parsedItems = JSON.parse(parsedItems);
                        } catch (e) {
                            console.error("Gagal parse items JSON:", e);
                            break;
                        }
                    }

                    if (parsedItems) {
                        if (Array.isArray(parsedItems)) {
                            itemsList = parsedItems;
                        } else if (typeof parsedItems === 'object') {
                            itemsList = Object.values(parsedItems);
                        }
                    }

                    if (itemsList.length > 0) {
                        $('#section-shopping-items').removeClass('hidden');
                        $('#shopping-items-list').empty();
                        itemsList.forEach(function(item) {
                            const rowId = `shopping-item-row-${itemIndex}`;
                            const rowHtml = createShoppingItemRow(item.name, item.qty, item.price, item.supply_item_id || '');
                            $('#shopping-items-list').append(rowHtml);
                            // Stored qty/price are already in the base unit — keep the toggle
                            // off so the labels don't mislabel them as purchase-unit values.
                            const supplyItem = item.supply_item_id ? activeSupplyItems.find(si => si.uuid === item.supply_item_id) : null;
                            if (supplyItem) linkRowToSupplyItem($('#' + rowId), supplyItem, false);
                        });
                        calculateShoppingTotal();
                    }
                }

                // Show modal
                $('#modal-transaction').removeClass('hidden');
            });

            const accountsList = @json($accounts);

            function updateTransferDestinations() {
                const sourceUuid = $('#trx_account').val();
                const destSelect = $('#trx_destination_account');
                destSelect.empty();
                
                let count = 0;
                accountsList.forEach(function(acc) {
                    if (acc.uuid !== sourceUuid) {
                        destSelect.append(`<option value="${acc.uuid}">${acc.name} (Saldo: ${formatRupiah(acc.current_balance)})</option>`);
                        count++;
                    }
                });
                
                if (count === 0) {
                    destSelect.append('<option value="">-- Tidak ada akun tujuan lain --</option>');
                }
            }

            $('#trx_account').on('change', function() {
                if ($('#trx_type').val() === 'transfer') {
                    updateTransferDestinations();
                }
            });

            // Reset Transaction Form Helper
            function resetTransactionForm() {
                $('#form-transaction')[0].reset();
                $('#trx_date').val("{{ date('Y-m-d') }}");
                $('#is_sales_reconciliation').prop('checked', false).trigger('change');
                $('#section-reconciliation-toggle').removeClass('hidden');
                $('#cash_drawer_amount').val('0');

                // Reset Transfer fields
                $('#label-trx-account').text('Simpan Ke Akun');
                $('#container-trx-account').removeClass('hidden');
                $('#section-transfer-destination').addClass('hidden');
                $('#trx_destination_account').empty();

                // Reset Splits
                $('.split-amount-input').val('0');
                $('#section-reconciliation-splits').addClass('hidden');
                calculateSplitTotal();

                // Reset shopping items
                $('#shopping-items-list').empty();
                itemIndex = 0;
                $('#label-shopping-total').text(formatRupiah(0));
                $('#section-shopping-items').addClass('hidden');
                
                // Clear purchase place & pengajuan link
                $('#purchase_place').val('');
                $('#trx_purchase_request').val('');

                // Populate categories based on selected transaction type
                populateCategories($('#trx_type').val());
            }

            // Show/Hide Reconciliation & Transfer options based on Transaction Type
            $('#trx_type').on('change', function() {
                const type = $(this).val();
                populateCategories(type);

                if (type === 'income') {
                    $('#label-trx-account').text('Simpan Ke Akun');
                    $('#container-trx-account').removeClass('hidden');
                    $('#section-transfer-destination').addClass('hidden');
                    $('#section-reconciliation-toggle').removeClass('hidden');
                    $('#section-shopping-items').addClass('hidden');
                } else if (type === 'expense') {
                    $('#label-trx-account').text('Simpan Ke Akun');
                    $('#container-trx-account').removeClass('hidden');
                    $('#section-transfer-destination').addClass('hidden');
                    $('#is_sales_reconciliation').prop('checked', false).trigger('change');
                    $('#section-reconciliation-toggle').addClass('hidden');
                    $('#section-shopping-items').removeClass('hidden');
                } else if (type === 'transfer') {
                    $('#label-trx-account').text('Kirim Dari Akun');
                    $('#container-trx-account').removeClass('hidden');
                    $('#is_sales_reconciliation').prop('checked', false).trigger('change');
                    $('#section-reconciliation-toggle').addClass('hidden');
                    $('#section-shopping-items').addClass('hidden');
                    
                    // Populate transfer destinations
                    updateTransferDestinations();
                    $('#section-transfer-destination').removeClass('hidden');
                }
            });

            // Shopping Items Manager Logic
            let itemIndex = 0;

            function createShoppingItemRow(name = '', qty = 1, price = 0, supplyItemId = '') {
                const rowId = `shopping-item-row-${itemIndex}`;
                const isLinked = !!supplyItemId;
                const linkedSupply = isLinked ? activeSupplyItems.find(si => si.uuid === supplyItemId) : null;
                const searchLabel = linkedSupply ? `${linkedSupply.name} (${linkedSupply.unit})` : '';

                const html = `
                    <div id="${rowId}" class="shopping-item-row grid grid-cols-12 gap-2 items-center px-3 py-3 sm:py-2 bg-white transition-all hover:bg-rose-50/20 relative">
                        <div class="col-span-12 sm:col-span-6 relative flex flex-col gap-1">
                            <div class="item-supply-search-wrap relative ${isLinked ? 'hidden' : ''}">
                                <input type="text" class="item-supply-search w-full px-2 py-1 border border-gray-200 rounded-lg text-[11px] focus:outline-none focus:border-rose-400 bg-white text-gray-500 font-semibold cursor-pointer" placeholder="🔍 Cari dari Master Barang..." autocomplete="off" value="${escapeHtml(searchLabel)}">
                                <div class="item-supply-dropdown hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl z-50 max-h-[200px] overflow-y-auto divide-y divide-gray-50 text-xs font-semibold text-gray-700"></div>
                            </div>
                            <div class="relative">
                                <input type="text" name="items[${itemIndex}][name]" autocomplete="off" placeholder="Nama Barang (misal: Beras, Gas)" class="item-name w-full px-2.5 py-1.5 border rounded-lg text-xs focus:outline-none focus:border-rose-400 transition-all font-semibold text-gray-700 ${isLinked ? 'bg-emerald-50/50 border-emerald-200' : 'bg-gray-50/30 focus:bg-white border-gray-200'}" required value="${name}" ${isLinked ? 'readonly' : ''}>
                                <div class="autocomplete-dropdown hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl z-50 max-h-[160px] overflow-y-auto divide-y divide-gray-50 text-xs font-semibold text-gray-700"></div>
                                <button type="button" class="btn-unlink-supply-item ${isLinked ? '' : 'hidden'} absolute right-2 top-1/2 -translate-y-1/2 text-emerald-500 hover:text-emerald-700 cursor-pointer bg-transparent border-none text-xs" title="Ganti barang">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                            <input type="hidden" name="items[${itemIndex}][supply_item_id]" class="item-supply-id" value="${supplyItemId}">
                        </div>
                        <div class="col-span-4 sm:col-span-2">
                            <div class="flex items-center gap-1 sm:block">
                                <span class="text-[10px] text-gray-400 font-bold sm:hidden">Qty:</span>
                                <input type="number" name="items[${itemIndex}][qty]" placeholder="1" class="item-qty w-full px-1 py-1.5 border border-gray-200 rounded-lg text-xs text-center focus:outline-none focus:border-rose-400 bg-gray-50/30 focus:bg-white transition-all font-mono font-bold text-gray-700" min="0.01" step="any" required value="${qty}">
                                <p class="item-qty-unit-label text-[9px] text-gray-400 font-semibold mt-0.5 text-center"></p>
                            </div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <div class="flex items-center gap-1 sm:block">
                                <span class="text-[10px] text-gray-400 font-bold sm:hidden">Harga:</span>
                                <input type="number" name="items[${itemIndex}][price]" placeholder="0" class="item-price w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs text-right focus:outline-none focus:border-rose-400 bg-gray-50/30 focus:bg-white transition-all font-mono font-bold text-gray-700" min="0" required value="${price}">
                                <p class="item-price-unit-label text-[9px] text-gray-400 font-semibold mt-0.5 text-right"></p>
                            </div>
                        </div>
                        <div class="col-span-2 sm:col-span-1 flex items-center justify-center">
                            <button type="button" class="btn-remove-shopping-item text-rose-500 hover:text-rose-700 w-8 h-8 rounded-full flex items-center justify-center transition-all cursor-pointer border-none bg-transparent" data-row-id="${rowId}">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                        <div class="item-conversion-toggle col-span-12 hidden" data-conversion="">
                            <label class="inline-flex items-center gap-1.5 text-[10px] text-indigo-600 font-bold cursor-pointer select-none" title="Qty & harga otomatis dikonversi ke satuan pakai saat disimpan">
                                <input type="checkbox" class="item-purchase-mode-toggle w-3.5 h-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400" checked>
                                <i class="fas fa-repeat"></i>
                                <span>Per <span class="purchase-unit-name"></span> <span class="text-indigo-300">(bukan per <span class="usage-unit-name"></span>)</span></span>
                            </label>
                        </div>
                    </div>
                `;
                itemIndex++;
                return html;
            }

            // Reflect the row's current unit mode (purchase unit vs. base/usage unit) in the
            // small captions under the qty and price fields.
            function updateUnitLabels(row) {
                const toggle = row.find('.item-conversion-toggle');
                const supplyItem = row.data('linkedSupplyItem');
                let qtyUnit = '';
                let priceUnit = '';

                if (supplyItem && !toggle.hasClass('hidden') && row.find('.item-purchase-mode-toggle').is(':checked')) {
                    qtyUnit = supplyItem.purchase_unit;
                    priceUnit = '/' + supplyItem.purchase_unit;
                } else if (supplyItem) {
                    qtyUnit = supplyItem.unit;
                    priceUnit = '/' + supplyItem.unit;
                }

                row.find('.item-qty-unit-label').text(qtyUnit);
                row.find('.item-price-unit-label').text(priceUnit);
            }

            // Picking a Master Barang item locks the name field to it (so stock/price update
            // on save); switching back to "Custom" frees the name field for manual typing again.
            // defaultPurchaseMode=true is for a fresh pick from the search box (the friendly
            // default). Rows rebuilt from already-stored qty/price (editing a transaction) pass
            // false, since those numbers are already in the base unit and must stay labeled that
            // way — flipping the toggle would relabel the units without converting the numbers.
            function linkRowToSupplyItem(row, supplyItem, defaultPurchaseMode = true) {
                const nameInput = row.find('.item-name');
                const hiddenId = row.find('.item-supply-id');
                const searchInput = row.find('.item-supply-search');
                const searchWrap = row.find('.item-supply-search-wrap');
                const unlinkBtn = row.find('.btn-unlink-supply-item');
                const toggleWrap = row.find('.item-conversion-toggle');
                const toggleCheckbox = row.find('.item-purchase-mode-toggle');

                row.data('linkedSupplyItem', supplyItem || null);

                if (supplyItem) {
                    nameInput.val(supplyItem.name).prop('readonly', true)
                        .removeClass('bg-gray-50/30 border-gray-200').addClass('bg-emerald-50/50 border-emerald-200');
                    hiddenId.val(supplyItem.uuid);
                    searchInput.val(`${supplyItem.name} (${supplyItem.unit})`);
                    // The search box did its job — hide it so the row shows just the one
                    // resolved name field, with a small ✕ to reopen it if they want to change.
                    searchWrap.addClass('hidden');
                    unlinkBtn.removeClass('hidden');

                    const hasConversion = supplyItem.purchase_unit && parseFloat(supplyItem.purchase_conversion) > 0;
                    if (hasConversion) {
                        toggleWrap.removeClass('hidden').attr('data-conversion', supplyItem.purchase_conversion);
                        toggleWrap.find('.purchase-unit-name').text(supplyItem.purchase_unit);
                        toggleWrap.find('.usage-unit-name').text(supplyItem.unit);
                        toggleCheckbox.prop('checked', defaultPurchaseMode);
                    } else {
                        toggleWrap.addClass('hidden');
                    }

                    // Prefill with the last known price as a starting point — admin still
                    // corrects it to whatever was actually paid this time. Shown in whichever
                    // unit is currently active (purchase unit by default, when available).
                    const priceInput = row.find('.item-price');
                    if (!parseFloat(priceInput.val())) {
                        const usePurchaseMode = hasConversion && defaultPurchaseMode;
                        const startingPrice = usePurchaseMode ? supplyItem.unit_price * parseFloat(supplyItem.purchase_conversion) : supplyItem.unit_price;
                        priceInput.val(startingPrice);
                        calculateShoppingTotal();
                    }
                } else {
                    nameInput.val('').prop('readonly', false)
                        .removeClass('bg-emerald-50/50 border-emerald-200').addClass('bg-gray-50/30 border-gray-200');
                    hiddenId.val('');
                    searchInput.val('');
                    searchWrap.removeClass('hidden');
                    unlinkBtn.addClass('hidden');
                    toggleWrap.addClass('hidden');
                }

                updateUnitLabels(row);
            }

            // Toggling the unit mode only relabels the fields — the actual conversion of
            // whatever numbers are currently typed happens once, at submit time.
            $(document).on('change', '.item-purchase-mode-toggle', function() {
                updateUnitLabels($(this).closest('.shopping-item-row'));
            });

            function renderSupplyDropdown(dropdown, query) {
                const q = (query || '').toLowerCase().trim();
                const matches = activeSupplyItems.filter(si => si.name.toLowerCase().includes(q));

                let html = `<div class="item-supply-option px-3 py-2 hover:bg-rose-50 hover:text-rose-700 cursor-pointer transition-colors text-gray-500 italic" data-uuid="">+ Barang Custom (ketik manual)</div>`;
                if (matches.length > 0) {
                    matches.forEach(function(si) {
                        html += `<div class="item-supply-option px-3 py-2 hover:bg-rose-50 hover:text-rose-700 cursor-pointer transition-colors" data-uuid="${si.uuid}">${escapeHtml(si.name)} <span class="text-gray-400 font-normal">(${escapeHtml(si.unit)})</span></div>`;
                    });
                } else if (q !== '') {
                    html += `<div class="px-3 py-2 text-gray-400 italic">Tidak ada barang cocok "${escapeHtml(query)}"</div>`;
                }
                dropdown.html(html).removeClass('hidden');
            }

            // Open the dropdown (showing everything) on focus/click
            $(document).on('focus click', '.item-supply-search', function() {
                const dropdown = $(this).siblings('.item-supply-dropdown');
                renderSupplyDropdown(dropdown, '');
            });

            // Filter the dropdown as the admin types
            $(document).on('input keyup', '.item-supply-search', function() {
                const dropdown = $(this).siblings('.item-supply-dropdown');
                renderSupplyDropdown(dropdown, $(this).val());
            });

            // Pick an item (or "Custom") from the dropdown
            $(document).on('mousedown', '.item-supply-option', function(e) {
                e.preventDefault(); // Keep the search input's blur from firing before this click lands
                const row = $(this).closest('.shopping-item-row');
                const uuid = $(this).data('uuid');
                const supplyItem = uuid ? activeSupplyItems.find(si => si.uuid === uuid) : null;

                linkRowToSupplyItem(row, supplyItem);
                $(this).closest('.item-supply-dropdown').addClass('hidden');
            });

            // Hide the dropdown on blur
            $(document).on('blur', '.item-supply-search', function() {
                const dropdown = $(this).siblings('.item-supply-dropdown');
                setTimeout(() => dropdown.addClass('hidden'), 200);
            });

            // Picking a pending shopping-list request pre-fills the item rows (name + qty +
            // Master Barang link) from that request, so the admin only needs to fill in price.
            $('#trx_purchase_request').on('change', function() {
                const prUuid = $(this).val();
                if (!prUuid || !pendingPurchaseRequestsData[prUuid]) return;

                $('#shopping-items-list').empty();
                itemIndex = 0;
                pendingPurchaseRequestsData[prUuid].forEach(function(item) {
                    const supplyItem = activeSupplyItems.find(si => si.uuid === item.supply_item_id);
                    const startingPrice = supplyItem ? supplyItem.unit_price : 0;
                    const rowId = `shopping-item-row-${itemIndex}`;
                    $('#shopping-items-list').append(createShoppingItemRow(item.name, item.qty, startingPrice, item.supply_item_id || ''));
                    // qty here came from the pending request, already in the base unit — keep
                    // the toggle off so the labels line up with what's actually in the field.
                    if (supplyItem) linkRowToSupplyItem($('#' + rowId), supplyItem, false);
                });
                $('#section-shopping-items').removeClass('hidden');
                calculateShoppingTotal();
            });

            // "✕" next to a linked item's name reopens the Master Barang search so it can be
            // swapped for a different item (or cleared back to a free-text custom entry).
            $(document).on('click', '.btn-unlink-supply-item', function() {
                const row = $(this).closest('.shopping-item-row');
                linkRowToSupplyItem(row, null);
                row.find('.item-supply-search').focus();
            });

            // Custom Autocomplete Dropdown Logic
            $(document).on('keyup focus', '.item-name', function() {
                const input = $(this);
                const query = input.val().toLowerCase().trim();
                const dropdown = input.siblings('.autocomplete-dropdown');
                
                dropdown.empty();
                
                // Filter matching items from historical items
                const filteredMatches = historicalItems.filter(item => item.toLowerCase().includes(query));
                
                if (filteredMatches.length > 0) {
                    filteredMatches.forEach(item => {
                        dropdown.append(`
                            <div class="autocomplete-item px-3 py-2 hover:bg-rose-50 hover:text-rose-700 cursor-pointer transition-colors">
                                ${item}
                            </div>
                        `);
                    });
                    dropdown.removeClass('hidden');
                } else {
                    dropdown.addClass('hidden');
                }
            });

            // Select item from autocomplete dropdown
            $(document).on('mousedown', '.autocomplete-item', function(e) {
                e.preventDefault(); // Prevent input blur from firing before selection is complete
                const itemValue = $(this).text().trim();
                const dropdown = $(this).closest('.autocomplete-dropdown');
                const input = dropdown.siblings('.item-name');
                
                input.val(itemValue);
                dropdown.addClass('hidden');
            });

            // Hide dropdown on blur
            $(document).on('blur', '.item-name', function() {
                const dropdown = $(this).siblings('.autocomplete-dropdown');
                setTimeout(() => {
                    dropdown.addClass('hidden');
                }, 200);
            });

            function calculateShoppingTotal() {
                let grandTotal = 0;
                $('.shopping-item-row').each(function() {
                    const qty = parseFloat($(this).find('.item-qty').val()) || 0;
                    const price = parseInt($(this).find('.item-price').val()) || 0;
                    grandTotal += qty * price;
                });
                $('#label-shopping-total').text(formatRupiah(grandTotal));
                
                // If there are shopping items, sync the grand total to trx_amount
                if ($('.shopping-item-row').length > 0) {
                    $('#trx_amount').val(grandTotal);
                }
            }

            // Add item row handler
            $('#btn-add-shopping-item, #btn-add-shopping-item-bottom').on('click', function() {
                const rowHtml = createShoppingItemRow();
                $('#shopping-items-list').append(rowHtml);
                calculateShoppingTotal();
            });

            // Remove item row handler
            $(document).on('click', '.btn-remove-shopping-item', function() {
                const rowId = $(this).data('row-id');
                $(`#${rowId}`).remove();
                calculateShoppingTotal();
            });

            // Input listener on shopping item fields for live recalculations
            $(document).on('change keyup', '.item-qty, .item-price', function() {
                calculateShoppingTotal();
            });

            // Convert purchase-unit rows to base-unit numbers right before the form actually
            // submits — this is the one point where "3 batang @ Rp8.000" becomes "48 keping @
            // Rp500" for storage, so stock and HPP costing stay in the base unit everywhere else.
            $('#form-transaction').on('submit', function() {
                $('.shopping-item-row').each(function() {
                    const row = $(this);
                    const toggleWrap = row.find('.item-conversion-toggle');
                    if (toggleWrap.hasClass('hidden') || !row.find('.item-purchase-mode-toggle').is(':checked')) return;

                    const conversion = parseFloat(toggleWrap.attr('data-conversion')) || 1;
                    const qtyInput = row.find('.item-qty');
                    const priceInput = row.find('.item-price');
                    const qty = parseFloat(qtyInput.val()) || 0;
                    const price = parseFloat(priceInput.val()) || 0;

                    qtyInput.val((qty * conversion).toFixed(4));
                    priceInput.val(Math.round(price / conversion));
                });
            });

            // View shopping items details button click handler
            $(document).on('click', '.btn-view-items', function(e) {
                e.stopPropagation(); // prevent card selection trigger
                const items = $(this).data('items');
                const date = $(this).data('date');
                const place = $(this).data('place');
                const reference = $(this).data('reference');
                const totalAmount = $(this).data('amount');

                $('#detail-date').text(date);
                $('#detail-ref').text(reference);
                $('#detail-place').text(place);
                $('#detail-total-amount').text('Rp ' + totalAmount);

                const body = $('#shopping-details-body');
                body.empty();

                items.forEach(function(item) {
                    const row = `
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-2 font-bold text-gray-800">${item.name}</td>
                            <td class="px-4 py-2 text-center text-gray-500 font-mono">${item.qty}</td>
                            <td class="px-4 py-2 text-right font-mono text-gray-500">Rp ${formatNumber(item.price)}</td>
                            <td class="px-4 py-2 text-right font-mono font-bold text-gray-800">Rp ${formatNumber(item.total)}</td>
                        </tr>
                    `;
                    body.append(row);
                });

                $('#modal-shopping-details').removeClass('hidden');
            });

            // Close Shopping Modal
            $('.close-shopping-modal').on('click', function() {
                $('#modal-shopping-details').addClass('hidden');
            });

            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            // Price Comparison Modal & Expand Logic
            $(document).on('click', '.btn-expand-comparison', function() {
                const comp = $(this).data('comp-data');
                
                $('#comp-detail-item-name').text(comp.display_name);

                // Populate Cheapest (Shop comparison) Body
                const cheapestBody = $('#comp-cheapest-body');
                cheapestBody.empty();
                comp.history.forEach(function(item) {
                    const row = `
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-3 py-2.5 font-bold text-gray-800 capitalize"><i class="fas fa-store text-emerald-500/80 me-1"></i>${item.purchase_place}</td>
                            <td class="px-3 py-2.5 text-right font-bold font-mono text-emerald-600">Rp ${formatNumber(item.price)}</td>
                            <td class="px-3 py-2.5 text-center text-gray-500 font-mono">${item.qty}</td>
                            <td class="px-3 py-2.5 text-right font-semibold font-mono text-gray-700">Rp ${formatNumber(item.total)}</td>
                        </tr>
                    `;
                    cheapestBody.append(row);
                });

                // Populate Chronological Trends Body
                const trendsBody = $('#comp-trends-body');
                trendsBody.empty();
                comp.chrono_history.forEach(function(item) {
                    const row = `
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-3 py-2.5 font-bold text-gray-800">${item.formatted_date}</td>
                            <td class="px-3 py-2.5 text-gray-600 capitalize">${item.purchase_place}</td>
                            <td class="px-3 py-2.5 text-right font-bold font-mono text-indigo-600">Rp ${formatNumber(item.price)}</td>
                            <td class="px-3 py-2.5 text-center text-gray-500 font-mono">${item.qty}</td>
                        </tr>
                    `;
                    trendsBody.append(row);
                });

                // Reset inner modal sub-tabs to first tab
                $('.sub-tab-btn[data-target="#sub-tab-cheapest"]').trigger('click');

                $('#modal-price-comparison-details').removeClass('hidden');
            });

            // Inner Modal Sub-tab switcher
            $('.sub-tab-btn').on('click', function() {
                const target = $(this).data('target');
                
                $('.sub-tab-btn').removeClass('bg-white text-indigo-600 shadow-sm font-bold').addClass('text-gray-500 font-semibold hover:bg-gray-100/50');
                $(this).removeClass('text-gray-500 font-semibold hover:bg-gray-100/50').addClass('bg-white text-indigo-600 shadow-sm font-bold');
                
                $('.sub-tab-content').addClass('hidden');
                $(target).removeClass('hidden');
            });

            // Close Comparison Modal
            $('.close-comp-modal').on('click', function() {
                $('#modal-price-comparison-details').addClass('hidden');
            });

            // Search filter for price comparison grid
            $('#search-price-items').on('keyup', function() {
                const value = $(this).val().toLowerCase().trim();
                
                $('.comparison-card').each(function() {
                    const name = $(this).data('name');
                    if (name.indexOf(value) > -1) {
                        $(this).removeClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            });

            // Show/Hide date picker for Reconciliation
            $('#is_sales_reconciliation').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#section-reconciliation-date').removeClass('hidden');
                    $('#section-reconciliation-splits').removeClass('hidden');
                    $('#container-trx-account').addClass('hidden');
                    // Automatically fill date with transaction date if empty
                    if (!$('#reconciliation_date').val()) {
                        $('#reconciliation_date').val($('#trx_date').val());
                    }
                    $('#reconciliation_date').trigger('change');
                    calculateSplitTotal();
                } else {
                    $('#section-reconciliation-date').addClass('hidden');
                    $('#section-reconciliation-splits').addClass('hidden');
                    $('#container-trx-account').removeClass('hidden');
                    $('#reconciliation-live-status').addClass('hidden');
                }
            });

            // Live Sum for Account Splits
            $(document).on('change keyup', '.split-amount-input', function() {
                calculateSplitTotal();
            });

            function calculateSplitTotal() {
                let totalSplits = 0;
                $('.split-amount-input').each(function() {
                    totalSplits += parseInt($(this).val()) || 0;
                });
                $('#label-split-total').text(formatRupiah(totalSplits));
                
                // Compare with transaction amount
                const trxAmount = parseInt($('#trx_amount').val()) || 0;
                const container = $('#label-split-total').parent();
                if (totalSplits === trxAmount && trxAmount > 0) {
                    container.removeClass('text-indigo-900 bg-indigo-50/50').addClass('text-emerald-700 bg-emerald-50 border-emerald-100');
                } else {
                    container.removeClass('text-emerald-700 bg-emerald-50 border-emerald-100').addClass('text-indigo-900 bg-indigo-50/50');
                }
            }

            // Sync main amount to splits dynamically if splits sum to 0
            $('#trx_amount').on('change keyup', function() {
                const trxAmount = parseInt($(this).val()) || 0;
                
                let totalSplits = 0;
                $('.split-amount-input').each(function() {
                    totalSplits += parseInt($(this).val()) || 0;
                });
                
                if (totalSplits === 0 && $('.split-amount-input').length > 0) {
                    $('.split-amount-input').first().val(trxAmount);
                }
                
                calculateSplitTotal();
            });

            // Synergize Reconciliation Date with Transaction Date automatically
            $('#trx_date').on('change', function() {
                if ($('#is_sales_reconciliation').is(':checked') && !$('#reconciliation_date').val()) {
                    $('#reconciliation_date').val($(this).val()).trigger('change');
                }
            });

            // Live Sales Data AJAX Checker
            let currentSales = 0;
            let currentReconciled = 0;
            let currentRemaining = 0;

            $('#reconciliation_date, #trx_amount, #operational_expense, #cash_drawer_amount').on('change keyup', function() {
                const date = $('#reconciliation_date').val();
                if (!date || !$('#is_sales_reconciliation').is(':checked')) return;

                $.ajax({
                    url: "{{ route('cashflow.reconciliation.sales-data') }}",
                    data: { date: date },
                    success: function(response) {
                        if (response.success) {
                            currentSales = response.total_sales;
                            currentReconciled = response.total_reconciled;
                            currentRemaining = response.remaining_to_reconcile;

                            // Formats Rupiah
                            $('#label-actual-sales').text(formatRupiah(currentSales));
                            $('#label-reconciled-sales').text(formatRupiah(currentReconciled));
                            $('#label-remaining-sales').text(formatRupiah(currentRemaining));

                            // Calculate live match status based on current amount input + operational expense + cash drawer amount
                            const inputAmount = parseInt($('#trx_amount').val()) || 0;
                            const operationalExpense = parseInt($('#operational_expense').val()) || 0;
                            const cashDrawerAmount = parseInt($('#cash_drawer_amount').val()) || 0;
                            const totalRegisteredAmount = currentReconciled + inputAmount + operationalExpense + cashDrawerAmount;
                            
                            const badge = $('#badge-match-status');
                            badge.removeClass('bg-emerald-50 text-emerald-700 border-emerald-100 bg-rose-50 text-rose-700 border-rose-100 bg-amber-50 text-amber-700 border-amber-100');

                            if (currentSales === 0) {
                                badge.addClass('bg-gray-50 text-gray-500 border border-gray-100').html("<i class='fas fa-circle-info'></i> Tidak Ada Penjualan");
                            } else if (totalRegisteredAmount === currentSales) {
                                badge.addClass('bg-emerald-50 text-emerald-700 border border-emerald-100').html("<i class='fas fa-check-circle'></i> Cocok (Match 100%)");
                            } else {
                                const diff = currentSales - totalRegisteredAmount;
                                if (diff > 0) {
                                    badge.addClass('bg-amber-50 text-amber-700 border border-amber-100').html("<i class='fas fa-triangle-exclamation'></i> Kurang " + formatRupiah(diff));
                                } else {
                                    badge.addClass('bg-rose-50 text-rose-700 border border-rose-100').html("<i class='fas fa-circle-xmark'></i> Lebih " + formatRupiah(Math.abs(diff)));
                                }
                            }

                            $('#reconciliation-live-status').removeClass('hidden');
                        }
                    },
                    error: function(err) {
                        console.warn("Gagal menarik data penjualan harian: ", err);
                    }
                });
            });

            // Quick nominal copy handler taking operational expense and cash drawer amount into account
            $('#btn-copy-sales').on('click', function() {
                const operationalExpense = parseInt($('#operational_expense').val()) || 0;
                const cashDrawerAmount = parseInt($('#cash_drawer_amount').val()) || 0;
                const totalDeductions = operationalExpense + cashDrawerAmount;
                if (currentRemaining > 0) {
                    const fillAmount = Math.max(0, currentRemaining - totalDeductions);
                    $('#trx_amount').val(fillAmount).trigger('change');
                } else if (currentSales > 0) {
                    const fillAmount = Math.max(0, currentSales - totalDeductions);
                    $('#trx_amount').val(fillAmount).trigger('change');
                }
            });

            // Account Card click handler for filtering
            let selectedAccountName = null;

            $(document).on('click', '.account-card', function() {
                const name = $(this).data('name');
                const card = $(this);
                const color = card.data('color') || '#6366f1';

                if (selectedAccountName === name) {
                    // Clicked the active card again -> Clear filter
                    clearAccountFilter();
                } else {
                    // Select new card -> Apply filter
                    selectedAccountName = name;
                    
                    // Reset all other cards inline styles
                    $('.account-card').css({
                        'border-color': 'transparent',
                        'background-color': '#ffffff',
                        'box-shadow': '',
                        'outline': 'none'
                    });

                    // Set matching active custom highlight border, background, outline ring, and drop shadow!
                    card.css({
                        'border-color': color,
                        'background-color': color + '08', // ~3% opacity hex tint
                        'box-shadow': `0 10px 25px -5px ${color}25, 0 8px 10px -6px ${color}25`, // beautiful color matching glow
                        'outline': `2px solid ${color}15` // color matching outline ring
                    });
                    
                    // Apply DataTable filter on Column 1 (Akun Keuangan)
                    const table = $('#table-ledger-data').DataTable();
                    table.column(1).search(selectedAccountName).draw();

                    // Show filter badge
                    $('#active-filter-name').text(selectedAccountName);
                    // Match the badge colors with the active custom filter color dynamically
                    $('#active-filter-badge').css({
                        'background-color': color + '15',
                        'color': color,
                        'border-color': color + '30'
                    }).removeClass('hidden').addClass('inline-flex');
                    
                    // Smoothly switch tab to Ledger if they are currently in Reconciliation tab
                    $('.tab-btn[data-target="#tab-ledger"]').trigger('click');
                }
            });

            // Clear Filter Handler
            $('#btn-clear-account-filter').on('click', function(e) {
                e.stopPropagation(); // prevent card click triggers
                clearAccountFilter();
            });

            function clearAccountFilter() {
                selectedAccountName = null;
                $('.account-card').css({
                    'border-color': 'transparent',
                    'background-color': '#ffffff',
                    'box-shadow': '',
                    'outline': ''
                });
                
                const table = $('#table-ledger-data').DataTable();
                table.column(1).search('', false, false).draw();
                
                $('#active-filter-badge').addClass('hidden').removeClass('inline-flex');
            }

            // Prevent card selection when clicking edit/delete buttons
            $(document).on('click', '.btn-edit-account, .btn-delete-account-trigger', function(e) {
                e.stopPropagation();
            });

            // Currency formatting helper
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            }
        });
    </script>
    
        <!-- Modal Income Breakdown -->
        <div id="modal-income-breakdown" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px]">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 tracking-wide">Pemasukan per Kategori</h3>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer btn-close-income-breakdown border-none outline-none bg-transparent">
                        <i class="fas fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    @forelse($monthlyIncomeByCategory as $cat => $total)
                    <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-sm font-semibold text-gray-700">{{ $cat }}</span>
                        <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-400 text-sm">Tidak ada data pemasukan bulan ini</div>
                    @endforelse
                </div>
                <div class="mt-5 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="font-bold text-gray-500 text-xs uppercase">Total Pemasukan</span>
                    <span class="font-black text-lg text-emerald-600">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Modal Expense Breakdown -->
        <div id="modal-expense-breakdown" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px]">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                            <i class="fas fa-arrow-trend-down"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 tracking-wide">Pengeluaran per Kategori</h3>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer btn-close-expense-breakdown border-none outline-none bg-transparent">
                        <i class="fas fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    @forelse($monthlyExpenseByCategory as $cat => $total)
                    <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-sm font-semibold text-gray-700">{{ $cat }}</span>
                        <span class="text-sm font-bold text-rose-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-400 text-sm">Tidak ada data pengeluaran bulan ini</div>
                    @endforelse
                </div>
                <div class="mt-5 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="font-bold text-gray-500 text-xs uppercase">Total Pengeluaran</span>
                    <span class="font-black text-lg text-rose-600">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

<!-- Floating Action Button (FAB) for adding cash transaction -->
    <button type="button" class="btn-add-transaction group" style="position: fixed; bottom: 32px; right: 32px; z-index: 999; width: 56px; height: 56px; background-color: #4f46e5; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4); cursor: pointer; transition: all 0.2s ease-in-out;" title="Catat Transaksi Kas" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        <i class="fas fa-plus" style="font-size: 20px;"></i>
    </button>
@endsection
