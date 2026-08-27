@extends('layout.index')
@section('title','Resep & HPP')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">RESEP &amp; HPP</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-utensils text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ count($productsWithRecipe) }} / {{ $products->count() }} Menu Berresep</span>
        </div>
    </div>
@endsection

@section('container')
<div class="container-place w-full p-4 sm:p-6 flex gap-5 flex-wrap flex-col bg-gray-50/50">

    @if(session('success_recipe'))
        <div class="flex items-center p-4 text-sm text-emerald-700 rounded-2xl bg-emerald-50 border border-emerald-100" role="alert">
            <i class="me-2 fas fa-check"></i>
            <p><span class="font-medium me-1">Sukses!</span> {{ session('success_recipe') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-start p-4 text-sm text-red-700 rounded-2xl bg-red-50 border border-red-100" role="alert">
            <i class="me-2 mt-0.5 fas fa-circle-exclamation"></i>
            <div>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        </div>
    @endif

    @if($products->isEmpty() || $supplyItems->isEmpty())
        <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
            <div class="flex flex-col items-center justify-center text-center py-10">
                <span class="w-14 h-14 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 text-xl"><i class="fas fa-utensils"></i></span>
                <p class="text-sm font-bold text-gray-700">
                    {{ $products->isEmpty() ? 'Belum ada produk menu' : 'Belum ada bahan baku' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $products->isEmpty()
                        ? 'Tambahkan produk menu terlebih dahulu.'
                        : 'Tambahkan bahan baku di Master Barang agar bisa dipakai dalam resep.' }}
                </p>
                @if($supplyItems->isEmpty() && !$products->isEmpty())
                    <a href="{{ route('supply-item.index') }}" class="mt-4 bg-brand hover:bg-brand-strong text-white text-sm font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all">
                        <i class="fas fa-warehouse"></i> Ke Master Barang
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ============ PILIH PRODUK ============ --}}
            <div class="lg:col-span-1 p-5 bg-white rounded-3xl shadow-sm border border-gray-100 self-start">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Pilih Menu</p>
                        <p class="text-[11px] text-gray-400">Menu bertanda ✓ sudah punya resep</p>
                    </div>
                </div>

                <input type="text" id="product-search" placeholder="Cari menu..."
                    class="w-full px-4 py-2.5 mb-3 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white placeholder-gray-400 border border-gray-200 transition-all">

                <div class="flex flex-col gap-1.5 max-h-[420px] overflow-y-auto pe-1" id="product-list">
                    @foreach($products as $product)
                        @php $isSelected = $selectedProduct && $selectedProduct->uuid === $product->uuid; @endphp
                        <a href="{{ route('product-recipe.index', ['product' => $product->uuid]) }}"
                           class="product-pick flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-sm transition-all {{ $isSelected ? 'bg-brand text-white font-bold shadow-sm shadow-brand/20' : 'text-gray-600 hover:bg-brand-soft hover:text-brand font-semibold' }}"
                           data-name="{{ strtolower($product->name) }}">
                            <span class="truncate">{{ $product->name }}</span>
                            @if(in_array($product->uuid, $productsWithRecipe))
                                <i class="fas fa-check text-[11px] shrink-0 {{ $isSelected ? 'text-white' : 'text-emerald-500' }}"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ============ RESEP PRODUK TERPILIH ============ --}}
            <div class="lg:col-span-2 flex flex-col gap-5">
                @if($selectedProduct)
                    @php
                        $recipeCost = $selectedProduct->recipeCost();
                        $manualCost = (int) ($selectedProduct->cost_price ?? 0);
                        $difference = $recipeCost !== null ? $recipeCost - $manualCost : null;
                    @endphp

                    {{-- Costing --}}
                    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-lg">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-base font-bold text-gray-800 truncate">{{ $selectedProduct->name }}</p>
                                <p class="text-xs text-gray-400">Harga jual Rp {{ number_format($selectedProduct->price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wide">HPP Manual</p>
                                <p class="text-lg font-extrabold text-gray-800 mt-1">Rp {{ number_format($manualCost, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Dipakai laporan laba</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-brand-soft border border-brand/10">
                                <p class="text-[11px] text-brand font-semibold uppercase tracking-wide">HPP Menurut Resep</p>
                                <p class="text-lg font-extrabold text-brand mt-1">
                                    {{ $recipeCost !== null ? 'Rp ' . number_format($recipeCost, 0, ',', '.') : '—' }}
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $recipeCost !== null ? 'Hitungan dari bahan' : 'Belum ada resep' }}</p>
                            </div>
                            <div class="p-4 rounded-2xl {{ $difference === null ? 'bg-gray-50 border-gray-100' : ($difference > 0 ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100') }} border">
                                <p class="text-[11px] font-semibold uppercase tracking-wide {{ $difference === null ? 'text-gray-400' : ($difference > 0 ? 'text-red-600' : 'text-emerald-600') }}">Selisih</p>
                                <p class="text-lg font-extrabold mt-1 {{ $difference === null ? 'text-gray-800' : ($difference > 0 ? 'text-red-600' : 'text-emerald-600') }}">
                                    @if($difference === null)
                                        —
                                    @else
                                        {{ $difference > 0 ? '+' : '' }}Rp {{ number_format(abs($difference), 0, ',', '.') }}
                                    @endif
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    @if($difference === null)
                                        Perlu resep
                                    @elseif($difference > 0)
                                        Resep lebih mahal dari HPP manual
                                    @elseif($difference < 0)
                                        Resep lebih murah dari HPP manual
                                    @else
                                        Sudah sama
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($difference !== null && $difference != 0)
                            <p class="text-[11px] text-gray-500 mt-3">
                                <i class="fas fa-circle-info text-gray-400"></i>
                                Angka resep hanya sebagai pembanding — laporan laba tetap memakai HPP manual di halaman produk.
                            </p>
                        @endif
                    </div>

                    {{-- Daftar bahan --}}
                    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-lg">
                                <i class="fas fa-carrot"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-800">Bahan per 1 Porsi</p>
                                <p class="text-xs text-gray-400">Jumlah ini yang otomatis dipotong dari stok saat menu terjual.</p>
                            </div>
                        </div>

                        {{-- Tambah bahan --}}
                        <form method="POST" action="{{ route('product-recipe.store') }}" class="flex flex-wrap items-end gap-3 mb-5 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $selectedProduct->uuid }}">
                            <div class="flex-1 min-w-[180px]">
                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Bahan Baku</label>
                                <select name="supply_item_id" required
                                    class="w-full px-4 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-white border border-gray-200 transition-all">
                                    @foreach($supplyItems as $supply)
                                        <option value="{{ $supply->uuid }}">{{ $supply->name }} ({{ $supply->unit }}) — Rp {{ number_format($supply->unit_price, 0, ',', '.') }}/{{ $supply->unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32">
                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Jumlah</label>
                                <input type="number" step="0.001" min="0.001" name="qty" value="1" required
                                    class="w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-white border border-gray-200 transition-all">
                            </div>
                            <button type="submit" class="bg-brand hover:bg-brand-strong text-white text-sm font-semibold py-2 px-4 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </form>

                        @if($selectedProduct->recipes->isEmpty())
                            <div class="flex flex-col items-center justify-center text-center py-8">
                                <span class="w-12 h-12 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3"><i class="fas fa-bowl-food"></i></span>
                                <p class="text-sm font-bold text-gray-700">Belum ada bahan</p>
                                <p class="text-xs text-gray-400 mt-1">Menu ini tidak akan memotong stok saat terjual.</p>
                            </div>
                        @else
                            <div class="flex flex-col gap-2">
                                @foreach($selectedProduct->recipes as $recipe)
                                    <div class="flex flex-wrap items-center gap-3 p-3 rounded-2xl border border-gray-200">
                                        <div class="flex-1 min-w-[140px]">
                                            <p class="text-sm font-semibold text-gray-800">{{ $recipe->supplyItem->name ?? '—' }}</p>
                                            <p class="text-[11px] text-gray-400">
                                                Rp {{ number_format($recipe->supplyItem->unit_price ?? 0, 0, ',', '.') }} / {{ $recipe->supplyItem->unit ?? '' }}
                                                &middot; sisa stok {{ rtrim(rtrim(number_format($recipe->supplyItem->stock ?? 0, 2, ',', '.'), '0'), ',') }}
                                            </p>
                                        </div>

                                        <form method="POST" action="{{ route('product-recipe.update', $recipe->uuid) }}" class="flex items-end gap-2">
                                            @csrf
                                            @method('PUT')
                                            <div class="w-28">
                                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Jumlah</label>
                                                <input type="number" step="0.001" min="0.001" name="qty"
                                                       value="{{ rtrim(rtrim(number_format($recipe->qty, 3, '.', ''), '0'), '.') }}"
                                                    class="w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                                            </div>
                                            <span class="text-[11px] text-gray-400 pb-2.5">{{ $recipe->supplyItem->unit ?? '' }}</span>
                                            <button type="submit" class="bg-brand hover:bg-brand-strong text-white text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>

                                        <div class="text-right min-w-[90px]">
                                            <p class="text-[11px] text-gray-400">Biaya</p>
                                            <p class="text-sm font-bold text-gray-800">Rp {{ number_format($recipe->lineCost(), 0, ',', '.') }}</p>
                                        </div>

                                        <form method="POST" action="{{ route('product-recipe.destroy', $recipe->uuid) }}" onsubmit="return confirm('Hapus bahan ini dari resep?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var search = document.getElementById('product-search');
        if (!search) return;

        search.addEventListener('input', function() {
            var keyword = search.value.toLowerCase().trim();
            document.querySelectorAll('#product-list .product-pick').forEach(function(el) {
                el.style.display = el.dataset.name.indexOf(keyword) === -1 ? 'none' : '';
            });
        });
    });
</script>
@endsection
