<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\Api\WhatsappApiController;

/*
|--------------------------------------------------------------------------
| WhatsApp Webhook Routes (Public — no auth middleware)
|--------------------------------------------------------------------------
|
| GET  /api/whatsapp/webhook  → Meta verification challenge
| POST /api/whatsapp/webhook  → Incoming messages & status updates
|
*/

Route::get('/whatsapp/webhook',  [WhatsAppWebhookController::class, 'verify']);
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'receive']);

Route::get('/whatsappApi',  [WhatsappApiController::class, 'whatsappApi']);
