<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id', 'nip', 'name', 'position', 'department', 'employment_type', 'join_date', 'phone', 'email', 'salary', 'status'];

    protected $casts = ['salary' => 'decimal:2', 'join_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }

    public function attendances() { return $this->hasMany(EmployeeAttendance::class); }
}
