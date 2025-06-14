<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatepassReturnedHistory extends Model
{
    use HasFactory;
    protected $table = "gatepass_returned_history";
    protected $fillable = [
        'gatepass_head_id',
        'gatepass_item_id',
        'date_returned',
        'returned_qty',
        'remarks',
        'user_id',
    ];

    public function gatepass_items(){
        return $this->belongsTo(GatepassItems::class, 'gatepass_item_id');
    }
}
