<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AliCategory extends Model
{
    use HasFactory;

    public $table = 'ali_categories';

    protected $fillable = [
        'name', 'source_id', 'is_visible', 'parent_id'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function aliexpressAttributes() :BelongsToMany
    {
        return $this->belongsToMany(AliCategoryAttribute::class);
    }
}
