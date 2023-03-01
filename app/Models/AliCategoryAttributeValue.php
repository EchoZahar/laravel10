<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AliCategoryAttributeValue extends Model
{
    use HasFactory;

    public $table = 'ali_category_attribute_values';

    public $timestamps = false;

    protected $fillable = [
        'ali_attribute_id',
        'values', 'error'
    ];

    protected $casts = [
        'values'    => 'array',
        'error'     => 'array'
    ];

    public function aliAttributes() : BelongsToMany
    {
        return $this->belongsToMany(AliCategoryAttribute::class);
    }
}
