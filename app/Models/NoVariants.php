<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoVariants extends Model
{
    use HasFactory;
    protected $table = "no_variant";
    protected $fillable = [
        'item_id',
        'serial_no',
        'barcode',
        'expiration',
        'quantity',
        'unit_cost',
        'selling_price',
        'item_status_id',
        'identifier'
    ];
}
