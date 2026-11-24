<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'rcs_agent_id', 'google_message_id', 'customer_number',
        'content', 'status', 'failure_reason', 'sent_at', 'delivered_at', 'read_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(RcsAgent::class, 'rcs_agent_id');
    }
}
