<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFeatureMapping extends Model
{
    use HasFactory;

    protected $fillable = ['subscription_id', 'feature_id'];
}
