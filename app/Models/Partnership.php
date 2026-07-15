<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partnership extends Model
{
    protected $fillable = ['partner_name', 'partner_type', 'mou_number', 'start_date', 'end_date', 'scope', 'status', 'pic_name', 'pic_phone', 'document_path', 'created_by'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
