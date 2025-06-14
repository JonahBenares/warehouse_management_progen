<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PNSeries extends Model
{
    use HasFactory;
    protected $table = "pn_series";
    protected $fillable = [
        'subcat_prefix',
        'series'
    ];
}
