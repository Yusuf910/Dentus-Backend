<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'package_name', 'validity_period', 'base_price', 'discount_price'
    ];

    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'package_treatments')
                    ->withPivot('sessions');
    }
}
