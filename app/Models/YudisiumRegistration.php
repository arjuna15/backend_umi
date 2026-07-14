<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class YudisiumRegistration extends Model {
    protected $fillable = ['user_id', 'status', 'is_free_billing', 'is_free_library', 'thesis_title', 'gpa', 'thesis_file'];
    protected $casts = ['is_free_billing' => 'boolean', 'is_free_library' => 'boolean', 'gpa' => 'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
    public function wisuda() { return $this->hasOne(WisudaRegistration::class); }
}
