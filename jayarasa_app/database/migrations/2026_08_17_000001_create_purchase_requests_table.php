<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A nightly shopping list submitted by cashier/kitchen staff.
     */
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('user_id')->constrained('users', 'uuid'); // who submitted
            $table->date('request_date');
            $table->string('status', 20)->default('pending'); // pending | purchased | cancelled
            $table->text('note')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->foreignUuid('purchased_by')->nullable()->constrained('users', 'uuid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
