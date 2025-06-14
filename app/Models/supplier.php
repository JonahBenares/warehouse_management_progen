<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_name',
        'address',
        'contact_person',
        'contact_number',
        'email',
        'terms',
        'is_active'
    ];
}
