<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Par level = the minimum amount that should always be on hand. Once stock drops to or
     * below it, the item is due for restocking — this is what drives the shopping checklist
     * suggestions, rather than waiting for stock to hit zero.
     */
    public function up(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->decimal('par_level', 12, 2)->default(0)->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            $table->dropColumn('par_level');
        });
    }
};
