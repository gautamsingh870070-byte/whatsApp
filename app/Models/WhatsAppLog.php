<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'user_id',
        'phone_number',
        'template_name',
        'message_type',
        'status',
        'api_response',
    ];

    protected $casts = [
        'api_response' => 'array',
    ];
}
