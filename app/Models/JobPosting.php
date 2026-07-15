<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $fillable = ['company_name', 'position_title', 'location', 'employment_type', 'salary_range', 'description', 'requirements', 'deadline', 'contact_email', 'status', 'posted_by'];

    protected $casts = ['deadline' => 'date'];

    public function poster() { return $this->belongsTo(User::class, 'posted_by'); }

    public function applications() { return $this->hasMany(JobApplication::class); }
}
