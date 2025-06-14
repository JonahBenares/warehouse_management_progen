<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveItems extends Model
{
    use HasFactory;
    protected $table = "receive_items";
    protected $fillable = [
        'receive_head_id',
        'receive_details_id',
        'item_no',
        'item_id',
        'variant_id',
        'item_description',
        'pn_no',
        'supplier_id',
        'supplier_name',
        'brand',
        'color',
        'size',
        'catalog_no',
        'serial_no',
        'barcode',
        'location',
        'item_status_id',
        'uom',
        'item_status',
        'exp_quantity',
        'rec_quantity',
        'borrow_qty',
        'shipping_cost',
        'unit_cost',
        'currency',
        'selling_price',
        'pr_replenish',
        'prno_replenish',
        'expiry_date',
        'remarks',
        'eval_flag',
        'eval_date',
        'eval_user',
        'eval_reason',
        'rejected_qty',
        'acceptance_done'
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function items()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function receive_head()
    {
        return $this->belongsTo(ReceiveHead::class, 'receive_head_id');
    }

    public function receive_details()
    {
        return $this->belongsTo(ReceiveDetails::class, 'receive_details_id');
    }

  
}
