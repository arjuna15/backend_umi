<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EdomAnswer extends Model {
    protected $fillable = ['question_id', 'user_id', 'course_id', 'dosen_id', 'score', 'comments'];
    public function question() { return $this->belongsTo(EdomQuestion::class, 'question_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function dosen() { return $this->belongsTo(User::class, 'dosen_id'); }
}
