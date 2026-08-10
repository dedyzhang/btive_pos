@extends('layout.index')

@section('title','Categories')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">CATEGORIES <span class="text-base text-gray-400">> Add Categories</span></h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    
    <form action="{{route('categories.store')}}" method="POST" class="p-6">
        @csrf
        <div class="container-place w-full sm:w-[80%] grid grid-cols-1 gap-2 bg-white rounded-lg p-6">
            <div class="col-span-1">
                <label for="name" class="text-sm font-medium text-gray-700 mb-1 block">Category Name</label>
                <input type="text" name="name" id="name" placeholder="Category Name" class="w-full px-5 py-3 rounded focus:outline-none  @error('name') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('name') }}">
                @error('name')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-8 xl:grid-cols-12 gap-2">
                <div class="col-span-3 sm:col-span-4 md:col-span-4 lg:col-span-8 xl:col-span-12">
                    <p>Categories Icon</p>
                </div>
                {{-- icon list --}}
                <div class="col-span-1">
                    <input type="radio" id="icon-apple-whole" name="icon" value="fa-apple-whole" class="hidden peer" @if(old('icon') == 'fa-apple-whole') checked @endif>
                    <label for="icon-apple-whole" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block">
                            <i class="fas fa-apple-whole fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-bread-slice" name="icon" value="fa-bread-slice" class="hidden peer" @if(old('icon') == 'fa-bread-slice') checked @endif>
                    <label for="icon-bread-slice" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-bread-slice fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-burger" name="icon" value="fa-burger" class="hidden peer" @if(old('icon') == 'fa-burger') checked @endif>
                    <label for="icon-burger" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-burger fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-seedling" name="icon" value="fa-seedling" class="hidden peer" @if(old('icon') == 'fa-seedling') checked @endif>
                    <label for="icon-seedling" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-seedling fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-blender" name="icon" value="fa-blender" class="hidden peer" @if(old('icon') == 'fa-blender') checked @endif>
                    <label for="icon-blender" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-blender fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-beer-mug-empty" name="icon" value="fa-beer-mug-empty" class="hidden peer" @if(old('icon') == 'fa-beer-mug-empty') checked @endif>
                    <label for="icon-beer-mug-empty" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-beer-mug-empty fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-bottle-water" name="icon" value="fa-bottle-water" class="hidden peer" @if(old('icon') == 'fa-bottle-water') checked @endif>
                    <label for="icon-bottle-water" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-bottle-water fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-bowl-rice" name="icon" value="fa-bowl-rice" class="hidden peer" @if(old('icon') == 'fa-bowl-rice') checked @endif>
                    <label for="icon-bowl-rice" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-bowl-rice fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-cake-candles" name="icon" value="fa-cake-candles" class="hidden peer" @if(old('icon') == 'fa-cake-candles') checked @endif>
                    <label for="icon-cake-candles" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-cake-candles fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-cheese" name="icon" value="fa-cheese" class="hidden peer" @if(old('icon') == 'fa-cheese') checked @endif>
                    <label for="icon-cheese" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-cheese fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-cookie" name="icon" value="fa-cookie" class="hidden peer" @if(old('icon') == 'fa-cookie') checked @endif>
                    <label for="icon-cookie" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-cookie fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-ice-cream" name="icon" value="fa-ice-cream" class="hidden peer" @if(old('icon') == 'fa-ice-cream') checked @endif>
                    <label for="icon-ice-cream" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-ice-cream fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-mug-hot" name="icon" value="fa-mug-hot" class="hidden peer" @if(old('icon') == 'fa-mug-hot') checked @endif>
                    <label for="icon-mug-hot" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-mug-hot fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-pizza-slice" name="icon" value="fa-pizza-slice" class="hidden peer" @if(old('icon') == 'fa-pizza-slice') checked @endif>
                    <label for="icon-pizza-slice" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-pizza-slice fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-lemon" name="icon" value="fa-lemon" class="hidden peer" @if(old('icon') == 'fa-lemon') checked @endif>
                    <label for="icon-lemon" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-lemon fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-wine-glass" name="icon" value="fa-wine-glass" class="hidden peer" @if(old('icon') == 'fa-wine-glass') checked @endif>
                    <label for="icon-wine-glass" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-wine-glass fa-3x"></i>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="icon-bowl-food" name="icon" value="fa-bowl-food" class="hidden peer" @if(old('icon') == 'fa-bowl-food') checked @endif>
                    <label for="icon-bowl-food" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border-1 border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="block ">
                            <i class="fas fa-bowl-food fa-3x"></i>
                        </div>
                    </label>
                </div>
                @error('icon')
                    <div class="col-span-3 sm:col-span-4 md:col-span-4 lg:col-span-8 xl:col-span-12">
                        <p class="text-danger">{{$message}}</p>
                    </div>
                @enderror
            </div>
            <div class="col-span-1 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-8 xl:grid-cols-12 gap-2">
                <div class="col-span-3 sm:col-span-4 md:col-span-4 lg:col-span-8 xl:col-span-12">
                    <p>Color</p>
                </div>
                {{-- Color list --}}
                <div class="col-span-1">
                    <input type="radio" id="color-red-500" name="color" value="bg-red-500" class="hidden peer" @if(old('color') == 'bg-red-500') checked @endif>
                    <label for="color-red-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-red-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-red-300" name="color" value="bg-red-300" class="hidden peer" @if(old('color') == 'bg-red-300') checked @endif>
                    <label for="color-red-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-red-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-red-100" name="color" value="bg-red-100" class="hidden peer" @if(old('color') == 'bg-red-100') checked @endif>
                    <label for="color-red-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-red-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-blue-500" name="color" value="bg-blue-500" class="hidden peer" @if(old('color') == 'bg-blue-500') checked @endif>
                    <label for="color-blue-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-blue-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-blue-300" name="color" value="bg-blue-300" class="hidden peer" @if(old('color') == 'bg-blue-300') checked @endif>
                    <label for="color-blue-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-blue-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-blue-100" name="color" value="bg-blue-100" class="hidden peer" @if(old('color') == 'bg-blue-100') checked @endif>
                    <label for="color-blue-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-blue-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-green-500" name="color" value="bg-green-500" class="hidden peer" @if(old('color') == 'bg-green-500') checked @endif>
                    <label for="color-green-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-green-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-green-300" name="color" value="bg-green-300" class="hidden peer" @if(old('color') == 'bg-green-300') checked @endif>
                    <label for="color-green-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-green-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-green-100" name="color" value="bg-green-100" class="hidden peer" @if(old('color') == 'bg-green-100') checked @endif>
                    <label for="color-green-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-green-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-yellow-500" name="color" value="bg-yellow-500" class="hidden peer" @if(old('color') == 'bg-yellow-500') checked @endif>
                    <label for="color-yellow-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-yellow-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-yellow-300" name="color" value="bg-yellow-300" class="hidden peer" @if(old('color') == 'bg-yellow-300') checked @endif>
                    <label for="color-yellow-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-yellow-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-yellow-100" name="color" value="bg-yellow-100" class="hidden peer" @if(old('color') == 'bg-yellow-100') checked @endif>
                    <label for="color-yellow-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-yellow-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-lime-500" name="color" value="bg-lime-500" class="hidden peer" @if(old('color') == 'bg-lime-500') checked @endif>
                    <label for="color-lime-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-lime-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-lime-300" name="color" value="bg-lime-300" class="hidden peer" @if(old('color') == 'bg-lime-300') checked @endif>
                    <label for="color-lime-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-lime-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-lime-100" name="color" value="bg-lime-100" class="hidden peer" @if(old('color') == 'bg-lime-100') checked @endif>
                    <label for="color-lime-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-lime-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-indigo-500" name="color" value="bg-indigo-500" class="hidden peer" @if(old('color') == 'bg-indigo-500') checked @endif>
                    <label for="color-indigo-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-indigo-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-indigo-300" name="color" value="bg-indigo-300" class="hidden peer" @if(old('color') == 'bg-indigo-300') checked @endif>
                    <label for="color-indigo-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-indigo-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-indigo-100" name="color" value="bg-indigo-100" class="hidden peer" @if(old('color') == 'bg-indigo-100') checked @endif>
                    <label for="color-indigo-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-indigo-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-violet-500" name="color" value="bg-violet-500" class="hidden peer" @if(old('color') == 'bg-violet-500') checked @endif>
                    <label for="color-violet-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-violet-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-violet-300" name="color" value="bg-violet-300" class="hidden peer" @if(old('color') == 'bg-violet-300') checked @endif>
                    <label for="color-violet-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-violet-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-violet-100" name="color" value="bg-violet-100" class="hidden peer" @if(old('color') == 'bg-violet-100') checked @endif>
                    <label for="color-violet-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-violet-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-sky-500" name="color" value="bg-sky-500" class="hidden peer" @if(old('color') == 'bg-sky-500') checked @endif>
                    <label for="color-sky-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-sky-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-sky-300" name="color" value="bg-sky-300" class="hidden peer" @if(old('color') == 'bg-sky-300') checked @endif>
                    <label for="color-sky-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-sky-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-sky-100" name="color" value="bg-sky-100" class="hidden peer" @if(old('color') == 'bg-sky-100') checked @endif>
                    <label for="color-sky-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-sky-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-pink-500" name="color" value="bg-pink-500" class="hidden peer" @if(old('color') == 'bg-pink-500') checked @endif>
                    <label for="color-pink-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-pink-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-pink-300" name="color" value="bg-pink-300" class="hidden peer" @if(old('color') == 'bg-pink-300') checked @endif>
                    <label for="color-pink-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-pink-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-pink-100" name="color" value="bg-pink-100" class="hidden peer" @if(old('color') == 'bg-pink-100') checked @endif>
                    <label for="color-pink-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-pink-100"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-amber-500" name="color" value="bg-amber-500" class="hidden peer" @if(old('color') == 'bg-amber-500') checked @endif>
                    <label for="color-amber-500" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-amber-500"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-amber-300" name="color" value="bg-amber-300" class="hidden peer" @if(old('color') == 'bg-amber-300') checked @endif>
                    <label for="color-amber-300" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-amber-300"></div>
                        </div>
                    </label>
                </div>
                <div class="col-span-1">
                    <input type="radio" id="color-amber-100" name="color" value="bg-amber-100" class="hidden peer" @if(old('color') == 'bg-amber-100') checked @endif>
                    <label for="color-amber-100" class="inline-flex items-center justify-between w-full p-5 text-body bg-neutral-primary-soft border border-default rounded-base cursor-pointer peer-checked:hover:bg-brand-softer peer-checked:border-brand-subtle peer-checked:bg-brand-softer hover:bg-neutral-secondary-medium peer-checked:text-fg-brand-strong">                           
                        <div class="flex justify-center w-full">
                            <div class="w-10 h-10 rounded-full bg-amber-100"></div>
                        </div>
                    </label>
                </div>
                @error('color')
                    <div class="col-span-3 sm:col-span-4 md:col-span-4 lg:col-span-8 xl:col-span-12">
                        <p class="text-danger">{{$message}}</p>
                    </div>
                @enderror
            </div>
            <div class="col-span-1 mt-5">
                <button type="submit" class="w-full bg-brand-light hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto"><i class="fas fa-add"></i> Create Categories</button>
            </div>
        </div>
        
    </form>
@endsection