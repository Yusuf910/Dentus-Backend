<?php

namespace App\Models\UserModels;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class MedicalRecord extends Authenticatable
{
    protected $connection = 'mysql2';  // Use the second database connection
    protected $table = 'medical_records'; // Specify your table name
    use HasApiTokens, HasFactory, Notifiable;

    public function userdetail()
    {
        $user = $this->belongsTo(UserProfile::class, 'user_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $user;
    }

    public function memberdetail()
    {
        $user = $this->belongsTo(Member::class, 'member_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $user;
    }

}
