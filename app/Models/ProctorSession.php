<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProctorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'token',
        'started_at',
        'ended_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function logs()
    {
        return $this->hasMany(ProctorLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
