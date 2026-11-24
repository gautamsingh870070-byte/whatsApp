<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = \App\Models\User::where('role', 'admin')->first();
$hasCustom = $admin->permissions()->exists();
echo "Admin ID: {$admin->id}\n";
echo "Has Custom Permissions: " . ($hasCustom ? 'true' : 'false') . "\n";
$rolePerm = \Illuminate\Support\Facades\DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('role_permissions.role_name', 'admin')
                ->where('permissions.name', 'permissions.assign')
                ->exists();
echo "Role has permissions.assign: " . ($rolePerm ? 'true' : 'false') . "\n";
echo "Actual assigned permissions in user_permissions: \n";
print_r($admin->permissions->pluck('name')->toArray());
