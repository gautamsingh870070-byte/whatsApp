<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$wa = app(App\Services\WhatsAppService::class);
$result = Http::withToken(env('WHATSAPP_TOKEN'))
    ->withHeaders(['Content-Type' => 'application/json'])
    ->post(env('WHATSAPP_API_URL'), [
        'messaging_product' => 'whatsapp',
        'to' => '919634543853',
        'type' => 'text',
        'text' => ['body' => 'Test message from script']
    ]);

echo json_encode([
    'status' => $result->status(),
    'body' => $result->json()
], JSON_PRETTY_PRINT);
