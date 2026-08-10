@extends('layout.index')

@section('title','Activity')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">Activity <span class="text-base text-gray-400">> Billing Queues</span></h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="grid grid-cols-8 p-8 gap-5">
        <!-- Sidebar Navigation -->
        <div class="col-span-8 md:col-span-2 bg-white rounded-base p-5 pt-10 border border-gray-100 shadow-sm self-start">
            <div class="w-full text-sm font-medium text-heading bg-neutral-primary-soft flex flex-col gap-1">
                <a href="{{ route('activity.index') }}" aria-current="true" class="block w-full px-4 py-4 text-base font-bold bg-brand text-white rounded-xl transition-all shadow-md shadow-brand/20">
                    <i class="fas fa-clock mr-2"></i> Billing Queues
                </a>
                
                <a href="{{ route('activity.history') }}" class="block w-full px-4 py-4 text-base font-semibold cursor-pointer hover:bg-brand-soft hover:text-brand rounded-xl transition-all">
                    <i class="fas fa-history mr-2"></i> Order History
                </a>
                @can('admin')
                    <a href="{{ route('activity.report') }}" class="block w-full px-4 py-4 text-base font-semibold cursor-pointer hover:bg-brand-soft hover:text-brand rounded-xl transition-all">
                        <i class="fas fa-chart-line mr-2"></i> Sales Report
                    </a>
                @endcan
            </div>
        </div>

        <div class="col-span-8 md:col-span-6 bg-slate-50/40 rounded-2xl min-h-[calc(100vh-100px)] border border-gray-100/50 shadow-xs flex flex-col gap-6">
            <!-- Modern Tabs Navigation -->
            <div class="border-b border-default">
                <ul class="flex flex-wrap -mb-px text-sm font-bold text-center gap-2" id="bill-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="all-tab" data-tabs-target="#all" type="button" role="tab" aria-selected="false">Semua Antrean</button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="active-tab" data-tabs-target="#active" type="button" role="tab" aria-selected="false">Aktif</button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="process-tab" data-tabs-target="#process" type="button" role="tab" aria-selected="false">Proses Masak</button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="payment-tab" data-tabs-target="#payment" type="button" role="tab" aria-selected="false">Pembayaran</button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base text-gray-500 hover:text-brand hover:border-brand cursor-pointer transition-all focus:outline-none" id="paid-tab" data-tabs-target="#paid" type="button" role="tab" aria-selected="false">Lunas (Paid)</button>
                    </li>
                </ul>
            </div>

            <!-- Tab Contents -->
            <div id="default-tab-content" class="flex-1">
                
                <!-- ALL TAB -->
                <div class="hidden p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs" id="all" role="tabpanel">
                    @if($transactions->count() === 0)
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <span class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4"><i class="fas fa-folder-open text-2xl"></i></span>
                            <h4 class="font-bold text-gray-700 text-sm">Tidak Ada Antrean</h4>
                            <p class="text-xs text-gray-400 mt-1">Saat ini belum ada transaksi kasir yang berjalan.</p>
                        </div>
                    @else
                        <ul role="list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($transactions as $transaction)
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
                                <li class="p-5 bg-white border border-gray-100 hover:border-brand-medium rounded-2xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                    <div class="flex items-center gap-4 justify-between flex-wrap sm:flex-nowrap">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            @if ($transaction->order_type == 'take_away')
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                    <i class="fas fa-bag-shopping text-xl"></i>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                    <i class="fas fa-utensils text-xl"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-extrabold text-heading text-base truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                    <span class="status text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $class_color }} border">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md">
                                                        <i class="fas fa-table mr-1"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                    </span>
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <button type="button" class="w-10 h-10 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-xl shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                                <i class="fas fa-eye text-base"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- ACTIVE TAB -->
                <div class="hidden p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs" id="active" role="tabpanel">
                    @php
                        $transactions_active = $transactions->filter(fn($e) => $e->status == 'active');
                    @endphp
                    @if($transactions_active->count() === 0)
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <span class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4"><i class="fas fa-check-double text-2xl"></i></span>
                            <h4 class="font-bold text-gray-700 text-sm">Tidak Ada Antrean Aktif</h4>
                            <p class="text-xs text-gray-400 mt-1">Semua pesanan aktif kasir telah diproses.</p>
                        </div>
                    @else
                        <ul role="list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($transactions_active as $transaction)
                                <li class="p-5 bg-white border border-gray-100 hover:border-brand-medium rounded-2xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                    <div class="flex items-center gap-4 justify-between flex-wrap sm:flex-nowrap">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            @if ($transaction->order_type == 'take_away')
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                    <i class="fas fa-bag-shopping text-xl"></i>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                    <i class="fas fa-utensils text-xl"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-extrabold text-heading text-base truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                    <span class="status text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border-emerald-200 border">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md">
                                                        <i class="fas fa-table mr-1"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                    </span>
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <button type="button" class="w-10 h-10 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-xl shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                                <i class="fas fa-eye text-base"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- PROCESS TAB -->
                <div class="hidden p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs" id="process" role="tabpanel">
                    @php
                        $transactions_process = $transactions->filter(fn($e) => $e->status == 'process');
                    @endphp
                    @if($transactions_process->count() === 0)
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <span class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4"><i class="fas fa-fire-burner text-2xl"></i></span>
                            <h4 class="font-bold text-gray-700 text-sm">Tidak Ada Proses Masak</h4>
                            <p class="text-xs text-gray-400 mt-1">Saat ini dapur tidak sedang memasak antrean pesanan.</p>
                        </div>
                    @else
                        <ul role="list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($transactions_process as $transaction)
                                <li class="p-5 bg-white border border-gray-100 hover:border-brand-medium rounded-2xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                    <div class="flex items-center gap-4 justify-between flex-wrap sm:flex-nowrap">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            @if ($transaction->order_type == 'take_away')
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                    <i class="fas fa-bag-shopping text-xl"></i>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                    <i class="fas fa-utensils text-xl"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-extrabold text-heading text-base truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                    <span class="status text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-brand-soft text-brand border-brand-softer border">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md">
                                                        <i class="fas fa-table mr-1"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                    </span>
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <button type="button" class="w-10 h-10 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-xl shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                                <i class="fas fa-eye text-base"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- PAYMENT TAB -->
                <div class="hidden p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs" id="payment" role="tabpanel">
                    @php
                        $transactions_payment = $transactions->filter(fn($e) => $e->status == 'payment');
                    @endphp
                    @if($transactions_payment->count() === 0)
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <span class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4"><i class="fas fa-wallet text-2xl"></i></span>
                            <h4 class="font-bold text-gray-700 text-sm">Tidak Ada Tagihan Pembayaran</h4>
                            <p class="text-xs text-gray-400 mt-1">Saat ini kasir bersih dari antrean tagihan pembayaran.</p>
                        </div>
                    @else
                        <ul role="list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($transactions_payment as $transaction)
                                <li class="p-5 bg-white border border-gray-100 hover:border-brand-medium rounded-2xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                    <div class="flex items-center gap-4 justify-between flex-wrap sm:flex-nowrap">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            @if ($transaction->order_type == 'take_away')
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                    <i class="fas fa-bag-shopping text-xl"></i>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                    <i class="fas fa-utensils text-xl"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-extrabold text-heading text-base truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                    <span class="status text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border-amber-200 border">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md">
                                                        <i class="fas fa-table mr-1"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                    </span>
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <button type="button" class="w-10 h-10 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-xl shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                                <i class="fas fa-eye text-base"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- PAID TAB -->
                <div class="hidden p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs" id="paid" role="tabpanel">
                    @php
                        $transactions_paid = $transactions->filter(fn($e) => $e->status == 'paid');
                    @endphp
                    @if($transactions_paid->count() === 0)
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <span class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4"><i class="fas fa-circle-check text-2xl"></i></span>
                            <h4 class="font-bold text-gray-700 text-sm">Belum Ada Transaksi Lunas</h4>
                            <p class="text-xs text-gray-400 mt-1">Antrean kasir lunas akan tercantum di sini.</p>
                        </div>
                    @else
                        <ul role="list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($transactions_paid as $transaction)
                                <li class="p-5 bg-white border border-gray-100 hover:border-brand-medium rounded-2xl shadow-2xs hover:shadow-xs transition-all duration-300 relative group" data-uuid="{{ $transaction->uuid }}" data-status="{{ $transaction->status }}">
                                    <div class="flex items-center gap-4 justify-between flex-wrap sm:flex-nowrap">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            @if ($transaction->order_type == 'take_away')
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                                                    <i class="fas fa-bag-shopping text-xl"></i>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-brand-soft text-brand border border-brand-softer shrink-0">
                                                    <i class="fas fa-utensils text-xl"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="font-extrabold text-heading text-base truncate">{{ $transaction->customer_name ?? 'Guest' }}</p>
                                                    <span class="status text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 border-red-200 border">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gray-700">#{{ $transaction->invoice_number }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="font-semibold text-brand bg-brand-soft px-1.5 py-0.5 rounded-md">
                                                        <i class="fas fa-table mr-1"></i> {{ $transaction->order_type == 'take_away' ? 'Take Away' : ($transaction->table ? $transaction->table->name : 'Dine In') }}
                                                    </span>
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="far fa-clock"></i> {{ date('d M Y, H:i', strtotime($transaction->created_at)) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <button type="button" class="w-10 h-10 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-xl shadow-2xs hover:shadow-sm cursor-pointer transition-all duration-300 flex items-center justify-center see-transaction" title="Lihat Struk Detail">
                                                <i class="fas fa-eye text-base"></i>
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

    <script type="module">
        // options with default values
        const options = {
            placement: "center",
            backdrop: "dynamic",
            backdropClasses: "bg-gray-900/50 fixed inset-0 z-40",
            closable: true,
        };
        const modal = new window.Modal(document.getElementById('modal-see-transaction'), options);
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
        initBluetoothUI();

        // Bluetooth Printer Toggler
        $('#modal-see-transaction').on('click', '#btn-toggle-bluetooth', async function() {
            if (window.bluetoothPrinterInstance.isConnected()) {
                window.bluetoothPrinterInstance.disconnect();
                if (window._setBtUI) window._setBtUI(false);
                oAlert('orange', 'Disconnected', 'Printer Bluetooth terputus.');
            } else {
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
                            if (!window.bluetoothPrinterInstance.isConnected()) {
                                oAlert('orange', 'Warning', 'Printer Bluetooth belum terhubung. Silakan hubungkan terlebih dahulu.');
                                return;
                            }
                            try {
                                loading();
                                const bytes = buildEscPosReceipt(data, noPrice);
                                await window.bluetoothPrinterInstance.print(bytes);
                                removeLoading();
                                oAlert('green', 'Printed', 'Struk berhasil dicetak via Bluetooth.');
                            } catch (e) {
                                removeLoading();
                                oAlert('red', 'Error', 'Gagal mengirim data ke printer Bluetooth.');
                            }
                        } else if (method === 'rawbt') {
                            // Android RawBT intent print
                            try {
                                const bytes = buildEscPosReceipt(data, noPrice);
                                window.printViaRawBT(bytes);
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
            });
        });

        // Helper: Convert transaction data to ESC/POS binary bytes
        function buildEscPosReceipt(data, noPrice = false) {
            const tx = data.transaction;
            const items = tx.order_item || [];
            const res = data.restaurant || {};
            const cashier = data.user || 'Kasir';

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
    </script>
@endsection