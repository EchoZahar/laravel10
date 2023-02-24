<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AliexpressCategoryAttributeValue extends Model
{
    use HasFactory;

    public $table = 'aliexpress_category_attribute_values';

    protected $fillable = [
        'attribute_id', 'values', 'error'
    ];

    protected $casts = [
        'values'    => 'array',
        'error'     => 'array'
    ];
}
