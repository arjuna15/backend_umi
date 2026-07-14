<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model {
    protected $fillable = ['skpi_id', 'user_id', 'name', 'category', 'level', 'certificate_path', 'status'];
    public function user() { return $this->belongsTo(User::class); }
    public function skpi() { return $this->belongsTo(Skpi::class); }
}
