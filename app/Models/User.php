<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'position',
        'contact_no',
        'access',
        'temp_password',
        'change_password',
        'user_type',
        'acknowledge_flag',
        'approved_flag',
        'requested_flag',
        'released_flag',
        'reviewed_flag',
        'delivered_flag',
        'inspected_flag',
        'noted_flag',
        'received_flag',
        'returned_flag',
        'recommend_flag',
        'accepted_flag',
        'rejected_flag'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'access' => 'integer',
    ];

    public function department(){
        return $this->belongsTo(department::class, 'department_id');
    }
}
