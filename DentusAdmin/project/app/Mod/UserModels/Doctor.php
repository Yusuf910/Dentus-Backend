<?php
namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'specialization', 'experience_years', 'languages_spoken', 'bio', 'ratings', 'work_location', 'consultation_timings', 'qr_code'
    ];

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'clinic_doctors', 'doctor_id', 'clinic_id')->withPivot('role');
    }

    public function profile()
    {
        return $this->hasOne(DoctorProfile::class, 'doctor_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(DoctorSubscription::class, 'doctor_id');
    }
}
