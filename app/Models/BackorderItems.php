<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackorderItems extends Model
{
    use HasFactory;
    protected $table = "backorder_items";
    protected $fillable = [
        'backorder_head_id',
        'backorder_details_id',
        'receive_items_id',
        'variant_id',
        'item_no',
        'item_id',
        'item_description',
        'pn_no',
        'supplier_id',
        'supplier_name',
        'brand',
        'catalog_no',
        'serial_no',
        'barcode',
        'location',
        'item_status_id',
        'uom',
        'size',
        'color',
        'item_status',
        'exp_quantity',
        'rec_quantity',
        'bo_quantity',
        'unit_cost',
        'currency',
        'selling_price',
        'pr_replenish',
        'expiry_date',
        'remarks',
        'eval_flag',
        'eval_date',
        'eval_user',
        'eval_reason',
        'rejected_qty',
        'acceptance_done',
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function backorder_head(){
        return $this->belongsTo(BackorderHead::class, 'backorder_head_id');
    }

    public function backorder_details(){
        return $this->belongsTo(BackorderDetails::class, 'backorder_details_id');
    }

    public function receive_items(){
        return $this->belongsTo(ReceiveItems::class, 'receive_items_id');
    }

}
