<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    public function getLanguageNames()
    {
        $languageIds = explode('|', $this->language_known); // Explode pipe-separated values
        return MasterLangauage::whereIn('id', $languageIds)->pluck('name')->toArray();;
    }

    public function getSpecialisationNames()
    {
        $specialisationIds = explode('|', $this->specialisations);
        return MasterSpecialsation::whereIn('id', $specialisationIds)->pluck('name')->toArray();;
    }

    public function getServiceNames()
    {
        $serviceIds = explode('|', $this->services);
        return MasterServices::whereIn('id', $serviceIds)->pluck('name')->toArray();;
    }
}
