<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalNomenclature extends Model
{
    use HasFactory;

    public $table = 'portal_nomenclatures';

    protected $guarded = [];

    protected $casts = [
        'nomenclature_timing' => 'array'
    ];
}
