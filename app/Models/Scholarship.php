<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = ['name', 'provider', 'discount_type', 'discount_value', 'description', 'is_active'];

    public function studentScholarships()
    {
        return $this->hasMany(StudentScholarship::class);
    }
}
