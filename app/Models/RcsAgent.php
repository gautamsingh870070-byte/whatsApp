<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcsAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'agent_id', 'brand_id', 'service_account_json',
        'webhook_url', 'webhook_token', 'environment', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'service_account_json' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function templates()
    {
        return $this->hasMany(RcsTemplate::class);
    }
}
