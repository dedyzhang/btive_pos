<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Products extends Model
{
    use HasUuids;

    protected $table="products";
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'cost_price',
        'stock',
        'picture',
        'description',
        'is_active'
    ];

    public function category() : BelongsTo {
        return $this->belongsTo(Categories::class,'category_id','uuid');
    }
}
