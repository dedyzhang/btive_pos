<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tables extends Model
{
    use HasUuids;
    protected $table = "tables";
    protected $primaryKey = "uuid";
    protected $fillable = [
        'name',
        'color',
        'sort',
        'status'
    ];
}
