<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$templates = \App\Models\UserTemplate::orderBy('id', 'desc')->take(5)->get();
foreach ($templates as $t) {
    echo "ID: {$t->id}, Name: {$t->template_name}\n";
    $comp = json_decode($t->components, true);
    echo "Local Image: " . ($comp['local_image_path'] ?? 'NULL') . "\n";
    echo "--------------------------\n";
}
