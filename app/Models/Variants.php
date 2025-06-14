<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variants extends Model
{
    use HasFactory;
    protected $table = "variants";
    protected $fillable = [
        'item_id',
        'supplier_id',
        'supplier_name',
        'catalog_no',
        'brand',
        'serial_no',
        'barcode',
        'shipping_cost',
        'unit_cost',
        'currency',
        'expiration',
        'quantity',
        'average_cost',
        'item_status_id',
        'identifier',
        'uom',
        'color',
        'size',
        'receive_flag',
        'composite_flag'
    ];

    public function suppliers(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function item_status(){
        return $this->belongsTo(ItemStatus::class, 'item_status_id');
    }

    public function items(){
        return $this->belongsTo(Items::class, 'item_id');
    }

}
