<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Super Admin sees everyone
            $users = User::with('parent')->get();
            $remainingLimit = null; // Unlimited
        } elseif ($user->isAdmin()) {
            // Admins see themselves and the clients they created
            $users = User::with('parent')->where('created_by', $user->id)->orWhere('id', $user->id)->get();
            $usedLimit = User::where('created_by', $user->id)->sum('daily_limit');
            $remainingLimit = max(0, $user->daily_limit - $usedLimit);
        } else {
            // Clients only see themselves
            $users = User::with('parent')->where('id', $user->id)->get();
            $remainingLimit = 0;
        }

        return view('manage-user', compact('users', 'remainingLimit'));
    }
 
    public function store(Request $request)
    {
        if (auth()->user()->isClient()) {
            abort(403, 'Unauthorized action.');
        }

        $allowedRoles = auth()->user()->isSuperAdmin() ? 'admin,client' : 'client';
        $maxLimitRule = '';
        
        if (auth()->user()->isAdmin()) {
            $usedLimit = User::where('created_by', auth()->id())->sum('daily_limit');
            $remainingLimit = max(0, auth()->user()->daily_limit - $usedLimit);
            $maxLimitRule = '|max:' . $remainingLimit;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'daily_limit' => 'required|integer|min:0' . $maxLimitRule,
            'role' => 'required|in:'.$allowedRoles,
        ], [
            'daily_limit.max' => 'You only have ' . ($remainingLimit ?? 'unlimited') . ' daily limits remaining to distribute.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error_type', 'create');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'daily_limit' => $request->daily_limit,
            'role' => $request->role,
            'created_by' => auth()->id(),
        ]);

        $rolePermissionIds = \Illuminate\Support\Facades\DB::table('role_permissions')->where('role_name', $user->role)->pluck('permission_id')->toArray();
        $customPermissionIds = [];
        
        if ($request->has('permissions') && is_array($request->permissions)) {
            $customPermissionIds = \App\Models\Permission::whereIn('name', $request->permissions)->pluck('id')->toArray();
        }
        
        $finalPermissionIds = array_unique(array_merge($rolePermissionIds, $customPermissionIds));
        
        if (!empty($finalPermissionIds)) {
            $user->permissions()->sync($finalPermissionIds);
            
            foreach ($finalPermissionIds as $pId) {
                \App\Models\PermissionAuditLog::create([
                    'target_user_id' => $user->id,
                    'changed_by_user_id' => auth()->id(),
                    'permission_id' => $pId,
                    'action' => 'added',
                ]);
            }
        }

        return redirect()->route('manage.user')->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->isClient() && auth()->id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->isAdmin() && ($user->isSuperAdmin() || ($user->isAdmin() && $user->id !== auth()->id()))) {
            return redirect()->back()->withErrors(['update' => 'You do not have permission to edit this user.']);
        }

        $allowedRoles = auth()->user()->isSuperAdmin() ? 'admin,client' : 'client';
        $maxLimitRule = '';
        
        if (auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            $usedLimit = User::where('created_by', auth()->id())->where('id', '!=', $user->id)->sum('daily_limit');
            $remainingLimit = max(0, auth()->user()->daily_limit - $usedLimit);
            $maxLimitRule = '|max:' . $remainingLimit;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'daily_limit' => 'required|integer|min:0' . $maxLimitRule,
            'role' => 'required|in:'.$allowedRoles,
        ], [
            'daily_limit.max' => 'You only have ' . ($remainingLimit ?? 'unlimited') . ' daily limits remaining to distribute.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error_type', 'update')->with('error_id', $user->id);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        // Admins cannot change their own limit
        if (auth()->user()->isSuperAdmin() || auth()->id() !== $user->id) {
            $user->daily_limit = $request->daily_limit;
        }
        
        // Only superadmins can change roles, admins can only create clients or edit themselves (but not their role)
        if (auth()->user()->isSuperAdmin()) {
            $user->role = $request->role;
        }
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = \App\Models\Permission::whereIn('name', $request->permissions)->pluck('id')->toArray();
            $oldPermissions = $user->permissions->pluck('id')->toArray();
            $user->permissions()->sync($permissionIds);

            $added = array_diff($permissionIds, $oldPermissions);
            $removed = array_diff($oldPermissions, $permissionIds);

            foreach ($added as $pId) {
                \App\Models\PermissionAuditLog::create([
                    'target_user_id' => $user->id,
                    'changed_by_user_id' => auth()->id(),
                    'permission_id' => $pId,
                    'action' => 'added',
                ]);
            }
            foreach ($removed as $pId) {
                \App\Models\PermissionAuditLog::create([
                    'target_user_id' => $user->id,
                    'changed_by_user_id' => auth()->id(),
                    'permission_id' => $pId,
                    'action' => 'removed',
                ]);
            }
        }

        return redirect()->route('manage.user')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        if (auth()->user()->isClient()) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        
        if (auth()->user()->isAdmin() && ($user->isSuperAdmin() || $user->isAdmin())) {
            return redirect()->back()->withErrors(['delete' => 'You do not have permission to delete this user.']);
        }
        
        if (User::count() == 1) {
            return redirect()->back()->withErrors(['delete' => 'Cannot delete the only remaining user.']);
        }
        
        if (auth()->id() == $id) {
            return redirect()->back()->withErrors(['delete' => 'Cannot delete yourself while logged in.']);
        }
        
        $user->delete();

        return redirect()->route('manage.user')->with('success', 'User deleted successfully.');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user->name = $request->name;

        if ($request->hasFile('profile_image')) {
            $request->file('profile_image')->move(public_path('images'), 'profile.jpg');
        }

        if ($request->hasFile('app_logo')) {
            $request->file('app_logo')->move(public_path('images'), 'WABA.jpeg');
        }

        $user->save();

        return redirect()->back()->with('success', 'Changes saved successfully.');
    }

    public function editProfile()
    {
        return view('profile.edit');
    }

    public function editLogo()
    {
        return view('profile.logo');
    }
}
