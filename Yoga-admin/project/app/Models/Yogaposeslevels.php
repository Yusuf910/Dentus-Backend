<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Yogaposeslevels extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'yogaposeslevels';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;

    public function pose()
    {
        return $this->belongsTo(Practiceyogaposes::class, 'posesId', '_id');
    }
}
