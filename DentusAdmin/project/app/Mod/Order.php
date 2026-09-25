<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    function products() {
        return $this->hasMany(OrderProduct::class);
    }

    function addresses() {
        return $this->hasMany(OrderAddress::class);
    }

    function customer() {
        return $this->belongsTo(User::class,'user_id');
    }
}
