<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'category' => 'Dashboard', 'description' => 'View Dashboard'],
            ['name' => 'statistics.view', 'category' => 'Dashboard', 'description' => 'View Statistics'],
            
            // Users
            ['name' => 'users.view', 'category' => 'Users', 'description' => 'View Users'],
            ['name' => 'users.create', 'category' => 'Users', 'description' => 'Create Admin/Client'],
            ['name' => 'users.edit', 'category' => 'Users', 'description' => 'Edit User'],
            ['name' => 'users.delete', 'category' => 'Users', 'description' => 'Delete User'],
            
            // Permissions
            ['name' => 'permissions.view', 'category' => 'Permissions', 'description' => 'View Permissions'],
            ['name' => 'permissions.assign', 'category' => 'Permissions', 'description' => 'Assign/Edit Permissions'],
            
            // Messaging
            ['name' => 'messages.send', 'category' => 'Messaging', 'description' => 'Send Messages/Campaigns'],
            
            // Templates
            ['name' => 'templates.view', 'category' => 'Templates', 'description' => 'View WA Templates'],
            ['name' => 'templates.create', 'category' => 'Templates', 'description' => 'Create WA Templates'],
            
            // Communication (Live Chat)
            ['name' => 'livechat.view', 'category' => 'Communication', 'description' => 'View Live Chat'],
            ['name' => 'livechat.reply', 'category' => 'Communication', 'description' => 'Reply/Forward in Live Chat'],
            ['name' => 'livechat.manage', 'category' => 'Communication', 'description' => 'Edit/Block/Mark Read in Live Chat'],
            ['name' => 'livechat.delete', 'category' => 'Communication', 'description' => 'Delete Live Chat Messages'],
            ['name' => 'livechat.export', 'category' => 'Communication', 'description' => 'Export Live Chat'],
            
            // Contacts
            ['name' => 'contacts.view', 'category' => 'Contacts', 'description' => 'View Contacts'],
            ['name' => 'contacts.create', 'category' => 'Contacts', 'description' => 'Create Contact'],
            ['name' => 'contacts.edit', 'category' => 'Contacts', 'description' => 'Edit Contact'],
            ['name' => 'contacts.delete', 'category' => 'Contacts', 'description' => 'Delete Contact'],
            
            // Credits
            ['name' => 'credits.view', 'category' => 'Credits', 'description' => 'View Credits'],
            ['name' => 'credits.manage', 'category' => 'Credits', 'description' => 'Manage/Allocate Credits'],
            
            // Reports & Logs
            ['name' => 'history.view', 'category' => 'Reports', 'description' => 'View History'],
            ['name' => 'schedule_log.view', 'category' => 'Reports', 'description' => 'View Schedule Log'],
            ['name' => 'credit_history.view', 'category' => 'Reports', 'description' => 'View Credit History'],
            
            // Developer
            ['name' => 'developer.view', 'category' => 'Developer', 'description' => 'Developer Access / API'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(['name' => $p['name']], $p);
        }

        // Default Admin Permissions
        $adminPermissions = [
            'dashboard.view', 'statistics.view', 'users.view', 'users.create', 'users.edit', 'users.delete', 
            'permissions.view', 'permissions.assign', 'messages.send', 
            'templates.view', 'templates.create', 
            'livechat.view', 'livechat.reply', 'livechat.manage', 'livechat.delete', 'livechat.export',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'history.view', 'schedule_log.view', 'credit_history.view', 'developer.view'
        ];
        foreach ($adminPermissions as $ap) {
            $pid = DB::table('permissions')->where('name', $ap)->value('id');
            if ($pid) {
                DB::table('role_permissions')->updateOrInsert([
                    'role_name' => 'admin',
                    'permission_id' => $pid
                ]);
            }
        }

        // Default Client Permissions
        $clientPermissions = [
            'dashboard.view', 'messages.send', 
            'templates.view', 'templates.create', 
            'livechat.view', 'livechat.reply', 'livechat.manage', 'livechat.delete', 'livechat.export',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'history.view', 'schedule_log.view', 'credit_history.view', 'developer.view'
        ];
        foreach ($clientPermissions as $cp) {
            $pid = DB::table('permissions')->where('name', $cp)->value('id');
            if ($pid) {
                DB::table('role_permissions')->updateOrInsert([
                    'role_name' => 'client',
                    'permission_id' => $pid
                ]);
            }
        }
    }
}
