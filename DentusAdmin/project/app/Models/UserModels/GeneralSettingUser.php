<?php

namespace App\Models\UserModels;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class GeneralSettingUser extends Authenticatable
{
    protected $connection = 'mysql2';  // Use the second database connection
    protected $table = 'generalsettings_user'; // Specify your table name
    use HasApiTokens, HasFactory, Notifiable;

}
