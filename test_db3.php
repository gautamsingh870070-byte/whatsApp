<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$templates = \App\Models\UserTemplate::all();
foreach ($templates as $t) {
    echo $t->id . ' - ' . $t->template_name . "\n";
    echo $t->components . "\n\n";
}
