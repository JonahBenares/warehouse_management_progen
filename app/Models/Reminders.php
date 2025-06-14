<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminders extends Model
{
    use HasFactory;
    protected $table = "reminders";
    protected $fillable = [
        'reminder_date',
        'title',
        'notes',
        'person_to_notify_id',
        'person_to_notify_name',
        'added_by_id',
        'added_by_name',
        'done',
    ];
}
