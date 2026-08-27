<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Running-stock counts were dropped in favour of tracking purchase history instead:
     * a stock number is only correct if every gram consumed is also recorded, which never
     * survives daily kitchen use. Purchase dates fill themselves in from actual activity,
     * and answer the question that's actually useful — how long one purchase lasts.
     */
    public function up(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }

    public function down(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->decimal('stock', 12, 2)->default(0);
        });
    }
};
