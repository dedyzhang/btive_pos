@extends('layout.index')
@section('title', 'Rekap Absensi Staf')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">REKAP ABSENSI</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-clipboard-check text-lg text-brand"></i></div>
            <span class="text-gray-600 font-medium">Dasbor Rekap Kehadiran</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="container-place w-full p-6 flex flex-col gap-6">
        
        <!-- Filter & Statistics Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Quick Filter Card -->
            <div class="col-span-1 lg:col-span-4 bg-white p-5 rounded-2xl border border-gray-150 shadow-sm">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Filter Data Absensi</h3>
                <form method="GET" action="{{ route('attendance.recap') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="text-xs font-semibold text-gray-500 block mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand" value="{{ $startDate }}" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="text-xs font-semibold text-gray-500 block mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand" value="{{ $endDate }}" />
                    </div>

                    <!-- User Filter -->
                    <div>
                        <label for="user_id" class="text-xs font-semibold text-gray-500 block mb-1">Pilih Karyawan</label>
                        <select name="user_id" id="user_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand">
                            <option value="">Semua Karyawan</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->uuid }}" {{ $userId == $user->uuid ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons group -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2.5 bg-brand hover:bg-brand-strong text-white text-sm font-bold rounded-xl shadow-md shadow-brand/10 transition-all cursor-pointer flex items-center justify-center gap-1 border-none outline-none">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('attendance.export', ['start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId]) }}" class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-500/10 transition-all cursor-pointer flex items-center justify-center gap-1 border-none outline-none text-center">
                            <i class="fas fa-file-excel"></i> Ekspor Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats Banner -->
        @php
            $totalAttendance = $attendances->count();
            $lateCount = 0;
            foreach ($attendances as $att) {
                if ($att->clock_in > $lateTime) {
                    $lateCount++;
                }
            }
            $onTimeCount = $totalAttendance - $lateCount;
            $lateRate = $totalAttendance > 0 ? Math_round(($lateCount / $totalAttendance) * 100) : 0;
            
            function Math_round($val) {
                return round($val);
            }
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Stat 1: Total Attendance -->
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Total Kehadiran</p>
                    <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $totalAttendance }} Hari Staf</h3>
                </div>
            </div>

            <!-- Stat 2: On Time -->
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Tepat Waktu</p>
                    <h3 class="text-2xl font-black text-emerald-600 mt-0.5">{{ $onTimeCount }}</h3>
                </div>
            </div>

            <!-- Stat 3: Late Count -->
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-hourglass-end"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Terlambat</p>
                    <h3 class="text-2xl font-black text-rose-600 mt-0.5">{{ $lateCount }}</h3>
                </div>
            </div>

            <!-- Stat 4: Threshold Info -->
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Batas Jam Keterlambatan</p>
                    <h3 class="text-2xl font-black text-amber-500 mt-0.5">{{ substr($lateTime, 0, 5) }} WIB</h3>
                </div>
            </div>
        </div>

        <!-- Main Data Table Card -->
        <div class="bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Kehadiran Staf</h3>
            <div class="overflow-x-auto">
                <table id="recap-table" class="w-full text-sm text-left text-gray-500 table-auto">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3">No</th>
                            <th scope="col" class="px-4 py-3">Tanggal</th>
                            <th scope="col" class="px-4 py-3">Karyawan</th>
                            <th scope="col" class="px-4 py-3">Role</th>
                            <th scope="col" class="px-4 py-3">Clock In</th>
                            <th scope="col" class="px-4 py-3 text-center">Foto Masuk</th>
                            <th scope="col" class="px-4 py-3">Clock Out</th>
                            <th scope="col" class="px-4 py-3 text-center">Foto Pulang</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @foreach ($attendances as $index => $attendance)
                            @php
                                $isLate = $attendance->clock_in > $lateTime;
                                $statusText = "Tepat Waktu";
                                $statusClass = "bg-emerald-100 text-emerald-700 border-emerald-200";
                                $lateMinutes = 0;

                                if ($isLate) {
                                    $inTime = new \DateTime($attendance->clock_in);
                                    $limitTime = new \DateTime($lateTime);
                                    $interval = $inTime->diff($limitTime);
                                    $lateMinutes = ($interval->h * 60) + $interval->i;
                                    
                                    $statusText = "Terlambat (" . $lateMinutes . "m)";
                                    $statusClass = "bg-rose-100 text-rose-700 border-rose-200";
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5 font-semibold text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3.5 font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($attendance->tanggal)->translatedFormat('l, d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-gray-800">
                                    {{ $attendance->user->name ?? 'Staf Kasir' }}
                                </td>
                                <td class="px-4 py-3.5 text-gray-500 uppercase tracking-wide text-xs">
                                    <span class="px-2.5 py-0.5 rounded-full bg-gray-100 font-semibold border border-gray-200">{{ $attendance->user->role ?? 'Kasir' }}</span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-gray-700 font-bold">
                                    {{ substr($attendance->clock_in, 0, 5) }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($attendance->foto_in)
                                        <img src="{{ asset('storage/attendance/' . $attendance->foto_in) }}" class="w-11 h-11 object-cover rounded-xl border border-gray-250 cursor-pointer hover:scale-105 transition-all view-attendance-photo shadow-sm mx-auto" alt="Bukti Masuk: {{ $attendance->user->name ?? 'Kasir' }}" />
                                    @else
                                        <span class="text-xs text-gray-400 font-semibold italic">Tidak Ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-mono text-gray-700 font-bold">
                                    {{ $attendance->clock_out ? substr($attendance->clock_out, 0, 5) : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($attendance->foto_out)
                                        <img src="{{ asset('storage/attendance/' . $attendance->foto_out) }}" class="w-11 h-11 object-cover rounded-xl border border-gray-250 cursor-pointer hover:scale-105 transition-all view-attendance-photo shadow-sm mx-auto" alt="Bukti Pulang: {{ $attendance->user->name ?? 'Kasir' }}" />
                                    @else
                                        <span class="text-xs text-gray-400 font-semibold italic">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full border {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Fullscreen Attendance Image Viewer Overlay -->
    <div id="attendance-image-viewer" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/85 backdrop-blur-md transition-opacity duration-300 opacity-0">
        <div class="relative max-w-md w-full p-4 flex flex-col items-center">
            <button type="button" class="absolute top-4 right-4 bg-white/20 hover:bg-white/40 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all cursor-pointer border-none outline-none close-image-viewer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <img id="viewer-photo" class="max-w-full max-h-[70vh] rounded-2xl object-contain shadow-2xl border border-white/10" src="" alt="Bukti Kehadiran Full" />
            <p id="viewer-caption" class="text-white text-sm font-semibold mt-4 text-center px-4 py-2 bg-black/40 rounded-full backdrop-blur-sm"></p>
        </div>
    </div>

    <script type="module">
        $(document).ready(function() {
            // Setup DataTable
            if ($('#recap-table').length) {
                new DataTable('#recap-table', {
                    pageLength: 10,
                    ordering: false,
                    responsive: true,
                    language: {
                        search: "Cari Rekap:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ kehadiran",
                        infoEmpty: "Data absensi kosong",
                        zeroRecords: "Data tidak ditemukan",
                        paginate: {
                            next: "Lanjut",
                            previous: "Kembali"
                        }
                    }
                });
            }

            // View Photo full screen
            $(document).on('click', '.view-attendance-photo', function(e) {
                e.preventDefault();
                const src = $(this).attr('src');
                const caption = $(this).attr('alt');
                $('#viewer-photo').attr('src', src);
                $('#viewer-caption').text(caption);
                
                const $viewer = $('#attendance-image-viewer');
                $viewer.removeClass('hidden');
                setTimeout(() => {
                    $viewer.removeClass('opacity-0');
                }, 50);
            });

            // Close Photo Viewer
            $(document).on('click', '.close-image-viewer, #attendance-image-viewer', function(e) {
                if (e.target.id === 'viewer-photo' || e.target.id === 'viewer-caption') {
                    return;
                }
                const $viewer = $('#attendance-image-viewer');
                $viewer.addClass('opacity-0');
                setTimeout(() => {
                    $viewer.addClass('hidden');
                }, 300);
            });
        });
    </script>
@endsection
