<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$t = \App\Models\UserTemplate::where('template_name', 'stm_festival_pooja_booking')->first();
echo $t->components;
