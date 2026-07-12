<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmbDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'pmb_applicant_id',
        'type',
        'file_path',
        'original_name',
        'uploaded_at',
    ];

    protected $appends = ['file_url'];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function getFileUrlAttribute(): string
    {
        if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
            return $this->file_path;
        }
        return url('storage/' . $this->file_path);
    }

    public function applicant()
    {
        return $this->belongsTo(PmbApplicant::class, 'pmb_applicant_id');
    }
}
