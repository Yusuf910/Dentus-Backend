<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstablishmentAddress extends Model
{
    use HasFactory;

    protected $table ='establishment_address';
    protected $fillable = [
        'user_id', 'clinic_name', 'clinic_description', 'address', 'state', 'city', 'pincode', 'latitude', 'longitude'
    ];
}
