<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantsBalance extends Model
{
    use HasFactory;
    protected $table = "variants_balance";
    protected $fillable = [
        'item_id',
        'variant_id',
        'whstocks_qty',
        'composite_qty',
        'receive_qty',
        'issuance_qty',
        'restock_qty',
        'transfer_qty',
        'damage_qty',
        'borrow_deduct',
        'replenish_add',
        'borrow_add',
        'replenish_deduct',
        'backorder_qty',
        'rejected_qty',
        'balance'
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function items(){
        return $this->belongsTo(Items::class, 'item_id');
    }
}
