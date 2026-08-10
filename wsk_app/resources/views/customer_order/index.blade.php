<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $resName }} - Menu Pemesanan Mandiri</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        .category-pill-active {
            background-color: var(--color-brand, #2b66ff);
            color: white;
            box-shadow: 0 4px 12px rgba(43, 102, 255, 0.25);
        }
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .custom-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .product-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card:active {
            transform: scale(0.97);
        }
        /* Mobile Cart Drawer Animation */
        #cart-drawer {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(2deg); }
        }
        .animate-float {
            animation: float 3s infinite ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-brand-soft pb-28">
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 pointer-events-none flex flex-col gap-2 w-full max-w-[320px] px-4"></div>

    <!-- Welcome Screen Overlay -->
    <div id="welcome-screen" class="fixed inset-0 z-50 text-white flex flex-col justify-between p-8 select-none transition-all duration-500 opacity-100" style="background: linear-gradient(135deg, var(--color-brand), var(--color-brand-strong), color-mix(in srgb, var(--color-brand) 30%, black));">
        <!-- Floating shapes for premium look -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full blur-3xl -ml-20 -mb-20"></div>

        <!-- Top Row (Table Number Only - Large) -->
        <div class="flex flex-col items-center justify-center relative z-10 w-full pt-4">
            <span class="text-[11px] uppercase tracking-widest font-black text-white/60">Nomor Meja Anda</span>
            <span class="text-3xl font-black tracking-wide font-mono text-white mt-2 bg-white/10 px-8 py-3.5 rounded-full border border-white/20 shadow-inner leading-none">{{ $table->name }}</span>
        </div>

        <!-- Middle Row (Greeting & Animated Plate) -->
        <div class="my-auto text-center relative z-10 flex flex-col items-center">
            
            <!-- Animated Plate Icon Wrapper -->
            <div class="relative w-36 h-36 flex items-center justify-center mb-8">
                <!-- Pulsing expansion rings (Ripple effect) -->
                <div class="absolute inset-0 rounded-full bg-white/10 animate-ping opacity-75" style="animation-duration: 3s;"></div>
                <div class="absolute w-28 h-28 rounded-full bg-white/5 animate-pulse"></div>
                
                <!-- Slow rotating border ring -->
                <div class="absolute w-28 h-28 rounded-full border-2 border-dashed border-white/20 animate-spin" style="animation-duration: 15s;"></div>
                
                <!-- Main glassmorphic circle -->
                <div class="relative w-24 h-24 rounded-full bg-white/10 border-2 border-white/25 flex items-center justify-center shadow-2xl backdrop-blur-md">
                    <!-- Floating Plate Container -->
                    <div class="animate-float flex items-center justify-center w-full h-full">
                        <!-- Plate (white circle) -->
                        <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-xl border border-white/20 p-1 relative overflow-hidden">
                            @if($imgPath)
                                <img src="{{ $imgPath }}" class="w-full h-full object-contain rounded-full" alt="Restaurant Logo" />
                            @else
                                <!-- Fallback Crossed Fork & Spoon -->
                                <i class="fas fa-utensils text-2xl text-brand"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <span class="text-[10px] bg-white/25 px-3.5 py-1 rounded-full font-bold uppercase tracking-widest text-blue-100 mb-4 border border-white/10">Self-Order System</span>
            <h2 class="text-3xl font-black tracking-tight leading-tight mb-3">Selamat Datang di<br>{{ $resName }}</h2>
            <p class="text-xs text-blue-100 max-w-xs leading-relaxed opacity-95">
                Silakan pesan makanan & minuman favorit Anda secara langsung dari meja ini. Pesanan Anda akan otomatis diteruskan ke dapur.
            </p>
        </div>

        <!-- Bottom Row (Action Button) -->
        <div class="text-center relative z-10 pb-4">
            <button type="button" id="btn-start-order" class="w-full bg-white text-brand hover:text-brand-strong hover:bg-blue-50 font-black py-4 px-6 rounded-2xl shadow-2xl shadow-brand-strong/30 transition-all duration-300 transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer border-none outline-none text-sm uppercase tracking-wider">
                Mulai Memesan <i class="fas fa-arrow-right text-xs"></i>
            </button>
            <p class="text-[10px] text-blue-200 mt-4 font-semibold tracking-wide uppercase leading-none opacity-75">Sajikan Kelezatan Instan Dari Meja Anda</p>
        </div>
    </div>

    <header class="text-white rounded-b-[2.5rem] shadow-lg px-6 pt-5 pb-5 relative overflow-hidden select-none" style="background: linear-gradient(135deg, var(--color-brand), var(--color-brand-strong));">
        <!-- Floating shapes -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full blur-lg"></div>

        <div class="flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                @if($imgPath)
                    <img src="{{ $imgPath }}" class="w-12 h-12 object-contain rounded-full bg-white p-1 border-2 border-white/20 shadow-md" alt="Logo" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex';" />
                    <div id="logo-fallback" class="hidden w-12 h-12 rounded-full bg-white/10 flex items-center justify-center border-2 border-white/20 shadow-md">
                        <i class="fas fa-utensils text-xl"></i>
                    </div>
                @else
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center border-2 border-white/20 shadow-md">
                        <i class="fas fa-utensils text-xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-extrabold tracking-wide leading-tight">{{ $resName }}</h1>
                    @if($resLocation)
                        <p class="text-[9px] opacity-80 font-medium truncate max-w-[170px] mt-0.5"><i class="fas fa-map-marker-alt text-[8px] mr-0.5"></i> {{ $resLocation }}</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-2xl border border-white/10 text-center shadow-inner min-w-[80px]">
                <span class="text-[9px] uppercase tracking-widest block font-bold text-blue-200 mb-0.5">Meja</span>
                <span class="text-2xl font-black tracking-tight font-mono leading-none">{{ $table->name }}</span>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-md mx-auto px-4 mt-6">
        <!-- Search Input -->
        <div class="relative w-full mb-6">
            <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none text-gray-400">
                <i class="fas fa-search"></i>
            </div>
            <input type="text" id="menu-search" class="bg-white shadow-[0_4px_16px_rgba(0,0,0,0.03)] border border-gray-100/50 w-full ps-11 pe-4 py-3.5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition-all placeholder-gray-400 text-sm font-medium" placeholder="Cari makanan atau minuman lezat...">
        </div>

        <!-- Categories horizontal list -->
        <div class="w-full overflow-x-auto custom-scrollbar mb-6 select-none" id="categories-scroll">
            <div class="flex gap-2.5 pb-2">
                <button type="button" class="category-pill category-pill-active shrink-0 px-4 py-2.5 rounded-full text-xs font-bold border border-transparent bg-white shadow-[0_2px_8px_rgba(0,0,0,0.02)] text-gray-500 hover:bg-gray-50 transition-all cursor-pointer" data-category="all">
                    <i class="fas fa-border-all mr-1.5"></i>Semua Menu
                </button>
                <button type="button" class="category-pill shrink-0 px-4 py-2.5 rounded-full text-xs font-bold border border-transparent bg-white shadow-[0_2px_8px_rgba(0,0,0,0.02)] text-gray-500 hover:bg-gray-50 transition-all cursor-pointer" data-category="best-seller">
                    <i class="fas fa-crown mr-1.5 text-amber-500"></i>Terlaris
                </button>
                @foreach($categories as $category)
                    <button type="button" class="category-pill shrink-0 px-4 py-2.5 rounded-full text-xs font-bold border border-transparent bg-white shadow-[0_2px_8px_rgba(0,0,0,0.02)] text-gray-500 hover:bg-gray-50 transition-all cursor-pointer" data-category="{{ $category->uuid }}">
                        @if($category->icon)
                            <i class="fas {{ $category->icon }} mr-1.5 text-brand"></i>
                        @endif
                        {{ $category->nama }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Menu Section Header -->
        <h2 id="section-title" class="text-base font-extrabold text-gray-700 uppercase tracking-wider mb-4">Daftar Menu</h2>

        <!-- Products Grid List -->
        <div class="grid grid-cols-2 gap-3" id="products-list">
            @foreach($products as $product)
                @php
                    $isBestSeller = in_array($product->uuid, $bestSellerProductIds);
                @endphp
                <div class="product-card col-span-1 bg-white rounded-3xl p-3 shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-gray-100/50 flex flex-col justify-between relative cursor-pointer group" 
                     data-uuid="{{ $product->uuid }}" 
                     data-name="{{ $product->name }}" 
                     data-price="{{ $product->price }}" 
                     data-category="{{ $product->category_id }}" 
                     data-best-seller="{{ $isBestSeller ? '1' : '0' }}">
                    
                    <div class="product-image-wrapper cursor-zoom-in relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-3 bg-gray-50">
                        @if($isBestSeller)
                            <div class="absolute top-1.5 left-1.5 bg-gradient-to-r from-amber-500 to-yellow-400 text-white font-extrabold px-2 py-0.5 rounded-lg text-[8px] shadow-sm flex items-center gap-1 z-10 select-none tracking-wider pointer-events-none">
                                <i class="fas fa-crown text-[7px]"></i>
                                <span>TERLARIS</span>
                            </div>
                        @endif
                        <img src="{{ $product->picture == "" ? Vite::asset('resources/img/no_image_available.png') : asset('storage/products/'.$product->picture) }}" class="product-preview-img w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300" alt="{{ $product->name }}" />
                        <div class="absolute inset-0 bg-black/15 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <div class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center text-gray-800 shadow-md transform scale-90 group-hover:scale-100 transition-all duration-300">
                                <i class="fas fa-magnifying-glass-plus text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-gray-800 truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h3>
                        <p class="text-[10px] text-gray-400 font-semibold mb-2">{{ $product->category ? $product->category->nama : 'Uncategorized' }}</p>
                    </div>

                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm font-extrabold text-brand tracking-tight">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        <button type="button" class="btn-add-to-cart w-8 h-8 rounded-full bg-brand-soft text-brand hover:bg-brand hover:text-white flex items-center justify-center transition-all cursor-pointer border-none outline-none">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State Search -->
        <div id="empty-state" class="hidden text-center py-12 px-4">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4 text-xl">
                <i class="fas fa-magnifying-glass"></i>
            </div>
            <h3 class="font-bold text-gray-700 text-sm">Menu tidak ditemukan</h3>
            <p class="text-xs text-gray-400 mt-1">Cobalah mencari dengan kata kunci yang lain</p>
        </div>
    </main>

    <!-- Floating Cart Bar (Sticky Bottom) -->
    <div id="floating-cart" class="fixed bottom-0 left-0 right-0 p-4 bg-transparent z-40 max-w-md mx-auto pointer-events-none hidden">
        <button type="button" id="btn-open-cart" class="pointer-events-auto w-full text-white rounded-3xl py-4 px-5 shadow-2xl flex items-center justify-between transition-all duration-300 transform hover:scale-[1.01] cursor-pointer border-none outline-none" style="background: linear-gradient(135deg, var(--color-brand), var(--color-brand-strong));">
            <div class="flex items-center gap-3">
                <div class="relative w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center shadow-inner">
                    <i class="fas fa-shopping-bag text-sm"></i>
                    <span id="cart-badge" class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white font-extrabold w-5 h-5 rounded-full flex items-center justify-center text-[9px] shadow-md border-2 border-white">0</span>
                </div>
                <div class="text-left">
                    <span class="text-[9px] block uppercase tracking-wider font-bold text-blue-100">Keranjang Belanja</span>
                    <span id="cart-item-count" class="text-xs font-bold block leading-tight">0 Item Terpilih</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="cart-grand-total" class="text-sm font-extrabold tracking-tight">Rp0</span>
                <i class="fas fa-chevron-right text-xs opacity-75"></i>
            </div>
        </button>
    </div>

    <!-- Product Detail & Order Modal (Unified) -->
    <div id="product-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/75 backdrop-blur-[3px] flex">
        <div class="relative w-full max-w-sm bg-white rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col m-4 animate-scale-up">
            
            <!-- Close Button (Absolute on card) -->
            <button type="button" class="btn-close-modal absolute top-4 right-4 text-gray-400 hover:text-gray-700 hover:bg-gray-100 text-sm w-9 h-9 rounded-full flex items-center justify-center cursor-pointer border-none outline-none bg-white/95 backdrop-blur-md shadow-md z-30 transition-all duration-200">
                <i class="fas fa-xmark text-base"></i>
            </button>
            
            <!-- Product Image Area -->
            <div class="w-full aspect-[4/3] bg-gray-50 relative overflow-hidden flex items-center justify-center border-b border-gray-100">
                <img id="modal-product-img" src="" class="w-full h-full object-cover" alt="Product Image" />
            </div>
            
            <!-- Details & Actions Area -->
            <div class="p-6 flex flex-col">
                <!-- Title and Price Row -->
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="max-w-[70%]">
                        <h4 id="modal-product-name" class="text-base font-black text-gray-800 leading-tight">Nama Menu</h4>
                        <span id="modal-product-category" class="text-[9px] bg-brand-soft text-brand font-bold px-2 py-0.5 rounded-md mt-1.5 inline-block uppercase tracking-wider">Kategori</span>
                    </div>
                    <span id="modal-product-price" class="text-lg font-black text-brand tracking-tight font-mono">Rp0</span>
                </div>
                
                <!-- Qty Counter -->
                <div class="mb-4 text-center">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Jumlah Pesanan</label>
                    <div class="inline-flex items-center gap-4 bg-gray-50 rounded-2xl p-1.5 border border-gray-200/50">
                        <button type="button" id="btn-modal-dec" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center font-bold hover:bg-gray-100 transition-all cursor-pointer text-gray-600 border-none outline-none">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span id="modal-qty" class="text-lg font-extrabold font-mono w-12 select-none">1</span>
                        <button type="button" id="btn-modal-inc" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center font-bold hover:bg-gray-100 transition-all cursor-pointer text-gray-600 border-none outline-none">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Note Text Area -->
                <div class="mb-5">
                    <label for="modal-note" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea id="modal-note" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand/40 focus:bg-white focus:border-brand transition-all text-gray-700" placeholder="Contoh: pedas sedang, es sedikit, pisah kuah..."></textarea>
                </div>
                
                <!-- Action Button -->
                <button type="button" id="btn-modal-submit" class="w-full text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-brand/20 transition-all flex items-center justify-center gap-2 cursor-pointer border-none outline-none" style="background: linear-gradient(135deg, var(--color-brand), var(--color-brand-strong));">
                    Tambahkan Ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- Cart Drawer Modal (Right/Bottom Overlay) -->
    <div id="cart-drawer-overlay" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-[2px] hidden transition-opacity duration-300 opacity-0"></div>
    <div id="cart-drawer" class="fixed bottom-0 md:top-0 left-0 right-0 md:left-auto md:right-0 mx-auto md:mx-0 w-full max-w-md bg-white rounded-t-[2.5rem] md:rounded-t-none md:rounded-l-[2.5rem] shadow-2xl z-50 flex flex-col justify-between max-h-[85vh] md:max-h-screen translate-y-full md:translate-y-0 md:translate-x-full">
        <!-- Header -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-brand-soft text-brand flex items-center justify-center text-sm shadow-sm">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">Detail Keranjang</h3>
            </div>
            <button type="button" id="btn-close-cart" class="text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none outline-none bg-transparent">
                <i class="fas fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Scrollable Order Items List -->
        <div class="p-5 flex-grow overflow-y-auto space-y-4" id="cart-items-container">
            <!-- Dynamic Items -->
        </div>

        <!-- Sticky Footer (Summary & Submit) -->
        <div class="p-5 border-t border-gray-100 bg-gray-50 rounded-b-none md:rounded-bl-[2.5rem] shrink-0">
            <!-- Customer Name Input -->
            <div class="mb-4">
                <label for="customer_name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Pemesan <span class="text-red-500">*</span></label>
                <input type="text" id="customer_name" class="w-full px-3.5 py-3 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand/40 focus:border-brand transition-all font-semibold text-gray-700 bg-white" placeholder="Masukkan nama Anda (misal: Budi)" required>
            </div>

            <!-- Tax Summary -->
            <div class="space-y-1.5 mb-5 text-xs text-gray-600">
                <div class="flex justify-between font-semibold">
                    <span>Subtotal:</span>
                    <span id="summary-subtotal" class="font-mono text-gray-700">Rp0</span>
                </div>
                <div class="flex justify-between font-semibold">
                    <span>Pajak (Tax):</span>
                    <span id="summary-tax" class="font-mono text-gray-700">Rp0</span>
                </div>
                <div class="border-t border-dashed border-gray-200 my-2 pt-2 flex justify-between items-center text-sm">
                    <span class="font-extrabold text-gray-700">Total Tagihan:</span>
                    <span id="summary-grand-total" class="font-extrabold text-brand font-mono text-lg tracking-tight">Rp0</span>
                </div>
            </div>

            <button type="button" id="btn-submit-order" class="w-full text-white font-bold py-4 px-4 rounded-2xl shadow-lg shadow-brand/20 transition-all flex items-center justify-center gap-2 cursor-pointer border-none outline-none" style="background: linear-gradient(135deg, var(--color-brand), var(--color-brand-strong));">
                <i class="fas fa-paper-plane text-sm"></i> Kirim Pesanan Ke Dapur
            </button>
        </div>
    </div>

    <!-- Script Logika Menu & Keranjang Belanja -->
    <script type="module">
        $(document).ready(function() {
            // Cart Data Structure
            // [{product_id, name, price, qty, note, img}]
            let cart = [];
            
            const tableUuid = '{{ $table->uuid }}';
            const welcomeKey = 'welcome_seen_' + tableUuid;

            if (sessionStorage.getItem(welcomeKey)) {
                $('#welcome-screen').addClass('hidden');
            } else {
                $('body').addClass('overflow-hidden');
            }

            // Start Order action (dismiss welcome screen)
            $('#btn-start-order').on('click', function() {
                $('body').removeClass('overflow-hidden');
                $('#welcome-screen').addClass('opacity-0');
                sessionStorage.setItem(welcomeKey, '1');
                
                setTimeout(() => {
                    $('#welcome-screen').addClass('hidden');
                }, 500);
            });
            
            // Load cart from localStorage if exists
            const savedCart = localStorage.getItem('qr_cart');
            if (savedCart) {
                try {
                    cart = JSON.parse(savedCart);
                    updateCartUI();
                } catch(e) {
                    cart = [];
                }
            }

            // Tax Rate from backend settings (read if possible or default)
            const taxRate = parseFloat('{{ \App\Models\Settings::where("jenis","payment_tax")->first()->nilai ?? 0 }}') || 0;

            // Toast Notification Helper
            function showToast(message) {
                const toastId = 'toast-' + Date.now();
                const toastHtml = `
                    <div id="${toastId}" class="bg-gray-950/90 backdrop-blur-md text-white text-xs font-bold py-3.5 px-4 rounded-2xl shadow-xl flex items-center gap-2.5 transition-all duration-300 transform -translate-y-4 opacity-0 border border-white/10">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] shrink-0">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="flex-grow leading-tight">${message}</span>
                    </div>
                `;
                $('#toast-container').append(toastHtml);
                
                // Animate entry
                setTimeout(() => {
                    $(`#${toastId}`).removeClass('-translate-y-4 opacity-0').addClass('translate-y-0 opacity-100');
                }, 10);
                
                // Auto remove after 1.5 seconds
                setTimeout(() => {
                    $(`#${toastId}`).removeClass('translate-y-0 opacity-100').addClass('-translate-y-4 opacity-0');
                    setTimeout(() => {
                        $(`#${toastId}`).remove();
                    }, 300);
                }, 1500);
            }

            // Audio Synthesis Helper (Pop Beep Sound)
            let audioCtx = null;

            function initAudio() {
                if (audioCtx) return;
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (AudioContext) {
                    audioCtx = new AudioContext();
                }
            }

            function unlockAudio() {
                initAudio();
                if (audioCtx && audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
            }

            // Unlock Audio Context on first user gestures (clicks or touches)
            $(document).on('click touchstart', '#btn-start-order, .product-card, .btn-add-to-cart, #btn-modal-submit', function() {
                unlockAudio();
            });

            function playCartSound() {
                try {
                    initAudio();
                    if (!audioCtx) return;
                    
                    if (audioCtx.state === 'suspended') {
                        audioCtx.resume();
                    }
                    
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    
                    osc.type = 'sine';
                    
                    osc.frequency.setValueAtTime(450, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(900, audioCtx.currentTime + 0.12);
                    
                    gain.gain.setValueAtTime(0.8, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
                    
                    osc.start(audioCtx.currentTime);
                    osc.stop(audioCtx.currentTime + 0.16);
                } catch (e) {
                    console.log("AudioContext blocked or unsupported:", e);
                }
            }

            // Search product logic
            $('#menu-search').on('input', function() {
                filterMenu();
            });

            // Category filter logic
            $(document).on('click', '.category-pill', function() {
                $('.category-pill').removeClass('category-pill-active');
                $(this).addClass('category-pill-active');
                filterMenu();
            });

            function filterMenu() {
                const searchVal = $('#menu-search').val().toLowerCase().trim();
                const activeCategory = $('.category-pill-active').data('category');
                
                let matchesCount = 0;
                
                $('.product-card').each(function() {
                    const name = $(this).data('name').toLowerCase();
                    const category = $(this).data('category');
                    const isBestSeller = $(this).data('best-seller') === 1;
                    
                    let searchMatch = name.includes(searchVal);
                    let categoryMatch = false;
                    
                    if (activeCategory === 'all') {
                        categoryMatch = true;
                    } else if (activeCategory === 'best-seller') {
                        categoryMatch = isBestSeller;
                    } else {
                        categoryMatch = category === activeCategory;
                    }
                    
                    if (searchMatch && categoryMatch) {
                        $(this).removeClass('hidden');
                        matchesCount++;
                    } else {
                        $(this).addClass('hidden');
                    }
                });

                // Update section header name
                const activeText = $('.category-pill-active').text().trim();
                $('#section-title').text(searchVal ? 'Hasil Pencarian' : activeText);
                
                if (matchesCount === 0) {
                    $('#products-list').addClass('hidden');
                    $('#empty-state').removeClass('hidden');
                } else {
                    $('#products-list').removeClass('hidden');
                    $('#empty-state').addClass('hidden');
                }
            }

            // Open Add to Cart modal (Unified)
            let selectedProduct = null;
            let currentModalQty = 1;

            $(document).on('click', '.product-card, .btn-add-to-cart, .product-image-wrapper', function(e) {
                // If "+" button or image was clicked, prevent duplicate event bubbling
                if ($(e.target).closest('.btn-add-to-cart').length || $(e.target).closest('.product-image-wrapper').length) {
                    e.stopPropagation();
                }

                const card = $(this).closest('.product-card');
                const uuid = card.data('uuid');
                const name = card.data('name');
                const price = parseInt(card.data('price')) || 0;
                const category = card.find('.text-gray-400').text().trim() || 'Menu';
                const img = card.find('img').attr('src');

                selectedProduct = { uuid, name, price, img };
                
                // Reset Modal fields
                currentModalQty = 1;
                $('#modal-qty').text(currentModalQty);
                $('#modal-note').val('');
                
                $('#modal-product-name').text(name);
                $('#modal-product-category').text(category);
                $('#modal-product-price').text(formatRupiah(price));
                $('#modal-product-img').attr('src', img);

                // Show Modal
                $('#product-modal').removeClass('hidden');
            });

            // Close Modal
            $(document).on('click', '.btn-close-modal, #product-modal', function(e) {
                if (e.target === this || $(e.target).closest('.btn-close-modal').length) {
                    $('#product-modal').addClass('hidden');
                }
            });

            // Inc/Dec modal qty
            $('#btn-modal-inc').on('click', function() {
                currentModalQty++;
                $('#modal-qty').text(currentModalQty);
            });

            $('#btn-modal-dec').on('click', function() {
                if (currentModalQty > 1) {
                    currentModalQty--;
                    $('#modal-qty').text(currentModalQty);
                }
            });

            // Add from modal to cart array
            $('#btn-modal-submit').on('click', function() {
                const note = $('#modal-note').val().trim();
                
                // Check if product already exists in cart with same note
                const existingIdx = cart.findIndex(item => item.product_id === selectedProduct.uuid && item.note === note);
                
                if (existingIdx !== -1) {
                    cart[existingIdx].qty += currentModalQty;
                } else {
                    cart.push({
                        product_id: selectedProduct.uuid,
                        name: selectedProduct.name,
                        price: selectedProduct.price,
                        qty: currentModalQty,
                        note: note,
                        img: selectedProduct.img
                    });
                }

                // Save to localStorage & update UI
                localStorage.setItem('qr_cart', JSON.stringify(cart));
                updateCartUI();

                // Close Modal
                $('#product-modal').addClass('hidden');

                // Visual Toast Feedback & Sound
                showToast(`${selectedProduct.name} dimasukkan ke keranjang.`);
                playCartSound();
            });

            // Cart Drawer Open/Close Logic
            $('#btn-open-cart').on('click', function() {
                renderCartItems();
                
                $('#cart-drawer-overlay').removeClass('hidden');
                setTimeout(() => {
                    $('#cart-drawer-overlay').addClass('opacity-100').removeClass('opacity-0');
                }, 10);
                
                $('#cart-drawer').removeClass('translate-y-full md:translate-x-full');
            });

            $(document).on('click', '#btn-close-cart, #cart-drawer-overlay', function() {
                $('#cart-drawer-overlay').removeClass('opacity-100').addClass('opacity-0');
                $('#cart-drawer').addClass('translate-y-full md:translate-x-full');
                
                setTimeout(() => {
                    $('#cart-drawer-overlay').addClass('hidden');
                }, 300);
            });

            function formatRupiah(num) {
                return 'Rp' + num.toLocaleString('id-ID');
            }

            function updateCartUI() {
                let totalItems = 0;
                let subtotal = 0;

                cart.forEach(item => {
                    totalItems += item.qty;
                    subtotal += item.price * item.qty;
                });

                const tax = (subtotal * taxRate) / 100;
                const grandTotal = subtotal + tax;

                // Update badge and text
                $('#cart-badge').text(totalItems);
                $('#cart-item-count').text(totalItems + ' Menu Terpilih');
                $('#cart-grand-total').text(formatRupiah(grandTotal));

                if (totalItems > 0) {
                    $('#floating-cart').removeClass('hidden');
                } else {
                    $('#floating-cart').addClass('hidden');
                }
            }

            function renderCartItems() {
                const container = $('#cart-items-container');
                container.empty();

                if (cart.length === 0) {
                    container.html(`
                        <div class="text-center py-16 text-gray-400">
                            <i class="fas fa-shopping-basket text-4xl mb-3"></i>
                            <p class="text-xs font-semibold">Keranjang belanja Anda masih kosong</p>
                        </div>
                    `);
                    
                    $('#summary-subtotal').text(formatRupiah(0));
                    $('#summary-tax').text(formatRupiah(0));
                    $('#summary-grand-total').text(formatRupiah(0));
                    return;
                }

                let subtotal = 0;

                cart.forEach((item, idx) => {
                    const itemTotal = item.price * item.qty;
                    subtotal += itemTotal;

                    const rowHtml = `
                        <div class="flex items-center justify-between gap-3 p-2 bg-gray-50 border border-gray-100 rounded-xl hover:bg-gray-100/50 transition-all mb-2">
                            <div class="flex items-center gap-2.5 min-w-0 flex-grow">
                                <img src="${item.img}" class="w-10 h-10 object-cover rounded-lg border border-gray-200 shrink-0" alt="${item.name}">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-gray-800 truncate leading-tight">${item.name}</h4>
                                    <span class="text-[10px] font-extrabold text-brand mt-0.5 block">${formatRupiah(item.price)}</span>
                                    ${item.note ? `<p class="text-[8px] text-gray-400 font-semibold mt-0.5 truncate leading-none italic"><i class="far fa-note-sticky mr-0.5"></i>"${item.note}"</p>` : ''}
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Qty Selector -->
                                <div class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                                    <button type="button" class="btn-dec-cart w-6 h-6 rounded-md flex items-center justify-center hover:bg-gray-100 transition-all border-none bg-transparent cursor-pointer text-gray-600" data-idx="${idx}">
                                        <i class="fas fa-minus text-[8px]"></i>
                                    </button>
                                    <span class="text-xs font-extrabold font-mono w-4 text-center select-none">${item.qty}</span>
                                    <button type="button" class="btn-inc-cart w-6 h-6 rounded-md flex items-center justify-center hover:bg-gray-100 transition-all border-none bg-transparent cursor-pointer text-gray-600" data-idx="${idx}">
                                        <i class="fas fa-plus text-[8px]"></i>
                                    </button>
                                </div>
                                
                                <!-- Trash Btn -->
                                <button type="button" class="btn-remove-cart text-rose-500 hover:text-rose-700 p-1.5 transition-all border-none bg-transparent cursor-pointer" data-idx="${idx}">
                                    <i class="fas fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.append(rowHtml);
                });

                const tax = (subtotal * taxRate) / 100;
                const grandTotal = subtotal + tax;

                $('#summary-subtotal').text(formatRupiah(subtotal));
                $('#summary-tax').text(formatRupiah(tax));
                $('#summary-grand-total').text(formatRupiah(grandTotal));
            }

            // Increment Cart Item
            $(document).on('click', '.btn-inc-cart', function() {
                const idx = $(this).data('idx');
                cart[idx].qty++;
                localStorage.setItem('qr_cart', JSON.stringify(cart));
                updateCartUI();
                renderCartItems();
            });

            // Decrement Cart Item
            $(document).on('click', '.btn-dec-cart', function() {
                const idx = $(this).data('idx');
                if (cart[idx].qty > 1) {
                    cart[idx].qty--;
                } else {
                    cart.splice(idx, 1);
                }
                localStorage.setItem('qr_cart', JSON.stringify(cart));
                updateCartUI();
                renderCartItems();
            });

            // Remove Cart Item completely
            $(document).on('click', '.btn-remove-cart', function() {
                const idx = $(this).data('idx');
                cart.splice(idx, 1);
                localStorage.setItem('qr_cart', JSON.stringify(cart));
                updateCartUI();
                renderCartItems();
            });

            // Order Submission
            $('#btn-submit-order').on('click', function() {
                if (cart.length === 0) {
                    if (window.oAlert) {
                        window.oAlert('red', 'Keranjang Kosong', 'Silakan pilih menu terlebih dahulu.', false);
                    } else {
                        alert('Keranjang kosong! Silakan pilih menu.');
                    }
                    return;
                }

                const customerName = $('#customer_name').val().trim();
                if (!customerName) {
                    if (window.oAlert) {
                        window.oAlert('red', 'Nama Diperlukan', 'Masukkan nama Anda untuk identifikasi pesanan.', false);
                    } else {
                        alert('Masukkan nama Anda.');
                    }
                    $('#customer_name').focus();
                    return;
                }

                // Show Loading
                if (window.loading) {
                    window.loading();
                }

                // Prepare request payload
                const payload = {
                    customer_name: customerName,
                    items: cart.map(item => ({
                        product_id: item.product_id,
                        qty: item.qty,
                        note: item.note
                    }))
                };

                $.ajax({
                    type: "POST",
                    url: "{{ route('customer.order.submit', $table->uuid) }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: "application/json",
                    data: JSON.stringify(payload),
                    success: function(response) {
                        if (window.removeLoading) window.removeLoading();
                        
                        if (response.success) {
                            // Clear cart local storage
                            localStorage.removeItem('qr_cart');
                            cart = [];
                            updateCartUI();

                            // Redirect to status page
                            window.location.href = response.redirect;
                        } else {
                            if (window.oAlert) {
                                window.oAlert('red', 'Gagal', response.message || 'Gagal mengirim pesanan.');
                            } else {
                                alert(response.message || 'Gagal mengirim pesanan.');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (window.removeLoading) window.removeLoading();
                        
                        let msg = "Terjadi kesalahan server.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (window.oAlert) {
                            window.oAlert('red', 'Kesalahan', msg);
                        } else {
                            alert(msg);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
