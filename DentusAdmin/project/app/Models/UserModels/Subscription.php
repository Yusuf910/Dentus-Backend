<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    public function features()
    {
        return $this->belongsToMany(SubscriptionFeatures::class, 'subscription_feature_mappings', 'subscription_id', 'feature_id');
    }
}
