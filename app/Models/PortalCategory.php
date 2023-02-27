<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalCategory extends Model
{
    use HasFactory;

    protected $table = 'portal_categories';

    public $timestamps = FALSE;

    protected $fillable = [
        'nomenclature_type_id', 'parent_id', 'type_name', 'ozon_type_id', 'ozon_type_name'
    ];
}
