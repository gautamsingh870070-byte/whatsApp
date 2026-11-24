<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Run seeder
$seeder = new \Database\Seeders\PermissionSeeder();
$seeder->run();

// Sync all admins and clients with new role defaults so they don't miss out on these new permissions
$users = \App\Models\User::where('role', '!=', 'superadmin')->get();
foreach ($users as $user) {
    $rolePerms = \Illuminate\Support\Facades\DB::table('role_permissions')->where('role_name', $user->role)->pluck('permission_id')->toArray();
    $currentPerms = $user->permissions->pluck('id')->toArray();
    $merged = array_unique(array_merge($currentPerms, $rolePerms));
    $user->permissions()->sync($merged);
    echo "Synced full permissions to User ID: {$user->id}\n";
}
echo "PermissionSeeder executed successfully.\n";
