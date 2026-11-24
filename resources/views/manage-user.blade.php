@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div class="icon-bg" style="background-color: #e3f2fd; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #1976d2;">
        <i class="fa-solid fa-user-tie"></i>
    </div>
    <div>
        <h3 style="margin: 0; font-size: 18px; color: #333;">Manage Users</h3>
        <p style="margin: 0; font-size: 13px; color: #777;">Control user access and permissions</p>
    </div>
</div>

@if(session('success'))
<div style="background-color: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-weight: 500;">
    {{ session('success') }}
</div>
@endif

@if($errors->has('delete'))
<div style="background-color: #ffebee; color: #c62828; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-weight: 500;">
    {{ $errors->first('delete') }}
</div>
@endif

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h4 style="margin: 0; font-size: 16px; color: #333; display: inline-block;">Active Users</h4>
            @if(isset($remainingLimit) && Auth::user()->isAdmin())
            <span style="margin-left: 12px; background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                Available Limit: {{ $remainingLimit }}
            </span>
            @endif
        </div>
        @if(!Auth::user()->isClient())
        <button onclick="document.getElementById('add-user-modal').style.display='block'" class="btn-primary" style="background-color: #1976d2; border: none; color: white; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500;">
            <i class="fa-solid fa-plus"></i> Add New User
        </button>
        @endif
    </div>
    
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Name</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Email</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Role</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Created By</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Daily Limit</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px 16px; font-weight: 500; color: #333;">{{ $user->name }}</td>
                <td style="padding: 12px 16px; color: #666;">{{ $user->email }}</td>
                <td style="padding: 12px 16px;">
                    <span style="background: {{ $user->role === 'superadmin' ? '#ffebee' : ($user->role === 'admin' ? '#e3f2fd' : '#f5f5f5') }}; color: {{ $user->role === 'superadmin' ? '#c62828' : ($user->role === 'admin' ? '#1976d2' : '#616161') }}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: capitalize;">{{ $user->role }}</span>
                </td>
                <td style="padding: 12px 16px; color: #666; font-size: 13px;">
                    {{ $user->parent ? $user->parent->name : 'System' }}
                </td>
                <td style="padding: 12px 16px; color: #666;">
                    <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ $user->daily_limit }}</span>
                </td>
                <td style="padding: 12px 16px;">
                    @if(Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && $user->role === 'client') || Auth::id() === $user->id)
                    <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', {{ $user->daily_limit }})" style="background: none; border: none; color: #1976d2; margin-right: 12px; cursor: pointer;" title="Edit"><i class="fa-solid fa-pen"></i></button>
                    @endif
                    
                    @if(Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && $user->role === 'client'))
                    <button onclick="openPermissionsModal({{ $user->id }}, '{{ $user->name }}')" style="background: none; border: none; color: #f57c00; margin-right: 12px; cursor: pointer;" title="Manage Permissions"><i class="fa-solid fa-shield-halved"></i></button>
                    @endif
                    
                    @if(Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && $user->role === 'client'))
                    <form action="{{ route('manage.user.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #d32f2f; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" style="display: {{ session('error_type') == 'create' ? 'block' : 'none' }}; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto;">
    <div style="background: #fff; max-width: 500px; margin: 50px auto; border-radius: 8px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Add New User</h3>
            <button onclick="document.getElementById('add-user-modal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('manage.user.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Name</label>
                <input type="text" name="name" value="{{ session('error_type') == 'create' ? old('name') : '' }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'create' && $errors->has('name'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('name') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Email</label>
                <input type="email" name="email" value="{{ session('error_type') == 'create' ? old('email') : '' }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'create' && $errors->has('email'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'create' && $errors->has('password'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Role</label>
                <select name="role" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: white;">
                    <option value="client" {{ (session('error_type') == 'create' && old('role') == 'client') ? 'selected' : '' }}>Client</option>
                    @if(Auth::user()->isSuperAdmin())
                    <option value="admin" {{ (session('error_type') == 'create' && old('role') == 'admin') ? 'selected' : '' }}>Admin</option>
                    @endif
                </select>
                @if(session('error_type') == 'create' && $errors->has('role'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('role') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Daily Limit</label>
                <input type="number" name="daily_limit" value="{{ session('error_type') == 'create' ? old('daily_limit') : '500' }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'create' && $errors->has('daily_limit'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('daily_limit') }}</span>
                @endif
            </div>

            <!-- <div style="margin-bottom: 24px; border: 1px solid #eee; padding: 16px; border-radius: 6px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 500; color: #333;"><i class="fa-solid fa-shield-halved" style="color: #f57c00; margin-right: 8px;"></i> Access & Permissions</span>
                    <button type="button" onclick="document.getElementById('create-permissions-section').style.display = document.getElementById('create-permissions-section').style.display === 'none' ? 'block' : 'none';" style="background: #f5f5f5; border: 1px solid #ddd; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">Manage Permissions</button>
                </div>
                <div id="create-permissions-section" style="display: none; margin-top: 16px;">
                    <div style="margin-bottom: 12px; display: flex; gap: 12px;">
                        <button type="button" onclick="toggleAllCreatePermissions(true)" style="background: none; border: none; color: #1976d2; font-size: 13px; cursor: pointer;">Select All</button>
                        <button type="button" onclick="toggleAllCreatePermissions(false)" style="background: none; border: none; color: #d32f2f; font-size: 13px; cursor: pointer;">Deselect All</button>
                    </div>
                    <div id="create-permissions-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 14px;">
                        <p style="color: #666; grid-column: span 2;">Role defaults will be applied automatically unless explicitly selected here.</p>
                    </div>
                </div>
            </div>-->

            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('add-user-modal').style.display='none'" style="background: none; border: 1px solid #ddd; padding: 10px 20px; border-radius: 4px; margin-right: 8px; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: #1976d2; border: none; color: white; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" style="display: {{ session('error_type') == 'update' ? 'block' : 'none' }}; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto;">
    <div style="background: #fff; max-width: 500px; margin: 50px auto; border-radius: 8px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Edit User</h3>
            <button onclick="document.getElementById('edit-user-modal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>

        <form id="edit-user-form" action="{{ session('error_type') == 'update' ? route('manage.user.update', session('error_id')) : '#' }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Name</label>
                <input type="text" id="edit-name" name="name" value="{{ session('error_type') == 'update' ? old('name') : '' }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'update' && $errors->has('name'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('name') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Email</label>
                <input type="email" id="edit-email" name="email" value="{{ session('error_type') == 'update' ? old('email') : '' }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'update' && $errors->has('email'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Password <span style="font-size: 12px; color: #777;">(Leave blank to keep current)</span></label>
                <input type="password" name="password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'update' && $errors->has('password'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Role</label>
                <select id="edit-role" name="role" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: white;" {{ Auth::user()->isSuperAdmin() ? '' : 'disabled' }}>
                    <option value="client">Client</option>
                    @if(Auth::user()->isSuperAdmin())
                    <option value="admin">Admin</option>
                    @endif
                </select>
                @if(!Auth::user()->isSuperAdmin())
                <input type="hidden" id="edit-role-hidden" name="role" value="client">
                @endif
                @if(session('error_type') == 'update' && $errors->has('role'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('role') }}</span>
                @endif
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Daily Limit</label>
                <input type="number" id="edit-limit" name="daily_limit" value="{{ session('error_type') == 'update' ? old('daily_limit') : '' }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if(session('error_type') == 'update' && $errors->has('daily_limit'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('daily_limit') }}</span>
                @endif
            </div>

            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('edit-user-modal').style.display='none'" style="background: none; border: 1px solid #ddd; padding: 10px 20px; border-radius: 4px; margin-right: 8px; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: #1976d2; border: none; color: white; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Update User</button>
            </div>
        </form>
    </div>
</div>

<div id="permissions-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto;">
    <div style="background: #fff; max-width: 600px; margin: 50px auto; border-radius: 8px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Manage Permissions: <span id="perm-user-name" style="color: #1976d2;"></span></h3>
            <button onclick="document.getElementById('permissions-modal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>

        <form id="permissions-form" onsubmit="savePermissions(event)">
            <input type="hidden" id="perm-user-id">
            
            <div style="margin-bottom: 16px; display: flex; gap: 12px; background: #f9f9f9; padding: 10px; border-radius: 6px;">
                <button type="button" onclick="toggleAllModalPermissions(true)" class="btn-primary" style="background: #e3f2fd; border: none; color: #1976d2; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;">Select All</button>
                <button type="button" onclick="toggleAllModalPermissions(false)" style="background: #ffebee; border: none; color: #c62828; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 500;">Deselect All</button>
            </div>

            <div id="permissions-content">
                <div style="text-align: center; padding: 20px; color: #666;">Loading permissions...</div>
            </div>

            <div style="text-align: right; margin-top: 24px; border-top: 1px solid #eee; padding-top: 16px;">
                <button type="button" onclick="document.getElementById('permissions-modal').style.display='none'" style="background: none; border: 1px solid #ddd; padding: 10px 20px; border-radius: 4px; margin-right: 8px; cursor: pointer;">Cancel</button>
                <button type="submit" id="save-permissions-btn" style="background: #1976d2; border: none; color: white; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Save Permissions</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, email, role, limit) {
        document.getElementById('edit-user-form').action = '/manage-user/' + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = role;
        
        @if(!Auth::user()->isSuperAdmin())
        document.getElementById('edit-role-hidden').value = role;
        @endif
        
        document.getElementById('edit-limit').value = limit;
        document.getElementById('edit-user-modal').style.display = 'block';
    }

    function toggleAllCreatePermissions(check) {
        document.querySelectorAll('#create-permissions-container input[type="checkbox"]').forEach(el => el.checked = check);
    }
    
    function toggleAllModalPermissions(check) {
        document.querySelectorAll('#permissions-content input[type="checkbox"]').forEach(el => el.checked = check);
    }

    let allAvailablePermissions = null;

    @if(!Auth::user()->isClient())
    // Fetch template permissions on page load for the create form
    fetch('/permissions/user/{{ Auth::id() }}')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                allAvailablePermissions = data.permissions;
                let html = '';
                for (const [category, perms] of Object.entries(data.permissions)) {
                    html += `<div style="margin-bottom: 12px;">`;
                    html += `<h5 style="margin: 0 0 8px 0; color: #555; border-bottom: 1px solid #eee; padding-bottom: 4px;">${category}</h5>`;
                    perms.forEach(p => {
                        html += `<label style="display: block; margin-bottom: 4px; color: #444; font-size: 13px;">`;
                        html += `<input type="checkbox" name="permissions[]" value="${p.name}" style="margin-right: 6px;"> ${p.description || p.name}`;
                        html += `</label>`;
                    });
                    html += `</div>`;
                }
                const container = document.getElementById('create-permissions-container');
                if (container) {
                    container.innerHTML = html;
                }
            }
        });
    @endif

    function openPermissionsModal(id, name) {
        document.getElementById('perm-user-name').innerText = name;
        document.getElementById('perm-user-id').value = id;
        document.getElementById('permissions-modal').style.display = 'block';
        document.getElementById('permissions-content').innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Loading permissions...</div>';

        fetch('/permissions/user/' + id)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message);
                    document.getElementById('permissions-modal').style.display = 'none';
                    return;
                }

                let html = '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">';
                for (const [category, perms] of Object.entries(data.permissions)) {
                    html += `<div>`;
                    html += `<h4 style="margin: 0 0 8px 0; color: #333; font-size: 15px; border-bottom: 1px solid #eee; padding-bottom: 4px;">${category}</h4>`;
                    perms.forEach(p => {
                        const isChecked = data.active_permissions.includes(p.name) ? 'checked' : '';
                        const isRoleDefault = data.role_permissions.includes(p.name);
                        const labelStyle = isRoleDefault ? 'color: #1976d2;' : 'color: #444;';
                        
                        html += `<label style="display: flex; align-items: flex-start; margin-bottom: 6px; font-size: 14px;">`;
                        html += `<input type="checkbox" name="modal_permissions[]" value="${p.name}" ${isChecked} style="margin-right: 8px; margin-top: 2px;">`;
                        html += `<div><span style="${labelStyle}">${p.description || p.name}</span>`;
                        if (isRoleDefault) html += ` <span style="font-size: 11px; background: #e3f2fd; padding: 1px 4px; border-radius: 3px; color: #1976d2;">Default</span>`;
                        html += `</div></label>`;
                    });
                    html += `</div>`;
                }
                html += '</div>';
                document.getElementById('permissions-content').innerHTML = html;
            });
    }

    function savePermissions(e) {
        e.preventDefault();
        const id = document.getElementById('perm-user-id').value;
        const btn = document.getElementById('save-permissions-btn');
        btn.innerText = 'Saving...';
        btn.disabled = true;

        const checkedBoxes = document.querySelectorAll('#permissions-content input[name="modal_permissions[]"]:checked');
        const permissions = Array.from(checkedBoxes).map(cb => cb.value);

        fetch('/permissions/user/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ permissions: permissions })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerText = 'Save Permissions';
            btn.disabled = false;
            if (data.success) {
                alert('Permissions updated successfully!');
                document.getElementById('permissions-modal').style.display = 'none';
            } else {
                alert(data.message || 'Error saving permissions');
            }
        })
        .catch(err => {
            btn.innerText = 'Save Permissions';
            btn.disabled = false;
            alert('An error occurred.');
        });
    }
</script>
@endsection
