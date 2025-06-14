<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowHead extends Model
{
    use HasFactory;
    protected $table = "borrow_head";
    protected $fillable = [
        'borrow_date',
        'borrow_time',
        'borrowed_by_user',
        'borrowed_by_user_name',
        'user_id',
        'saved',
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

    public function borrow_details(){
        return $this->hasMany(BorrowDetails::class, 'borrow_head_id');
    }
}
