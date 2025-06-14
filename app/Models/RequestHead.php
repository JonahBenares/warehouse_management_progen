<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestHead extends Model
{
    use HasFactory;
    protected $table = "request_head";
    protected $fillable = [
        'mreqf_no',
        'request_date',
        'request_time',
        'request_type',
        'pr_no',
        'department_id',
        'department_name',
        'purpose_id',
        'purpose_name',
        'enduse_id',
        'enduse_name',
        'borrow_from_pr',
        'remarks',
        'user_id',
        'close',
        'saved',
        'draft',
        'borrowed_id',
        'replenish_id',
        'prepared_by',
        'prepared_by_name',
        'prepared_by_position',
        'requested_by',
        'requested_by_name',
        'requested_by_position',
        'approved_by',
        'approved_by_name',
        'approved_by_position',
        'reviewed_by',
        'reviewed_by_name',
        'reviewed_by_position',
        'noted_by',
        'noted_by_name',
        'noted_by_position'
    ];

    public function request_items(){
        return $this->hasMany(RequestItems::class, 'request_head_id');
    }

    public function issuance(){
        return $this->hasMany(IssuanceHead::class, 'request_head_id');
    }
}
