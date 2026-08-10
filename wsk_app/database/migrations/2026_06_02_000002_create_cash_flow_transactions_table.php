<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_flow_transactions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('account_id')->constrained('cash_flow_accounts', 'uuid')->onDelete('cascade');
            $table->string('type'); // 'income' or 'expense'
            $table->bigInteger('amount');
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('is_sales_reconciliation')->default(false);
            $table->date('reconciliation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flow_transactions');
    }
};
