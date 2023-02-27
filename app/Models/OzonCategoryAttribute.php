<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Получить attribute values при загрузке.
     * @return mixed
     */
    public function scopeAttributeValues() : mixed
    {
        return OzonCategoryAttributeValue::where('ozon_attribute_source_id', '=', $this->source_id)->get();
    }
}
