@extends('layout.index')
@section('title','Point Of Sale')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-100% md:w-[calc(100%-450px)] lg:w-[calc(100%-550px)] gap-4">
        <div class="flex items-center gap-3">
            <div class="date-place inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
                <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-brand-light"></i></div>
                <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
            </div>
            <button type="button" id="btn-shortcut-guide" class="w-12 h-12 rounded-full bg-white hover:bg-brand-soft hover:text-brand text-gray-500 shadow flex items-center justify-center cursor-pointer transition-all duration-200 border-none outline-none" title="Petunjuk Shortcut Keyboard (F10)">
                <i class="fas fa-keyboard text-lg"></i>
            </button>
        </div>
    </div>
@endsection

@section('container')
    <!-- Side Toast Container for Quick Add -->
    <div id="quick-add-toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <style>
        /* Sizing untuk Antrian Orderan di Desktop */
        .order-list .order-item, 
        .order-list .order-item button.add-order-button {
            width: 240px !important;
            height: 80px !important;
        }
        .order-list .order-item.customer {
            width: 220px !important;
            height: 80px !important;
            transition: all 0.2s ease-in-out;
        }
        .order-list .order-item.customer.expanded {
            height: auto !important;
            min-height: 80px !important;
        }

        /* Smooth transitions for slide up/down */
        .order-list {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .order-list.collapsed {
            transform: translateY(100%);
        }

        /* Sizing & Animasi untuk Mobile View (Di bawah 768px) */
        @media (max-width: 767px) {
            .order-container {
                display: block !important;
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                height: 100% !important;
                width: 0px !important;
                max-width: 100% !important;
                overflow: hidden !important;
                transform: none !important;
                translate: none !important;
                transition: width 0.3s ease-in-out !important;
                z-index: 40 !important;
            }
            .order-container.active {
                width: 350px !important;
            }
            .open-close-order {
                position: fixed !important;
                bottom: 120px !important;
                right: 0 !important;
                display: flex !important;
                transform: none !important;
                translate: none !important;
                transition: right 0.3s ease-in-out !important;
                z-index: 50 !important;
            }
            .open-close-order.active {
                right: 350px !important;
            }

            /* Antrian Orderan Mobile */
            .order-list .order-item, 
            .order-list .order-item button.add-order-button {
                width: 176px !important;
                height: 64px !important;
                font-size: 14px !important;
            }
            .order-list .order-item.customer {
                width: 176px !important;
                height: 64px !important;
                padding: 8px !important;
                transition: all 0.2s ease-in-out;
            }
            .order-list .order-item.customer.expanded {
                height: auto !important;
                min-height: 64px !important;
            }
            .order-list .order-item.customer .order-name {
                font-size: 14px !important;
                margin-bottom: 4px !important;
            }
            .order-list .order-item.customer .table-detail,
            .order-list .order-item.customer .time-detail {
                font-size: 12px !important;
            }
            .order-list .order-item.customer .order-status {
                font-size: 10px !important;
                position: absolute !important;
                top: 8px !important;
                right: 8px !important;
                padding: 2px 8px !important;
            }
        }

        /* Sizing untuk Desktop (Lebar Layar >= 768px) */
        @media (min-width: 768px) {
            .order-container {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                height: 100% !important;
                width: 350px !important;
                display: block !important;
                transform: none !important;
                translate: none !important;
                overflow: hidden !important;
                z-index: 40 !important;
            }
            #modal-preview-products .relative.w-full.max-w-xl {
                max-height: none !important;
            }
            #modal-preview-products .relative.bg-white {
                max-height: none !important;
                overflow: visible !important;
                display: block !important;
            }
            #modal-preview-products .overflow-y-auto.flex-1 {
                overflow-y: visible !important;
                display: block !important;
            }
        }
        @media (min-width: 1024px) {
            .order-container {
                width: 450px !important;
            }
        }
    </style>
    @if(Auth::user()->role === 'dapur')
        <!-- Premium Dapur Dashboard -->
        <div class="w-full flex flex-col items-center justify-center min-h-[calc(100vh-100px)] p-6 bg-gray-50">
            <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100 transform hover:scale-[1.01] transition-all duration-300">
                <!-- Welcome Header -->
                <div class="w-20 h-20 bg-brand-soft rounded-full flex items-center justify-center mx-auto mb-6 text-brand">
                    <i class="fas fa-kitchen-utensils text-4xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Halo, Tim Dapur!</h1>
                <p class="text-gray-500 mb-8 text-sm">Selamat bekerja! Gunakan tombol di bawah ini untuk mengelola absensi kehadiran harian Anda dengan cepat dan mudah.</p>
                
                <!-- Absensi Button -->
                <button class="btn-open-attendance w-full bg-brand text-white hover:bg-brand-strong font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-3 text-lg cursor-pointer">
                    <i class="fas fa-clock-rotate-left"></i>
                    Mulai Absensi Staf
                </button>
                
                <!-- Time Indicator -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-center gap-2 text-gray-400 text-xs font-semibold">
                    <i class="fas fa-circle-check text-emerald-500"></i>
                    <span>Sistem Absensi Aktif & Tersinkronisasi</span>
                </div>
            </div>
        </div>
    @else
        <div class="product-container w-full md:w-[calc(100%-350px)] lg:w-[calc(100%-450px)] p-3 relative">
        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success')}}</p>
            </div>
        @endif
        
        <div class="categories-list max-w-full overflow-x-auto" id="categories-list">
            <ul class="inline-flex gap-2 md:gap-3 mb-3 md:mb-5">
                <li class="group w-28 md:w-40 shrink-0 relative">
                        <input type="radio" id="categories-all-item" name="category_filter" value="all-item" class="hidden peer category_filter" checked>
                        <div class="absolute top-1.5 right-1.5 w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-brand scale-0 peer-checked:scale-100 transition-transform duration-200 z-20 pointer-events-none shadow-sm"></div>
                        <label for="categories-all-item" class="inline-flex items-center justify-between w-full p-2 md:p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-soft/50 peer-checked:border-brand-medium peer-checked:bg-brand-soft hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong peer-checked:ring-2 peer-checked:ring-inset peer-checked:ring-brand/70">                           
                            <div class="flex flex-wrap gap-1 md:gap-2">
                                <div class="icon-place w-8 h-8 md:w-10 md:h-10 rounded-full bg-brand-subtle mb-0.5 md:mb-1 flex items-center justify-center text-brand">
                                    <i class="fas fa-apple-whole text-sm md:text-lg"></i>
                                </div>
                                <div class="text-place w-full">
                                    <p class="text-xs md:text-lg font-semibold truncate">All Items</p>
                                    <p class="text-[9px] md:text-sm text-gray-400">{{ $products->count() }} Items</p>
                                </div>
                            </div>
                        </label>
                    </li>
                    <li class="group w-28 md:w-40 shrink-0 relative">
                        <input type="radio" id="categories-best-seller" name="category_filter" value="best-seller" class="hidden peer category_filter">
                        <div class="absolute top-1.5 right-1.5 w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-amber-500 scale-0 peer-checked:scale-100 transition-transform duration-200 z-20 pointer-events-none shadow-sm"></div>
                        <label for="categories-best-seller" class="inline-flex items-center justify-between w-full p-2 md:p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-amber-100/50 peer-checked:border-amber-400 peer-checked:bg-amber-50 hover:bg-neutral-secondary-medium peer-checked:text-amber-800 peer-checked:ring-2 peer-checked:ring-inset peer-checked:ring-amber-500/70">                           
                            <div class="flex flex-wrap gap-1 md:gap-2">
                                <div class="icon-place w-8 h-8 md:w-10 md:h-10 rounded-full bg-amber-100 mb-0.5 md:mb-1 flex items-center justify-center text-amber-600">
                                    <i class="fas fa-crown text-sm md:text-lg"></i>
                                </div>
                                <div class="text-place w-full">
                                    <p class="text-xs md:text-lg font-semibold truncate">Best Seller</p>
                                    <p class="text-[9px] md:text-sm text-gray-400">{{ count($bestSellerProductIds) }} Items</p>
                                </div>
                            </div>
                        </label>
                    </li>
                @foreach ($categories as $category)
                    @php
                        $splitcolor = explode('-', $category->color);
                        $colorName = isset($splitcolor[1]) ? $splitcolor[1] : 'brand';
                    @endphp
                    <li class="group w-28 md:w-40 shrink-0 relative">
                        <input type="radio" id="categories-{{ $category->uuid }}" name="category_filter" value="{{ $category->uuid }}" class="hidden peer category_filter">
                        <div class="absolute top-1.5 right-1.5 w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-{{ $colorName }}-500 scale-0 peer-checked:scale-100 transition-transform duration-200 z-20 pointer-events-none shadow-sm"></div>
                        <label for="categories-{{ $category->uuid }}" class="inline-flex items-center justify-between w-full p-2 md:p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-{{ $colorName }}-100/50 peer-checked:border-{{ $colorName }}-300 peer-checked:bg-{{ $colorName }}-50 hover:bg-neutral-secondary-medium peer-checked:text-{{ $colorName }}-800 peer-checked:ring-2 peer-checked:ring-inset peer-checked:ring-{{ $colorName }}-400/70">                           
                            <div class="flex flex-wrap gap-1 md:gap-2 relative w-full">
                                <div class="icon-place w-8 h-8 md:w-10 md:h-10 rounded-full bg-{{ $colorName }}-100 mb-0.5 md:mb-1 flex items-center justify-center text-{{ $colorName }}-600">
                                    <i class="fas {{ $category->icon }} text-sm md:text-lg"></i>
                                </div>
                                <div class="text-place w-full">
                                    <p class="text-xs md:text-lg font-semibold truncate">{{$category->nama}}</p>
                                    <p class="text-[9px] md:text-sm text-gray-400">{{ $category->products()->count() }} Items</p>
                                </div>
                            </div>
                        </label>
                    </li>
                @endforeach
               
            </ul>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full mb-5">
            <div class="search-place flex-grow relative">
                <div class="absolute inset-y-2 end-0 flex justify-center items-center w-10 h-10 bg-gray-200 rounded-full me-2 pointer-events-none">
                    <svg class="w-5 h-5" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>search</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-256.000000, -1139.000000)" fill="#000000"> <path d="M269.46,1163.45 C263.17,1163.45 258.071,1158.44 258.071,1152.25 C258.071,1146.06 263.17,1141.04 269.46,1141.04 C275.75,1141.04 280.85,1146.06 280.85,1152.25 C280.85,1158.44 275.75,1163.45 269.46,1163.45 L269.46,1163.45 Z M287.688,1169.25 L279.429,1161.12 C281.591,1158.77 282.92,1155.67 282.92,1152.25 C282.92,1144.93 276.894,1139 269.46,1139 C262.026,1139 256,1144.93 256,1152.25 C256,1159.56 262.026,1165.49 269.46,1165.49 C272.672,1165.49 275.618,1164.38 277.932,1162.53 L286.224,1170.69 C286.629,1171.09 287.284,1171.09 287.688,1170.69 C288.093,1170.3 288.093,1169.65 287.688,1169.25 L287.688,1169.25 Z" id="search" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
                </div>
                <input type="text" class="bg-neutral-primary focus:bg-brand-softer w-full px-5 py-4 rounded-full border-0 outline-0 placeholder-gray-500" name="search" id="searchInput" placeholder="Search Something Delicious On Your Mind">
            </div>
            <div class="quick-add-toggle shrink-0 bg-white rounded-full px-4 py-3.5 shadow-[0_1px_3px_rgba(0,0,0,0.03)] border border-gray-100/50 flex items-center justify-between sm:justify-start gap-3 select-none">
                <div class="flex items-center gap-1.5">
                    <div class="w-6 h-6 rounded-full bg-brand-soft text-brand flex items-center justify-center text-[10px]">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider">Quick Add</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="quick-add-checkbox" class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
        </div>
        <div class="product-list grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 md:gap-4 mt-2 pb-36">
            @foreach ($products as $product)
                <div class="col-span-1 {{ $product->is_active == 1 ? 'bg-white' : 'bg-slate-200' }} rounded-lg p-2 md:p-3 flex flex-wrap gap-2 md:gap-3 cursor-pointer product-item" data-uuid="{{ $product->uuid }}" data-category="{{ $product->category_id }}" data-best-seller="{{ in_array($product->uuid, $bestSellerProductIds) ? '1' : '0' }}">
                    <div class="img-place w-full h-20 sm:h-24 md:h-30 rounded-base overflow-hidden relative">
                        @if(in_array($product->uuid, $bestSellerProductIds))
                            <div class="absolute top-1.5 left-1.5 bg-gradient-to-r from-amber-500 to-yellow-400 text-white font-extrabold px-2 py-0.5 md:py-1 rounded-md text-[8px] md:text-[9px] shadow-md flex items-center gap-1 z-10 select-none tracking-wide">
                                <i class="fas fa-crown text-[7px] md:text-[8px]"></i>
                                <span>BEST SELLER</span>
                            </div>
                        @endif
                        <img src="{{ $product->picture == "" ? Vite::asset('resources/img/no_image_available.png') : asset('storage/products/'.$product->picture) }}" class="w-full h-full object-cover object-center" />
                    </div>
                    <div class="product-detail w-full">
                        <p class="name-product text-xs sm:text-sm md:text-base mb-1 md:mb-2 font-medium truncate">{{Str::limit($product->name,20,'...')}}</p>
                        <div class="product-price-detail flex flex-wrap gap-1 w-full justify-between items-center">
                            @php
                                if ($product->category) {
                                    $splitcolor = explode('-', $product->category->color);
                                    $color = isset($splitcolor[1]) ? $splitcolor[1] : 'brand';
                                    $catColorClass = $product->category->color;
                                    $catColorText = "text-{$color}-800";
                                    $catName = $product->category->nama;
                                } else {
                                    $catColorClass = 'bg-gray-100';
                                    $catColorText = 'text-gray-800';
                                    $catName = 'Uncategorized';
                                }
                            @endphp
                            <p class="categories-product {{ $catColorClass }} {{ $catColorText }} px-2 py-0.5 md:px-3 md:py-1 rounded-full text-[9px] md:text-[10px]">{{ $catName }}</p>
                            <p class="price-product text-xs sm:text-sm md:text-lg font-bold text-neutral-800">Rp.{{ number_format($product->price,0,',') }},-</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="order-list p-2 md:p-3 bg-white fixed bottom-0 left-0 w-full md:w-[calc(100%-350px)] lg:w-[calc(100%-450px)] z-30 border-t border-gray-100 shadow-[0_-8px_30px_rgba(0,0,0,0.04)]">
            <!-- Action Buttons (Track Order & Fullscreen) -->
            <div class="absolute bottom-full left-4 mb-2 flex items-center gap-2 z-30">
                <!-- Toggle Button for Order List (Slide Up/Down) -->
                <button type="button" id="btn-toggle-order-list" class="bg-brand hover:bg-brand-strong text-white font-bold py-2 pl-2 pr-2 sm:pl-3.5 sm:pr-3.5 rounded-full sm:rounded-xl shadow-lg flex items-center sm:gap-2 text-xs transition-all duration-300 cursor-pointer border-none outline-none select-none">
                    <span id="toggle-order-text" class="hidden sm:block">Track Order</span>
                    <span class="w-7 h-7 sm:w-5 sm:h-5 rounded-full bg-white text-brand flex items-center justify-center text-xs sm:text-[10px] shrink-0 font-extrabold shadow-sm">
                        <i class="fas fa-minus transition-transform duration-300" id="toggle-order-icon"></i>
                    </span>
                </button>
                
                <!-- Fullscreen Button -->
                <button type="button" id="btn-fullscreen-order-list" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 pl-2 pr-2 sm:pl-3.5 sm:pr-3.5 rounded-full sm:rounded-xl shadow-lg flex items-center sm:gap-2 text-xs transition-all duration-300 cursor-pointer border-none outline-none select-none">
                    <span class="hidden sm:block">Fullscreen</span>
                    <span class="w-7 h-7 sm:w-5 sm:h-5 rounded-full bg-white text-indigo-600 flex items-center justify-center text-xs sm:text-[10px] shrink-0 font-extrabold shadow-sm">
                        <i class="fas fa-expand"></i>
                    </span>
                </button>
            </div>
            <div id="order-list" class="max-w-full overflow-x-auto" >
                <ul class="inline-flex gap-3 items-start" id="order">
                    <li class="order-item" id="order-item-button">
                        <button class="bg-neutral-50 border-2 text-gray-700 border-dashed border-gray-400 hover:bg-neutral-100 h-16 md:h-20 rounded-lg w-44 md:w-60 cursor-pointer add-order-button text-sm md:text-base">Add Order</button>
                    </li>
                    @foreach ($transactions as $transaction)
                        <li class="order-item customer border border-gray-400 w-44 md:w-55 h-16 md:h-20 bg-primary border-primary hover:bg-brand-softer cursor-pointer rounded-lg p-2 md:p-3 relative" data-uuid="{{ $transaction->uuid }}">
                            <p class="order-name text-sm md:text-lg text-neutral-700 mb-1 md:mb-2 font-medium truncate pr-14">{{$transaction->customer_name != null ? $transaction->customer_name : 'Guest'}}</p>
                            <div class="flex order-subdetail justify-between pr-6">
                                <p class="table-detail text-xs md:text-sm text-neutral-400 truncate"><span class="table-detail-place">{{ $transaction->table_id != null && $transaction->table ? $transaction->table->name : "Unset Table" }}</span> - <span class="order-type-detail">{{$transaction->order_type != null ? $transaction->order_type : ""}}</span></p>
                                <p class="time-detail text-xs md:text-sm text-neutral-400 whitespace-nowrap ml-1">{{date('H:i',strtotime($transaction->created_at))}}</p>
                            </div>
                            @php
                                if($transaction->status == 'active') {
                                    $class_color = "bg-emerald-50 text-emerald-700 border border-emerald-200";
                                } else if($transaction->status == 'process') {
                                    $class_color = "bg-brand-soft text-brand-light border border-brand-medium";
                                } else if($transaction->status == 'payment') {
                                    $class_color = "bg-amber-100 text-amber-800 border border-accent-yellow shadow-xs";
                                } else {
                                    $class_color = "bg-gray-100 text-gray-700 border border-gray-200";
                                }
                            @endphp
                            <p class="order-status {{ $class_color }} px-2 py-0.5 md:py-1 rounded-full text-[10px] md:text-xs absolute top-2 right-2 md:top-4">{{$transaction->status}}</p>
                            
                            <!-- Toggle Button to Open Menu List -->
                            <button type="button" class="toggle-items-btn absolute bottom-1.5 right-1.5 md:bottom-2 md:right-2 h-5 w-5 rounded-full bg-gray-50 hover:bg-brand-soft hover:text-brand flex items-center justify-center text-gray-400 cursor-pointer shadow-xs border border-gray-200 z-10"><i class="fas fa-chevron-down text-[10px]"></i></button>

                            <!-- Hidden Items List Wrapper -->
                            <div class="items-list-wrapper hidden mt-2.5 pt-2.5 border-t border-dashed border-gray-300">
                                @php
                                    $items = $transaction->orderItem;
                                    $itemCount = count($items);
                                @endphp
                                <ul class="order-items-list text-[11px] md:text-xs text-neutral-600 space-y-1">
                                    @foreach($items as $item)
                                        <li class="flex justify-between items-start">
                                            <span class="truncate max-w-[180px]">{{ $item->qty }}x {{ $item->product_name }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="border-t border-dashed border-gray-200 mt-2 pt-1.5 flex justify-between items-center text-[10px] md:text-xs text-neutral-500">
                                    <span>Total Order:</span>
                                    <span class="total-order-count font-bold text-neutral-700">{{ $itemCount }} items</span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    
                </ul>
            </div>
            
        </div>
        

        <!-- Fullscreen Queue Modal Overlay -->
        <div id="modal-fullscreen-queue" tabindex="-1" class="hidden fixed inset-0 z-50 bg-white/95 backdrop-blur-md flex flex-col p-6 overflow-y-auto animate-scaleUp">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-6 select-none shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand/20 text-brand flex items-center justify-center text-lg shadow-inner">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-800 uppercase tracking-wider">Monitor Antrian Pesanan</h3>
                        <p class="text-xs text-gray-500 font-semibold mt-0.5">Memantau seluruh pesanan aktif secara langsung</p>
                    </div>
                </div>
                
                <button type="button" id="btn-close-fullscreen-queue" class="text-gray-400 hover:text-gray-600 text-sm w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none outline-none bg-transparent transition-all">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Active Orders Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="fullscreen-queue-grid">
                @foreach ($transactions as $transaction)
                    <div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-4 flex flex-col justify-between hover:shadow-md hover:border-gray-300 transition-all duration-300 relative group cursor-pointer order-item customer" data-uuid="{{ $transaction->uuid }}">
                        
                        <!-- Status Badge -->
                        @php
                            if($transaction->status == 'active') {
                                $badge_class = "bg-emerald-500/15 text-emerald-400 border border-emerald-500/25";
                            } else if($transaction->status == 'process') {
                                $badge_class = "bg-brand/25 text-brand-light border border-brand/35";
                            } else if($transaction->status == 'payment') {
                                $badge_class = "bg-amber-500/15 text-amber-400 border border-amber-500/25 shadow-sm";
                            } else {
                                $badge_class = "bg-white/10 text-white/70 border border-white/20";
                            }
                        @endphp

                        <!-- Card Header -->
                        <div class="pb-3 border-b border-gray-100 shrink-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="text-base font-extrabold text-gray-800 truncate max-w-[70%]" title="{{ $transaction->customer_name ?? 'Guest' }}">{{ $transaction->customer_name ?? 'Guest' }}</h4>
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $badge_class }}">{{ $transaction->status }}</span>
                            </div>
                            
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mt-1 font-semibold">
                                <span><i class="fas fa-chair text-[9px]"></i> {{ $transaction->table ? $transaction->table->name : 'Unset Table' }}</span>
                                <span>•</span>
                                <span><i class="fas fa-utensils text-[9px]"></i> {{ $transaction->order_type ?? 'Dining' }}</span>
                                <span>•</span>
                                <span><i class="fas fa-clock text-[9px]"></i> {{ date('H:i', strtotime($transaction->created_at)) }}</span>
                            </div>
                        </div>

                        <!-- Items List -->
                        <div class="py-3 flex-grow overflow-y-auto max-h-48 custom-scrollbar">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Item Pesanan</p>
                            <ul class="text-[11px] text-gray-600 space-y-1.5">
                                @foreach($transaction->orderItem as $item)
                                    <li class="flex justify-between gap-1 items-start">
                                        <span class="font-bold text-gray-800 shrink-0">{{ $item->qty }}x</span>
                                        <span class="truncate flex-grow text-left pl-1.5 text-gray-600">{{ $item->product_name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Card Footer (Total Items count) -->
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] font-bold text-gray-500 shrink-0">
                            <span>Jumlah Item:</span>
                            <span class="text-gray-800">{{ count($transaction->orderItem) }} Items</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Modal Petunjuk Penggunaan Shortcut -->
        <div id="modal-shortcut-guide" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px] flex">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-soft text-brand flex items-center justify-center">
                            <i class="fas fa-keyboard text-sm"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wide">Petunjuk Shortcut Kasir</h3>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center close-shortcut-modal cursor-pointer border-none outline-none bg-transparent">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                
                <div class="flex flex-col gap-3 py-1">
                    <p class="text-xs text-gray-500 leading-relaxed">Gunakan shortcut keyboard berikut untuk memproses transaksi dengan kecepatan tinggi tanpa perlu menyentuh mouse:</p>
                    
                    <div class="flex flex-col gap-2 mt-2">
                        <!-- F1: Search -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100/50 hover:bg-brand-softer transition-all">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-white border border-gray-300 rounded-lg shadow-sm font-mono text-xs font-bold text-gray-700 min-w-[32px] text-center">F1</span>
                                <span class="text-xs font-semibold text-gray-700">Fokus Kolom Cari Produk</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">Arahkan kursor ke pencarian</span>
                        </div>

                        <!-- F2: Submit -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100/50 hover:bg-brand-softer transition-all">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-white border border-gray-300 rounded-lg shadow-sm font-mono text-xs font-bold text-gray-700 min-w-[32px] text-center">F2</span>
                                <span class="text-xs font-semibold text-gray-700">Tombol Aksi Utama</span>
                            </div>
                            <span class="text-[10px] text-brand font-bold uppercase">Submit/Bayar</span>
                        </div>

                        <!-- F3: Toggle Quick Add -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100/50 hover:bg-brand-softer transition-all">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-white border border-gray-300 rounded-lg shadow-sm font-mono text-xs font-bold text-gray-700 min-w-[32px] text-center">F3</span>
                                <span class="text-xs font-semibold text-gray-700">Toggle Mode Quick Add</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">Aktif/Nonaktif Quick Add</span>
                        </div>

                        <!-- F4: Add Order -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100/50 hover:bg-brand-softer transition-all">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-white border border-gray-300 rounded-lg shadow-sm font-mono text-xs font-bold text-gray-700 min-w-[32px] text-center">F4</span>
                                <span class="text-xs font-semibold text-gray-700">Tambah Transaksi Baru</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">Buat invoice / pelanggan baru</span>
                        </div>

                        <!-- F10: Help -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100/50 hover:bg-brand-softer transition-all">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-white border border-gray-300 rounded-lg shadow-sm font-mono text-xs font-bold text-gray-700 min-w-[32px] text-center">F10</span>
                                <span class="text-xs font-semibold text-gray-700">Tampilkan Panduan Ini</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">Membuka bantuan</span>
                        </div>

                        <!-- ESC: Close Modal -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100/50 hover:bg-brand-softer transition-all">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 bg-white border border-gray-300 rounded-lg shadow-sm font-mono text-[10px] font-bold text-gray-700 min-w-[32px] text-center">ESC</span>
                                <span class="text-xs font-semibold text-gray-700">Tutup Semua Modal Aktif</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">Batalkan / tutup pop-up</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5 pt-3 border-t border-gray-100 flex justify-end">
                    <button type="button" class="close-shortcut-modal bg-brand hover:bg-brand-strong text-white font-bold px-5 py-2.5 rounded-xl text-xs cursor-pointer border-none outline-none">
                        Mengerti, Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Place --}}
        <div id="modal-preview-products" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full z-99">
            <div class="relative p-2 md:p-4 w-full max-w-xl max-h-[95vh] md:max-h-none">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm max-h-[90vh] md:max-h-none flex flex-col overflow-hidden md:overflow-visible">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 rounded-t text-start sm:text-center shrink-0 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-dark-soft w-full">
                            Detail Products
                        </h3>
                        <div class="button-place flex gap-1">
                            
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                    </div>
                    <!-- Modal body -->
                    <div class="overflow-y-auto md:overflow-y-visible flex-1 flex flex-col">
                        <input type="hidden" name="uuid_product" id="uuid_product" />
                        <div class="picture-place flex align-center justify-center p-2 md:p-4 space-y-4 shrink-0">
                            <div class="w-full h-40 md:h-70 rounded-lg overflow-hidden">
                                <img src="" class="picture_product w-full h-40 md:h-70 object-cover object-center rounded-lg cursor-pointer hover:opacity-90 transition-opacity" title="Klik untuk memperbesar" />
                            </div>
                        </div>
                        <div class="text-place w-full flex flex-wrap gap-1.5 md:gap-2 p-3 md:p-4">
                            <p class="category-product px-3 py-1 rounded-full text-[10px]"></p>
                            <h5 class="product-name text-xl md:text-2xl w-full font-semibold text-dark-soft"></h5>
                            <p class="product-description text-xs md:text-sm text-dark-soft"></p> 
                            <p class="product-price text-brand-light font-bold text-xl md:text-2xl w-full"></p>   
                            <textarea id="descriptionOrder" class="description-order w-full border-0 bg-gray-50 placeholder-gray-400 bg-neutral outline-0 rounded-base focus:outline-brand focus:bg-brand-subtle text-sm" placeholder="Masukkan Catatan"></textarea>
                        </div>
                        <label for="quantity-input" class="block mb-1.5 ps-3 md:ps-4 text-xs md:text-sm font-medium text-heading">Quantity:</label>
                        <div class="relative flex items-center w-full shadow-xs rounded-base ps-3 pe-3 pb-3 md:ps-4 md:pe-4 md:pb-4">
                            <button type="button" id="decrement-button" data-input-counter-decrement="quantity-input" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium leading-5 rounded-s-base text-sm px-3 focus:outline-none h-10">
                                <svg class="w-4 h-4 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                            </button>
                            <input type="text" id="quantity-input" data-input-counter aria-describedby="helper-text-explanation" class="border-x-0 h-10 text-center w-full bg-neutral-secondary-medium border-default-medium py-2.5 placeholder-gray-400 focus:outline-0" placeholder="Qty" value="1" required />
                            <button type="button" id="increment-button" data-input-counter-increment="quantity-input" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium leading-5 rounded-e-base text-sm px-3 focus:outline-none h-10">
                                <svg class="w-4 h-4 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                            </button>
                        </div>
                        <button class="place-order-button bg-brand rounded-bl-lg rounded-br-lg text-neutral-50 hover:bg-brand-strong cursor-pointer w-full py-2.5 md:py-3 shrink-0">
                            Add To Cart <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- Fullscreen Image Viewer Modal --}}
        <div id="modal-fullscreen-image" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md transition-opacity duration-300 opacity-0">
            <button id="close-fullscreen-image" class="absolute top-4 right-4 text-white hover:text-gray-300 focus:outline-none bg-white/10 hover:bg-white/20 p-2.5 rounded-full z-[110] cursor-pointer transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="relative max-w-[95vw] max-h-[90vh] p-2 flex items-center justify-center">
                <img id="fullscreen-image-element" src="" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl transition-transform duration-300 cursor-zoom-out hover:scale-[1.02]" />
            </div>
        </div>
        <div id="modal-edit-transaction" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full z-99">
            <div class="relative p-4 w-full max-w-xl max-h-[90%]">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 md:p-3 rounded-t text-start sm:text-center">
                        <h3 class="text-lg font-semibold text-dark-soft w-full">
                            Transaction Detail
                        </h3>
                        <div class="button-place flex gap-1">
                            
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal-transaction">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                    </div>
                    <!-- Modal body -->
                    <div>
                        <div class="grid grid-cols-1 p-4 w-full">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 bg-neutral outline-0 rounded-base focus:outline-brand focus:bg-brand-subtle" placeholder="Write Down Customer Name" />
                            <div class="flex flex-wrap w-full gap-1 mt-2">
                                <button class="bg-brand w-full sm:w-auto text-white rounded-base px-4 py-2 mt-2 hover:bg-brand-strong cursor-pointer save-name-button">Save Name</button>
                                <button class="bg-danger w-full sm:w-auto text-white rounded-base px-4 py-2 mt-2 hover:bg-danger-strong cursor-pointer delete-transaction-button">Delete Transaction</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div id="modal-edit-order" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full z-99">
            <div class="relative p-4 w-full max-w-xl max-h-[90%]">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 md:p-3 rounded-t text-start sm:text-center">
                        <h3 class="text-lg font-semibold text-dark-soft w-full">
                            Edit Order Item
                        </h3>
                        <div class="button-place flex gap-1">
                            
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal-order">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                    </div>
                    <!-- Modal body -->
                    <div>
                        <div class="grid grid-cols-1 p-4 w-full gap-3">
                            <input type="hidden" name="uuid_order" id="uuid_order" />

                            {{-- Nama Produk --}}
                            <div>
                                <label for="order_product_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-tag text-xs mr-1 text-brand-light"></i> Nama Produk
                                </label>
                                <input type="text" id="order_product_name" name="order_product_name"
                                    class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 rounded-base px-3 py-2 outline-0 focus:outline-brand focus:bg-brand-subtle text-sm"
                                    placeholder="Nama produk pada struk" />
                            </div>

                            {{-- Harga --}}
                            <div>
                                <label for="order_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-coins text-xs mr-1 text-brand-light"></i> Harga (per item)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-500 text-sm">Rp</span>
                                    <input type="number" id="order_price" name="order_price" min="0"
                                        class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 rounded-base pl-10 pr-3 py-2 outline-0 focus:outline-brand focus:bg-brand-subtle text-sm"
                                        placeholder="0" />
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Subtotal akan dikalkulasi ulang otomatis dari harga × qty.</p>
                            </div>

                            {{-- Catatan --}}
                            <div>
                                <label for="order_description" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-sticky-note text-xs mr-1 text-brand-light"></i> Catatan Order
                                </label>
                                <textarea name="order_description" id="order_description"
                                    class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 bg-neutral outline-0 rounded-base focus:outline-brand focus:bg-brand-subtle text-sm px-3 py-2"
                                    rows="2" placeholder="Tulis catatan order di sini…"></textarea>
                            </div>

                            <div class="flex flex-wrap w-full gap-2 mt-1">
                                <button class="bg-brand flex-1 text-white rounded-base px-4 py-2 hover:bg-brand-strong cursor-pointer save-note-button">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                                <button class="bg-danger flex-1 text-white rounded-base px-4 py-2 hover:bg-danger-strong cursor-pointer delete-order-button">
                                    <i class="fas fa-trash mr-1"></i> Hapus Item
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        
        <div id="modal-see-transaction" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full z-99">
            <div class="relative p-4 w-full max-w-xl max-h-[90%]">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 md:p-3 rounded-t text-start sm:text-center border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-dark-soft w-full">
                            Transaction Detail
                        </h3>
                        <div class="button-place flex gap-1">
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-success-subtle text-success rounded-full hover:bg-green-500 cursor-pointer outline-0 inline-flex justify-center items-center print-transaction-button">
                               <svg class="w-5 h-5" viewBox="0 -2 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000" transform="matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.064"></g><g id="SVGRepo_iconCarrier"> <title>print</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke-width="0.00032" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-100.000000, -205.000000)" fill="#000000"> <path d="M130,226 C130,227.104 129.104,228 128,228 L125.858,228 C125.413,226.278 123.862,225 122,225 L110,225 C108.138,225 106.587,226.278 106.142,228 L104,228 C102.896,228 102,227.104 102,226 L102,224 C102,222.896 102.896,222 104,222 L128,222 C129.104,222 130,222.896 130,224 L130,226 L130,226 Z M122,231 L110,231 C108.896,231 108,230.104 108,229 C108,227.896 108.896,227 110,227 L122,227 C123.104,227 124,227.896 124,229 C124,230.104 123.104,231 122,231 L122,231 Z M108,209 C108,207.896 108.896,207 110,207 L122,207 C123.104,207 124,207.896 124,209 L124,220 L108,220 L108,209 L108,209 Z M128,220 L126,220 L126,209 C126,206.791 124.209,205 122,205 L110,205 C107.791,205 106,206.791 106,209 L106,220 L104,220 C101.791,220 100,221.791 100,224 L100,226 C100,228.209 101.791,230 104,230 L106.142,230 C106.587,231.723 108.138,233 110,233 L122,233 C123.862,233 125.413,231.723 125.858,230 L128,230 C130.209,230 132,228.209 132,226 L132,224 C132,221.791 130.209,220 128,220 L128,220 Z" id="print" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
                            </button>
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-warning-subtle text-warning rounded-full hover:bg-orange-300 cursor-pointer outline-0 inline-flex justify-center items-center edit-transaction-button">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M13.0207 5.82839L15.8491 2.99996L20.7988 7.94971L17.9704 10.7781M13.0207 5.82839L3.41405 15.435C3.22652 15.6225 3.12116 15.8769 3.12116 16.1421V20.6776H7.65669C7.92191 20.6776 8.17626 20.5723 8.3638 20.3847L17.9704 10.7781M13.0207 5.82839L17.9704 10.7781" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                            </button>
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-brand-subtle text-brand rounded-full hover:bg-brand-strong hover:text-white cursor-pointer outline-0 inline-flex justify-center items-center delete-transaction-button">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M20.5001 6H3.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <path d="M18.8332 8.5L18.3732 15.3991C18.1962 18.054 18.1077 19.3815 17.2427 20.1907C16.3777 21 15.0473 21 12.3865 21H11.6132C8.95235 21 7.62195 21 6.75694 20.1907C5.89194 19.3815 5.80344 18.054 5.62644 15.3991L5.1665 8.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <path d="M6.5 6C6.55588 6 6.58382 6 6.60915 5.99936C7.43259 5.97849 8.15902 5.45491 8.43922 4.68032C8.44784 4.65649 8.45667 4.62999 8.47434 4.57697L8.57143 4.28571C8.65431 4.03708 8.69575 3.91276 8.75071 3.8072C8.97001 3.38607 9.37574 3.09364 9.84461 3.01877C9.96213 3 10.0932 3 10.3553 3H13.6447C13.9068 3 14.0379 3 14.1554 3.01877C14.6243 3.09364 15.03 3.38607 15.2493 3.8072C15.3043 3.91276 15.3457 4.03708 15.4286 4.28571L15.5257 4.57697C15.5433 4.62992 15.5522 4.65651 15.5608 4.68032C15.841 5.45491 16.5674 5.97849 17.3909 5.99936C17.4162 6 17.4441 6 17.5 6" stroke="#1C274C" stroke-width="1.5"></path> </g></svg>
                            </button>
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal-order">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
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
                    <!-- Modal body -->
                    <div>
                        <div class="p-4 w-full">
                            <input type="hidden" name="uuid_transaction_detail" id="uuid_transaction_detail" />
                            <ul class="transaction-detail-list w-full h-[400px] overflow-y-auto">

                            </ul>
                            <div class="order-total-place w-full border-t border-gray-200 flex flex-wrap items-start">
                            <div class="flex justify-between items-center p-3 w-full">
                                <p class="text-gray-500 font-semibold">Subtotal</p>
                                <p class="text-lg font-bold transaction-detail-total-price">Rp. 0,-</p>
                            </div>
                            <div class="process-to-payment w-full mt-2">
                                <button class="bg-brand text-neutral-50 hover:bg-brand-strong cursor-pointer w-full py-3 px-3 rounded-lg process-to-payment-button">Process To Payment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {{-- Modal Add Manual Item --}}
        <div id="modal-add-manual-item" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full">
            <div class="relative p-4 w-full max-w-xl max-h-[90%]">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 md:p-3 rounded-t text-start sm:text-center border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-dark-soft w-full">
                            Tambah Item Manual (Sekali Pakai)
                        </h3>
                        <div class="button-place flex gap-1">
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal-manual">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                    </div>
                    <!-- Modal body -->
                    <div>
                        <div class="grid grid-cols-1 p-4 w-full gap-3">
                            {{-- Nama Item --}}
                            <div>
                                <label for="manual_item_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Item
                                </label>
                                <input type="text" id="manual_item_name" class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 rounded-base px-3 py-2 outline-0 focus:outline-brand focus:bg-brand-subtle text-sm" placeholder="Contoh: Kresek / Biaya Tambahan" required />
                            </div>

                            {{-- Harga --}}
                            <div>
                                <label for="manual_item_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Satuan
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-500 text-sm">Rp</span>
                                    <input type="number" id="manual_item_price" min="0" class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 rounded-base pl-10 pr-3 py-2 outline-0 focus:outline-brand focus:bg-brand-subtle text-sm" placeholder="0" required />
                                </div>
                            </div>

                            {{-- Qty --}}
                            <div>
                                <label for="manual_item_qty" class="block text-sm font-medium text-gray-700 mb-1">
                                    Quantity
                                </label>
                                <input type="number" id="manual_item_qty" min="1" value="1" class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 rounded-base px-3 py-2 outline-0 focus:outline-brand focus:bg-brand-subtle text-sm" placeholder="1" required />
                            </div>

                            {{-- Catatan --}}
                            <div>
                                <label for="manual_item_description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Catatan
                                </label>
                                <textarea id="manual_item_description" class="w-full border border-gray-300 bg-gray-50 placeholder-gray-400 outline-0 rounded-base focus:outline-brand focus:bg-brand-subtle text-sm px-3 py-2" rows="2" placeholder="Masukkan Catatan (opsional)"></textarea>
                            </div>

                            <div class="flex w-full mt-2">
                                <button class="bg-brand w-full text-white rounded-base py-3 hover:bg-brand-strong cursor-pointer save-manual-item-btn font-semibold">
                                    <i class="fas fa-check mr-1"></i> Tambahkan ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    {{-- Modal Pembayaran Langsung (Direct Checkout) --}}
    <div id="modal-direct-payment" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px]">
        <div class="relative p-3 md:p-4 w-full max-w-4xl max-h-[95vh]">
            <div class="relative bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-[90vh]">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-100 shrink-0">
                    <div class="flex flex-col">
                        <h3 class="text-xl font-bold text-gray-800">
                            Pembayaran Langsung
                        </h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Invoice: <span id="dp-invoice-number" class="font-bold text-brand">#000000</span> | Pelanggan: <span id="dp-customer-name" class="font-bold text-gray-600">Guest</span></p>
                    </div>
                    <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-200 cursor-pointer outline-none flex items-center justify-center dp-close-modal">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <!-- Modal Body (Scrollable) -->
                <div class="p-4 md:p-6 overflow-y-auto flex-1">
                    <input type="hidden" id="dp-transaction-uuid" value="">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Kolom Kiri: Detail Order & Rincian Tagihan -->
                        <div class="lg:col-span-7 flex flex-col gap-4">
                            <div class="p-4 bg-slate-50 border border-gray-100 rounded-xl">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-shopping-basket text-brand-light"></i> Rincian Pesanan
                                </h4>
                                <ul id="dp-order-items-list" class="flex flex-col gap-2 max-h-[220px] overflow-y-auto pr-1">
                                    <!-- Dinamis diisi via JS -->
                                </ul>
                            </div>

                            <!-- Form Diskon -->
                            <div class="p-4 bg-slate-50 border border-gray-100 rounded-xl">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-tag text-brand-light"></i> Terapkan Diskon
                                </h4>
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-xs font-bold">Rp</span>
                                        <input type="number" id="dp-discount-input" class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand" placeholder="0" min="0">
                                    </div>
                                    <button type="button" id="dp-apply-discount-btn" class="px-4 py-2 bg-brand hover:bg-brand-strong text-white text-sm font-semibold rounded-lg transition-colors cursor-pointer shadow-sm">Apply</button>
                                </div>
                            </div>

                            <!-- Ringkasan Biaya -->
                            <div class="p-4 bg-slate-50 border border-gray-100 rounded-xl">
                                <ul class="flex flex-col gap-2.5 text-sm">
                                    <li class="flex justify-between items-center text-gray-500">
                                        <span>Subtotal</span>
                                        <span id="dp-subtotal-text" class="font-semibold text-gray-700">Rp 0</span>
                                    </li>
                                    <li class="flex justify-between items-center text-gray-500">
                                        <span>Pajak (Tax)</span>
                                        <span id="dp-tax-text" class="font-semibold text-gray-700">Rp 0</span>
                                    </li>
                                    <li class="flex justify-between items-center text-gray-500">
                                        <span>Diskon</span>
                                        <span id="dp-discount-text" class="font-semibold text-amber-600">-Rp 0</span>
                                    </li>
                                    <li class="border-t border-dashed border-gray-200 pt-2.5 flex justify-between items-center text-base font-bold text-gray-800">
                                        <span>Total Pembayaran</span>
                                        <span id="dp-total-text" class="text-lg text-brand">Rp 0</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Metode Bayar & Input Jumlah Bayar -->
                        <div class="lg:col-span-5 flex flex-col gap-4">
                            <div class="p-4 bg-slate-50 border border-gray-100 rounded-xl">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-wallet text-brand-light"></i> Metode Pembayaran
                                </h4>
                                <div class="grid grid-cols-3 gap-2 select-none">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="dp_payment_method" value="cash" class="hidden peer" checked>
                                        <div class="flex flex-col items-center justify-center p-3 bg-white border border-gray-200 rounded-xl hover:bg-brand-soft/20 peer-checked:border-brand peer-checked:bg-brand-soft peer-checked:text-fg-brand-strong transition-all shadow-sm">
                                            <i class="fas fa-money-bill-wave text-lg mb-1.5"></i>
                                            <span class="text-xs font-bold">Cash</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="dp_payment_method" value="qris" class="hidden peer">
                                        <div class="flex flex-col items-center justify-center p-3 bg-white border border-gray-200 rounded-xl hover:bg-brand-soft/20 peer-checked:border-brand peer-checked:bg-brand-soft peer-checked:text-fg-brand-strong transition-all shadow-sm">
                                            <i class="fas fa-qrcode text-lg mb-1.5"></i>
                                            <span class="text-xs font-bold">QRIS</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="dp_payment_method" value="transfer" class="hidden peer">
                                        <div class="flex flex-col items-center justify-center p-3 bg-white border border-gray-200 rounded-xl hover:bg-brand-soft/20 peer-checked:border-brand peer-checked:bg-brand-soft peer-checked:text-fg-brand-strong transition-all shadow-sm">
                                            <i class="fas fa-university text-lg mb-1.5"></i>
                                            <span class="text-xs font-bold">Transfer</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Input Uang & Kembalian -->
                            <div class="p-4 bg-slate-50 border border-gray-100 rounded-xl flex flex-col gap-4">
                                <div>
                                    <label for="dp-paid-amount" class="block text-sm font-bold text-gray-700 mb-1.5">Jumlah yang Dibayar</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-3.5 flex items-center text-gray-500 font-extrabold text-base">Rp</span>
                                        <input type="number" id="dp-paid-amount" class="w-full pl-10 pr-3 py-3 text-lg font-black bg-white border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand" placeholder="0" min="0">
                                    </div>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-xl shadow-xs">
                                    <span class="text-sm font-bold text-gray-500">Uang Kembalian</span>
                                    <span id="dp-change-text" class="text-lg font-black text-emerald-600">Rp 0</span>
                                </div>
                            </div>

                            <!-- Bluetooth Printer Control Panel (Dalam Modal Pembayaran) -->
                            <div class="p-4 bg-white border border-gray-200 rounded-xl flex flex-col gap-3 text-xs shadow-sm">
                                <div class="flex items-center justify-between flex-wrap gap-2 border-b border-gray-100 pb-2">
                                    <span class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">Bluetooth Printer</span>
                                    <span id="dp-bt-device-name" class="font-semibold text-emerald-600 hidden truncate max-w-[130px]"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="dp-btn-toggle-bluetooth" class="px-3 py-2 bg-brand hover:bg-brand-strong text-white font-bold rounded-lg flex items-center gap-1.5 transition-all cursor-pointer shadow-sm w-full justify-center text-[11px] outline-none">
                                        <i class="fab fa-bluetooth text-[11px]"></i> <span id="dp-bt-status-text">Hubungkan Bluetooth</span>
                                    </button>
                                </div>
                                
                                <div class="flex items-center justify-between gap-1.5 pt-1">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode Cetak:</label>
                                    <select id="dp-print-method-select" class="px-2 py-1 bg-white border border-brand rounded-lg text-[11px] font-bold focus:outline-none cursor-pointer">
                                        <option value="browser" selected>Browser Print (HTML)</option>
                                        <option value="bluetooth">Direct Bluetooth</option>
                                        <option value="rawbt">RawBT (Android)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="p-4 border-t border-gray-100 bg-slate-50 flex gap-3 shrink-0">
                    <button type="button" id="dp-print-check-btn" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm cursor-pointer text-sm flex items-center justify-center gap-1.5">
                        <i class="fas fa-print"></i> Cetak Tagihan (Check)
                    </button>
                    <button type="button" id="dp-process-payment-btn" class="flex-1 bg-brand hover:bg-brand-strong text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm cursor-pointer text-sm flex items-center justify-center gap-1.5">
                        <i class="fas fa-check-circle"></i> Selesaikan Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Sukses Pembayaran Lunas (Direct Checkout Success) --}}
    <div id="modal-direct-payment-success" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px]">
        <div class="relative p-4 w-full max-w-md max-h-[90vh]">
            <div class="relative bg-white rounded-2xl shadow-2xl flex flex-col p-6 items-center text-center">
                <div class="w-24 h-24 mb-4">
                    <img src="{{ Vite::asset('resources/img/cash_payment.gif') }}" class="w-full h-full object-contain image-payment-success-direct" />
                </div>
                <h3 class="text-2xl font-black text-gray-800 tracking-tight">PEMBAYARAN SUKSES</h3>
                <p class="text-xs text-gray-400 font-medium mt-1 mb-6">Transaksi telah berhasil dicatat dan lunas.</p>
                
                <div class="w-full p-4 bg-slate-50 border border-gray-100 rounded-xl mb-6">
                    <ul class="flex flex-col gap-2.5 text-sm text-gray-600">
                        <li class="flex justify-between w-full">
                            <span class="text-gray-400">Invoice</span>
                            <span id="dps-invoice" class="font-bold text-gray-800">#000000</span>
                        </li>
                        <li class="flex justify-between w-full">
                            <span class="text-gray-400">Total Belanja</span>
                            <span id="dps-total" class="font-bold text-gray-800">Rp 0</span>
                        </li>
                        <li class="flex justify-between w-full">
                            <span class="text-gray-400">Jumlah Bayar</span>
                            <span id="dps-paid" class="font-bold text-gray-800">Rp 0</span>
                        </li>
                        <li class="flex justify-between w-full border-t border-dashed border-gray-200 pt-2.5">
                            <span class="font-bold text-gray-800">Kembalian</span>
                            <span id="dps-change" class="font-extrabold text-emerald-600 text-base">Rp 0</span>
                        </li>
                    </ul>
                </div>

                <!-- Bluetooth Printer Control Panel (Dalam Modal Sukses) -->
                <div class="w-full p-4 bg-white border border-gray-200 rounded-xl mb-4 flex flex-col gap-3 text-xs shadow-sm text-left">
                    <div class="flex items-center justify-between flex-wrap gap-2 border-b border-gray-100 pb-2">
                        <span class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">Bluetooth Printer</span>
                        <span id="dps-bt-device-name" class="font-semibold text-emerald-600 hidden truncate max-w-[130px]"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="dps-btn-toggle-bluetooth" class="px-3 py-2 bg-brand hover:bg-brand-strong text-white font-bold rounded-lg flex items-center gap-1.5 transition-all cursor-pointer shadow-sm w-full justify-center text-[11px] outline-none">
                            <i class="fab fa-bluetooth text-[11px]"></i> <span id="dps-bt-status-text">Hubungkan Bluetooth</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between gap-1.5 pt-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode Cetak:</label>
                        <select id="dps-print-method-select" class="px-2 py-1 bg-white border border-brand rounded-lg text-[11px] font-bold focus:outline-none cursor-pointer">
                            <option value="browser" selected>Browser Print (HTML)</option>
                            <option value="bluetooth">Direct Bluetooth</option>
                            <option value="rawbt">RawBT (Android)</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full">
                    <button type="button" id="dps-print-receipt-btn" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm cursor-pointer text-sm flex items-center justify-center gap-1.5">
                        <i class="fas fa-print"></i> Cetak Struk Belanja
                    </button>
                    <button type="button" id="dps-close-btn" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition-colors cursor-pointer text-sm">
                        Selesai & Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="button-to-open-order">

    </div>
    <div class="button-to-open-order">
        <button class="bg-brand text-neutral-50 hover:bg-brand-strong cursor-pointer w-16 h-16 rounded-tl-full rounded-bl-full fixed md:hidden flex justify-center items-center open-order-button open-close-order z-50">
            <i class="fas fa-shopping-cart text-xl"></i>
        </button>
    </div>
    {{-- Order Place --}}
    
    <div class="order-container fixed top-0 right-0 h-full bg-white shadow-lg overflow-hidden z-40">
        <div class="order-form hidden h-full">
            <div class="flex flex-col h-full">
                <input type="hidden" id="transaction-id" name="transaction-id" value="" />
                <div class="customer-name-place text-center px-2 py-4 w-full shadow-md relative shrink-0">
                    <p class="text-gray-500 m-0 text-lg font-semibold order-form-name">Guest</p>
                    <p class="text-gray-400 text-base order-form-id">Order ID #001</p>
                    <button id="edit_transaction_detail" class="absolute top-4 right-4 bg-gray-50 hover:bg-brand group rounded-full p-3 cursor-pointer"><i class="fa-solid fa-pencil text-xl text-gray-500 group-hover:text-white"></i></button>
                </div>
                <div class="customer-detail-place p-2 w-full flex gap-3 shadow shrink-0">
                    <select type="select" id="meja" class="js-example-basic-single w-full bg-gray-50 rounded-full ps-6 pe-2 py-2 border border-gray-200 focus:border-brand focus:ring-2 focus:ring-brand-soft select2 text-sm" >
                        <option value="" disabled selected>Select Table</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->uuid }}">{{ $table->name }}</option>
                        @endforeach
                    </select>
                    <select type="select" id="orderType" class="js-example-basic-single w-full bg-gray-50 rounded-full ps-6 pe-2 py-2 border border-gray-200 focus:border-brand focus:ring-2 focus:ring-brand-soft select2 text-sm" >
                        <option value="" disabled selected>Select Type</option>
                        <option value="take_away">Take Away</option>
                        <option value="dine_in">Dine In</option>
                    </select>
                </div>
                <div class="product-item-detail p-2 w-full flex-1 overflow-y-auto">
                    <button class="bg-gray-50 hover:bg-brand-soft text-gray-700 w-full py-2.5 rounded-lg border border-dashed border-gray-300 font-semibold mb-3 flex items-center justify-center gap-2 cursor-pointer add-manual-item-btn text-sm shrink-0">
                        <i class="fas fa-plus text-xs text-brand"></i> Tambah Item Manual
                    </button>
                    <ul class="product-item-list w-full">
                        
                    </ul>
                </div>
                <div class="order-total-place w-full border-t border-gray-200 shrink-0 flex flex-col items-start bg-white">
                    <div class="flex justify-between items-center p-3 w-full">
                        <p class="text-gray-500 font-semibold">Subtotal</p>
                        <p class="text-lg font-bold order-total-price">Rp. 0,-</p>
                    </div>
                    <div class="flex w-full p-2 pt-0">
                        <button class="submit-order bg-brand text-neutral-50 hover:bg-brand-strong cursor-pointer w-full py-3 rounded-lg font-semibold">Submit Order</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="standby-form flex flex-col h-full">
            @php
                $occupiedTableIds = $transactions->pluck('table_id')->filter()->unique()->toArray();
                $emptyTables = $tables->whereNotIn('uuid', $occupiedTableIds);
            @endphp
            <div class="px-4 pt-5 pb-3 border-b border-gray-100 shrink-0">
                <p class="text-sm font-bold text-gray-700">Mulai Order Baru</p>
                <p class="text-xs text-gray-400 mt-0.5">Pilih meja kosong untuk langsung mulai, atau pesan tanpa meja.</p>
            </div>
            <div id="standby-tables" class="flex-1 overflow-y-auto p-4">
                @if($emptyTables->count() > 0)
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach($emptyTables as $table)
                            <button type="button" class="quick-table-card flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border border-gray-200 bg-gray-50 hover:border-brand hover:bg-brand-soft transition-all cursor-pointer" data-table-uuid="{{ $table->uuid }}">
                                <span class="w-8 h-8 rounded-full {{ $table->color }} flex items-center justify-center text-white text-xs shrink-0">
                                    <i class="fas fa-chair"></i>
                                </span>
                                <span class="text-xs font-semibold text-gray-700 truncate w-full text-center">{{ $table->name }}</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center text-center py-8">
                        <span class="w-12 h-12 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3"><i class="fas fa-chair text-lg"></i></span>
                        <p class="text-xs text-gray-500 font-medium">Semua meja sedang terisi</p>
                    </div>
                @endif
            </div>
            <div class="p-4 border-t border-gray-100 shrink-0">
                <button type="button" class="add-order-button w-full py-3 rounded-xl border-2 border-dashed border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-sm cursor-pointer">
                    <i class="fas fa-bag-shopping mr-1.5"></i> Take Away / Tanpa Meja
                </button>
            </div>
        </div>
    </div>
    @endif
    <div id="printreceiptcheck" class="hidden">
        <!-- Content to be printed -->
        <h1 style="text-align: center; margin:0;padding:0" class="print-receipt">CHECK</h1>
        <h1 style="text-align: center; margin:0;padding:0" class="print-receipt" id="receipt-meja"></h1>
        <p style="text-align: center; margin:0;padding:0" class="print-receipt">=======================</p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Date : <span id="receipt-date">{{ date('Y-m-d H:i:s') }}</span></p>
        <p style="text-align: center; margin:0;padding:0" class="print-receipt">=======================</p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">No Inv : <span id="receipt-invoice-number"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Name : <span id="receipt-customer-name"></span></p>
        <p style="margin:0;padding:0;font-size:13px" class="print-receipt">Order Type : <span id="receipt-order-type"></span></p>
        <p style="text-align: center; margin:0;padding:0; margin-bottom:10px;" class="print-receipt">=======================</p>
        <div id="receipt-items">

        </div>
        <p style="page-break-after: auto !important"></p>

    </div>
    <iframe id="printreceiptcheck-iframe" name="printreceiptcheck" class="hidden" style="font-family: Georgia, 'Times New Roman', Times, serif">
    </iframe>
    <script type="module">
        window.allProducts = @json($products);
        var _currentCartJson = null;
        var _isSyncing = false;

        window.renderCartItems = function(orderItems) {
            var orderList = "";
            var orderTotal = 0;
            
            if (orderItems && orderItems.length > 0) {
                orderItems.forEach(elem => {
                    var productItem = window.allProducts.filter(prod => {
                        return prod.uuid == elem.product_id;
                    })[0];
                    
                    // Fallback for manual item if dummy product is not in allProducts cache
                    if (productItem == null) {
                        productItem = {
                            name: elem.product_name || 'Item Manual',
                            picture: ''
                        };
                    }
                    
                    var image = "";
                    if (productItem.picture != null && productItem.picture != "") {
                        image = "{{ asset('storage/products/:picture') }}".replace(':picture', productItem.picture);
                    } else {
                        image = "{{ Vite::asset('resources/img/no_image_available.png') }}";
                    }
                    
                    orderList += `
                        <li class="flex relative w-full mb-2 border-b-2 border-dashed border-gray-300 p-3" data-uuid="${elem.uuid}" data-subtotal="${elem.subtotal}" data-price="${elem.price}">
                            <div class="product-image h-20 w-20">
                                <img class="h-20 w-20 object-cover rounded-lg" src="${image}">
                            </div>
                            <div class="product-detail ms-2">
                                <p class="text-base text-gray-700">${elem.product_name || productItem.name}</p>
                                <p class="text-sm text-gray-500">Rp. ${addCommas(elem.subtotal)},-</p>
                                <p class="text-sm text-gray-500 product-note">${elem.note ? elem.note : '-'}</p>
                                <button class="rounded-full bg-gray-100 p-1 text-gray-700 cursor-pointer text-sm hover:bg-brand-light hover:text-white edit-product-order"><i class="fas fa-pencil"></i></button>
                            </div>
                            <div class="flex items-center absolute bottom-1 right-1">
                                <button type="button" id="decrement-button" data-input-counter-decrement="counter-input-${elem.uuid}" class="decrement-qty flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary rounded-full text-sm focus:outline-none h-6 w-6">
                                    <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                                </button>
                                <input type="text" id="counter-input-${elem.uuid}" data-input-counter class="input-qty shrink-0 text-heading border-0 bg-transparent text-sm font-normal focus:outline-none focus:ring-0 max-w-[2.5rem] text-center" placeholder="" value="${elem.qty}"/>

                                <button type="button" id="increment-button-${elem.uuid}" data-input-counter-increment="counter-input" class="increment-qty flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary rounded-full text-sm focus:outline-none h-6 w-6">
                                    <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                                </button>
                            </div>
                        </li>
                    `;
                    orderTotal += parseInt(elem.subtotal || 0);
                });
            }
            
            $('.product-item-list').html(orderList);
            $('.order-total-price').html('Rp. ' + addCommas(orderTotal) + ',-');
        };

        const osInstance = OverlayScrollbars(document.querySelector('#categories-list'), {});
        const osInstanc = OverlayScrollbars(document.querySelector('#order-list'), {});
        const osInstances = OverlayScrollbars(document.querySelector('.product-item-detail'), {});

        const $targetEl = document.getElementById("modal-preview-products");
        const $targetEditTransaction = document.getElementById("modal-edit-transaction");
        const $targetEditOrder = document.getElementById("modal-edit-order");
        // options with default values
        const options = {
            placement: "center",
            backdrop: "dynamic",
            backdropClasses: "bg-gray-900/50 fixed inset-0 z-40",
            closable: true,
        };
        const optionsStatic = {
            placement: "center",
            backdrop: "static",
            backdropClasses: "bg-gray-900/50 fixed inset-0 z-40",
            closable: true,
        };
        const modal = new Modal($targetEl,options);
        const modal2 = new Modal($targetEditTransaction,options);
        const modal3 = new Modal($targetEditOrder,optionsStatic);
        const modal4 = new Modal(document.getElementById('modal-see-transaction'),options);
        const modalManual = new Modal(document.getElementById('modal-add-manual-item'), options);
        const helpModal = new Modal(document.getElementById('modal-shortcut-guide'), options);

        function toggleShortcutGuideModal() {
            helpModal.toggle();
        }
        
        function closeShortcutGuideModal() {
            helpModal.hide();
        }
        
        $('#btn-shortcut-guide').on('click', function() {
            toggleShortcutGuideModal();
        });
        
        $('.close-shortcut-modal').on('click', function() {
            closeShortcutGuideModal();
        });

        // --- Keyboard Shortcuts System ---
        $(document).on('keydown', function(e) {
            // F1: Focus Search Input
            if (e.key === 'F1') {
                e.preventDefault();
                $('#searchInput').focus().select();
                showQuickAddToast("Pencarian Produk Aktif (F1)");
            }
            
            // F2: Primary Action (Checkout / Submit Order / Process Payment)
            if (e.key === 'F2') {
                e.preventDefault();
                
                // Check if payment modal is visible
                var dpEl = $('#modal-direct-payment');
                if (dpEl.length > 0 && !dpEl.hasClass('hidden') && dpEl.is(':visible')) {
                    $('#dp-process-payment-btn').click();
                } else {
                    var processBtn = $('.process-to-payment-button');
                    var submitBtn = $('.submit-order');
                    
                    if (processBtn.length > 0 && processBtn.is(':visible')) {
                        processBtn.click();
                    } else if (submitBtn.length > 0 && submitBtn.is(':visible')) {
                        submitBtn.click();
                    } else {
                        oAlert("orange", "Warning", "Tidak ada tombol aksi utama (Submit/Bayar) yang aktif saat ini.");
                    }
                }
            }
            
            // F3: Toggle Quick Add Mode
            if (e.key === 'F3') {
                e.preventDefault();
                $('#quick-add-checkbox').click();
            }
            
            // F4: Add Order
            if (e.key === 'F4') {
                e.preventDefault();
                var addOrderBtn = $('.add-order-button');
                if (addOrderBtn.length > 0) {
                    addOrderBtn.click();
                }
            }
            
            // F10: Shortcut Help Guide Modal
            if (e.key === 'F10') {
                e.preventDefault();
                toggleShortcutGuideModal();
            }
            
            // ESC: Close Modals (or, if none are open, close the active order form)
            if (e.key === 'Escape') {
                var anyModalOpen = $('#modal-preview-products, #modal-edit-transaction, #modal-edit-order, #modal-add-manual-item, #modal-direct-payment, #modal-direct-payment-success, #modal-shortcut-guide, #modal-attendance')
                    .filter(function() { return !$(this).hasClass('hidden'); }).length > 0;

                if (!$('#modal-shortcut-guide').hasClass('hidden')) {
                    closeShortcutGuideModal();
                }

                if (typeof modal !== 'undefined') modal.hide();
                if (typeof modal2 !== 'undefined') modal2.hide();
                if (typeof modal3 !== 'undefined') modal3.hide();
                if (typeof modalManual !== 'undefined') modalManual.hide();
                if (typeof dpModal !== 'undefined') dpModal.hide();
                if (typeof dpsModal !== 'undefined') dpsModal.hide();

                $('#modal-attendance').addClass('hidden');

                // Tidak ada modal yang sedang terbuka — anggap ESC sebagai "tutup order, kembali ke pemilihan meja"
                if (!anyModalOpen && !$('.order-form').hasClass('hidden')) {
                    closeOrderForm();
                }
            }
        });

        // --- Toggle Order List (Billing Queues at bottom) ---
        var orderListCollapsed = localStorage.getItem('order_list_collapsed') === 'true';
        if (orderListCollapsed) {
            $('.order-list').addClass('collapsed');
            $('#toggle-order-icon').removeClass('fa-minus').addClass('fa-plus');
        } else {
            $('.order-list').removeClass('collapsed');
            $('#toggle-order-icon').removeClass('fa-plus').addClass('fa-minus');
        }

        $('#btn-toggle-order-list').on('click', function(e) {
            e.stopPropagation();
            var $orderList = $(this).closest('.order-list');
            $orderList.toggleClass('collapsed');
            
            var isCollapsed = $orderList.hasClass('collapsed');
            localStorage.setItem('order_list_collapsed', isCollapsed);
            
            if (isCollapsed) {
                $('#toggle-order-icon').removeClass('fa-minus').addClass('fa-plus');
            } else {
                $('#toggle-order-icon').removeClass('fa-plus').addClass('fa-minus');
            }
        });

        // --- Quick Add Mode Setting ---
        var quickAddMode = localStorage.getItem('quick_add_mode') === 'true';
        $('#quick-add-checkbox').prop('checked', quickAddMode);

        $('#quick-add-checkbox').on('change', function() {
            quickAddMode = $(this).is(':checked');
            localStorage.setItem('quick_add_mode', quickAddMode);
            
            if (quickAddMode) {
                oAlert('green', 'Success', 'Mode Quick Add Diaktifkan! Klik produk langsung masuk keranjang.', true);
            } else {
                oAlert('orange', 'Info', 'Mode Quick Add Dinonaktifkan. Klik produk akan membuka detail.', true);
            }
        });

        // Small side toast notification for Quick Add
        function showQuickAddToast(productName) {
            const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            const toastHtml = `
                <div id="${toastId}" class="flex items-center gap-3 bg-white/95 backdrop-blur-md border border-emerald-100/50 shadow-[0_10px_30px_rgba(0,0,0,0.06)] rounded-2xl px-4 py-3 min-w-[240px] max-w-sm pointer-events-auto transform translate-y-2 opacity-0 transition-all duration-300">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                        <i class="fas fa-circle-check text-sm animate-bounce"></i>
                    </div>
                    <div class="flex-grow overflow-hidden pr-2">
                        <p class="text-xs font-bold text-gray-800 truncate">${productName}</p>
                        <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-wider mt-0.5">Berhasil Ditambahkan</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer border-none outline-none bg-transparent text-xs self-center" onclick="document.getElementById('${toastId}').remove()">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            `;
            
            $('#quick-add-toast-container').append(toastHtml);
            
            // Trigger animation in
            setTimeout(() => {
                $(`#${toastId}`).removeClass('translate-y-2 opacity-0').addClass('translate-y-0 opacity-100');
            }, 30);
            
            // Auto fadeout and remove
            setTimeout(() => {
                $(`#${toastId}`).removeClass('translate-y-0 opacity-100').addClass('translate-y-2 opacity-0');
                setTimeout(() => {
                    $(`#${toastId}`).remove();
                }, 300);
            }, 2500);
        }

        // Buka modal tambah item manual
        $('.product-item-detail').on('click', '.add-manual-item-btn', function() {
            var activeId = $('#transaction-id').val();
            if (!activeId) {
                oAlert("orange", "Warning", "Tidak ada transaksi aktif. Silakan pilih atau buat orderan terlebih dahulu.");
                return;
            }

            // Reset inputs
            $('#manual_item_name').val('');
            $('#manual_item_price').val('');
            $('#manual_item_qty').val(1);
            $('#manual_item_description').val('');

            modalManual.toggle();

            $('.tutup-modal-manual').off('click').on('click', function() {
                modalManual.hide();
            });
        });

        // Simpan item manual
        $('#modal-add-manual-item').on('click', '.save-manual-item-btn', function() {
            var idOrder = $('#transaction-id').val();
            var name = $('#manual_item_name').val().trim();
            var price = $('#manual_item_price').val();
            var qty = $('#manual_item_qty').val();
            var description = $('#manual_item_description').val().trim();

            if (!name) {
                oAlert("orange", "Warning", "Nama item tidak boleh kosong");
                return;
            }
            if (price === "" || price < 0) {
                oAlert("orange", "Warning", "Harga item tidak valid");
                return;
            }
            if (!qty || qty <= 0) {
                oAlert("orange", "Warning", "Quantity minimal 1");
                return;
            }

            loading();
            var url = "{{ route('transaction.order.create', ':id') }}";
            url = url.replace(':id', idOrder);

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    'is_manual': true,
                    'custom_name': name,
                    'custom_price': price,
                    'qty': qty,
                    'description': description
                },
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    _currentCartJson = null; // force reload active cart on next poll
                    modalManual.hide();
                    doPoll();
                },
                error: function(err) {
                    removeLoading();
                    console.log(err.responseJSON ? err.responseJSON.message : err);
                    oAlert("red", "Error", "Gagal menambahkan item manual");
                }
            });
        });
        
        //Tutup buka Samping Orderan untuk layar kecil
        $('.open-close-order').on('click',function() {
            $('.order-container').toggleClass('active');
            $(this).toggleClass('active');
        });

        // Helper: tutup sidebar orderan di mobile
        function closeMobileSidebar() {
            $('.order-container').removeClass('active');
            $('.open-close-order').removeClass('active');
        }

        // Helper: tutup form order aktif, kembali ke tampilan pemilihan meja (standby)
        function closeOrderForm() {
            $('.order-form').addClass('hidden');
            $('.standby-form').removeClass('hidden');
            $('.order-container').removeClass('active');
            $('.open-close-order').removeClass('active');
            $('#order .customer').removeClass('bg-brand-soft');
        }

        // Helper: hitung ulang total keranjang dari semua item data-subtotal
        function recalculateCartTotal() {
            var total = 0;
            $('.product-item-list li[data-subtotal]').each(function() {
                total += parseInt($(this).attr('data-subtotal') || 0);
            });
            $('.order-total-price').text('Rp. ' + addCommas(total) + ',-');
        }

        // tombol buka modul
        $('.product-item').on('click',function(){
            var uuid = $(this).data('uuid');
            var idOrder = $('#transaction-id').val();

            if (quickAddMode) {
                if (!idOrder) {
                    oAlert("orange", "Warning", "Tidak ada transaksi aktif. Silakan pilih atau buat orderan terlebih dahulu.");
                    return;
                }

                var urlOrder = "{{ route('transaction.order.create',':id') }}";
                urlOrder = urlOrder.replace(':id', idOrder);
                
                $.ajax({
                    type: "POST",
                    url: urlOrder,
                    data: {
                        'idProduct': uuid,
                        'description': '',
                        'qty': 1
                    },
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(data) {
                        if (data.orderItem) {
                            showQuickAddToast(data.product.name);
                            _currentCartJson = null; // force reload active cart on next poll
                            doPoll();
                        }
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                        oAlert("red", "Error", "Gagal menambahkan produk ke keranjang.");
                    }
                });
                return;
            }

            //removeAllModalPreviousData
            $('.picture_product').attr('src','');
            $(".category-product").removeClass (function (index, className) {
                return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
            });
            $('.product-name').text('');
            $('.product-description').text('');
            $('.product-price').text('');
            $('#uuid_product').val('');
            $('#descriptionOrder').val('');
            $('#quantity-input').val(1);

            var url = "{{ route('products.show',':id') }}";
            url = url.replace(':id',uuid);

            loading();
            $.ajax({
                type: "GET",
                url : url,
                success: function(data) {
                    
                    if(data.success === true) {
                        var product = data.product;
                        if(product.picture != "") {
                        var picture = "{{ asset('storage/products/:picture') }}";
                        picture = picture.replace(':picture',product.picture);
                        } else {
                            var picture = "{{ Vite::asset('resources/img/no_image_available.png') }}";
                        }
                        $('.picture_product').attr('src',picture);
                        
                        // Categories-setting
                        var categoriesName = product.category ? product.category.nama : 'Uncategorized';
                        var categoriesColor = product.category ? product.category.color : 'bg-gray-100 text-gray-800';
                        $('.category-product').text(categoriesName);
                        $('.category-product').addClass(categoriesColor);

                        //Detail Products
                        $('.product-name').text(product.name);
                        $('.product-description').text(product.description);
                        var price = "Rp."+addCommas(product.price)+",-";
                        $('.product-price').text(price);
                        $('#uuid_product').val(product.uuid);
                        
                        

                        //open Modal
                        removeLoading();
                        
                        modal.toggle();

                        $('.tutup-modal').on('click',function() {
                            modal.hide();
                        });
                    }
                }
            });
        });
        // tombol tambah list barang
        $('.place-order-button').on('click',function() {
            var idOrder = $('#transaction-id').val();
            var qty = $('#quantity-input').val();

            if(idOrder == "") {
                oAlert("orange","Warning","There is no active transaction");
            } else if (qty == "" || qty == 0) {
                oAlert("orange","Warning","Jumlah Barang tidak boleh kosong");
            } else {
                loading();
                var url = "{{ route('transaction.order.create',':id') }}";
                url = url.replace(':id',idOrder);
                var dataItem = $('#uuid_product').val();
                var deskripsiOrder = $('#descriptionOrder').val();
                $.ajax({
                    type: "POST",
                    url : url,
                    data : {'idProduct' : dataItem,'description' : deskripsiOrder,
                    'qty' : qty},
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success : function(data) {
                        removeLoading();
                        var orderItem = data.orderItem;
                        var productItem = data.product;
                        
                        if(productItem != null) {
                            if(productItem.picture != null && productItem.picture != "") {
                                var image = "{{ asset('storage/products/:picture') }}";
                                image = image.replace(':picture',productItem.picture);
                            } else {
                                var image = "{{ Vite::asset('resources/img/no_image_available.png') }}";
                            }
                            
                            var orderList = `
                                <li class="flex relative w-full mb-2 border-b-2 border-dashed border-gray-300 p-3" data-uuid="${orderItem.uuid}" data-subtotal="${orderItem.subtotal}" data-price="${orderItem.price}">
                                    <div class="product-image h-20 w-20">
                                        <img class="h-20 w-20 object-cover rounded-lg" src="${image}">
                                    </div>
                                    <div class="product-detail ms-2">
                                        <p class="text-base text-gray-700">${productItem.name}</p>
                                        <p class="text-sm text-gray-500">Rp. ${addCommas(orderItem.subtotal)},-</p>
                                        <p class="text-sm text-gray-500 product-note">${orderItem.note ? orderItem.note : '-'}</p>
                                        <button class="rounded-full bg-gray-100 p-1 text-gray-700 cursor-pointer text-sm hover:bg-brand-light hover:text-white edit-product-order"><i class="fas fa-pencil"></i></button>
                                    </div>
                                    <div class="flex items-center absolute bottom-1 right-1">
                                        <button type="button" id="decrement-button" data-input-counter-decrement="counter-input-${orderItem.uuid}" class="decrement-qty flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary rounded-full text-sm focus:outline-none h-6 w-6">
                                            <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                                        </button>
                                        <input type="text" id="counter-input-${orderItem.uuid}" data-input-counter class="input-qty shrink-0 text-heading border-0 bg-transparent text-sm font-normal focus:outline-none focus:ring-0 max-w-[2.5rem] text-center" placeholder="" value="${orderItem.qty}"/>

                                        <button type="button" id="increment-button-${orderItem.uuid}" data-input-counter-increment="counter-input" class="increment-qty flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary rounded-full text-sm focus:outline-none h-6 w-6">
                                            <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                                        </button>
                                    </div>
                                </li>
                            `;
                        }
                        $('.product-item-list').append(orderList);
                        recalculateCartTotal();
                        _currentCartJson = null; // force reload active cart on next poll
                        doPoll();
                        modal.hide();
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                })
            }
        });
        // tombol filter per categori
        $('.category_filter').on('click',function() {
            var uuid = $(this).val();

            if(uuid == 'all-item') {
                $('.product-item').removeClass('hidden');
            } else if(uuid == 'best-seller') {
                $('.product-item').filter(function(){
                    return $(this).data('best-seller') != 1    
                }).addClass('hidden');
                $('.product-item').filter(function(){
                    return $(this).data('best-seller') == 1    
                }).removeClass('hidden');
            } else {
                $('.product-item').filter(function(){
                    return $(this).data('category') != uuid    
                }).addClass('hidden');
                $('.product-item').filter(function(){
                    return $(this).data('category') == uuid    
                }).removeClass('hidden');
            }
        });
        // tombol mencari
        $('#searchInput').on('keyup',function() {
            var value = this.value.toLowerCase().trim();
            var selected_filter = $('input[name="category_filter"]:checked').val();
            if(selected_filter == 'all-item') {
                $(".product-item").removeClass('hidden').filter(function() {
                    return $(this).find('.name-product').text().toLowerCase().trim().indexOf(value) == -1;
                }).addClass('hidden');
            } else if(selected_filter == 'best-seller') {
                $(".product-item[data-best-seller='1']").removeClass('hidden').filter(function() {
                    return $(this).find('.name-product').text().toLowerCase().trim().indexOf(value) == -1;
                }).addClass('hidden');
            } else {
                $(".product-item[data-category='"+selected_filter+"']").removeClass('hidden').filter(function() {
                    return $(this).find('.name-product').text().toLowerCase().trim().indexOf(value) == -1;
                }).addClass('hidden');
            }
            // console.log('a');
        });
        // Tombol tambah order (opsional: langsung set meja, dipakai oleh quick-table-card)
        function startNewOrder(presetTableUuid) {
            loading();
            $.ajax({
                type: "POST",
                url: "{{ route('transaction.create') }}",
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    var transaction = data.transaction;

                    // Tampilkan form order
                    $('.order-form').removeClass('hidden');
                    $('.standby-form').addClass('hidden');
                    $('.order-container').addClass('active');
                    $('.open-close-order').addClass('active');
                    $('#transaction-id').val(transaction.uuid);
                    $('.order-form-id').text("Order ID #"+transaction.invoice_number);
                    
                    if (presetTableUuid) {
                        // Langsung set meja & tipe order, lalu trigger change supaya
                        // tersimpan ke backend lewat handler #meja/#orderType yang sudah ada.
                        $('#meja').val(presetTableUuid).trigger('change');
                        $('#orderType').val('dine_in').trigger('change');
                    } else {
                        _isSyncing = true;
                        $('#meja').val("");
                        $('#orderType').val("");
                        _isSyncing = false;
                    }

                    _currentCartJson = JSON.stringify([]);
                    $('.product-item-list').html('');
                    $('.order-total-price').text("Rp. 0,-");
                    $('#order .customer').removeClass('bg-brand-soft');

                    // Render kartu order LANGSUNG dari response — tanpa menunggu polling
                    var uid       = transaction.uuid;
                    var name      = transaction.customer_name || 'Guest';
                    var table     = (transaction.table && transaction.table.name) ? transaction.table.name : 'Unset Table';
                    var orderType = transaction.order_type || '';
                    var time      = moment(transaction.created_at).format('HH:mm');
                    var badge     = statusClass(transaction.status || 'active');

                    var $existing = $('.order-item.customer[data-uuid="' + uid + '"]');
                    if (!$existing.length) {
                        var $card = $('<li class="order-item customer border border-gray-400 w-44 md:w-55 h-16 md:h-20 bg-primary border-primary hover:bg-brand-softer cursor-pointer rounded-lg p-2 md:p-3 relative" data-uuid="' + uid + '">'
                            + '  <p class="order-name text-sm md:text-lg text-neutral-700 mb-1 md:mb-2 font-medium truncate pr-14">' + name + '</p>'
                            + '  <div class="flex order-subdetail justify-between pr-6">'
                            + '    <p class="table-detail text-xs md:text-sm text-neutral-400 truncate"><span class="table-detail-place">' + table + '</span> - <span class="order-type-detail">' + orderType + '</span></p>'
                            + '    <p class="time-detail text-xs md:text-sm text-neutral-400 whitespace-nowrap ml-1">' + time + '</p>'
                            + '  </div>'
                            + '  <p class="order-status ' + badge + ' px-2 py-0.5 rounded-full text-[10px] md:text-xs font-semibold whitespace-nowrap absolute top-2 right-2 md:top-4">' + (transaction.status || 'active') + '</p>'
                            + '  <button type="button" class="toggle-items-btn absolute bottom-1.5 right-1.5 md:bottom-2 md:right-2 h-5 w-5 rounded-full bg-gray-50 hover:bg-brand-soft hover:text-brand flex items-center justify-center text-gray-400 cursor-pointer shadow-xs border border-gray-200 z-10"><i class="fas fa-chevron-down text-[10px]"></i></button>'
                            + '  <div class="items-list-wrapper hidden mt-2.5 pt-2.5 border-t border-dashed border-gray-300">'
                            + '    <ul class="order-items-list text-[11px] md:text-xs text-neutral-600 space-y-1"></ul>'
                            + '    <div class="border-t border-dashed border-gray-200 mt-2 pt-1.5 flex justify-between items-center text-[10px] md:text-xs text-neutral-500">'
                            + '      <span>Total Order:</span>'
                            + '      <span class="total-order-count font-bold text-neutral-700">0 items</span>'
                            + '    </div>'
                            + '  </div>'
                            + '</li>');

                        $('#order-item-button').after($card);

                        // Tandai aktif
                        $card.addClass('bg-brand-soft');

                        // Tambah ke daftar uuid yang diketahui agar polling tidak re-insert
                        if (_knownUuids.indexOf(uid) === -1) {
                            _knownUuids.push(uid);
                        }
                    }

                    removeLoading();
                },
                error: function(data) {
                    removeLoading();
                    oAlert('red', 'Error', 'Gagal membuat order baru.');
                    console.log(data.responseJSON ? data.responseJSON.message : data);
                }

            });
        }

        $('.add-order-button').on('click', function() {
            startNewOrder();
        });

        $('#standby-tables').on('click', '.quick-table-card', function() {
            startNewOrder($(this).data('table-uuid'));
        });

        // Toggle Tampilan Menu/Item di Kartu Antrian
        $('#order').on('click', '.toggle-items-btn', function(e) {
            e.stopPropagation(); // Mencegah pemilihan order saat tombol diklik
            var $btn = $(this);
            var $card = $btn.closest('li.customer');
            var $wrapper = $card.find('.items-list-wrapper');
            var $icon = $btn.find('i');
            
            var isExpanded = $card.hasClass('expanded');
            if (isExpanded) {
                // Smooth slide collapse
                $wrapper.slideUp(200, function() {
                    $card.removeClass('expanded');
                    $wrapper.addClass('hidden'); // Keep it hidden for css safety
                });
                $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                // Smooth slide expand
                $card.addClass('expanded');
                $wrapper.hide().removeClass('hidden').slideDown(200);
                $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        });

        // tombol melihat kembali order lama
        $('#order').on('click','.customer',function() {
            $('#meja').val("");
            $('#orderType').val("");
            $('.product-item-list').html('');
            $('#order .customer').removeClass('bg-brand-soft');
            $(this).addClass('bg-brand-soft');
            var currentTotal = parseInt($('.order-total-price').text().replace(/[^0-9]/g,''));
            var newTotal = 0;
            $('.order-total-price').text("Rp. "+addCommas(newTotal)+",-");
            $('#uuid_transaction_detail').val('');


            var uuid = $(this).data('uuid');
            var url = "{{ route('transaction.show',':id') }}";
            url = url.replace(':id',uuid);
            loading();
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    if(data.success == true) {
                        if(data.transaction.status == 'process') {
                            var transaction = data.transaction;
                            $('#uuid_transaction_detail').val(transaction.uuid);

                            var product = data.product;

                            if(transaction.order_item.length > 0) {
                                var orderList = "";
                                var orderTotal = 0;
                                transaction.order_item.forEach(elem => {
                                    var productItem = product.filter(prod => {
                                        return prod.uuid == elem.product_id;
                                    })[0];
                                    
                                    // Fallback for manual item if dummy product is not in database product active query
                                    if (productItem == null) {
                                        productItem = {
                                            name: elem.product_name || 'Item Manual',
                                            picture: ''
                                        };
                                    }
                                    
                                    var image = "";
                                    if (productItem.picture != null && productItem.picture != "") {
                                        var image = "{{ asset('storage/products/:picture') }}";
                                        image = image.replace(':picture',productItem.picture);
                                    } else {
                                        var image = "{{ Vite::asset('resources/img/no_image_available.png') }}";
                                    }
                                    
                                    orderList += `
                                        <li class="flex relative w-full mb-2 border-b-2 border-dashed border-gray-300 p-3" data-uuid="${elem.uuid}" data-subtotal="${elem.subtotal}" data-price="${elem.price}">
                                            <div class="product-image h-20 w-20">
                                                <img class="h-20 w-20 object-cover rounded-lg" src="${image}">
                                            </div>
                                            <div class="product-detail ms-2">
                                                <p class="text-base text-gray-700">${elem.product_name || productItem.name}</p>
                                                <p class="text-sm text-gray-500">Rp. ${addCommas(elem.subtotal)},-</p>
                                                <p class="text-sm text-gray-500 product-note">${elem.note ? elem.note : '-'}</p>
                                            </div>
                                            <div class="flex absolute bottom-1 right-1">
                                                ${elem.qty}x
                                            </div>
                                        </li>
                                    `;
                                    orderTotal += elem.subtotal; 
                                    $('.transaction-detail-list').html(orderList);
                                    $('.transaction-detail-total-price').html('Rp. ' + addCommas(orderTotal) + ',-');
                                }); 
                            }
                            modal4.toggle();
                            removeLoading();

                            $('.tutup-modal-order').on('click',function() {
                                modal4.hide();
                            });
                        } else if(data.transaction.status == "payment") {
                            removeLoading();
                            openDirectPaymentModal(data.transaction);
                        } else {
                            var transaction = data.transaction;
                            $('#transaction-id').val(transaction.uuid);
                            $('.order-form-name').text(transaction.customer_name ? transaction.customer_name : "Guest" );
                            
                            _isSyncing = true;
                            $('#meja').val(transaction.table ? transaction.table.uuid : "");
                            $('#orderType').val(transaction.order_type ? transaction.order_type : "");
                            _isSyncing = false;
                            
                            $('.order-form-id').text("Order ID #"+transaction.invoice_number);
                            $('.order-form').removeClass('hidden');
                            $('.standby-form').addClass('hidden');
                            $('.order-container').addClass('active');
                            $('.open-close-order').addClass('active');
                            
                            _currentCartJson = JSON.stringify(transaction.order_item);
                            renderCartItems(transaction.order_item);
                            removeLoading();
                        }
                        
                    }
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                } 
            });
        });
        // Bluetooth — auto-reconnect saat halaman dimuat
        initBluetoothUI();

        // Bluetooth Printer Toggler (Global di Halaman Home & Semua Modal Pembayaran)
        $(document).on('click', '#btn-toggle-bluetooth, #dp-btn-toggle-bluetooth, #dps-btn-toggle-bluetooth', async function() {
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

        //Print Check Order (Kitchen/No Price Check)
        $('#modal-see-transaction').on('click','.print-transaction-button',function() {
            loading();
            var transactionId = $('#uuid_transaction_detail').val();
            var url = "{{ route('transaction.print.check.noprice',':id') }}";
            url = url.replace(':id',transactionId);
            
            $.ajax({
                method: 'GET',
                url: url,
                success: async function(data) {
                    removeLoading();
                    if(data.success == true) {
                        const method = $('#print-method-select').val();
                        const noPrice = true; // Home details printed are always process (kitchen/no price check)
                        
                        if (method === 'bluetooth') {
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
                            try {
                                const bytes = buildEscPosReceipt(data, noPrice);
                                window.printViaRawBT(bytes);
                                oAlert('green', 'Success', 'Struk dikirim ke RawBT.');
                            } catch (e) {
                                oAlert('red', 'Error', 'Gagal memicu RawBT.');
                            }
                        } else {
                            printHtmlReceipt(data, noPrice);
                        }
                    } else {
                        oAlert('red', 'Error', 'Gagal memuat data struk.');
                    }
                },
                error: function(data) {
                    removeLoading();
                    oAlert('red', 'Error', 'Gagal mengambil data struk.');
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
            encoder.line('================================');

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
                    encoder.line(`Qty: ${elem.qty}`);
                } else {
                    const qtyPrice = `${elem.qty} x Rp ${addCommas(elem.price || (elem.subtotal/elem.qty))}`;
                    const itemTotal = `Rp ${addCommas(elem.subtotal)}`;
                    encoder.twoColumnRow(qtyPrice, itemTotal);
                }
            });

            encoder.line('--------------------------------');

            if (!noPrice) {
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

            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 250);
        }
        $('#modal-see-transaction').on('click','.edit-transaction-button',function() {
            var transactionId = $('#uuid_transaction_detail').val();
            // Get original status from DOM before changing it to active
            var originalStatus = $('.order-item.customer[data-uuid='+transactionId+']').find('.order-status').text().trim().toLowerCase();
            $('#modal-edit-transaction').attr('data-original-status', originalStatus);
            modal4.hide();
            var url = "{{ route('transaction.update',':id') }}";
            url = url.replace(':id',transactionId);
            loading();
            $.ajax({
                method: 'POST',
                url: url,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    var transaction = data.transaction;
                    $('#transaction-id').val(transaction.uuid);
                    $('.order-form-name').text(transaction.customer_name ? transaction.customer_name : "Guest" );
                    
                    _isSyncing = true;
                    $('#meja').val(transaction.table ? transaction.table.uuid : "");
                    $('#orderType').val(transaction.order_type ? transaction.order_type : "");
                    _isSyncing = false;
                    
                    $('.order-form-id').text("Order ID #"+transaction.invoice_number);
                    $('.order-form').removeClass('hidden');
                    $('.standby-form').addClass('hidden');
                    
                    _currentCartJson = JSON.stringify(transaction.order_item);
                    renderCartItems(transaction.order_item);
                    
                    // Update status badge class and text in UI instantly to active
                    var badge = statusClass('active');
                    $('.order-item.customer[data-uuid='+transactionId+']').find('.order-status')
                        .text("active")
                        .attr('class', 'order-status ' + badge + ' px-2 py-0.5 rounded-full text-[10px] md:text-xs font-semibold whitespace-nowrap absolute top-2 right-2 md:top-4');
                    
                    recalculateCartTotal();
                    renderCartItems(transaction.order_item);
                    modal4.hide();
                    removeLoading();
                }
            });
        });

        function performDelete(idTransaction, adminPassword, modal) {
            loading();
            var url = "{{ route('transaction.delete',':id') }}";
            url = url.replace(':id',idTransaction);
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
                    removeLoading();
                    if (data.success) {
                        oAlert("green", "Success", "Transaksi berhasil dihapus.");
                        // If deleting active transaction, reset forms
                        if ($('#transaction-id').val() == idTransaction) {
                            $('#transaction-id').val("");
                            $('.order-form').addClass('hidden');
                            $('.standby-form').removeClass('hidden');
                        }
                        $('.order-item.customer[data-uuid='+idTransaction+']').remove();
                        closeMobileSidebar();
                        if (modal) modal.hide();
                    } else {
                        oAlert("red", "Error", data.message || "Gagal menghapus transaksi.");
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

        $('#modal-see-transaction').on('click','.delete-transaction-button',function() {
            var idTransaction = $('#uuid_transaction_detail').val();
            var status = $('.order-item.customer[data-uuid='+idTransaction+']').find('.order-status').text().trim().toLowerCase();
            const userRole = "{{ auth()->user()->role }}";

            cConfirm("Warning","Are you sure want to delete this transaction?",function() {
                // If status is not active and user is not admin, ask for password approval
                if (status !== 'active' && userRole !== 'admin') {
                    window.requestAdminApproval(function(password) {
                        if (!password) {
                            oAlert("orange", "Batal", "Penghapusan transaksi dibatalkan.");
                            return;
                        }
                        performDelete(idTransaction, password, modal4);
                    });
                } else {
                    performDelete(idTransaction, null, modal4);
                }
            });
        });

        $('.product-item-list').on('click','.decrement-qty',function() {
            var inputval = $(this).closest('li').find('.input-qty').val();
            var idProduct = $(this).closest('li').data('uuid');
            var ini = this;
            loading();
            var url = "{{ route('transaction.order.decrement',':id') }}";
            url = url.replace(':id',idProduct);
            $.ajax({
                type:"POST",
                url: url,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    _currentCartJson = null; // force reload active cart on next poll
                    var orderItem = data.orderItem;
                    if(orderItem == null) {
                        $(ini).closest('li').remove();
                        recalculateCartTotal();
                        doPoll();
                    } else {
                        if(orderItem.qty >= 1) {
                            $(ini).closest('li').find('.input-qty').val(orderItem.qty);
                            $(ini).closest('li').find('.product-detail p:nth-child(2)').text("Rp. "+addCommas(orderItem.subtotal)+",-");
                            $(ini).closest('li').attr('data-subtotal', orderItem.subtotal);
                            recalculateCartTotal();
                            doPoll();
                        }
                    }
                },
            })
            
            // if(inputval >= 1) {
            //     $(this).closest('li').find('.input-qty').val(parseInt(inputval) - 1);
            // }
        });
        //Tambahkan jumlah Order
        $('.product-item-list').on('click','.increment-qty',function() {
            var inputval = $(this).closest('li').find('.input-qty').val();
            var idProduct = $(this).closest('li').data('uuid');
            var ini = this;
            loading();
            var url = "{{ route('transaction.order.increment',':id') }}";
            url = url.replace(':id',idProduct);
            $.ajax({
                type:"POST",
                url: url,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    _currentCartJson = null; // force reload active cart on next poll
                    var orderItem = data.orderItem;
                    $(ini).closest('li').find('.input-qty').val(orderItem.qty);
                    $(ini).closest('li').find('.product-detail p:nth-child(2)').text("Rp. "+addCommas(orderItem.subtotal)+",-");
                    $(ini).closest('li').attr('data-subtotal', orderItem.subtotal);
                    recalculateCartTotal();
                    doPoll();
                },
            })
        });
        //Ganti Jumlah Order Manual
        var oldVal = 0;
        $('.product-item-list').on('focus','.input-qty',function() {
            oldVal = $(this).val();
        });
        $('.product-item-list').on('change','.input-qty',function() {
            var inputval = $(this).val();
            var idProduct = $(this).closest('li').data('uuid');
            var ini = this;
            if(inputval >= 1) {
                loading();
                var url = "{{ route('transaction.order.changeQty',':id') }}";
                url = url.replace(':id',idProduct);
                $.ajax({
                    type:"POST",
                    url: url,
                    data: {'qty' : inputval},
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(data) {
                        removeLoading();
                        _currentCartJson = null; // force reload active cart on next poll
                        var orderItem = data.orderItem;
                        $(ini).closest('li').find('.input-qty').val(orderItem.qty);
                        $(ini).closest('li').find('.product-detail p:nth-child(2)').text("Rp. "+addCommas(orderItem.subtotal)+",-");
                        $(ini).closest('li').attr('data-subtotal', orderItem.subtotal);
                        recalculateCartTotal();
                        doPoll();
                    },
                })
            } else {
                oAlert("orange","Warning","Quantity must be at least 1");
                $(this).val(oldVal);
            }
        });
        //Edit Order Item (Nama, Harga, Catatan)
        $('.product-item-list').on('click','.edit-product-order',function() {
            var $li    = $(this).closest('li');
            var orderId = $li.data('uuid');

            // Reset fields
            $('#uuid_order').val('');
            $('#order_product_name').val('');
            $('#order_price').val('');
            $('#order_description').val('');

            // Pre-fill dari DOM
            var currentName  = $li.find('.product-detail p:first-child').text().trim();
            var currentPrice = parseInt($li.attr('data-price') || 0);
            var currentNote  = $li.find('.product-note').text().trim();
            if(currentNote === '-') currentNote = '';

            $('#uuid_order').val(orderId);
            $('#order_product_name').val(currentName);
            $('#order_price').val(currentPrice > 0 ? currentPrice : '');
            $('#order_description').val(currentNote);

            modal3.toggle();

            $('.tutup-modal-order').off('click').on('click',function() {
                modal3.hide();
            });
        });
        // Simpan semua perubahan (nama, harga, catatan) dalam satu request
        $('.save-note-button').on('click',function() {
            var orderID     = $('#uuid_order').val();
            var productName = $('#order_product_name').val().trim();
            var price       = $('#order_price').val();
            var note        = $('#order_description').val();

            if (!orderID) return;

            loading();

            // 1. Simpan nama & harga dulu
            var urlDetail = "{{ route('transaction.order.changeDetail',':id') }}";
            urlDetail = urlDetail.replace(':id', orderID);
            $.ajax({
                type: "POST",
                url: urlDetail,
                data: { product_name: productName, price: price },
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(detailData) {
                    // 2. Simpan catatan
                    var urlNote = "{{ route('transaction.order.changeNote',':id') }}";
                    urlNote = urlNote.replace(':id', orderID);
                    $.ajax({
                        type: "POST",
                        url: urlNote,
                        data: { note: note },
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        success: function() {
                            _currentCartJson = null; // force reload active cart on next poll
                            var $li = $('li[data-uuid="' + orderID + '"]');
                            var orderItem = detailData.orderItem;

                            // Update DOM langsung
                            if (productName) {
                                $li.find('.product-detail p:first-child').text(productName);
                            }
                            if (price !== '' && !isNaN(price)) {
                                $li.find('.product-detail p:nth-child(2)').text('Rp. ' + addCommas(orderItem.subtotal) + ',-');
                                $li.attr('data-subtotal', orderItem.subtotal);
                                $li.attr('data-price', orderItem.price);
                                recalculateCartTotal();
                            }
                            $li.find('.product-note').text(note ? note : '-');
                            doPoll();

                            modal3.hide();
                            removeLoading();
                        },
                        error: function(err) {
                            console.log(err.responseJSON ? err.responseJSON.message : err);
                            removeLoading();
                        }
                    });
                },
                error: function(err) {
                    console.log(err.responseJSON ? err.responseJSON.message : err);
                    removeLoading();
                }
            });
        });

        //Hapus Orderan
        $('.delete-order-button').on('click',function() {
            var orderID = $('#uuid_order').val();
            cConfirm("Warning","Are you sure want to delete this order?",function() {
                loading();
                var url = "{{ route('transaction.order.delete',':id') }}";
                url = url.replace(':id',orderID);
                $.ajax({
                    type: "DELETE",
                    url: url,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(data) {
                        _currentCartJson = null; // force reload active cart on next poll
                        $('li[data-uuid="'+orderID+'"]').remove();
                        recalculateCartTotal();
                        doPoll();
                        modal3.hide();
                        removeLoading();
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                })
            });
        });
        //Ganti No Meja
        $('.customer-detail-place').on('change','#meja',function() {
            if (_isSyncing) return;
            var idTable = $(this).val();
            var idTransaction = $('#transaction-id').val();
            loading();
            var url = "{{ route('transaction.order.changeTable',':id') }}";
            url = url.replace(':id',idTransaction);
            $.ajax({
                type: "POST",
                url: url,
                data: {'table_id' : idTable},
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    var tableName = data.orderItem.table ? data.orderItem.table.name : "Unset Table";
                    $('.order-item.customer[data-uuid='+idTransaction+']').find('.table-detail-place').text(tableName);
                },error: function(data) {
                    console.log(data.responseJSON.message);
                }
            })
        });
        //Ganti Tipe Order
        $('.customer-detail-place').on('change','#orderType',function() {
            if (_isSyncing) return;
            var order = $(this).val();
            var idTransaction = $('#transaction-id').val();
            loading();
            var url = "{{ route('transaction.order.changeOrderType',':id') }}";
            url = url.replace(':id',idTransaction);
            $.ajax({
                type: "POST",
                url: url,
                data: {'order' : order},
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    $('.order-item.customer[data-uuid='+idTransaction+']').find('.order-type-detail').text(order);
                },error: function(data) {
                    console.log(data.responseJSON.message);
                }
            })
        });
        //edit Detail Transaksi
        $('#edit_transaction_detail').on('click',function() {
            var id = $('#transaction-id').val();
            var url = "{{ route('transaction.show',':id') }}";
            url = url.replace(':id',id);
            loading();
            $.ajax({
                type: "GET",
                url: url,
                success: function(data) {
                    removeLoading();
                    if(data.success == true) {
                        var transaction = data.transaction;
                        $('#customer_name').val(transaction.customer_name);
                        $('#transaction-detail-name-place').val(transaction.customer_name);
                        modal2.toggle();
                    }
                },
                error: function(data) {
                    console.log(data.responseJSON.message);
                }
            });

            $('.save-name-button').off('click').on('click',function() {
                var name = $('#customer_name').val();
                var idTransaction = $('#transaction-id').val();
                loading();
                var url = "{{ route('transaction.order.changeName',':id') }}";
                url = url.replace(':id',idTransaction);
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {'name' : name},
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(data) {
                        removeLoading();
                        $('.order-form-name').text(name ? name : "Guest");
                        $('.order-item.customer[data-uuid='+idTransaction+']').find('.order-name').text(name ? name : "Guest");
                        modal2.hide();
                    },error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                });
            });

            $('#modal-edit-transaction .delete-transaction-button').off('click').on('click',function() {
                var idTransaction = $('#transaction-id').val();
                var originalStatus = $('#modal-edit-transaction').attr('data-original-status') || 'active';
                const userRole = "{{ auth()->user()->role }}";

                cConfirm("Warning","Are you sure want to delete this transaction?",function() {
                    if (originalStatus !== 'active' && userRole !== 'admin') {
                        window.requestAdminApproval(function(password) {
                            if (!password) {
                                oAlert("orange", "Batal", "Penghapusan transaksi dibatalkan.");
                                return;
                            }
                            performDelete(idTransaction, password, modal2);
                            $('#modal-edit-transaction').removeAttr('data-original-status');
                        });
                    } else {
                        performDelete(idTransaction, null, modal2);
                        $('#modal-edit-transaction').removeAttr('data-original-status');
                    }
                });
            });
            
            $('#modal-edit-transaction .tutup-modal-transaction').off('click').on('click',function() {
                modal2.hide();
            });
        });
        //Submit Orderan
        $('.submit-order').on('click',function() {
            var idTransaction = $('#transaction-id').val();
            
            // Check if orderType is selected
            var orderType = $('#orderType').val();
            if (!orderType) {
                oAlert("red", "Peringatan", "Silakan pilih tipe pesanan (Take Away atau Dine In) terlebih dahulu!");
                return;
            }

            cConfirm("Confirmation","Are you sure want to submit this order?",function() {
                loading();
                var url = "{{ route('transaction.submit',':id') }}";
                url = url.replace(':id',idTransaction);
                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(data) {
                        if(data.success == true) {   
                            removeLoading();
                            var badge = statusClass('process');
                            $('.order-item.customer[data-uuid='+idTransaction+']').find('.order-status')
                                .text("process")
                                .attr('class', 'order-status ' + badge + ' px-2 py-0.5 rounded-full text-[10px] md:text-xs font-semibold whitespace-nowrap absolute top-2 right-2 md:top-4');
                            doPoll();
                            $('.order-item.customer[data-uuid='+idTransaction+']').removeClass('bg-brand-soft');
                            $('#transaction-id').val("");
                            $('.order-form').addClass('hidden');
                            $('.standby-form').removeClass('hidden');
                            closeMobileSidebar();
                        } else {
                            removeLoading();
                            oAlert("red","Error",data.message);
                        }
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message); 
                    }
                });
            });
        });

        // ── Fitur Integrasi Pembayaran Langsung (Direct Checkout) di Halaman Home ──
        let dpModal = null;
        let dpsModal = null;

        // Inisialisasi Flowbite Modal instance
        if (document.getElementById('modal-direct-payment')) {
            dpModal = new Modal(document.getElementById('modal-direct-payment'));
        }
        if (document.getElementById('modal-direct-payment-success')) {
            dpsModal = new Modal(document.getElementById('modal-direct-payment-success'));
        }

        // Tutup modal jika tombol silang diklik
        $(document).on('click', '.dp-close-modal', function() {
            if (dpModal) dpModal.hide();
        });

        // Fungsi Global untuk Membuka Modal Pembayaran Langsung (Direct Checkout)
        window.openDirectPaymentModal = function(tx) {
            // Isi data transaksi di modal
            $('#dp-transaction-uuid').val(tx.uuid);
            $('#dp-invoice-number').text('#' + tx.invoice_number);
            $('#dp-customer-name').text(tx.customer_name || 'Guest');
            
            // Rincian Biaya
            $('#dp-subtotal-text').text('Rp ' + addCommas(tx.subtotal));
            $('#dp-tax-text').text('Rp ' + addCommas(tx.tax || 0));
            $('#dp-discount-text').text('-Rp ' + addCommas(tx.discount || 0));
            $('#dp-total-text').text('Rp ' + addCommas(tx.total));
            
            // Input
            $('#dp-discount-input').val(tx.discount || '');
            $('#dp-paid-amount').val(tx.total); // default isi dengan uang pas
            $('#dp-change-text').text('Rp 0').removeClass('text-red-500').addClass('text-emerald-600');
            
            // Pilihan metode printer & status Bluetooth
            const currentMethod = $('#print-method-select').val() || 'browser';
            $('#dp-print-method-select').val(currentMethod);
            
            if (window.bluetoothPrinterInstance && window.bluetoothPrinterInstance.isConnected()) {
                const devName = window.bluetoothPrinterInstance.device.name || 'BT Printer';
                $('#dp-bt-device-name').removeClass('hidden').text(devName);
            } else {
                $('#dp-bt-device-name').addClass('hidden').text('');
            }

            // Render item belanja
            let itemsHtml = "";
            const items = tx.order_item || tx.order_items || [];
            items.forEach(item => {
                itemsHtml += `
                    <li class="flex justify-between items-center bg-white p-2.5 rounded-lg border border-gray-100 text-xs">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800">${item.product_name}</span>
                            ${item.note ? `<span class="text-[10px] text-amber-600 font-medium">Catatan: ${item.note}</span>` : ''}
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-gray-400 font-medium">x${item.qty}</span>
                            <span class="font-bold text-gray-700">Rp ${addCommas(item.subtotal)}</span>
                        </div>
                    </li>
                `;
            });
            $('#dp-order-items-list').html(itemsHtml);

            // Buka modal
            if (dpModal) dpModal.show();
        };

        // Selesaikan Orderan dan buka Modal Pembayaran Langsung secara Instan
        $('.process-to-payment-button').on('click', function() {
            var uuid = $('#uuid_transaction_detail').val();

            cConfirm("Konfirmasi Pembayaran", "Apakah Anda yakin ingin memproses transaksi ini ke pembayaran langsung?", function() {
                loading();
                // Tutup modal detail see-transaction terlebih dahulu
                $('#modal-see-transaction').find('.tutup-modal-order, .tutup-modal').first().click();

                var url = "{{ route('transaction.payment.proceed', ':id') }}";
                url = url.replace(':id', uuid);

                $.ajax({
                    type: "POST",
                    url: url,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    success: function(data) {
                        removeLoading();
                        if (data.success == true) {
                            openDirectPaymentModal(data.transaction);
                        } else {
                            oAlert("red", "Error", data.message);
                        }
                    },
                    error: function(data) {
                        removeLoading();
                        oAlert("red", "Error", "Gagal memproses transaksi.");
                    }
                });
            });
        });

        // Terapkan Diskon AJAX
        $('#dp-apply-discount-btn').on('click', function() {
            const uuid = $('#dp-transaction-uuid').val();
            const discountVal = parseFloat($('#dp-discount-input').val()) || 0;

            loading();
            let url = "{{ route('transaction.payment.discount', ':id') }}";
            url = url.replace(':id', uuid);

            $.ajax({
                type: "POST",
                url: url,
                data: { discount: discountVal },
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    if (data.success == true) {
                        // Perbarui teks di layar
                        $('#dp-discount-text').text('-Rp ' + data.discount_formatted);
                        $('#dp-tax-text').text('Rp ' + data.tax_formatted);
                        $('#dp-total-text').text('Rp ' + data.total_formatted);
                        
                        // Perbarui paid amount ke total baru
                        $('#dp-paid-amount').val(data.total).trigger('input');
                        oAlert("green", "Sukses", "Diskon berhasil diterapkan.");
                    } else {
                        oAlert("red", "Error", "Gagal menerapkan diskon.");
                    }
                },
                error: function() {
                    removeLoading();
                    oAlert("red", "Error", "Gagal menghubungi server.");
                }
            });
        });

        // Hitung Kembalian Real-time
        $('#dp-paid-amount').on('input keyup', function() {
            const paid = parseFloat($(this).val()) || 0;
            const totalText = $('#dp-total-text').text().replace(/[^\d]/g, '');
            const total = parseFloat(totalText) || 0;
            const change = paid - total;
            
            if (change >= 0) {
                $('#dp-change-text').text('Rp ' + addCommas(change)).removeClass('text-red-500').addClass('text-emerald-600');
            } else {
                $('#dp-change-text').text('-Rp ' + addCommas(Math.abs(change))).removeClass('text-emerald-600').addClass('text-red-500');
            }
        });

        // Sinkronkan metode cetak modal ke halaman utama
        $('#dp-print-method-select').on('change', function() {
            $('#print-method-select').val($(this).val()).trigger('change');
        });

        // Cetak Tagihan (Check Receipt)
        $('#dp-print-check-btn').on('click', function() {
            const uuid = $('#dp-transaction-uuid').val();
            loading();
            
            var url = "{{ route('transaction.print.check', ':id') }}";
            url = url.replace(':id', uuid);

            $.ajax({
                method: 'GET',
                url: url,
                success: async function(data) {
                    removeLoading();
                    if (data.success == true) {
                        const method = $('#dp-print-method-select').val();
                        
                        if (method === 'bluetooth') {
                            if (!window.bluetoothPrinterInstance.isConnected()) {
                                oAlert('orange', 'Warning', 'Printer Bluetooth belum terhubung. Silakan hubungkan terlebih dahulu.');
                                return;
                            }
                            try {
                                const bytes = buildEscPosReceipt(data, false);
                                await window.bluetoothPrinterInstance.print(bytes);
                                oAlert('green', 'Printed', 'Struk tagihan berhasil dicetak via Bluetooth.');
                            } catch (e) {
                                oAlert('red', 'Error', 'Gagal mencetak: ' + e.message);
                            }
                        } else if (method === 'rawbt') {
                            try {
                                const bytes = buildEscPosReceipt(data, false);
                                window.printViaRawBT(bytes);
                            } catch (e) {
                                oAlert('red', 'Error', 'Gagal memanggil RawBT: ' + e.message);
                            }
                        } else {
                            // HTML Browser Print
                            printHtmlReceipt(data, false);
                        }
                    } else {
                        oAlert("red", "Error", "Gagal mengambil data cetak.");
                    }
                },
                error: function() {
                    removeLoading();
                    oAlert("red", "Error", "Gagal memproses cetak.");
                }
            });
        });

        // Finalisasi Selesaikan Pembayaran
        $('#dp-process-payment-btn').on('click', function() {
            const uuid = $('#dp-transaction-uuid').val();
            const method = $('input[name="dp_payment_method"]:checked').val() || 'cash';
            const amount = parseFloat($('#dp-paid-amount').val()) || 0;
            const totalText = $('#dp-total-text').text().replace(/[^\d]/g, '');
            const total = parseFloat(totalText) || 0;

            if (amount < total) {
                oAlert("orange", "Peringatan", "Jumlah pembayaran kurang dari total tagihan!");
                return;
            }

            loading();
            let url = "{{ route('transaction.payment.finalize', ':id') }}";
            url = url.replace(':id', uuid);

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    method: method,
                    amount: amount
                },
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    removeLoading();
                    if (data.success == true) {
                        const tx = data.transaction;
                        
                        // Sembunyikan modal pembayaran
                        if (dpModal) dpModal.hide();

                        // Set GIF Animasi sesuai metode bayar
                        let gifUrl = "";
                        if (method === 'qris') {
                            gifUrl = "{{ Vite::asset('resources/img/qris_payment.gif') }}";
                        } else if (method === 'transfer') {
                            gifUrl = "{{ Vite::asset('resources/img/credit_card_payment.gif') }}";
                        } else {
                            gifUrl = "{{ Vite::asset('resources/img/cash_payment.gif') }}";
                        }
                        
                        $('.image-payment-success-direct').attr('src', gifUrl);

                        // Isi data modal sukses
                        $('#dps-invoice').text('#' + tx.invoice_number);
                        $('#dps-total').text('Rp ' + addCommas(tx.total));
                        $('#dps-paid').text('Rp ' + addCommas(tx.total_paid));
                        
                        const change = tx.total_paid - tx.total;
                        $('#dps-change').text('Rp ' + addCommas(change));
                        
                        // Simpan UUID transaksi sukses di tombol cetak
                        $('#dps-print-receipt-btn').data('uuid', tx.uuid);

                        // Sinkronkan metode cetak & status printer di modal sukses
                        const currentMethod = $('#print-method-select').val() || 'browser';
                        $('#dps-print-method-select').val(currentMethod);
                        if (window.bluetoothPrinterInstance && window.bluetoothPrinterInstance.isConnected()) {
                            const devName = window.bluetoothPrinterInstance.device.name || 'BT Printer';
                            $('#dps-bt-device-name').removeClass('hidden').text(devName);
                        } else {
                            $('#dps-bt-device-name').addClass('hidden').text('');
                        }

                        // Tampilkan modal sukses
                        if (dpsModal) dpsModal.show();
                    } else {
                        oAlert("red", "Error", data.message);
                    }
                },
                error: function() {
                    removeLoading();
                    oAlert("red", "Error", "Gagal memproses pembayaran lunas.");
                }
            });
        });

        // Cetak Struk Belanja Lunas (Final Receipt)
        $('#dps-print-receipt-btn').on('click', function() {
            const uuid = $(this).data('uuid');
            loading();

            let url = "{{ route('transaction.print.payment', ':id') }}";
            url = url.replace(':id', uuid);

            $.ajax({
                method: 'GET',
                url: url,
                success: async function(data) {
                    removeLoading();
                    if (data.success == true) {
                        const method = $('#dps-print-method-select').val();
                        
                        if (method === 'bluetooth') {
                            if (!window.bluetoothPrinterInstance.isConnected()) {
                                oAlert('orange', 'Warning', 'Printer Bluetooth belum terhubung. Silakan hubungkan terlebih dahulu.');
                                return;
                            }
                            try {
                                const bytes = buildEscPosReceipt(data, false);
                                await window.bluetoothPrinterInstance.print(bytes);
                                oAlert('green', 'Printed', 'Struk final belanja berhasil dicetak via Bluetooth.');
                            } catch (e) {
                                oAlert('red', 'Error', 'Gagal mencetak: ' + e.message);
                            }
                        } else if (method === 'rawbt') {
                            try {
                                const bytes = buildEscPosReceipt(data, false);
                                window.printViaRawBT(bytes);
                            } catch (e) {
                                oAlert('red', 'Error', 'Gagal memanggil RawBT: ' + e.message);
                            }
                        } else {
                            // HTML Browser Print
                            printHtmlReceipt(data, false);
                        }
                    } else {
                        oAlert("red", "Error", "Gagal mengambil data cetak.");
                    }
                },
                error: function() {
                    removeLoading();
                    oAlert("red", "Error", "Gagal memproses cetak struk final.");
                }
            });
        });

        // Sinkronkan metode cetak modal sukses ke halaman utama
        $('#dps-print-method-select').on('change', function() {
            $('#print-method-select').val($(this).val()).trigger('change');
        });

        // Tutup Modal Sukses & Reset Keranjang secara Dinamis (Tanpa Reload agar Bluetooth Tetap Konek)
        $('#dps-close-btn').on('click', function() {
            if (dpsModal) dpsModal.hide();
            
            // Ambil UUID transaksi yang telah diselesaikan
            const completedUuid = $('#dps-print-receipt-btn').data('uuid');
            
            // 1. Hapus transaksi dari baris antrean di bagian bawah
            if (completedUuid) {
                $('.order-item.customer[data-uuid="' + completedUuid + '"]').remove();
            }
            
            // 2. Reset status keranjang belanja (sidebar kanan)
            $('#transaction-id').val("");
            $('#uuid_transaction_detail').val("");
            $('.product-item-list').html('');
            $('.order-total-price').text("Rp. 0,-");
            
            // 3. Kembalikan sidebar ke standby form
            $('.order-form').addClass('hidden');
            $('.standby-form').removeClass('hidden');
            
            // 4. Tutup sidebar mobile jika aktif
            $('.order-container').removeClass('active');
            $('.open-close-order').removeClass('active');
            
            if (typeof liveToast === 'function') {
                liveToast('Transaksi berhasil diselesaikan!');
            }
        });

        // ═══════════════════════════════════════════════════════════════
        // LIVE UPDATES — AJAX Polling (kompatibel dengan Windows / php artisan serve)
        // Setiap 3 detik browser request data terbaru dari server.
        // ═══════════════════════════════════════════════════════════════

        // ── 1. Live status badge di navbar ────────────────────────────────
        (function() {
            var badge = '<div id="live-badge" style="'
                + 'display:inline-flex;align-items:center;gap:6px;'
                + 'background:#fff;border:1px solid #e5e7eb;border-radius:999px;'
                + 'padding:5px 13px;font-size:12px;color:#6b7280;'
                + 'box-shadow:0 1px 4px rgba(0,0,0,.08);margin-left:8px;'
                + 'user-select:none;flex-shrink:0;">'
                + '<span id="live-dot" style="width:8px;height:8px;border-radius:50%;'
                + 'background:#d1d5db;display:inline-block;transition:background .4s,box-shadow .4s;"></span>'
                + '<span id="live-label" style="transition:color .4s;">Connecting…</span>'
                + '</div>';
            $('.navbar-container').append(badge);
        })();

        function setLiveStatus(status) {
            var dot   = document.getElementById('live-dot');
            var label = document.getElementById('live-label');
            if (!dot || !label) return;
            if (status === 'live') {
                dot.style.background  = '#22c55e';
                dot.style.boxShadow   = '0 0 0 3px rgba(34,197,94,.3)';
                label.textContent     = '● Live';
                label.style.color     = '#16a34a';
            } else if (status === 'offline') {
                dot.style.background  = '#ef4444';
                dot.style.boxShadow   = 'none';
                label.textContent     = '● Offline';
                label.style.color     = '#dc2626';
            } else {
                dot.style.background  = '#f59e0b';
                dot.style.boxShadow   = '0 0 0 3px rgba(245,158,11,.25)';
                label.textContent     = '● Connecting…';
                label.style.color     = '#b45309';
            }
        }

        // Helper untuk memainkan suara notifikasi sederhana
        function playNotificationSound() {
            try {
                var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                var oscillator = audioCtx.createOscillator();
                var gainNode = audioCtx.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                // Suara "ding" sederhana
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                oscillator.frequency.exponentialRampToValueAtTime(1760, audioCtx.currentTime + 0.1); // A6
                
                gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                
                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.3);
            } catch (e) {
                console.log("Audio not supported or blocked by autoplay policy");
            }
        }

        // ── 2. Toast notifikasi ───────────────────────────────────────────
        function liveToast(msg) {
            playNotificationSound();
            
            var id  = 'lt' + Date.now();
            var $el = $('<div id="' + id + '">'
                + '<span style="color:#4ade80;font-size:15px;">⟳</span> ' + msg
                + '</div>').css({
                    position:'fixed', bottom:'80px', right:'20px', zIndex:9999,
                    background:'#1f2937', color:'#f9fafb', borderRadius:'10px',
                    padding:'10px 16px', fontSize:'13px',
                    display:'flex', alignItems:'center', gap:'8px',
                    boxShadow:'0 4px 18px rgba(0,0,0,.35)',
                    opacity:0, transform:'translateY(8px)',
                    transition:'opacity .3s, transform .3s'
                });
            $('body').append($el);
            setTimeout(function() { $el.css({ opacity:1, transform:'translateY(0)' }); }, 10);
            setTimeout(function() {
                $el.css({ opacity:0, transform:'translateY(8px)' });
                setTimeout(function() { $el.remove(); }, 350);
            }, 3500);
        }

        // ── 3. Kelas badge status transaksi ───────────────────────────────
        function statusClass(s) {
            if (s === 'active')  return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            if (s === 'process') return 'bg-brand-soft text-brand-light border border-brand-medium';
            if (s === 'payment') return 'bg-amber-100 text-amber-800 border border-accent-yellow shadow-xs';
            return 'bg-gray-100 text-gray-700 border border-gray-200';
        }

        // ── 4. Seed UUID yang sudah diketahui dari render awal ────────────
        var _knownUuids = [];
        @foreach($transactions as $t)
            _knownUuids.push('{{ $t->uuid }}');
        @endforeach

        // ── 5. Sinkronisasi sidebar order dengan data terbaru ─────────────
        function syncTransactions(incoming) {
            var activeId    = $('#transaction-id').val();
            var incomingIds = incoming.map(function(t) { return t.uuid; });

            // Hapus kartu order yang sudah tidak ada di server
            _knownUuids.forEach(function(uid) {
                if (incomingIds.indexOf(uid) === -1) {
                    var $card = $('.order-item.customer[data-uuid="' + uid + '"]');
                    if ($card.length) {
                        $card.fadeOut(300, function() { $(this).remove(); });
                        if (activeId === uid) {
                            $('#transaction-id').val('');
                            $('.order-form').addClass('hidden');
                            $('.standby-form').removeClass('hidden');
                            closeMobileSidebar();
                            liveToast('Order ditutup dari perangkat lain.');
                        }
                    }
                }
            });
            _knownUuids = incomingIds;

            // Tambah / update kartu order
            incoming.forEach(function(trx) {
                var uid       = trx.uuid;
                var name      = trx.customer_name || 'Guest';
                var table     = (trx.table && trx.table.name) ? trx.table.name : 'Unset Table';
                var orderType = trx.order_type || '';
                var time      = moment(trx.created_at).format('HH:mm');
                var badge     = statusClass(trx.status);
                var $existing = $('.order-item.customer[data-uuid="' + uid + '"]');

                if ($existing.length) {
                    // Update in-place tanpa reload
                    $existing.find('.order-name').text(name);
                    $existing.find('.table-detail-place').text(table);
                    $existing.find('.order-type-detail').text(orderType);
                    $existing.find('.order-status')
                        .text(trx.status)
                        .attr('class', 'order-status ' + badge + ' px-2 py-0.5 rounded-full text-[10px] md:text-xs font-semibold whitespace-nowrap absolute top-2 right-2 md:top-4');
                    
                    // Check expansion state
                    var isExpanded = $existing.hasClass('expanded');
                    
                    var itemsHtml = "";
                    var orderItems = trx.order_item || trx.orderItem || [];
                    var itemsCount = orderItems.length;
                    if (orderItems.length > 0) {
                        orderItems.forEach(function(item) {
                            itemsHtml += '<li class="flex justify-between items-start">'
                                      +  '  <span class="truncate max-w-[180px]">' + item.qty + 'x ' + (item.product_name || 'Item') + '</span>'
                                      +  '</li>';
                        });
                    }
                    $existing.find('.order-items-list').html(itemsHtml);
                    $existing.find('.total-order-count').text(itemsCount + ' items');
                    
                    var $wrapper = $existing.find('.items-list-wrapper');
                    var $icon = $existing.find('.toggle-items-btn i');
                    if (isExpanded) {
                        $wrapper.show().removeClass('hidden');
                        $existing.addClass('expanded');
                        $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    } else {
                        $wrapper.hide().addClass('hidden');
                        $existing.removeClass('expanded');
                        $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    }
                } else {
                    // Kartu baru — insert setelah tombol "Add Order"
                    var itemsHtml = "";
                    var orderItems = trx.order_item || trx.orderItem || [];
                    var itemsCount = orderItems.length;
                    if (orderItems.length > 0) {
                        orderItems.forEach(function(item) {
                            itemsHtml += '<li class="flex justify-between items-start">'
                                      +  '  <span class="truncate max-w-[180px]">' + item.qty + 'x ' + (item.product_name || 'Item') + '</span>'
                                      +  '</li>';
                        });
                    }

                    var $card = $('<li class="order-item customer border border-gray-400 w-44 md:w-55 h-16 md:h-20 bg-primary border-primary hover:bg-brand-softer cursor-pointer rounded-lg p-2 md:p-3 relative" data-uuid="' + uid + '">'
                        + '  <p class="order-name text-sm md:text-lg text-neutral-700 mb-1 md:mb-2 font-medium truncate pr-14">' + name + '</p>'
                        + '  <div class="flex order-subdetail justify-between pr-6">'
                        + '    <p class="table-detail text-xs md:text-sm text-neutral-400 truncate"><span class="table-detail-place">' + table + '</span> - <span class="order-type-detail">' + orderType + '</span></p>'
                        + '    <p class="time-detail text-xs md:text-sm text-neutral-400 whitespace-nowrap ml-1">' + time + '</p>'
                        + '  </div>'
                        + '  <p class="order-status ' + badge + ' px-2 py-0.5 rounded-full text-[10px] md:text-xs font-semibold whitespace-nowrap absolute top-2 right-2 md:top-4">' + trx.status + '</p>'
                        + '  <button type="button" class="toggle-items-btn absolute bottom-1.5 right-1.5 md:bottom-2 md:right-2 h-5 w-5 rounded-full bg-gray-50 hover:bg-brand-soft hover:text-brand flex items-center justify-center text-gray-400 cursor-pointer shadow-xs border border-gray-200 z-10"><i class="fas fa-chevron-down text-[10px]"></i></button>'
                        + '  <div class="items-list-wrapper hidden mt-2.5 pt-2.5 border-t border-dashed border-gray-300">'
                        + '    <ul class="order-items-list text-[11px] md:text-xs text-neutral-600 space-y-1">'
                        +        itemsHtml
                        + '    </ul>'
                        + '    <div class="border-t border-dashed border-gray-200 mt-2 pt-1.5 flex justify-between items-center text-[10px] md:text-xs text-neutral-500">'
                        + '      <span>Total Order:</span>'
                        + '      <span class="total-order-count font-bold text-neutral-700">' + itemsCount + ' items</span>'
                        + '    </div>'
                        + '  </div>'
                        + '</li>');
                    
                    $('#order-item-button').after($card);
                    liveToast('Order baru masuk: ' + name);
                }

                // ── SINKRONISASI LIVE UNTUK ITEM DI DALAM CART SIDEBAR ──
                if (uid === activeId) {
                    var orderItems = trx.order_item || trx.orderItem || [];
                    var newCartJson = JSON.stringify(orderItems);
                    if (newCartJson !== _currentCartJson) {
                        // Hanya update jika user tidak sedang fokus mengedit qty atau catatan di cart
                        if ($('.product-item-list input:focus, .product-item-list textarea:focus').length === 0) {
                            _currentCartJson = newCartJson;
                            renderCartItems(orderItems);

                            $('.order-form-name').text(trx.customer_name ? trx.customer_name : "Guest");
                            $('.order-form-id').text("Order ID #" + trx.invoice_number);

                            _isSyncing = true;
                            if ($('#meja').val() !== (trx.table_id || '')) {
                                $('#meja').val(trx.table_id || '').trigger('change');
                            }
                            if ($('#orderType').val() !== (trx.order_type || '')) {
                                $('#orderType').val(trx.order_type || '').trigger('change');
                            }
                            _isSyncing = false;
                        }
                    }
                }
            });

            // Update the Fullscreen Queue Grid live
            if (typeof rebuildFullscreenQueueGrid === 'function') {
                rebuildFullscreenQueueGrid(incoming);
            }
        }

        // ── Fullscreen Queue Modal Triggers & Card Handlers ──
        $(document).on('click', '#btn-fullscreen-order-list', function() {
            $('#modal-fullscreen-queue').removeClass('hidden');
            $('.open-close-order').hide();
        });

        $(document).on('click', '#btn-close-fullscreen-queue', function() {
            $('#modal-fullscreen-queue').addClass('hidden');
            $('.open-close-order').show();
        });

        $(document).on('click', '#fullscreen-queue-grid .customer', function() {
            var uuid = $(this).data('uuid');
            $('#modal-fullscreen-queue').addClass('hidden');
            $('.open-close-order').show();
            
            // Trigger click on corresponding bottom bar card
            var $targetCard = $('#order .order-item.customer[data-uuid="' + uuid + '"]');
            if ($targetCard.length) {
                $targetCard.click();
            }
        });

        function rebuildFullscreenQueueGrid(incoming) {
            var $grid = $('#fullscreen-queue-grid');
            if (!$grid.length) return;
            
            var gridHtml = "";
            incoming.forEach(function(trx) {
                var uid       = trx.uuid;
                var name      = trx.customer_name || 'Guest';
                var table     = (trx.table && trx.table.name) ? trx.table.name : 'Unset Table';
                var orderType = trx.order_type || 'Dining';
                var time      = moment(trx.created_at).format('HH:mm');
                
                var badge_class = "";
                if(trx.status === 'active') {
                    badge_class = "bg-emerald-500/15 text-emerald-400 border border-emerald-500/25";
                } else if(trx.status === 'process') {
                    badge_class = "bg-brand/25 text-brand-light border border-brand/35";
                } else if(trx.status === 'payment') {
                    badge_class = "bg-amber-500/15 text-amber-400 border border-amber-500/25 shadow-sm";
                } else {
                    badge_class = "bg-white/10 text-white/70 border border-white/20";
                }
                
                var itemsHtml = "";
                var orderItems = trx.order_item || trx.orderItem || [];
                orderItems.forEach(function(item) {
                    itemsHtml += '<li class="flex justify-between gap-1 items-start">'
                              +  '  <span class="font-bold text-gray-800 shrink-0">' + item.qty + 'x</span>'
                              +  '  <span class="truncate flex-grow text-left pl-1.5 text-gray-600">' + (item.product_name || 'Item') + '</span>'
                              +  '</li>';
                });
                
                gridHtml += '<div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-4 flex flex-col justify-between hover:shadow-md hover:border-gray-300 transition-all duration-300 relative group cursor-pointer customer" data-uuid="' + uid + '">'
                         +  '  <div class="pb-3 border-b border-gray-100 shrink-0">'
                         +  '    <div class="flex items-start justify-between gap-2">'
                         +  '      <h4 class="text-base font-extrabold text-gray-800 truncate max-w-[70%]" title="' + name + '">' + name + '</h4>'
                         +  '      <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider ' + badge_class + '">' + trx.status + '</span>'
                         +  '    </div>'
                         +  '    <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mt-1 font-semibold">'
                         +  '      <span><i class="fas fa-chair text-[9px]"></i> ' + table + '</span>'
                         +  '      <span>•</span>'
                         +  '      <span><i class="fas fa-utensils text-[9px]"></i> ' + orderType + '</span>'
                         +  '      <span>•</span>'
                         +  '      <span><i class="fas fa-clock text-[9px]"></i> ' + time + '</span>'
                         +  '    </div>'
                         +  '  </div>'
                         +  '  <div class="py-3 flex-grow overflow-y-auto max-h-48 custom-scrollbar">'
                         +  '    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Item Pesanan</p>'
                         +  '    <ul class="text-[11px] text-gray-600 space-y-1.5">'
                         +         itemsHtml
                         +  '    </ul>'
                         +  '  </div>'
                         +  '  <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] font-bold text-gray-500 shrink-0">'
                         +  '    <span>Jumlah Item:</span>'
                         +  '    <span class="text-gray-800">' + orderItems.length + ' Items</span>'
                         +  '  </div>'
                         +  '</div>';
            });
            
            $grid.html(gridHtml);
        }

        // ── 6. Polling AJAX setiap 3 detik ───────────────────────────────
        var _pollUrl       = '{{ route("transaction.live-updates") }}';
        var _pollActive    = true;
        var _pollFailCount = 0;

        function doPoll() {
            if (!_pollActive) return;

            $.ajax({
                type: 'GET',
                url: _pollUrl,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(data) {
                    _pollFailCount = 0;
                    setLiveStatus('live');
                    if (data.success && Array.isArray(data.transactions)) {
                        syncTransactions(data.transactions);
                    }
                },
                error: function() {
                    _pollFailCount++;
                    setLiveStatus(_pollFailCount >= 2 ? 'offline' : 'connecting');
                }
            });
        }

        // Mulai polling pertama kali
        doPoll();
        // Polling berikutnya setiap 3 detik
        var _pollTimer = setInterval(doPoll, 3000);

        // Hentikan polling saat tab ditutup
        window.addEventListener('beforeunload', function() {
            _pollActive = false;
            clearInterval(_pollTimer);
        });

        // ── 7. Fullscreen Image Viewer ──────────────────────────────────
        $('.picture_product').on('click', function() {
            var src = $(this).attr('src');
            if (src && src.trim() !== '') {
                $('#fullscreen-image-element').attr('src', src);
                $('#modal-fullscreen-image').removeClass('hidden');
                setTimeout(function() {
                    $('#modal-fullscreen-image').removeClass('opacity-0').addClass('opacity-100');
                }, 10);
            }
        });

        function closeFullscreenImage() {
            $('#modal-fullscreen-image').removeClass('opacity-100').addClass('opacity-0');
            setTimeout(function() {
                $('#modal-fullscreen-image').addClass('hidden');
                $('#fullscreen-image-element').attr('src', '');
            }, 300);
        }

        $('#close-fullscreen-image, #modal-fullscreen-image').on('click', function(e) {
            // Close only if click is on background or close button (not on the image itself)
            if (e.target.id === 'modal-fullscreen-image' || e.target.id === 'close-fullscreen-image' || $(e.target).closest('#close-fullscreen-image').length > 0) {
                closeFullscreenImage();
            }
        });

        // Also close when clicking the zoomed image itself (intuitive gesture)
        $('#fullscreen-image-element').on('click', function() {
            closeFullscreenImage();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$('#modal-fullscreen-image').hasClass('hidden')) {
                closeFullscreenImage();
            }
        });

        // ═══════════════════════════════════════════════════════════════
        // END LIVE UPDATES
        // ═══════════════════════════════════════════════════════════════
    </script>
@endsection

