<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bill of materials: how much of each supply item one serving of a menu product uses.
     * Drives both recipe costing and the automatic stock deduction on order submit.
     */
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('product_id')->constrained('products', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('supply_item_id')->constrained('supply_items', 'uuid')->cascadeOnDelete();
            // Amount used per single serving, in the supply item's own unit (0.2 kg, 1 pcs, ...).
            $table->decimal('qty', 12, 3);
            $table->timestamps();

            // One row per ingredient per product — adding the same ingredient twice is an edit.
            $table->unique(['product_id', 'supply_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
    }
};
