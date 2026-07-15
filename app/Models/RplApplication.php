<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RplApplication extends Model
{
    protected $fillable = ['user_id', 'applicant_name', 'email', 'phone', 'previous_institution', 'previous_program', 'target_program', 'work_experience_years', 'status', 'credits_recognized', 'reviewer_notes', 'reviewed_by', 'document_path'];

    public function user() { return $this->belongsTo(User::class); }

    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function documents() { return $this->hasMany(RplDocument::class); }
}
