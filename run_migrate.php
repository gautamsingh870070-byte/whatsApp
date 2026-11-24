<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$log = \App\Models\WhatsAppLog::latest()->first();
if ($log) {
    echo "Latest API Response: " . json_encode($log->api_response);
} else {
    echo "No logs found.";
}
