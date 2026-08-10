@extends('layout.index')

@section('title','Report')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">Activity <span class="text-base text-gray-400">> Reports</span></h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <!-- ApexCharts CDN dependency -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        @media print {
            /* Sembunyikan navbar, sidebar, panel filter, tombol, pencarian, dan elemen non-cetak lainnya */
            nav, 
            .col-span-8.md:col-span-2,
            .flex.flex-wrap.items-end.justify-between.gap-4.bg-white,
            .print\:hidden,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_paginate,
            #table-search,
            button,
            input,
            select,
            .print-button {
                display: none !important;
            }

            /* Atur ukuran halaman A4 dan margin cetak default */
            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }

            /* Reset latar belakang dan font body agar bersih saat dicetak */
            body {
                background-color: #ffffff !important;
                color: #111827 !important;
                font-family: 'Red Hat Text', sans-serif !important;
                font-size: 11px !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Hilangkan layout grid luar POS dan jadikan 100% lebar */
            .grid.grid-cols-8 {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                border: none !important;
                box-shadow: none !important;
            }

            .col-span-8.md:col-span-6 {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            #print-report {
                display: flex !important;
                flex-direction: column !important;
                gap: 20px !important;
                width: 100% !important;
                background: transparent !important;
            }

            /* Grid Ringkasan Metrik (Omzet, Laba, HPP) dipaksa sebaris 5 kolom */
            .grid.grid-cols-1.sm:grid-cols-2.lg:grid-cols-5 {
                display: grid !important;
                grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                gap: 12px !important;
                width: 100% !important;
            }

            .grid.grid-cols-1.sm:grid-cols-2.lg:grid-cols-5 > div {
                min-height: auto !important;
                padding: 12px !important;
                border: 1px solid #e5e7eb !important;
                border-left-width: 4px !important;
                box-shadow: none !important;
                border-radius: 12px !important;
                background-color: #ffffff !important;
            }

            .grid.grid-cols-1.sm:grid-cols-2.lg:grid-cols-5 > div span {
                font-size: 9px !important;
                font-weight: 700 !important;
                color: #6b7280 !important;
            }

            .grid.grid-cols-1.sm:grid-cols-2.lg:grid-cols-5 > div .text-xl {
                font-size: 13px !important;
                font-weight: 800 !important;
                margin-top: 6px !important;
                color: #111827 !important;
                display: block !important;
            }

            /* Hilangkan deskripsi bantuan metrik kecil agar struk laporan bersih */
            .grid.grid-cols-1.sm:grid-cols-2.lg:grid-cols-5 > div p {
                display: none !important;
            }

            /* Atur grid Ringkasan Tipe Pesanan & Metode Pembayaran berdampingan */
            .grid.grid-cols-1.md:grid-cols-2 {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 20px !important;
                width: 100% !important;
            }

            /* Kotak pembungkus tabel & detail */
            .bg-white.p-6.rounded-2xl {
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            /* Judul bagian cetak */
            h3.font-extrabold {
                font-size: 13px !important;
                font-weight: 850 !important;
                color: #111827 !important;
                margin-bottom: 2px !important;
            }

            h3.font-extrabold + p {
                font-size: 9px !important;
                color: #9ca3af !important;
                margin-bottom: 10px !important;
            }

            /* Tabel responsif dan border */
            .relative.overflow-x-auto {
                overflow: visible !important;
                border: 1px solid #e5e7eb !important;
                border-radius: 10px !important;
                background-color: #ffffff !important;
                width: 100% !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th {
                background-color: #f9fafb !important;
                color: #374151 !important;
                font-weight: 700 !important;
                font-size: 9px !important;
                padding: 8px 10px !important;
                text-transform: uppercase !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            td {
                padding: 8px 10px !important;
                font-size: 9px !important;
                color: #1f2937 !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Detail Kalkulasi Profitabilitas (Kiri-Kanan) */
            .grid-cols-1.md:grid-cols-2.gap-8 {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 30px !important;
                width: 100% !important;
            }

            .divide-y.divide-gray-100 {
                border-collapse: collapse !important;
            }

            .divide-y.divide-gray-100 tr {
                display: flex !important;
                justify-content: space-between !important;
                padding: 6px 0 !important;
                border-bottom: 1px solid #f3f4f6 !important;
            }

            .divide-y.divide-gray-100 tr td {
                padding: 0 !important;
                border: none !important;
                font-size: 10px !important;
            }

            .divide-y.divide-gray-100 border-l {
                border-left: none !important;
                padding-left: 0 !important;
            }

            /* Highlight Estimasi Laba Bersih */
            .bg-brand-soft.px-3.rounded-xl {
                background-color: #eff6ff !important;
                border: 1px solid #bfdbfe !important;
                padding: 8px 12px !important;
                border-radius: 8px !important;
            }

            /* Header Cetakan di paling atas */
            .print\:block {
                display: block !important;
                text-align: center !important;
                margin-bottom: 25px !important;
            }

            .print\:block h2 {
                font-size: 18px !important;
                font-weight: 900 !important;
                letter-spacing: 0.5px !important;
                color: #111827 !important;
                margin: 0 !important;
            }

            .print\:block p {
                font-size: 10px !important;
                color: #6b7280 !important;
                margin-top: 4px !important;
            }
        }
    </style>

    <div class="grid grid-cols-8 p-8 gap-5">
        <div class="col-span-8 md:col-span-2 bg-white rounded-base p-5 pt-10 border border-gray-100 shadow-sm self-start">
            <div class="w-full text-sm font-medium text-heading bg-neutral-primary-soft flex flex-col gap-1">
                <a href="{{ route('activity.index') }}" aria-current="true" class="block w-full px-4 py-4 text-base font-semibold cursor-pointer hover:bg-brand-soft hover:text-brand rounded-xl transition-all">
                    <i class="fas fa-clock mr-2"></i> Billing Queues
                </a>
                
                <a href="{{ route('activity.history') }}" class="block w-full px-4 py-4 text-base font-semibold cursor-pointer hover:bg-brand-soft hover:text-brand rounded-xl transition-all">
                    <i class="fas fa-history mr-2"></i> Order History
                </a>
                @can('admin')
                    <a href="{{ route('activity.report') }}" class="block w-full px-4 py-4 text-base font-bold bg-brand text-white rounded-xl transition-all shadow-md shadow-brand/20">
                        <i class="fas fa-chart-line mr-2"></i> Sales Report
                    </a>
                @endcan
            </div>
        </div>

        <div class="col-span-8 md:col-span-6 bg-slate-50/40 rounded-2xl p-6 min-h-[calc(100vh-100px)] border border-gray-100/50 shadow-xs flex flex-col gap-6">
            <!-- Filter Panel -->
            <div class="flex flex-wrap items-end justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
                <div class="flex flex-wrap items-end gap-4 w-full lg:w-auto">
                    <div class="flex flex-col gap-1.5 w-full sm:w-auto">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mode Laporan</label>
                        <select id="filter-mode" class="px-4 py-2.5 bg-neutral-primary rounded-xl border border-brand-medium text-sm font-semibold focus:ring-2 focus:ring-brand focus:outline-none cursor-pointer">
                            <option value="single">Laporan Harian (Harian)</option>
                            <option value="weekly">Laporan Mingguan (7 Hari)</option>
                            <option value="monthly">Laporan Bulanan (1 Bulan)</option>
                            <option value="range">Rentang Tanggal (Kustom)</option>
                        </select>
                    </div>
                    
                    <div class="flex flex-col gap-1.5 w-full sm:w-auto filter-date-single">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pilih Tanggal</label>
                        <input type="date" id="report-date-single" class="px-4 py-2 bg-neutral-primary rounded-xl border border-brand-medium text-sm font-semibold focus:ring-2 focus:ring-brand focus:outline-none cursor-pointer" value="{{ date('Y-m-d') }}" />
                    </div>

                    <div class="flex flex-col gap-1.5 w-full sm:w-auto filter-date-weekly hidden">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pilih Hari (Dalam Minggu)</label>
                        <input type="date" id="report-date-weekly" class="px-4 py-2 bg-neutral-primary rounded-xl border border-brand-medium text-sm font-semibold focus:ring-2 focus:ring-brand focus:outline-none cursor-pointer" value="{{ date('Y-m-d') }}" />
                    </div>

                    <div class="flex flex-col gap-1.5 w-full sm:w-auto filter-date-monthly hidden">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pilih Bulan</label>
                        <input type="month" id="report-date-monthly" class="px-4 py-2 bg-neutral-primary rounded-xl border border-brand-medium text-sm font-semibold focus:ring-2 focus:ring-brand focus:outline-none cursor-pointer" value="{{ date('Y-m') }}" />
                    </div>

                    <div class="flex flex-col gap-1.5 w-full sm:w-auto filter-date-range hidden">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Mulai</label>
                        <input type="date" id="report-date-start" class="px-4 py-2 bg-neutral-primary rounded-xl border border-brand-medium text-sm font-semibold focus:ring-2 focus:ring-brand focus:outline-none cursor-pointer" value="{{ date('Y-m-d') }}" />
                    </div>

                    <div class="flex flex-col gap-1.5 w-full sm:w-auto filter-date-range hidden">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Selesai</label>
                        <input type="date" id="report-date-end" class="px-4 py-2 bg-neutral-primary rounded-xl border border-brand-medium text-sm font-semibold focus:ring-2 focus:ring-brand focus:outline-none cursor-pointer" value="{{ date('Y-m-d') }}" />
                    </div>

                    <button type="button" id="btn-apply-filter" class="w-full sm:w-auto px-6 py-2.5 bg-brand hover:bg-brand-strong text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-brand/20 cursor-pointer flex items-center justify-center gap-2">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </div>
                
                <div class="flex gap-2.5 w-full lg:w-auto justify-end">
                    <button type="button" id="btn-export-excel" class="px-5 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                        <i class="fas fa-file-excel"></i> Export XLSX
                    </button>
                    <button type="button" class="px-5 py-2.5 bg-brand-softer hover:bg-brand-medium text-brand border border-brand-light font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-xs cursor-pointer print-button">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>

            <!-- Printable Area Container -->
            <div id="print-report" class="flex flex-col gap-6 w-full">
                <!-- Header Printing info (Hidden on screen) -->
                <div class="hidden print:block border-b-2 border-brand pb-4 mb-4">
                    <h2 class="text-2xl font-extrabold text-heading text-center">LAPORAN PENJUALAN POS KASIR</h2>
                    <p class="text-xs text-gray-500 text-center mt-1">Periode Tanggal: <span id="print-period-text">-</span></p>
                </div>

                <!-- Vibrant Summary Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                    <!-- Omzet Card -->
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-brand shadow-xs flex flex-col justify-between min-h-[110px] relative overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Omzet Bersih</span>
                            <span class="p-1.5 rounded-lg bg-brand-soft text-brand"><i class="fas fa-money-bill-wave text-sm"></i></span>
                        </div>
                        <div class="mt-4">
                            <span class="text-xl font-extrabold text-heading" id="summary-revenue">Rp 0</span>
                            <p class="text-[9px] text-gray-400 mt-1">Pendapatan bersih setelah diskon</p>
                        </div>
                    </div>
                    <!-- Laba Kotor Card -->
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-emerald-500 shadow-xs flex flex-col justify-between min-h-[110px] relative overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laba Kotor</span>
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600"><i class="fas fa-chart-line text-sm"></i></span>
                        </div>
                        <div class="mt-4">
                            <span class="text-xl font-extrabold text-heading" id="summary-gross-profit">Rp 0</span>
                            <p class="text-[9px] text-gray-400 mt-1">Pendapatan bersih dikurangi HPP</p>
                        </div>
                    </div>
                    <!-- Laba Bersih Card -->
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-amber-500 shadow-xs flex flex-col justify-between min-h-[110px] relative overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laba Bersih</span>
                            <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600"><i class="fas fa-wallet text-sm"></i></span>
                        </div>
                        <div class="mt-4">
                            <span class="text-xl font-extrabold text-heading" id="summary-net-profit">Rp 0</span>
                            <p class="text-[9px] text-gray-400 mt-1">Laba kotor dikurangi biaya operasional (50% HPP)</p>
                        </div>
                    </div>
                    <!-- HPP Card -->
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-red-500 shadow-xs flex flex-col justify-between min-h-[110px] relative overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">HPP (Modal)</span>
                            <span class="p-1.5 rounded-lg bg-red-50 text-red-600"><i class="fas fa-tags text-sm"></i></span>
                        </div>
                        <div class="mt-4">
                            <span class="text-xl font-extrabold text-heading" id="summary-cost-price">Rp 0</span>
                            <p class="text-[9px] text-gray-400 mt-1">Harga Pokok Pembelian menu</p>
                        </div>
                    </div>
                    <!-- Stats Card -->
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-purple-500 shadow-xs flex flex-col justify-between min-h-[110px] relative overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ringkasan Vol.</span>
                            <span class="p-1.5 rounded-lg bg-purple-50 text-purple-600"><i class="fas fa-receipt text-sm"></i></span>
                        </div>
                        <div class="mt-4 flex flex-col gap-1 text-[11px]">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Transaksi:</span>
                                <span class="font-bold text-heading" id="total-transactions">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Item Terjual:</span>
                                <span class="font-bold text-heading" id="total-items">0 item</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ApexCharts Dashboard Section (Hidden on Print) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print:hidden">
                    <!-- Chart 3: Line Chart Tren Penjualan -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs lg:col-span-2">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h4 class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">Grafik Tren Penjualan (Naik-Turun Omzet)</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">Visualisasi naik-turunnya volume penjualan berdasarkan waktu/tanggal</p>
                            </div>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100 flex items-center gap-1.5">
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                                </span>
                                <i class="fas fa-chart-line mr-0.5 text-xs"></i> Fluktuasi Penjualan
                            </span>
                        </div>
                        <div id="sales-trend-line-chart" class="w-full min-h-[300px]"></div>
                    </div>

                    <!-- Chart 1: Revenue vs Profit Breakdown -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">Perbandingan Keuangan & Profitabilitas</h4>
                            <span class="text-[10px] font-semibold text-brand bg-brand-soft px-2 py-1 rounded-md"><i class="fas fa-chart-bar mr-1"></i> Keuangan</span>
                        </div>
                        <div id="financial-trend-chart" class="w-full"></div>
                    </div>
                    <!-- Chart 2: Category distribution -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">Porsi Penjualan Kategori Menu</h4>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md"><i class="fas fa-chart-pie mr-1"></i> Kategori</span>
                        </div>
                        <div id="category-distribution-chart" class="w-full"></div>
                    </div>
                </div>

                <!-- Sales By Menus -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-heading">Sales By Menus</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Rincian penjualan masing-masing menu makanan dan minuman</p>
                        </div>
                        <div class="w-full sm:w-auto relative print:hidden">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </div>
                            <input type="text" id="table-search" placeholder="Cari Nama Menu..." class="w-full sm:w-64 ps-9 pe-4 py-2 bg-neutral-primary border border-brand-medium rounded-xl text-xs font-semibold focus:ring-2 focus:ring-brand focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-2xs rounded-xl border border-default">
                        <table class="w-full text-sm text-left rtl:text-right text-body">
                            <thead class="text-xs text-gray-500 uppercase bg-neutral-secondary-soft border-b border-default">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold">Kategori</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Product Name</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Qty</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Harga Jual</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Estimasi HPP</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Total HPP</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Total Penjualan</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Keuntungan Bersih</th>
                                </tr>
                            </thead>
                            <tbody id="sales-by-menu" class="divide-y divide-default">
                                <!-- Ajax loads here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Lower Breakdowns: Order Type & Payment Method Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Order Type Breakdown -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs flex flex-col">
                        <div class="mb-4">
                            <h3 class="font-extrabold text-base text-heading">Ringkasan Tipe Pesanan</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Proporsi pesanan Dine In vs Take Away</p>
                        </div>
                        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-2xs rounded-xl border border-default flex-1">
                            <table class="w-full text-sm text-left rtl:text-right text-body">
                                <thead class="text-xs text-gray-500 uppercase bg-neutral-secondary-soft border-b border-default">
                                    <tr>
                                        <th scope="col" class="px-6 py-3.5 font-bold">Tipe Pesanan</th>
                                        <th scope="col" class="px-6 py-3.5 font-bold text-center">Transaksi</th>
                                        <th scope="col" class="px-6 py-3.5 font-bold">Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody id="order-type-summary" class="divide-y divide-default">
                                    <!-- Ajax loads here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Method Breakdown -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs flex flex-col">
                        <div class="mb-4">
                            <h3 class="font-extrabold text-base text-heading">Metode Pembayaran</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Summary transaksi berdasarkan metode pembayaran</p>
                        </div>
                        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-2xs rounded-xl border border-default flex-1">
                            <table class="w-full text-sm text-left rtl:text-right text-body">
                                <thead class="text-xs text-gray-500 uppercase bg-neutral-secondary-soft border-b border-default">
                                    <tr>
                                        <th scope="col" class="px-6 py-3.5 font-bold">Metode Pembayaran</th>
                                        <th scope="col" class="px-6 py-3.5 font-bold text-center">Transaksi</th>
                                        <th scope="col" class="px-6 py-3.5 font-bold">Total Setoran</th>
                                    </tr>
                                </thead>
                                <tbody id="payment-method-summary" class="divide-y divide-default">
                                    <!-- Ajax loads here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detailed Tax, Discount, Cost Breakdowns -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
                    <div class="mb-4">
                        <h3 class="font-extrabold text-base text-heading">Profitabilitas & Pengeluaran</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Detail kalkulasi laba kotor, pajak, diskon, dan perkiraan laba bersih</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <table class="w-full text-sm divide-y divide-gray-100">
                            <tr class="py-3 flex justify-between">
                                <td class="font-medium text-gray-500">Total Omzet Pendapatan</td>
                                <td class="font-bold text-heading" id="detail-revenue">Rp 0</td>
                            </tr>
                            <tr class="py-3 flex justify-between">
                                <td class="font-medium text-gray-500">Total Potongan Diskon</td>
                                <td class="font-bold text-red-500" id="summary-discount">Rp 0</td>
                            </tr>
                            <tr class="py-3 flex justify-between">
                                <td class="font-medium text-gray-500">Total Pungutan Pajak</td>
                                <td class="font-bold text-blue-500" id="summary-tax">Rp 0</td>
                            </tr>
                            <tr class="py-3 flex justify-between border-t border-gray-100 pt-3">
                                <td class="font-bold text-heading">Total Penerimaan Riil</td>
                                <td class="font-extrabold text-brand" id="detail-actual-received">Rp 0</td>
                            </tr>
                        </table>
                        
                        <table class="w-full text-sm divide-y divide-gray-100 border-l border-gray-100 pl-0 md:pl-8">
                            <tr class="py-3 flex justify-between">
                                <td class="font-medium text-gray-500">Total HPP (Modal Menu)</td>
                                <td class="font-bold text-heading" id="detail-cost-price">Rp 0</td>
                            </tr>
                            <tr class="py-3 flex justify-between">
                                <td class="font-medium text-gray-500">Laba Kotor (Omzet - HPP)</td>
                                <td class="font-extrabold text-emerald-600" id="summary-gross-profit-2">Rp 0</td>
                            </tr>
                            <tr class="py-3 flex justify-between text-xs text-gray-400">
                                <td class="font-medium">Biaya Operasional (50% HPP)</td>
                                <td class="font-bold" id="summary-operational-cost">Rp 0</td>
                            </tr>
                            <tr class="py-3 flex justify-between border-t border-brand pt-3 bg-brand-soft px-3 rounded-xl">
                                <td class="font-bold text-brand">Estimasi Laba Bersih</td>
                                <td class="font-extrabold text-brand" id="summary-net-profit-2">Rp 0</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- NEW: Detailed Transaction Logs Table -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs print:hidden">
                    <div class="mb-4">
                        <h3 class="font-extrabold text-lg text-heading">Log Riwayat Transaksi Detail</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Daftar transaksi pembayaran terperinci dalam periode terpilih</p>
                    </div>
                    
                    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-2xs rounded-xl border border-default">
                        <table class="w-full text-sm text-left rtl:text-right text-body">
                            <thead class="text-xs text-gray-500 uppercase bg-neutral-secondary-soft border-b border-default">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold">No. Invoice</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Waktu Bayar</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Pelanggan</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Meja</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Diskon</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Pajak</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Total Pembayaran</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Metode</th>
                                </tr>
                            </thead>
                            <tbody id="transaction-log-rows" class="divide-y divide-default">
                                <!-- Dynamic Transaction Rows load here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        let financialChart = null;
        let categoryChart = null;
        let salesLineChart = null;

        // Initialize empty state helper
        function renderEmptyCharts() {
            if (financialChart) financialChart.destroy();
            if (categoryChart) categoryChart.destroy();
            if (salesLineChart) salesLineChart.destroy();

            // Sales Trend Line Chart (Premium Spline Area Chart)
            const lineOptions = {
                chart: { 
                    type: 'area', 
                    height: 280, 
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Red Hat Text, sans-serif'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: { 
                    curve: 'smooth', 
                    width: 3 
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                series: [{ name: 'Penjualan', data: [] }],
                colors: ['#6366f1'], // Premium Indigo
                xaxis: { 
                    type: 'category', // Force categorical behavior to prevent timeline shifting
                    categories: [],
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 500
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { 
                    labels: { 
                        formatter: val => 'Rp ' + Math.round(val).toLocaleString('id-ID'),
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px'
                        }
                    }
                },
                tooltip: { 
                    shared: true,
                    intersect: false,
                    y: { 
                        formatter: val => 'Rp ' + Math.round(val).toLocaleString('id-ID') 
                    },
                    theme: 'light'
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                markers: {
                    size: 4,
                    colors: ['#6366f1'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: { size: 6 }
                }
            };
            salesLineChart = new ApexCharts(document.querySelector("#sales-trend-line-chart"), lineOptions);
            salesLineChart.render();

            // Financial chart
            const financialOptions = {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: 'Jumlah', data: [0, 0, 0, 0] }],
                colors: ['#2b66ff', '#10b981', '#f59e0b', '#ef4444'],
                plotOptions: { bar: { distributed: true, borderRadius: 6, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: ['Omzet', 'Laba Kotor', 'Laba Bersih', 'HPP'] },
                legend: { show: false }
            };
            financialChart = new ApexCharts(document.querySelector("#financial-trend-chart"), financialOptions);
            financialChart.render();

            // Category chart
            const categoryOptions = {
                chart: { type: 'donut', height: 280 },
                series: [1],
                labels: ['Belum Ada Data'],
                colors: ['#cbd5e1'],
                dataLabels: { enabled: false },
                legend: { position: 'bottom' }
            };
            categoryChart = new ApexCharts(document.querySelector("#category-distribution-chart"), categoryOptions);
            categoryChart.render();
        }

        // Initialize empty charts immediately on page load
        renderEmptyCharts();

        // Single vs Range Toggle
        $('#filter-mode').on('change', function() {
            const mode = $(this).val();
            if (mode === 'single') {
                $('.filter-date-single').removeClass('hidden');
                $('.filter-date-weekly').addClass('hidden');
                $('.filter-date-monthly').addClass('hidden');
                $('.filter-date-range').addClass('hidden');
            } else if (mode === 'weekly') {
                $('.filter-date-single').addClass('hidden');
                $('.filter-date-weekly').removeClass('hidden');
                $('.filter-date-monthly').addClass('hidden');
                $('.filter-date-range').addClass('hidden');
            } else if (mode === 'monthly') {
                $('.filter-date-single').addClass('hidden');
                $('.filter-date-weekly').addClass('hidden');
                $('.filter-date-monthly').removeClass('hidden');
                $('.filter-date-range').addClass('hidden');
            } else {
                $('.filter-date-single').addClass('hidden');
                $('.filter-date-weekly').addClass('hidden');
                $('.filter-date-monthly').addClass('hidden');
                $('.filter-date-range').removeClass('hidden');
            }
        });

        // Trigger loading and data fetch
        function loadReportData() {
            const mode = $('#filter-mode').val();
            let startDate = '';
            let endDate = '';

            if (mode === 'single') {
                startDate = $('#report-date-single').val();
                endDate = startDate;
            } else if (mode === 'weekly') {
                const selectedDate = $('#report-date-weekly').val();
                if (selectedDate) {
                    startDate = moment(selectedDate).startOf('isoWeek').format('YYYY-MM-DD');
                    endDate = moment(selectedDate).endOf('isoWeek').format('YYYY-MM-DD');
                }
            } else if (mode === 'monthly') {
                const selectedMonth = $('#report-date-monthly').val();
                if (selectedMonth) {
                    startDate = moment(selectedMonth + '-01').startOf('month').format('YYYY-MM-DD');
                    endDate = moment(selectedMonth + '-01').endOf('month').format('YYYY-MM-DD');
                }
            } else {
                startDate = $('#report-date-start').val();
                endDate = $('#report-date-end').val();
            }

            if (!startDate || !endDate) {
                oAlert("red", "Warning", "Tanggal laporan belum dipilih sepenuhnya");
                return;
            }

            // Empty old tables
            $('#sales-by-menu').html('');
            $('#order-type-summary').html('');
            $('#payment-method-summary').html('');
            $('#transaction-log-rows').html('');

            loading();

            let baseRoute = "{{ route('activity.report.show', ':date') }}";
            let url = baseRoute.replace(':date', startDate) + `?start_date=${startDate}&end_date=${endDate}`;

            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    removeLoading();
                    const summary = data.summary;
                    const rawTransactions = data.transaction;

                    // Update Title Period for printing
                    const periodStr = startDate === endDate 
                        ? moment(startDate).format('dddd, D MMMM YYYY')
                        : moment(startDate).format('D MMMM YYYY') + ' - ' + moment(endDate).format('D MMMM YYYY');
                    $('#print-period-text').text(periodStr);

                    // Update Top Stats Grid
                    $('#summary-revenue').text('Rp ' + summary.total_revenue.toLocaleString('id-ID'));
                    $('#summary-gross-profit').text('Rp ' + summary.laba_kotor.toLocaleString('id-ID'));
                    $('#summary-net-profit').text('Rp ' + summary.laba_bersih.toLocaleString('id-ID'));
                    $('#summary-cost-price').text('Rp ' + summary.total_cost_price.toLocaleString('id-ID'));
                    $('#total-transactions').text(summary.total_transactions);
                    $('#total-items').text(summary.total_items + ' item');

                    // Update Lower Summary
                    $('#detail-revenue').text('Rp ' + summary.total_revenue.toLocaleString('id-ID'));
                    $('#summary-discount').text('Rp ' + summary.total_discount.toLocaleString('id-ID'));
                    $('#summary-tax').text('Rp ' + summary.total_tax.toLocaleString('id-ID'));
                    
                    const riil = summary.total_revenue - summary.total_discount + summary.total_tax;
                    $('#detail-actual-received').text('Rp ' + riil.toLocaleString('id-ID'));

                    $('#detail-cost-price').text('Rp ' + summary.total_cost_price.toLocaleString('id-ID'));
                    $('#summary-gross-profit-2').text('Rp ' + summary.laba_kotor.toLocaleString('id-ID'));
                    $('#summary-operational-cost').text('Rp ' + summary.operasional_cost.toLocaleString('id-ID'));
                    $('#summary-net-profit-2').text('Rp ' + summary.laba_bersih.toLocaleString('id-ID'));

                    // Rincian Menu (Sales By Menus)
                    let menuRows = '';
                    if (Object.keys(summary.items).length === 0) {
                        menuRows = `<tr><td colspan="8" class="px-6 py-6 text-center text-gray-400 font-semibold">Tidak ada data penjualan menu pada tanggal ini.</td></tr>`;
                    } else {
                        Object.entries(summary.items).forEach(([key, elem]) => {
                            menuRows += `
                            <tr class="bg-neutral-primary border-b border-default hover:bg-neutral-secondary-soft menu-row-item">
                                <td class="px-6 py-4 font-semibold text-gray-500">${elem.category_name}</td>
                                <td class="px-6 py-4 font-bold text-heading menu-name-field">${elem.name}</td>
                                <td class="px-6 py-4 text-center font-bold text-heading">${elem.qty}</td>
                                <td class="px-6 py-4 font-medium text-gray-500">Rp ${elem.price.toLocaleString('id-ID')}</td>
                                <td class="px-6 py-4 text-xs text-gray-400">Rp ${elem.cost_price.toLocaleString('id-ID')}</td>
                                <td class="px-6 py-4 font-medium text-gray-500">Rp ${elem.cost_price_total.toLocaleString('id-ID')}</td>
                                <td class="px-6 py-4 font-bold text-heading">Rp ${elem.total.toLocaleString('id-ID')}</td>
                                <td class="px-6 py-4 font-bold text-emerald-600">Rp ${elem.profit.toLocaleString('id-ID')}</td>
                            </tr>`;
                        });
                    }
                    $('#sales-by-menu').html(menuRows);

                    // Order Types Breakdown
                    let orderTypeRows = '';
                    if (Object.keys(summary.order_types).length === 0) {
                        orderTypeRows = `<tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">Belum ada transaksi</td></tr>`;
                    } else {
                        Object.entries(summary.order_types).forEach(([key, elem]) => {
                            orderTypeRows += `
                            <tr class="bg-neutral-primary border-b border-default hover:bg-neutral-secondary-soft">
                                <td class="px-6 py-3.5 font-semibold text-heading">${key}</td>
                                <td class="px-6 py-3.5 text-center font-bold text-heading">${elem.total_transaction}</td>
                                <td class="px-6 py-3.5 font-bold text-brand">Rp ${elem.total.toLocaleString('id-ID')}</td>
                            </tr>`;
                        });
                    }
                    $('#order-type-summary').html(orderTypeRows);

                    // Payment Methods Breakdown
                    let paymentRows = '';
                    if (Object.keys(summary.payment_methods).length === 0) {
                        paymentRows = `<tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">Belum ada transaksi</td></tr>`;
                    } else {
                        Object.entries(summary.payment_methods).forEach(([key, elem]) => {
                            paymentRows += `
                            <tr class="bg-neutral-primary border-b border-default hover:bg-neutral-secondary-soft">
                                <td class="px-6 py-3.5 font-semibold text-heading">${key || 'CASH'}</td>
                                <td class="px-6 py-3.5 text-center font-bold text-heading">${elem.total_transaction}</td>
                                <td class="px-6 py-3.5 font-bold text-brand">Rp ${elem.total.toLocaleString('id-ID')}</td>
                            </tr>`;
                        });
                    }
                    $('#payment-method-summary').html(paymentRows);

                    // Detailed Transaction Logs Table
                    let logRows = '';
                    if (rawTransactions.length === 0) {
                        logRows = `<tr><td colspan="8" class="px-6 py-6 text-center text-gray-400 font-semibold">Tidak ada riwayat invoice ditemukan.</td></tr>`;
                    } else {
                        rawTransactions.forEach(tx => {
                            const timeStr = moment(tx.paid_at).format('D MMM YYYY, HH:mm');
                            const tableName = tx.order_type === 'take_away' ? 'Take Away' : (tx.table ? tx.table.name : 'Dine In');
                            const discStr = tx.discount > 0 ? `Rp ${tx.discount.toLocaleString('id-ID')}` : '-';
                            const taxStr = tx.tax > 0 ? `Rp ${tx.tax.toLocaleString('id-ID')}` : '-';
                            
                            logRows += `
                            <tr class="bg-neutral-primary border-b border-default hover:bg-neutral-secondary-soft text-xs">
                                <td class="px-6 py-3.5 font-bold text-heading whitespace-nowrap">${tx.invoice_number}</td>
                                <td class="px-6 py-3.5 text-gray-400">${timeStr}</td>
                                <td class="px-6 py-3.5 font-medium text-heading">${tx.customer_name || '-'}</td>
                                <td class="px-6 py-3.5 text-center font-bold text-heading">${tableName}</td>
                                <td class="px-6 py-3.5 text-red-500">${discStr}</td>
                                <td class="px-6 py-3.5 text-blue-500">${taxStr}</td>
                                <td class="px-6 py-3.5 font-bold text-heading">Rp ${tx.total.toLocaleString('id-ID')}</td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-md bg-brand-soft text-brand-light">${tx.paid_method || 'CASH'}</span>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#transaction-log-rows').html(logRows);

                    // Line chart aggregation
                    let labels = [];
                    let dataValues = [];

                    if (startDate === endDate) {
                        // HOURLY TREND
                        // Create 24 hours map: '00:00', '01:00', ... '23:00'
                        const hourlyData = {};
                        for (let h = 0; h < 24; h++) {
                            const hrStr = String(h).padStart(2, '0') + ':00';
                            hourlyData[hrStr] = 0;
                        }

                        // Aggregate transactions
                        rawTransactions.forEach(tx => {
                            if (tx.paid_at) {
                                const hourKey = moment(tx.paid_at).format('HH:00');
                                if (hourlyData[hourKey] !== undefined) {
                                    hourlyData[hourKey] += parseFloat(tx.total || 0);
                                }
                            }
                        });

                        labels = Object.keys(hourlyData);
                        dataValues = Object.values(hourlyData);
                    } else {
                        // DAILY TREND
                        // Iterate from startDate to endDate using moment
                        const dailyData = {};
                        let current = moment(startDate);
                        const end = moment(endDate);

                        while (current.isSameOrBefore(end, 'day')) {
                            const dateStr = current.format('YYYY-MM-DD');
                            dailyData[dateStr] = 0;
                            current.add(1, 'day');
                        }

                        // Aggregate transactions
                        rawTransactions.forEach(tx => {
                            if (tx.paid_at) {
                                const dateKey = moment(tx.paid_at).format('YYYY-MM-DD');
                                if (dailyData[dateKey] !== undefined) {
                                    dailyData[dateKey] += parseFloat(tx.total || 0);
                                }
                            }
                        });

                        // For daily labels, format them to a nice readable form based on the selected mode
                        if (mode === 'weekly') {
                            const dayNameMap = {
                                'Monday': 'Senin',
                                'Tuesday': 'Selasa',
                                'Wednesday': 'Rabu',
                                'Thursday': 'Kamis',
                                'Friday': 'Jumat',
                                'Saturday': 'Sabtu',
                                'Sunday': 'Minggu'
                            };
                            labels = Object.keys(dailyData).map(d => {
                                const engDay = moment(d).format('dddd');
                                const indoDay = dayNameMap[engDay] || engDay;
                                return indoDay + ' (' + moment(d).format('DD MMM') + ')';
                            });
                        } else if (mode === 'monthly') {
                            // Monthly: Just the day number to prevent crowded X-axis labels on mobile
                            labels = Object.keys(dailyData).map(d => moment(d).format('DD'));
                        } else {
                            labels = Object.keys(dailyData).map(d => moment(d).format('D MMM'));
                        }
                        dataValues = Object.values(dailyData);
                    }

                    // Update Charts with new dynamic datasets!
                    // Using a single updateOptions call guarantees that the Y-axis auto-scales and formats properly without bugging out.
                    if (salesLineChart) {
                        salesLineChart.updateOptions({
                            series: [{
                                name: 'Total Penjualan',
                                data: dataValues
                            }],
                            xaxis: {
                                type: 'category', // Force categorical behavior to prevent timeline shifting on update
                                categories: labels
                            }
                        });
                    }

                    if (financialChart) {
                        financialChart.updateSeries([{
                            name: 'Jumlah',
                            data: [summary.total_revenue, summary.laba_kotor, summary.laba_bersih, summary.total_cost_price]
                        }]);
                    }

                    if (categoryChart) {
                        const catLabels = [];
                        const catValues = [];
                        const catColors = [];

                        Object.values(summary.categories).forEach(c => {
                            catLabels.push(c.name);
                            catValues.push(c.qty);
                            
                            // Extract base color name from Tailwind class (e.g. 'bg-red-500' -> 'red')
                            const baseColor = (c.color || 'blue').replace('bg-', '').split('-')[0].toLowerCase();
                            
                            // Map all standard base colors to premium hex codes
                            const colorMap = {
                                'red': '#ef4444', 
                                'orange': '#f97316',
                                'amber': '#f59e0b',
                                'yellow': '#ffd000', 
                                'lime': '#84cc16', 
                                'green': '#22c55e', 
                                'emerald': '#10b981', 
                                'teal': '#14b8a6',
                                'cyan': '#06b6d4',
                                'sky': '#0ea5e9', 
                                'blue': '#2b66ff', 
                                'indigo': '#6366f1', 
                                'violet': '#8b5cf6', 
                                'purple': '#a855f7',
                                'fuchsia': '#d946ef',
                                'pink': '#ec4899', 
                                'rose': '#f43f5e',
                                'gray': '#64748b',
                                'slate': '#64748b',
                                'stone': '#78716c',
                                'neutral': '#737373',
                                'zinc': '#71717a'
                            };
                            catColors.push(colorMap[baseColor] || '#3b82f6');
                        });

                        if (catValues.length === 0) {
                            categoryChart.updateOptions({
                                series: [1],
                                labels: ['Belum Ada Penjualan'],
                                colors: ['#cbd5e1']
                            });
                        } else {
                            categoryChart.updateOptions({
                                series: catValues,
                                labels: catLabels,
                                colors: catColors
                            });
                        }
                    }
                },
                error: function(xhr) {
                    removeLoading();
                    oAlert("red", "Error", "Gagal memuat laporan data dari server");
                    console.log(xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText);
                }
            });
        }

        // Apply filter click
        $('#btn-apply-filter').on('click', function() {
            loadReportData();
        });

        // Search within Menu Table (Client side live filter)
        $('#table-search').on('keyup', function() {
            const query = $(this).val().toLowerCase();
            $('.menu-row-item').each(function() {
                const text = $(this).find('.menu-name-field').text().toLowerCase();
                if (text.indexOf(query) > -1) {
                    $(this).removeClass('hidden');
                } else {
                    $(this).addClass('hidden');
                }
            });
        });

        // Backend XLSX exporter
        $('#btn-export-excel').on('click', function() {
            const rows = document.querySelectorAll("#sales-by-menu tr");
            if (rows.length === 0 || rows[0].innerText.includes("Tidak ada data")) {
                oAlert("red", "Warning", "Tidak ada data penjualan untuk diekspor");
                return;
            }

            const mode = $('#filter-mode').val();
            let startDate = '';
            let endDate = '';

            if (mode === 'single') {
                startDate = $('#report-date-single').val();
                endDate = startDate;
            } else if (mode === 'weekly') {
                const selectedDate = $('#report-date-weekly').val();
                if (selectedDate) {
                    startDate = moment(selectedDate).startOf('isoWeek').format('YYYY-MM-DD');
                    endDate = moment(selectedDate).endOf('isoWeek').format('YYYY-MM-DD');
                }
            } else if (mode === 'monthly') {
                const selectedMonth = $('#report-date-monthly').val();
                if (selectedMonth) {
                    startDate = moment(selectedMonth + '-01').startOf('month').format('YYYY-MM-DD');
                    endDate = moment(selectedMonth + '-01').endOf('month').format('YYYY-MM-DD');
                }
            } else {
                startDate = $('#report-date-start').val();
                endDate = $('#report-date-end').val();
            }

            if (!startDate || !endDate) {
                oAlert("red", "Warning", "Tanggal laporan belum dipilih sepenuhnya");
                return;
            }

            let url = "{{ route('activity.report.export') }}";
            url += `?mode=${mode}&start_date=${startDate}&end_date=${endDate}`;

            window.location.href = url;
        });

        // Print function
        $('.print-button').on('click', function() {
            window.print();
        });

        // Trigger load initially for today
        loadReportData();
    </script>
@endsection