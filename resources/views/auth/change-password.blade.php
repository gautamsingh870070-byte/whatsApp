@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 150px);">
    <div class="form-card" style="width: 100%; max-width: 500px;">
        <h2 style="margin-bottom: 24px; text-align: center; color: var(--text-main);">Change Password</h2>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div style="background-color: #ffebee; color: #c62828; padding: 16px; border-radius: 6px; margin-bottom: 24px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 16px;">
                <i class="fa-solid fa-save"></i> Update Password
            </button>
        </form>
    </div>
</div>
@endsection
