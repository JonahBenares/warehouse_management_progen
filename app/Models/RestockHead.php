<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockHead extends Model
{
    use HasFactory;
    protected $table = "restock_head";
    protected $fillable = [
        'source_pr',
        'destination',
        'mrs_no',
        'department_id',
        'department',
        'enduse_id',
        'enduse',
        'purpose_id',
        'purpose',
        'date',
        'time',
        'returned_by',
        'returned_by_name',
        'returned_by_position',
        'acknowledged_by',
        'acknowledged_by_name',
        'acknowledged_by_position',
        'inspected_by',
        'inspected_by_name',
        'inspected_by_position',
        'noted_by',
        'noted_by_name',
        'noted_by_position',
        'user_id',
        'saved'
    ];
    public function restock_details(){
        return $this->belongsTo(RestockDetails::class, 'id');
    }
}
