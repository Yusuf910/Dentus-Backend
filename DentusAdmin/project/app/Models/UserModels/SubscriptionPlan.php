<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    protected $table="subscribe_plan";


    protected $fillable = [
        'plan_id',
        'user_id',
        'user_type',
        'amount',
        'job_id',
        'validity',
        'start_day',
        'end_day',
        'remaining_call',
        'remaining_job',
        'remaining_find_worker',
        'txn_id',
        'status',
        'created_at',
        'updated_at'
    ];

    function Subscription() {
        return $this->belongsTo(Subscription::class, 'plan_id');
    }

    function User() {
        return $this->belongsTo(User::class);
    }

}
