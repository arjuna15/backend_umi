<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EdomQuestion extends Model {
    protected $fillable = ['question', 'category'];
    public function answers() { return $this->hasMany(EdomAnswer::class, 'question_id'); }
}
