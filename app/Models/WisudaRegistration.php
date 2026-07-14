<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WisudaRegistration extends Model {
    protected $fillable = ['yudisium_registration_id', 'toga_size', 'status', 'seat_number'];
    public function yudisium() { return $this->belongsTo(YudisiumRegistration::class, 'yudisium_registration_id'); }
}
