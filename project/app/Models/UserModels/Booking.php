<?php

namespace App\Models\UserModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\UserEstablishmentClinic;
use App\Models\Prescription;
class Booking extends Model
{
    protected $connection = 'mysql2'; // Use the second database connection
    protected $table = 'bookings'; // Specify your table name

    // Add the fields that you want to be mass assignable
    protected $fillable = [
        'doctor_id',
        'user_id',
        'consultation_type',
        'appointment_type',
        'treatment_id',
        'member_id',
        'member_type',
        'symptoms',
        'clinic_id',
        'schedule_date',
        'schedule_time',
        'subtotal',
        'gst',
        'start_time',
        'end_time',
        'payment_mode',
        'status',
        'plan_id',
        'loyality_points',
        'partial_amount',
        'total_amount',
        'is_paid',
        'coupan_code_id',
        'discount_amount',
        'payid',

    ];

    protected $attributes = [
        'plan_id'=>0,
        'loyality_points'=>0,
        'partial_amount'=>0,
        'total_amount'=>0,
        'is_paid'=>0,
        'coupan_code_id'=>0,
        'discount_amount'=>0
    ];

    use HasApiTokens, HasFactory, Notifiable;

    public function doctordetail()
    {
        $doctor = $this->belongsTo(User::class, 'doctor_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $doctor;
    }

    public function doctor()
    {
        $doctor = $this->belongsTo(User::class, 'doctor_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $doctor;
    }

    public function clinicdetail()
    {
        $clinic = $this->belongsTo(UserEstablishmentClinic::class, 'clinic_id');
        // if ($clinic) {
        //     $clinic->setConnection('mysql');
        // }
        return $clinic;
    }

    public function userdetail()
    {
        $user = $this->belongsTo(UserProfile::class, 'user_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $user;
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'booking_id');
    }

    public function loyalitydetail()
    {
        $user = $this->belongsTo(UserPayment::class, 'id','booking_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $user;
    }

    public function memberdetail()
    {
        $user = $this->belongsTo(Member::class, 'member_id');
        // if ($doctor) {
        //     $doctor->setConnection('mysql');
        // }
        return $user;
    }
}
