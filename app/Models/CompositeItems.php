<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompositeItems extends Model
{
    use HasFactory;
    protected $table = "composite_items";
    protected $fillable = [
        'item_id',
        'compose_item_id',
        'quantity',
        'identifier',
        'variant_id'
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function items(){
        return $this->belongsTo(Items::class, 'compose_item_id');
    }
    
    
}
