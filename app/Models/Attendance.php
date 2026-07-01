<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'meeting_number', 'date', 'mode'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
