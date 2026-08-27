<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasUuids;

    protected $table = 'stock_opnames';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'user_id',
        'opname_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_id', 'uuid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    /** Total rupiah value lost (negative) or gained (positive) across this count. */
    public function varianceValue(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->varianceValue());
    }
}
