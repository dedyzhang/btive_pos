@extends('layout.index')
@section('title','Master Barang')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">MASTER BARANG</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-boxes-stacked text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ $supplyItems->count() }} Barang</span>
        </div>
    </div>
@endsection

@section('container')
<div class="container-place w-full p-4 sm:p-6 flex gap-5 flex-wrap flex-col bg-gray-50/50">

    @if(session('success_supply'))
        <div class="flex items-center p-4 text-sm text-emerald-700 rounded-2xl bg-emerald-50 border border-emerald-100" role="alert">
            <i class="me-2 fas fa-check"></i>
            <p><span class="font-medium me-1">Sukses!</span> {{ session('success_supply') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-start p-4 text-sm text-red-700 rounded-2xl bg-red-50 border border-red-100" role="alert">
            <i class="me-2 mt-0.5 fas fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ TAMBAH BARANG ============ --}}
    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-brand-soft text-brand flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <p class="text-base font-bold text-gray-800">Tambah Barang</p>
                <p class="text-xs text-gray-400">Bahan baku / perlengkapan yang bisa diajukan untuk dibeli.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('supply-item.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label for="name" class="text-sm font-medium text-gray-700 mb-1 block">Nama Barang</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Misal: Beras, Telur, Gas LPG" required
                    class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white placeholder-gray-400 border border-gray-200 transition-all">
            </div>
            <div>
                <label for="unit" class="text-sm font-medium text-gray-700 mb-1 block" title="Satuan untuk stok, par level, dan resep — pakai satuan terkecil yang benar-benar dipakai di dapur">Satuan Pakai</label>
                <input type="text" name="unit" id="unit" value="{{ old('unit', 'pcs') }}" placeholder="keping / kg / liter" required list="unit-suggestions"
                    class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white placeholder-gray-400 border border-gray-200 transition-all">
                <datalist id="unit-suggestions">
                    <option value="kg"><option value="gram"><option value="liter"><option value="ml">
                    <option value="pcs"><option value="pack"><option value="dus"><option value="botol"><option value="ikat"><option value="keping">
                </datalist>
            </div>
            <div>
                <label for="unit_price" class="text-sm font-medium text-gray-700 mb-1 block">Harga / Satuan Pakai</label>
                <input type="number" min="0" name="unit_price" id="unit_price" value="{{ old('unit_price', 0) }}" placeholder="15000"
                    class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white placeholder-gray-400 border border-gray-200 transition-all">
            </div>

            <div class="md:col-span-4">
                <button type="button" id="btn-toggle-new-conversion" class="text-xs font-semibold text-indigo-500 hover:text-indigo-700 cursor-pointer bg-transparent border-none flex items-center gap-1.5">
                    <i class="fas fa-repeat"></i> Barang ini dibeli dalam satuan berbeda? (opsional)
                    <i class="fas fa-chevron-down text-[10px] transition-transform" id="new-conversion-chevron"></i>
                </button>
                <div id="new-conversion-box" class="{{ old('purchase_unit') || old('purchase_conversion') ? '' : 'hidden' }} mt-3 bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4">
                    <p class="text-[11px] text-indigo-500 mb-3">Misal: beli per <b>batang</b>, dipakai di resep per <b>keping</b>.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <div>
                            <label for="purchase_unit" class="text-[11px] font-medium text-indigo-900 mb-1 block">Satuan Beli</label>
                            <input type="text" name="purchase_unit" id="purchase_unit" value="{{ old('purchase_unit') }}" placeholder="Misal: batang, karung, dus"
                                class="w-full px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 bg-white placeholder-gray-400 border border-indigo-200 transition-all">
                        </div>
                        <div>
                            <label for="purchase_conversion" class="text-[11px] font-medium text-indigo-900 mb-1 block">1 Satuan Beli = Berapa Satuan Pakai</label>
                            <input type="number" min="0" step="0.0001" name="purchase_conversion" id="purchase_conversion" value="{{ old('purchase_conversion') }}" placeholder="Misal: 16"
                                class="w-full px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 bg-white placeholder-gray-400 border border-indigo-200 transition-all">
                        </div>
                        <p class="text-xs text-indigo-600 font-medium pb-2.5" id="new-item-conversion-preview"></p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-4">
                <button type="submit" class="w-full sm:w-auto bg-brand hover:bg-brand-strong text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-plus"></i> Tambah Barang
                </button>
            </div>
        </form>
    </div>

    {{-- ============ DAFTAR BARANG ============ --}}
    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-warehouse"></i>
            </div>
            <div>
                <p class="text-base font-bold text-gray-800">Daftar Barang &amp; Riwayat Pembelian</p>
                <p class="text-xs text-gray-400">Terakhir dibeli &amp; perkiraan berapa hari sekali barang habis.</p>
            </div>
        </div>

        @if($supplyItems->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <span class="w-14 h-14 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 text-xl"><i class="fas fa-box-open"></i></span>
                <p class="text-sm font-bold text-gray-700">Belum ada barang</p>
                <p class="text-xs text-gray-400 mt-1">Tambahkan barang lewat form di atas.</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach($supplyItems as $item)
                    @php
                        $stat = $purchaseStats[$item->uuid] ?? null;
                        $daysSince = $stat['days_since_last'] ?? null;
                        $avgDays = $stat['average_days_between'] ?? null;
                        $hasAdvanced = (float) $item->par_level > 0 || $item->hasPurchaseConversion();
                    @endphp
                    <div class="rounded-2xl border {{ $item->is_active ? 'border-gray-200' : 'border-gray-200 bg-gray-50 opacity-70' }} overflow-hidden">
                        <form method="POST" action="{{ route('supply-item.update', $item->uuid) }}"
                              class="supply-item-form flex flex-wrap items-end gap-3 p-4">
                            @csrf
                            @method('PUT')

                            <div class="flex-1 min-w-[160px]">
                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Nama</label>
                                <input type="text" name="name" value="{{ $item->name }}" required
                                    class="w-full px-4 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                                @if($stat)
                                    <p class="text-[11px] text-gray-400 mt-1">
                                        Terakhir dibeli {{ $daysSince === 0 ? 'hari ini' : $daysSince . ' hari lalu' }}
                                        @if($avgDays)
                                            &middot; biasanya habis tiap ~{{ rtrim(rtrim(number_format($avgDays, 1, ',', '.'), '0'), ',') }} hari
                                        @endif
                                    </p>
                                @else
                                    <p class="text-[11px] text-gray-400 mt-1">Belum pernah dibeli</p>
                                @endif
                            </div>
                            <div class="w-24">
                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Satuan Pakai</label>
                                <input type="text" name="unit" value="{{ $item->unit }}" required
                                    class="w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                            </div>
                            <div class="w-28">
                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Harga/Satuan</label>
                                <input type="number" min="0" name="unit_price" value="{{ (int) $item->unit_price }}"
                                    class="w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                            </div>
                            <div class="w-24">
                                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Stok</label>
                                <input type="number" step="0.01" name="stock" value="{{ rtrim(rtrim(number_format($item->stock, 2, '.', ''), '0'), '.') }}"
                                    class="stock-input w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand {{ $item->isBelowPar() ? 'bg-red-50 border-red-200 text-red-700' : 'bg-gray-50 focus:bg-white border-gray-200' }} border transition-all">
                                @if($item->hasPurchaseConversion())
                                    <button type="button" class="btn-toggle-stock-calc text-[9px] text-indigo-500 font-semibold mt-1 hover:text-indigo-700 cursor-pointer bg-transparent border-none block">
                                        <i class="fas fa-calculator"></i> dari {{ $item->purchase_unit }}
                                    </button>
                                    <div class="stock-calc hidden mt-1 flex items-center gap-1" data-conversion="{{ (float) $item->purchase_conversion }}">
                                        <input type="number" min="0" step="0.0001" placeholder="0" class="calc-purchase-qty w-14 px-1.5 py-1 text-[11px] text-right rounded-lg border border-indigo-200 focus:outline-none focus:border-indigo-400 bg-white">
                                        <span class="text-[9px] text-gray-400 whitespace-nowrap">{{ $item->purchase_unit }}</span>
                                    </div>
                                @endif
                            </div>
                            <button type="submit" class="bg-brand hover:bg-brand-strong text-white text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer shrink-0">
                                <i class="fas fa-save"></i> Simpan
                            </button>

                            {{-- Advanced settings, tucked away since most items never need them --}}
                            <div class="w-full {{ $hasAdvanced ? '' : 'hidden' }} advanced-settings grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 mt-1 border-t border-dashed border-gray-100">
                                <div>
                                    <label class="text-[11px] font-medium text-gray-400 mb-1 block" title="Stok minimum sebelum perlu belanja lagi">Par Level</label>
                                    <input type="number" step="0.01" min="0" name="par_level" value="{{ rtrim(rtrim(number_format($item->par_level, 2, '.', ''), '0'), '.') }}"
                                        class="w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                                </div>
                                <div>
                                    <label class="text-[11px] font-medium text-gray-400 mb-1 block" title="Isi kalau beli dalam satuan berbeda, misal beli per batang">Satuan Beli</label>
                                    <input type="text" name="purchase_unit" value="{{ $item->purchase_unit }}" placeholder="batang"
                                        class="supply-purchase-unit w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                                </div>
                                <div class="col-span-2 sm:col-span-2">
                                    <label class="text-[11px] font-medium text-gray-400 mb-1 block" title="1 satuan beli = berapa satuan pakai">Isi / Konversi</label>
                                    <input type="number" step="0.0001" min="0" name="purchase_conversion" value="{{ $item->purchase_conversion !== null ? rtrim(rtrim(number_format((float) $item->purchase_conversion, 4, '.', ''), '0'), '.') : '' }}" placeholder="16"
                                        class="supply-purchase-conversion w-full px-3 py-2 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                                    <p class="supply-conversion-preview text-[10px] text-indigo-500 font-medium mt-1"></p>
                                </div>
                            </div>
                        </form>

                        <div class="flex items-center gap-2 px-4 pb-3.5 -mt-1">
                            <button type="button" class="btn-toggle-advanced text-[11px] font-semibold text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none flex items-center gap-1">
                                <i class="fas fa-sliders"></i> Detail Lanjutan
                                <i class="fas fa-chevron-down text-[9px] transition-transform {{ $hasAdvanced ? 'rotate-180' : '' }} advanced-chevron"></i>
                            </button>
                            <span class="text-gray-200">|</span>
                            <form method="POST" action="{{ route('supply-item.toggle', $item->uuid) }}">
                                @csrf
                                <button type="submit" class="text-[11px] font-semibold py-1 px-2.5 rounded-lg transition-all cursor-pointer {{ $item->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                    {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('supply-item.destroy', $item->uuid) }}" onsubmit="return confirm('Hapus barang ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[11px] font-semibold py-1 px-2.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                            @if(!$item->is_active)
                                <span class="text-[11px] text-gray-400">Tidak muncul di daftar pengajuan</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function trimNumber(value) {
            return String(parseFloat(value.toFixed(4)));
        }

        function updatePreview(purchaseUnitInput, conversionInput, previewEl, usageUnit) {
            const purchaseUnit = purchaseUnitInput.value.trim();
            const conversion = parseFloat(conversionInput.value);

            if (purchaseUnit && conversion > 0) {
                previewEl.textContent = `1 ${purchaseUnit} = ${trimNumber(conversion)} ${usageUnit || 'satuan pakai'}`;
            } else {
                previewEl.textContent = '';
            }
        }

        // Collapsible "beli dalam satuan berbeda?" box on the Tambah Barang form
        const toggleNewConversionBtn = document.getElementById('btn-toggle-new-conversion');
        const newConversionBox = document.getElementById('new-conversion-box');
        if (toggleNewConversionBtn && newConversionBox) {
            toggleNewConversionBtn.addEventListener('click', function() {
                newConversionBox.classList.toggle('hidden');
                document.getElementById('new-conversion-chevron').classList.toggle('rotate-180');
            });
        }

        // Collapsible "Detail Lanjutan" per item row (Par Level / Satuan Beli / Konversi)
        document.querySelectorAll('.btn-toggle-advanced').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var panel = btn.closest('.rounded-2xl').querySelector('.advanced-settings');
                panel.classList.toggle('hidden');
                btn.querySelector('.advanced-chevron').classList.toggle('rotate-180');
            });
        });

        // Tambah Barang form
        const newPurchaseUnit = document.getElementById('purchase_unit');
        const newConversion = document.getElementById('purchase_conversion');
        const newUnit = document.getElementById('unit');
        const newPreview = document.getElementById('new-item-conversion-preview');
        if (newPurchaseUnit && newConversion && newPreview) {
            const refresh = () => updatePreview(newPurchaseUnit, newConversion, newPreview, newUnit.value.trim());
            [newPurchaseUnit, newConversion, newUnit].forEach(el => el.addEventListener('input', refresh));
        }

        // Per-item edit rows
        document.querySelectorAll('.supply-item-form').forEach(function(form) {
            const purchaseUnitInput = form.querySelector('.supply-purchase-unit');
            const conversionInput = form.querySelector('.supply-purchase-conversion');
            const unitInput = form.querySelector('input[name="unit"]');
            const previewEl = form.querySelector('.supply-conversion-preview');
            if (!purchaseUnitInput || !conversionInput || !previewEl) return;

            const refresh = () => updatePreview(purchaseUnitInput, conversionInput, previewEl, unitInput.value.trim());
            [purchaseUnitInput, conversionInput, unitInput].forEach(el => el.addEventListener('input', refresh));
            refresh();
        });

        // "Stok dari batang" helper: typing a purchase-unit qty multiplies it into the actual
        // Stok field, so nobody has to do the ×16 math by hand when correcting stock.
        document.querySelectorAll('.btn-toggle-stock-calc').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const calc = btn.nextElementSibling;
                calc.classList.toggle('hidden');
                if (!calc.classList.contains('hidden')) {
                    calc.querySelector('.calc-purchase-qty').focus();
                }
            });
        });

        document.querySelectorAll('.stock-calc').forEach(function(calc) {
            const conversion = parseFloat(calc.dataset.conversion) || 1;
            const stockInput = calc.closest('.w-24').querySelector('.stock-input');
            const purchaseQtyInput = calc.querySelector('.calc-purchase-qty');

            purchaseQtyInput.addEventListener('input', function() {
                const purchaseQty = parseFloat(purchaseQtyInput.value);
                if (!purchaseQty && purchaseQty !== 0) return;
                stockInput.value = parseFloat((purchaseQty * conversion).toFixed(4));
            });
        });
    });
</script>
@endsection
