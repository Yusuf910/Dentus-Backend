<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEstablishmentClinic extends Model
{
    use HasFactory;

    public function galleries()
    {
        return $this->hasMany(UserEstablishmentGallery::class, 'establishment_clinics_id'); 
    }

    // Relationship with UserEstablishmentHoliday
    public function holidays()
    {
        return $this->hasMany(UserEstablishmentHoliday::class, 'establishment_clinics_id');
    }
}
