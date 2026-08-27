<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlowTransaction extends Model
{
    use HasUuids;

    protected $table = 'cash_flow_transactions';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'account_id',
        'destination_account_id',
        'type',
        'amount',
        'operational_expense',
        'cash_drawer_amount',
        'transaction_date',
        'description',
        'reference',
        'purchase_request_id',
        'is_sales_reconciliation',
        'reconciliation_date',
        'items',
        'purchase_place',
        'category_id'
    ];

    protected $casts = [
        'items' => 'array'
    ];

    /**
     * Get the account that owns the transaction.
     */
    public function account() : BelongsTo
    {
        return $this->belongsTo(CashFlowAccount::class, 'account_id', 'uuid');
    }

    /**
     * Get the destination account for transfers.
     */
    public function destinationAccount() : BelongsTo
    {
        return $this->belongsTo(CashFlowAccount::class, 'destination_account_id', 'uuid');
    }

    /**
     * Get the category associated with the transaction.
     */
    public function category() : BelongsTo
    {
        return $this->belongsTo(CashFlowCategory::class, 'category_id', 'uuid');
    }

    /**
     * The shopping-list request this purchase fulfills, if any.
     */
    public function purchaseRequest() : BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id', 'uuid');
    }
}
