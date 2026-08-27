@extends('layout.index')

@section('title', 'Roles & Permissions')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold text-gray-800">ROLES & ACCESS</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="container-place w-full p-6">
        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 mb-6 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success')}}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Side: Role Form (Create / Edit) -->
            <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all duration-300">
                <div class="border-b border-gray-100 pb-3 mb-5">
                    <h3 id="form-title" class="text-lg font-bold text-gray-800">Tambah Role Baru</h3>
                    <p id="form-desc" class="text-xs text-gray-400 mt-0.5">Definisikan nama role dan tentukan hak akses fiturnya.</p>
                </div>

                <form id="role-form" action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">

                    <!-- Role Name -->
                    <div class="mb-5">
                        <label for="role-name" class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Nama Role</label>
                        <input type="text" name="name" id="role-name" placeholder="Contoh: supervisor, manager, cashier" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-400 border border-default text-sm font-semibold transition-all" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions Checkboxes Grouped -->
                    <div class="mb-6">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Hak Akses Fitur (Permissions)</label>

                        <!-- Group 1: Core Panel Access -->
                        <div class="mb-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-cubes text-brand text-xs"></i>
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Akses Utama</span>
                            </div>
                            <div class="space-y-2.5">
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="access_admin_dashboard" class="perm-checkbox w-4.5 h-4.5 text-brand bg-white border-gray-300 rounded focus:ring-brand focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Dashboard Admin</span>
                                        <span class="text-gray-400">Melihat ringkasan omzet, laba rugi, dan statistik penjualan.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="access_cashier" class="perm-checkbox w-4.5 h-4.5 text-brand bg-white border-gray-300 rounded focus:ring-brand focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Point of Sales (Kasir)</span>
                                        <span class="text-gray-400">Akses halaman kasir utama, membuat & membayar transaksi.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="view_kitchen_queue" class="perm-checkbox w-4.5 h-4.5 text-brand bg-white border-gray-300 rounded focus:ring-brand focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Antrean Dapur</span>
                                        <span class="text-gray-400">Melihat antrean pesanan aktif tanpa melihat rincian harga/biaya.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Group 2: Management Access -->
                        <div class="mb-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-boxes-stacked text-emerald-500 text-xs"></i>
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Pengelolaan Data</span>
                            </div>
                            <div class="space-y-2.5">
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_products" class="perm-checkbox w-4.5 h-4.5 text-emerald-500 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Kelola Produk</span>
                                        <span class="text-gray-400">Menambah, mengedit, menghapus, & stok produk.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_categories" class="perm-checkbox w-4.5 h-4.5 text-emerald-500 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Kelola Kategori</span>
                                        <span class="text-gray-400">Mengelola kategori menu makanan & minuman.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_cashflow" class="perm-checkbox w-4.5 h-4.5 text-emerald-500 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Kelola Arus Kas (Cash Flow)</span>
                                        <span class="text-gray-400">Pencatatan pengeluaran operasional & mutasi kas toko.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_stock" class="perm-checkbox w-4.5 h-4.5 text-emerald-500 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Pengajuan Belanja Stok</span>
                                        <span class="text-gray-400">Mengajukan daftar barang yang perlu dibeli. Menandai sudah dibeli & kelola master barang tetap khusus admin.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_users" class="perm-checkbox w-4.5 h-4.5 text-emerald-500 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Kelola Users & Role</span>
                                        <span class="text-gray-400">Membuat akun karyawan, menyetel kata sandi, & role akses.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_attendance" class="perm-checkbox w-4.5 h-4.5 text-emerald-500 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Kelola Absensi Staf</span>
                                        <span class="text-gray-400">Melihat rekap kehadiran staf & mengekspor data absensi.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Group 3: System, Settings & Logs -->
                        <div class="mb-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-gears text-purple-500 text-xs"></i>
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Sistem & Laporan</span>
                            </div>
                            <div class="space-y-2.5">
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="manage_settings" class="perm-checkbox w-4.5 h-4.5 text-purple-500 bg-white border-gray-300 rounded focus:ring-purple-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Kelola Settings</span>
                                        <span class="text-gray-400">Pengaturan printer, informasi toko, pajak, meja, & urutan kategori.</span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="permissions[]" value="view_reports" class="perm-checkbox w-4.5 h-4.5 text-purple-500 bg-white border-gray-300 rounded focus:ring-purple-500 focus:ring-2 mt-0.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-700 block">Laporan & Riwayat Aktivitas</span>
                                        <span class="text-gray-400">Melihat log penjualan, rekap menu terlaris, & ekspor laporan bulanan.</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" id="btn-submit" class="flex-grow bg-brand hover:bg-brand-strong text-white font-semibold py-2.5 px-4 rounded-xl cursor-pointer transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i> <span id="btn-submit-text">Tambah Role</span>
                        </button>
                        <button type="button" id="btn-cancel" class="hidden bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-4 rounded-xl cursor-pointer transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Roles List & Permissions Badges -->
            <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all duration-300">
                <div class="border-b border-gray-100 pb-3 mb-5">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Role Terdaftar</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar peran pengguna sistem dan setelan perizinan aktif.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/70 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-4 py-4 font-bold rounded-tl-xl text-gray-600">Nama Role</th>
                                <th scope="col" class="px-4 py-4 font-bold text-gray-600">Permissions Terpasang</th>
                                <th scope="col" class="px-4 py-4 font-bold rounded-tr-xl text-center text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Hardcoded Admin (System Master) -->
                            <tr class="bg-gray-50/20 text-gray-700">
                                <td class="px-4 py-4 font-bold text-heading whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">admin</span>
                                        <span class="bg-red-50 text-red-600 border border-red-100 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">Sistem</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-block bg-neutral-primary-soft text-gray-600 border border-default px-2.5 py-0.5 rounded-full text-xs font-semibold select-none shadow-3xs"><i class="fas fa-infinity text-[9px] text-gray-400 me-1"></i> Semua Akses (Super-Admin)</span>
                                </td>
                                <td class="px-4 py-4 text-center text-xs text-gray-400 italic">
                                    Tidak dapat diubah
                                </td>
                            </tr>

                            <!-- Database Custom Roles -->
                            @if(count($roles) == 0)
                                <!-- Only system default role present -->
                            @endif

                            @foreach($roles as $role)
                                @if($role->name !== 'admin')
                                    <tr class="bg-white hover:bg-gray-50/50 transition-colors role-row" data-uuid="{{ $role->uuid }}" data-name="{{ $role->name }}" data-permissions="{{ json_encode($role->permissions) }}">
                                        <td class="px-4 py-4 font-bold text-heading whitespace-nowrap capitalize">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $role->name }}</span>
                                                @if($role->name == 'cashier')
                                                    <span class="bg-blue-50 text-blue-600 border border-blue-100 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">Kasir Utama</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap gap-1.5 max-w-[400px]">
                                                @if(is_array($role->permissions) && count($role->permissions) > 0)
                                                    @foreach($role->permissions as $perm)
                                                        @php
                                                            $badgeColor = 'bg-brand-soft text-brand-light border-brand-medium';
                                                            if(str_starts_with($perm, 'manage_')) {
                                                                $badgeColor = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                                            } elseif(str_starts_with($perm, 'view_') || $perm == 'view_reports') {
                                                                $badgeColor = 'bg-purple-50 text-purple-600 border-purple-100';
                                                            }
                                                        @endphp
                                                        <span class="inline-block {{ $badgeColor }} border px-2 py-0.5 rounded-lg text-[10px] font-semibold capitalize select-none shadow-3xs">{{ str_replace('_', ' ', $perm) }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-xs text-gray-400 font-semibold italic">Belum disetel</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="inline-flex rounded-xl shadow-xs" role="group">
                                                <button type="button" class="btn-edit bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded-l-xl text-xs font-bold cursor-pointer transition-all flex items-center gap-1.5 outline-none">
                                                    <i class="fas fa-edit text-gray-400"></i> Edit
                                                </button>
                                                @if($role->name !== 'cashier' && $role->name !== 'dapur')
                                                    <button type="button" class="btn-delete bg-white border-t border-b border-r border-gray-200 hover:bg-red-50 hover:text-red-600 text-gray-700 px-3 py-1.5 rounded-r-xl text-xs font-bold cursor-pointer transition-all flex items-center gap-1.5 outline-none">
                                                        <i class="fas fa-trash-can text-gray-400 hover:text-red-500"></i> Hapus
                                                    </button>
                                                @else
                                                    <button type="button" class="bg-gray-50 border-t border-b border-r border-gray-200 text-gray-300 px-3 py-1.5 rounded-r-xl text-xs font-bold cursor-not-allowed flex items-center gap-1.5 outline-none" disabled>
                                                        Hapus
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        $(document).ready(function() {
            // Edit Button Trigger
            $(document).on('click', '.btn-edit', function() {
                const row = $(this).closest('.role-row');
                const uuid = row.data('uuid');
                const name = row.data('name');
                const permissions = row.data('permissions');

                // Update form header
                $('#form-title').text('Edit Role: ' + name);
                $('#form-desc').text('Ubah setelan izin akses untuk role ini.');

                // Populate Name Field (Prevent modifying name for cashier)
                $('#role-name').val(name);
                if (name === 'cashier' || name === 'dapur') {
                    $('#role-name').prop('readonly', true).addClass('bg-gray-50 text-gray-400 cursor-not-allowed');
                } else {
                    $('#role-name').prop('readonly', false).removeClass('bg-gray-50 text-gray-400 cursor-not-allowed');
                }

                // Populate Checkboxes
                $('.perm-checkbox').prop('checked', false);
                if (Array.isArray(permissions)) {
                    permissions.forEach(perm => {
                        $(`.perm-checkbox[value="${perm}"]`).prop('checked', true);
                    });
                }

                // Change Form Actions
                let updateRoute = "{{ route('roles.update', ':id') }}";
                updateRoute = updateRoute.replace(':id', uuid);
                $('#role-form').attr('action', updateRoute);
                $('#form-method').val('PUT');

                // Adjust buttons
                $('#btn-submit-text').text('Simpan Perubahan');
                $('#btn-submit').find('i').removeClass('fa-plus').addClass('fa-save');
                $('#btn-cancel').removeClass('hidden');

                // Smooth scroll to form on mobile
                $('html, body').animate({
                    scrollTop: $("#role-form").offset().top - 100
                }, 300);
            });

            // Cancel Edit Trigger
            $('#btn-cancel').on('click', function() {
                resetForm();
            });

            function resetForm() {
                $('#form-title').text('Tambah Role Baru');
                $('#form-desc').text('Definisikan nama role dan tentukan hak akses fiturnya.');
                
                $('#role-name').val('').prop('readonly', false).removeClass('bg-gray-50 text-gray-400 cursor-not-allowed');
                $('.perm-checkbox').prop('checked', false);

                $('#role-form').attr('action', "{{ route('roles.store') }}");
                $('#form-method').val('POST');

                $('#btn-submit-text').text('Tambah Role');
                $('#btn-submit').find('i').removeClass('fa-save').addClass('fa-plus');
                $('#btn-cancel').addClass('hidden');
            }

            // AJAX Delete Trigger
            $(document).on('click', '.btn-delete', function() {
                const row = $(this).closest('.role-row');
                const uuid = row.data('uuid');
                const name = row.data('name');

                let deleteRoute = "{{ route('roles.destroy', ':id') }}";
                deleteRoute = deleteRoute.replace(':id', uuid);

                cConfirm("Hapus Peran", `Apakah Anda yakin ingin menghapus role "${name}"? Pengguna dengan role ini tidak akan memiliki akses khusus lagi.`, function() {
                    loading();
                    $.ajax({
                        type: "DELETE",
                        url: deleteRoute,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            removeLoading();
                            if (data.success === true) {
                                cAlert("green", "Sukses", data.message, true);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        },
                        error: function(data) {
                            removeLoading();
                            var err = data.responseJSON;
                            oAlert("red", "Gagal", err ? err.message : "Gagal menghapus role.");
                        }
                    });
                });
            });
        });
    </script>
@endsection
