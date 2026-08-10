<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashFlowAccount extends Model
{
    use HasUuids;

    protected $table = 'cash_flow_accounts';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'account_number',
        'initial_balance',
        'description',
        'icon',
        'color'
    ];

    /**
     * Get the transactions for the cash flow account.
     */
    public function transactions() : HasMany
    {
        return $this->hasMany(CashFlowTransaction::class, 'account_id', 'uuid');
    }
}
