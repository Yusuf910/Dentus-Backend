<?php

namespace App\Models\UserModels;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserProfile extends Authenticatable
{
    protected $connection = 'mysql2'; // Use the second database connection
    protected $table = 'users'; // Specify your table name
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'last_name',
        'mobile',
        'clinic_id',
        'link_id',
        'status',
        'date_of_birth',
        'referral_code',
        'ip_address',
        'device_id',
        'device_token',
        'device_type',
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
    ];

    function UserInformationDetails() {
        return $this->hasOne(UserInformation::class, 'user_id');
    }

    function UserClinicDetails() {
        return $this->hasOne(UserEstablishmentClinic::class, 'user_id');
    }

    public function userPayments()
    {
        return $this->hasMany(UserPayment::class, 'user_id', 'id');
    }

}
