<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Userpurchases extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'userpurchases';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;
}
