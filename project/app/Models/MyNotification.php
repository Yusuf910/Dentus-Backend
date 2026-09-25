<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyNotification extends Model
{
    protected $connection = 'mysql';
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
