@extends('layouts.app')

@section('content')
<div class="card" style="justify-content: center; text-align: center; padding: 60px 20px;">
    <div style="font-size: 64px; color: var(--icon-color-blue); margin-bottom: 24px;">
        <i class="fa-solid fa-person-digging"></i>
    </div>
    <h2 style="color: var(--text-main); margin-bottom: 12px;">Under Construction</h2>
    <p style="color: var(--text-muted); font-size: 16px; max-width: 500px; margin: 0 auto;">
        This page is currently being built and will be available in a future update.
    </p>
    <a href="{{ route('dashboard') }}" class="btn-primary" style="display: inline-block; margin-top: 32px; text-decoration: none;">
        Return to Dashboard
    </a>
</div>
@endsection
