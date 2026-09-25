<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'treatment_name', 'average_duration',
        'call_before_confirmation', 'instructions', 'treatment_fees'
    ];

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'treatment_doctors', 'treatment_id', 'doctor_id');
    }
}
