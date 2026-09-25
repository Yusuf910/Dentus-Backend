<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $table ='educations';
    protected $fillable = [
        'user_id', 'degree', 'college_institute', 'year_of_completion', 'years_of_experience'
    ];
}
