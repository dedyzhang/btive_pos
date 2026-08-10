<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetails extends Model
{
    use HasUuids;
    protected $table = 'transaction_details';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'qty',
        'note',
        'subtotal',
        'is_kitchen_done'
    ];

    public function product() : BelongsTo {
        return $this->belongsTo(Products::class,'product_id','uuid');
    }

    public function order() : BelongsTo {
        return $this->belongsTo(Transactions::class,'order_id','uuid');
    }
}
