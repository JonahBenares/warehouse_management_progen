<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatepassHead extends Model
{
    use HasFactory;
    protected $table = "gatepass_head";
    protected $fillable = [
        'gatepass_no',
        'to_company',
        'destination',
        'vehicle_no',
        'date_issued',
        'status',
        'remarks',
        'user_id',
        'saved',
        'prepared_by',
        'prepared_by_name',
        'prepared_by_position',
        'noted_by',
        'noted_by_name',
        'noted_by_position',
        'approved_by',
        'approved_by_name',
        'approved_by_position',
        'received_by',
        'received_by_name',
        'received_by_position',
        // 'verified_by',
        // 'verified_by_name',
        // 'verified_by_position',
        'cpgc_guard_name',
        'npc_guard_name'
    ];

    public function gatepass_items(){
        return $this->hasMany(GatepassItems::class);
    }
}
