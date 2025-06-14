<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackorderHead extends Model
{
    use HasFactory;
    protected $table = "backorder_head";
    protected $fillable = [
        'backorder_date',
        'mrecf_no',
        'waybill_no',
        'dr_no',
        'po_no',
        'si_or',
        'pcf',
        'user_id',
        'saved',
        'draft',
        'closed',
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

    public function backorder_details()
    {
        return $this->hasMany(BackorderDetails::class);
    }
    
    public function backorder_items(){
        return $this->hasMany(BackorderItems::class);
    }
}
