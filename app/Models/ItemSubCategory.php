<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSubCategory extends Model
{
    use HasFactory;
    protected $table = "item_sub_categories";
    protected $fillable = [
        'item_category_id',
        'subcat_code',
        'subcat_prefix',
        'subcat_name'
    ];
}
