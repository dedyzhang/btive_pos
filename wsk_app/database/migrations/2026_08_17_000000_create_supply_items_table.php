<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master data for kitchen supplies / raw materials (beras, telur, gas, ...).
     * Deliberately separate from `products`, which holds sellable menu items.
     */
    public function up(): void
    {
        Schema::create('supply_items', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->string('unit', 30)->default('pcs'); // kg, liter, pcs, dus, ...
            // Decimal because supplies are often fractional (2.5 kg, 1.5 liter).
            $table->decimal('stock', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_items');
    }
};
