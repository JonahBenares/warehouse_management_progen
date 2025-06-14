<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowDetails extends Model
{
    use HasFactory;
    protected $table = "borrow_details";
    protected $fillable = [
        'borrow_head_id',
        'borrowed_by',
        'borrowed_from',
        'item_id',
        'item_description',
        'variant_id',
        'avail_qty',
        'quantity',
        'replenished_qty',
        'balance',
        'department_id',
        'department_name',
        'enduse_id',
        'enduse_name',
        'purpose_id',
        'purpose_name',
        'remarks',
    ];

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }
    
    public function items()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function borrow_head(){
        return $this->belongsTo(BorrowHead::class, 'borrow_head_id');
    }
}
