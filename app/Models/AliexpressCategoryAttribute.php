<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AliexpressCategoryAttribute extends Model
{
    use HasFactory;

    public $table = 'aliexpress_category_attributes';

    protected $fillable = [
        'aliexpress_category_id', 'is_sku', 'name', 'source_id', 'is_required', 'is_key', 'is_brand', 'is_enum_prop',
        'is_multi_select', 'is_input_prop', 'has_unit', 'has_customized_pic', 'has_customized_name', 'units'
    ];

    protected $casts = [
        'units'                 => 'array',
        'is_sku'                => 'boolean',
        'is_required'           => 'boolean',
        'is_key'                => 'boolean',
        'is_brand'              => 'boolean',
        'is_enum_prop'          => 'boolean',
        'is_multi_select'       => 'boolean',
        'is_input_prop'         => 'boolean',
        'has_unit'              => 'boolean',
        'has_customized_pic'    => 'boolean',
        'has_customized_name'   => 'boolean',
    ];

    public function aliexpressCategory() : BelongsTo
    {
        return $this->belongsTo(AliexpressCategory::class, 'aliexpress_category_id', 'id');
    }
}
