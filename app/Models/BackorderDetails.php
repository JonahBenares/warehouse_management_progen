<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackorderDetails extends Model
{
    use HasFactory;
    protected $table = "backorder_details";
    protected $fillable = [
        'backorder_head_id',
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

    public function backorder_head()
    {
        return $this->belongsTo(BackorderHead::class);
    }

    public function backorder_items(){
        return $this->hasMany(BackorderItems::class, 'backorder_details_id');
    }
}
