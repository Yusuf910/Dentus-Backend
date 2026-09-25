<?php
namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name', 'logo', 'address', 'contact_info', 'description', 'social_media_links', 'admin_doctor_id', 'qr_code'
    ];

    // public function doctors()
    // {
    //     return $this->belongsToMany(Doctor::class, 'clinic_doctors', 'clinic_id', 'doctor_id')->withPivot('role');
    // }
}
