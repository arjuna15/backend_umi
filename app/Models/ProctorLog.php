<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProctorLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'proctor_session_id',
        'user_id',
        'event',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function session()
    {
        return $this->belongsTo(ProctorSession::class, 'proctor_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
