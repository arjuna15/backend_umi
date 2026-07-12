<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumni_id',
        'questionnaire',
        'salary_range',
        'satisfaction',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'questionnaire' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }
}
