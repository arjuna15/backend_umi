<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = ['name', 'token', 'permissions', 'rate_limit', 'is_active', 'last_used_at', 'created_by', 'expires_at'];

    protected $casts = ['permissions' => 'array', 'is_active' => 'boolean', 'last_used_at' => 'datetime', 'expires_at' => 'datetime'];

    protected $hidden = ['token'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
