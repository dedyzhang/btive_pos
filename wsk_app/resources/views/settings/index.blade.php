@extends('layout.index')
@section('title','Settings')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">SETTINGS</h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    <div class="container-place w-full p-6 flex gap-2 flex-wrap flex-col">
        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2 border-b border-gray-200 pb-3">
                <p class="text-lg font-bold">Setting Restaurant</p>
                <p class="text-sm">Restaurant Configuration</p>
            </div>
            @if(session('success_restaurant'))
            <div class="col-span-1 md:col-span-2 flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success_restaurant')}}</p>
            </div>
            @endif
            <div class="col-span-1">
                @php
                    $settingrestaurant = $settings->first(function($item) {
                        return $item->jenis == 'restaurant_settings';
                    });
                    $settingrestaurantlogo = $settings->first(function($item) {
                        return $item->jenis == 'restaurant_logo';
                    });

                    if($settingrestaurant && $settingrestaurant->nilai) {
                        $settingResArray = @unserialize($settingrestaurant->nilai);
                        if ($settingResArray === false) {
                            $settingResArray = @unserialize(stripslashes($settingrestaurant->nilai)) ?: array();
                        }
                    } else {
                        $settingResArray = array();
                    }
                @endphp
                <form method="POST" action="{{ route('settings.restaurant.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group flex-col w-full">
                        <label for="restaurant_name">Restaurant Name</label>
                        <input type="text" name="restaurant_name" id="restaurant_name" placeholder="Input Restaurant Name" class="w-full px-5 py-3 rounded focus:outline-none  @error('restaurant_name') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('restaurant_name',$settingResArray && $settingResArray['name'] ? $settingResArray['name'] : '') }}" />
                        @error('restaurant_name')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group flex-col w-full">
                        <label for="restaurant_location">Restaurant Location</label>
                        <input type="text" name="restaurant_location" id="restaurant_location" placeholder="Input Restaurant Location" class="w-full px-5 py-3 rounded focus:outline-none  @error('restaurant_location') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('restaurant_location',$settingResArray && $settingResArray['location'] ? $settingResArray['location'] : '') }}" />
                        @error('restaurant_location')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group flex-col w-full mt-3">
                        <label for="restaurant_accent_color">Restaurant Accent Color (Sistem Self-Order & Dashboard)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="restaurant_accent_color" id="restaurant_accent_color" class="w-14 h-12 p-0.5 rounded cursor-pointer border border-gray-200 bg-white" value="{{ old('restaurant_accent_color', $settingResArray && isset($settingResArray['accent_color']) ? $settingResArray['accent_color'] : '#2b66ff') }}" />
                            <div class="flex flex-wrap gap-1.5 max-w-md" id="color-presets">
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#2b66ff" style="background-color: #2b66ff;" title="Brand Blue"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#4f46e5" style="background-color: #4f46e5;" title="Indigo"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#8b5cf6" style="background-color: #8b5cf6;" title="Violet Purple"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#d946ef" style="background-color: #d946ef;" title="Fuchsia Pink"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#e11d48" style="background-color: #e11d48;" title="Rose Red"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#f43f5e" style="background-color: #f43f5e;" title="Coral Pink"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#dc2626" style="background-color: #dc2626;" title="Crimson Red"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#f97316" style="background-color: #f97316;" title="Sunset Orange"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#d97706" style="background-color: #d97706;" title="Amber Gold"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#eab308" style="background-color: #eab308;" title="Yellow Gold"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#84cc16" style="background-color: #84cc16;" title="Lime Green"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#10b981" style="background-color: #10b981;" title="Emerald Green"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#0d9488" style="background-color: #0d9488;" title="Teal Blue"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#0ea5e9" style="background-color: #0ea5e9;" title="Sky Blue"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#374151" style="background-color: #374151;" title="Charcoal Dark"></button>
                                <button type="button" class="w-8 h-8 rounded-full border border-gray-200 cursor-pointer btn-color-preset" data-color="#78350f" style="background-color: #78350f;" title="Warm Brown"></button>
                            </div>
                        </div>
                        @error('restaurant_accent_color')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group flex-col w-full">
                        <input type="hidden" value="0" name="is_changed" id="is_changed" />
                        <label for="picture" class="text-sm font-medium text-gray-700 mb-1 block">Restaurant Logo</label>
                        <div class="flex items-center justify-center w-full uploaded-place {{$settingrestaurantlogo && $settingrestaurantlogo->nilai ? 'hidden' : ''}}">
                            <label for="picture" class="flex flex-col items-center justify-center w-full h-64 bg-neutral-secondary-medium border border-dashed border-default-strong rounded-base cursor-pointer hover:bg-neutral-tertiary-medium">
                                <div class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/></svg>
                                    <p class="mb-2 text-sm"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs">PNG, JPG or GIF (MAX : 2MB )</p>
                                </div>
                                <input id="picture" name="picture" type="file" class="hidden" />
                            </label>
                        </div> 
                        <div class="preview-place flex border border-gray-200 rounded-lg w-full sm:w-100 relative {{$settingrestaurantlogo && $settingrestaurantlogo->nilai ? '' : 'hidden'}} ">
                            <button type="button" class="absolute flex justify-center top-1 right-2 text-3xl delete-image cursor-pointer w-10 h-10 bg-neutral-primary hover:bg-brand-light hover:text-neutral-primary shadow-lg rounded-full">
                                &times;
                            </button>
                            <div class="image-place w-full sm:w-100 h-50">
                                <img src="{{ $settingrestaurantlogo && $settingrestaurantlogo->nilai ? asset('storage/'.$settingrestaurantlogo->nilai) : '' }}" class="w-full h-50 object-contain" id="image-preview" alt="Preview Image Uploaded" />
                            </div>
                        </div>
                        @error('picture')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-brand text-white py-2 px-4 rounded hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand focus:ring-opacity-50 mt-3">
                        Update Restaurant
                    </button>
                </form>
            </div>
        </div>
        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2">
                <p class="text-lg font-bold">Setting Table</p>
                <p class="text-sm">Add, Edit and Delete Table that is available in the store</p>
            </div>
            @if(session('success_table'))
                <div class="col-span-1 md:col-span-2 flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                    <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                    <p><span class="font-medium me-1">Sukses!</span> {{session('success_table')}}</p>
                </div>
            @endif
            <div class="col-span-1 flex flex-wrap gap-2">
                <p class="w-full text-base pb-2 border-b border-b-gray-200">Add Tables Form</p>
                <form action="{{ route('settings.table.create') }}" method="POST">
                    @csrf
                    <div class="form-group relative w-full">
                        <label for="table_name" class="text-sm font-medium text-gray-700 mb-1 block">Table Name</label>
                        <input type="text" name="table_name" id="table_name" placeholder="Masukkan Nama Produk" class="w-full px-5 py-3 rounded focus:outline-none  @error('table_name') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('table_name') }}">
                        @error('table_name')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="Form group grid grid-cols-5 sm:grid-cols-6 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-12 gap-2">
                        <div class="col-span-5 sm:col-span-6 md:col-span-6 lg:col-span-8 xl:col-span-12">
                            <p class="text-sm font-medium text-gray-700 mb-1 block">Table Color</p>
                        </div>
                        {{-- Color list --}}
                        <div class="col-span-1">
                            <input type="radio" id="table_color_red_500" name="table_color" value="bg-red-500" class="hidden peer" @if(old('table_color') == 'bg-red-500') checked @endif>
                            <label for="table_color_red_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-red-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_red_300" name="table_color" value="bg-red-300" class="hidden peer" @if(old('table_color') == 'bg-red-300') checked @endif>
                            <label for="table_color_red_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-red-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_red_100" name="table_color" value="bg-red-100" class="hidden peer" @if(old('table_color') == 'bg-red-100') checked @endif>
                            <label for="table_color_red_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-red-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_blue_500" name="table_color" value="bg-blue-500" class="hidden peer" @if(old('table_color') == 'bg-blue-500') checked @endif>
                            <label for="table_color_blue_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-blue-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_blue_300" name="table_color" value="bg-blue-300" class="hidden peer" @if(old('table_color') == 'bg-blue-300') checked @endif>
                            <label for="table_color_blue_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-blue-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_blue_100" name="table_color" value="bg-blue-100" class="hidden peer" @if(old('table_color') == 'bg-blue-100') checked @endif>
                            <label for="table_color_blue_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-blue-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_green_500" name="table_color" value="bg-green-500" class="hidden peer" @if(old('table_color') == 'bg-green-500') checked @endif>
                            <label for="table_color_green_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-green-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_green_300" name="table_color" value="bg-green-300" class="hidden peer" @if(old('table_color') == 'bg-green-300') checked @endif>
                            <label for="table_color_green_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-green-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_green_100" name="table_color" value="bg-green-100" class="hidden peer" @if(old('table_color') == 'bg-green-100') checked @endif>
                            <label for="table_color_green_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-green-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_yellow_500" name="table_color" value="bg-yellow-500" class="hidden peer" @if(old('table_color') == 'bg-yellow-500') checked @endif>
                            <label for="table_color_yellow_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-yellow-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_yellow_300" name="table_color" value="bg-yellow-300" class="hidden peer" @if(old('table_color') == 'bg-yellow-300') checked @endif>
                            <label for="table_color_yellow_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-yellow-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_yellow_100" name="table_color" value="bg-yellow-100" class="hidden peer" @if(old('table_color') == 'bg-yellow-100') checked @endif>
                            <label for="table_color_yellow_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-yellow-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_lime_500" name="table_color" value="bg-lime-500" class="hidden peer" @if(old('table_color') == 'bg-lime-500') checked @endif>
                            <label for="table_color_lime_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-lime-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_lime_300" name="table_color" value="bg-lime-300" class="hidden peer" @if(old('table_color') == 'bg-lime-300') checked @endif>
                            <label for="table_color_lime_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-lime-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_lime_100" name="table_color" value="bg-lime-100" class="hidden peer" @if(old('table_color') == 'bg-lime-100') checked @endif>
                            <label for="table_color_lime_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-lime-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_indigo_500" name="table_color" value="bg-indigo-500" class="hidden peer" @if(old('table_color') == 'bg-indigo-500') checked @endif>
                            <label for="table_color_indigo_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-indigo-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_indigo_300" name="table_color" value="bg-indigo-300" class="hidden peer" @if(old('table_color') == 'bg-indigo-300') checked @endif>
                            <label for="table_color_indigo_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-indigo-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_indigo_100" name="table_color" value="bg-indigo-100" class="hidden peer" @if(old('table_color') == 'bg-indigo-100') checked @endif>
                            <label for="table_color_indigo_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-indigo-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_violet_500" name="table_color" value="bg-violet-500" class="hidden peer" @if(old('table_color') == 'bg-violet-500') checked @endif>
                            <label for="table_color_violet_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-violet-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_violet_300" name="table_color" value="bg-violet-300" class="hidden peer" @if(old('table_color') == 'bg-violet-300') checked @endif>
                            <label for="table_color_violet_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-violet-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_violet_100" name="table_color" value="bg-violet-100" class="hidden peer" @if(old('table_color') == 'bg-violet-100') checked @endif>
                            <label for="table_color_violet_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-violet-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_sky_500" name="table_color" value="bg-sky-500" class="hidden peer" @if(old('table_color') == 'bg-sky-500') checked @endif>
                            <label for="table_color_sky_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-sky-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_sky_300" name="table_color" value="bg-sky-300" class="hidden peer" @if(old('table_color') == 'bg-sky-300') checked @endif>
                            <label for="table_color_sky_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-sky-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_sky_100" name="table_color" value="bg-sky-100" class="hidden peer" @if(old('table_color') == 'bg-sky-100') checked @endif>
                            <label for="table_color_sky_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-sky-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_pink_500" name="table_color" value="bg-pink-500" class="hidden peer" @if(old('table_color') == 'bg-pink-500') checked @endif>
                            <label for="table_color_pink_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-pink-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_pink_300" name="table_color" value="bg-pink-300" class="hidden peer" @if(old('table_color') == 'bg-pink-300') checked @endif>
                            <label for="table_color_pink_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-pink-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_pink_100" name="table_color" value="bg-pink-100" class="hidden peer" @if(old('table_color') == 'bg-pink-100') checked @endif>
                            <label for="table_color_pink_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-pink-100"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_amber_500" name="table_color" value="bg-amber-500" class="hidden peer" @if(old('table_color') == 'bg-amber-500') checked @endif>
                            <label for="table_color_amber_500" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-amber-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_amber_300" name="table_color" value="bg-amber-300" class="hidden peer" @if(old('table_color') == 'bg-amber-300') checked @endif>
                            <label for="table_color_amber_300" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-amber-300"></div>
                                </div>
                            </label>
                        </div>
                        <div class="col-span-1">
                            <input type="radio" id="table_color_amber_100" name="table_color" value="bg-amber-100" class="hidden peer" @if(old('table_color') == 'bg-amber-100') checked @endif>
                            <label for="table_color_amber_100" class="inline-flex items-center justify-between w-full p-3 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                                <div class="flex justify-center w-full">
                                    <div class="w-5 h-5 rounded-full bg-amber-100"></div>
                                </div>
                            </label>
                        </div>
                        @error('table_color')
                            <div class="col-span-3 sm:col-span-4 md:col-span-4 lg:col-span-8 xl:col-span-12">
                                <p class="text-danger">{{$message}}</p>
                            </div>
                        @enderror
                    </div>
                    <div class="form-group button-place w-full mt-2">
                        <button type="submit" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto"><i class="fas fa-save"></i> Add Tables</button>
                    </div>
                </form>
            </div>
            <div class="col-span-1">
                <p class="tables-list-title text-base font-medium text-gray-700">Tables List</p>
                <p class="table-description text-sm font-medium text-gray-500 mb-4">Drag to rearrange the table</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-5 gap-2 auto-rows-min" id="sortable-table">
                    @foreach ($tables as $table)
                        <div class="table-list col-span-1 border border-gray-200 rounded-lg p-3 flex flex-wrap relative cursor-move" data-uuid="{{ $table->uuid }}" data-name="{{ $table->name }}" data-color="{{$table->color}}">
                            <div class="icon-place flex items-center justify-center mb-3 w-8 h-8 rounded-full {{ $table->color }}">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>table</title> <path d="M18.76,6l2,4H3.24l2-4H18.76M20,4H4L1,10v2H3v7H5V16H19v3h2V12h2V10L20,4ZM5,14V12H19v2Z"></path> <rect width="24" height="24" fill="none"></rect> </g></svg>
                            </div>
                            <div class="table-detail-place w-full">
                                <p class="table-name text-lg font-bold text-gray-700 mb-3">{{ $table->name }}</p>
                                
                            </div>
                            <!-- QR Code Button -->
                            <button type="button" class="btn-show-qr w-7 h-7 bg-brand-soft text-brand hover:bg-brand hover:text-white rounded-lg flex items-center justify-center cursor-pointer border-none outline-none absolute top-2 right-10" data-uuid="{{ $table->uuid }}" data-name="{{ $table->name }}" title="Tampilkan QR Code">
                                <i class="fas fa-qrcode text-xs"></i>
                            </button>
                            <button class="px-2 py-1 rounded-full text-gray-700 cursor-pointer absolute top-2 right-2 delete-table">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    @endforeach
                    
                </div>
                <div class="flex flex-col sm:flex-row gap-2 mt-3 w-full">
                    <button type="button" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base sort-table"><i class="fas fa-sort"></i> Sort Table</button>
                    <button type="button" id="btn-print-all-qr" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium py-2 px-4 cursor-pointer rounded-base"><i class="fas fa-print"></i> Cetak Semua QR Code</button>
                </div>
        </div>
        
        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2 border-b border-gray-200 pb-3">
                <p class="text-lg font-bold">Setting Urutan Kategori</p>
                <p class="text-sm">Drag to rearrange category order for display and receipt printing</p>
            </div>
            
            <div class="col-span-1 md:col-span-2">
                <p class="categories-list-title text-base font-medium text-gray-700">Categories List</p>
                <p class="category-description text-sm font-medium text-gray-500 mb-4">Drag to rearrange the categories</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 auto-rows-min" id="sortable-category">
                    @foreach ($categories as $category)
                        <div class="category-list col-span-1 border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center relative cursor-move hover:shadow-md transition-shadow" data-uuid="{{ $category->uuid }}" data-name="{{ $category->nama }}" data-color="{{ $category->color }}" data-icon="{{ $category->icon }}">
                            <div class="icon-place flex items-center justify-center mb-3 w-12 h-12 rounded-2xl text-white {{ $category->color }}">
                                <i class="fas {{ $category->icon }} text-lg"></i>
                            </div>
                            <div class="category-detail-place w-full text-center">
                                <p class="category-name text-sm font-bold text-gray-700">{{ $category->nama }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto mt-4 sort-category"><i class="fas fa-sort"></i> Urutkan Kategori</button>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2 border-b border-gray-200 pb-3">
                <p class="text-lg font-bold">Setting Payment</p>
                <p class="text-sm">Payments Configuration on the system</p>
            </div>
            @if(session('success_tax'))
            <div class="col-span-1 md:col-span-2 flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success_tax')}}</p>
            </div>
            @endif
            <div class="col-span-1">
                @php
                    $settingtax = $settings->first(function($item) {
                        return $item->jenis == 'payment_tax';
                    });
                @endphp
                <form method="POST" action="{{ route('settings.payment.tax.update') }}">
                    @csrf
                    <p class="text-base mb-1">Payment Tax (% Percentage)</p>
                    <input type="number" name="tax" class="w-full px-5 py-3 rounded focus:outline-none  @error('tax') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('tax',$settingtax ? $settingtax->nilai : "") }}" id="payment_tax" placeholder="Input tax percentage">
                    @error('tax')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto mt-2"><i class="fas fa-save"></i> Update Tax</button>
                    
                </form>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2 border-b border-gray-200 pb-3">
                <p class="text-lg font-bold">Setting Absensi Karyawan</p>
                <p class="text-sm">Batas Toleransi Waktu Absen Masuk Staf</p>
            </div>
            @if(session('success_late_time'))
            <div class="col-span-1 md:col-span-2 flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success_late_time')}}</p>
            </div>
            @endif
            <div class="col-span-1">
                @php
                    $settingLate = $settings->first(function($item) {
                        return $item->jenis == 'attendance_late_time';
                    });
                @endphp
                <form method="POST" action="{{ route('settings.attendance.late.update') }}">
                    @csrf
                    <p class="text-base mb-1">Jam Batas Masuk (Format Jam:Menit, e.g. 08:00)</p>
                    <input type="time" name="late_time" class="w-full px-5 py-3 rounded focus:outline-none focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default" value="{{ old('late_time', $settingLate ? $settingLate->nilai : '08:00') }}" id="attendance_late_time">
                    @error('late_time')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto mt-2"><i class="fas fa-save"></i> Update Jam Batas</button>
                </form>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2 border-b border-gray-200 pb-3">
                <p class="text-lg font-bold">Notifikasi Laporan Pendapatan Harian</p>
                <p class="text-sm">Kirim notifikasi total pendapatan hari ini ke HP admin pada jam tertentu</p>
            </div>
            @if(session('success_daily_revenue'))
            <div class="col-span-1 md:col-span-2 flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success_daily_revenue')}}</p>
            </div>
            @endif
            <div class="col-span-1">
                @php
                    $settingDailyRevenue = $settings->first(function($item) {
                        return $item->jenis == 'daily_revenue_notification';
                    });
                    $dailyRevenueData = $settingDailyRevenue && $settingDailyRevenue->nilai ? (@unserialize($settingDailyRevenue->nilai) ?: []) : [];
                @endphp
                <form method="POST" action="{{ route('settings.notification.daily-revenue.update') }}">
                    @csrf
                    <label class="flex items-center gap-2 cursor-pointer select-none mb-3">
                        <input type="checkbox" name="enabled" value="1" class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand" {{ old('enabled', $dailyRevenueData['enabled'] ?? false) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Aktifkan notifikasi</span>
                    </label>

                    <p class="text-base mb-1">Jam Kirim Notifikasi</p>
                    <input type="time" name="time" class="w-full px-5 py-3 rounded focus:outline-none @error('time') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('time', $dailyRevenueData['time'] ?? '21:00') }}" id="daily_revenue_time">
                    @error('time')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto mt-3"><i class="fas fa-save"></i> Simpan Pengaturan</button>
                </form>
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 md:col-span-2 border-b border-gray-200 pb-3">
                <p class="text-lg font-bold">Aplikasi Android (APK)</p>
                <p class="text-sm">Unggah file APK agar bisa diunduh pengguna langsung dari halaman login</p>
            </div>
            @if(session('success_apk'))
            <div class="col-span-1 md:col-span-2 flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                <i class="me-2 mt-0.5 sm:mt-0 fas fa-check"></i>
                <p><span class="font-medium me-1">Sukses!</span> {{session('success_apk')}}</p>
            </div>
            @endif
            <div class="col-span-1 md:col-span-2">
                @php
                    $settingApk = $settings->first(function($item) {
                        return $item->jenis == 'app_apk';
                    });
                    $apkData = $settingApk && $settingApk->nilai ? (@unserialize($settingApk->nilai) ?: []) : [];
                @endphp

                @if(!empty($apkData['filename']))
                    <div class="flex items-center justify-between gap-4 p-4 border border-gray-200 rounded-lg mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-lg bg-brand-soft flex items-center justify-center shrink-0">
                                <i class="fab fa-android text-brand text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate">{{ $apkData['original_name'] ?? 'app.apk' }}</p>
                                <p class="text-xs text-gray-500">
                                    @if(!empty($apkData['version'])) Versi {{ $apkData['version'] }} @endif
                                    @if(!empty($apkData['version_code'])) (code {{ $apkData['version_code'] }}) @endif
                                    &middot; {{ isset($apkData['size']) ? number_format($apkData['size'] / 1048576, 1) . ' MB' : '' }}
                                    @if(!empty($apkData['uploaded_at'])) &middot; Diunggah {{ \Carbon\Carbon::parse($apkData['uploaded_at'])->format('d M Y H:i') }} @endif
                                </p>
                                @if(empty($apkData['version_code']))
                                    <p class="text-[11px] text-amber-600 mt-0.5"><i class="fas fa-triangle-exclamation"></i> Version Code belum diisi — fitur cek update otomatis di app tidak akan mendeteksi APK ini.</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('app.download') }}" class="bg-brand-light hover:bg-brand-strong text-white text-sm font-medium py-2 px-4 rounded-base"><i class="fas fa-download"></i> Unduh</a>
                            <form method="POST" action="{{ route('settings.apk.delete') }}" onsubmit="return confirm('Hapus APK yang sedang aktif? Pengguna tidak akan bisa mengunduh aplikasi sampai Anda unggah yang baru.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-danger-soft hover:bg-danger-medium text-fg-danger-strong text-sm font-medium py-2 px-4 rounded-base"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('settings.apk.upload') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div class="form-group flex-col w-full">
                        <label for="apk_file" class="text-sm font-medium text-gray-700 mb-1 block">File APK</label>
                        <input type="file" name="apk_file" id="apk_file" accept=".apk" class="w-full px-5 py-3 rounded focus:outline-none @error('apk_file') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" />
                        @error('apk_file')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group flex-col w-full">
                        <label for="version" class="text-sm font-medium text-gray-700 mb-1 block">Versi (opsional, contoh: 1.0.0)</label>
                        <input type="text" name="version" id="version" placeholder="1.0.0" class="w-full px-5 py-3 rounded focus:outline-none focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default" value="{{ old('version') }}" />
                    </div>
                    <div class="form-group flex-col w-full">
                        <label for="version_code" class="text-sm font-medium text-gray-700 mb-1 block">Version Code (wajib untuk cek update otomatis)</label>
                        <input type="number" name="version_code" id="version_code" min="1" placeholder="Samakan dengan versionCode di build.gradle.kts" class="w-full px-5 py-3 rounded focus:outline-none @error('version_code') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('version_code') }}" />
                        @error('version_code')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <button type="submit" class="w-full sm:w-auto bg-brand text-white py-2 px-4 rounded hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand focus:ring-opacity-50"><i class="fas fa-upload"></i> Unggah Aplikasi</button>
                    </div>
                </form>
            </div>
        </div>


        {{-- Modal Place --}}
        <!-- Modal QR Code Meja -->
        <div id="modal-table-qr" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full bg-black/60 backdrop-blur-[2px] flex">
            <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100 border border-gray-100 flex flex-col p-6 m-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                    <h3 class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">QR Code Meja</h3>
                    <button type="button" class="close-qr-modal text-gray-400 hover:text-gray-600 text-sm w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer border-none outline-none bg-transparent">
                        <i class="fas fa-xmark text-sm"></i>
                    </button>
                </div>
                
                <div class="text-center py-4">
                    <h4 id="qr-table-title" class="font-extrabold text-gray-800 text-lg mb-4">Meja</h4>
                    
                    <!-- QR Code Display -->
                    <div class="w-48 h-48 bg-white border border-gray-200 rounded-3xl mx-auto flex items-center justify-center p-3 shadow-md mb-4 overflow-hidden">
                        <img id="qr-image" src="" class="w-full h-full object-contain" alt="QR Code Meja" />
                    </div>
                    
                    <p class="text-[11px] text-gray-400 font-semibold px-4 mb-6 leading-relaxed">
                        Pengunjung dapat memesan menu langsung dengan men-scan kode QR di atas menggunakan kamera smartphone.
                    </p>
                    
                    <!-- Print Button -->
                    <button type="button" id="btn-print-qr" class="w-full bg-brand hover:bg-brand-strong text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-brand/20 transition-all flex items-center justify-center gap-2 cursor-pointer border-none outline-none">
                        <i class="fas fa-print"></i> Cetak QR Code Meja
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        //Table Configuration
        var el = document.getElementById('sortable-table');
        Sortable.create(el,{
            animation: 150,
        });

        // Color presets listener
        $('.btn-color-preset').click(function() {
            const color = $(this).data('color');
            $('#restaurant_accent_color').val(color);
        });

        // Print All QR Codes Logic
        $('#btn-print-all-qr').on('click', function() {
            let tablesData = [];
            $('.table-list').each(function() {
                tablesData.push({
                    uuid: $(this).data('uuid'),
                    name: $(this).data('name')
                });
            });

            if (tablesData.length === 0) {
                oAlert("red", "Error", "Tidak ada meja untuk dicetak.");
                return;
            }

            const printWindow = window.open('', '_blank');
            let cardsHtml = '';
            
            tablesData.forEach(function(tb) {
                let orderUrl = "{{ route('customer.order.table', ':uuid') }}";
                orderUrl = orderUrl.replace(':uuid', tb.uuid);
                const qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' + encodeURIComponent(orderUrl);
                
                const logoPath = '{{ $settingrestaurantlogo && $settingrestaurantlogo->nilai ? asset("storage/".$settingrestaurantlogo->nilai) : "" }}';
                const logoHtml = logoPath ? `<img src="${logoPath}" class="res-logo" alt="Logo">` : '';
                
                cardsHtml += `
                    <div class="qr-card">
                        <div class="card-header">
                            ${logoHtml}
                            <h2>{{ $settingResArray && isset($settingResArray['name']) ? $settingResArray['name'] : 'Restaurant' }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-badge">${tb.name}</div>
                            <div class="qr-box">
                                <img src="${qrApiUrl}" alt="QR Code">
                            </div>
                            <div class="instructions">
                                <p class="scan-title"><i class="fas fa-camera"></i> PINDAI UNTUK MEMESAN</p>
                                <p class="scan-sub">Pesan menu favorit Anda langsung dari meja secara mandiri</p>
                            </div>
                        </div>
                        <div class="card-footer">${window.location.host}</div>
                    </div>
                `;
            });

            printWindow.document.write(`
                <html>
                <head>
                    <title>Cetak Semua QR Code Meja</title>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
                    <style>
                        @page {
                            size: A4;
                            margin: 12mm;
                        }
                        body {
                            font-family: 'Red Hat Text', 'Segoe UI', sans-serif;
                            margin: 0;
                            padding: 0;
                            background: white;
                            color: #333;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .grid-container {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 25px;
                        }
                        .qr-card {
                            border: 2px solid {{ $settingResArray['accent_color'] ?? '#2b66ff' }};
                            border-radius: 24px;
                            overflow: hidden;
                            background: #ffffff;
                            box-sizing: border-box;
                            page-break-inside: avoid;
                            display: flex;
                            flex-direction: column;
                            height: 400px;
                            position: relative;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
                        }
                        .card-header {
                            background-color: {{ $settingResArray['accent_color'] ?? '#2b66ff' }};
                            color: #ffffff;
                            padding: 14px 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 10px;
                            border-bottom: 1px solid rgba(0,0,0,0.05);
                        }
                        .res-logo {
                            width: 26px;
                            height: 26px;
                            object-fit: contain;
                            border-radius: 50%;
                            background: #ffffff;
                            padding: 2px;
                            border: 1px solid rgba(255,255,255,0.3);
                        }
                        .card-header h2 {
                            font-size: 13px;
                            margin: 0;
                            color: #ffffff;
                            text-transform: uppercase;
                            font-weight: 800;
                            letter-spacing: 1px;
                        }
                        .card-body {
                            padding: 16px 20px;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            flex-grow: 1;
                        }
                        .table-badge {
                            font-size: 26px;
                            color: {{ $settingResArray['accent_color'] ?? '#2b66ff' }};
                            font-weight: 900;
                            margin-bottom: 12px;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }
                        .qr-box {
                            width: 170px;
                            height: 170px;
                            padding: 8px;
                            border: 2px solid color-mix(in srgb, {{ $settingResArray['accent_color'] ?? '#2b66ff' }} 15%, transparent);
                            border-radius: 20px;
                            background: #ffffff;
                            margin-bottom: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .qr-box img {
                            width: 100%;
                            height: 100%;
                            object-fit: contain;
                        }
                        .instructions {
                            text-align: center;
                        }
                        .scan-title {
                            font-size: 10px;
                            color: #1f2937;
                            margin: 0 0 3px 0;
                            font-weight: 800;
                            letter-spacing: 0.5px;
                            text-transform: uppercase;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 4px;
                        }
                        .scan-sub {
                            font-size: 8.5px;
                            color: #6b7280;
                            margin: 0;
                            font-weight: 600;
                            line-height: 1.3;
                            max-w: 220px;
                        }
                        .card-footer {
                            background: #f9fafb;
                            border-top: 1px solid #f3f4f6;
                            padding: 8px;
                            font-size: 9px;
                            color: #9ca3af;
                            font-weight: 700;
                            text-align: center;
                            letter-spacing: 0.5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="grid-container">
                        ${cardsHtml}
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });

        //Category Configuration
        var elCat = document.getElementById('sortable-category');
        if (elCat) {
            Sortable.create(elCat,{
                animation: 150,
            });
        }
        $('.sort-category').click(function() {
            loading();
            var url = "{{route('settings.category.sort')}}";
            var urutan_array = [];
            var i = 1;
            $('.category-list').each(function() {
                urutan_array.push({
                    "sort": i,
                    "uuid" : $(this).data('uuid'),
                    "color" : $(this).data('color'),
                    "nama" : $(this).data('name'),
                    "icon" : $(this).data('icon'),
                });
                i++;
            });
            
            $.ajax({
                type: "post",
                url: url,
                headers: {
                    "X-CSRF-TOKEN": "{{csrf_token()}}"
                },
                data: {
                    "urutan" : urutan_array
                },
                success: function(data) {
                    if(data.success === true) {
                        removeLoading();
                        oAlert("green","Success",data.message);
                    }
                },
                error: function(data) {
                    removeLoading();
                    var errors = data.responseJSON;
                    oAlert("red","Error", errors ? errors.message : "Gagal mengurutkan kategori.");
                }
            })
        });
        $('.sort-table').click(function() {
            loading();
            var url = "{{route('settings.table.sort')}}";
            var urutan_array = [];
            var i = 1;
            $('.table-list').each(function() {
                urutan_array.push({
                    "sort": i,
                    "uuid" : $(this).data('uuid'),
                    "color" : $(this).data('color'),
                    "name" : $(this).data('name'),
                });
                i++;
            });
            
            $.ajax({
                type: "post",
                url: url,
                headers: {
                    "X-CSRF-TOKEN": "{{csrf_token()}}"
                },
                data: {
                    "urutan" : urutan_array
                },
                success: function(data) {
                    if(data.success === true) {
                        removeLoading();
                        oAlert("green","Success",data.message);
                    }
                },
                error: function(data) {
                    var errors = data.responseJSON;
                    console.log(errors.message);
                }
            })
        });
        $('.delete-table').click(function() {
            var uuid = $(this).closest('.table-list').data('uuid');
            var url = "{{route('settings.table.delete',':id')}}";
            url = url.replace(':id',uuid);

            cConfirm("Warning","Confirm to delete the table",function() {
                loading();
                $.ajax({
                    type: "DELETE",
                    url : url,
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    success: function(data) {
                        if(data.success === true) {
                            removeLoading(),
                            cAlert("green","Success","Successfully Deleted Tables",true);
                        }
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                })
            })
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image-preview').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#picture').change(function() {
            var file = this.files[0];
            var filename = file.name;
            var filesize = file.size;
            const maxSizeInBytes = 2097152;
            var ext = file.name.split('.').pop().toLowerCase();
            var allow_ext = ['jpg','jpeg','png'];

            if($.inArray(ext,allow_ext) == -1) {
                oAlert("red","Warning","File Must Be JPG, JPEG and PNG");
                return false;
            }
            if(filesize > maxSizeInBytes) {
                oAlert("red","Warning","File size must below 2MB");
                return false;
            }


            readURL(this);

            $(this).closest('.uploaded-place').addClass('hidden');
            $('.preview-place').removeClass('hidden');
        });

        $('.preview-place').on('click','.delete-image',function(){
            $('#is_changed').val('1');
            $('#image-preview').attr('src','');
            $('#picture').val("");
            $('#picture').closest('.uploaded-place').removeClass('hidden');
            $('.preview-place').addClass('hidden');
        });

        // QR Code Meja Modal & Print Logic
        let qrModal = null;
        const qrModalEl = document.getElementById('modal-table-qr');
        if (qrModalEl && window.Modal) {
            qrModal = new window.Modal(qrModalEl, {
                placement: 'center',
                backdrop: 'dynamic',
                backdropClasses: 'bg-gray-900/60 backdrop-blur-sm fixed inset-0 z-40',
                closable: true
            });
        }

        let selectedTableUuid = '';
        let selectedTableName = '';

        $(document).on('click', '.btn-show-qr', function() {
            selectedTableUuid = $(this).data('uuid');
            selectedTableName = $(this).data('name');

            $('#qr-table-title').text(selectedTableName);

            // Generate full visitor self-order URL
            let orderUrl = "{{ route('customer.order.table', ':uuid') }}";
            orderUrl = orderUrl.replace(':uuid', selectedTableUuid);

            // Build dynamic QR Server API URL
            const qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(orderUrl);
            $('#qr-image').attr('src', qrApiUrl);

            if (qrModal) qrModal.show();
        });

        $(document).on('click', '.close-qr-modal, #modal-table-qr', function(e) {
            if (e.target === this || $(e.target).closest('.close-qr-modal').length) {
                if (qrModal) qrModal.hide();
            }
        });

        $('#btn-print-qr').on('click', function() {
            if (!selectedTableUuid) return;

            let orderUrl = "{{ route('customer.order.table', ':uuid') }}";
            orderUrl = orderUrl.replace(':uuid', selectedTableUuid);

            const qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' + encodeURIComponent(orderUrl);

            const logoPath = '{{ $settingrestaurantlogo && $settingrestaurantlogo->nilai ? asset("storage/".$settingrestaurantlogo->nilai) : "" }}';
            const logoHtml = logoPath ? `<img src="${logoPath}" class="res-logo" alt="Logo">` : '';

            // Open clean print page
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Cetak QR Code ${selectedTableName}</title>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
                    <style>
                        @page {
                            size: A4;
                            margin: 20mm;
                        }
                        body {
                            font-family: 'Red Hat Text', 'Segoe UI', sans-serif;
                            margin: 0;
                            padding: 0;
                            background: white;
                            color: #333;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            min-height: 80vh;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .qr-card {
                            border: 2px solid {{ $settingResArray['accent_color'] ?? '#2b66ff' }};
                            border-radius: 24px;
                            overflow: hidden;
                            background: #ffffff;
                            box-sizing: border-box;
                            display: flex;
                            flex-direction: column;
                            width: 320px;
                            height: 420px;
                            position: relative;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
                        }
                        .card-header {
                            background-color: {{ $settingResArray['accent_color'] ?? '#2b66ff' }};
                            color: #ffffff;
                            padding: 14px 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 10px;
                            border-bottom: 1px solid rgba(0,0,0,0.05);
                        }
                        .res-logo {
                            width: 26px;
                            height: 26px;
                            object-fit: contain;
                            border-radius: 50%;
                            background: #ffffff;
                            padding: 2px;
                            border: 1px solid rgba(255,255,255,0.3);
                        }
                        .card-header h2 {
                            font-size: 13px;
                            margin: 0;
                            color: #ffffff;
                            text-transform: uppercase;
                            font-weight: 800;
                            letter-spacing: 1px;
                        }
                        .card-body {
                            padding: 18px 20px;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            flex-grow: 1;
                        }
                        .table-badge {
                            font-size: 28px;
                            color: {{ $settingResArray['accent_color'] ?? '#2b66ff' }};
                            font-weight: 900;
                            margin-bottom: 14px;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }
                        .qr-box {
                            width: 180px;
                            height: 180px;
                            padding: 8px;
                            border: 2px solid color-mix(in srgb, {{ $settingResArray['accent_color'] ?? '#2b66ff' }} 15%, transparent);
                            border-radius: 20px;
                            background: #ffffff;
                            margin-bottom: 14px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .qr-box img {
                            width: 100%;
                            height: 100%;
                            object-fit: contain;
                        }
                        .instructions {
                            text-align: center;
                        }
                        .scan-title {
                            font-size: 10.5px;
                            color: #1f2937;
                            margin: 0 0 3px 0;
                            font-weight: 800;
                            letter-spacing: 0.5px;
                            text-transform: uppercase;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 4px;
                        }
                        .scan-sub {
                            font-size: 9px;
                            color: #6b7280;
                            margin: 0;
                            font-weight: 600;
                            line-height: 1.3;
                            max-width: 240px;
                        }
                        .card-footer {
                            background: #f9fafb;
                            border-top: 1px solid #f3f4f6;
                            padding: 8px;
                            font-size: 9.5px;
                            color: #9ca3af;
                            font-weight: 700;
                            text-align: center;
                            letter-spacing: 0.5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="qr-card">
                        <div class="card-header">
                            ${logoHtml}
                            <h2>{{ $settingResArray && isset($settingResArray['name']) ? $settingResArray['name'] : 'Restaurant' }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-badge">${selectedTableName}</div>
                            <div class="qr-box">
                                <img src="${qrApiUrl}" alt="QR Code">
                            </div>
                            <div class="instructions">
                                <p class="scan-title"><i class="fas fa-camera"></i> PINDAI UNTUK MEMESAN</p>
                                <p class="scan-sub">Pesan menu favorit Anda langsung dari meja secara mandiri</p>
                            </div>
                        </div>
                        <div class="card-footer">${window.location.host}</div>
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    </script>
@endsection