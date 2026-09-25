<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Generalsetting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'generalsettings';
    // protected $dates = ['createdAt', 'updatedAt'];

    use HasFactory;
}
