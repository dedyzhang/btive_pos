<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Betive POS</title>
    
    <!-- Google Fonts: Outfit & Red Hat Text -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback Tailwind CSS & JQuery CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @endif
    
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/img/headers-icon.png') }}">
    
    <style>
        body {
            font-family: 'Outfit', 'Red Hat Text', sans-serif;
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.03); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) scale(1.03); }
            50% { transform: translateY(20px) scale(1); }
        }
        .animate-float-1 {
            animation: float-slow 15s infinite ease-in-out;
        }
        .animate-float-2 {
            animation: float-reverse 18s infinite ease-in-out;
        }
        
        /* Focus state helper */
        .glass-input-wrapper:focus-within {
            border-color: var(--color-brand, #2b66ff) !important;
            box-shadow: 0 0 0 4px rgba(43, 102, 255, 0.08) !important;
            background-color: #ffffff !important;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-100 via-indigo-50/50 to-rose-50/30 min-h-screen flex items-center justify-center p-4 relative overflow-hidden select-none">

    <!-- Glowing background blobs -->
    <div class="absolute top-1/4 left-1/4 w-[300px] md:w-[500px] h-[300px] md:h-[500px] bg-brand/10 rounded-full blur-[90px] md:blur-[130px] animate-float-1 pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[350px] md:w-[600px] h-[350px] md:h-[600px] bg-indigo-600/8 rounded-full blur-[100px] md:blur-[160px] animate-float-2 pointer-events-none"></div>

    <!-- Center Login Card -->
    <div class="relative w-full max-w-[400px] bg-white/95 backdrop-blur-md border border-slate-100 rounded-[2rem] md:rounded-[2.5rem] p-6 sm:p-8 md:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)] flex flex-col items-center z-10 overflow-hidden">
        <!-- Top brand glowing accent line -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-brand to-transparent"></div>
        
        <!-- Logo Area -->
        @php
            $loginRestaurantLogo = \App\Models\Settings::where('jenis', 'restaurant_logo')->first();
            $loginLogoUrl = ($loginRestaurantLogo && $loginRestaurantLogo->nilai)
                ? asset('storage/' . $loginRestaurantLogo->nilai)
                : Vite::asset('resources/img/logo-icon.png');
        @endphp
        <div class="relative w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-white border border-slate-100 flex items-center justify-center shadow-lg shadow-brand/10 mb-4 md:mb-5 relative group p-2.5">
            <!-- Pulsing outer ring -->
            <div class="absolute inset-0 rounded-2xl bg-brand/10 animate-ping pointer-events-none"></div>
            <img src="{{ $loginLogoUrl }}" class="w-full h-full object-contain" alt="Logo">
        </div>

        <span class="text-[9px] md:text-[10px] text-brand font-black uppercase tracking-widest mb-1">Sistem POS Kasir</span>
        <h2 class="text-2xl md:text-3xl font-black tracking-tight text-slate-800 mb-1 text-center">Selamat Datang</h2>
        <p class="text-[11px] md:text-xs text-slate-400 font-medium text-center leading-relaxed max-w-[280px]">Silakan masukkan kredensial akun kasir atau manajemen untuk masuk.</p>

        <!-- Form Submit -->
        <form class="w-full flex flex-col mt-6 md:mt-8" method="POST" action="{{ route('auth.login') }}">
            @csrf

            <!-- Custom alert for failed logins -->
            @if (session('error'))
                <div class="w-full flex items-start p-3.5 mb-4 text-[11px] md:text-xs text-rose-700 rounded-2xl bg-rose-50 border border-rose-200/80 animate-pulse" role="alert">
                    <i class="fas fa-circle-exclamation text-rose-500 text-sm mr-2 shrink-0 mt-0.5"></i>
                    <p class="leading-relaxed"><span class="font-extrabold text-rose-600">Masuk Gagal!</span> Kredensial salah.</p>
                </div>
            @endif

            <!-- Input Fields Wrapper -->
            <div class="space-y-4 md:space-y-5">
                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Username ID</label>
                    <div class="glass-input-wrapper flex items-center w-full bg-slate-50/50 border @error('username') border-rose-500/50 @else border-slate-200/80 @enderror rounded-2xl h-12 md:h-13 pl-4 md:pl-5 pr-4 gap-3 transition-all">
                        <i class="fas fa-user-shield text-xs md:text-sm @error('username') text-rose-400/80 @else text-slate-400 @enderror shrink-0"></i>
                        <input type="text" name="username" id="username" placeholder="Masukkan username Anda" class="bg-transparent text-slate-800 border-none outline-none text-xs md:text-sm w-full h-full font-semibold placeholder-slate-400 focus:ring-0" value="{{ old('username') }}" required autocomplete="off">
                    </div>
                    @error('username')
                        <p class="text-[9px] md:text-[10px] text-rose-500 font-bold mt-1.5 ml-1"><i class="fas fa-triangle-exclamation mr-1"></i>Username wajib diisi</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Password</label>
                    <div class="glass-input-wrapper flex items-center w-full bg-slate-50/50 border @error('password') border-rose-500/50 @else border-slate-200/80 @enderror rounded-2xl h-12 md:h-13 pl-4 md:pl-5 pr-11 md:pr-12 gap-3 relative transition-all">
                        <i class="fas fa-lock text-xs md:text-sm @error('password') text-rose-400/80 @else text-slate-400 @enderror shrink-0"></i>
                        <input type="password" name="password" id="password" placeholder="Masukkan password Anda" class="bg-transparent text-slate-800 border-none outline-none text-xs md:text-sm w-full h-full font-semibold placeholder-slate-400 focus:ring-0" required>
                        
                        <!-- Toggle show/hide button -->
                        <button type="button" id="btn-toggle-login-password" class="absolute inset-y-0 right-0 pr-4 md:pr-5 flex items-center text-slate-400 hover:text-brand transition-all cursor-pointer border-none outline-none select-none">
                            <i class="fas fa-eye text-xs" id="icon-toggle-login-password"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[9px] md:text-[10px] text-rose-500 font-bold mt-1.5 ml-1"><i class="fas fa-triangle-exclamation mr-1"></i>Password wajib diisi</p>
                    @enderror
                </div>
            </div>

            <!-- Remember me -->
            <div class="w-full flex items-center justify-between mt-5">
                <label for="checkbox" class="flex items-center gap-2 cursor-pointer select-none group">
                    <input class="w-3.5 h-3.5 rounded border-slate-200 bg-slate-50 text-brand focus:ring-brand/20 transition-all cursor-pointer" type="checkbox" id="checkbox" name="remember">
                    <span class="text-[11px] md:text-xs text-slate-500 font-bold group-hover:text-slate-700 transition-colors">Ingat Saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full h-12 md:h-13 bg-gradient-to-r from-brand to-indigo-600 hover:from-brand-strong hover:to-indigo-700 text-white font-extrabold rounded-2xl shadow-lg shadow-brand/20 hover:shadow-brand/35 transition-all active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2 border-none outline-none text-[11px] md:text-xs uppercase tracking-wider mt-6 md:mt-8">
                Masuk Ke Aplikasi <i class="fas fa-right-to-bracket text-xs"></i>
            </button>
        </form>

        @php
            $loginApkSetting = \App\Models\Settings::where('jenis', 'app_apk')->first();
            $loginApkData = $loginApkSetting && $loginApkSetting->nilai ? (@unserialize($loginApkSetting->nilai) ?: []) : [];
        @endphp
        @if(!empty($loginApkData['filename']))
            <a href="{{ route('app.download') }}" class="w-full h-11 md:h-12 mt-3 bg-white border border-slate-200 hover:border-brand/40 hover:bg-brand-soft text-slate-600 hover:text-brand font-bold rounded-2xl transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-[11px] md:text-xs uppercase tracking-wider">
                <i class="fab fa-android text-sm"></i> Unduh Aplikasi Android
            </a>
        @endif
    </div>

    <!-- Password visibility toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnToggle = document.getElementById('btn-toggle-login-password');
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('icon-toggle-login-password');

            if (btnToggle && passwordInput && icon) {
                btnToggle.addEventListener('click', function() {
                    const showing = passwordInput.type === 'text';
                    passwordInput.type = showing ? 'password' : 'text';
                    icon.classList.toggle('fa-eye', showing);
                    icon.classList.toggle('fa-eye-slash', !showing);
                });
            }
        });
    </script>
</body>
</html>