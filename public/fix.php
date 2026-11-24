<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    $log = \App\Models\WhatsAppLog::latest()->first();
    if ($log) {
        echo "<h3>Latest API Response:</h3><pre>" . json_encode($log->api_response, JSON_PRETTY_PRINT) . "</pre>";
        echo "<h3>Target Phone:</h3>" . $log->phone_number;
        echo "<h3>Message Type:</h3>" . $log->message_type;
    } else {
        echo "No logs found.";
    }
