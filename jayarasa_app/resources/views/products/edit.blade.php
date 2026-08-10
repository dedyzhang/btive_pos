@extends('layout.index')

@section('title','Categories')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">PRODUCTS <span class="text-base text-gray-400">> Edit Products</span></h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <!-- Cropper.js CDN Dependencies -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    
    <form action="{{route('products.update',$product->uuid)}}" method="POST" class="p-6" enctype="multipart/form-data">
        @method('put')
        @csrf
        <input type="hidden" name="cropped_image_data" id="cropped_image_data" />
        <div class="container-place w-full sm:w-[80%] grid grid-cols-1 gap-2 bg-white rounded-lg p-6">
            <div class="col-span-1">
                <label for="name" class="text-sm font-medium text-gray-700 mb-1 block">Nama Produk</label>
                <input type="text" name="name" id="name" placeholder="Masukkan Nama Produk" class="w-full px-5 py-3 rounded focus:outline-none  @error('name') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('name',$product->name) }}">
                @error('name')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="categories" class="text-sm font-medium text-gray-700 mb-1 block">Kategori</label>
                <select name="categories" id="categories" placeholder="Masukkan Kategori Produk" class="w-full px-5 py-3 rounded focus:outline-none  @error('categories') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror">
                    <option value="">Select Categories</option>
                    @foreach ($categories as $category)
                        <option {{ old('categories',$product->category_id) == $category->uuid ? "selected" : "" }} value="{{ $category->uuid }}">{{$category->nama}}</option>
                    @endforeach
                </select>
                @error('categories')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="price" class="text-sm font-medium text-gray-700 mb-1 block">Harga Jual</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        Rp
                    </div>
                    <input type="number" name="price" id="price" placeholder="Masukkan Harga Jual Produk" class="w-full ps-10 pe-5 py-3 rounded focus:outline-none  @error('price') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('price',$product->price) }}">
                </div>
                @error('price')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="cost_price" class="text-sm font-medium text-gray-700 mb-1 block">Harga Modal</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        Rp
                    </div>
                    <input type="number" name="cost_price" id="cost_price" placeholder="Masukkan Harga Modal Produk (HPP)" class="w-full ps-10 pe-5 py-3 rounded focus:outline-none  @error('cost_price') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('cost_price',$product->cost_price) }}">
                </div>
                @error('cost_price')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="stock" class="text-sm font-medium text-gray-700 mb-1 block">Stok Produk</label>
                <input type="number" name="stock" id="stock" placeholder="Masukkan Stok Produk" class="w-full px-5 py-3 rounded focus:outline-none  @error('stock') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('stock',$product->stock) }}">
                @error('stock')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="description" class="text-sm font-medium text-gray-700 mb-1 block">Deskripsi Produk</label>
                <textarea name="description" id="description" rows="5" placeholder="Masukkan Deskripsi Produk (Ingredients yang ditanyakan oleh customer)" class="w-full px-5 py-3 rounded focus:outline-none  @error('description') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror">{{ old('description',$product->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="picture" class="text-sm font-medium text-gray-700 mb-1 block">Gambar Produk</label>
                
                <div class="flex items-center justify-center w-full uploaded-place {{ $product->picture != "" ? 'hidden' : '' }}">
                    <label for="picture" class="flex flex-col items-center justify-center w-full h-64 bg-neutral-secondary-medium border border-dashed border-default-strong rounded-base cursor-pointer hover:bg-neutral-tertiary-medium">
                        <div class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/></svg>
                            <p class="mb-2 text-sm"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs">PNG, JPG or GIF (MAX : 2MB )</p>
                        </div>
                        <input id="picture" name="picture" type="file" class="hidden" />
                    </label>
                </div> 
                <div class="preview-place flex border border-gray-200 rounded-lg w-full sm:w-100 relative {{ $product->picture == "" ? 'hidden' : '' }}">
                    <input type="hidden" name="is_deleted" id="is_deleted" value="0" />
                    <button type="button" class="absolute flex justify-center top-1 right-2 text-3xl delete-image cursor-pointer w-10 h-10 bg-neutral-primary hover:bg-brand-light hover:text-neutral-primary shadow-lg rounded-full">
                        &times;
                    </button>
                    <div class="image-place w-full sm:w-100 h-50">
                        <img src="{{ $product->picture != "" ? asset('storage/products/'.$product->picture) : '' }}" class="w-full h-50 object-contain" id="image-preview" alt="Preview Image Uploaded" />
                    </div>
                </div>
                @error('picture')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="col-span-1 mt-5">
                <button type="submit" class="w-full bg-brand hover:bg-brand-strong text-white font-medium py-2.5 px-6 cursor-pointer rounded-base w-full sm:w-auto transition-all shadow-md shadow-brand/20"><i class="fas fa-pencil"></i> Edit Product</button>
            </div>
        </div>

        <!-- HTML Modal Cropper -->
        <div id="cropperModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden transform transition-all duration-300 scale-95 opacity-100 flex flex-col">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Crop Product Picture</h3>
                    <button type="button" class="close-cropper text-gray-400 hover:text-gray-600 text-2xl cursor-pointer outline-0">&times;</button>
                </div>
                <!-- Body -->
                <div class="p-6 flex flex-col items-center justify-center bg-gray-50/50 flex-1">
                    <div class="w-full max-h-[350px] overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                        <img id="cropperImage" class="max-w-full max-h-[300px] block" src="" alt="To Crop" />
                    </div>
                    <div class="mt-4 flex gap-2 flex-wrap justify-center">
                        <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-semibold cursor-pointer border border-gray-300 transition-all" id="rotate-left"><i class="fas fa-rotate-left mr-1"></i> Rotate Left</button>
                        <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-semibold cursor-pointer border border-gray-300 transition-all" id="rotate-right"><i class="fas fa-rotate-right mr-1"></i> Rotate Right</button>
                        <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-semibold cursor-pointer border border-gray-300 transition-all" id="reset-crop"><i class="fas fa-arrows-rotate mr-1"></i> Reset</button>
                    </div>
                </div>
                <!-- Footer -->
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/20">
                    <button type="button" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm font-semibold cursor-pointer transition-all close-cropper">Cancel</button>
                    <button type="button" class="px-5 py-2.5 bg-brand hover:bg-brand-strong text-white rounded-lg text-sm font-semibold shadow-md shadow-brand/20 transition-all cursor-pointer" id="save-crop">Crop & Apply</button>
                </div>
            </div>
        </div>
        
    </form>
    <script type="module">
        let cropper = null;

        function initCropper(imageSrc) {
            $('#cropperImage').attr('src', imageSrc);
            $('#cropperModal').removeClass('hidden');
            
            const image = document.getElementById('cropperImage');
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(image, {
                aspectRatio: 1, // Force square crop
                viewMode: 1,
                autoCropArea: 0.8,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false
            });
        }

        $('#picture').change(function() {
            var file = this.files[0];
            if (!file) return;

            var filename = file.name;
            var filesize = file.size;
            const maxSizeInBytes = 2097152;
            var ext = file.name.split('.').pop().toLowerCase();
            var allow_ext = ['jpg','jpeg','png'];

            if($.inArray(ext,allow_ext) == -1) {
                oAlert("red","Warning","File Must Be JPG, JPEG and PNG");
                $(this).val("");
                return false;
            }
            if(filesize > maxSizeInBytes) {
                oAlert("red","Warning","File size must below 2MB");
                $(this).val("");
                return false;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                initCropper(e.target.result);
            }
            reader.readAsDataURL(file);
        });

        // Cropper Actions
        $('#rotate-left').on('click', function() {
            if (cropper) cropper.rotate(-90);
        });

        $('#rotate-right').on('click', function() {
            if (cropper) cropper.rotate(90);
        });

        $('#reset-crop').on('click', function() {
            if (cropper) cropper.reset();
        });

        $('#save-crop').on('click', function() {
            if (cropper) {
                var canvas = cropper.getCroppedCanvas({
                    width: 600,
                    height: 600
                });
                var croppedDataUrl = canvas.toDataURL('image/png');
                
                // Set hidden input data
                $('#cropped_image_data').val(croppedDataUrl);
                
                // Set preview
                $('#image-preview').attr('src', croppedDataUrl);
                
                // Hide input, show preview container
                $('#picture').closest('.uploaded-place').addClass('hidden');
                $('.preview-place').removeClass('hidden');
                
                // We are uploading/saving a new cropped image, so reset is_deleted to 0
                $('#is_deleted').val("0");
                
                // Cleanup
                $('#cropperModal').addClass('hidden');
                cropper.destroy();
                cropper = null;
            }
        });

        $('.close-cropper').on('click', function() {
            $('#picture').val("");
            $('#cropperModal').addClass('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        $('.preview-place').on('click','.delete-image',function(){
            $('#is_deleted').val("1");
            $('#image-preview').attr('src','');
            $('#picture').val("");
            $('#cropped_image_data').val("");
            $('#picture').closest('.uploaded-place').removeClass('hidden');
            $('.preview-place').addClass('hidden');
        });

    </script>
@endsection