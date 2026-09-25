<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRegistration extends Model
{
    use HasFactory;

    protected $table ='medical_registration';
    protected $fillable = [
        'user_id', 'registration_number', 'registration_council', 'registration_year'
    ];
}
