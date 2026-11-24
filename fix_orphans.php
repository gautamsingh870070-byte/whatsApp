<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fix orphaned clients
$admin = \App\Models\User::where('role', 'admin')->first();
if ($admin) {
    \App\Models\User::where('role', 'client')->whereNull('created_by')->update(['created_by' => $admin->id]);
    echo "Fixed orphaned clients to Admin ID: {$admin->id}\n";
}

// Fix missing admin permissions
$admins = \App\Models\User::where('role', 'admin')->get();
foreach ($admins as $adminUser) {
    $rolePerms = \Illuminate\Support\Facades\DB::table('role_permissions')->where('role_name', 'admin')->pluck('permission_id')->toArray();
    $currentPerms = $adminUser->permissions->pluck('id')->toArray();
    $merged = array_unique(array_merge($currentPerms, $rolePerms));
    $adminUser->permissions()->sync($merged);
    echo "Synced full permissions to Admin ID: {$adminUser->id}\n";
}
