<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleOverride extends Model
{
    protected $fillable = [
        'original_schedule_id',
        'override_date',
        'status',
        'swapped_with_schedule_id',
        'new_date',
        'new_time',
        'notes',
    ];

    protected $casts = [
        'override_date' => 'date',
        'new_date' => 'date',
    ];

    public function originalSchedule()
    {
        return $this->belongsTo(Course::class, 'original_schedule_id');
    }

    public function swappedWithSchedule()
    {
        return $this->belongsTo(Course::class, 'swapped_with_schedule_id');
    }
}
