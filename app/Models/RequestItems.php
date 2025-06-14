<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItems extends Model
{
    use HasFactory;
    protected $table = "request_items";
    protected $fillable = [
        'request_head_id',
        'item_id',
        'item_description',
        'variant_id',
        'composite_id',
        'quantity',
        'unit_cost',
        'currency',
        'shipping_cost'
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function request_head()
    {
        return $this->belongsTo(RequestHead::class, 'request_head_id');
    }

    
}
