<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdjustment extends Model
{
    use HasUuids;

    protected $table = 'salary_adjustments';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'user_id',
        'tanggal',
        'type',
        'amount',
        'notes',
    ];

    /**
     * Get the user associated with this salary adjustment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
