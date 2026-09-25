<?php

namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferCodeHistory extends Model
{
    protected $connection = 'mysql2'; // Use the second database connection
    protected $table = 'refer_code_histories'; 
    use HasFactory;
}
