<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_flow_accounts', function (Blueprint $table) {
            $table->string('icon')->nullable()->default('fa-wallet');
            $table->string('color')->nullable()->default('#6366f1'); // Default to Indigo / Brand-like color
        });

        // Set default icon for existing Bank BCA
        \Illuminate\Support\Facades\DB::table('cash_flow_accounts')
            ->where('name', 'like', '%BCA%')
            ->orWhere('name', 'like', '%Bank%')
            ->update([
                'icon' => 'fa-building-columns',
                'color' => '#3b82f6' // Blue for Bank
            ]);
            
        // Set default color for Cash laci kasir
        \Illuminate\Support\Facades\DB::table('cash_flow_accounts')
            ->where('name', 'like', '%Cash%')
            ->orWhere('name', 'like', '%Kas%')
            ->update([
                'icon' => 'fa-wallet',
                'color' => '#10b981' // Emerald for Cash
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_flow_accounts', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color']);
        });
    }
};
