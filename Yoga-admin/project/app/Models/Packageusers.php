<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Packageusers extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'packageusers';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;

    // public function features()
    // {
    //     return $this->hasMany(practiceyogaposes::class, 'packageId', '_id');
    // }
}
