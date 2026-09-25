<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Practiceyogaposes extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'practiceyogaposes';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;

    public function levels()
    {
        return $this->hasMany(Yogaposeslevels::class, 'posesId', '_id');
    }
}
