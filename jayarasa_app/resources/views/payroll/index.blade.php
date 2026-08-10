@extends('layout.index')
@section('title', 'Sistem Penggajian Staf')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">PENGGAJIAN STAF</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-50"><i class="fas fa-money-bill-wave text-lg text-brand"></i></div>
            <span class="text-gray-600 font-medium">Dasbor Penggajian Karyawan</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="container-place w-full p-6 flex flex-col gap-6">
        
        <!-- Filter & Info Banner -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Filter Month Card -->
            <div class="col-span-1 lg:col-span-4 bg-white p-5 rounded-2xl border border-gray-150 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Periode Penggajian</h3>
                    <p class="text-xs text-gray-500 font-medium">Hitung otomatis gaji pokok berdasarkan kehadiran & penyesuaian bulanan</p>
                </div>
                <form method="GET" action="{{ route('payroll.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="month" name="month" id="month" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand font-semibold text-gray-700" value="{{ $selectedMonth }}" onchange="this.form.submit()" />
                </form>
            </div>
        </div>

        @if(session('success_payroll'))
        <div class="flex items-start sm:items-center p-4 text-sm text-fg-success-strong rounded-2xl bg-success-soft border border-success-subtle" role="alert">
            <i class="me-2 mt-0.5 sm:mt-0 fas fa-check-circle"></i>
            <p><span class="font-bold me-1">Sukses!</span> {{ session('success_payroll') }}</p>
        </div>
        @endif

        <!-- Grid of Staf Payroll Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($payrollData as $data)
                @php
                    $user = $data['user'];
                    // Resolve profile picture
                    $profilePicture = $user->profile_picture 
                        ? (str_starts_with($user->profile_picture, 'resources/') 
                            ? Vite::asset($user->profile_picture) 
                            : (str_starts_with($user->profile_picture, 'http') ? $user->profile_picture : asset($user->profile_picture)))
                        : Vite::asset('resources/img/profile/boy_1.png');
                @endphp
                <div class="bg-white rounded-3xl border border-gray-150 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-300">
                    
                    <!-- Header Section -->
                    <div class="p-6 pb-4 border-b border-gray-100 flex items-center gap-4">
                        <img src="{{ $profilePicture }}" class="w-14 h-14 object-cover rounded-full ring-4 ring-brand-soft" alt="{{ $user->name }}" />
                        <div class="overflow-hidden">
                            <h3 class="font-black text-gray-800 text-base truncate leading-tight">{{ $user->name }}</h3>
                            <span class="inline-block text-[10px] font-bold text-brand bg-brand-soft border border-brand-subtle px-2 py-0.5 rounded-full uppercase tracking-wider mt-1.5">{{ $user->role }}</span>
                        </div>
                    </div>

                    <!-- Payroll Calculation Details -->
                    <div class="p-6 py-4 flex-grow flex flex-col gap-3.5">
                        
                        <!-- Mini Stats Row -->
                        <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                            <div class="text-center border-r border-gray-200/60">
                                <span class="text-[9px] uppercase tracking-widest text-gray-400 font-bold block">Gaji / Hari</span>
                                <span class="text-xs font-extrabold text-gray-700 block mt-0.5">Rp {{ number_format($user->daily_salary, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-center">
                                <span class="text-[9px] uppercase tracking-widest text-gray-400 font-bold block">Hari Kerja</span>
                                <span class="text-xs font-extrabold text-emerald-600 block mt-0.5">{{ $data['total_days_worked'] }} Hari</span>
                            </div>
                        </div>

                        <!-- Calculation Items -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs font-medium text-gray-500">
                                <span>Gaji Pokok Kehadiran</span>
                                <span class="font-mono text-gray-700 font-bold">Rp {{ number_format($data['base_salary_total'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-medium text-gray-500">
                                <span class="text-emerald-600 flex items-center gap-1"><i class="fas fa-circle-plus text-[10px]"></i> Total Bonus</span>
                                <span class="font-mono text-emerald-600 font-bold">+ Rp {{ number_format($data['total_bonuses'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-medium text-gray-500">
                                <span class="text-rose-600 flex items-center gap-1"><i class="fas fa-circle-minus text-[10px]"></i> Total Potongan</span>
                                <span class="font-mono text-rose-600 font-bold">- Rp {{ number_format($data['total_deductions'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Payout Status Banner -->
                        @if($data['payout'])
                            <div class="p-2 bg-emerald-50 border border-emerald-250 rounded-2xl flex items-center justify-between text-[11px] mb-1">
                                <span class="text-emerald-700 font-extrabold flex items-center gap-1">
                                    <i class="fas fa-circle-check"></i> LUNAS DIBAYAR
                                </span>
                                <span class="text-gray-500 font-bold font-mono text-[9px]">
                                    via {{ $data['payout']->account->name }}
                                </span>
                            </div>
                        @else
                            <div class="p-2 bg-amber-50 border border-amber-250 rounded-2xl flex items-center justify-between text-[11px] mb-1">
                                <span class="text-amber-700 font-extrabold flex items-center gap-1">
                                    <i class="fas fa-circle-xmark"></i> BELUM DIBAYAR
                                </span>
                            </div>
                        @endif

                        <hr class="border-dashed border-gray-150 my-1.5" />

                        <!-- Net Salary Output -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800 uppercase tracking-wide">Gaji Bersih Diterima</span>
                            <span class="text-lg font-black text-brand font-mono">Rp {{ number_format($data['net_salary'], 0, ',', '.') }}</span>
                        </div>

                        <!-- Payout CTA Button -->
                        <div class="mt-4">
                            @if($data['payout'])
                                <button type="button" class="btn-cancel-payout w-full py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-250 text-rose-700 font-extrabold rounded-2xl text-xs flex items-center justify-center gap-2 cursor-pointer transition-all outline-none" data-uuid="{{ $data['payout']->uuid }}" data-name="{{ $user->name }}">
                                    <i class="fas fa-rotate-left text-xs"></i> Batal Pembayaran Gaji
                                </button>
                            @else
                                <button type="button" class="btn-trigger-payout w-full py-2.5 bg-brand hover:bg-brand-strong text-white font-extrabold rounded-2xl text-xs flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-brand/10 transition-all border-none outline-none" data-uuid="{{ $user->uuid }}" data-name="{{ $user->name }}" data-salary="{{ $data['net_salary'] }}">
                                    <i class="fas fa-wallet text-xs"></i> Bayar Gaji Sekarang
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action Button Panel -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-4 gap-1.5">
                        <!-- Set Salary Rate -->
                        <button type="button" class="btn-edit-daily-salary flex flex-col items-center justify-center py-2 px-1 bg-white hover:bg-brand-soft border border-gray-200 hover:border-brand-subtle rounded-xl text-gray-600 hover:text-brand transition-all cursor-pointer outline-none" data-uuid="{{ $user->uuid }}" data-name="{{ $user->name }}" data-salary="{{ $user->daily_salary }}">
                            <i class="fas fa-money-bill text-xs mb-1"></i>
                            <span class="text-[9px] font-bold uppercase tracking-wider">Set Gaji</span>
                        </button>
                        
                        <!-- Add Bonus / Deduction -->
                        <button type="button" class="btn-manage-adjustments flex flex-col items-center justify-center py-2 px-1 bg-white hover:bg-brand-soft border border-gray-200 hover:border-brand-subtle rounded-xl text-gray-600 hover:text-brand transition-all cursor-pointer outline-none" data-uuid="{{ $user->uuid }}" data-name="{{ $user->name }}" data-adjustments='@json($data["adjustments"])'>
                            <i class="fas fa-plus-minus text-xs mb-1"></i>
                            <span class="text-[9px] font-bold uppercase tracking-wider">Penyesuaian</span>
                        </button>

                        <!-- View Calendar -->
                        <button type="button" class="btn-view-calendar flex flex-col items-center justify-center py-2 px-1 bg-white hover:bg-brand-soft border border-gray-200 hover:border-brand-subtle rounded-xl text-gray-600 hover:text-brand transition-all cursor-pointer outline-none" data-uuid="{{ $user->uuid }}" data-name="{{ $user->name }}">
                            <i class="fas fa-calendar-days text-xs mb-1"></i>
                            <span class="text-[9px] font-bold uppercase tracking-wider">Kalender</span>
                        </button>

                        <!-- Print Payslip -->
                        <a href="{{ route('payroll.print', [$user->uuid, 'month' => $selectedMonth]) }}" target="_blank" class="flex flex-col items-center justify-center py-2 px-1 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 rounded-xl text-gray-600 hover:text-emerald-600 transition-all text-center">
                            <i class="fas fa-print text-xs mb-1"></i>
                            <span class="text-[9px] font-bold uppercase tracking-wider">Cetak Slip</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Atur Gaji Harian -->
    <div id="modal-daily-salary" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px] flex">
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4 animate-scaleUp">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">Atur Gaji Karyawan</h3>
                <button type="button" class="close-salary-modal text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none bg-transparent">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>
            <form id="form-daily-salary" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Karyawan</p>
                    <p id="salary-modal-employee-name" class="text-sm font-extrabold text-gray-800"></p>
                </div>
                <div class="form-group flex-col w-full mb-6">
                    <label for="daily_salary" class="text-xs font-bold text-gray-500 mb-1">Gaji Per Hari (Rupiah)</label>
                    <input type="number" name="daily_salary" id="daily_salary" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand font-semibold text-gray-700" placeholder="Masukkan gaji harian" required />
                </div>
                <button type="submit" class="w-full bg-brand hover:bg-brand-strong text-white font-bold py-3 px-4 rounded-2xl shadow-lg shadow-brand/20 transition-all cursor-pointer border-none outline-none text-xs uppercase tracking-wide">
                    Simpan Konfigurasi
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Kelola Bonus & Potongan -->
    <div id="modal-adjustments" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px] flex">
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">Penyesuaian Gaji</h3>
                    <p id="adj-modal-employee-name" class="text-[11px] text-gray-400 font-bold mt-0.5 uppercase"></p>
                </div>
                <button type="button" class="close-adj-modal text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none bg-transparent">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Existing Adjustments List -->
            <div class="mb-6">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2.5">Daftar Penyesuaian (Bulan Ini)</h4>
                <div class="max-h-40 overflow-y-auto border border-gray-150 rounded-2xl bg-gray-50">
                    <table class="w-full text-xs text-left text-gray-500">
                        <thead class="text-[10px] text-gray-600 uppercase bg-gray-100/80 sticky top-0">
                            <tr>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Tipe</th>
                                <th class="px-4 py-2">Nominal</th>
                                <th class="px-4 py-2">Ket</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="adj-table-body" class="divide-y divide-gray-150">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="border-gray-150 mb-4" />

            <!-- Add Adjustment Form -->
            <form id="form-add-adjustment" method="POST" action="{{ route('payroll.adjustments.store') }}">
                @csrf
                <input type="hidden" name="user_id" id="adj_user_id" value="" />
                
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Tambah Penyesuaian Baru</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-5">
                    <!-- Date -->
                    <div>
                        <label for="adj_tanggal" class="text-[11px] font-bold text-gray-500 block mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="adj_tanggal" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand font-semibold text-gray-700" required />
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="adj_type" class="text-[11px] font-bold text-gray-500 block mb-1">Tipe Penyesuaian</label>
                        <select name="type" id="adj_type" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand font-semibold text-gray-700" required>
                            <option value="bonus">Bonus / Insentif (+)</option>
                            <option value="deduction">Potongan Gaji (-)</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="adj_amount" class="text-[11px] font-bold text-gray-500 block mb-1">Nominal (Rupiah)</label>
                        <input type="number" name="amount" id="adj_amount" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand font-semibold text-gray-700" placeholder="Masukkan jumlah" required />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="adj_notes" class="text-[11px] font-bold text-gray-500 block mb-1">Keterangan / Alasan</label>
                        <input type="text" name="notes" id="adj_notes" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand font-semibold text-gray-700" placeholder="Contoh: Bonus Lembur, Terlambat" />
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand hover:bg-brand-strong text-white font-bold py-3 px-4 rounded-2xl shadow-lg shadow-brand/20 transition-all cursor-pointer border-none outline-none text-xs uppercase tracking-wide">
                    Simpan Penyesuaian Gaji
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Kalender Absensi -->
    <div id="modal-attendance-calendar" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px] flex">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4 animate-scaleUp">
            
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">Kalender Absensi</h3>
                    <p id="cal-modal-employee-name" class="text-[11px] text-gray-400 font-bold mt-0.5 uppercase"></p>
                </div>
                <button type="button" class="close-cal-modal text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none bg-transparent">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Calendar Container -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between bg-gray-50 px-4 py-2 rounded-xl border border-gray-150">
                    <span id="cal-month-title" class="text-xs font-extrabold text-gray-700 uppercase">Bulan</span>
                    <div class="flex gap-4 text-[10px] font-bold">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Terlambat</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Alpa</span>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1.5 text-center">
                    <!-- Day Headers -->
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Min</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Sen</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Sel</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Rab</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Kam</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Jum</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase py-1">Sab</div>

                    <!-- Calendar Days Container -->
                    <div id="calendar-days-grid" class="contents"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bayar Gaji -->
    <div id="modal-payroll-payout" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px] flex">
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4 animate-scaleUp">
            
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">Konfirmasi Bayar Gaji</h3>
                <button type="button" class="close-payout-modal text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none bg-transparent">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="form-payroll-payout" method="POST" action="{{ route('payroll.payout.store') }}">
                @csrf
                <input type="hidden" name="user_id" id="payout_user_id" value="" />
                <input type="hidden" name="month" value="{{ $selectedMonth }}" />
                <input type="hidden" name="amount" id="payout_amount" value="" />

                <div class="mb-4 space-y-3">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Karyawan</p>
                        <p id="payout-modal-employee-name" class="text-sm font-extrabold text-gray-800 mt-0.5"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Periode</p>
                        <p class="text-xs font-bold text-gray-650 mt-0.5">{{ \Carbon\Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Gaji Bersih</p>
                        <p id="payout-modal-amount-label" class="text-base font-black text-brand mt-0.5"></p>
                    </div>
                </div>

                <hr class="border-gray-150 mb-4" />

                <!-- Account selection -->
                <div class="form-group flex-col w-full mb-4">
                    <label for="payout_account_id" class="text-xs font-bold text-gray-500 mb-1">Sumber Kas Pembayaran</label>
                    <select name="account_id" id="payout_account_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand font-semibold text-gray-700" required>
                        @if($accounts->count() > 0)
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->uuid }}">{{ $acc->name }}</option>
                            @endforeach
                        @else
                            <option value="" disabled selected>Tidak ada akun kas keuangan</option>
                        @endif
                    </select>
                    @if($accounts->count() === 0)
                        <span class="text-[10px] text-rose-500 font-bold mt-1 block">Silakan buat akun kas di menu Arus Kas terlebih dahulu!</span>
                    @endif
                </div>

                <!-- Description -->
                <div class="form-group flex-col w-full mb-6">
                    <label for="payout_description" class="text-xs font-bold text-gray-500 mb-1">Keterangan Transaksi</label>
                    <input type="text" name="description" id="payout_description" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-brand font-semibold text-gray-700" required />
                </div>

                <button type="submit" class="w-full bg-brand hover:bg-brand-strong text-white font-bold py-3 px-4 rounded-2xl shadow-lg shadow-brand/20 transition-all cursor-pointer border-none outline-none text-xs uppercase tracking-wide" {{ $accounts->count() === 0 ? 'disabled' : '' }}>
                    Bayar & Catat ke Arus Kas
                </button>
            </form>
        </div>
    </div>

    <!-- Hidden delete form for adjustments -->
    <form id="form-delete-adjustment" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden form for cancel payout -->
    <form id="form-cancel-payout" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script type="module">
        // Modal instances
        let dailySalaryModal = null;
        let adjustmentsModal = null;
        let calendarModal = null;
        let payrollPayoutModal = null;

        const dsModalEl = document.getElementById('modal-daily-salary');
        const adjModalEl = document.getElementById('modal-adjustments');
        const calModalEl = document.getElementById('modal-attendance-calendar');
        const payoutModalEl = document.getElementById('modal-payroll-payout');

        if (window.Modal) {
            dailySalaryModal = new window.Modal(dsModalEl, {
                placement: 'center',
                backdrop: 'dynamic',
                backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                closable: true
            });

            adjustmentsModal = new window.Modal(adjModalEl, {
                placement: 'center',
                backdrop: 'dynamic',
                backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                closable: true
            });

            calendarModal = new window.Modal(calModalEl, {
                placement: 'center',
                backdrop: 'dynamic',
                backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                closable: true
            });

            payrollPayoutModal = new window.Modal(payoutModalEl, {
                placement: 'center',
                backdrop: 'dynamic',
                backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                closable: true
            });
        }

        // Close Calendar Modal
        $('.close-cal-modal, #modal-attendance-calendar').click(function(e) {
            if (e.target === this || $(e.target).closest('.close-cal-modal').length) {
                if (calendarModal) calendarModal.hide();
            }
        });

        // Close Payout Modal
        $('.close-payout-modal, #modal-payroll-payout').click(function(e) {
            if (e.target === this || $(e.target).closest('.close-payout-modal').length) {
                if (payrollPayoutModal) payrollPayoutModal.hide();
            }
        });

        // Daily Salary Modal Trigger
        $('.btn-edit-daily-salary').click(function() {
            const uuid = $(this).data('uuid');
            const name = $(this).data('name');
            const salary = $(this).data('salary');

            $('#salary-modal-employee-name').text(name);
            $('#daily_salary').val(salary);

            let actionUrl = "{{ route('payroll.salary.update', ':uuid') }}";
            actionUrl = actionUrl.replace(':uuid', uuid);
            $('#form-daily-salary').attr('action', actionUrl);

            if (dailySalaryModal) dailySalaryModal.show();
        });

        // Close Daily Salary Modal
        $('.close-salary-modal, #modal-daily-salary').click(function(e) {
            if (e.target === this || $(e.target).closest('.close-salary-modal').length) {
                if (dailySalaryModal) dailySalaryModal.hide();
            }
        });

        // Manage Adjustments Modal Trigger
        $('.btn-manage-adjustments').click(function() {
            const uuid = $(this).data('uuid');
            const name = $(this).data('name');
            const adjustments = $(this).data('adjustments');

            $('#adj-modal-employee-name').text(name);
            $('#adj_user_id').val(uuid);

            // Set current date as default for date input
            const today = new Date().toISOString().substring(0, 10);
            $('#adj_tanggal').val(today);

            // Populate existing adjustments table
            const tbody = $('#adj-table-body');
            tbody.empty();

            if (adjustments && adjustments.length > 0) {
                adjustments.forEach(function(adj) {
                    const formattedDate = new Date(adj.tanggal).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                    const typeLabel = adj.type === 'bonus' 
                        ? '<span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold border border-emerald-250">Bonus</span>' 
                        : '<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold border border-rose-250">Potongan</span>';
                    const amountText = 'Rp ' + parseInt(adj.amount).toLocaleString('id-ID');
                    const notesText = adj.notes ? adj.notes : '-';

                    let row = `
                        <tr class="hover:bg-gray-100 transition-colors">
                            <td class="px-4 py-2.5 font-medium text-gray-800">${formattedDate}</td>
                            <td class="px-4 py-2.5">${typeLabel}</td>
                            <td class="px-4 py-2.5 font-mono font-bold text-gray-700">${amountText}</td>
                            <td class="px-4 py-2.5 font-medium text-gray-500">${notesText}</td>
                            <td class="px-4 py-2.5 text-center">
                                <button type="button" class="btn-delete-adj-row text-rose-500 hover:text-rose-700 w-7 h-7 rounded-full hover:bg-rose-50 flex items-center justify-center mx-auto cursor-pointer border-none outline-none" data-uuid="${adj.uuid}" title="Hapus Penyesuaian">
                                    <i class="fas fa-trash-can text-[10px]"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 font-semibold italic">Tidak ada penyesuaian untuk bulan ini</td></tr>');
            }

            if (adjustmentsModal) adjustmentsModal.show();
        });

        // Close Adjustments Modal
        $('.close-adj-modal, #modal-adjustments').click(function(e) {
            if (e.target === this || $(e.target).closest('.close-adj-modal').length) {
                if (adjustmentsModal) adjustmentsModal.hide();
            }
        });

        // Handle delete adjustment click
        $(document).on('click', '.btn-delete-adj-row', function() {
            const uuid = $(this).data('uuid');
            
            cConfirm("Warning", "Apakah Anda yakin ingin menghapus penyesuaian gaji ini?", function() {
                let deleteUrl = "{{ route('payroll.adjustments.destroy', ':uuid') }}";
                deleteUrl = deleteUrl.replace(':uuid', uuid);
                
                const deleteForm = $('#form-delete-adjustment');
                deleteForm.attr('action', deleteUrl);
                
                loading();
                deleteForm.submit();
            });
        });

        // View Calendar Trigger
        $('.btn-view-calendar').click(function() {
            const uuid = $(this).data('uuid');
            const name = $(this).data('name');
            const month = '{{ $selectedMonth }}';

            $('#cal-modal-employee-name').text(name);
            loading();

            let url = "{{ route('payroll.calendar-data', ['uuid' => ':uuid']) }}?month=:month";
            url = url.replace(':uuid', uuid).replace(':month', month);

            $.ajax({
                type: "GET",
                url: url,
                success: function(response) {
                    removeLoading();
                    if (response.success) {
                        $('#cal-month-title').text(response.month_name);
                        
                        const grid = $('#calendar-days-grid');
                        grid.empty();

                        // Render offset empty slots
                        const startOfWeek = response.start_of_week;
                        for (let i = 0; i < startOfWeek; i++) {
                            grid.append('<div class="aspect-square"></div>');
                        }

                        // Render days
                        response.days.forEach(function(day) {
                            let dayHtml = '';
                            if (day.status === 'present') {
                                dayHtml = `
                                    <div class="aspect-square bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-xl flex flex-col items-center justify-center relative group cursor-help">
                                        <span class="text-xs font-bold">${day.day}</span>
                                        <span class="text-[8px] font-bold opacity-75 mt-0.5">${day.clock_in}</span>
                                        <div class="hidden group-hover:block absolute bottom-full mb-1 z-50 bg-gray-900 text-white text-[9px] rounded-lg p-2 shadow-xl w-32 text-center leading-normal">
                                            <strong>Hadir (Tepat Waktu)</strong><br>
                                            Masuk: ${day.clock_in} WIB<br>
                                            Pulang: ${day.clock_out ? day.clock_out + ' WIB' : '-'}
                                        </div>
                                    </div>
                                `;
                            } else if (day.status === 'late') {
                                dayHtml = `
                                    <div class="aspect-square bg-amber-50 text-amber-700 border border-amber-250 rounded-xl flex flex-col items-center justify-center relative group cursor-help">
                                        <span class="text-xs font-bold">${day.day}</span>
                                        <span class="text-[8px] font-bold opacity-75 mt-0.5">${day.clock_in}</span>
                                        <div class="hidden group-hover:block absolute bottom-full mb-1 z-50 bg-gray-900 text-white text-[9px] rounded-lg p-2 shadow-xl w-32 text-center leading-normal">
                                            <strong>Hadir (Terlambat)</strong><br>
                                            Masuk: ${day.clock_in} WIB<br>
                                            Pulang: ${day.clock_out ? day.clock_out + ' WIB' : '-'}
                                        </div>
                                    </div>
                                `;
                            } else if (day.status === 'absent') {
                                dayHtml = `
                                    <div class="aspect-square bg-rose-50 text-rose-700 border border-rose-250 rounded-xl flex items-center justify-center relative group cursor-help">
                                        <span class="text-xs font-bold">${day.day}</span>
                                        <div class="hidden group-hover:block absolute bottom-full mb-1 z-50 bg-gray-900 text-white text-[9px] rounded-lg p-2 shadow-xl w-32 text-center leading-normal">
                                            <strong>Tidak Absen (Alpa)</strong>
                                        </div>
                                    </div>
                                `;
                            } else {
                                // Future day
                                dayHtml = `
                                    <div class="aspect-square bg-gray-50 text-gray-300 border border-gray-150 rounded-xl flex items-center justify-center">
                                        <span class="text-xs font-bold">${day.day}</span>
                                    </div>
                                `;
                            }
                            grid.append(dayHtml);
                        });

                        if (calendarModal) calendarModal.show();
                    }
                },
                error: function() {
                    removeLoading();
                    oAlert("red", "Error", "Gagal mengambil data kalender absensi.");
                }
            });
        });

        // Payout Modal Trigger
        $('.btn-trigger-payout').click(function() {
            const uuid = $(this).data('uuid');
            const name = $(this).data('name');
            const salary = $(this).data('salary');

            $('#payout_user_id').val(uuid);
            $('#payout_amount').val(salary);
            $('#payout-modal-employee-name').text(name);
            $('#payout-modal-amount-label').text('Rp ' + parseInt(salary).toLocaleString('id-ID'));
            
            // Set default description
            const monthText = $('#cal-month-title').text() || 'Bulan Ini';
            $('#payout_description').val('Pembayaran Gaji ' + name);

            if (payrollPayoutModal) payrollPayoutModal.show();
        });

        // Cancel Payout Trigger
        $('.btn-cancel-payout').click(function() {
            const uuid = $(this).data('uuid');
            const name = $(this).data('name');

            cConfirm("Warning", "Apakah Anda yakin ingin membatalkan pembayaran gaji " + name + "? Transaksi pengeluaran kas terkait akan dihapus.", function() {
                let cancelUrl = "{{ route('payroll.payout.destroy', ':uuid') }}";
                cancelUrl = cancelUrl.replace(':uuid', uuid);

                const cancelForm = $('#form-cancel-payout');
                cancelForm.attr('action', cancelUrl);

                loading();
                cancelForm.submit();
            });
        });
    </script>
@endsection
