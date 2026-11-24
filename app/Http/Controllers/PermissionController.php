<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use App\Models\PermissionAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all()->groupBy('category');
        return view('permissions.index', compact('permissions'));
    }

    public function getUserPermissions($userId)
    {
        $targetUser = User::findOrFail($userId);
        
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && $currentUser->id != $userId && !$currentUser->canManageClient($userId)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $allPermissions = Permission::all()->groupBy('category');
        
        // Get user's specific permissions. Since role defaults are now copied to user_permissions, this is the exact truth.
        $userPermissions = $targetUser->permissions->pluck('name')->toArray();
        
        // Get default role permissions just to show the UI which ones are considered defaults
        $rolePermissions = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role_name', $targetUser->role)
            ->pluck('permissions.name')
            ->toArray();

        // Active permissions is strictly what the user has.
        $activePermissions = $userPermissions;

        return response()->json([
            'success' => true,
            'permissions' => $allPermissions,
            'active_permissions' => $activePermissions,
            'user_permissions' => $userPermissions, // Specifically assigned
            'role_permissions' => $rolePermissions, // Defaults
        ]);
    }

    public function updateUserPermissions(Request $request, $userId)
    {
        $targetUser = User::findOrFail($userId);
        $currentUser = Auth::user();

        if (!$currentUser->isSuperAdmin() && !$currentUser->canManageClient($userId)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only superadmin can change another admin's permissions
        if ($targetUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Only Superadmin can manage Admin permissions.'], 403);
        }

        if ($targetUser->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Superadmin permissions cannot be modified.'], 403);
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $requestedPermissions = $request->input('permissions', []);
        
        // To save, we map names to IDs
        $permissionIds = Permission::whereIn('name', $requestedPermissions)->pluck('id')->toArray();

        $oldPermissions = $targetUser->permissions->pluck('id')->toArray();

        // Sync user permissions
        $targetUser->permissions()->sync($permissionIds);

        // Audit Logs
        $added = array_diff($permissionIds, $oldPermissions);
        $removed = array_diff($oldPermissions, $permissionIds);

        foreach ($added as $pId) {
            PermissionAuditLog::create([
                'target_user_id' => $targetUser->id,
                'changed_by_user_id' => $currentUser->id,
                'permission_id' => $pId,
                'action' => 'added',
            ]);
        }

        foreach ($removed as $pId) {
            PermissionAuditLog::create([
                'target_user_id' => $targetUser->id,
                'changed_by_user_id' => $currentUser->id,
                'permission_id' => $pId,
                'action' => 'removed',
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Permissions updated successfully.']);
        }

        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }
}
