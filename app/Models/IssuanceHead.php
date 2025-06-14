<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuanceHead extends Model
{
    use HasFactory;
    protected $table = "issuance_head";
    protected $fillable = [
        'request_head_id',
        'mreqf_no',
        'issuance_date',
        'issuance_time',
        'mif_no',
        'mgp_no',
        'pr_no',
        'department_id',
        'department_name',
        'purpose_id',
        'purpose_name',
        'enduse_id',
        'enduse_name',
        'remarks',
        'user_id',
        'saved',
        'contractor',
        'contractor_name',
        'prepared_by',
        'prepared_by_name',
        'prepared_by_pos',
        'received_by',
        'received_by_name',
        'received_by_pos',
        'released_by',
        'released_by_name',
        'released_by_pos',
        'noted_by',
        'noted_name',
        'noted_pos',
        'requested_by',
        'requested_by_name',
        'requested_by_pos',
        'approved_by',
        'approved_by_name',
        'approved_by_pos',
        'recommended_by',
        'recommended_by_name',
        'recommended_by_pos',
        'inspected_by',
        'inspected_by_name',
        'inspected_by_pos',
        'noted_by_gp',
        'noted_by_name_gp',
        'noted_by_pos_gp'
    ];

    public function request(){
        return $this->belongsTo(RequestHead::class, 'request_head_id');
    }

    public function issuance_items(){
        return $this->hasMany(IssuanceItems::class, 'id');
    }
}
