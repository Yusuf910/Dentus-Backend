<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentDoctor extends Model
{
    use HasFactory;

    protected $table = 'treatment_doctors';

    protected $fillable = ['treatment_id', 'doctor_id'];

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
