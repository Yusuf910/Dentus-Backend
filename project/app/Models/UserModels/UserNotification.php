<?php

namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $connection = 'mysql2';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'notification_type',
        'notification',
        'status',
        'image',
];
}
