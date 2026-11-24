<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcsTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'rcs_agent_id', 'name', 'type', 'content', 'media_url', 'suggestions', 'is_approved'
    ];

    protected $casts = [
        'suggestions' => 'array',
        'is_approved' => 'boolean',
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
