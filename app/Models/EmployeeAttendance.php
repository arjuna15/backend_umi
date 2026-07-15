<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'status', 'notes'];

    protected $casts = ['date' => 'date'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
