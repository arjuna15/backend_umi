<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'mahasiswa_id',
        'score',
        'answers'
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'float'
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
