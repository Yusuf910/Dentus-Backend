<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class MasterTrade extends Model
{
    use HasFactory;

    protected $table = 'master_trades';

    protected $fillable = [
        'name',
	'projectType_id',
        'position',
        'image',
        'status',
        'created_at',
        'updated_at',
        'code',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return URL::to('/').'/adminassets/images/trades/'.$this->image;
    }

}
