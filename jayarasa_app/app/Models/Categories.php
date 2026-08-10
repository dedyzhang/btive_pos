<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories extends Model
{
    use HasUuids;
    protected $table = 'categories';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'nama',
        'icon',
        'color',
        'sort'
    ];

    public function products() : HasMany {
        return $this->hasMany(Products::class,'category_id','uuid');
    }
}
