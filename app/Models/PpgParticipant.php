<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpgParticipant extends Model
{
    protected $fillable = ['user_id', 'name', 'nip', 'school_origin', 'subject', 'batch', 'status', 'start_date', 'end_date', 'certificate_number'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }

    public function activities() { return $this->hasMany(PpgActivity::class); }
}
