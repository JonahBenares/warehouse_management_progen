<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuanceItems extends Model
{
    use HasFactory;
    protected $table = "issuance_items";
    protected $fillable = [
        'issuance_head_id',
        'item_id',
        'item_description',
        'variant_id',
        'composite_item_id',
        'composite_id',
        'request_items_id',
        'inventory_balance',
        'issued_qty',
        'request_qty',
        'pr_qty',
        'unit_cost',
        'currency',
        'shipping_cost',
        'remarks'
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function items(){
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function issuance_head(){
        return $this->belongsTo(IssuanceHead::class, 'issuance_head_id');
    }

    public function receive_items(){
        return $this->belongsTo(ReceiveItems::class, 'variant_id');
    }
}
