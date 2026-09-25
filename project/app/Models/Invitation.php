<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    // protected $table = 'invitations';
    protected $guarded = [];
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'mobile',
    //     'doctor_id', // Add this line
    //     // Add other attributes as needed
    // ];
}
