<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveDetails extends Model
{
    use HasFactory;
    protected $table = "receive_details";
    protected $fillable = [
        'receive_head_id',
        'detail_no',
        'pr_no',
        'department_id',
        'department_name',
        'enduse_id',
        'enduse_name',
        'purpose_id',
        'purpose_name',
        'inspected_id',
        'inspected_name',
    ];

    public function receive_head()
    {
        return $this->belongsTo(ReceiveHead::class);
    }

    public function receive_items(){
        return $this->hasMany(ReceiveItems::class, 'receive_details_id');
    }

    // public function receive_items(){
    //     return $this->belongsTo(ReceiveItems::class, 'receive_details_id');
    // }

  

}
