@extends('layout.index')

@section('title', 'Antrean Dapur')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <div class="flex items-center gap-3">
            <h1 class="text-lg md:text-3xl font-bold text-gray-800">ANTREAN DAPUR</h1>
            <span class="hidden md:inline-flex bg-brand-soft text-brand border border-brand-medium/30 px-3 py-1 rounded-full text-xs font-semibold select-none items-center gap-1.5">
                <i class="fas fa-fire-burner animate-pulse"></i> Monitoring Live
            </span>
        </div>
        <div class="date-place inline-flex px-3 py-2 bg-white rounded-full shadow-sm items-center gap-3 border border-gray-100">
            <div class="menu-icon rounded-full h-10 w-10 flex items-center justify-center bg-brand-soft text-brand"><i class="fas fa-clock text-base"></i></div>
            <span id="live-clock" class="text-gray-700 font-bold text-sm md:text-base">00:00:00</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="container-place w-full p-6">
        
        <!-- Search and Filter Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 bg-white p-4 rounded-2xl border border-gray-100 shadow-3xs">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <i class="fas fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="search-queue" placeholder="Cari meja atau pelanggan..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-default text-xs font-semibold focus:outline-none focus:border-brand-subtle focus:bg-brand-softer bg-neutral-primary-soft transition-all">
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto">
                <button type="button" class="btn-filter bg-brand text-white border border-brand font-bold py-2 px-4 rounded-xl text-xs cursor-pointer transition-all active-filter" data-type="all">
                    Semua Tipe
                </button>
                <button type="button" class="btn-filter bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-2 px-4 rounded-xl text-xs cursor-pointer transition-all" data-type="dine_in">
                    Dine In
                </button>
                <button type="button" class="btn-filter bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-2 px-4 rounded-xl text-xs cursor-pointer transition-all" data-type="take_away">
                    Take Away
                </button>
            </div>
        </div>

        <!-- Tab Headers -->
        <div class="border-b border-gray-200 mb-6 flex gap-4">
            <button class="tab-link border-b-2 border-brand text-brand py-3 px-4 font-bold text-sm flex items-center gap-2 focus:outline-none cursor-pointer" data-tab="tab-cooking">
                <i class="fas fa-fire-burner"></i> Sedang Dimasak 
                <span id="count-cooking" class="bg-brand text-white px-2 py-0.5 rounded-full text-[10px] font-bold">0</span>
            </button>
            <button class="tab-link border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-3 px-4 font-bold text-sm flex items-center gap-2 focus:outline-none cursor-pointer" data-tab="tab-completed">
                <i class="fas fa-circle-check"></i> Selesai Baru 
                <span id="count-completed" class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-[10px] font-bold">0</span>
            </button>
        </div>

        <!-- Tab Contents -->
        <div id="tab-cooking" class="tab-content">
            <div id="cooking-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Cooking cards loaded via JS -->
            </div>
            <div id="cooking-empty" class="hidden flex-col items-center justify-center py-24 text-center">
                <span class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-4 border border-emerald-100"><i class="fas fa-thumbs-up text-3xl animate-bounce"></i></span>
                <h3 class="font-bold text-gray-700 text-base">Semua Pesanan Selesai!</h3>
                <p class="text-xs text-gray-400 mt-1.5 max-w-xs">Tidak ada antrean pesanan aktif yang perlu dimasak saat ini.</p>
            </div>
        </div>

        <div id="tab-completed" class="tab-content hidden">
            <div id="completed-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Completed cards loaded via JS -->
            </div>
            <div id="completed-empty" class="hidden flex-col items-center justify-center py-24 text-center">
                <span class="w-20 h-20 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4"><i class="fas fa-history text-3xl"></i></span>
                <h3 class="font-bold text-gray-700 text-base">Belum Ada Riwayat Selesai</h3>
                <p class="text-xs text-gray-400 mt-1.5 max-w-xs">Belum ada pesanan yang diselesaikan memasak pada sesi ini.</p>
            </div>
        </div>
    </div>

    <!-- Web Audio API chime implementation -->
    <script type="module">
        let knownOrderIds = [];
        let firstLoad = true;

        $(document).ready(function() {
            // Clock Logic
            setInterval(updateClock, 1000);
            updateClock();

            // Tabs Logic
            $('.tab-link').on('click', function() {
                const target = $(this).data('tab');
                $('.tab-link').removeClass('border-brand text-brand').addClass('border-transparent text-gray-500 hover:text-gray-700');
                $(this).addClass('border-brand text-brand').removeClass('border-transparent text-gray-500 hover:text-gray-700');
                
                $('.tab-content').addClass('hidden');
                $('#' + target).removeClass('hidden');
            });

            // Filter Logic
            $('.btn-filter').on('click', function() {
                $('.btn-filter').removeClass('bg-brand text-white border-brand active-filter').addClass('bg-white text-gray-600 border-gray-200');
                $(this).addClass('bg-brand text-white border-brand active-filter').removeClass('bg-white text-gray-600 border-gray-200');
                filterCards();
            });

            // Search Logic
            $('#search-queue').on('input', function() {
                filterCards();
            });

            // Initial and Polling Loads
            loadQueue();
            setInterval(loadQueue, 3000);

            // Serve/Complete Button
            $(document).on('click', '.btn-complete-cooking', function() {
                const uuid = $(this).closest('.order-card').data('uuid');
                const $card = $(this).closest('.order-card');
                updateStatus(uuid, 'ready', $card);
            });

            // Revert/Undo Button
            $(document).on('click', '.btn-revert-cooking', function() {
                const uuid = $(this).closest('.order-card').data('uuid');
                const $card = $(this).closest('.order-card');
                updateStatus(uuid, 'cooking', $card);
            });

            // Toggle Item Completion Checkbox
            $(document).on('change', '.chk-item-done', function() {
                const itemUuid = $(this).data('uuid');
                const isChecked = $(this).is(':checked');
                const $labelSpan = $(this).siblings('span');
                const $noteDiv = $(this).closest('.py-2').find('.mt-1');
                
                // Fast visual update for premium feedback
                if (isChecked) {
                    $labelSpan.addClass('line-through text-gray-400 font-semibold').removeClass('font-extrabold text-gray-800');
                    $noteDiv.addClass('line-through text-gray-300').removeClass('text-amber-800');
                } else {
                    $labelSpan.removeClass('line-through text-gray-400 font-semibold').addClass('font-extrabold text-gray-800');
                    $noteDiv.removeClass('line-through text-gray-300').addClass('text-amber-800');
                }

                // Send AJAX update
                let url = "{{ route('kitchen.item.status.update', ':id') }}";
                url = url.replace(':id', itemUuid);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        is_kitchen_done: isChecked ? 1 : 0
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success === true) {
                            loadQueue();
                        }
                    },
                    error: function(xhr) {
                        // Revert visual state if failed
                        $(this).prop('checked', !isChecked);
                        if (!isChecked) {
                            $labelSpan.addClass('line-through text-gray-400 font-semibold').removeClass('font-extrabold text-gray-800');
                            $noteDiv.addClass('line-through text-gray-300').removeClass('text-amber-800');
                        } else {
                            $labelSpan.removeClass('line-through text-gray-400 font-semibold').addClass('font-extrabold text-gray-800');
                            $noteDiv.removeClass('line-through text-gray-300').addClass('text-amber-800');
                        }
                        const err = xhr.responseJSON;
                        oAlert("red", "Gagal", err ? err.message : "Gagal memperbarui status item.");
                    }
                });
            });
        });

        // Live clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            $('#live-clock').text(`${hours}:${minutes}:${seconds}`);
        }

        // Web Audio synthesized chime for notifications
        function playChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                
                // Tone 1: Ding (G5)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.type = 'triangle';
                osc1.frequency.setValueAtTime(783.99, ctx.currentTime);
                gain1.gain.setValueAtTime(0, ctx.currentTime);
                gain1.gain.linearRampToValueAtTime(0.2, ctx.currentTime + 0.05);
                gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc1.start(ctx.currentTime);
                osc1.stop(ctx.currentTime + 0.35);

                // Tone 2: Dong (E5) after 0.18s
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.type = 'triangle';
                osc2.frequency.setValueAtTime(659.25, ctx.currentTime + 0.18);
                gain2.gain.setValueAtTime(0, ctx.currentTime);
                gain2.gain.setValueAtTime(0, ctx.currentTime + 0.18);
                gain2.gain.linearRampToValueAtTime(0.2, ctx.currentTime + 0.23);
                gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc2.start(ctx.currentTime);
                osc2.stop(ctx.currentTime + 0.6);
            } catch (e) {
                console.warn("Audio blocked or not supported", e);
            }
        }

        // Load Queue from Server
        function loadQueue() {
            $.ajax({
                url: "{{ route('kitchen.live-updates') }}",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    if (data.success === true) {
                        renderCooking(data.cooking);
                        renderCompleted(data.completed);
                        
                        // Check for new orders to play chime
                        let newOrderAdded = false;
                        data.cooking.forEach(tx => {
                            if (!knownOrderIds.includes(tx.uuid)) {
                                knownOrderIds.push(tx.uuid);
                                if (!firstLoad) {
                                    newOrderAdded = true;
                                }
                            }
                        });

                        if (newOrderAdded) {
                            playChime();
                        }
                        firstLoad = false;
                        
                        // Re-apply filters
                        filterCards();
                    }
                },
                error: function(err) {
                    console.error("Gagal memuat antrean dapur", err);
                }
            });
        }

        // Render Cooking Tab
        function renderCooking(list) {
            $('#count-cooking').text(list.length);
            
            if (list.length === 0) {
                $('#cooking-grid').addClass('hidden');
                $('#cooking-empty').removeClass('hidden').addClass('flex');
                return;
            }
            
            $('#cooking-empty').addClass('hidden').removeClass('flex');
            $('#cooking-grid').removeClass('hidden');

            const now = new Date();

            list.forEach(tx => {
                const createdAt = new Date(tx.created_at);
                const diffMins = Math.floor((now - createdAt) / 60000);
                
                // Color theme by age
                let colorClass = 'bg-emerald-50/50 border-emerald-200 text-emerald-800';
                let timeBadge = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                let pulseText = '';
                
                if (diffMins >= 10 && diffMins < 20) {
                    colorClass = 'bg-amber-50/30 border-amber-200 text-amber-900';
                    timeBadge = 'bg-amber-100 text-amber-800 border border-amber-200';
                } else if (diffMins >= 20) {
                    colorClass = 'bg-rose-50/30 border-rose-200 text-rose-900';
                    timeBadge = 'bg-rose-100 text-rose-800 border border-rose-200 animate-pulse font-extrabold';
                    pulseText = 'pulse-card';
                }

                // Table or Type badge
                let typeBadge = '';
                if (tx.order_type === 'take_away') {
                    typeBadge = `<span class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-lg text-[10px] font-bold"><i class="fas fa-bag-shopping"></i> TAKE AWAY</span>`;
                } else {
                    typeBadge = `<span class="bg-brand-soft text-brand-light border border-brand-medium/30 px-2 py-0.5 rounded-lg text-[10px] font-bold"><i class="fas fa-utensils"></i> DINE IN</span>`;
                }

                // Table info
                const tableName = tx.table ? tx.table.name : '-';
                
                // Items html & check completion state
                let itemsHtml = '';
                let listAllItemsDone = true;
                tx.order_item.forEach(item => {
                    const isDone = item.is_kitchen_done === true;
                    if (!isDone) listAllItemsDone = false;

                    const itemDoneClass = isDone ? 'line-through text-gray-400 font-semibold' : 'font-extrabold text-gray-800';
                    const noteDoneClass = isDone ? 'line-through text-gray-300' : 'text-amber-800';
                    const checkedAttr = isDone ? 'checked' : '';

                    itemsHtml += `
                        <div class="py-2 flex flex-col border-b border-gray-100 last:border-b-0">
                            <div class="flex items-start justify-between gap-3">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none flex-1 min-w-0">
                                    <input type="checkbox" class="chk-item-done w-4.5 h-4.5 text-emerald-600 bg-white border-gray-300 rounded focus:ring-emerald-500 mt-0.5" data-uuid="${item.uuid}" ${checkedAttr}>
                                    <span class="text-sm truncate ${itemDoneClass}">${item.product_name}</span>
                                </label>
                                <span class="font-black text-brand text-sm shrink-0 pl-3">x${item.qty}</span>
                            </div>
                            ${item.note ? `
                                <div class="mt-1 pl-7 bg-amber-50/50 ${noteDoneClass} text-[11px] px-2 py-1 rounded-lg border border-amber-100/60 flex items-center gap-1.5">
                                    <i class="fas fa-sticky-note text-[10px] text-amber-500 shrink-0"></i>
                                    <span class="font-semibold italic">Catatan: ${item.note}</span>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });

                // Check card glowing style for fully completed items
                let readyBadge = '';
                let readyCardClass = '';
                if (tx.order_item.length > 0 && listAllItemsDone) {
                    readyBadge = `<span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 rounded-lg text-[10px] font-extrabold animate-bounce"><i class="fas fa-circle-check"></i> SIAP SAJI</span>`;
                    readyCardClass = 'border-emerald-500 shadow-md shadow-emerald-50/15 bg-emerald-50/5';
                }

                const timeStr = diffMins === 0 ? 'Baru saja' : `${diffMins}m yang lalu`;

                const cardHtml = `
                    <div class="order-card bg-white rounded-2xl border border-gray-100 shadow-3xs p-5 hover:shadow-xs transition-all duration-300 flex flex-col justify-between ${pulseText} ${readyCardClass}" data-uuid="${tx.uuid}" data-type="${tx.order_type || 'dine_in'}" data-search="${tableName.toLowerCase()} ${tx.customer_name ? tx.customer_name.toLowerCase() : ''}">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3 gap-2">
                                <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                                    ${typeBadge}
                                    ${readyBadge}
                                    <span class="font-extrabold text-heading text-xs tracking-tight truncate">${tableName}</span>
                                </div>
                                <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${timeBadge}">${timeStr}</span>
                            </div>

                            <!-- Customer Profile -->
                            <div class="mb-4">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Pelanggan</span>
                                <span class="font-extrabold text-gray-800 text-sm">${tx.customer_name || 'Guest'}</span>
                            </div>

                            <!-- Items List -->
                            <div class="mb-4 space-y-1">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Daftar Menu</span>
                                ${itemsHtml}
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="pt-3 mt-auto border-t border-gray-100">
                            <button type="button" class="btn-complete-cooking w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs cursor-pointer transition-all flex items-center justify-center gap-1.5 shadow-2xs hover:shadow-xs">
                                <i class="fas fa-circle-check text-sm"></i> Selesai Masak
                            </button>
                        </div>
                    </div>
                `;

                // Update or append card
                const $existingCard = $('#cooking-grid').find(`.order-card[data-uuid="${tx.uuid}"]`);
                if ($existingCard.length > 0) {
                    $existingCard.html($(cardHtml).html());
                    $existingCard.attr('class', $(cardHtml).attr('class'));
                } else {
                    $('#cooking-grid').append(cardHtml);
                }
            });

            // Remove cards no longer in data
            const currentUuids = list.map(tx => tx.uuid);
            $('#cooking-grid').find('.order-card').each(function() {
                const uuid = $(this).data('uuid');
                if (!currentUuids.includes(uuid)) {
                    $(this).fadeOut(300, function() { $(this).remove(); });
                }
            });
        }

        // Render Completed Tab
        function renderCompleted(list) {
            $('#count-completed').text(list.length);
            
            if (list.length === 0) {
                $('#completed-grid').addClass('hidden');
                $('#completed-empty').removeClass('hidden').addClass('flex');
                return;
            }
            
            $('#completed-empty').addClass('hidden').removeClass('flex');
            $('#completed-grid').removeClass('hidden');

            list.forEach(tx => {
                const completedAt = new Date(tx.updated_at);
                const timeStr = completedAt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                // Table or Type badge
                let typeBadge = '';
                if (tx.order_type === 'take_away') {
                    typeBadge = `<span class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-lg text-[10px] font-bold"><i class="fas fa-bag-shopping"></i> TAKE AWAY</span>`;
                } else {
                    typeBadge = `<span class="bg-brand-soft text-brand-light border border-brand-medium/30 px-2 py-0.5 rounded-lg text-[10px] font-bold"><i class="fas fa-utensils"></i> DINE IN</span>`;
                }

                // Table info
                const tableName = tx.table ? tx.table.name : '-';
                
                // Items html
                let itemsHtml = '';
                tx.order_item.forEach(item => {
                    itemsHtml += `
                        <div class="py-2 flex items-start justify-between border-b border-gray-100 last:border-b-0">
                            <span class="font-semibold text-gray-500 text-sm line-through">${item.product_name}</span>
                            <span class="font-extrabold text-gray-400 text-sm">x${item.qty}</span>
                        </div>
                    `;
                });

                const cardHtml = `
                    <div class="order-card bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 p-5 flex flex-col justify-between opacity-80 hover:opacity-100 transition-opacity" data-uuid="${tx.uuid}" data-type="${tx.order_type || 'dine_in'}" data-search="${tableName.toLowerCase()} ${tx.customer_name ? tx.customer_name.toLowerCase() : ''}">
                        <div>
                            <!-- Card Header -->
                            <div class="flex items-center justify-between border-b border-gray-200/50 pb-3 mb-3 gap-2">
                                <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                                    ${typeBadge}
                                    <span class="font-extrabold text-gray-500 text-xs truncate">${tableName}</span>
                                </div>
                                <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-200 text-gray-500 border border-gray-300">Pukul ${timeStr}</span>
                            </div>

                            <!-- Customer Profile -->
                            <div class="mb-4">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Pelanggan</span>
                                <span class="font-extrabold text-gray-600 text-sm">${tx.customer_name || 'Guest'}</span>
                            </div>

                            <!-- Items List -->
                            <div class="mb-4 space-y-1">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Daftar Menu</span>
                                ${itemsHtml}
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="pt-3 mt-auto border-t border-gray-200/50">
                            <button type="button" class="btn-revert-cooking w-full bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-2.5 px-4 rounded-xl text-xs cursor-pointer transition-all flex items-center justify-center gap-1.5 shadow-3xs">
                                <i class="fas fa-undo text-xs"></i> Kembalikan ke Antrean
                            </button>
                        </div>
                    </div>
                `;

                // Update or append card
                const $existingCard = $('#completed-grid').find(`.order-card[data-uuid="${tx.uuid}"]`);
                if ($existingCard.length > 0) {
                    $existingCard.html($(cardHtml).html());
                    $existingCard.attr('class', $(cardHtml).attr('class'));
                } else {
                    $('#completed-grid').append(cardHtml);
                }
            });

            // Remove cards no longer in data
            const currentUuids = list.map(tx => tx.uuid);
            $('#completed-grid').find('.order-card').each(function() {
                const uuid = $(this).data('uuid');
                if (!currentUuids.includes(uuid)) {
                    $(this).fadeOut(300, function() { $(this).remove(); });
                }
            });
        }

        // Apply Search and Type Filters
        function filterCards() {
            const query = $('#search-queue').val().toLowerCase().trim();
            const typeFilter = $('.active-filter').data('type');

            $('.order-card').each(function() {
                const textMatch = $(this).data('search').indexOf(query) !== -1;
                
                let typeMatch = true;
                if (typeFilter === 'dine_in') {
                    typeMatch = $(this).data('type') === 'dine_in';
                } else if (typeFilter === 'take_away') {
                    typeMatch = $(this).data('type') === 'take_away';
                }

                if (textMatch && typeMatch) {
                    $(this).removeClass('hidden');
                } else {
                    $(this).addClass('hidden');
                }
            });
        }

        // Update Kitchen Status of order via AJAX
        function updateStatus(uuid, status, $card) {
            loading();
            let url = "{{ route('kitchen.status.update', ':id') }}";
            url = url.replace(':id', uuid);

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    removeLoading();
                    if (data.success === true) {
                        $card.fadeOut(250, function() {
                            $(this).remove();
                            loadQueue();
                        });
                    }
                },
                error: function(xhr) {
                    removeLoading();
                    const err = xhr.responseJSON;
                    oAlert("red", "Gagal", err ? err.message : "Gagal memperbarui status antrean.");
                }
            });
        }
    </script>

    {{-- Scroll to + briefly highlight the order that triggered a "Pesanan Baru Masuk" push
         notification (e.g. "?transaction=<uuid>"). Cards render async via live-updates polling,
         so poll for it instead of assuming it's already in the DOM.
         Pure vanilla JS on purpose — must not depend on jQuery having loaded yet. --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var params = new URLSearchParams(window.location.search);
            var targetUuid = params.get('transaction');
            if (!targetUuid) return;

            var attempts = 0;
            var maxAttempts = 10; // ~5s at 500ms interval

            var poll = setInterval(function() {
                attempts++;
                var card = document.querySelector('.order-card[data-uuid="' + targetUuid + '"]');

                if (card) {
                    clearInterval(poll);
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    card.classList.add('highlight-target-card');
                    setTimeout(function() {
                        card.classList.remove('highlight-target-card');
                    }, 5000);
                } else if (attempts >= maxAttempts) {
                    clearInterval(poll);
                }
            }, 500);

            var url = new URL(window.location.href);
            url.searchParams.delete('transaction');
            window.history.replaceState({}, '', url);
        });
    </script>

    <!-- Custom CSS inside container -->
    <style>
        .active-filter {
            box-shadow: 0 4px 14px rgba(6, 182, 212, 0.25) !important;
        }

        .pulse-card {
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1) !important;
            animation: pulse-border 2s infinite ease-in-out;
        }

        @keyframes pulse-border {
            0%, 100% {
                border-color: rgba(239, 68, 68, 0.25);
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.05) !important;
            }
            50% {
                border-color: rgba(239, 68, 68, 0.6);
                box-shadow: 0 0 0 5px rgba(239, 68, 68, 0.2) !important;
            }
        }

        .highlight-target-card {
            box-shadow: 0 0 0 2px rgba(43, 102, 255, 0.15) !important;
            animation: pulse-border-brand 1.4s infinite ease-in-out;
        }

        @keyframes pulse-border-brand {
            0%, 100% {
                border-color: rgba(43, 102, 255, 0.3);
                box-shadow: 0 0 0 2px rgba(43, 102, 255, 0.08) !important;
            }
            50% {
                border-color: rgba(43, 102, 255, 0.7);
                box-shadow: 0 0 0 6px rgba(43, 102, 255, 0.22) !important;
            }
        }
    </style>
@endsection
