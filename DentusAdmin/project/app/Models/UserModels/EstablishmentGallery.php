<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstablishmentGallery extends Model
{
    use HasFactory;

    protected $table ='establishment_gallery';
    protected $fillable = [
        'user_id', 'image'
    ];
}
