<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The shopping-list feature is meant to be filled in by cashier/kitchen staff, so grant
     * them the new permission up front instead of requiring a manual role edit after deploy.
     */
    public function up(): void
    {
        foreach (['cashier', 'kasir', 'dapur'] as $roleName) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            if (!$role) {
                continue;
            }

            $permissions = json_decode($role->permissions, true) ?: [];
            if (in_array('manage_stock', $permissions, true)) {
                continue;
            }

            $permissions[] = 'manage_stock';
            DB::table('roles')->where('name', $roleName)->update([
                'permissions' => json_encode($permissions),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        foreach (['cashier', 'kasir', 'dapur'] as $roleName) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            if (!$role) {
                continue;
            }

            $permissions = array_values(array_filter(
                json_decode($role->permissions, true) ?: [],
                fn ($permission) => $permission !== 'manage_stock'
            ));

            DB::table('roles')->where('name', $roleName)->update([
                'permissions' => json_encode($permissions),
                'updated_at' => now(),
            ]);
        }
    }
};
