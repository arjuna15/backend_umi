<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attendance_weight' => 'float',
        'assignment_weight' => 'float',
        'uts_weight' => 'float',
        'uas_weight' => 'float',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
