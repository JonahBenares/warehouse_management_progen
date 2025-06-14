<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mrec extends Model
{
    use HasFactory;
    protected $table = "mrec_series";
    protected $fillable = [
        'year',
        'series'
    ];
}
