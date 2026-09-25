<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Practiceyogacategories extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'practiceyogacategories';
    // protected $dates = ['createdAt', 'updatedAt'];
    protected $fillable = ['name','status','image'];
    use HasFactory;
}
