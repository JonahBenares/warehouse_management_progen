<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $table = "items";
    protected $fillable = [
        'item_category_id',
        'item_sub_category_id',
        'item_description',
        'location_id',
        'location_description',
        'warehouse_id',
        'warehouse_description',
        'group_id',
        'group_description',
        'rack_id',
        'rack_description',
        'uom',
        'moq',
        'image1',
        'image2',
        'image3',
        'running_balance',
        'composite_flag',
        'variant_flag'
    ];
}
