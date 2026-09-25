<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'prescription';

    protected $fillable = [
        'booking_id',
        'notes',
        'diagnosis',
        'advice',
        'prescription',
        'pdf'
    ];

    protected $casts = [
        'prescription' => 'array', // Automatically cast JSON data to an array
    ];
}