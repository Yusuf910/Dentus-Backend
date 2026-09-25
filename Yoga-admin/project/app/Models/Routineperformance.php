<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Routineperformance extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'routineperformances';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;

    
    public function routine()
    {
        return $this->belongsTo(Practiceroutines::class, 'routineId', '_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', '_id');
    }
}
