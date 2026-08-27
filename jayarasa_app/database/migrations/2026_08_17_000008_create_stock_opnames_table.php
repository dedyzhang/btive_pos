<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A physical stock count. Recipe deduction keeps the running figure useful day to day,
     * but spillage, imprecise portioning and shrinkage always make it drift — an opname
     * resets it to reality and, more usefully, records how far it had drifted.
     */
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('user_id')->constrained('users', 'uuid');
            $table->date('opname_date');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('stock_opname_id')->constrained('stock_opnames', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('supply_item_id')->constrained('supply_items', 'uuid');
            // What the system believed at the moment of counting...
            $table->decimal('system_stock', 12, 2);
            // ...versus what was physically there.
            $table->decimal('actual_stock', 12, 2);
            // Stored rather than derived so historical variance can't shift if stock changes later.
            $table->decimal('variance', 12, 2);
            $table->string('item_name');
            $table->string('unit', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};
