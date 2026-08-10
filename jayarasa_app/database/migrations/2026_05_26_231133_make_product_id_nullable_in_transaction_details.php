<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat product_id nullable agar item manual tidak perlu produk dummy
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->uuid('product_id')->nullable()->change();
        });

        // Ubah referensi item manual (UUID all-zeros) menjadi NULL
        DB::table('transaction_details')
            ->where('product_id', '00000000-0000-0000-0000-000000000000')
            ->update(['product_id' => null]);

        // Hapus record dummy Item Manual dari tabel products
        DB::table('products')
            ->where('uuid', '00000000-0000-0000-0000-000000000000')
            ->delete();
    }

    public function down(): void
    {
        // Kembalikan ke NOT NULL (tidak restore record dummy)
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->uuid('product_id')->nullable(false)->change();
        });
    }
};
