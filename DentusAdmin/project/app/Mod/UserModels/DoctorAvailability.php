<?php
namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    use HasFactory;

    protected $table ='doctor_availability';
    protected $fillable = [
        'user_id', 'days', 'morning_from', 'morning_to', 'evening_from', 'evening_to'
    ];
}
