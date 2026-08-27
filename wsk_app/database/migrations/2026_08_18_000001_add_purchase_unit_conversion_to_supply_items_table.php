<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `unit` stays the base/usage unit (what stock, par level, and recipes are counted in —
     * e.g. "keping"). These two columns describe the unit an item is actually bought in when
     * it differs (e.g. "batang"), and how many base units one purchase unit equals, so
     * purchasing can be entered the natural way ("beli 3 batang") and converted automatically.
     */
    public function up(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->string('purchase_unit')->nullable()->after('unit');
            $table->decimal('purchase_conversion', 12, 4)->nullable()->after('purchase_unit');
        });
    }

    public function down(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_unit', 'purchase_conversion']);
        });
    }
};
