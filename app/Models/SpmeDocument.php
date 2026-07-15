<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpmeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'status',
        'year',
        'upload_date',
        'file_path',
    ];
}
