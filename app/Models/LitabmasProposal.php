<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LitabmasProposal extends Model {
    protected $fillable = ['user_id', 'type', 'title', 'abstract', 'budget', 'proposal_file', 'status', 'reviewer_notes'];
    protected $casts = ['budget' => 'decimal:2'];
    public function user() { return $this->belongsTo(User::class); }
}
