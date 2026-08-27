<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a cash flow expense entry record which pending shopping-list request it fulfills,
     * so recording the real purchase (with real price) can also close that request out.
     */
    public function up(): void
    {
        Schema::table('cash_flow_transactions', function (Blueprint $table) {
            $table->uuid('purchase_request_id')->nullable()->after('reference');
            $table->foreign('purchase_request_id')->references('uuid')->on('purchase_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_flow_transactions', function (Blueprint $table) {
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn('purchase_request_id');
        });
    }
};
