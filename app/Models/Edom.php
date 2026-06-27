<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edom extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
