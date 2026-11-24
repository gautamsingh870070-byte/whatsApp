@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Update Profile</h2>
        <p style="color: #64748b; font-size: 14px;">Update your account's profile information and profile image.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #dcfce7; color: #16a34a; padding: 12px 16px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; border: 1px solid #fecaca; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Profile Name</label>
                <input type="text" name="name" style="width: 100%; padding: 10px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; outline: none; transition: border-color 0.2s;" value="{{ Auth::user()->name ?? '' }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Profile Image (Top Right)</label>
                <input type="file" name="profile_image" style="width: 100%; padding: 10px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; outline: none; transition: border-color 0.2s; background: #f8fafc;" accept=".jpg,.jpeg,.png">
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 6px;"><i class="fa-solid fa-circle-info"></i> Leave empty to keep current image.</small>
            </div>
            
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary" style="background-color: #10b981; color: white; padding: 10px 20px; border-radius: 6px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
