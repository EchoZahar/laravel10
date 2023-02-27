<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OzonCategoryAttributeValue extends Model
{
    use HasFactory;

    public $table = 'ozon_category_attribute_values';

    public $timestamps = false;

    protected $fillable = [
        'ozon_category_attribute_id', 'source_id', 'value', 'info', 'picture'
    ];

    public function ozonCategoryAttribute(): BelongsTo
    {
        return $this->belongsTo(OzonCategoryAttribute::class);
    }

}
