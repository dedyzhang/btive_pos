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
        Schema::table('cash_flow_transactions', function (Blueprint $table) {
            $table->bigInteger('cash_drawer_amount')->default(0)->after('operational_expense');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_flow_transactions', function (Blueprint $table) {
            $table->dropColumn('cash_drawer_amount');
        });
    }
};
