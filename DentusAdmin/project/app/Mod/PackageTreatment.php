<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageTreatment extends Model
{
    use HasFactory;

    protected $table = 'package_treatments';

    protected $fillable = ['package_id', 'treatment_id', 'sessions'];
}
