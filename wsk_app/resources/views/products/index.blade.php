@extends('layout.index')
@section('title','Products')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">PRODUCTS</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="container-place w-full p-6">
        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success')}}</p>
            </div>
        @endif
        <div class="button-list flex flex-wrap gap-2 align-items-center w-full mb-5 p-4 bg-white rounded-lg">
            <a href="{{ route('categories.index') }}" class="bg-brand hover:bg-brand-strong transition-all duration-200 shadow-sm cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium"><i class="fas fa-folder"></i> Category</a>
            <a href="{{ route('products.create') }}" class="bg-emerald-500 hover:bg-emerald-600 transition-all duration-200 shadow-sm cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium"><i class="fas fa-apple-whole"></i> Add Products</a>
        </div>
        <div class="categories-list max-w-full overflow-x-auto" id="categories-list">
            <ul class="inline-flex gap-3 mb-5">
                <li class="group w-40 relative">
                        <input type="radio" id="categories-all-item" name="category_filter" value="all-item" class="hidden peer category_filter" checked>
                        <div class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-brand scale-0 peer-checked:scale-100 transition-transform duration-200 z-20 pointer-events-none shadow-sm"></div>
                        <label for="categories-all-item" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-soft/50 peer-checked:border-brand-medium peer-checked:bg-brand-soft hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong peer-checked:ring-2 peer-checked:ring-inset peer-checked:ring-brand/70">                           
                            <div class="flex flex-wrap gap-2">
                                <div class="icon-place w-12 h-12 rounded-full bg-brand-subtle mb-3 flex items-center justify-center text-brand">
                                    <i class="fas fa-apple-whole text-xl"></i>
                                </div>
                                <div class="text-place w-full">
                                    <p class="text-xl">All Items</p>
                                    <p class="">{{ $products->count() }} Items</p>
                                </div>
                            </div>
                        </label>
                    </li>
                @foreach ($categories as $category)
                    @php
                        $splitcolor = explode('-', $category->color);
                        $colorName = isset($splitcolor[1]) ? $splitcolor[1] : 'brand';
                    @endphp
                    <li class="group w-40 relative">
                        <input type="radio" id="categories-{{ $category->uuid }}" name="category_filter" value="{{ $category->uuid }}" class="hidden peer category_filter">
                        <div class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-{{ $colorName }}-500 scale-0 peer-checked:scale-100 transition-transform duration-200 z-20 pointer-events-none shadow-sm"></div>
                        <label for="categories-{{ $category->uuid }}" class="inline-flex items-center justify-between w-full text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-{{ $colorName }}-100/50 peer-checked:border-{{ $colorName }}-300 peer-checked:bg-{{ $colorName }}-50 hover:bg-neutral-secondary-medium peer-checked:text-{{ $colorName }}-800 peer-checked:ring-2 peer-checked:ring-inset peer-checked:ring-{{ $colorName }}-400/70">                           
                            <div class="flex flex-wrap gap-2 relative p-5">
                                <div class="icon-place w-12 h-12 rounded-full bg-{{ $colorName }}-100 mb-3 flex items-center justify-center text-{{ $colorName }}-600">
                                    <i class="fas {{ $category->icon }} text-xl"></i>
                                </div>
                                <div class="text-place w-full">
                                    <p class="text-xl">{{$category->nama}}</p>
                                    <p class="">{{ $category->products()->count() }} Items</p>
                                </div>
                            </div>
                        </label>
                    </li>
                @endforeach
               
            </ul>
        </div>
        <div class="search-place w-full mb-5 relative">
            <div class="absolute inset-y-2 end-0 flex justify-center items-center w-10 h-10 bg-gray-200 rounded-full me-2 pointer-events-none">
                <svg class="w-5 h-5" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>search</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set" sketch:type="MSLayerGroup" transform="translate(-256.000000, -1139.000000)" fill="#000000"> <path d="M269.46,1163.45 C263.17,1163.45 258.071,1158.44 258.071,1152.25 C258.071,1146.06 263.17,1141.04 269.46,1141.04 C275.75,1141.04 280.85,1146.06 280.85,1152.25 C280.85,1158.44 275.75,1163.45 269.46,1163.45 L269.46,1163.45 Z M287.688,1169.25 L279.429,1161.12 C281.591,1158.77 282.92,1155.67 282.92,1152.25 C282.92,1144.93 276.894,1139 269.46,1139 C262.026,1139 256,1144.93 256,1152.25 C256,1159.56 262.026,1165.49 269.46,1165.49 C272.672,1165.49 275.618,1164.38 277.932,1162.53 L286.224,1170.69 C286.629,1171.09 287.284,1171.09 287.688,1170.69 C288.093,1170.3 288.093,1169.65 287.688,1169.25 L287.688,1169.25 Z" id="search" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
            </div>
            <input type="text" class="bg-neutral-primary focus:bg-brand-softer w-full px-5 py-4 rounded-full border-0 outline-0 placeholder-gray-500" name="search" id="searchInput" placeholder="Search Something Delicious On Your Mind">
        </div>
        <div class="product-list grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mt-2">
            @foreach ($products as $product)
                <div class="col-span-1 {{ $product->is_active == 1 ? 'bg-white' : 'bg-slate-200' }} rounded-lg p-3 flex flex-wrap gap-3 cursor-pointer product-item" data-uuid="{{ $product->uuid }}" data-category="{{ $product->category_id }}">
                    <div class="img-place w-full h-30 rounded-base overflow-hidden">
                        <img src="{{ $product->picture == "" ? Vite::asset('resources/img/no_image_available.png') : asset('storage/products/'.$product->picture) }}" class="w-full h-full object-cover object-center" />
                    </div>
                    <div class="product-detail w-full">
                        <p class="name-product text-base mb-2">{{Str::limit($product->name,25,'...')}}</p>
                        <div class="product-price-detail flex gap-2 w-full justify-between items-center">
                            @php
                                if ($product->category) {
                                    $splitcolor = explode('-', $product->category->color);
                                    $color = isset($splitcolor[1]) ? $splitcolor[1] : 'gray';
                                    $catColorClass = $product->category->color;
                                    $catColorText = "text-{$color}-800";
                                    $catName = $product->category->nama;
                                } else {
                                    $catColorClass = 'bg-gray-100';
                                    $catColorText = 'text-gray-800';
                                    $catName = 'Uncategorized';
                                }
                            @endphp
                            <p class="categories-product {{ $catColorClass }} {{ $catColorText }} px-3 py-1 rounded-full text-[10px]">{{ $catName }}</p>
                            <p class="price-product text-xl">Rp.{{ number_format($product->price,0,',') }},-</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modal Place --}}
        <div id="modal-preview-products" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-[90%]">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-3 md:p-3 rounded-t text-start sm:text-center">
                        <h3 class="text-lg font-semibold text-dark-soft w-full">
                            Detail Products
                        </h3>
                        <div class="button-place flex gap-1">
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-warning-subtle text-warning rounded-full hover:bg-orange-300 cursor-pointer outline-0 inline-flex justify-center items-center edit-product">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M13.0207 5.82839L15.8491 2.99996L20.7988 7.94971L17.9704 10.7781M13.0207 5.82839L3.41405 15.435C3.22652 15.6225 3.12116 15.8769 3.12116 16.1421V20.6776H7.65669C7.92191 20.6776 8.17626 20.5723 8.3638 20.3847L17.9704 10.7781M13.0207 5.82839L17.9704 10.7781" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                            </button>
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-brand-subtle text-brand rounded-full hover:bg-brand-strong hover:text-white cursor-pointer outline-0 inline-flex justify-center items-center delete-product">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M20.5001 6H3.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <path d="M18.8332 8.5L18.3732 15.3991C18.1962 18.054 18.1077 19.3815 17.2427 20.1907C16.3777 21 15.0473 21 12.3865 21H11.6132C8.95235 21 7.62195 21 6.75694 20.1907C5.89194 19.3815 5.80344 18.054 5.62644 15.3991L5.1665 8.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <path d="M6.5 6C6.55588 6 6.58382 6 6.60915 5.99936C7.43259 5.97849 8.15902 5.45491 8.43922 4.68032C8.44784 4.65649 8.45667 4.62999 8.47434 4.57697L8.57143 4.28571C8.65431 4.03708 8.69575 3.91276 8.75071 3.8072C8.97001 3.38607 9.37574 3.09364 9.84461 3.01877C9.96213 3 10.0932 3 10.3553 3H13.6447C13.9068 3 14.0379 3 14.1554 3.01877C14.6243 3.09364 15.03 3.38607 15.2493 3.8072C15.3043 3.91276 15.3457 4.03708 15.4286 4.28571L15.5257 4.57697C15.5433 4.62992 15.5522 4.65651 15.5608 4.68032C15.841 5.45491 16.5674 5.97849 17.3909 5.99936C17.4162 6 17.4441 6 17.5 6" stroke="#1C274C" stroke-width="1.5"></path> </g></svg>
                            </button>
                            <button type="button" class="text-sm w-9 h-9 ms-auto bg-danger-subtle text-danger rounded-full hover:bg-red-300 cursor-pointer outline-0 inline-flex justify-center items-center tutup-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-4 space-y-4">
                        <input type="hidden" name="uuid_product" id="uuid_product" />
                        <div class="picture-place flex align-center justify-center">
                            <div class="w-full h-70 rounded-lg">
                                <img src="" class="picture_product w-full h-70 object-cover object-center rounded-lg" />
                            </div>
                        </div>
                        <div class="text-place w-full flex flex-wrap gap-2">
                            <p class="category-product px-3 py-1 rounded-full text-[10px]"></p>
                            <h5 class="product-name text-2xl w-full font-semibold text-dark-soft"></h5>
                            <p class="product-description text-dark-soft"></p> 
                            <p class="product-price text-brand-light font-bold text-2xl w-full">Rp. 20.000,-</p>   
                            <p class="text-gray-500 text-lg w-full">HPP : <span class="product-cost-price"></span></p>   
                            <p class="stock-product text-gray-500 text-lg w-full">Stock : <span class="product-stock">0</span></p>
                            <div class="w-full toggle-product">
                                <label class="inline-flex items-center cursor-pointer">
                                    <span class="select-none text-sm font-medium text-heading">Non Active</span>
                                    <input type="checkbox" name="is_active_button" id="is_active_button" value="" class="sr-only peer" checked="">
                                    <div class="relative mx-3 w-9 h-5 bg-neutral-quaternary peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-soft rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-buffer after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                                    <span class="select-none text-sm font-medium text-heading">Active</span>
                                </label>    
                            </div>   
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script type="module">
        const osInstance = OverlayScrollbars(document.querySelector('#categories-list'), {});

        const $targetEl = document.getElementById("modal-preview-products");

        // options with default values
        const options = {
            placement: "center",
            backdrop: "dynamic",
            backdropClasses: "bg-gray-900/50 fixed inset-0 z-40",
            closable: true,
        };

        $('.product-item').on('click',function(){

            //removeAllModalPreviousData
            $('.picture_product').attr('src','');
            $(".category-product").removeClass (function (index, className) {
                return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
            });
            $('.product-name').text('');
            $('.product-description').text('');
            $('.product-price').text('');
            $('.product-cost-price').text('');
            $('.stock').text('');
            $('#is_active_button').prop('checked',false);
            $('#uuid_product').val('');

            var uuid = $(this).data('uuid');
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
                        var costPrice = "Rp."+addCommas(product.cost_price)+",-";
                        $('.product-price').text(price);
                        $('.product-cost-price').text(costPrice);
                        $('.stock').text(product.stock);
                        if(product.is_active === 1) {
                            $('#is_active_button').prop('checked',true);
                        } else {
                            $('#is_active_button').prop('checked',false);
                        }
                        $('#uuid_product').val(product.uuid);
                        
                        

                        //open Modal
                        removeLoading();
                        const modal = new Modal($targetEl,options);
                        modal.toggle();

                        $('.tutup-modal').on('click',function() {
                            modal.hide();
                        });

                        $('.edit-product').on('click',function() {
                            var uuid = $('#uuid_product').val();
                            var url = "{{ route('products.edit',':id') }}";
                            url = url.replace(':id',uuid);

                            window.location.href= url;
                        });

                        $('.delete-product').on('click',function() {
                            var uuid = $('#uuid_product').val();
                            var url = "{{ route('products.destroy',':id') }}";
                            url = url.replace(':id',uuid);

                            cConfirm("Warning","Confirm to delete this product?",function() {
                                loading();
                                $.ajax({
                                    type: "DELETE",
                                    url : url,
                                    headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                    success : function(data) {
                                        if(data.success === true) {
                                            removeLoading();
                                            cAlert("green","Success",data.message,true);
                                        }
                                    },
                                    error: function(data) {
                                        console.log(data.responseJSON.message);
                                    }
                                });
                            });
                        });
                        
                        $('input[name="is_active_button"]').change(function() {
                            var uuid = $('#uuid_product').val();
                            var url = "{{ route('products.active',':id') }}";
                            var active = $(this).is(':checked');
                            url = url.replace(':id',uuid);

                            loading();
                            $.ajax({
                                type: "POST",
                                url : url,
                                headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                data: {active : active},
                                success : function(data) {
                                    if(data.success === true) {
                                        removeLoading();

                                        if(active == true) {
                                            $('.product-item').filter(function(elem) {
                                                return $(this).data('uuid') == uuid;
                                            }).addClass('bg-white').removeClass('bg-slate-200');
                                        } else {
                                            $('.product-item').filter(function(elem) {
                                                return $(this).data('uuid') == uuid;
                                            }).addClass('bg-slate-200').removeClass('bg-white');
                                        }
                                    }
                                },
                                error: function(data) {
                                    console.log(data.responseJSON.message);
                                }
                            });
                        });

                    }
                }
            });
        });

        $('.category_filter').on('click',function() {
            var uuid = $(this).val();

            if(uuid == 'all-item') {
                $('.product-item').removeClass('hidden');
            } else {
                $('.product-item').filter(function(){
                    return $(this).data('category') != uuid    
                }).addClass('hidden');
                $('.product-item').filter(function(){
                    return $(this).data('category') == uuid    
                }).removeClass('hidden');
            }
        });

        $('#searchInput').on('keyup',function() {
            var value = this.value.toLowerCase().trim();
            var selected_filter = $('input[name="category_filter"]:checked').val();
            if(selected_filter == 'all-item') {
                $(".product-item").removeClass('hidden').filter(function() {
                    return $(this).find('.name-product').text().toLowerCase().trim().indexOf(value) == -1;
                }).addClass('hidden');
            } else {
                $(".product-item[data-category='"+selected_filter+"']").removeClass('hidden').filter(function() {
                    return $(this).find('.name-product').text().toLowerCase().trim().indexOf(value) == -1;
                }).addClass('hidden');
            }
            // console.log('a');
        })
    </script>
@endsection