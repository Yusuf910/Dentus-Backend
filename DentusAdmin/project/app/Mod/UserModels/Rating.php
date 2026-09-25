<?php

namespace App\Models\UserModels;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Rating extends Authenticatable
{
    protected $connection = 'mysql2';  // Use the second database connection
    protected $table = 'ratings'; // Specify your table name
    use HasApiTokens, HasFactory, Notifiable;

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'id');
    }
}
