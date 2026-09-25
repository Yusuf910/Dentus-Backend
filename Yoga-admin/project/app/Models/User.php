<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;

    public function routines()
    {
        return $this->hasMany(Practiceroutines::class, 'userId', '_id');
    }

    public function performances()
    {
        return $this->hasMany(Routineperformance::class, 'userId', '_id');
    }

    public function purchases()
    {
        return $this->hasMany(Userpurchases::class, 'userId', '_id');
    }

    



}
