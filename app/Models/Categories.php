<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    public $table = 'categories';

    protected $fillable = [
        'parent_id', 'name', 'slug', 'ozon_category_id', 'aliexpress_category_id'
    ];
}
