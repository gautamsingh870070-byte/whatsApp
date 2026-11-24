@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: var(--text-dark);">RCS Dashboard</h2>
        <a href="{{ route('rcs.send.form') }}" class="btn-primary">
            <i class="fa-solid fa-paper-plane"></i> Send RCS Message
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    <div class="grid">
        <div class="card" style="border-left: 4px solid var(--primary);">
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Total Messages</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-dark); margin-top: 8px;">{{ $totalMessages }}</div>
        </div>
        
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Sent</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-dark); margin-top: 8px;">{{ $sent }}</div>
        </div>
        
        <div class="card" style="border-left: 4px solid #10b981;">
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Delivered</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-dark); margin-top: 8px;">{{ $delivered }}</div>
        </div>
        
        <div class="card" style="border-left: 4px solid #8b5cf6;">
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Read</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-dark); margin-top: 8px;">{{ $read }}</div>
        </div>
        
        <div class="card" style="border-left: 4px solid #ef4444;">
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Failed</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-dark); margin-top: 8px;">{{ $failed }}</div>
        </div>
    </div>

    <div class="grid mt-4" style="margin-top: 24px;">
        <div class="card">
            <h4 style="font-weight: 700; color: var(--text-dark); margin-bottom: 16px;">Quick Stats</h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-main); font-weight: 500;">Today's Messages</span>
                    <span style="font-weight: 700; color: var(--text-dark);">{{ $todayMessages }}</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-main); font-weight: 500;">This Month's Messages</span>
                    <span style="font-weight: 700; color: var(--text-dark);">{{ $thisMonthMessages }}</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 12px 0;">
                    <span style="color: var(--text-main); font-weight: 500;">Configured Agents</span>
                    <span style="font-weight: 700; color: var(--text-dark);">{{ $agentsConfigured }}</span>
                </li>
            </ul>
        </div>
        
        <div class="card">
            <h4 style="font-weight: 700; color: var(--text-dark); margin-bottom: 16px;">System Status</h4>
            @if($agentsConfigured > 0)
                <div style="display: flex; align-items: center; gap: 12px; padding: 16px; background-color: #f0fdf4; border-radius: var(--radius-md); border: 1px solid #bbf7d0;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 24px;"></i>
                    <div>
                        <div style="font-weight: 700; color: #065f46;">Google RCS API Connected</div>
                        <div style="font-size: 13px; color: #166534; margin-top: 4px;">Your agents are active and ready to send messages.</div>
                    </div>
                </div>
            @else
                <div style="display: flex; align-items: center; gap: 12px; padding: 16px; background-color: #fef2f2; border-radius: var(--radius-md); border: 1px solid #fecaca;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444; font-size: 24px;"></i>
                    <div>
                        <div style="font-weight: 700; color: #991b1b;">RCS Agent Not Configured</div>
                        <div style="font-size: 13px; color: #b91c1c; margin-top: 4px;">Please configure your Google RCS Agent credentials in settings to start sending messages.</div>
                        <a href="{{ route('rcs.settings.index') }}" style="display: inline-block; margin-top: 8px; font-size: 13px; color: #dc2626; font-weight: 600;">Go to Settings &rarr;</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
