<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'aspect',
        'rating',
        'respondents_count',
    ];
}
