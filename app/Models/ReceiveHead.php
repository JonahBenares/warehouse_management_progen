<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveHead extends Model
{
    use HasFactory;
    protected $table = "receive_head";
    protected $fillable = [
        'receive_date',
        'mrecf_no',
        'waybill_no',
        'dr_no',
        'po_no',
        'si_or',
        'pcf',
        'user_id',
        'saved',
        'draft',
        'prepared_by',
        'prepared_by_name',
        'prepared_position',
        'received_by',
        'received_by_name',
        'receive_position',
        'acknowledged_by',
        'acknowledged_by_name',
        'acknowledged_position',
        'noted_by',
        'noted_name',
        'noted_position',
        'delivered_by'
    ];

    public function receive_details()
    {
        return $this->hasMany(ReceiveDetails::class);
    }

    public function receive_items(){
        return $this->hasMany(ReceiveItems::class);
    }
    // public function receive_items(){
    //     return $this->belongTo(ReceiveItems::class, 'receive_head_id');
    // }


    
}
