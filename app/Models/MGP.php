<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MGP extends Model
{
    use HasFactory;
    protected $table = "mgp_series";
    protected $fillable = [
        'year',
        'series'
    ];
}
