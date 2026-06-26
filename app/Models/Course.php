<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Course extends Model {
    protected $guarded = [];
    public function dosen() { return $this->belongsTo(User::class, 'dosen_id'); }
    public function grades() { return $this->hasMany(Grade::class); }
}
