<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SpmiDocument extends Model {
    protected $fillable = ['title', 'category', 'file_path', 'academic_year'];
}
