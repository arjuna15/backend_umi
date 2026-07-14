<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Skpi extends Model {
    protected $fillable = ['user_id', 'status', 'approved_by', 'approved_at', 'notes'];
    protected $casts = ['approved_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function prestasis() { return $this->hasMany(Prestasi::class); }
}
