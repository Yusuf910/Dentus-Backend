<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Treatment extends Model
{
    // use HasFactory;

    protected $fillable = [
        'doctor_id', 'treatment_name', 'average_duration',
        'call_before_confirmation', 'instructions', 'treatment_fees','tax_id'
    ];

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'treatment_doctors', 'treatment_id', 'doctor_id');
    }

    // Add a deleting model event to handle pivot table cleanup
    protected static function booted()
    {
        static::deleting(function ($treatment) {
            // Cascade delete related records in treatment_doctors
            DB::table('treatment_doctors')
                ->where('treatment_id', $treatment->id)
                ->delete();
        });
    }
}

