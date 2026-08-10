<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashFlowCategory extends Model
{
    use HasUuids;

    protected $table = 'cash_flow_categories';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'type'
    ];

    /**
     * Get the transactions associated with the category.
     */
    public function transactions() : HasMany
    {
        return $this->hasMany(CashFlowTransaction::class, 'category_id', 'uuid');
    }
}
