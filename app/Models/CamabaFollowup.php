<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamabaFollowup extends Model
{
    protected $fillable = ['prospect_id', 'user_id', 'method', 'notes', 'followed_at'];

    protected $casts = ['followed_at' => 'datetime'];

    public function prospect() { return $this->belongsTo(CamabaProspect::class, 'prospect_id'); }

    public function user() { return $this->belongsTo(User::class); }
}
