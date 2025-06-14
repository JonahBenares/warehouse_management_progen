<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverrideLogs extends Model
{
    use HasFactory;
    protected $table = "override_logs";
    protected $fillable = [
        'receive',
        'request',
        'issuance',
        'backorder',
        'restock',
        'override_user_id',
        'user_id',
    ];
}
