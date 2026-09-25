<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'start_date',
        'end_date',
        'payment_id',
        'status'
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function premiumAddons()
    {
        return $this->hasMany(UserPremiumAddon::class, 'subscription_id')
                    ->where('premium_feature_id', 1);
    }
}
