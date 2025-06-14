<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockReason extends Model
{
    use HasFactory;
    protected $fillable = ['reason'];
}
