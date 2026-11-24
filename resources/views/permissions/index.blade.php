@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div class="icon-bg" style="background-color: #fff3e0; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #f57c00;">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
    <div>
        <h3 style="margin: 0; font-size: 18px; color: #333;">Permissions Directory</h3>
        <p style="margin: 0; font-size: 13px; color: #777;">System permissions and role defaults</p>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 24px;">
    
    <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #eee;">
        <h4 style="margin: 0 0 8px 0; color: #333; font-size: 16px;">How Permissions Work</h4>
        <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.5;">
            Permissions control what users can see and do. <strong>Superadmins</strong> automatically have all permissions.<br>
            <strong>Admins</strong> and <strong>Clients</strong> receive default permissions based on their role, but you can override and explicitly assign granular permissions to any user in the <a href="{{ route('manage.user') }}" style="color: #1976d2; text-decoration: none;">Manage Users</a> section by clicking the <i class="fa-solid fa-shield-halved" style="color: #f57c00;"></i> icon.
        </p>
    </div>

    @foreach($permissions as $category => $perms)
        <div style="margin-bottom: 32px;">
            <h5 style="margin: 0 0 16px 0; color: #1976d2; font-size: 16px; border-left: 4px solid #1976d2; padding-left: 12px; background: #f8f9fa; padding: 8px 12px; border-radius: 0 4px 4px 0;">
                {{ $category }}
            </h5>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                @foreach($perms as $perm)
                    <div style="border: 1px solid #eee; padding: 16px; border-radius: 6px; background: #fff;">
                        <div style="font-weight: 600; color: #333; margin-bottom: 4px;">{{ $perm->description ?: $perm->name }}</div>
                        <div style="font-size: 12px; color: #777; font-family: monospace; background: #f5f5f5; padding: 2px 6px; border-radius: 3px; display: inline-block;">{{ $perm->name }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</div>
@endsection
