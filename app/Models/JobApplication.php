<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = ['job_posting_id', 'user_id', 'resume_path', 'cover_letter', 'status', 'applied_at'];

    protected $casts = ['applied_at' => 'datetime'];

    public function jobPosting() { return $this->belongsTo(JobPosting::class); }

    public function user() { return $this->belongsTo(User::class); }
}
