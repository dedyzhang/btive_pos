<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipe extends Model
{
    use HasUuids;

    protected $table = 'product_recipes';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'product_id',
        'supply_item_id',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id', 'uuid');
    }

    public function supplyItem(): BelongsTo
    {
        return $this->belongsTo(SupplyItem::class, 'supply_item_id', 'uuid');
    }

    /** Cost contribution of this ingredient to one serving. */
    public function lineCost(): float
    {
        return (float) $this->qty * (float) ($this->supplyItem->unit_price ?? 0);
    }
}
