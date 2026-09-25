<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class PracticeYogaCoachFile extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'practiceyogacoachfiles';
    protected $fillable = [
        'video_file',
        'userId',
        'gender',
        'posesId',
        'levelId',
        'duration',
        'status',
        'created_at',
        'updated_at',
    ];

    
}
