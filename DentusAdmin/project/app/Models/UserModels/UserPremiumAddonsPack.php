<?php

namespace App\Models\UserModels;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserPremiumAddonsPack extends Authenticatable
{
    protected $connection = 'mysql2';  // Use the second database connection
    protected $table = 'user_premium_addons_pack'; // Specify your table name
    use HasApiTokens, HasFactory, Notifiable;

    public function featurelist() {
        return $this->hasMany(PackFeature::class, 'id', 'pack_feature_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'plan_id', 'subscription_id');
    }


}
