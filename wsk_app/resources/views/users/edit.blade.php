@extends('layout.index')

@section('title','Categories')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">USERS <span class="text-base text-gray-400">> Edit User</span></h1>
        <div class="date-place hidden md:inline-flex px-2 py-2 pe-4 bg-white rounded-full shadow items-center gap-3">
            <div class="menu-icon rounded-full h-12 w-12 flex items-center justify-center bg-gray-100"><i class="fas fa-calendar-days text-lg text-blue-400"></i></div>
            <span class="text-gray-600 font-medium">{{ date('D, d M Y') }}</span>
        </div>
    </div>
@endsection

@section('container')
    
    <form action="{{route('users.update',$user->uuid)}}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <div class="container-place w-full sm:w-[80%] grid grid-cols-1 gap-4 bg-white rounded-lg p-6">
            <div class="col-span-1">
                <label for="name" class="text-sm font-medium text-gray-700 mb-1 block">User Name</label>
                <input type="text" name="name" id="name" placeholder="User Name" class="w-full px-5 py-3 rounded focus:outline-none  @error('name') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('name', $user->name) }}">
                @error('name')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="role" class="text-sm font-medium text-gray-700 mb-1 block">Role</label>
                <select name="role" id="role" class="w-full px-5 py-3 rounded focus:outline-none  @error('role') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror">
                    <option value="">Select Role</option>
                    <option value="admin" {{ old('role',$user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="cashier" {{ old('role',$user->role) == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="dapur" {{ old('role',$user->role) == 'dapur' ? 'selected' : '' }}>Dapur</option>
                    @foreach(\App\Models\Role::all() as $customRole)
                        @if(!in_array($customRole->name, ['admin', 'cashier', 'dapur']))
                            <option value="{{ $customRole->name }}" {{ old('role',$user->role) == $customRole->name ? 'selected' : '' }}>{{ ucfirst($customRole->name) }}</option>
                        @endif
                    @endforeach
                </select>
                @error('role')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="email" class="text-sm font-medium text-gray-700 mb-1 block">Email</label>
                <input type="email" name="email" id="email" placeholder="Email Address" class="w-full px-5 py-3 rounded focus:outline-none  @error('email') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('email', $user->email) }}">
                @error('email')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="phone_number" class="text-sm font-medium text-gray-700 mb-1 block">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" class="w-full px-5 py-3 rounded focus:outline-none  @error('phone_number') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror" value="{{ old('phone_number', $user->phone_number) }}">
                @error('phone_number')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="gender" class="text-sm font-medium text-gray-700 mb-1 block">Gender</label>
                <select name="gender" id="gender" class="w-full px-5 py-3 rounded focus:outline-none  @error('gender') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror">
                    <option value="">Select Gender</option>
                    <option value="Laki-laki" {{ old('gender', $user->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('gender', $user->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="address" class="text-sm font-medium text-gray-700 mb-1 block">Address</label>
                <textarea name="address" id="address" rows="3" placeholder="Address" class="w-full px-5 py-3 rounded focus:outline-none  @error('address') focus:border-danger-subtle bg-danger-soft focus:bg-danger-medium placeholder-danger-strong border-danger @else focus:border-brand-subtle bg-neutral-primary-soft focus:bg-brand-softer placeholder-gray-500 border border-default @enderror">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="col-span-1 mt-5">
                <button type="submit" class="w-full bg-brand hover:bg-brand-strong text-white font-medium py-2 px-4 cursor-pointer rounded-base w-full sm:w-auto"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </div>
        
    </form>
@endsection