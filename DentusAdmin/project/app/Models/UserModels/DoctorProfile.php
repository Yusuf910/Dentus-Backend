<?php
namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'user_id', 'profile_picture', 'bio', 'specialization', 'experience_years', 'languages_spoken', 'practice_type',
        'introduction', 'education', 'awards', 'medical_registration_doc', 'clinic_address', 'establishment_timings', 'subscription_plan', 'is_main_doctor'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
