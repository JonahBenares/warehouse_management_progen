<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_code',
        'prefix',
        'category_name'
    ];

    public function categories()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function sub_categories(){
        return $this->hasMany(ItemSubCategory::class);
    }
}
