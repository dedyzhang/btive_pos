@extends('layout.index')
@section('title','Pengajuan Belanja')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">PENGAJUAN BELANJA</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
<div class="container-place w-full p-4 sm:p-6 flex gap-5 flex-wrap flex-col bg-gray-50/50">

    @if(session('success_request'))
        <div class="flex items-center p-4 text-sm text-emerald-700 rounded-2xl bg-emerald-50 border border-emerald-100" role="alert">
            <i class="me-2 fas fa-check"></i>
            <p><span class="font-medium me-1">Sukses!</span> {{ session('success_request') }}</p>
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

    {{-- ============ FORM PENGAJUAN ============ --}}
    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-brand-soft text-brand flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-cart-plus"></i>
            </div>
            <div>
                <p class="text-base font-bold text-gray-800">Daftar Belanja Besok</p>
                <p class="text-xs text-gray-400">Centang barang yang perlu dibeli, lalu isi jumlahnya.</p>
            </div>
        </div>

        @if($supplyItems->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <span class="w-14 h-14 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 text-xl"><i class="fas fa-box-open"></i></span>
                <p class="text-sm font-bold text-gray-700">Belum ada data barang</p>
                <p class="text-xs text-gray-400 mt-1">Admin perlu menambahkan master barang terlebih dahulu.</p>
                @can('admin')
                    <a href="{{ route('supply-item.index') }}" class="mt-4 bg-brand hover:bg-brand-strong text-white text-sm font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all">
                        <i class="fas fa-plus"></i> Kelola Master Barang
                    </a>
                @endcan
            </div>
        @else
            <form method="POST" action="{{ route('purchase-request.store') }}" id="form-purchase-request">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="request_date" class="text-sm font-medium text-gray-700 mb-1 block">Tanggal Belanja</label>
                        <input type="date" name="request_date" id="request_date" value="{{ old('request_date', now()->addDay()->toDateString()) }}"
                            class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                    </div>
                    <div>
                        <label for="note" class="text-sm font-medium text-gray-700 mb-1 block">Catatan (opsional)</label>
                        <input type="text" name="note" id="note" value="{{ old('note') }}" placeholder="Misal: beli di pasar pagi"
                            class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white placeholder-gray-400 border border-gray-200 transition-all">
                    </div>
                </div>

                @php
                    $dueCount = 0;
                    foreach ($supplyItems as $checkItem) {
                        $checkStat = $purchaseStats[$checkItem->uuid] ?? null;
                        $checkAvg = $checkStat['average_days_between'] ?? null;
                        $checkDays = $checkStat['days_since_last'] ?? null;
                        if ($checkItem->isBelowPar() || ($checkAvg !== null && $checkDays !== null && $checkDays >= $checkAvg)) {
                            $dueCount++;
                        }
                    }
                @endphp

                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-bold text-gray-700">Pilih Barang</p>
                    <span class="text-xs text-gray-400"><span id="selected-count">0</span> barang dipilih</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-2" id="due-items-grid">
                    @foreach($supplyItems as $item)
                        @php
                            $stat = $purchaseStats[$item->uuid] ?? null;
                            $daysSince = $stat['days_since_last'] ?? null;
                            $avgDays = $stat['average_days_between'] ?? null;
                            // Below par is the primary restock signal; the purchase interval is
                            // a fallback for items whose par level hasn't been set yet.
                            $isBelowPar = $item->isBelowPar();
                            $isDue = $isBelowPar
                                || ($avgDays !== null && $daysSince !== null && $daysSince >= $avgDays);

                            $hasConversion = $item->hasPurchaseConversion();
                            $conversion = $hasConversion ? (float) $item->purchase_conversion : 1;
                            $inputUnit = $hasConversion ? $item->purchase_unit : $item->unit;
                            // Suggest the amount needed to get back up to par, so the usual
                            // case needs no typing at all. In purchase-unit mode, round up to
                            // whole purchase units — you can't buy 0.3 batang.
                            $deficit = (float) $item->par_level - (float) $item->stock;
                            $suggestedQty = $isBelowPar && $deficit > 0
                                ? ($hasConversion ? ceil($deficit / $conversion) : ceil($deficit * 100) / 100)
                                : 1;

                            $historyTitle = $stat
                                ? 'Dibeli ' . ($daysSince === 0 ? 'hari ini' : $daysSince . ' hari lalu') . ($avgDays ? ' · biasanya tiap ~' . rtrim(rtrim(number_format($avgDays, 1, ',', '.'), '0'), ',') . ' hari' : '')
                                : 'Belum pernah dibeli';
                        @endphp
                        @if($isDue)
                            <label class="supply-row flex items-center gap-3 p-3 rounded-2xl border border-amber-300 bg-amber-50 hover:border-brand/40 transition-all cursor-pointer"
                                   data-uuid="{{ $item->uuid }}" data-name="{{ $item->name }}" data-unit="{{ $item->unit }}" data-conversion="{{ $conversion }}">
                                <input type="checkbox" class="supply-check w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand shrink-0" {{ $isBelowPar ? 'checked' : '' }}>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">
                                        {{ $item->name }}
                                        <i class="fas fa-triangle-exclamation text-amber-500 text-[11px] ms-0.5" title="{{ $historyTitle }}"></i>
                                    </p>
                                    <p class="text-[11px] {{ $isBelowPar ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        Sisa {{ rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',') }} {{ $item->unit }}
                                        @if((float) $item->par_level > 0)
                                            <span class="{{ $isBelowPar ? '' : 'text-gray-400' }}">/ par {{ rtrim(rtrim(number_format($item->par_level, 2, ',', '.'), '0'), ',') }}</span>
                                        @endif
                                    </p>
                                    @if($hasConversion)
                                        <p class="supply-conversion-hint text-[10px] text-indigo-500 font-medium"></p>
                                    @endif
                                </div>
                                <input type="number" step="{{ $hasConversion ? '1' : '0.01' }}" min="{{ $hasConversion ? '1' : '0.01' }}" value="{{ $hasConversion ? (int) $suggestedQty : rtrim(rtrim(number_format($suggestedQty, 2, '.', ''), '0'), '.') }}" disabled
                                       class="supply-qty w-20 px-2 py-1.5 text-sm text-center rounded-xl border border-gray-200 bg-white disabled:bg-gray-100 disabled:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-soft shrink-0">
                                <span class="text-[11px] text-gray-400 w-10 shrink-0">{{ $inputUnit }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>

                @if($dueCount === 0)
                    <p class="text-xs text-gray-400 mb-2">Tidak ada barang yang perlu dibeli sekarang — semua stok masih aman. 👍</p>
                @endif

                <button type="button" id="btn-toggle-other-items" class="text-xs font-semibold text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none flex items-center gap-1.5 mb-3">
                    <i class="fas fa-box-open"></i> Barang lainnya (belum perlu dibeli)
                    <i class="fas fa-chevron-down text-[10px] transition-transform" id="other-items-chevron"></i>
                </button>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-5 {{ $dueCount === 0 ? '' : 'hidden' }}" id="other-items-grid">
                    @foreach($supplyItems as $item)
                        @php
                            $stat = $purchaseStats[$item->uuid] ?? null;
                            $daysSince = $stat['days_since_last'] ?? null;
                            $avgDays = $stat['average_days_between'] ?? null;
                            $isBelowPar = $item->isBelowPar();
                            $isDue = $isBelowPar
                                || ($avgDays !== null && $daysSince !== null && $daysSince >= $avgDays);

                            $hasConversion = $item->hasPurchaseConversion();
                            $conversion = $hasConversion ? (float) $item->purchase_conversion : 1;
                            $inputUnit = $hasConversion ? $item->purchase_unit : $item->unit;

                            $historyTitle = $stat
                                ? 'Dibeli ' . ($daysSince === 0 ? 'hari ini' : $daysSince . ' hari lalu') . ($avgDays ? ' · biasanya tiap ~' . rtrim(rtrim(number_format($avgDays, 1, ',', '.'), '0'), ',') . ' hari' : '')
                                : 'Belum pernah dibeli';
                        @endphp
                        @if(!$isDue)
                            <label class="supply-row flex items-center gap-3 p-3 rounded-2xl border border-gray-200 bg-gray-50 hover:border-brand/40 transition-all cursor-pointer"
                                   data-uuid="{{ $item->uuid }}" data-name="{{ $item->name }}" data-unit="{{ $item->unit }}" data-conversion="{{ $conversion }}">
                                <input type="checkbox" class="supply-check w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" title="{{ $historyTitle }}">{{ $item->name }}</p>
                                    <p class="text-[11px] text-gray-500">
                                        Sisa {{ rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',') }} {{ $item->unit }}
                                        @if((float) $item->par_level > 0)
                                            <span class="text-gray-400">/ par {{ rtrim(rtrim(number_format($item->par_level, 2, ',', '.'), '0'), ',') }}</span>
                                        @endif
                                    </p>
                                    @if($hasConversion)
                                        <p class="supply-conversion-hint text-[10px] text-indigo-500 font-medium"></p>
                                    @endif
                                </div>
                                <input type="number" step="{{ $hasConversion ? '1' : '0.01' }}" min="{{ $hasConversion ? '1' : '0.01' }}" value="1" disabled
                                       class="supply-qty w-20 px-2 py-1.5 text-sm text-center rounded-xl border border-gray-200 bg-white disabled:bg-gray-100 disabled:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-soft shrink-0">
                                <span class="text-[11px] text-gray-400 w-10 shrink-0">{{ $inputUnit }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>

                {{-- Hidden inputs for the selected items get injected here on submit --}}
                <div id="selected-items-container"></div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-brand hover:bg-brand-strong text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all active:scale-[0.98] cursor-pointer">
                        <i class="fas fa-paper-plane"></i> Simpan Pengajuan
                    </button>
                    <button type="button" id="btn-share-wa-draft" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-emerald-500/20 transition-all active:scale-[0.98] cursor-pointer">
                        <i class="fab fa-whatsapp"></i> Share ke WhatsApp
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- ============ RIWAYAT PENGAJUAN ============ --}}
    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <p class="text-base font-bold text-gray-800">Riwayat Pengajuan</p>
                <p class="text-xs text-gray-400">30 pengajuan terakhir</p>
            </div>
        </div>

        @if($requests->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <span class="w-14 h-14 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 text-xl"><i class="fas fa-inbox"></i></span>
                <p class="text-sm font-bold text-gray-700">Belum ada pengajuan</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach($requests as $req)
                    @php
                        $statusMap = [
                            'pending'   => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                            'purchased' => ['label' => 'Sudah Dibeli', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-gray-100 text-gray-500 border-gray-200'],
                        ];
                        $badge = $statusMap[$req->status] ?? $statusMap['pending'];
                    @endphp
                    <div class="border border-gray-200 rounded-2xl p-4">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-bold text-gray-800">{{ $req->request_date->format('d M Y') }}</p>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    Diajukan oleh {{ $req->user->name ?? '-' }} &middot; {{ $req->created_at->format('d M Y H:i') }}
                                    @if($req->status === 'purchased' && $req->purchaser)
                                        &middot; dibeli oleh {{ $req->purchaser->name }}
                                    @endif
                                </p>
                                @if($req->note)
                                    <p class="text-[11px] text-gray-500 mt-1"><i class="far fa-sticky-note"></i> {{ $req->note }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                <button type="button"
                                        class="btn-share-wa-existing bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer"
                                        data-text="{{ $req->request_date->format('d M Y') }}|{{ $req->items->map(fn($i) => $i->item_name . ' - ' . rtrim(rtrim(number_format($i->qty, 2, ',', '.'), '0'), ',') . ' ' . $i->unit)->implode('||') }}|{{ $req->note }}">
                                    <i class="fab fa-whatsapp"></i> Share
                                </button>
                                @can('admin')
                                    @if($req->status === 'pending')
                                        <button type="button"
                                                class="btn-open-purchased-modal bg-brand hover:bg-brand-strong text-white text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer"
                                                data-uuid="{{ $req->uuid }}"
                                                data-items="{{ $req->items->map(fn($i) => ['uuid' => $i->uuid, 'name' => $i->item_name, 'unit' => $i->unit, 'qty' => (float) $i->qty])->toJson() }}">
                                            <i class="fas fa-check"></i> Sudah Dibeli
                                        </button>
                                        <form method="POST" action="{{ route('purchase-request.cancel', $req->uuid) }}" onsubmit="return confirm('Batalkan pengajuan ini?');">
                                            @csrf
                                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('purchase-request.destroy', $req->uuid) }}" onsubmit="return confirm('Hapus pengajuan ini? Stok yang sudah ditambahkan tidak ikut dikurangi.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                        <ul class="mt-3 pt-3 border-t border-dashed border-gray-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-1">
                            @foreach($req->items as $item)
                                <li class="text-xs text-gray-600 flex justify-between gap-2">
                                    <span class="truncate">{{ $item->item_name }}</span>
                                    <span class="font-bold text-gray-800 shrink-0">{{ rtrim(rtrim(number_format($item->qty, 2, ',', '.'), '0'), ',') }} {{ $item->unit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ MODAL: TANDAI SUDAH DIBELI ============ --}}
    <div id="modal-mark-purchased" tabindex="-1" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-white shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-800"><i class="fas fa-check-circle text-brand me-1.5"></i>Tandai Sudah Dibeli</h3>
                    <p class="text-[10px] text-gray-400 font-medium mt-0.5">Sesuaikan jumlah kalau beda dari yang diajukan.</p>
                </div>
                <button type="button" class="close-purchased-modal text-gray-400 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex justify-center items-center cursor-pointer border-none">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <form method="POST" id="form-mark-purchased" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <div id="purchased-modal-items" class="p-5 overflow-y-auto flex-grow flex flex-col gap-3"></div>
                <div class="p-4 border-t border-gray-100 bg-white shrink-0">
                    <button type="submit" class="w-full bg-brand hover:bg-brand-strong text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all active:scale-[0.98] cursor-pointer">
                        <i class="fas fa-check"></i> Konfirmasi Sudah Dibeli
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var restaurantName = @json($resName ?? 'Restoran');

        // ---- Enable/disable qty per checkbox ----
        function refreshSelectedCount() {
            var count = document.querySelectorAll('.supply-check:checked').length;
            var el = document.getElementById('selected-count');
            if (el) el.textContent = count;
        }

        // Items that aren't due yet are tucked away by default so the checklist only shows
        // what actually needs attention today.
        var toggleOtherBtn = document.getElementById('btn-toggle-other-items');
        var otherGrid = document.getElementById('other-items-grid');
        if (toggleOtherBtn && otherGrid) {
            toggleOtherBtn.addEventListener('click', function() {
                otherGrid.classList.toggle('hidden');
                document.getElementById('other-items-chevron').classList.toggle('rotate-180');
            });
        }

        function trimNumber(value) {
            return String(parseFloat(value.toFixed(2)));
        }

        document.querySelectorAll('.supply-row').forEach(function(row) {
            var check = row.querySelector('.supply-check');
            var qty = row.querySelector('.supply-qty');
            var hint = row.querySelector('.supply-conversion-hint');
            var conversion = parseFloat(row.dataset.conversion) || 1;

            function syncRow(focusQty) {
                qty.disabled = !check.checked;
                row.classList.toggle('border-brand', check.checked);
                row.classList.toggle('bg-brand-soft', check.checked);
                if (check.checked && focusQty) qty.focus();
            }

            check.addEventListener('change', function() {
                syncRow(true);
                refreshSelectedCount();
            });

            // Below-par rows render pre-checked, which fires no change event — apply their
            // state on load so the qty box isn't left disabled.
            syncRow(false);

            // Typing in the qty box shouldn't toggle the surrounding label's checkbox.
            qty.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); });

            // Live "= X keping" hint while typing a purchase-unit qty, so the base-unit
            // amount that'll actually be added to stock is never a surprise.
            if (hint) {
                function refreshHint() {
                    var typed = parseFloat(qty.value) || 0;
                    hint.textContent = typed > 0 ? '= ' + trimNumber(typed * conversion) + ' ' + row.dataset.unit : '';
                }
                qty.addEventListener('input', refreshHint);
                refreshHint();
            }
        });

        refreshSelectedCount();

        // ---- Collect the checked rows ----
        // qty/unit are always in the base (satuan pakai) unit — what actually gets stored
        // and later added to stock. displayQty/displayUnit keep whatever the person typed
        // (purchase unit, if the item has one) so the WhatsApp list stays in market terms.
        function collectSelected() {
            var selected = [];
            document.querySelectorAll('.supply-row').forEach(function(row) {
                var check = row.querySelector('.supply-check');
                if (!check.checked) return;
                var qtyInput = row.querySelector('.supply-qty');
                var typedQty = parseFloat(qtyInput.value);
                if (!typedQty || typedQty <= 0) return;
                var conversion = parseFloat(row.dataset.conversion) || 1;
                selected.push({
                    uuid: row.dataset.uuid,
                    name: row.dataset.name,
                    unit: row.dataset.unit,
                    qty: typedQty * conversion,
                    displayQty: typedQty,
                    displayUnit: qtyInput.nextElementSibling ? qtyInput.nextElementSibling.textContent.trim() : row.dataset.unit
                });
            });
            return selected;
        }

        // ---- Submit: turn the checked rows into real form inputs ----
        var form = document.getElementById('form-purchase-request');
        if (form) {
            form.addEventListener('submit', function(e) {
                var selected = collectSelected();
                if (selected.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu barang dan isi jumlahnya.');
                    return;
                }

                var container = document.getElementById('selected-items-container');
                container.innerHTML = '';
                selected.forEach(function(item, i) {
                    container.insertAdjacentHTML('beforeend',
                        '<input type="hidden" name="items[' + i + '][supply_item_id]" value="' + item.uuid + '">' +
                        '<input type="hidden" name="items[' + i + '][qty]" value="' + item.qty + '">'
                    );
                });
            });
        }

        // ---- Build the WhatsApp message ----
        function buildMessage(dateLabel, lines, note) {
            var text = '*DAFTAR BELANJA - ' + restaurantName + '*\n';
            text += 'Tanggal: ' + dateLabel + '\n';
            text += '------------------------\n';
            lines.forEach(function(line, i) {
                text += (i + 1) + '. ' + line + '\n';
            });
            if (note) {
                text += '------------------------\n';
                text += 'Catatan: ' + note + '\n';
            }
            return text;
        }

        function openWhatsApp(text) {
            // No phone number in the URL on purpose: WhatsApp then shows its own picker so the
            // same link works for a group chat or a personal chat.
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        }

        function formatQty(qty) {
            return (qty % 1 === 0 ? qty.toFixed(0) : String(qty).replace('.', ','));
        }

        // Share the list currently being filled in (before saving)
        var btnDraft = document.getElementById('btn-share-wa-draft');
        if (btnDraft) {
            btnDraft.addEventListener('click', function() {
                var selected = collectSelected();
                if (selected.length === 0) {
                    alert('Pilih minimal satu barang dan isi jumlahnya.');
                    return;
                }
                var dateInput = document.getElementById('request_date');
                var dateLabel = dateInput && dateInput.value ? dateInput.value : '-';
                var noteInput = document.getElementById('note');
                var lines = selected.map(function(item) {
                    return item.name + ' - ' + formatQty(item.displayQty) + ' ' + item.displayUnit;
                });
                openWhatsApp(buildMessage(dateLabel, lines, noteInput ? noteInput.value : ''));
            });
        }

        // Share an already-saved request from the history list
        document.querySelectorAll('.btn-share-wa-existing').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var parts = (btn.dataset.text || '').split('|');
                var dateLabel = parts[0] || '-';
                var lines = (parts[1] || '').split('||').filter(function(l) { return l.trim() !== ''; });
                var note = parts[2] || '';
                if (lines.length === 0) {
                    alert('Pengajuan ini tidak punya item.');
                    return;
                }
                openWhatsApp(buildMessage(dateLabel, lines, note));
            });
        });

        // ---- Modal: tandai sudah dibeli, dengan qty yang bisa disesuaikan ----
        var purchasedUrlTemplate = @json(route('purchase-request.purchased', ':uuid'));
        var modalPurchased = document.getElementById('modal-mark-purchased');
        var formPurchased = document.getElementById('form-mark-purchased');
        var purchasedItemsContainer = document.getElementById('purchased-modal-items');

        function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, function(c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function openPurchasedModal(uuid, items) {
            formPurchased.action = purchasedUrlTemplate.replace(':uuid', uuid);
            purchasedItemsContainer.innerHTML = '';
            items.forEach(function(item, i) {
                purchasedItemsContainer.insertAdjacentHTML('beforeend',
                    '<div class="flex items-center gap-3">' +
                        '<div class="flex-1 min-w-0">' +
                            '<p class="text-sm font-semibold text-gray-800 truncate">' + escapeHtml(item.name) + '</p>' +
                            '<p class="text-[11px] text-gray-400">Diajukan: ' + item.qty + ' ' + escapeHtml(item.unit) + '</p>' +
                        '</div>' +
                        '<input type="hidden" name="items[' + i + '][uuid]" value="' + item.uuid + '">' +
                        '<input type="number" step="0.01" min="0.01" name="items[' + i + '][qty]" value="' + item.qty + '" required ' +
                            'class="w-24 px-3 py-2 text-sm text-right rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">' +
                        '<span class="text-[11px] text-gray-400 w-10 shrink-0">' + escapeHtml(item.unit) + '</span>' +
                    '</div>'
                );
            });
            modalPurchased.classList.remove('hidden');
        }

        document.querySelectorAll('.btn-open-purchased-modal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var items = JSON.parse(btn.dataset.items || '[]');
                openPurchasedModal(btn.dataset.uuid, items);
            });
        });

        document.querySelectorAll('.close-purchased-modal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                modalPurchased.classList.add('hidden');
            });
        });
        modalPurchased.addEventListener('click', function(e) {
            if (e.target === modalPurchased) modalPurchased.classList.add('hidden');
        });
    });
</script>
@endsection
