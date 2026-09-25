<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFeatures extends Model
{
    use HasFactory;

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_feature_mappings', 'feature_id', 'subscription_id');
    }
}
