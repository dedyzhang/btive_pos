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

    /**
     * Where items without a category (manual items, or products whose category was deleted)
     * belong in the receipt's category ordering. Admin sets this by dragging the
     * "Tanpa Kategori" card in Settings > Setting Urutan Kategori.
     *
     * Defaults to last so behaviour is unchanged until an admin positions it explicitly.
     */
    public static function uncategorizedSort(): int
    {
        $setting = static::where('jenis', 'uncategorized_sort')->first();

        return $setting && $setting->nilai !== null && $setting->nilai !== ''
            ? (int) $setting->nilai
            : self::UNCATEGORIZED_SORT_LAST;
    }

    public const UNCATEGORIZED_SORT_LAST = 999999;
}
