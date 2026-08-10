<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::orderBy('created_at', 'desc')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
        ], [
            'name.required' => 'Nama role harus diisi.',
            'name.unique' => 'Nama role sudah terdaftar.',
        ]);

        Role::create([
            'name' => strtolower(trim($request->name)),
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $role = Role::findOrFail($uuid);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $uuid . ',uuid',
            'permissions' => 'nullable|array',
        ], [
            'name.required' => 'Nama role harus diisi.',
            'name.unique' => 'Nama role sudah terdaftar.',
        ]);

        $role->update([
            'name' => strtolower(trim($request->name)),
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(string $uuid)
    {
        $role = Role::findOrFail($uuid);
        
        // Prevent deleting critical roles if needed
        if (in_array($role->name, ['admin', 'dapur'])) {
            return response()->json([
                'success' => false,
                'message' => 'Role bawaan sistem tidak dapat dihapus.'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus.'
        ]);
    }
}
