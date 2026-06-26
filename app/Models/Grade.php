<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Grade extends Model {
    protected $guarded = [];
    public function mahasiswa() { return $this->belongsTo(User::class, 'mahasiswa_id'); }
    public function course() { return $this->belongsTo(Course::class); }
}
