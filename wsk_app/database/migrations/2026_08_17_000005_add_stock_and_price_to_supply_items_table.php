<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stock comes back now that recipes provide auto-deduction: consumption is recorded
     * automatically from sales, which was the missing half that made a manual stock count
     * drift out of date. Stock in = purchase requests, stock out = recipe deduction.
     *
     * unit_price is the cost of one `unit` of the supply, used to price a recipe.
     */
    public function up(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->decimal('stock', 12, 2)->default(0)->after('unit');
            $table->integer('unit_price')->default(0)->after('stock'); // rupiah, integer
        });
    }

    public function down(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->dropColumn(['stock', 'unit_price']);
        });
    }
};
