<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    use HasUuids;

    protected $table = 'purchase_requests';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'user_id',
        'request_date',
        'status',
        'note',
        'purchased_at',
        'purchased_by',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'purchased_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class, 'purchase_request_id', 'uuid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by', 'uuid');
    }
}
