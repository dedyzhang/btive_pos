<div class="w-[260px] hidden bg-white/95 backdrop-blur-md shadow-[4px_0_30px_rgba(0,0,0,0.06)] fixed top-0 left-0 h-full sidebar z-99 border-r border-gray-100/50 flex flex-col justify-between transition-all duration-300">
    <div class="sidebar-container flex flex-col h-full justify-between">
        
        <!-- Top Section: Profile Card -->
        <div class="profile-place w-full shrink-0">
            <div class="profile-place-inner p-4 flex items-center justify-between border-b border-gray-100 relative">
                <!-- User Profile Card -->
                <div class="profile flex-grow p-1.5 hover:bg-gray-50 rounded-2xl flex items-center gap-3 cursor-pointer transition-all duration-200 btn-toggle-profile-dropdown select-none">
                    <div class="relative shrink-0">
                        <img id="sidebar-user-avatar" src="{{ $account->profile_picture ? (str_starts_with($account->profile_picture, 'resources/') ? Vite::asset($account->profile_picture) : asset($account->profile_picture)) : Vite::asset('resources/img/profile/boy_1.png') }}" class="rounded-full w-9 h-9 object-cover ring-2 ring-brand-medium/20 shadow-sm" />
                    </div>
                    <div class="profile-info overflow-hidden flex-grow pr-1">
                        <h3 id="sidebar-user-name" class="font-bold text-sm leading-tight truncate text-gray-800">{{$account->name}}</h3>
                        <p id="sidebar-user-role" class="text-[11px] font-semibold text-gray-400 leading-tight uppercase tracking-wider mt-0.5">{{ $account->role }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-[10px] transition-transform duration-300 icon-profile-chevron shrink-0 mr-1"></i>
                </div>

                <!-- Sleek Close Sidebar Trigger -->
                <div class="close-sidebar shrink-0 ml-2">
                    <button class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-full flex items-center justify-center cursor-pointer transition-all border-none outline-none">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Premium Floating Profile Dropdown -->
                <div id="profile-dropdown" class="hidden absolute top-[85%] left-4 right-4 bg-white/95 backdrop-blur-md border border-gray-100/50 rounded-2xl shadow-xl z-50 py-1.5 mt-1 transform origin-top transition-all duration-200 scale-95 opacity-0">
                    <a href="#" class="px-4 py-2.5 hover:bg-brand/5 hover:text-brand flex items-center gap-3 text-gray-700 font-semibold text-xs transition-all duration-200 btn-open-edit-profile group border-b border-gray-100/50">
                        <div class="w-7 h-7 rounded-full bg-gray-50 group-hover:bg-brand/10 text-gray-500 group-hover:text-brand flex items-center justify-center transition-all duration-200">
                            <i class="fas fa-user-edit text-[10px]"></i>
                        </div>
                        Edit Profil
                    </a>
                    <a href="#" class="px-4 py-2.5 hover:bg-brand/5 hover:text-brand flex items-center gap-3 text-gray-700 font-semibold text-xs transition-all duration-200 btn-open-change-password group">
                        <div class="w-7 h-7 rounded-full bg-gray-50 group-hover:bg-brand/10 text-gray-500 group-hover:text-brand flex items-center justify-center transition-all duration-200">
                            <i class="fas fa-key text-[10px]"></i>
                        </div>
                        Ganti Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Middle Section: Navigation Menu Items -->
        <div class="navigation-menu-place flex-grow overflow-y-auto py-3">
            <ul class="list-none p-0 m-0 space-y-0.5">
                @can('access_admin_dashboard')
                        <!-- 0. Admin Dashboard -->
                        @php $dashboardActive = request()->routeIs('admin.dashboard'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $dashboardActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $dashboardActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-chart-pie text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Dashboard</span>
                            </a>
                        </li>
                    @endcan

                    <!-- 1. Point of Sales -->
                    @can('access_cashier')
                        @php $posActive = request()->routeIs('auth.index'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('auth.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $posActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $posActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-home text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Point of Sales</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Antrean Dapur -->
                    @can('view_kitchen_queue')
                        @php $kitchenActive = request()->routeIs('kitchen.queue'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('kitchen.queue') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $kitchenActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $kitchenActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-fire-burner text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Antrean Dapur</span>
                            </a>
                        </li>
                    @endcan
                    
                    <!-- 2. Products -->
                    @can('manage_products')
                        @php $productsActive = request()->routeIs('products.*') || request()->routeIs('categories.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('products.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $productsActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $productsActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-apple-whole text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Products</span>
                            </a>
                        </li>
                    @endcan
                    
                    <!-- 3. Activity -->
                    @can('view_reports')
                        @php $activityActive = request()->routeIs('activity.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('activity.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $activityActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $activityActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-arrow-trend-up text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Activity</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Cash Flow -->
                    @can('manage_cashflow')
                        @php $cashFlowActive = request()->routeIs('cashflow.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('cashflow.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $cashFlowActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $cashFlowActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-wallet text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Cash Flow</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Pengajuan Belanja Stok -->
                    @can('manage_stock')
                        @php $purchaseRequestActive = request()->routeIs('purchase-request.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('purchase-request.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $purchaseRequestActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $purchaseRequestActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-cart-shopping text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Pengajuan Belanja</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Stock Opname -->
                    @can('manage_stock')
                        @php $opnameActive = request()->routeIs('stock-opname.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('stock-opname.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $opnameActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $opnameActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-clipboard-check text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Stock Opname</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Resep & HPP (admin) -->
                    @can('admin')
                        @php $recipeActive = request()->routeIs('product-recipe.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('product-recipe.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $recipeActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $recipeActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-utensils text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Resep &amp; HPP</span>
                            </a>
                        </li>
                    @endcan

                    <!-- Master Barang / Stok (admin) -->
                    @can('admin')
                        @php $supplyItemActive = request()->routeIs('supply-item.*'); @endphp
                        <li class="mx-3 my-0.5">
                            <a href="{{ route('supply-item.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $supplyItemActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $supplyItemActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                    <i class="fas fa-warehouse text-sm"></i>
                                </div>
                                <span class="text-xs tracking-wide">Master Barang</span>
                            </a>
                        </li>
                    @endcan

                <!-- 4. Absensi Staf -->
                <li class="mx-3 my-0.5 btn-open-attendance">
                    <div class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand">
                            <i class="fas fa-clock-rotate-left text-sm"></i>
                        </div>
                        <span class="text-xs tracking-wide">Absensi Staf</span>
                    </div>
                </li>
                
                <!-- 5. Rekap Absensi -->
                @can('manage_attendance')
                    @php $recapActive = request()->routeIs('attendance.*'); @endphp
                    <li class="mx-3 my-0.5">
                        <a href="{{ route('attendance.recap') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $recapActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $recapActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                <i class="fas fa-clipboard-user text-sm"></i>
                            </div>
                            <span class="text-xs tracking-wide">Rekap Absensi</span>
                        </a>
                    </li>
                @endcan

                <!-- 5.5. Penggajian Staf -->
                @can('manage_attendance')
                    @php $payrollActive = request()->routeIs('payroll.*'); @endphp
                    <li class="mx-3 my-0.5">
                        <a href="{{ route('payroll.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $payrollActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $payrollActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                <i class="fas fa-money-bill-wave text-sm"></i>
                            </div>
                            <span class="text-xs tracking-wide">Penggajian</span>
                        </a>
                    </li>
                @endcan
                
                <!-- 6. Users -->
                @can('manage_users')
                    @php $usersActive = request()->routeIs('users.*') || request()->routeIs('roles.*'); @endphp
                    <li class="mx-3 my-0.5">
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $usersActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $usersActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span class="text-xs tracking-wide">Users</span>
                        </a>
                    </li>
                @endcan
                
                <!-- 7. Settings -->
                @can('manage_settings')
                    @php $settingsActive = request()->routeIs('settings.*'); @endphp
                    <li class="mx-3 my-0.5">
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer {{ $settingsActive ? 'bg-brand text-white shadow-lg shadow-brand/15 font-bold' : 'text-gray-600 hover:text-brand hover:bg-brand/5 font-semibold' }}">
                            <div class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 shrink-0 {{ $settingsActive ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-brand/10 group-hover:text-brand' }}">
                                <i class="fas fa-gear text-sm"></i>
                            </div>
                            <span class="text-xs tracking-wide">Settings</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>

        <!-- Bottom Section: Sleek Logout Button -->
        <div class="logout-button-wrapper w-full p-4 shrink-0 border-t border-gray-100 mt-auto">
            <button class="w-full group hover:bg-red-50 bg-gray-50 hover:text-red-600 text-gray-600 border border-gray-100 flex items-center justify-center gap-2.5 rounded-xl py-2.5 px-4 cursor-pointer transition-all duration-300 logout-button-place outline-none">
                <i class="fas fa-sign-out text-sm text-red-400 group-hover:text-red-500 transition-colors"></i>
                <span class="text-xs font-bold tracking-wide">Logout</span>
            </button>
        </div>

        <!-- Dropdown and profile logic script -->
        <script type="module">
            $(document).ready(function() {
                // Toggle Profile Dropdown
                $(document).on('click', '.btn-toggle-profile-dropdown', function(e) {
                    e.stopPropagation();
                    const dropdown = $('#profile-dropdown');
                    const chevron = $('.icon-profile-chevron');
                    
                    if (dropdown.hasClass('hidden')) {
                        dropdown.removeClass('hidden');
                        setTimeout(() => {
                            dropdown.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
                            chevron.addClass('rotate-180');
                        }, 10);
                    } else {
                        hideProfileDropdown();
                    }
                });

                // Hide Profile Dropdown Helper
                function hideProfileDropdown() {
                    const dropdown = $('#profile-dropdown');
                    const chevron = $('.icon-profile-chevron');
                    if (!dropdown.hasClass('hidden')) {
                        dropdown.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
                        chevron.removeClass('rotate-180');
                        setTimeout(() => {
                            dropdown.addClass('hidden');
                        }, 200);
                    }
                }

                // Close Dropdown when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.btn-toggle-profile-dropdown, #profile-dropdown').length) {
                        hideProfileDropdown();
                    }
                });

                // Close dropdown when action clicked
                $(document).on('click', '.btn-open-change-password', function() {
                    hideProfileDropdown();
                });

                $(document).on('click', '.btn-open-edit-profile', function() {
                    hideProfileDropdown();
                });

                $('.logout-button-place').off('click').on('click', function() {
                    cConfirm("Perhatian","Confirm To Logout?",function() {
                        loading();

                        // Unlink this device's FCM token from the logging-out user first, so a
                        // shared/handed-off device doesn't keep notifying whoever used it last.
                        var fcmToken = localStorage.getItem('jayarasa_fcm_token');
                        var unregisterFcm = fcmToken
                            ? $.ajax({
                                type: "delete",
                                url: "{{ route('fcm.token.destroy') }}",
                                headers: {'X-CSRF-TOKEN' : '{{ csrf_token() }}'},
                                data: { fcm_token: fcmToken }
                            })
                            : $.Deferred().resolve();

                        unregisterFcm.always(function() {
                            localStorage.removeItem('jayarasa_fcm_token');
                            $.ajax({
                                type: "post",
                                url : "{{ route('auth.logout') }}",
                                headers: {'X-CSRF-TOKEN' : '{{ csrf_token() }}'},
                                success: function(data) {
                                    if(data.success === true) {
                                        removeLoading();
                                        cAlert("green","Success",data.message,true);
                                        setTimeout(() => {
                                            window.location.href = "/";
                                        }, 1500);
                                    }
                                },
                                error: function(data) {
                                    console.log(data.responseJSON.message);
                                }
                            });
                        });
                    });
                });
            });
        </script>
    </div>
</div>