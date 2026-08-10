<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('roles')->where('name', 'dapur')->exists();
        if (!$exists) {
            DB::table('roles')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => 'dapur',
                'permissions' => json_encode(['view_kitchen_queue']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('name', 'dapur')->delete();
    }
};
