<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Items extends Model
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
        'moq',
        'image1',
        'image2',
        'image3',
        'running_balance',
        'begbal',
        'composite_flag',
        'variant_flag',
        'novariant_flag',
        'draft',
        'pn_no',
        'copy_qty',
        'composite_cost'
    ];

    public function getImageAttribute($value){
        return Storage::url("images/" .$value);
    }

    public function sub_category(){
        return $this->belongsTo(ItemSubCategory::class);
    }

    public function category(){
        return $this->belongsTo(ItemCategory::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }

    public function groups(){
        return $this->belongsTo(group::class);
    }

    public function rack(){
        return $this->belongsTo(Rack::class);
    }

    public function variants(){
        return $this->hasMany(Variants::class, 'item_id');
    }

    public function pritems(){
        return $this->hasMany(PRItems::class, 'item_id');
    }

    public function composite_items(){
        return $this->hasMany(CompositeItems::class, 'id');
    }

}
