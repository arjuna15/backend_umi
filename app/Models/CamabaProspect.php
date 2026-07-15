<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamabaProspect extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'school_origin', 'program_interest', 'source', 'status', 'follow_up_date', 'notes', 'handled_by'];

    protected $casts = ['follow_up_date' => 'date'];

    public function handler() { return $this->belongsTo(User::class, 'handled_by'); }

    public function followups() { return $this->hasMany(CamabaFollowup::class, 'prospect_id'); }
}
