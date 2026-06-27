<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KrsSubmission extends Model
{
    protected $guarded = [];

    protected $casts = [
        'course_ids' => 'array',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
