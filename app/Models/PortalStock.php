<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalStock extends Model
{
    use HasFactory;

    public $table = 'portal_stocks';

    protected $guarded = [];
}
