<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatepassItems extends Model
{
    use HasFactory;
    protected $table = "gatepass_items";
    protected $fillable = [
        'gatepass_head_id',
        'item_description',
        'quantity',
        'uom',
        'type',
        'remarks',
        'image',
        'display_flag',
    ];

    public function gatepass_head(){
        return $this->belongsTo(GatepassHead::class, 'gatepass_head_id');
    }
}
