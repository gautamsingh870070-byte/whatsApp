<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$t1 = \App\Models\UserTemplate::where('template_name', 'stm_festival_pooja_booking')->first();
if ($t1) {
    $t1->components = json_encode(['local_image_path' => 'template_images/9pIjLYIaH8bJIeiPlQTa9ZG2QMJHrOyAUvga7PXC.jpg']);
    $t1->save();
}

$t2 = \App\Models\UserTemplate::where('template_name', 'stm_panditji_special_offer')->first();
if ($t2) {
    $t2->components = json_encode(['local_image_path' => 'template_images/N4yU6MTlM7GPTYOEfGbXQRDyKQr7gyPBuVZJdvre.png']);
    $t2->save();
}

echo "Fixed!\n";
