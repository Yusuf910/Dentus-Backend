<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPremiumAddon extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'subscription_id',
        'premium_feature_id',
        'payment_id',
        'unlimited',
        'quantity',
        'price'
        
        
    ];

    protected $guarded = [];

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }
}
