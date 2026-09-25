<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Practiceroutines extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'practiceroutines';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', '_id');
    }

    // ACCESS POSE MODELS
    public function poses()
    {
        return $this->belongsToMany(
            Practiceyogaposes::class,
            null,
            '_id',
            'poseslist.posesId'
        );
    }

    // ACCESS LEVEL MODELS
    public function poseLevels()
    {
        return $this->belongsToMany(
            Yogaposeslevels::class,
            null,
            '_id',
            'poseslist.levelId'
        );
    }

    // CUSTOM ACCESSOR → returns mapped list with pose + level
    public function getDetailedPosesAttribute()
    {
        return collect($this->poseslist)->map(function ($p) {
            return [
                'pose' => Practiceyogaposes::find($p['posesId']),
                'level' => Yogaposeslevels::find($p['levelId']),
                'duration' => $p['duration'] ?? 0,
            ];
        });
    }
}
