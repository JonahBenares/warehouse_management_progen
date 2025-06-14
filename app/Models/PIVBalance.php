<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PIVBalance extends Model
{
    use HasFactory;
    protected $table = "piv_balance";
    protected $fillable = [
        'pr_no',
        'item_id',
        'variant_id',
        'quantity',
        'rejected_qty',
    ];

    public function items(){
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function variants(){
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function pritems(){
        return $this->hasMany(PRItems::class, 'item_id');
    }
}
