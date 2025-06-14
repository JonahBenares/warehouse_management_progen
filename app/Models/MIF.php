<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MIF extends Model
{
    use HasFactory;
    protected $table = "mif_series";
    protected $fillable = [
        'year',
        'series'
    ];
}
