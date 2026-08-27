<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class SupplyItem extends Model
{
    use HasUuids;

    protected $table = 'supply_items';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'unit',
        'purchase_unit',
        'purchase_conversion',
        'stock',
        'par_level',
        'unit_price',
        'is_active',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'par_level' => 'decimal:2',
            'purchase_conversion' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Due for restocking once stock reaches the par level. A par of 0 means "no threshold
     * set" — fall back to only flagging when it's actually run out.
     */
    public function isBelowPar(): bool
    {
        return (float) $this->par_level > 0
            ? (float) $this->stock <= (float) $this->par_level
            : (float) $this->stock <= 0;
    }

    /**
     * Whether this item is bought in a different unit than it's used/stocked in
     * (e.g. bought per "batang", used per "keping").
     */
    public function hasPurchaseConversion(): bool
    {
        return !empty($this->purchase_unit) && (float) $this->purchase_conversion > 0;
    }

    public function purchaseRequestItems(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class, 'supply_item_id', 'uuid');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class, 'supply_item_id', 'uuid');
    }

    /**
     * Purchase insight per item, derived from history instead of a running stock count:
     * when it was last bought, how long ago, and the average gap between purchases —
     * which is effectively "how long one purchase lasts".
     *
     * Returned as one collection keyed by supply_item_id so callers can render a whole
     * list without an N+1 query per item.
     *
     * @return \Illuminate\Support\Collection<string, array>
     */
    public static function purchaseStats()
    {
        $rows = DB::table('purchase_request_items as pri')
            ->join('purchase_requests as pr', 'pr.uuid', '=', 'pri.purchase_request_id')
            ->where('pr.status', 'purchased')
            ->whereNotNull('pr.purchased_at')
            ->orderBy('pr.purchased_at')
            ->get(['pri.supply_item_id', 'pri.qty', 'pr.purchased_at']);

        $today = Carbon::now()->startOfDay();

        return $rows->groupBy('supply_item_id')->map(function ($itemRows) use ($today) {
            $dates = $itemRows
                ->map(fn ($row) => Carbon::parse($row->purchased_at)->startOfDay())
                ->sort()
                ->values();

            $lastPurchase = $dates->last();

            // Average gap between consecutive purchases. Needs at least two purchases —
            // with only one we can't know the interval yet.
            $averageDaysBetween = null;
            if ($dates->count() >= 2) {
                $span = $dates->first()->diffInDays($dates->last());
                if ($span > 0) {
                    $averageDaysBetween = round($span / ($dates->count() - 1), 1);
                }
            }

            return [
                'last_purchased_at' => $lastPurchase,
                'days_since_last' => $lastPurchase ? (int) $lastPurchase->diffInDays($today) : null,
                'purchase_count' => $dates->count(),
                'average_days_between' => $averageDaysBetween,
                'last_qty' => (float) $itemRows->last()->qty,
            ];
        });
    }
}
