<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserModels\Booking;
use Laravel\Passport\HasApiTokens;
use App\Models\UserModels\Rating;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'status',
        'parent_id',
        'ip_address',
        'device_id',
        'device_token',
        'device_type',
        
    ];

    protected $attributes = [
        'parent_id' => 0,
        'qrcode'=>''
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

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id')->where('status', 1);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'doctor_id'); // doctor_id is the foreign key in the bookings table
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'doctor_id', 'id')->where('status',1);
    }

    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class, 'doctor_id', 'id');
    // }
}
