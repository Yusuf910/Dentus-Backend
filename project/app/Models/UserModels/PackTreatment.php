<?php

namespace App\Models\UserModels;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PackTreatment extends Authenticatable
{
    protected $connection = 'mysql2';  // Use the second database connection
    protected $table = 'pack_treatment'; // Specify your table name
    use HasApiTokens, HasFactory, Notifiable;

    public function featurelist() {
        return $this->hasMany(PackFeature::class, 'pack_id', 'id');
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscriptionsPack::class, 'pack_id', 'id');
    }

}
