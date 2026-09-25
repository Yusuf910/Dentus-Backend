<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(District::class);
    }

    public function stated()
    {
        return $this->belongsTo(State::class,'state_id');
    }

    public function countryd()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
}
