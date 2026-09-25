<?php
namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'plan_id', 'subscribed_at'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
