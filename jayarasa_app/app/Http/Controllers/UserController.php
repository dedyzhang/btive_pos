<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();
        return view('users.index',compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:1000',
        ]);
        if($request->role == 'admin') {
            $codeAccess = '100';
        } else {
            $codeAccess = '200';
        }
        $username = $codeAccess.date('dmY').rand(100,999);
        $password = Hash::make("Pass.".$username);

        User::create([
            'name' => $request->name,
            'role' => $request->role,
            'username' => $username,
            'password' => $password,
            'token' => 0,
            'profile_picture' => 'resources/img/profile/boy_2.png',
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        return redirect()->route('users.index')->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $uuid)
    {
        $user = User::findOrFail($uuid);
        return view('users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $uuid)
    {
        $user = User::findOrFail($uuid);
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:1000',
        ]);
        $user->update([
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);
        return redirect()->route('users.index')->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $uuid)
    {
        $user = User::findOrFail($uuid);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    public function resetPassword(String $uuid)
    {
        $user = User::findOrFail($uuid);
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            . '0123456789'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, 6) as $k) $rand .= $seed[$k];
        $password = Hash::make($rand);
        $user->update([
            'password' => $password,
            'token' => 0,
        ]);

        return response()->json([
            'success' => true,
            'password' => $rand
        ]);
    }

    /**
     * Change the currently authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = auth()->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini salah.'
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password Anda berhasil diubah!'
        ]);
    }

    /**
     * Update the currently authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|string',
            'profile_picture_file' => 'nullable|image|max:2048'
        ], [
            'name.required' => 'Nama lengkap harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'profile_picture_file.image' => 'File harus berupa gambar.',
            'profile_picture_file.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'address' => $request->address,
        ];

        if ($request->hasFile('profile_picture_file')) {
            $file = $request->file('profile_picture_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profile_pictures', $filename, 'public');
            $data['profile_picture'] = 'storage/profile_pictures/' . $filename;

            // Delete old custom profile picture if exists
            if ($user->profile_picture && !str_starts_with($user->profile_picture, 'resources/')) {
                $oldPath = public_path($user->profile_picture);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        } elseif ($request->profile_picture) {
            if (str_starts_with($request->profile_picture, 'resources/')) {
                // If the user previously had a custom upload, delete it
                if ($user->profile_picture && !str_starts_with($user->profile_picture, 'resources/')) {
                    $oldPath = public_path($user->profile_picture);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $data['profile_picture'] = $request->profile_picture;
            }
        }

        $user->update($data);

        $resolvedPic = $user->profile_picture 
            ? (str_starts_with($user->profile_picture, 'resources/') 
                ? \Illuminate\Support\Facades\Vite::asset($user->profile_picture) 
                : asset($user->profile_picture))
            : \Illuminate\Support\Facades\Vite::asset('resources/img/profile/boy_1.png');

        return response()->json([
            'success' => true,
            'message' => 'Profil Anda berhasil diperbarui!',
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'gender' => $user->gender,
                'address' => $user->address,
                'profile_picture' => $resolvedPic
            ]
        ]);
    }
}
