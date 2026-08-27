<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('purchase_request_id')->constrained('purchase_requests', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('supply_item_id')->constrained('supply_items', 'uuid');
            $table->decimal('qty', 12, 2);
            // Snapshot of the item's name/unit at submission time, so an later rename or
            // deletion of the master item doesn't rewrite past shopping lists.
            $table->string('item_name');
            $table->string('unit', 30);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
