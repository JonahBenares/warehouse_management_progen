<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mreqf extends Model
{
    use HasFactory;
    protected $table = "mreqf_series";
    protected $fillable = [
        'year',
        'series'
    ];
}
