<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRItems extends Model
{
    use HasFactory;
    protected $table = "pr_items";
    protected $fillable = [
        'pr_no',
        'item_id',
        'begbal',
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

    public function items(){
        return $this->belongsTo(Items::class, 'item_id');
    }
   
    // public function variants(){
    //     return $this->belongsToMany(Variants::class, 'pr_variants', 'pr_items_id','variants_id');
    // }

   // return $this->belongsToMany(Regions::class, 'regions_stores', 'stores_id', 'regions_id');
}
