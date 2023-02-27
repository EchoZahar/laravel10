<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonCategoryAttributeValue extends Model
{
    use HasFactory;

    public $table = 'ozon_category_attribute_values';

    public $timestamps = false;

    protected $fillable = [
        'ozon_attribute_source_id', 'source_id', 'value', 'info', 'picture'
    ];
}
