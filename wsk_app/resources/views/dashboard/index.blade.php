@extends('layout.index')
@section('title', 'Admin Dashboard')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl md:text-4xl font-extrabold uppercase tracking-tight text-gray-800">DASHBOARD</h1>
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-soft text-brand text-xs font-extrabold rounded-full uppercase tracking-wider">
                <i class="fas fa-shield-halved"></i> Admin
            </span>
        </div>
        <div class="date-place hidden md:inline-flex px-3 py-2 pe-5 bg-white rounded-full shadow-sm items-center gap-3 border border-gray-100">
            <div class="menu-icon rounded-full h-10 w-10 flex items-center justify-center bg-gray-50">
                <i class="fas fa-calendar-days text-base text-brand"></i>
            </div>
            <span class="text-gray-700 font-bold text-sm">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
<style>
    /* ====== DASHBOARD PREMIUM STYLES ====== */
    .dashboard-metric-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        background: white;
        border: 1px solid rgba(0,0,0,0.04);
        padding: 1.5rem 1.75rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.01);
        display: block;
        color: inherit;
        text-decoration: none;
        cursor: pointer;
    }
    .dashboard-metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        text-decoration: none;
        color: inherit;
    }
    .dashboard-metric-card .card-glow {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        border-radius: 1.5rem 1.5rem 0 0;
    }
    .dashboard-metric-card .icon-circle {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    @media (max-width: 640px) {
        .dashboard-metric-card .icon-circle {
            width: 46px; height: 46px;
            border-radius: 12px;
            font-size: 1.1rem;
        }
        .dashboard-metric-card {
            padding: 1.25rem 1.25rem;
        }
    }

    /* Change indicator badge */
    .change-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.02em;
    }
    .change-badge.positive { background: #ecfdf5; color: #059669; }
    .change-badge.negative { background: #fef2f2; color: #dc2626; }
    .change-badge.neutral { background: #f3f4f6; color: #6b7280; }

    /* Chart container styling */
    .chart-card {
        background: white;
        border-radius: 1.5rem;
        border: 1px solid rgba(0,0,0,0.04);
        padding: 1.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
    }
    .chart-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }

    /* Table styles */
    .dashboard-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .dashboard-table thead th {
        font-size: 11px;
        font-weight: 800;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f3f4f6;
        text-align: left;
    }
    .dashboard-table tbody td {
        padding: 0.85rem 1rem;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #f9fafb;
        vertical-align: middle;
    }
    .dashboard-table tbody tr:last-child td { border-bottom: none; }
    .dashboard-table tbody tr {
        transition: background 0.15s ease;
    }
    .dashboard-table tbody tr:hover {
        background: #f8fafc;
    }

    /* Rank badge */
    .rank-badge {
        width: 28px; height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
    }
    .rank-badge.gold { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }
    .rank-badge.silver { background: linear-gradient(135deg, #f3f4f6, #e5e7eb); color: #4b5563; }
    .rank-badge.bronze { background: linear-gradient(135deg, #fed7aa, #fdba74); color: #c2410c; }
    .rank-badge.default { background: #f3f4f6; color: #9ca3af; }

    /* Monthly summary card */
    .summary-pill {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-radius: 1.5rem;
        background: white;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        color: inherit;
        text-decoration: none;
        cursor: pointer;
    }
    .summary-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        text-decoration: none;
        color: inherit;
    }

    /* Pulse live indicator */
    @keyframes pulse-live {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .live-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: pulse-live 1.5s ease-in-out infinite;
        display: inline-block;
    }
</style>

@php
    $today = \Carbon\Carbon::today()->format('Y-m-d');
@endphp

<div class="container-place w-full p-4 sm:p-6 flex flex-col gap-6">

    {{-- ===== ROW 0: PREMIUM DATE RANGE FILTER ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <h2 class="text-base sm:text-lg font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fas fa-filter text-brand text-sm"></i> Filter Periode Laporan
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Menampilkan data dari <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> s/d <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
            </p>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" id="filter-form" class="flex flex-wrap items-center gap-3">
            <!-- Presets -->
            <div class="flex flex-wrap items-center gap-1 bg-gray-50 p-1 rounded-xl border border-gray-100">
                <button type="button" class="preset-btn px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all border-none outline-none cursor-pointer"
                    data-start="{{ $today }}" data-end="{{ $today }}"
                    data-label="Hari Ini">
                    Hari Ini
                </button>
                <button type="button" class="preset-btn px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all border-none outline-none cursor-pointer"
                    data-start="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}" data-end="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                    data-label="Kemarin">
                    Kemarin
                </button>
                <button type="button" class="preset-btn px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all border-none outline-none cursor-pointer"
                    data-start="{{ \Carbon\Carbon::today()->subDays(6)->format('Y-m-d') }}" data-end="{{ $today }}"
                    data-label="7 Hari Terakhir">
                    7 Hari
                </button>
                <button type="button" class="preset-btn px-3.5 py-1.5 rounded-lg text-xs font-extrabold transition-all border-none outline-none cursor-pointer"
                    data-start="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" data-end="{{ $today }}"
                    data-label="Bulan Ini">
                    Bulan Ini
                </button>
            </div>

            <!-- Custom Inputs -->
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs sm:text-sm font-bold text-gray-700 focus:outline-none focus:border-brand transition-all">
                <span class="text-xs font-extrabold text-gray-400">s/d</span>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs sm:text-sm font-bold text-gray-700 focus:outline-none focus:border-brand transition-all">
            </div>

            <button type="submit" class="bg-brand hover:bg-brand-strong text-white font-extrabold text-xs px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-brand/10 cursor-pointer border-none flex items-center gap-1.5">
                <i class="fas fa-check"></i> Terapkan
            </button>
        </form>
    </div>

    {{-- ===== ROW 1: HERO METRIC CARDS (ENLARGED TYPOGRAPHY) ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Card 1: Omzet Hari Ini --}}
        <a href="{{ route('activity.index') }}" class="dashboard-metric-card hover:no-underline">
            <div class="card-glow bg-gradient-to-r from-indigo-500 to-blue-500"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-circle bg-indigo-50 text-indigo-500">
                    <i class="fas fa-coins"></i>
                </div>
                @php $revClass = $revenueChange >= 0 ? 'positive' : 'negative'; @endphp
                <span class="change-badge {{ $revClass }}">
                    <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }} text-[9px]"></i>
                    {{ abs($revenueChange) }}%
                </span>
            </div>
            <p class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Omzet Hari Ini</p>
            <h3 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black text-gray-900 tracking-tight leading-tight my-2">
                Rp {{ number_format($todayRevenue, 0, ',', '.') }}
            </h3>
            <p class="text-xs sm:text-sm text-gray-400 mt-3 font-semibold">
                Periode lalu: <span class="text-gray-700">Rp {{ number_format($yesterdayRevenue ?? 0, 0, ',', '.') }}</span>
            </p>
        </a>

        {{-- Card 2: Laba Kotor --}}
        <a href="{{ route('activity.index') }}" class="dashboard-metric-card hover:no-underline">
            <div class="card-glow bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-circle bg-emerald-50 text-emerald-500">
                    <i class="fas fa-chart-line"></i>
                </div>
                @php $profitClass = $profitChange >= 0 ? 'positive' : 'negative'; @endphp
                <span class="change-badge {{ $profitClass }}">
                    <i class="fas fa-arrow-{{ $profitChange >= 0 ? 'up' : 'down' }} text-[9px]"></i>
                    {{ abs($profitChange) }}%
                </span>
            </div>
            <p class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Laba Kotor</p>
            <h3 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black text-gray-900 tracking-tight leading-tight my-2">
                Rp {{ number_format($todayGrossProfit, 0, ',', '.') }}
            </h3>
            <p class="text-xs sm:text-sm text-gray-400 mt-3 font-semibold">
                HPP: <span class="text-gray-700">Rp {{ number_format($todayCostPrice, 0, ',', '.') }}</span>
            </p>
        </a>

        {{-- Card 3: Total Transaksi --}}
        <a href="{{ route('activity.index') }}" class="dashboard-metric-card hover:no-underline">
            <div class="card-glow bg-gradient-to-r from-amber-500 to-orange-500"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-circle bg-amber-50 text-amber-500">
                    <i class="fas fa-receipt"></i>
                </div>
                @php $txClass = $txCountChange >= 0 ? 'positive' : 'negative'; @endphp
                <span class="change-badge {{ $txClass }}">
                    <i class="fas fa-arrow-{{ $txCountChange >= 0 ? 'up' : 'down' }} text-[9px]"></i>
                    {{ abs($txCountChange) }}%
                </span>
            </div>
            <p class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Total Transaksi</p>
            <h3 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black text-gray-900 tracking-tight leading-tight my-2">
                {{ $todayTransactionCount }}
            </h3>
            <p class="text-xs sm:text-sm text-gray-400 mt-3 font-semibold">
                Pesanan Aktif: <span class="text-brand font-bold">{{ $activeOrders }}</span>
            </p>
        </a>

        {{-- Card 4: Item Terjual --}}
        <a href="{{ route('products.index') }}" class="dashboard-metric-card hover:no-underline">
            <div class="card-glow bg-gradient-to-r from-purple-500 to-fuchsia-500"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-circle bg-purple-50 text-purple-500">
                    <i class="fas fa-box"></i>
                </div>
                @php $itemClass = $itemsChange >= 0 ? 'positive' : 'negative'; @endphp
                <span class="change-badge {{ $itemClass }}">
                    <i class="fas fa-arrow-{{ $itemsChange >= 0 ? 'up' : 'down' }} text-[9px]"></i>
                    {{ abs($itemsChange) }}%
                </span>
            </div>
            <p class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Item Terjual</p>
            <h3 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black text-gray-900 tracking-tight leading-tight my-2">
                {{ number_format($todayItemsSold, 0, ',', '.') }}
            </h3>
            <p class="text-xs sm:text-sm text-gray-400 mt-3 font-semibold">
                Produk Aktif: <span class="text-gray-700">{{ $totalProducts }}</span>
            </p>
        </a>
    </div>

    {{-- ===== ROW 2: CHARTS SET 1 (TRENDS & PAYMENT METHODS) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Chart: 7-Day Revenue Trend (2/3 width) --}}
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-800">Tren Pendapatan Mingguan</h3>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Analisis omzet per hari dalam filter rentang aktif</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="live-dot"></span>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Live</span>
                </div>
            </div>
            <div id="chart-weekly-trend" style="min-height: 280px;"></div>
        </div>

        {{-- Chart: Payment Methods Donut (1/3 width) --}}
        <div class="chart-card">
            <div class="mb-4">
                <h3 class="text-base sm:text-lg font-extrabold text-gray-800">Metode Pembayaran</h3>
                <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Distribusi jenis transaksi</p>
            </div>
            <div id="chart-payment-donut" style="min-height: 280px;"></div>
            @if(empty($paymentMethods))
                <div class="flex flex-col items-center justify-center py-8 text-center" id="payment-empty">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                        <i class="fas fa-credit-card text-gray-300 text-lg"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-400 font-bold">Belum ada transaksi di periode ini</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== ROW 3: CHARTS SET 2 (CATEGORY SALES & PEAK HOURS) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        {{-- Chart: Product Category Distribution (1/3 width) --}}
        <div class="chart-card">
            <div class="mb-4">
                <h3 class="text-base sm:text-lg font-extrabold text-gray-800">Distribusi Kategori Menu</h3>
                <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Penjualan berdasarkan kategori produk</p>
            </div>
            <div id="chart-category-donut" style="min-height: 280px;"></div>
            <div class="flex flex-col items-center justify-center py-8 text-center hidden" id="category-empty">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i class="fas fa-tags text-gray-300 text-lg"></i>
                </div>
                <p class="text-xs sm:text-sm text-gray-400 font-bold">Belum ada penjualan kategori</p>
            </div>
        </div>

        {{-- Chart: Peak Ordering Hours (2/3 width) --}}
        <div class="chart-card lg:col-span-2">
            <div class="mb-4">
                <h3 class="text-base sm:text-lg font-extrabold text-gray-800">Jam Sibuk Pemesanan</h3>
                <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Total omzet berdasarkan waktu transaksi (24 jam)</p>
            </div>
            <div id="chart-peak-hours" style="min-height: 280px;"></div>
        </div>
    </div>

    {{-- ===== ROW 3.5: MONITOR ANTREAN PESANAN (NEW) ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col gap-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-lg font-extrabold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-brand"></i> Monitor Antrean Pesanan
                </h3>
                <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Daftar transaksi aktif dan selesai pada periode filter</p>
            </div>
            <a href="{{ route('activity.index') }}" class="text-xs font-extrabold text-brand hover:text-brand-strong transition-colors flex items-center gap-1">
                Kelola Semua Antrean <i class="fas fa-arrow-right text-[9px]"></i>
            </a>
        </div>

        <!-- Flowbite Tabs Navigation -->
        <div class="border-b border-gray-100">
            <ul class="flex flex-wrap -mb-px text-sm font-bold text-center gap-2" id="dashboard-bill-tab" data-tabs-toggle="#dashboard-tab-content" role="tablist">
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="dash-all-tab" data-tabs-target="#dash-all" type="button" role="tab" aria-selected="true">Semua Antrean ({{ $todayTransactions->count() }})</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="dash-active-tab" data-tabs-target="#dash-active" type="button" role="tab" aria-selected="false">Aktif ({{ $todayTransactions->filter(fn($t) => $t->status == 'active')->count() }})</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="dash-process-tab" data-tabs-target="#dash-process" type="button" role="tab" aria-selected="false">Proses Masak ({{ $todayTransactions->filter(fn($t) => $t->status == 'process')->count() }})</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="dash-payment-tab" data-tabs-target="#dash-payment" type="button" role="tab" aria-selected="false">Pembayaran ({{ $todayTransactions->filter(fn($t) => $t->status == 'payment')->count() }})</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="dash-paid-tab" data-tabs-target="#dash-paid" type="button" role="tab" aria-selected="false">Lunas ({{ $todayTransactions->filter(fn($t) => $t->status == 'paid')->count() }})</button>
                </li>
            </ul>
        </div>

        <!-- Tab Contents -->
        <div id="dashboard-tab-content">
            
            <!-- ALL TAB -->
            <div class="hidden p-2 rounded-2xl" id="dash-all" role="tabpanel">
                @if($todayTransactions->count() === 0)
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-3"><i class="fas fa-folder-open text-lg"></i></span>
                        <h4 class="font-bold text-gray-700 text-xs">Tidak Ada Antrean</h4>
                        <p class="text-[11px] text-gray-400 mt-1">Saat ini belum ada transaksi kasir yang berjalan di periode ini.</p>
                    </div>
                @else
                    <ul role="list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($todayTransactions as $transaction)
                            @php
                                if($transaction->status == 'active') {
                                    $class_color = "bg-emerald-50 text-emerald-700 border-emerald-200";
                                } else if($transaction->status == 'process') {
                                    $class_color = "bg-brand-soft text-brand border-brand-softer";
                                } else if($transaction->status == 'payment') {
                                    $class_color = "bg-amber-50 text-amber-700 border-amber-200";
                                } else if($transaction->status == 'paid') {
                                    $class_color = "bg-red-50 text-red-700 border-red-200";
                                } else {
                                    $class_color = "bg-gray-50 text-gray-600 border-gray-200";
                                }
                            @endphp
                            <li class="p-4 bg-white border border-gray-100 hover:border-brand-medium rounded-xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                <div class="flex items-center gap-3 justify-between flex-wrap sm:flex-nowrap">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        @if ($transaction->order_type == 'take_away')
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                <i class="fas fa-bag-shopping text-lg"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                <i class="fas fa-utensils text-lg"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-extrabold text-heading text-sm truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                <span class="status text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $class_color }} border">
                                                    {{ $transaction->status }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1 flex-wrap">
                                                <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md text-[10px]">
                                                    <i class="fas fa-table mr-1 text-[9px]"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                </span>
                                            </p>
                                            <p class="text-[9px] text-gray-400 mt-1 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        <button type="button" class="w-8 h-8 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-lg shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- ACTIVE TAB -->
            <div class="hidden p-2 rounded-2xl" id="dash-active" role="tabpanel">
                @php
                    $dash_active = $todayTransactions->filter(fn($e) => $e->status == 'active');
                @endphp
                @if($dash_active->count() === 0)
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-3"><i class="fas fa-check-double text-lg"></i></span>
                        <h4 class="font-bold text-gray-700 text-xs">Tidak Ada Antrean Aktif</h4>
                        <p class="text-[11px] text-gray-400 mt-1">Semua pesanan aktif kasir telah diproses.</p>
                    </div>
                @else
                    <ul role="list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($dash_active as $transaction)
                            <li class="p-4 bg-white border border-gray-100 hover:border-brand-medium rounded-xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                <div class="flex items-center gap-3 justify-between flex-wrap sm:flex-nowrap">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        @if ($transaction->order_type == 'take_away')
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                <i class="fas fa-bag-shopping text-lg"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                <i class="fas fa-utensils text-lg"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-extrabold text-heading text-sm truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                <span class="status text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border-emerald-200 border">
                                                    {{ $transaction->status }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md text-[10px]">
                                                    <i class="fas fa-table mr-1 text-[9px]"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                </span>
                                            </p>
                                            <p class="text-[9px] text-gray-400 mt-1 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        <button type="button" class="w-8 h-8 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-lg shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- PROCESS TAB -->
            <div class="hidden p-2 rounded-2xl" id="dash-process" role="tabpanel">
                @php
                    $dash_process = $todayTransactions->filter(fn($e) => $e->status == 'process');
                @endphp
                @if($dash_process->count() === 0)
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-3"><i class="fas fa-fire-burner text-lg"></i></span>
                        <h4 class="font-bold text-gray-700 text-xs">Tidak Ada Proses Masak</h4>
                        <p class="text-[11px] text-gray-400 mt-1">Saat ini dapur tidak sedang memasak antrean pesanan.</p>
                    </div>
                @else
                    <ul role="list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($dash_process as $transaction)
                            <li class="p-4 bg-white border border-gray-100 hover:border-brand-medium rounded-xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                <div class="flex items-center gap-3 justify-between flex-wrap sm:flex-nowrap">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        @if ($transaction->order_type == 'take_away')
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                <i class="fas fa-bag-shopping text-lg"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                <i class="fas fa-utensils text-lg"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-extrabold text-heading text-sm truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                <span class="status text-[9px] font-bold uppercase tracking-wider bg-brand-soft text-brand border-brand-softer border rounded-full px-2 py-0.5">
                                                    {{ $transaction->status }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md text-[10px]">
                                                    <i class="fas fa-table mr-1 text-[9px]"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                </span>
                                            </p>
                                            <p class="text-[9px] text-gray-400 mt-1 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        <button type="button" class="w-8 h-8 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-lg shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- PAYMENT TAB -->
            <div class="hidden p-2 rounded-2xl" id="dash-payment" role="tabpanel">
                @php
                    $dash_payment = $todayTransactions->filter(fn($e) => $e->status == 'payment');
                @endphp
                @if($dash_payment->count() === 0)
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-3"><i class="fas fa-wallet text-lg"></i></span>
                        <h4 class="font-bold text-gray-700 text-xs">Tidak Ada Tagihan Pembayaran</h4>
                        <p class="text-[11px] text-gray-400 mt-1">Saat ini kasir bersih dari antrean tagihan pembayaran.</p>
                    </div>
                @else
                    <ul role="list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($dash_payment as $transaction)
                            <li class="p-4 bg-white border border-gray-100 hover:border-brand-medium rounded-xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                <div class="flex items-center gap-3 justify-between flex-wrap sm:flex-nowrap">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        @if ($transaction->order_type == 'take_away')
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                <i class="fas fa-bag-shopping text-lg"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                <i class="fas fa-utensils text-lg"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-extrabold text-heading text-sm truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                <span class="status text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border-amber-200 border">
                                                    {{ $transaction->status }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md text-[10px]">
                                                    <i class="fas fa-table mr-1 text-[9px]"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                </span>
                                            </p>
                                            <p class="text-[9px] text-gray-400 mt-1 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        <button type="button" class="w-8 h-8 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-lg shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- PAID TAB -->
            <div class="hidden p-2 rounded-2xl" id="dash-paid" role="tabpanel">
                @php
                    $dash_paid = $todayTransactions->filter(fn($e) => $e->status == 'paid');
                @endphp
                @if($dash_paid->count() === 0)
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-3"><i class="fas fa-circle-check text-lg"></i></span>
                        <h4 class="font-bold text-gray-700 text-xs">Belum Ada Transaksi Lunas</h4>
                        <p class="text-[11px] text-gray-400 mt-1">Antrean kasir lunas akan tercantum di sini.</p>
                    </div>
                @else
                    <ul role="list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($dash_paid as $transaction)
                            <li class="p-4 bg-white border border-gray-100 hover:border-brand-medium rounded-xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                <div class="flex items-center gap-3 justify-between flex-wrap sm:flex-nowrap">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        @if ($transaction->order_type == 'take_away')
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                <i class="fas fa-bag-shopping text-lg"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                <i class="fas fa-utensils text-lg"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-extrabold text-heading text-sm truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                <span class="status text-[9px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border-red-200 border rounded-full px-2 py-0.5">
                                                    {{ $transaction->status }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md text-[10px]">
                                                    <i class="fas fa-table mr-1 text-[9px]"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                </span>
                                            </p>
                                            <p class="text-[9px] text-gray-400 mt-1 flex items-center gap-1">
                                                <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-2">
                                        <button type="button" class="w-8 h-8 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-lg shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>

    {{-- ===== ROW 4: DATA TABLES & LOW STOCK ALERT (3 COLUMNS) ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Top 5 Products --}}
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-800">🏆 Menu Terlaris</h3>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Top 5 terlaris di periode ini</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-brand bg-brand-soft px-3 py-1 rounded-full">TOP 5</span>
                    <a href="{{ route('products.index') }}" class="text-xs font-extrabold text-brand hover:text-brand-strong transition-colors">
                        Lihat Semua <i class="fas fa-arrow-right ml-0.5 text-[9px]"></i>
                    </a>
                </div>
            </div>
            @if($topProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th class="w-12">#</th>
                                <th>Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $idx => $product)
                                @php
                                    $rankClass = match($idx) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => 'default' };
                                @endphp
                                <tr>
                                    <td>
                                        <span class="rank-badge {{ $rankClass }}">{{ $idx + 1 }}</span>
                                    </td>
                                    <td class="font-bold text-gray-700">{{ $product->product_name }}</td>
                                    <td class="text-center">
                                        <span class="inline-flex items-center justify-center w-9 h-7 bg-brand-soft text-brand text-xs font-extrabold rounded-lg">
                                            {{ $product->total_qty }}
                                        </span>
                                    </td>
                                    <td class="text-right font-extrabold text-gray-800">Rp {{ number_format($product->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center mb-3">
                        <i class="fas fa-trophy text-amber-300 text-xl"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-400 font-bold">Belum ada penjualan</p>
                </div>
            @endif
        </div>

        {{-- Recent Transactions --}}
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-800">📋 Transaksi Terbaru</h3>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">5 transaksi terakhir periode ini</p>
                </div>
                <a href="{{ route('activity.index') }}" class="text-xs font-extrabold text-brand hover:text-brand-strong transition-colors">
                    Lihat Semua <i class="fas fa-arrow-right ml-0.5 text-[9px]"></i>
                </a>
            </div>
            @if($recentTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Waktu</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                                <tr>
                                    <td>
                                        <span class="font-mono text-xs font-bold text-gray-500 bg-gray-50 px-2.5 py-1 rounded-lg">
                                            {{ Str::limit($tx->invoice_number, 12) }}
                                        </span>
                                    </td>
                                    <td class="text-gray-500 text-xs font-medium">
                                        {{ \Carbon\Carbon::parse($tx->paid_at)->format('H:i') }}
                                    </td>
                                    <td class="text-right font-extrabold text-gray-800">
                                        Rp {{ number_format($tx->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $method = strtoupper($tx->paid_method ?? 'CASH');
                                            $methodColor = match($method) {
                                                'QRIS' => 'bg-violet-50 text-violet-600',
                                                'DEBIT', 'CREDIT' => 'bg-blue-50 text-blue-600',
                                                'TRANSFER' => 'bg-cyan-50 text-cyan-600',
                                                default => 'bg-emerald-50 text-emerald-600'
                                            };
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 {{ $methodColor }} text-[10px] font-extrabold rounded-full uppercase tracking-wider">
                                            {{ $method }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-3">
                        <i class="fas fa-receipt text-blue-300 text-xl"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-400 font-bold">Belum ada transaksi</p>
                </div>
            @endif
        </div>

        {{-- Low Stock Warnings Widget (NEW) --}}
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-800">⚠️ Peringatan Stok</h3>
                    <p class="text-xs sm:text-sm text-gray-400 font-medium mt-0.5">Produk dengan stok menipis (≤ 10)</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-full uppercase">Stok Rendah</span>
                    <a href="{{ route('products.index') }}" class="text-xs font-extrabold text-brand hover:text-brand-strong transition-colors">
                        Lihat Semua <i class="fas fa-arrow-right ml-0.5 text-[9px]"></i>
                    </a>
                </div>
            </div>
            @if($lowStockProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Stok</th>
                                <th class="text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $prod)
                                @php
                                    $badgeColor = $prod->stock <= 5 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600';
                                @endphp
                                <tr>
                                    <td>
                                        <p class="font-bold text-gray-700 leading-tight">{{ $prod->nama }}</p>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">{{ $prod->category->nama ?? 'No Category' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 {{ $badgeColor }} text-xs font-extrabold rounded-lg">
                                            {{ $prod->stock }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('products.edit', $prod->uuid) }}" class="text-xs font-extrabold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition-colors border-none inline-block">
                                            Update
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center h-full">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mb-3 text-emerald-500">
                        <i class="fas fa-check-circle text-xl animate-pulse"></i>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 font-bold">Stok Aman</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-1">Seluruh stok produk berada di atas 10 unit</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== ROW 5: MONTHLY SUMMARY BAR (ENLARGED TYPOGRAPHY) ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('activity.index') }}" class="summary-pill hover:no-underline">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shrink-0 shadow-md">
                <i class="fas fa-calendar-check text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Omzet Bulan Ini</p>
                <h4 class="text-lg sm:text-xl lg:text-2xl font-black text-gray-800 mt-0.5">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</h4>
            </div>
        </a>
        <a href="{{ route('activity.index') }}" class="summary-pill hover:no-underline">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0 shadow-md">
                <i class="fas fa-hashtag text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Transaksi Bulan Ini</p>
                <h4 class="text-lg sm:text-xl lg:text-2xl font-black text-gray-800 mt-0.5">{{ number_format($monthlyTransactionCount, 0, ',', '.') }} Invoice</h4>
            </div>
        </a>
        <a href="{{ route('activity.index') }}" class="summary-pill hover:no-underline">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shrink-0 shadow-md">
                <i class="fas fa-divide text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Rata-Rata Harian</p>
                <h4 class="text-lg sm:text-xl lg:text-2xl font-black text-gray-800 mt-0.5">Rp {{ number_format($monthlyAvgPerDay, 0, ',', '.') }}</h4>
            </div>
        </a>
    </div>

    {{-- ===== ADDITIONAL INFO ROW ===== --}}
    <div class="flex items-center justify-between bg-white rounded-2xl px-5 py-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-brand-soft flex items-center justify-center">
                <i class="fas fa-info-circle text-brand text-sm"></i>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 font-semibold">
                Diskon total periode ini:
                <span class="font-bold text-gray-800">Rp {{ number_format($todayDiscount, 0, ',', '.') }}</span> &middot;
                Pajak total:
                <span class="font-bold text-gray-800">Rp {{ number_format($todayTax, 0, ',', '.') }}</span>
            </p>
        </div>
        <button onclick="location.reload()" class="shrink-0 w-9 h-9 rounded-xl bg-gray-50 hover:bg-brand-soft text-gray-400 hover:text-brand flex items-center justify-center cursor-pointer transition-all duration-200 border-none outline-none">
            <i class="fas fa-rotate text-sm"></i>
        </button>
    </div>

</div>

<!-- HTML Modal Rincian Struk (Realistic Cashier Receipt Modal) -->
<div id="modal-see-transaction" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all duration-300 scale-95 opacity-100 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-receipt text-brand"></i> Rincian Struk Transaksi
            </h3>
            <div class="flex items-center gap-2">
                <button type="button" class="w-8 h-8 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 cursor-pointer outline-none border border-emerald-200 transition-all print-transaction-button" title="Cetak Struk">
                    <i class="fas fa-print text-sm"></i>
                </button>
                <button type="button" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100 cursor-pointer outline-none border border-red-200 transition-all delete-transaction-button" title="Hapus Transaksi">
                    <i class="fas fa-trash-can text-sm"></i>
                </button>
                <button type="button" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200 cursor-pointer outline-none border border-gray-200 transition-all tutup-modal-order">
                    &times;
                </button>
            </div>
        </div>
        <!-- Bluetooth Printer Control Panel -->
        <div class="px-6 py-3 bg-slate-50 border-b border-gray-100 flex items-center justify-between gap-3 text-xs flex-wrap">
            <div class="flex items-center gap-2">
                <button type="button" id="btn-toggle-bluetooth" class="px-3 py-1.5 bg-brand hover:bg-brand-strong text-white font-bold rounded-lg flex items-center gap-1.5 transition-all cursor-pointer shadow-sm">
                    <i class="fab fa-bluetooth text-[11px]"></i> <span id="bt-status-text">Hubungkan Bluetooth</span>
                </button>
                <span id="bt-device-name" class="font-semibold text-emerald-600 hidden truncate max-w-[120px]"></span>
            </div>
            
            <div class="flex items-center gap-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode:</label>
                <select id="print-method-select" class="px-2 py-1 bg-white border border-brand-medium rounded-lg text-[11px] font-bold focus:outline-none cursor-pointer">
                    <option value="browser" selected>Browser Print (HTML)</option>
                    <option value="bluetooth">Direct Bluetooth</option>
                    <option value="rawbt">RawBT (Android)</option>
                </select>
            </div>
        </div>
        <!-- Body (Struk Kasir Fisik) -->
        <div class="p-6 overflow-y-auto bg-gray-50/50 flex-1 flex flex-col gap-4">
            <input type="hidden" name="uuid_transaction_detail" id="uuid_transaction_detail" />
            
            <!-- Paper Struk Container -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs flex flex-col relative">
                <!-- Left and Right decorative notches for ticket look -->
                <div class="absolute top-1/2 -left-2 w-4 h-4 bg-gray-50 border-r border-gray-200 rounded-full -translate-y-1/2"></div>
                <div class="absolute top-1/2 -right-2 w-4 h-4 bg-gray-50 border-l border-gray-200 rounded-full -translate-y-1/2"></div>
                
                <!-- Header Struk -->
                <div class="text-center pb-4 border-b border-dashed border-gray-200 mb-4">
                    <h4 class="font-extrabold text-heading text-lg tracking-wider">POS KASIR</h4>
                    <p class="text-[10px] text-gray-400 mt-1 uppercase" id="receipt-invoice-num">INVOICE</p>
                </div>
                
                <!-- Metadata Struk -->
                <div class="flex flex-col gap-1.5 text-[11px] text-gray-600 pb-4 border-b border-dashed border-gray-200 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-medium">Tanggal:</span>
                        <span class="font-bold text-gray-700" id="receipt-date">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-medium">Meja:</span>
                        <span class="font-bold text-gray-700" id="receipt-table">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-medium">Input Oleh:</span>
                        <span class="font-bold text-brand" id="receipt-cashier">-</span>
                    </div>
                </div>
                
                <!-- List Item Penjualan -->
                <ul class="transaction-detail-list divide-y divide-dashed divide-gray-200 flex flex-col gap-2 max-h-[260px] overflow-y-auto pr-1">
                    <!-- Ajax list loads here -->
                </ul>
                
                <!-- Total Struk Breakdown -->
                <div class="border-t border-dashed border-gray-200 pt-4 mt-4 flex flex-col gap-1.5 text-xs text-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-400">Subtotal</span>
                        <span class="font-semibold text-heading transaction-detail-subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-400">Pajak (10%)</span>
                        <span class="font-semibold text-heading transaction-detail-pajak">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-400">Potongan Diskon</span>
                        <span class="font-semibold text-red-500 transaction-detail-diskon">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-dashed border-gray-200 pt-2 mt-2">
                        <span class="font-bold text-heading text-sm">TOTAL</span>
                        <span class="font-extrabold text-brand text-lg transaction-detail-total">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-dashed border-gray-100 pt-2 text-[11px]">
                        <span class="font-semibold text-gray-400">Dibayar (<span class="paid_method font-bold text-brand uppercase text-[10px]"></span>)</span>
                        <span class="font-bold text-heading transaction-detail-paid">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-400">Kembalian</span>
                        <span class="font-bold text-heading transaction-detail-changed">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50/20">
            <button type="button" class="px-5 py-2.5 bg-brand hover:bg-brand-strong text-white rounded-xl text-xs font-bold shadow-md shadow-brand/20 transition-all cursor-pointer w-full text-center tutup-modal-order">Tutup Rincian</button>
        </div>
    </div>
</div>

{{-- ===== APEXCHARTS CDN ===== --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script type="module">
$(document).ready(function() {

    // === PRESET DATE FILTER HIGHLIGHT ===
    const currentStart = '{{ $startDate }}';
    const currentEnd = '{{ $endDate }}';
    
    $('.preset-btn').each(function() {
        const btnStart = $(this).data('start');
        const btnEnd = $(this).data('end');
        if (btnStart === currentStart && btnEnd === currentEnd) {
            $(this).addClass('bg-brand text-white shadow-sm shadow-brand/10');
            $(this).removeClass('text-gray-600 hover:bg-gray-100 bg-transparent');
        } else {
            $(this).addClass('text-gray-600 hover:bg-gray-100 bg-transparent');
            $(this).removeClass('bg-brand text-white shadow-sm shadow-brand/10');
        }
    });

    $('.preset-btn').click(function() {
        const start = $(this).data('start');
        const end = $(this).data('end');
        $('#start_date').val(start);
        $('#end_date').val(end);
        $('#filter-form').submit();
    });

    // === 7-DAY REVENUE TREND CHART ===
    const weeklyData = @json($weeklyTrend);
    const trendLabels = weeklyData.map(d => d.label);
    const trendValues = weeklyData.map(d => d.revenue);

    const trendOptions = {
        series: [{
            name: 'Omzet',
            data: trendValues
        }],
        chart: {
            type: 'area',
            height: 280,
            fontFamily: 'Red Hat Text, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false },
            dropShadow: {
                enabled: true,
                top: 4,
                left: 0,
                blur: 10,
                opacity: 0.15,
                color: '#6366f1'
            }
        },
        colors: ['#6366f1'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 95, 100]
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        dataLabels: {
            enabled: true,
            formatter: val => val >= 1000000 ? 'Rp ' + (val / 1000000).toFixed(1) + 'jt' : (val >= 1000 ? 'Rp ' + (val / 1000).toFixed(0) + 'rb' : 'Rp ' + val),
            style: {
                fontSize: '11px',
                fontWeight: 700,
                colors: ['#4f46e5'],
                pointerEvents: 'none'
            },
            background: {
                enabled: false
            },
            offsetY: -10
        },
        markers: {
            size: 5,
            colors: ['#fff'],
            strokeColors: '#6366f1',
            strokeWidth: 2.5,
            hover: { size: 7 }
        },
        xaxis: {
            categories: trendLabels,
            labels: {
                style: {
                    fontSize: '11px',
                    fontWeight: 600,
                    colors: '#9ca3af'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: val => {
                    if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                    if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
                },
                style: {
                    fontSize: '11px',
                    fontWeight: 600,
                    colors: '#9ca3af'
                }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 3,
            padding: { left: 15, right: 40 }
        },
        tooltip: {
            shared: true,
            intersect: false,
            theme: 'dark',
            y: {
                formatter: val => 'Rp ' + Math.round(val).toLocaleString('id-ID')
            },
            style: { fontSize: '12px' }
        }
    };

    const trendChart = new ApexCharts(document.querySelector('#chart-weekly-trend'), trendOptions);
    trendChart.render();

    // === PAYMENT METHOD DONUT CHART ===
    const paymentData = @json($paymentMethods);
    const paymentLabels = Object.keys(paymentData);
    const paymentValues = paymentLabels.map(l => paymentData[l].total);

    if (paymentLabels.length > 0) {
        const donutColorMap = {
            'CASH': '#10b981',
            'QRIS': '#8b5cf6',
            'DEBIT': '#3b82f6',
            'CREDIT': '#06b6d4',
            'TRANSFER': '#f59e0b',
            'EDC': '#ec4899'
        };
        const donutColors = paymentLabels.map(l => donutColorMap[l] || '#6b7280');

        const donutOptions = {
            series: paymentValues,
            labels: paymentLabels,
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Red Hat Text, sans-serif'
            },
            colors: donutColors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '13px',
                                fontWeight: 700,
                                color: '#374151'
                            },
                            value: {
                                show: true,
                                fontSize: '16px',
                                fontWeight: 800,
                                color: '#111827',
                                formatter: val => 'Rp ' + parseInt(val).toLocaleString('id-ID')
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '12px',
                                fontWeight: 600,
                                color: '#9ca3af',
                                formatter: w => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return 'Rp ' + total.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 600,
                labels: { colors: '#6b7280' },
                markers: { size: 6, shape: 'circle' },
                itemMargin: { horizontal: 8, vertical: 4 }
            },
            stroke: {
                width: 3,
                colors: ['#fff']
            },
            tooltip: {
                y: {
                    formatter: val => 'Rp ' + parseInt(val).toLocaleString('id-ID')
                },
                style: { fontSize: '12px' }
            }
        };

        // Hide the empty state
        $('#payment-empty').hide();

        const donutChart = new ApexCharts(document.querySelector('#chart-payment-donut'), donutOptions);
        donutChart.render();
    }

    // === PRODUCT CATEGORY DONUT CHART (NEW) ===
    const categorySalesData = @json($categorySales);
    const categoryLabels = categorySalesData.map(d => d.category_name);
    const categoryValues = categorySalesData.map(d => parseFloat(d.total_sales));

    if (categoryLabels.length > 0) {
        const catColors = ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#3b82f6', '#8b5cf6', '#06b6d4', '#f43f5e', '#14b8a6'];
        
        const catDonutOptions = {
            series: categoryValues,
            labels: categoryLabels,
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Red Hat Text, sans-serif'
            },
            colors: catColors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '13px',
                                fontWeight: 700,
                                color: '#374151'
                            },
                            value: {
                                show: true,
                                fontSize: '16px',
                                fontWeight: 800,
                                color: '#111827',
                                formatter: val => 'Rp ' + parseInt(val).toLocaleString('id-ID')
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '12px',
                                fontWeight: 600,
                                color: '#9ca3af',
                                formatter: w => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return 'Rp ' + total.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 600,
                labels: { colors: '#6b7280' },
                markers: { size: 6, shape: 'circle' },
                itemMargin: { horizontal: 8, vertical: 4 }
            },
            stroke: {
                width: 3,
                colors: ['#fff']
            },
            tooltip: {
                y: {
                    formatter: val => 'Rp ' + parseInt(val).toLocaleString('id-ID')
                },
                style: { fontSize: '12px' }
            }
        };

        $('#category-empty').hide();
        const catChart = new ApexCharts(document.querySelector('#chart-category-donut'), catDonutOptions);
        catChart.render();
    } else {
        $('#category-empty').removeClass('hidden');
        $('#chart-category-donut').hide();
    }

    // === HOURLY PEAK ORDERING HOURS CHART (NEW) ===
    const hourlyData = @json($hourlyData);
    const hourlyLabels = Array.from({length: 24}, (_, i) => String(i).padStart(2, '0') + ':00');

    const peakOptions = {
        series: [{
            name: 'Omzet',
            data: hourlyData
        }],
        chart: {
            type: 'bar',
            height: 280,
            fontFamily: 'Red Hat Text, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        colors: ['#3b82f6'],
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: hourlyLabels,
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: false,
                style: {
                    fontSize: '10px',
                    fontWeight: 600,
                    colors: '#9ca3af'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: val => {
                    if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                    if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
                },
                style: {
                    fontSize: '11px',
                    fontWeight: 600,
                    colors: '#9ca3af'
                }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 3,
            padding: { left: 15, right: 15 }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: val => 'Rp ' + Math.round(val).toLocaleString('id-ID')
            },
            style: { fontSize: '12px' }
        }
    };

    const peakChart = new ApexCharts(document.querySelector('#chart-peak-hours'), peakOptions);
    peakChart.render();

    // === DASHBOARD BILLING QUEUES MODAL & DETAIL LOGIC ===
    const modal = new window.Modal(document.getElementById('modal-see-transaction'), {
        placement: "center",
        backdrop: "dynamic",
        backdropClasses: "bg-gray-900/50 fixed inset-0 z-40",
        closable: true,
    });
    var ini;

    // Use delegated click selector for robust event handling across tab switches
    $(document).on('click', '.see-transaction', function() {
        $('.transaction-detail-list').html('');
        var uuid = $(this).closest('li').data('uuid');
        var url = "{{ route('transaction.show', ':id') }}";
        url = url.replace(':id', uuid);
        ini = this;
        loading();
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                if(data.success == true) {
                    var transaction = data.transaction;
                    $('#uuid_transaction_detail').val(transaction.uuid);
                    $('#modal-see-transaction').attr('data-status', transaction.status);
                    $('#receipt-invoice-num').text('INVOICE #' + transaction.invoice_number);
                    $('#receipt-date').text(moment(transaction.paid_at || transaction.created_at).format('DD/MM/YYYY HH:mm'));
                    
                    var tableName = transaction.order_type === 'take_away' ? 'Take Away' : (transaction.table ? transaction.table.name : 'Dine In');
                    $('#receipt-table').text(tableName);
                    
                    var cashierName = transaction.user ? transaction.user.name : '-';
                    $('#receipt-cashier').text(cashierName);

                    var product = data.product;

                    if(transaction.order_item.length > 0) {
                        var orderList = "";
                        var orderTotal = 0;
                        transaction.order_item.forEach(elem => {
                            var productItem = product.filter(prod => {
                                return prod.uuid == elem.product_id;
                            })[0];
                            
                            var name = elem.product_name || 'Item Manual';
                            var image = "{{ Vite::asset('resources/img/no_image_available.png') }}";
                            
                            if(productItem != null) {
                                name = productItem.name;
                                image = productItem.picture 
                                    ? "{{ asset('storage/products/:picture') }}".replace(':picture', productItem.picture)
                                    : "{{ Vite::asset('resources/img/no_image_available.png') }}";
                            }
                            
                            orderList += `
                                <li class="flex items-center gap-3 py-2.5">
                                    <img class="h-10 w-10 object-cover rounded-lg border border-gray-100 shrink-0" src="${image}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-800 truncate">${name}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">${elem.qty}x @ Rp ${addCommas(elem.price || (elem.subtotal/elem.qty))}</p>
                                        ${elem.note ? `<p class="text-[9px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md mt-1 w-max"><i class="far fa-sticky-note mr-0.5"></i> ${elem.note}</p>` : ''}
                                    </div>
                                    <div class="text-right text-xs font-bold text-gray-700 shrink-0">
                                        Rp ${addCommas(elem.subtotal)}
                                    </div>
                                </li>
                            `;
                            orderTotal += elem.subtotal;
                        });
                        
                        var tax = transaction.tax || 0;
                        var discount = transaction.discount || 0;
                        var total = transaction.total || orderTotal;
                        var paid = transaction.total_paid || 0;
                        var changed = paid > 0 ? (paid - total) : 0;
                        
                        $('.transaction-detail-list').html(orderList);
                        $('.transaction-detail-subtotal').html('Rp ' + addCommas(transaction.subtotal || orderTotal));
                        $('.transaction-detail-pajak').html('Rp ' + addCommas(tax));
                        $('.transaction-detail-diskon').html('Rp ' + addCommas(discount));
                        $('.transaction-detail-total').html('Rp ' + addCommas(total));
                        $('.transaction-detail-paid').html('Rp ' + addCommas(paid));
                        $('.transaction-detail-changed').html('Rp ' + addCommas(changed));
                        $('.paid_method').text(transaction.paid_method || 'CASH');
                    }
                    
                    modal.toggle();
                    removeLoading();

                    $('.tutup-modal-order').on('click', function() {
                        modal.hide();
                    });
                }
            },
            error: function(data) {
                removeLoading();
                oAlert('red', 'Error', 'Gagal memuat rincian transaksi');
            }
        });
    });

    // Bluetooth — auto-reconnect saat halaman dimuat
    if (typeof initBluetoothUI === 'function') {
        initBluetoothUI();
    }

    // Bluetooth Printer Toggler
    $('#modal-see-transaction').on('click', '#btn-toggle-bluetooth', async function() {
        if (window.bluetoothPrinterInstance && window.bluetoothPrinterInstance.isConnected()) {
            window.bluetoothPrinterInstance.disconnect();
            if (window._setBtUI) window._setBtUI(false);
            oAlert('orange', 'Disconnected', 'Printer Bluetooth terputus.');
        } else if (window.bluetoothPrinterInstance) {
            loading();
            try {
                await window.bluetoothPrinterInstance.connect();
                const deviceName = window.bluetoothPrinterInstance.device.name || 'BT Printer';
                if (window._setBtUI) window._setBtUI(true, deviceName);
                removeLoading();
                oAlert('green', 'Connected', `Terhubung ke ${deviceName}`);
            } catch (e) {
                removeLoading();
                oAlert('red', 'Error', 'Gagal menghubungkan printer bluetooth atau dibatalkan.');
            }
        }
    });

    // Print Check Order
    $('#modal-see-transaction').on('click', '.print-transaction-button', function() {
        var transactionId = $('#uuid_transaction_detail').val();
        var status = $(ini).closest('li').data('status');
        
        var url = "";
        var noPrice = false;
        if(status == 'payment') {
            url = "{{ route('transaction.print.check', ':id') }}";
        } else if(status == 'paid') {
            url = "{{ route('transaction.print.payment', ':id') }}";
        } else {
            url = "{{ route('transaction.print.check.noprice', ':id') }}";
            noPrice = true;
        }
        url = url.replace(':id', transactionId);
        
        loading();
        $.ajax({
            method: 'GET',
            url: url,
            success: async function(data) {
                removeLoading();
                if(data.success == true) {
                    const method = $('#print-method-select').val();
                    
                    if (method === 'bluetooth') {
                        // Direct Bluetooth print
                        if (window.bluetoothPrinterInstance && !window.bluetoothPrinterInstance.isConnected()) {
                            oAlert('orange', 'Warning', 'Printer Bluetooth belum terhubung. Silakan hubungkan terlebih dahulu.');
                            return;
                        }
                        try {
                            loading();
                            const bytes = buildEscPosReceipt(data, noPrice);
                            if (window.bluetoothPrinterInstance) {
                                await window.bluetoothPrinterInstance.print(bytes);
                            }
                            removeLoading();
                            oAlert('green', 'Printed', 'Struk berhasil dicetak via Bluetooth.');
                        } catch (e) {
                            removeLoading();
                            oAlert('red', 'Error', 'Gagal mengirim data to printer Bluetooth.');
                        }
                    } else if (method === 'rawbt') {
                        // Android RawBT intent print
                        try {
                            const bytes = buildEscPosReceipt(data, noPrice);
                            if (typeof window.printViaRawBT === 'function') {
                                window.printViaRawBT(bytes);
                            }
                            oAlert('green', 'Success', 'Struk dikirim ke RawBT.');
                        } catch (e) {
                            oAlert('red', 'Error', 'Gagal memicu RawBT.');
                        }
                    } else {
                        // Standard optimized HTML print
                        printHtmlReceipt(data, noPrice);
                    }
                } else {
                    oAlert('red', 'Error', 'Gagal memuat data struk.');
                }
            },
            error: function(data) {
                removeLoading();
                oAlert('red', 'Error', 'Gagal menghubungi server untuk mengambil data struk.');
            }
        });
    });

    function performHistoryDelete(transactionId, adminPassword) {
        loading();
        var url = "{{ route('transaction.delete', ':id') }}";
        url = url.replace(':id', transactionId);
        var data = {};
        if (adminPassword) {
            data.admin_password = adminPassword;
        }
        $.ajax({
            type: "DELETE",
            url: url,
            data: data,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    removeLoading();
                    oAlert('red', 'Error', data.message || 'Gagal menghapus transaksi');
                }
            },
            error: function(xhr) {
                removeLoading();
                var message = "Gagal menghapus transaksi.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                oAlert("red", "Error", message);
            }
        });
    }

    // Delete Transaction
    $('#modal-see-transaction').on('click', '.delete-transaction-button', function() {
        var transactionId = $('#uuid_transaction_detail').val();
        var status = $('#modal-see-transaction').attr('data-status') || 'active';
        const userRole = "{{ auth()->user()->role }}";

        cConfirm("Warning", "Apakah Anda yakin ingin menghapus transaksi ini?", function() {
            if (status !== 'active' && userRole !== 'admin') {
                if (typeof window.requestAdminApproval === 'function') {
                    window.requestAdminApproval(function(password) {
                        if (!password) {
                            oAlert("orange", "Batal", "Penghapusan transaksi dibatalkan.");
                            return;
                        }
                        performHistoryDelete(transactionId, password);
                    });
                } else {
                    performHistoryDelete(transactionId, null);
                }
            } else {
                performHistoryDelete(transactionId, null);
            }
        });
    });

    // Helper: Convert transaction data to ESC/POS binary bytes
    function buildEscPosReceipt(data, noPrice = false) {
        const tx = data.transaction;
        const items = tx.order_item || [];
        const res = data.restaurant || {};
        const cashier = data.user || 'Kasir';

        if (typeof window.EscPosEncoder === 'undefined') {
            oAlert('red', 'Error', 'ESC/POS Encoder library not loaded.');
            return null;
        }
        const encoder = new window.EscPosEncoder();
        encoder.initialize();

        // Header
        encoder.alignCenter();
        encoder.bold(true);
        encoder.doubleSize(true);
        if (noPrice) {
            encoder.line('KITCHEN CHECK');
        } else {
            encoder.line(res.name || 'POS KASIR');
        }
        encoder.doubleSize(false);
        encoder.bold(false);
        
        if (!noPrice && res.location) {
            encoder.line(res.location);
        }
        encoder.line('================================'); // 58mm printer has 32 chars standard

        // Invoice details
        encoder.alignLeft();
        encoder.line(`Tanggal: ${moment(tx.paid_at || tx.created_at).format('DD/MM/YYYY HH:mm')}`);
        encoder.line(`Invoice: #${tx.invoice_number}`);
        encoder.line(`Meja   : ${tx.order_type === 'take_away' ? 'Take Away' : (tx.table ? tx.table.name : 'Dine In')}`);
        encoder.line(`Kasir  : ${cashier}`);
        encoder.line('--------------------------------');

        // Sales Items
        items.forEach(elem => {
            encoder.bold(true);
            encoder.line(elem.product_name || 'Item');
            encoder.bold(false);

            if (elem.note) {
                encoder.line(` * Note: ${elem.note}`);
            }

            if (noPrice) {
                // Kitchen check shows qty only
                encoder.line(`Qty: ${elem.qty}`);
            } else {
                const qtyPrice = `${elem.qty} x Rp ${addCommas(elem.price || (elem.subtotal/elem.qty))}`;
                const itemTotal = `Rp ${addCommas(elem.subtotal)}`;
                encoder.twoColumnRow(qtyPrice, itemTotal);
            }
        });

        encoder.line('--------------------------------');

        if (!noPrice) {
            // Totals and math for paid/check receipts
            const subtotalStr = `Rp ${addCommas(tx.subtotal || tx.total)}`;
            const taxStr = `Rp ${addCommas(tx.tax || 0)}`;
            const discStr = `Rp ${addCommas(tx.discount || 0)}`;
            const totalStr = `Rp ${addCommas(tx.total)}`;
            const paidStr = `Rp ${addCommas(tx.total_paid || 0)}`;
            const changed = tx.total_paid > 0 ? (tx.total_paid - tx.total) : 0;
            const changedStr = `Rp ${addCommas(changed)}`;

            encoder.twoColumnRow('Subtotal', subtotalStr);
            encoder.twoColumnRow('Pajak (10%)', taxStr);
            if (tx.discount > 0) {
                encoder.twoColumnRow('Diskon', '-' + discStr);
            }
            encoder.line('--------------------------------');
            
            encoder.bold(true);
            encoder.twoColumnRow('TOTAL', totalStr);
            encoder.bold(false);
            
            encoder.line('--------------------------------');
            encoder.twoColumnRow(`Dibayar (${tx.paid_method || 'CASH'})`, paidStr);
            encoder.twoColumnRow('Kembalian', changedStr);

            encoder.line('================================');
        }
        
        // Footer
        encoder.alignCenter();
        encoder.line(noPrice ? 'Sajian Segera Disiapkan' : 'Terima Kasih');
        if (!noPrice) {
            encoder.line('Atas Kunjungan Anda');
        }
        
        encoder.feed(3);
        encoder.cut();

        return encoder.getRaw();
    }

    // Helper: Generate structured 58mm HTML receipt inside dynamic iframe
    function printHtmlReceipt(data, noPrice = false) {
        const tx = data.transaction;
        const items = tx.order_item || [];
        const res = data.restaurant || {};
        const cashier = data.user || 'Kasir';
        
        let itemsHtml = '';
        items.forEach(elem => {
            itemsHtml += `
                <div style="margin-bottom: 8px;">
                    <p style="margin: 0; font-weight: bold; font-size: 13px;">${elem.product_name}</p>
                    ${elem.note ? `<p style="margin: 2px 0 2px 10px; font-style: italic; font-size: 11px;">* Note: ${elem.note}</p>` : ''}
                    <div style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 2px;">
                        ${noPrice ? `
                        <span style="font-weight: bold; font-size: 13px;">Qty: ${elem.qty}</span>
                        ` : `
                        <span>${elem.qty} x Rp ${addCommas(elem.price || (elem.subtotal/elem.qty))}</span>
                        <span style="font-weight: bold;">Rp ${addCommas(elem.subtotal)}</span>
                        `}
                    </div>
                </div>
            `;
        });

        const tax = tx.tax || 0;
        const discount = tx.discount || 0;
        const total = tx.total;
        const paid = tx.total_paid || 0;
        const changed = paid > 0 ? (paid - total) : 0;

        const receiptHtml = `
            <html>
            <head>
                <title>Print Receipt</title>
                <style>
                    @page { margin: 0; }
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        width: 58mm;
                        margin: 0;
                        padding: 10px;
                        box-sizing: border-box;
                        color: #000;
                        background: #fff;
                    }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .bold { font-weight: bold; }
                    .divider { border-top: 1px dashed #000; margin: 8px 0; }
                    .row { display: flex; justify-content: space-between; font-size: 12px; margin: 3px 0; }
                    h4 { margin: 0; font-size: 15px; font-weight: bold; }
                    p { margin: 2px 0; font-size: 11px; }
                </style>
            </head>
            <body>
                <div class="text-center" style="margin-bottom: 8px;">
                    ${!noPrice && res.logo ? `<img src="${res.logo}" alt="logo" style="max-height: 45px; max-width: 90px; object-fit: contain; margin-bottom: 6px; filter: grayscale(100%); display: inline-block;"><br>` : ''}
                    <h4 style="text-transform: uppercase; font-size: 14px; margin: 4px 0;">${noPrice ? 'KITCHEN CHECK' : (res.name || 'POS KASIR')}</h4>
                    ${!noPrice && res.location ? `<p style="font-size: 10px; margin: 2px 0 0 0; line-height: 1.2;">${res.location}</p>` : ''}
                </div>
                <div class="divider"></div>
                <div>
                    <p>Tanggal: ${moment(tx.paid_at || tx.created_at).format('DD/MM/YYYY HH:mm')}</p>
                    <p>Invoice: #${tx.invoice_number}</p>
                    <p>Meja   : ${tx.order_type === 'take_away' ? 'Take Away' : (tx.table ? tx.table.name : 'Dine In')}</p>
                    <p>Kasir  : ${cashier}</p>
                </div>
                <div class="divider"></div>
                <div>
                    ${itemsHtml}
                </div>
                <div class="divider"></div>
                ${!noPrice ? `
                <div>
                    <div class="row">
                        <span>Subtotal</span>
                        <span>Rp ${addCommas(tx.subtotal || total)}</span>
                    </div>
                    <div class="row">
                        <span>Pajak (10%)</span>
                        <span>Rp ${addCommas(tax)}</span>
                    </div>
                    ${discount > 0 ? `
                    <div class="row" style="color: red;">
                        <span>Diskon</span>
                        <span>-Rp ${addCommas(discount)}</span>
                    </div>
                    ` : ''}
                    <div class="divider"></div>
                    <div class="row bold" style="font-size: 13px;">
                        <span>TOTAL</span>
                        <span>Rp ${addCommas(total)}</span>
                    </div>
                    <div class="divider"></div>
                    <div class="row">
                        <span>Dibayar (${tx.paid_method || 'CASH'})</span>
                        <span>Rp ${addCommas(paid)}</span>
                    </div>
                    <div class="row">
                        <span>Kembalian</span>
                        <span>Rp ${addCommas(changed)}</span>
                    </div>
                </div>
                <div class="divider" style="border-top: 1px double #000;"></div>
                ` : ''}
                <div class="text-center" style="margin-top: 10px; font-size: 12px; font-weight: bold;">
                    <p>${noPrice ? 'Sajian Segera Disiapkan' : 'Terima Kasih'}</p>
                    ${!noPrice ? '<p>Atas Kunjungan Anda</p>' : ''}
                </div>
            </body>
            </html>
        `;

        // Create a hidden iframe
        let iframe = document.getElementById('bt-print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'bt-print-iframe';
            iframe.style.position = 'absolute';
            iframe.style.width = '0px';
            iframe.style.height = '0px';
            iframe.style.border = 'none';
            iframe.style.left = '-9999px';
            document.body.appendChild(iframe);
        }

        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(receiptHtml);
        doc.close();

        // Trigger printing
        setTimeout(() => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }, 250);
    }

    // Helper: addCommas formatting
    function addCommas(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

});
</script>
@endsection
