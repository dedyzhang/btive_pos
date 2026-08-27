<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasUuids;

    protected $table = 'stock_opname_items';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'stock_opname_id',
        'supply_item_id',
        'system_stock',
        'actual_stock',
        'variance',
        'item_name',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'system_stock' => 'decimal:2',
            'actual_stock' => 'decimal:2',
            'variance' => 'decimal:2',
        ];
    }

    public function opname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id', 'uuid');
    }

    public function supplyItem(): BelongsTo
    {
        return $this->belongsTo(SupplyItem::class, 'supply_item_id', 'uuid');
    }

    /**
     * Rupiah value of the variance, priced at the supply's current unit price.
     * Negative = physical count came up short (waste, spillage, over-portioning).
     */
    public function varianceValue(): float
    {
        return (float) $this->variance * (float) ($this->supplyItem->unit_price ?? 0);
    }
}
