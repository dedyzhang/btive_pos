@extends('layout.index')

@section('title','Categories')

@section('navbar')
    <div class="navbar-container flex items-center justify-between w-full gap-4 pe-6">
        <h1 class="text-lg md:text-3xl font-bold">USERS</h1>
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
            <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-600 cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium"><i class="fas fa-user-plus"></i> Add Users</a>
            <a href="{{ route('roles.index') }}" class="bg-purple-600 hover:bg-purple-700 cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium"><i class="fas fa-shield-halved"></i> Kelola Role & Akses</a>
        </div>
        <div class="users-list p-6 bg-white rounded-lg">
            <div class="wrapper-table" class="w-full">
                <table id="dataTables" class="w-full">
                    <thead class="bg-brand-softer text-sm text-gray-600 border-b border-default h-20">
                        <tr>
                            <th class="text-left p-3 rounded-tl-lg">Nama</th>
                            <th class="text-left p-3">Username</th>
                            <th class="text-left p-3">Role</th>
                            <th class="text-left p-3 rounded-tr-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $users)
                            <tr class="border border-default odd:bg-neutral-primary even:bg-neutral-secondary text-sm" data-uuid="{{ $users->uuid }}">
                                <td class="p-3">{{ $users->name }} </td>
                                <td class="p-3">{{ $users->username }}</td>
                                <td class="p-3">{{ $users->role }}</td>
                                <td class="p-3 flex flex-wrap gap-2">
                                    <a href="{{ route('users.edit',$users->uuid) }}" class="bg-brand hover:bg-brand-strong cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium"><i class="fas fa-edit"></i> <span class="hidden sm:inline">Edit</span></a>
                                    <button class="bg-success hover:bg-success-strong cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium resetPassword"><i class="fas fa-sync"></i> <span class="hidden sm:inline">Reset Password</span></button>
                                    <button class="bg-danger hover:bg-danger-strong cursor-pointer text-white px-4 py-2 rounded-md text-sm font-medium hapusUsers"><i class="fas fa-trash-can"></i> <span class="hidden sm:inline">Delete</span></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                        
                </table>
            </div>
        </div>
       
    </div>
    <script type="module">
        let table = new DataTable('#dataTables',{
            columns: [{ width: '25%' },{ width: '25%' },{ width: '10%' },{ width: '25%' }],
        });

        $('#dataTables').on('click','.hapusUsers',function() {
            var uuid = $(this).closest('tr').data('uuid');
            var url = "{{ route('users.destroy',':id') }}";
            url = url.replace(':id',uuid);
            cConfirm("Perhatian","Confirm To Delete?",function() {
                loading();
                $.ajax({
                    type: "DELETE",
                    url : url,
                    headers: {'X-CSRF-TOKEN' : '{{ csrf_token() }}'},
                    success: function(data) {
                        if(data.success === true) {
                            removeLoading();
                            cAlert("green","Success",data.message,true);
                        }
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                })
            });
        });
        $('#dataTables').on('click','.resetPassword',function() {
            var uuid = $(this).closest('tr').data('uuid');
            var url = "{{ route('users.reset',':id') }}";
            url = url.replace(':id',uuid);

            cConfirm("Perhatian","Confirm To Reset Password?",function() {
                loading();
                $.ajax({
                    type: "POST",
                    url : url,
                    headers: {'X-CSRF-TOKEN' : '{{ csrf_token() }}'},
                    success: function(data) {
                        if(data.success === true) {
                            removeLoading();
                            oAlert("green","Sukses","<p>Password Admin Berhasil Di Reset</p><strong>Password Baru Adalah : "+data.password+"</strong>");
                        }
                    },
                    error: function(data) {
                        console.log(data.responseJSON.message);
                    }
                })
            });
        });
    </script>
@endsection