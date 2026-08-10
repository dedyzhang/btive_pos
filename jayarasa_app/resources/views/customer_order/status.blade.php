<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Status Pesanan - {{ $resName }}</title>
    
    <!-- Red Hat Text Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Load CSS/JS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback styles -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <style>
            body { font-family: 'Red Hat Text', sans-serif; background-color: #f4f7fc; }
        </style>
    @endif

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
        .step-active .step-icon {
            background-color: var(--color-brand) !important;
            color: white !important;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-brand) 15%, transparent) !important;
        }
        .step-active .step-label {
            color: var(--color-brand) !important;
            font-weight: 800 !important;
        }
        .step-completed .step-icon {
            background-color: #10b981 !important;
            color: white !important;
        }
        .step-completed .step-label {
            color: #10b981 !important;
            font-weight: 600 !important;
        }
        .pulse-animation {
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(43, 102, 255, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(43, 102, 255, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(43, 102, 255, 0); }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-brand-soft pb-12">
    <header class="text-white rounded-b-[2.5rem] shadow-lg px-6 pt-5 pb-5 relative overflow-hidden select-none" style="background: linear-gradient(135deg, var(--color-brand), var(--color-brand-strong));">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full blur-lg"></div>

        <div class="flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                @if($imgPath)
                    <img src="{{ $imgPath }}" class="w-10 h-10 object-contain rounded-full bg-white p-0.5 border-2 border-white/20 shadow-md" alt="Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';" />
                    <div id="logo-fallback" class="hidden w-10 h-10 rounded-full bg-white/10 flex items-center justify-center border-2 border-white/20 shadow-md">
                        <i class="fas fa-utensils text-sm"></i>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center border-2 border-white/20 shadow-md">
                        <i class="fas fa-utensils text-sm"></i>
                    </div>
                @endif
                <h1 class="text-base font-extrabold tracking-wide">{{ $resName }}</h1>
            </div>
            
            <div class="bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-2xl border border-white/10 text-center shadow-inner min-w-[80px]">
                <span class="text-[9px] uppercase tracking-widest block font-bold text-blue-200 mb-0.5">Meja</span>
                <span class="text-2xl font-black tracking-tight font-mono leading-none">{{ $transaction->table ? $transaction->table->name : '-' }}</span>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-md mx-auto px-4 mt-6 relative z-10">
        <!-- Live Tracker Title -->
        <div class="text-center mb-6">
            <span class="text-[10px] bg-brand-soft text-brand px-3 py-1 rounded-full font-bold uppercase tracking-wider shadow-sm">Live Order Tracker</span>
            <h2 class="text-xl font-extrabold mt-3 tracking-tight text-gray-800">Status Pesanan Anda</h2>
            <p class="text-xs text-gray-450 mt-1">Halaman ini diperbarui otomatis setiap 5 detik</p>
        </div>

        <!-- Tracker Card -->
        <div class="bg-white rounded-[2rem] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100/50 mb-6">
            
            <!-- Progress Status Bar (Vertical Steps) -->
            <div class="relative pl-8 space-y-8 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-[2px] before:bg-gray-150">
                
                <!-- Step 1: Received -->
                <div class="relative flex gap-4 items-start step-node" id="step-received">
                    <div class="step-icon absolute -left-8 w-7 h-7 rounded-full bg-gray-100 text-gray-400 border-2 border-white flex items-center justify-center text-xs z-10 transition-all duration-300">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="step-label text-xs font-bold text-gray-450 uppercase tracking-wider transition-colors duration-300">Pesanan Diterima</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Pesanan Anda telah masuk ke sistem restoran.</p>
                    </div>
                </div>

                <!-- Step 2: Cooking -->
                <div class="relative flex gap-4 items-start step-node" id="step-cooking">
                    <div class="step-icon absolute -left-8 w-7 h-7 rounded-full bg-gray-100 text-gray-400 border-2 border-white flex items-center justify-center text-xs z-10 transition-all duration-300">
                        <i class="fas fa-fire-burner"></i>
                    </div>
                    <div>
                        <h3 class="step-label text-xs font-bold text-gray-450 uppercase tracking-wider transition-colors duration-300">Sedang Dimasak</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Koki kami sedang mempersiapkan hidangan lezat Anda.</p>
                    </div>
                </div>

                <!-- Step 3: Ready -->
                <div class="relative flex gap-4 items-start step-node" id="step-ready">
                    <div class="step-icon absolute -left-8 w-7 h-7 rounded-full bg-gray-100 text-gray-400 border-2 border-white flex items-center justify-center text-xs z-10 transition-all duration-300">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h3 class="step-label text-xs font-bold text-gray-450 uppercase tracking-wider transition-colors duration-300">Siap Disajikan</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Selamat menikmati!</p>
                    </div>
                </div>

                <!-- Step 4: Finished/Paid -->
                <div class="relative flex gap-4 items-start step-node" id="step-completed">
                    <div class="step-icon absolute -left-8 w-7 h-7 rounded-full bg-gray-100 text-gray-400 border-2 border-white flex items-center justify-center text-xs z-10 transition-all duration-300">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <div>
                        <h3 class="step-label text-xs font-bold text-gray-450 uppercase tracking-wider transition-colors duration-300">Selesai</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Transaksi selesai. Terima kasih atas kunjungan Anda!</p>
                    </div>
                </div>
            </div>

            <!-- Ready banner callout (hidden by default) -->
            <div id="ready-banner" class="hidden mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 animate-pulse text-emerald-800">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <i class="fas fa-circle-check text-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-extrabold">Pesanan Sudah Selesai!</h4>
                    <p class="text-[10px] text-emerald-700 mt-0.5">Selamat menikmati!</p>
                </div>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white rounded-[2rem] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100/50">
            <h3 class="text-xs font-extrabold text-gray-700 uppercase tracking-wider border-b border-gray-100 pb-3 mb-4">Rincian Pesanan</h3>
            
            <div class="space-y-1.5 text-xs mb-4 text-gray-500 font-medium">
                <div class="flex justify-between">
                    <span>ID Transaksi:</span>
                    <span class="font-mono text-gray-700 font-semibold">{{ $transaction->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Nama Pelanggan:</span>
                    <span class="text-gray-700 font-bold">{{ $transaction->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Waktu Pesan:</span>
                    <span class="text-gray-700 font-semibold">{{ $transaction->created_at->format('H:i') }} WIB</span>
                </div>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-gray-100 border-t border-b border-gray-100 py-3 mb-4">
                @foreach($transaction->orderItem as $item)
                    <div class="flex justify-between items-start py-2">
                        <div class="max-w-[250px]">
                            <h4 class="text-xs font-bold text-gray-800 leading-tight">{{ $item->qty }}x {{ $item->product_name }}</h4>
                            @if($item->note)
                                <p class="text-[9px] text-gray-400 mt-0.5 italic"><i class="far fa-note-sticky mr-0.5"></i> "{{ $item->note }}"</p>
                            @endif
                        </div>
                        <span class="text-xs font-semibold text-gray-600 font-mono">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Total payment summary -->
            <div class="space-y-1.5 text-xs text-gray-500 font-medium mb-3">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-mono text-gray-700 font-semibold">Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak (Tax):</span>
                    <span class="font-mono text-gray-700 font-semibold">Rp{{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm font-extrabold text-gray-800 pt-2 border-t border-dashed border-gray-100">
                    <span>Total Tagihan:</span>
                    <span class="text-brand font-mono text-base">Rp{{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider leading-relaxed"><i class="fas fa-circle-exclamation mr-1"></i>Tunjukkan Halaman Ini ke Kasir Saat Melakukan Pembayaran</p>
            </div>
        </div>
    </main>

    <!-- Polling script -->
    <script type="module">
        $(document).ready(function() {
            const uuid = '{{ $transaction->uuid }}';
            const pollingUrl = '{{ route("customer.order.liveStatus", ":uuid") }}'.replace(':uuid', uuid);
            
            // Set initial steps visual based on values
            updateStatusVisuals('{{ $transaction->status }}', '{{ $transaction->kitchen_status }}');

            // Set interval for live polling (5 seconds)
            const intervalId = setInterval(checkLiveStatus, 5000);

            function checkLiveStatus() {
                $.ajax({
                    type: "GET",
                    url: pollingUrl,
                    success: function(response) {
                        if (response.success) {
                            updateStatusVisuals(response.status, response.kitchen_status);
                            
                            // If transaction is fully completed (paid), you can stop polling after a short delay
                            if (response.status === 'paid') {
                                clearInterval(intervalId);
                            }
                        }
                    },
                    error: function(err) {
                        console.error("Live status check error:", err);
                    }
                });
            }

            function updateStatusVisuals(status, kitchenStatus) {
                // Clear all state classes
                $('.step-node').removeClass('step-active step-completed');
                $('.step-icon').removeClass('pulse-animation');
                $('#ready-banner').addClass('hidden');

                // Step 1: Received is always completed
                $('#step-received').addClass('step-completed');

                if (status === 'paid') {
                    // All completed
                    $('#step-received').addClass('step-completed');
                    $('#step-cooking').addClass('step-completed');
                    $('#step-ready').addClass('step-completed');
                    $('#step-completed').addClass('step-completed');
                    return;
                }

                // If not paid, map using kitchenStatus
                if (kitchenStatus === 'cooking') {
                    $('#step-cooking').addClass('step-active');
                    $('#step-cooking .step-icon').addClass('pulse-animation');
                } else if (kitchenStatus === 'ready') {
                    $('#step-cooking').addClass('step-completed');
                    $('#step-ready').addClass('step-active');
                    $('#step-ready .step-icon').addClass('pulse-animation');
                    $('#ready-banner').removeClass('hidden');
                } else {
                    // If it is still pending in queue
                    $('#step-received').addClass('step-active');
                    $('#step-received .step-icon').addClass('pulse-animation');
                }
            }
        });
    </script>
</body>
</html>
