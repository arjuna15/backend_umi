<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmbApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'pmb_period_id',
        'registration_number',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'school_origin',
        'program_choice',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function period()
    {
        return $this->belongsTo(PmbPeriod::class, 'pmb_period_id');
    }

    public function documents()
    {
        return $this->hasMany(PmbDocument::class);
    }
}
