<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    public function board()
    {
        return $this->belongsTo(Board::class,'board_id');
    }

    public function subjectstream()
    {
        return $this->belongsTo(SubjectStream::class,'stream_id');
    }

    public function getstate()
    {
        return $this->belongsTo(State::class,'state_id');
    }

    public function getcity()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function getschool()
    {
        return $this->belongsTo(School::class,'school_id');
    }

    public function getclass()
    {
        return $this->belongsTo(Class_genre::class,'class_id');
    }

    public function countryget()
    {
        return $this->belongsTo(State::class, Country::class);
    }
}
