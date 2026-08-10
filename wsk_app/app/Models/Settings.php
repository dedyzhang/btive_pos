<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasUuids;
    protected $table = 'settings';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'jenis',
        'nilai'
    ];
}
