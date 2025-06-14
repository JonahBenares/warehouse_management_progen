<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockDetails extends Model
{
    use HasFactory;
    protected $table = "restock_details";
    protected $fillable = [
        'restock_head_id',
        'mif_no',
        'item_id',
        'item_description',
        'variant_id',
        'receive_items_id',
        'quantity',
        'reason_id',
        'reason',
        'remarks',
        'item_status',
        'item_status_id',
        'identifier'
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function items(){
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function restock_head(){
        return $this->belongsTo(RestockHead::class, 'restock_head_id');
    }

    public function receive_items(){
        return $this->belongsTo(ReceiveItems::class, 'receive_items_id');
    }
}
