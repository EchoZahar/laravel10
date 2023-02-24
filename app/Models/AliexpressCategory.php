<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AliexpressCategory extends Model
{
    use HasFactory;

    public $table = 'aliexpress_categories';

    protected $fillable = [
        'name', 'source_id', 'is_visible'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function attributes() : HasMany
    {
        return $this->hasMany(AliexpressCategoryAttribute::class);
    }
}
