<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RplDocument extends Model
{
    protected $fillable = ['rpl_application_id', 'type', 'file_path', 'original_name'];

    public function application() { return $this->belongsTo(RplApplication::class, 'rpl_application_id'); }
}
