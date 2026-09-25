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

    public function getIsParentAttribute()
    {
        return $this->parent_id == 0 ? 'yes' : 'no';
    }

    public function masterTax()
    {
        return $this->belongsTo(MasterTax::class, 'tax_id');
    }
}
