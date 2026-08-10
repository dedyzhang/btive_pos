<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_flow_accounts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('account_number')->nullable();
            $table->bigInteger('initial_balance')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed some default accounts
        DB::table('cash_flow_accounts')->insert([
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Cash / Laci Kasir',
                'account_number' => null,
                'initial_balance' => 0,
                'description' => 'Kas fisik harian di laci kasir toko',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Bank BCA',
                'account_number' => '8012345678',
                'initial_balance' => 0,
                'description' => 'Rekening transfer bank operasional',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flow_accounts');
    }
};
