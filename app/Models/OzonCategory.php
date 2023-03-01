<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OzonCategory extends Model
{
    use HasFactory;

    public $table = 'ozon_categories';

    protected $fillable = [
        'source_id', 'name', 'parent_id'
    ];

    public function parent() : BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function children() : HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function ozonCategoryAttributes() : HasMany
    {
        return $this->hasMany(OzonCategoryAttribute::class);
    }
}
