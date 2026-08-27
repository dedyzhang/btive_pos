@extends('layout.index')
@section('title','Stock Opname')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">STOCK OPNAME</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-clipboard-check text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
<div class="container-place w-full p-4 sm:p-6 flex gap-5 flex-wrap flex-col bg-gray-50/50">

    @if(session('success_opname'))
        <div class="flex items-center p-4 text-sm text-emerald-700 rounded-2xl bg-emerald-50 border border-emerald-100" role="alert">
            <i class="me-2 fas fa-check"></i>
            <p><span class="font-medium me-1">Sukses!</span> {{ session('success_opname') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-start p-4 text-sm text-red-700 rounded-2xl bg-red-50 border border-red-100" role="alert">
            <i class="me-2 mt-0.5 fas fa-circle-exclamation"></i>
            <div>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        </div>
    @endif

    {{-- ============ FORM OPNAME ============ --}}
    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-brand-soft text-brand flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div>
                <p class="text-base font-bold text-gray-800">Hitung Fisik</p>
                <p class="text-xs text-gray-400">Isi jumlah sebenarnya di gudang. Kosongkan yang tidak dihitung kali ini.</p>
            </div>
        </div>

        @if($supplyItems->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <span class="w-14 h-14 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 text-xl"><i class="fas fa-box-open"></i></span>
                <p class="text-sm font-bold text-gray-700">Belum ada barang</p>
                <a href="{{ route('supply-item.index') }}" class="mt-4 bg-brand hover:bg-brand-strong text-white text-sm font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all">
                    <i class="fas fa-warehouse"></i> Ke Master Barang
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('stock-opname.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="opname_date" class="text-sm font-medium text-gray-700 mb-1 block">Tanggal Opname</label>
                        <input type="date" name="opname_date" id="opname_date" value="{{ old('opname_date', now()->toDateString()) }}"
                            class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                    </div>
                    <div>
                        <label for="note" class="text-sm font-medium text-gray-700 mb-1 block">Catatan (opsional)</label>
                        <input type="text" name="note" id="note" value="{{ old('note') }}" placeholder="Misal: opname mingguan"
                            class="w-full px-5 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white placeholder-gray-400 border border-gray-200 transition-all">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-sm">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-gray-400 border-b border-gray-100">
                                <th class="text-left font-semibold py-2">Barang</th>
                                <th class="text-right font-semibold py-2 w-32">Stok Sistem</th>
                                <th class="text-right font-semibold py-2 w-36">Hitungan Fisik</th>
                                <th class="text-right font-semibold py-2 w-32">Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplyItems as $index => $item)
                                <tr class="border-b border-gray-50 opname-row" data-price="{{ (int) $item->unit_price }}" data-name="{{ $item->name }}" data-unit="{{ $item->unit }}">
                                    <td class="py-2.5">
                                        <input type="hidden" name="items[{{ $index }}][supply_item_id]" value="{{ $item->uuid }}">
                                        <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $item->unit }} &middot; Rp {{ number_format($item->unit_price, 0, ',', '.') }}/{{ $item->unit }}</p>
                                    </td>
                                    <td class="py-2.5 text-right">
                                        <span class="system-stock font-semibold {{ $item->stock < 0 ? 'text-red-600' : 'text-gray-700' }}"
                                              data-value="{{ (float) $item->stock }}">
                                            {{ rtrim(rtrim(number_format($item->stock, 2, ',', '.'), '0'), ',') }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right">
                                        <input type="number" step="0.01" name="items[{{ $index }}][actual_stock]" placeholder="—"
                                            class="actual-stock w-28 px-3 py-2 text-sm text-right rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-soft focus:border-brand bg-gray-50 focus:bg-white border border-gray-200 transition-all">
                                        @if($item->hasPurchaseConversion())
                                            <button type="button" class="btn-toggle-calc text-[10px] text-indigo-500 font-semibold mt-1 hover:text-indigo-700 cursor-pointer bg-transparent border-none block ms-auto">
                                                <i class="fas fa-calculator"></i> Hitung dari {{ $item->purchase_unit }}
                                            </button>
                                            <div class="opname-calc hidden mt-1.5 flex items-center gap-1 justify-end flex-wrap"
                                                 data-conversion="{{ (float) $item->purchase_conversion }}">
                                                <input type="number" min="0" step="1" placeholder="0" class="calc-purchase-qty w-12 px-1.5 py-1 text-xs text-right rounded-lg border border-indigo-200 focus:outline-none focus:border-indigo-400 bg-white">
                                                <span class="text-[10px] text-gray-400">{{ $item->purchase_unit }} +</span>
                                                <input type="number" min="0" step="0.01" placeholder="0" class="calc-remainder-qty w-12 px-1.5 py-1 text-xs text-right rounded-lg border border-indigo-200 focus:outline-none focus:border-indigo-400 bg-white">
                                                <span class="text-[10px] text-gray-400">{{ $item->unit }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-right">
                                        <span class="variance-cell text-gray-300 font-semibold">—</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 mt-5 pt-4 border-t border-gray-100">
                    <div class="text-sm">
                        <span class="text-gray-400">Estimasi nilai selisih:</span>
                        <span id="total-variance-value" class="font-extrabold text-gray-800 ms-1">Rp 0</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" id="btn-share-wa-draft" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-emerald-500/20 transition-all active:scale-[0.98] cursor-pointer">
                            <i class="fab fa-whatsapp"></i> Share ke WhatsApp
                        </button>
                        <button type="submit" class="bg-brand hover:bg-brand-strong text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm shadow-brand/20 transition-all active:scale-[0.98] cursor-pointer">
                            <i class="fas fa-save"></i> Simpan Opname &amp; Sesuaikan Stok
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    {{-- ============ RIWAYAT OPNAME ============ --}}
    <div class="p-5 sm:p-7 bg-white rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 text-lg">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <p class="text-base font-bold text-gray-800">Riwayat &amp; Selisih</p>
                <p class="text-xs text-gray-400">Selisih minus = susut/waste. Ini data untuk menekan kebocoran.</p>
            </div>
        </div>

        @if($opnames->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <span class="w-14 h-14 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 text-xl"><i class="fas fa-inbox"></i></span>
                <p class="text-sm font-bold text-gray-700">Belum ada opname</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach($opnames as $opname)
                    @php $totalValue = $opname->varianceValue(); @endphp
                    <div class="border border-gray-200 rounded-2xl p-4">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $opname->opname_date->format('d M Y') }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    Oleh {{ $opname->user->name ?? '-' }} &middot; {{ $opname->created_at->format('d M Y H:i') }} &middot; {{ $opname->items->count() }} barang
                                </p>
                                @if($opname->note)
                                    <p class="text-[11px] text-gray-500 mt-1"><i class="far fa-sticky-note"></i> {{ $opname->note }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="text-right">
                                    <p class="text-[11px] text-gray-400">Nilai Selisih</p>
                                    <p class="text-sm font-extrabold {{ $totalValue < 0 ? 'text-red-600' : ($totalValue > 0 ? 'text-emerald-600' : 'text-gray-700') }}">
                                        {{ $totalValue < 0 ? '-' : ($totalValue > 0 ? '+' : '') }}Rp {{ number_format(abs($totalValue), 0, ',', '.') }}
                                    </p>
                                </div>
                                <button type="button"
                                        class="btn-share-wa-existing bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer"
                                        data-date="{{ $opname->opname_date->format('d M Y') }}"
                                        data-note="{{ $opname->note }}"
                                        data-total="{{ $totalValue }}"
                                        data-text="{{ $opname->items->map(fn($i) => $i->item_name . '|' . rtrim(rtrim(number_format($i->system_stock, 2, ',', '.'), '0'), ',') . '|' . rtrim(rtrim(number_format($i->actual_stock, 2, ',', '.'), '0'), ',') . '|' . ($i->variance > 0 ? '+' : '') . rtrim(rtrim(number_format($i->variance, 2, ',', '.'), '0'), ',') . '|' . $i->unit)->implode('||') }}">
                                    <i class="fab fa-whatsapp"></i> Share
                                </button>
                                @can('admin')
                                    <form method="POST" action="{{ route('stock-opname.destroy', $opname->uuid) }}" onsubmit="return confirm('Hapus riwayat opname ini? Stok saat ini tidak akan berubah.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold py-2 px-3 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                        <button type="button" class="btn-toggle-opname-detail text-[11px] font-semibold text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none flex items-center gap-1 mt-2">
                            Lihat Rincian per Barang
                            <i class="fas fa-chevron-down text-[9px] transition-transform"></i>
                        </button>

                        <div class="opname-detail hidden mt-3 pt-3 border-t border-dashed border-gray-200 overflow-x-auto">
                            <table class="w-full min-w-[460px] text-xs">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-wide text-gray-400">
                                        <th class="text-left font-semibold pb-1">Barang</th>
                                        <th class="text-right font-semibold pb-1">Sistem</th>
                                        <th class="text-right font-semibold pb-1">Fisik</th>
                                        <th class="text-right font-semibold pb-1">Selisih</th>
                                        <th class="text-right font-semibold pb-1">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($opname->items as $item)
                                        @php $itemValue = $item->varianceValue(); @endphp
                                        <tr class="text-gray-600">
                                            <td class="py-1 truncate">{{ $item->item_name }}</td>
                                            <td class="py-1 text-right">{{ rtrim(rtrim(number_format($item->system_stock, 2, ',', '.'), '0'), ',') }}</td>
                                            <td class="py-1 text-right">{{ rtrim(rtrim(number_format($item->actual_stock, 2, ',', '.'), '0'), ',') }}</td>
                                            <td class="py-1 text-right font-bold {{ $item->variance < 0 ? 'text-red-600' : ($item->variance > 0 ? 'text-emerald-600' : 'text-gray-400') }}">
                                                {{ $item->variance > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($item->variance, 2, ',', '.'), '0'), ',') }} {{ $item->unit }}
                                            </td>
                                            <td class="py-1 text-right {{ $itemValue < 0 ? 'text-red-600' : ($itemValue > 0 ? 'text-emerald-600' : 'text-gray-400') }}">
                                                {{ $itemValue < 0 ? '-' : ($itemValue > 0 ? '+' : '') }}Rp {{ number_format(abs($itemValue), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var restaurantName = @json($resName ?? 'Restoran');

        function formatRupiah(value) {
            return 'Rp ' + Math.abs(Math.round(value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function trimNumber(value) {
            return String(parseFloat(value.toFixed(2))).replace('.', ',');
        }

        // Live variance preview so the person counting sees the gap immediately,
        // before committing the adjustment.
        function recalculate() {
            var totalValue = 0;

            document.querySelectorAll('.opname-row').forEach(function(row) {
                var input = row.querySelector('.actual-stock');
                var cell = row.querySelector('.variance-cell');
                var systemStock = parseFloat(row.querySelector('.system-stock').dataset.value) || 0;
                var price = parseFloat(row.dataset.price) || 0;

                if (input.value === '') {
                    cell.textContent = '—';
                    cell.className = 'variance-cell text-gray-300 font-semibold';
                    return;
                }

                var variance = (parseFloat(input.value) || 0) - systemStock;
                totalValue += variance * price;

                cell.textContent = (variance > 0 ? '+' : '') + trimNumber(variance);
                cell.className = 'variance-cell font-bold ' +
                    (variance < 0 ? 'text-red-600' : (variance > 0 ? 'text-emerald-600' : 'text-gray-400'));
            });

            var totalEl = document.getElementById('total-variance-value');
            totalEl.textContent = (totalValue < 0 ? '-' : (totalValue > 0 ? '+' : '')) + formatRupiah(totalValue);
            totalEl.className = 'font-extrabold ms-1 ' +
                (totalValue < 0 ? 'text-red-600' : (totalValue > 0 ? 'text-emerald-600' : 'text-gray-800'));
        }

        // History entries only show their date/total by default — the per-item breakdown
        // table is the part that gets long fast, so it's tucked behind a toggle.
        document.querySelectorAll('.btn-toggle-opname-detail').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var detail = btn.nextElementSibling;
                detail.classList.toggle('hidden');
                btn.querySelector('i').classList.toggle('rotate-180');
            });
        });

        document.querySelectorAll('.actual-stock').forEach(function(input) {
            input.addEventListener('input', recalculate);
        });

        // Optional "hitung dari batang" calculator: batang qty (+ sisa keping) => total keping,
        // written straight into the actual Hitungan Fisik field so precise counts (including
        // odd leftovers) don't require mental math.
        document.querySelectorAll('.btn-toggle-calc').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var calc = btn.nextElementSibling;
                calc.classList.toggle('hidden');
                if (!calc.classList.contains('hidden')) {
                    calc.querySelector('.calc-purchase-qty').focus();
                }
            });
        });

        document.querySelectorAll('.opname-calc').forEach(function(calc) {
            var conversion = parseFloat(calc.dataset.conversion) || 1;
            var actualInput = calc.closest('td').querySelector('.actual-stock');
            var purchaseQtyInput = calc.querySelector('.calc-purchase-qty');
            var remainderInput = calc.querySelector('.calc-remainder-qty');

            function applyCalc() {
                var purchaseQty = parseFloat(purchaseQtyInput.value) || 0;
                var remainder = parseFloat(remainderInput.value) || 0;
                if (purchaseQty === 0 && remainder === 0) return;

                actualInput.value = parseFloat((purchaseQty * conversion + remainder).toFixed(2));
                actualInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            purchaseQtyInput.addEventListener('input', applyCalc);
            remainderInput.addEventListener('input', applyCalc);
        });

        // ---- Build & open the WhatsApp message ----
        function buildMessage(title, dateLabel, lines, totalValue, note) {
            var text = '*' + title + ' - ' + restaurantName + '*\n';
            text += 'Tanggal: ' + dateLabel + '\n';
            text += '------------------------\n';
            lines.forEach(function(line, i) {
                text += (i + 1) + '. ' + line + '\n';
            });
            text += '------------------------\n';
            text += 'Total nilai selisih: ' + (totalValue < 0 ? '-' : (totalValue > 0 ? '+' : '')) + formatRupiah(totalValue) + '\n';
            if (note) {
                text += 'Catatan: ' + note + '\n';
            }
            return text;
        }

        function openWhatsApp(text) {
            // No phone number in the URL on purpose: WhatsApp then shows its own picker so the
            // same link works for a group chat or a personal chat.
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        }

        // Share what's been counted so far, before saving.
        var btnDraft = document.getElementById('btn-share-wa-draft');
        if (btnDraft) {
            btnDraft.addEventListener('click', function() {
                var lines = [];
                var totalValue = 0;

                document.querySelectorAll('.opname-row').forEach(function(row) {
                    var input = row.querySelector('.actual-stock');
                    if (input.value === '') return;

                    var name = row.dataset.name;
                    var unit = row.dataset.unit;
                    var systemStock = parseFloat(row.querySelector('.system-stock').dataset.value) || 0;
                    var actualStock = parseFloat(input.value) || 0;
                    var price = parseFloat(row.dataset.price) || 0;
                    var variance = actualStock - systemStock;
                    totalValue += variance * price;

                    lines.push(name + ': sistem ' + trimNumber(systemStock) + ' ' + unit + ', fisik ' + trimNumber(actualStock) + ' ' + unit +
                        ' (selisih ' + (variance > 0 ? '+' : '') + trimNumber(variance) + ' ' + unit + ')');
                });

                if (lines.length === 0) {
                    alert('Isi minimal satu hitungan fisik dulu.');
                    return;
                }

                var dateInput = document.getElementById('opname_date');
                var dateLabel = dateInput && dateInput.value ? dateInput.value : '-';
                var noteInput = document.getElementById('note');
                openWhatsApp(buildMessage('HASIL STOCK OPNAME', dateLabel, lines, totalValue, noteInput ? noteInput.value : ''));
            });
        }

        // Share an already-saved opname from the history list.
        document.querySelectorAll('.btn-share-wa-existing').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var parts = (btn.dataset.text || '').split('||').filter(function(l) { return l.trim() !== ''; });
                var lines = parts.map(function(part) {
                    var f = part.split('|');
                    return f[0] + ': sistem ' + f[1] + ' ' + f[4] + ', fisik ' + f[2] + ' ' + f[4] + ' (selisih ' + f[3] + ' ' + f[4] + ')';
                });

                if (lines.length === 0) {
                    alert('Opname ini tidak punya item.');
                    return;
                }

                var totalValue = parseFloat(btn.dataset.total) || 0;
                openWhatsApp(buildMessage('HASIL STOCK OPNAME', btn.dataset.date || '-', lines, totalValue, btn.dataset.note || ''));
            });
        });
    });
</script>
@endsection
