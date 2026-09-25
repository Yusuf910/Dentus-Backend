<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorClinicSlot extends Model
{
    use HasFactory;

     function UserClinicDetails() {
        return $this->belongsTo(UserEstablishmentClinic::class, 'clinic_id')->where('status',1);
    }
}
