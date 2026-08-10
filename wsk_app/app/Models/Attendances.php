<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendances extends Model
{
    use HasUuids;

    protected $table = 'attendances';
    protected $primaryKey = 'uuid';
    
    protected $fillable = [
        'user_id',
        'tanggal',
        'clock_in',
        'clock_out',
        'foto_in',
        'foto_out',
    ];

    /**
     * Get the user that owns the attendance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
