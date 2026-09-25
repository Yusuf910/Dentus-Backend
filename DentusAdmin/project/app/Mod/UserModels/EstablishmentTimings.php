<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstablishmentTimings extends Model
{
    use HasFactory;

    protected $table ='establishment_timing';
    protected $fillable = [
        'user_id', 'days', 'morning_from', 'morning_to', 'evening_from', 'evening_to'
    ];
}
