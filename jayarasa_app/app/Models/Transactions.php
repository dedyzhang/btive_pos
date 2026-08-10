<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transactions extends Model
{
    use HasUuids;
    protected $table = 'transactions';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'invoice_number',
        'user_id',
        'table_id',
        'customer_name',
        'order_type',
        'subtotal',
        'discount',
        'tax',
        'service_charge',
        'total',
        'status',
        'paid_at',
        'paid_method',
        'total_paid',
        'kitchen_status'
    ];

    public function table() : HasOne {
        return $this->hasOne(Tables::class,'uuid','table_id');
    }

    public function orderItem() : HasMany {
        return $this->hasMany(TransactionDetails::class,'order_id','uuid');
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
