<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function recipes() : HasMany {
        return $this->hasMany(ProductRecipe::class,'product_id','uuid');
    }

    /**
     * Cost of one serving based on the recipe's ingredients.
     *
     * Shown alongside the manually-entered `cost_price` as a reference — it deliberately
     * does NOT overwrite it, so the figure the profit reports use stays under admin control.
     * Returns null when the product has no recipe yet (nothing to base a figure on).
     */
    public function recipeCost() : ?float {
        if ($this->recipes->isEmpty()) {
            return null;
        }

        return (float) $this->recipes->sum(fn ($recipe) => $recipe->lineCost());
    }
}
