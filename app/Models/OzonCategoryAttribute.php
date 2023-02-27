<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OzonCategoryAttribute extends Model
{
    use HasFactory;

    public $table = 'ozon_category_attributes';

    public $timestamps = false;

    protected $fillable = [
        'ozon_category_id', 'source_id', 'name', 'description', 'type',
        'is_collection', 'is_required', 'group_id', 'group_name', 'dictionary_id',
        'is_aspect', 'category_dependent'
    ];

    public function ozonCategory() : BelongsTo
    {
        return $this->belongsTo(OzonCategory::class);
    }

    public function ozonCategoryAttributeValues() : HasMany
    {
        return $this->hasMany(OzonCategoryAttributeValue::class);
    }
}
