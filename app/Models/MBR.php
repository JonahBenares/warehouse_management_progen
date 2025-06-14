<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBR extends Model
{
    use HasFactory;
    protected $table = "mbr_series";
    protected $fillable = [
        'year',
        'series'
    ];
}
