<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicDoctor extends Model
{
    use HasFactory;

    protected $table = 'clinic_doctors';
    protected $fillable = ['clinic_id', 'doctor_id', 'role'];
}
