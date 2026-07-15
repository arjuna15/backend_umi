<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpgActivity extends Model
{
    protected $fillable = ['ppg_participant_id', 'activity_type', 'title', 'date', 'score', 'notes'];

    protected $casts = ['date' => 'date', 'score' => 'decimal:2'];

    public function participant() { return $this->belongsTo(PpgParticipant::class, 'ppg_participant_id'); }
}
