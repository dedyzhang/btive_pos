<!DOCTYPE html>
<html>
    <head>
        <title>Betive POS - @yield('title',config('app.name','Laravel'))</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ Vite::asset('resources/img/headers-icon.png') }}">
        {{-- Font --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
        
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @php
            $restaurant_settings = null;
            $rawSettings = \App\Models\Settings::where('jenis', 'restaurant_settings')->first()?->nilai;
            if ($rawSettings) {
                $restaurant_settings = @unserialize($rawSettings);
                if ($restaurant_settings === false) {
                    $restaurant_settings = @unserialize(stripslashes($rawSettings));
                }
            }
            $accentColor = $restaurant_settings['accent_color'] ?? '#2b66ff';
        @endphp
        <style>
            :root {
                --color-brand: {{ $accentColor }} !important;
                --color-brand-strong: color-mix(in srgb, var(--color-brand) 80%, black) !important;
                --color-brand-soft: color-mix(in srgb, var(--color-brand) 10%, white) !important;
                --color-brand-softer: color-mix(in srgb, var(--color-brand) 5%, white) !important;
                --color-brand-subtle: color-mix(in srgb, var(--color-brand) 15%, white) !important;
                --color-brand-medium: color-mix(in srgb, var(--color-brand) 50%, white) !important;
                --color-brand-light: color-mix(in srgb, var(--color-brand) 75%, white) !important;
                --color-fg-brand: var(--color-brand) !important;
                --color-fg-brand-strong: var(--color-brand-strong) !important;
            }
        </style>
    </head>
    <body class="bg-gray-100">
        {{-- Navbar --}}
        <nav class="ps-7 py-5 flex items-center justify-start gap-4">
            <button class="bg-white rounded-full px-4 py-4 cursor-pointer open-sidebar"><i class="fa-solid fa-bars text-2xl text-gray-500"></i></button>
            @yield('navbar')
        </nav>
        <div class="container-body">
            @yield('container')
        </div>        
        {{-- Sidebar Modal --}}
        {{-- Sidebar Modal --}}
        @include('layout.sidebar')

        {{-- Modal Absensi Staf Premium --}}
        <div id="modal-attendance" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[96vh] md:max-h-[90vh]">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 rounded-t shrink-0 border-b border-gray-100 bg-white">
                    <h3 class="text-lg font-bold text-gray-800">
                        Absensi Harian
                    </h3>
                    <div class="button-place flex gap-1">
                        <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center close-attendance-modal border-none">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>

                <!-- Body Modal (Scrollable) -->
                <div class="p-5 overflow-y-auto flex-grow max-h-[78vh] md:max-h-[55vh]">
                    <!-- Real-time Clock and Date Card -->
                    <div class="bg-gradient-to-r from-brand to-indigo-600 p-4 rounded-2xl text-white text-center shadow-md mb-4 flex-shrink-0">
                        <p class="text-[10px] uppercase tracking-widest text-blue-100 font-semibold opacity-90 mb-0.5">Waktu Sekarang</p>
                        <h2 id="attendance-clock" class="text-2xl font-extrabold tracking-tight drop-shadow-sm my-0.5 font-mono">00:00:00</h2>
                        <p id="attendance-date" class="text-[10px] font-medium opacity-90"></p>
                    </div>

                    <!-- Profil Karyawan Card -->
                    <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-2xl mb-4 border border-gray-100">
                        <img id="attendance-user-avatar" src="" class="rounded-full w-12 h-12 object-cover border-2 border-brand/20 shadow-md animate-pulse" alt="Avatar" />
                        <div>
                            <h3 id="attendance-user-name" class="font-bold text-gray-800 text-base leading-tight">Nama Karyawan</h3>
                            <span id="attendance-user-role" class="inline-block mt-0.5 px-2.5 py-0.5 bg-brand-soft text-fg-brand-strong text-[10px] font-semibold rounded-full uppercase tracking-wider">ROLE</span>
                        </div>
                    </div>

                    <!-- Panel Kamera Absensi Premium -->
                    <div id="attendance-photo-panel" class="mb-4 hidden transition-all duration-300">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2" id="attendance-photo-title">Ambil Foto Bukti Kehadiran</h4>
                        
                        <div class="relative w-full h-[45vh] md:h-60 bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 flex items-center justify-center shadow-inner group">
                            <!-- Live Video Feed -->
                            <video id="attendance-video" class="w-full h-full object-cover block hidden" autoplay playsinline></video>
                            
                            <!-- Static Preview Image (after capture) -->
                            <img id="attendance-photo-preview" class="w-full h-full object-cover block hidden" alt="Pratinjau Foto" />
                            
                            <!-- Placeholder -->
                            <div id="attendance-photo-placeholder" class="text-center p-4">
                                <div class="w-10 h-10 rounded-full bg-brand-soft text-brand flex items-center justify-center text-lg mx-auto mb-2 animate-bounce">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <p class="text-xs font-semibold text-gray-500">Menginisialisasi kamera...</p>
                                <p class="text-[10px] text-gray-400 mt-1">Harap berikan izin akses kamera jika diminta</p>
                            </div>

                            <!-- Overlay control when video streaming is active -->
                            <div id="attendance-video-overlay" class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 hidden">
                                <button type="button" id="btn-snap-photo" class="px-4 py-2 bg-brand hover:bg-brand-strong text-white text-xs font-bold rounded-xl shadow-lg transition-all cursor-pointer flex items-center gap-1 border-none outline-none">
                                    <i class="fa-solid fa-camera"></i> Jepret Foto
                                </button>
                            </div>

                            <!-- Overlay control when preview is active -->
                            <div id="attendance-preview-overlay" class="absolute top-3 right-3 hidden">
                                <button type="button" id="btn-retake-photo" class="w-8 h-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-lg transition-all cursor-pointer border-none outline-none" title="Ulangi Foto">
                                    <i class="fa-solid fa-rotate-left text-sm"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Canvas for resizing and compression (always hidden) -->
                        <canvas id="attendance-canvas" class="hidden"></canvas>
                    </div>

                    <!-- Status Absensi Timeline Card -->
                    <div class="space-y-3 mb-2">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Kehadiran Hari Ini</h4>
                        
                        <!-- Clock In Card -->
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-white transition-all hover:shadow-sm" id="card-clock-in">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-base">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Clock In (Masuk)</p>
                                    <p id="txt-clock-in-time" class="text-xs font-bold text-gray-700 mt-0.5">Belum Tercatat</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div id="thumb-clock-in" class="hidden">
                                    <img src="" class="w-8 h-8 object-cover rounded-lg border border-gray-200 cursor-pointer hover:scale-105 transition-all view-attendance-photo shadow-sm" alt="Bukti Foto Masuk" />
                                </div>
                                <span id="badge-clock-in" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-gray-100 text-gray-500">Belum Absen</span>
                            </div>
                        </div>

                        <!-- Clock Out Card -->
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-white transition-all hover:shadow-sm" id="card-clock-out">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-base">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Clock Out (Pulang)</p>
                                    <p id="txt-clock-out-time" class="text-xs font-bold text-gray-700 mt-0.5">Belum Tercatat</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div id="thumb-clock-out" class="hidden">
                                    <img src="" class="w-8 h-8 object-cover rounded-lg border border-gray-200 cursor-pointer hover:scale-105 transition-all view-attendance-photo shadow-sm" alt="Bukti Foto Pulang" />
                                </div>
                                <span id="badge-clock-out" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-gray-100 text-gray-500">Belum Absen</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer (Fixed Action Buttons) -->
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex-shrink-0 rounded-b-3xl">
                    <div id="attendance-action-container">
                        <!-- Dinamis diisi lewat JS -->
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Fullscreen Attendance Image Viewer Overlay -->
        <div id="attendance-image-viewer" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/85 backdrop-blur-md transition-opacity duration-300 opacity-0">
            <div class="relative max-w-md w-full p-4 flex flex-col items-center">
                <button type="button" class="absolute top-4 right-4 bg-white/20 hover:bg-white/40 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all cursor-pointer border-none outline-none close-image-viewer animate-pulse">
                    <i class="fas fa-times text-base"></i>
                </button>
                <img id="viewer-photo" class="max-w-full max-h-[70vh] rounded-2xl object-contain shadow-2xl border border-white/10" src="" alt="Bukti Kehadiran Full" />
                <p id="viewer-caption" class="text-white text-sm font-semibold mt-4 text-center px-4 py-2 bg-black/40 rounded-full backdrop-blur-sm"></p>
            </div>
        </div>

        {{-- Modal Ganti Password Premium --}}
        <div id="modal-change-password" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[90vh]">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-3 rounded-t shrink-0 border-b border-gray-100 bg-white">
                    <h3 class="text-lg font-bold text-gray-800">
                        Ganti Password
                    </h3>
                    <div class="button-place flex gap-1">
                        <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center close-change-password-modal border-none">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>

                <!-- Body Modal -->
                <form id="form-change-password" class="m-0">
                    <div class="p-5 space-y-4 overflow-y-auto max-h-[60vh]">
                        <!-- Password Saat Ini -->
                        <div>
                            <label for="current_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all pr-10" placeholder="Masukkan password saat ini" required>
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand btn-toggle-password" data-target="current_password">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <label for="new_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password Baru (Minimal 6 karakter)</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all pr-10" placeholder="Masukkan password baru" required>
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand btn-toggle-password" data-target="new_password">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all pr-10" placeholder="Ulangi password baru" required>
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand btn-toggle-password" data-target="new_password_confirmation">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Modal -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                        <button type="button" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-2xl transition-all cursor-pointer close-change-password-modal border-none outline-none">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-strong text-white text-sm font-semibold rounded-2xl shadow-md transition-all cursor-pointer border-none outline-none">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Profil Premium --}}
        <div id="modal-edit-profile" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[90vh]">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 rounded-t shrink-0 border-b border-gray-100 bg-white">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user-edit text-brand"></i> Edit Profil Pribadi
                    </h3>
                    <div class="button-place flex gap-1">
                        <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center close-edit-profile-modal border-none">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>

                <!-- Body Modal -->
                <form id="form-edit-profile" class="m-0" enctype="multipart/form-data">
                    <div class="p-6 space-y-4 overflow-y-auto max-h-[65vh]">
                        <!-- Profile Image Section -->
                        <div class="flex flex-col items-center justify-center mb-4">
                            <div class="relative group cursor-pointer" id="avatar-preview-container">
                                <img id="edit-profile-preview" src="{{ $account->profile_picture ? (str_starts_with($account->profile_picture, 'resources/') ? Vite::asset($account->profile_picture) : asset($account->profile_picture)) : Vite::asset('resources/img/profile/boy_1.png') }}" class="rounded-full w-24 h-24 object-cover border-4 border-brand/10 shadow-lg group-hover:opacity-85 transition-opacity animate-fade-in" />
                                <div class="absolute inset-0 bg-black/45 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-camera text-white text-lg"></i>
                                </div>
                            </div>
                            <button type="button" id="btn-upload-custom-avatar" class="mt-2 text-xs font-bold text-brand hover:text-brand-strong bg-brand-soft px-3 py-1.5 rounded-full transition-all">
                                <i class="fas fa-upload mr-1"></i> Unggah Foto Kustom
                            </button>
                            <input type="file" id="profile_picture_file" name="profile_picture_file" class="hidden" accept="image/*" />
                            <input type="hidden" id="selected_predefined_avatar" name="profile_picture" value="{{ $account->profile_picture }}" />
                        </div>

                        <!-- Predefined Avatars Grid -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih Avatar 3D Bawaan</label>
                            <div class="grid grid-cols-4 gap-2 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                                @php
                                    $predefinedAvatars = [
                                        'resources/img/profile/boy_1.png',
                                        'resources/img/profile/boy_2.png',
                                    ];
                                    for ($i = 1; $i <= 22; $i++) {
                                        $predefinedAvatars[] = "resources/img/profile/avatar_$i.png";
                                    }
                                @endphp
                                @foreach($predefinedAvatars as $avatarPath)
                                    <div class="avatar-option-item relative rounded-xl overflow-hidden aspect-square border-2 border-transparent hover:border-brand cursor-pointer transition-all duration-200" data-path="{{ $avatarPath }}">
                                        <img src="{{ Vite::asset($avatarPath) }}" class="w-full h-full object-cover" />
                                        <div class="avatar-selected-overlay absolute inset-0 bg-brand/35 flex items-center justify-center opacity-0 transition-opacity">
                                            <i class="fas fa-check-circle text-white text-sm animate-scale-up"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="profile_name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" id="profile_name" name="name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all" value="{{ $account->name }}" required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="profile_email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alamat Email</label>
                            <input type="email" id="profile_email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all" value="{{ $account->email }}" placeholder="contoh@domain.com">
                        </div>

                        <!-- Nomor Telepon -->
                        <div>
                            <label for="profile_phone" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor Telepon</label>
                            <input type="text" id="profile_phone" name="phone_number" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all" value="{{ $account->phone_number }}" placeholder="08xxxxxxxxxx">
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="gender-card border-2 border-gray-100 rounded-2xl p-3 flex items-center justify-center gap-2 cursor-pointer transition-all hover:bg-gray-50" id="gender-male-label">
                                    <input type="radio" name="gender" value="Laki-laki" class="hidden" id="gender_male" {{ $account->gender == 'Laki-laki' ? 'checked' : '' }}>
                                    <i class="fas fa-mars text-blue-500"></i> Laki-laki
                                </label>
                                <label class="gender-card border-2 border-gray-100 rounded-2xl p-3 flex items-center justify-center gap-2 cursor-pointer transition-all hover:bg-gray-50" id="gender-female-label">
                                    <input type="radio" name="gender" value="Perempuan" class="hidden" id="gender_female" {{ $account->gender == 'Perempuan' ? 'checked' : '' }}>
                                    <i class="fas fa-venus text-pink-500"></i> Perempuan
                                </label>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="profile_address" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                            <textarea id="profile_address" name="address" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition-all" placeholder="Tulis alamat rumah Anda di sini...">{{ $account->address }}</textarea>
                        </div>
                    </div>

                    <!-- Footer Modal -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 shrink-0 rounded-b-3xl">
                        <button type="button" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-2xl transition-all cursor-pointer close-edit-profile-modal border-none outline-none">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-strong text-white text-sm font-semibold rounded-2xl shadow-md transition-all cursor-pointer border-none outline-none">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Persetujuan Admin (Admin Approval Password) --}}
        <div id="modal-admin-approval" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[90vh]">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 rounded-t shrink-0 border-b border-gray-100 bg-white">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-shield-halved text-red-500"></i> Persetujuan Admin Diperlukan
                    </h3>
                    <div class="button-place flex gap-1">
                        <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center close-admin-approval-modal border-none">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                </div>

                <!-- Body Modal -->
                <form id="form-admin-approval" class="m-0">
                    <div class="p-6 space-y-4 overflow-y-auto">
                        <div class="text-center mb-2">
                            <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-lg mx-auto mb-2 animate-bounce">
                                <i class="fas fa-lock"></i>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Transaksi yang sudah diproses membutuhkan persetujuan **Admin** sebelum dapat dihapus. Silakan masukkan password Admin di bawah ini.
                            </p>
                        </div>

                        <!-- Admin Password Input -->
                        <div>
                            <label for="approval_admin_password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password Admin</label>
                            <div class="relative">
                                <input type="password" id="approval_admin_password" name="admin_password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all pr-10" placeholder="Masukkan password Admin" required>
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 btn-toggle-password" data-target="approval_admin_password">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Modal -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 shrink-0 rounded-b-3xl">
                        <button type="button" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-2xl transition-all cursor-pointer close-admin-approval-modal border-none outline-none">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-2xl shadow-md transition-all cursor-pointer border-none outline-none">
                            Verifikasi & Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script type="module">
            $(document).ready(function() {
                let attendanceStream = null;
                let capturedPhotoBase64 = null;

                // Initialize digital clock & date immediately
                function updateClock() {
                    if (window.moment) {
                        $('#attendance-clock').text(moment().format('HH:mm:ss'));
                        $('#attendance-date').text(moment().format('dddd, DD MMMM YYYY'));
                    } else {
                        const now = new Date();
                        $('#attendance-clock').text(now.toTimeString().split(' ')[0]);
                        $('#attendance-date').text(now.toDateString());
                    }
                }
                updateClock();
                setInterval(updateClock, 1000);

                // Setup Flowbite Modal
                let attendanceModal = null;
                const attendanceModalEl = document.getElementById('modal-attendance');
                if (attendanceModalEl && window.Modal) {
                    attendanceModal = new window.Modal(attendanceModalEl, {
                        placement: 'center',
                        backdrop: 'dynamic',
                        backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                        closable: true,
                        onHide: function() {
                            stopCamera();
                        }
                    });
                }

                let changePasswordModal = null;
                const changePasswordModalEl = document.getElementById('modal-change-password');
                if (changePasswordModalEl && window.Modal) {
                    changePasswordModal = new window.Modal(changePasswordModalEl, {
                        placement: 'center',
                        backdrop: 'dynamic',
                        backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                        closable: true,
                        onHide: function() {
                            // Reset form on hide
                            $('#form-change-password')[0].reset();
                            // Reset password visibility icon and type
                            $('#form-change-password input').attr('type', 'password');
                            $('.btn-toggle-password i').removeClass('fa-eye-slash').addClass('fa-eye');
                        }
                    });
                }

                let editProfileModal = null;
                const editProfileModalEl = document.getElementById('modal-edit-profile');
                if (editProfileModalEl && window.Modal) {
                    editProfileModal = new window.Modal(editProfileModalEl, {
                        placement: 'center',
                        backdrop: 'dynamic',
                        backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                        closable: true,
                        onHide: function() {
                            $('#profile_picture_file').val('');
                        }
                    });
                }

                let adminApprovalModal = null;
                const adminApprovalModalEl = document.getElementById('modal-admin-approval');
                if (adminApprovalModalEl && window.Modal) {
                    adminApprovalModal = new window.Modal(adminApprovalModalEl, {
                        placement: 'center',
                        backdrop: 'dynamic',
                        backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                        closable: true,
                        onHide: function() {
                            $('#form-admin-approval')[0].reset();
                        }
                    });
                    window.adminApprovalModal = adminApprovalModal;
                }

                // Global helper to request admin approval from any page
                window.requestAdminApproval = function(callback) {
                    window._adminApprovalCallback = callback;
                    if (adminApprovalModal) {
                        adminApprovalModal.show();
                    } else {
                        callback('');
                    }
                };

                $(document).on('click', '.close-admin-approval-modal', function() {
                    if (adminApprovalModal) adminApprovalModal.hide();
                });

                $('#form-admin-approval').on('submit', function(e) {
                    e.preventDefault();
                    const password = $('#approval_admin_password').val();
                    if (adminApprovalModal) adminApprovalModal.hide();
                    if (window._adminApprovalCallback) {
                        window._adminApprovalCallback(password);
                    }
                });

                // Handle Sidebar "Edit Profil" Click
                $(document).on('click', '.btn-open-edit-profile', function(e) {
                    e.preventDefault();
                    // Close sidebar
                    $('.sidebar').addClass('hidden');
                    
                    // Show current selected avatar in options
                    const currentAvatarPath = $('#selected_predefined_avatar').val();
                    $('.avatar-option-item').removeClass('border-brand').find('.avatar-selected-overlay').addClass('opacity-0');
                    if (currentAvatarPath && currentAvatarPath.startsWith('resources/')) {
                        const targetItem = $(`.avatar-option-item[data-path="${currentAvatarPath}"]`);
                        if (targetItem.length) {
                            targetItem.addClass('border-brand').find('.avatar-selected-overlay').removeClass('opacity-0');
                        }
                    }
                    
                    // Update gender card visuals
                    updateGenderCardVisuals();
                    
                    if (editProfileModal) editProfileModal.show();
                });

                $(document).on('click', '.close-edit-profile-modal', function() {
                    if (editProfileModal) editProfileModal.hide();
                });

                // Predefined avatar selection
                $(document).on('click', '.avatar-option-item', function() {
                    const path = $(this).data('path');
                    $('#selected_predefined_avatar').val(path);
                    $('#profile_picture_file').val(''); // Clear custom file upload
                    
                    // Set preview image
                    const previewSrc = $(this).find('img').attr('src');
                    $('#edit-profile-preview').attr('src', previewSrc);
                    
                    // Highlight selected item
                    $('.avatar-option-item').removeClass('border-brand').find('.avatar-selected-overlay').addClass('opacity-0');
                    $(this).addClass('border-brand').find('.avatar-selected-overlay').removeClass('opacity-0');
                });

                // Custom avatar upload trigger
                $(document).on('click', '#btn-upload-custom-avatar, #avatar-preview-container', function() {
                    $('#profile_picture_file').click();
                });

                // Preview custom avatar when selected
                $(document).on('change', '#profile_picture_file', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#edit-profile-preview').attr('src', e.target.result);
                            // Clear predefined selection
                            $('#selected_predefined_avatar').val('');
                            $('.avatar-option-item').removeClass('border-brand').find('.avatar-selected-overlay').addClass('opacity-0');
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Gender card selection visual toggle
                function updateGenderCardVisuals() {
                    $('.gender-card').removeClass('bg-brand-soft border-brand text-brand').addClass('border-gray-100 bg-white');
                    
                    if ($('#gender_male').is(':checked')) {
                        $('#gender-male-label').removeClass('border-gray-100 bg-white').addClass('bg-brand-soft border-brand text-brand font-semibold');
                    }
                    if ($('#gender_female').is(':checked')) {
                        $('#gender-female-label').removeClass('border-gray-100 bg-white').addClass('bg-brand-soft border-brand text-brand font-semibold');
                    }
                }

                $(document).on('change', 'input[name="gender"]', function() {
                    updateGenderCardVisuals();
                });

                // AJAX Submit Edit Profile
                $('#form-edit-profile').on('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    loading();
                    
                    $.ajax({
                        type: "POST",
                        url: "{{ route('users.update-profile') }}",
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            removeLoading();
                            if (response.success) {
                                if (editProfileModal) editProfileModal.hide();
                                oAlert("green", "Sukses", response.message);
                                
                                // Update sidebar UI dynamically
                                $('#sidebar-user-name').text(response.user.name);
                                $('#sidebar-user-avatar').attr('src', response.user.profile_picture);
                                
                                // Update layout profile state in forms
                                $('#selected_predefined_avatar').val(response.user.profile_picture);
                                $('#profile_name').val(response.user.name);
                                $('#profile_email').val(response.user.email);
                                $('#profile_phone').val(response.user.phone_number);
                                $('#profile_address').val(response.user.address);
                                
                                // Update attendance avatar if displayed
                                $('#attendance-user-avatar').attr('src', response.user.profile_picture);
                            } else {
                                oAlert("red", "Gagal", response.message || "Gagal memperbarui profil.");
                            }
                        },
                        error: function(xhr) {
                            removeLoading();
                            let msg = "Terjadi kesalahan server saat memperbarui profil.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const firstKey = Object.keys(errors)[0];
                                msg = errors[firstKey][0];
                            }
                            oAlert("red", "Gagal", msg);
                        }
                    });
                });

                // Handle Sidebar "Absensi Staf" Click
                $(document).on('click', '.btn-open-attendance', function(e) {
                    e.preventDefault();
                    // Close sidebar
                    $('.sidebar').addClass('hidden');
                    
                    // Reset photo state
                    capturedPhotoBase64 = null;
                    
                    // Fetch attendance info
                    loadAttendanceStatus();
                });

                // Handle Sidebar "Ganti Password" Click
                $(document).on('click', '.btn-open-change-password', function(e) {
                    e.preventDefault();
                    // Close sidebar
                    $('.sidebar').addClass('hidden');
                    
                    // Open change password modal
                    if (changePasswordModal) changePasswordModal.show();
                });

                // Handle Modal Close Click
                $(document).on('click', '.close-attendance-modal', function() {
                    if (attendanceModal) attendanceModal.hide();
                });

                $(document).on('click', '.close-change-password-modal', function() {
                    if (changePasswordModal) changePasswordModal.hide();
                });

                // Toggle Password Visibility
                $(document).on('click', '.btn-toggle-password', function() {
                    const targetId = $(this).data('target');
                    const input = $('#' + targetId);
                    const icon = $(this).find('i');
                    
                    if (input.attr('type') === 'password') {
                        input.attr('type', 'text');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        input.attr('type', 'password');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });

                // AJAX Submit Ganti Password
                $('#form-change-password').on('submit', function(e) {
                    e.preventDefault();
                    
                    const currentPassword = $('#current_password').val();
                    const newPassword = $('#new_password').val();
                    const confirmPassword = $('#new_password_confirmation').val();

                    if (newPassword.length < 6) {
                        oAlert("red", "Gagal", "Password baru minimal 6 karakter.");
                        return;
                    }

                    if (newPassword !== confirmPassword) {
                        oAlert("red", "Gagal", "Konfirmasi password baru tidak cocok.");
                        return;
                    }

                    loading();
                    
                    $.ajax({
                        type: "POST",
                        url: "{{ route('users.change-password') }}",
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        data: {
                            current_password: currentPassword,
                            new_password: newPassword,
                            new_password_confirmation: confirmPassword
                        },
                        success: function(response) {
                            removeLoading();
                            if (response.success) {
                                if (changePasswordModal) changePasswordModal.hide();
                                oAlert("green", "Sukses", response.message);
                            } else {
                                oAlert("red", "Gagal", response.message || "Gagal mengubah password.");
                            }
                        },
                        error: function(xhr) {
                            removeLoading();
                            let msg = "Terjadi kesalahan server saat mengganti password.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const firstKey = Object.keys(errors)[0];
                                msg = errors[firstKey][0];
                            }
                            oAlert("red", "Gagal", msg);
                        }
                    });
                });

                // AJAX Load Status Absensi
                function loadAttendanceStatus() {
                    loading();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('attendance.today') }}",
                        success: function(response) {
                            removeLoading();
                            if (response.success) {
                                renderAttendanceModal(response);
                                if (attendanceModal) attendanceModal.show();
                            } else {
                                oAlert("red", "Kesalahan", "Gagal memuat data absensi.");
                            }
                        },
                        error: function(xhr) {
                            removeLoading();
                            console.error(xhr);
                            oAlert("red", "Kesalahan", "Terjadi kesalahan server saat memuat data absensi.");
                        }
                    });
                }

                // Render Modal UI dynamically
                function renderAttendanceModal(data) {
                    const user = data.user;
                    const attendance = data.attendance;
                    const status = data.status;

                    // 1. User Info
                    $('#attendance-user-name').text(user.name);
                    $('#attendance-user-role').text(user.role);
                    $('#attendance-user-avatar').attr('src', user.profile_picture).removeClass('animate-pulse');

                    // 2. Clear panels
                    $('#attendance-photo-panel').addClass('hidden');

                    // 3. Attendance Status Card
                    if (status === 'belum_absen') {
                        // Clock In
                        $('#txt-clock-in-time').text('Belum Tercatat').removeClass('text-emerald-600').addClass('text-gray-700');
                        $('#badge-clock-in').text('Belum Absen').removeClass('bg-emerald-100 text-emerald-700').addClass('bg-gray-100 text-gray-500');
                        $('#card-clock-in').removeClass('border-emerald-200 bg-emerald-50/10').addClass('border-gray-100 bg-white');
                        $('#thumb-clock-in').addClass('hidden');

                        // Clock Out
                        $('#txt-clock-out-time').text('Belum Tercatat').removeClass('text-indigo-600').addClass('text-gray-700');
                        $('#badge-clock-out').text('Belum Absen').removeClass('bg-indigo-100 text-indigo-700').addClass('bg-gray-100 text-gray-500');
                        $('#card-clock-out').removeClass('border-indigo-200 bg-indigo-50/10').addClass('border-gray-100 bg-white');
                        $('#thumb-clock-out').addClass('hidden');

                        // Action Button (Active immediately, blue brand theme)
                        $('#attendance-action-container').html(`
                            <button id="btn-clock-in" class="w-full py-4 bg-brand hover:bg-brand-strong text-white rounded-2xl font-bold text-base shadow-lg shadow-brand/20 transition-all hover:scale-[1.02] cursor-pointer flex items-center justify-center gap-2 border-none outline-none">
                                <i class="fa-solid fa-right-to-bracket text-lg"></i>
                                CLOCK IN (Absen Masuk)
                            </button>
                        `);

                        // Show photo panel & start webcam
                        $('#attendance-photo-panel').removeClass('hidden');
                        $('#attendance-photo-title').text('Ambil Foto Bukti Masuk (Clock In)');
                        startCamera();
                    } 
                    else if (status === 'sudah_clock_in') {
                        // Clock In (Tercatat)
                        const clockInFormatted = attendance.clock_in.substring(0, 5);
                        $('#txt-clock-in-time').text(clockInFormatted + ' WIB').addClass('text-emerald-600').removeClass('text-gray-700');
                        $('#badge-clock-in').text('Tercatat').addClass('bg-emerald-100 text-emerald-700').removeClass('bg-gray-100 text-gray-500');
                        $('#card-clock-in').addClass('border-emerald-200 bg-emerald-50/10').removeClass('border-gray-100 bg-white');
                        
                        if (data.foto_in_url) {
                            $('#thumb-clock-in img').attr('src', data.foto_in_url);
                            $('#thumb-clock-in').removeClass('hidden');
                        } else {
                            $('#thumb-clock-in').addClass('hidden');
                        }

                        // Clock Out
                        $('#txt-clock-out-time').text('Belum Tercatat').removeClass('text-indigo-600').addClass('text-gray-700');
                        $('#badge-clock-out').text('Belum Absen').removeClass('bg-indigo-100 text-indigo-700').addClass('bg-gray-100 text-gray-500');
                        $('#card-clock-out').removeClass('border-indigo-200 bg-indigo-50/10').addClass('border-gray-100 bg-white');
                        $('#thumb-clock-out').addClass('hidden');

                        // Action Button (Active immediately, blue brand theme)
                        $('#attendance-action-container').html(`
                            <button id="btn-clock-out" class="w-full py-4 bg-brand hover:bg-brand-strong text-white rounded-2xl font-bold text-base shadow-lg shadow-brand/20 transition-all hover:scale-[1.02] cursor-pointer flex items-center justify-center gap-2 border-none outline-none">
                                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                                CLOCK OUT (Absen Pulang)
                            </button>
                        `);

                        // Show photo panel & start webcam
                        $('#attendance-photo-panel').removeClass('hidden');
                        $('#attendance-photo-title').text('Ambil Foto Bukti Pulang (Clock Out)');
                        startCamera();
                    } 
                    else if (status === 'sudah_clock_out') {
                        // Clock In (Tercatat)
                        const clockInFormatted = attendance.clock_in.substring(0, 5);
                        $('#txt-clock-in-time').text(clockInFormatted + ' WIB').addClass('text-emerald-600').removeClass('text-gray-700');
                        $('#badge-clock-in').text('Tercatat').addClass('bg-emerald-100 text-emerald-700').removeClass('bg-gray-100 text-gray-500');
                        $('#card-clock-in').addClass('border-emerald-200 bg-emerald-50/10').removeClass('border-gray-100 bg-white');
                        
                        if (data.foto_in_url) {
                            $('#thumb-clock-in img').attr('src', data.foto_in_url);
                            $('#thumb-clock-in').removeClass('hidden');
                        } else {
                            $('#thumb-clock-in').addClass('hidden');
                        }

                        // Clock Out (Tercatat)
                        const clockOutFormatted = attendance.clock_out.substring(0, 5);
                        $('#txt-clock-out-time').text(clockOutFormatted + ' WIB').addClass('text-indigo-600').removeClass('text-gray-700');
                        $('#badge-clock-out').text('Tercatat').addClass('bg-indigo-100 text-indigo-700').removeClass('bg-gray-100 text-gray-500');
                        $('#card-clock-out').addClass('border-indigo-200 bg-indigo-50/10').removeClass('border-gray-100 bg-white');
                        
                        if (data.foto_out_url) {
                            $('#thumb-clock-out img').attr('src', data.foto_out_url);
                            $('#thumb-clock-out').removeClass('hidden');
                        } else {
                            $('#thumb-clock-out').addClass('hidden');
                        }

                        // Action Button Disabled
                        $('#attendance-action-container').html(`
                            <button disabled class="w-full py-4 bg-gray-100 text-gray-400 rounded-2xl font-bold text-base cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200 outline-none">
                                <i class="fa-solid fa-circle-check text-lg text-emerald-500"></i>
                                Selesai Absen Hari Ini
                            </button>
                        `);
                    }
                }

                // webcam utilities
                function startCamera() {
                    // Reset elements
                    $('#attendance-video').addClass('hidden');
                    $('#attendance-photo-preview').addClass('hidden').attr('src', '');
                    $('#attendance-photo-placeholder').removeClass('hidden');
                    $('#attendance-video-overlay').addClass('hidden');
                    $('#attendance-preview-overlay').addClass('hidden');
                    
                    stopCamera();

                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({ 
                            video: { 
                                width: { ideal: 640 }, 
                                height: { ideal: 480 }, 
                                facingMode: "user" 
                            } 
                        })
                        .then(function(stream) {
                            attendanceStream = stream;
                            const video = document.getElementById('attendance-video');
                            video.srcObject = stream;
                            $(video).removeClass('hidden');
                            $('#attendance-photo-placeholder').addClass('hidden');
                            $('#attendance-video-overlay').removeClass('hidden');
                        })
                        .catch(function(err) {
                            console.warn("Kamera terblokir atau tidak terdeteksi:", err);
                        });
                    }
                }

                function stopCamera() {
                    if (attendanceStream) {
                        attendanceStream.getTracks().forEach(track => track.stop());
                        attendanceStream = null;
                    }
                }

                function enableAttendanceActionButton() {
                    const $btnIn = $('#btn-clock-in');
                    if ($btnIn.length) {
                        $btnIn.prop('disabled', false)
                            .removeClass('bg-gray-200 text-gray-400 cursor-not-allowed')
                            .addClass('bg-brand hover:bg-brand-strong text-white shadow-brand/20 cursor-pointer border-none outline-none');
                    }
                    const $btnOut = $('#btn-clock-out');
                    if ($btnOut.length) {
                        $btnOut.prop('disabled', false)
                            .removeClass('bg-gray-200 text-gray-400 cursor-not-allowed')
                            .addClass('bg-brand hover:bg-brand-strong text-white shadow-brand/20 cursor-pointer border-none outline-none');
                    }
                }

                function disableAttendanceActionButton() {
                    enableAttendanceActionButton();
                }

                // Handle snap click
                $(document).on('click', '#btn-snap-photo', function(e) {
                    e.preventDefault();
                    const video = document.getElementById('attendance-video');
                    const canvas = document.getElementById('attendance-canvas');
                    const ctx = canvas.getContext('2d');
                    
                    if (video.paused || video.ended) return;

                    let width = video.videoWidth || 640;
                    let height = video.videoHeight || 480;
                    const max_size = 800;
                    
                    if (width > max_size || height > max_size) {
                        if (width > height) {
                            height = Math.round((height * max_size) / width);
                            width = max_size;
                        } else {
                            width = Math.round((width * max_size) / height);
                            height = max_size;
                        }
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(video, 0, 0, width, height);
                    
                    // Compress client side (JPEG quality: 0.6)
                    capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.6);
                    
                    stopCamera();
                    
                    $('#attendance-video').addClass('hidden');
                    $('#attendance-video-overlay').addClass('hidden');
                    
                    $('#attendance-photo-preview').attr('src', capturedPhotoBase64).removeClass('hidden');
                    $('#attendance-preview-overlay').removeClass('hidden');
                    
                    enableAttendanceActionButton();
                });



                // Retake button click
                $(document).on('click', '#btn-retake-photo', function(e) {
                    e.preventDefault();
                    capturedPhotoBase64 = null;
                    disableAttendanceActionButton();
                    startCamera();
                });

                // Image Viewer Click Handlers
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

                // Handle Clock In Action Click
                $(document).on('click', '#btn-clock-in', function(e) {
                    e.preventDefault();
                    if (!capturedPhotoBase64) {
                        const video = document.getElementById('attendance-video');
                        if (video && !video.paused && !video.ended) {
                            const canvas = document.getElementById('attendance-canvas');
                            const ctx = canvas.getContext('2d');
                            let width = video.videoWidth || 640;
                            let height = video.videoHeight || 480;
                            const max_size = 800;
                            if (width > max_size || height > max_size) {
                                if (width > height) {
                                    height = Math.round((height * max_size) / width);
                                    width = max_size;
                                } else {
                                    width = Math.round((width * max_size) / height);
                                    height = max_size;
                                }
                            }
                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(video, 0, 0, width, height);
                            capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.6);
                        } else {
                            oAlert("orange", "Kamera Menginisialisasi", "Mohon tunggu kamera aktif atau izinkan akses kamera.");
                            return;
                        }
                    }

                    loading();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('attendance.clock-in') }}",
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        data: {
                            foto: capturedPhotoBase64
                        },
                        success: function(response) {
                            removeLoading();
                            if (response.success) {
                                oAlert("green", "Berhasil Masuk", response.message);
                                loadAttendanceStatus(); // Reload data status inside modal dynamically
                            } else {
                                oAlert("red", "Gagal", response.message || "Gagal melakukan Clock In.");
                            }
                        },
                        error: function(xhr) {
                            removeLoading();
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan server saat memproses Clock In.";
                            oAlert("red", "Gagal", msg);
                        }
                    });
                });

                // Handle Clock Out Action Click
                $(document).on('click', '#btn-clock-out', function(e) {
                    e.preventDefault();
                    if (!capturedPhotoBase64) {
                        const video = document.getElementById('attendance-video');
                        if (video && !video.paused && !video.ended) {
                            const canvas = document.getElementById('attendance-canvas');
                            const ctx = canvas.getContext('2d');
                            let width = video.videoWidth || 640;
                            let height = video.videoHeight || 480;
                            const max_size = 800;
                            if (width > max_size || height > max_size) {
                                if (width > height) {
                                    height = Math.round((height * max_size) / width);
                                    width = max_size;
                                } else {
                                    width = Math.round((width * max_size) / height);
                                    height = max_size;
                                }
                            }
                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(video, 0, 0, width, height);
                            capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.6);
                        } else {
                            oAlert("orange", "Kamera Menginisialisasi", "Mohon tunggu kamera aktif atau izinkan akses kamera.");
                            return;
                        }
                    }

                    loading();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('attendance.clock-out') }}",
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        data: {
                            foto: capturedPhotoBase64
                        },
                        success: function(response) {
                            removeLoading();
                            if (response.success) {
                                oAlert("green", "Berhasil Pulang", response.message);
                                loadAttendanceStatus(); // Reload data status inside modal dynamically
                            } else {
                                oAlert("red", "Gagal", response.message || "Gagal melakukan Clock Out.");
                            }
                        },
                        error: function(xhr) {
                            removeLoading();
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan server saat memproses Clock Out.";
                            oAlert("red", "Gagal", msg);
                        }
                    });
                });
            });
        </script>

        {{-- Bridge for the Android app: called via WebView.evaluateJavascript() with the
             device's FCM token so this device gets tied to whichever user is logged in. --}}
        <script>
            window.registerFcmToken = function(token) {
                if (!token) return;
                localStorage.setItem('jayarasa_fcm_token', token);
                fetch("{{ route('fcm.token.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ fcm_token: token })
                }).catch(function(err) {
                    console.error('FCM token registration failed', err);
                });
            };
        </script>
    </body>
</html>