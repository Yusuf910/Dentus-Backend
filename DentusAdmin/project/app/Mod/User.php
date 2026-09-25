<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\HasRolesAndPermissions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions;

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

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id')->where('status', 1);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    // public function isSuper()
    // {
    //     return $this->role === 'super'; // Adjust according to your role structure
    // }

    public function IsSuper(){
        if ($this->id == 1) {
           return true;
        }
        return false;
    }


    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class);
    // }

    // public function hasPermission($permission)
    // {
    //     return $this->permissions()->where('name', $permission)->exists();
    // }
}
