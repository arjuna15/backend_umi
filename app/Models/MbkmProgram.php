<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MbkmProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'sks',
        'period',
        'status',
    ];

    public function submissions()
    {
        return $this->hasMany(MbkmSubmission::class);
    }
}
