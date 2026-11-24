@extends('layouts.app')

@section('content')
<style>
    /* Premium Dashboard Styles */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 32px;
        animation: fadeInDown 0.6s ease;
    }
    .welcome-text {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-dark);
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .welcome-sub {
        font-size: 15px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .date-badge {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 2px 5px rgba(0,0,0,0.02);
    }
    .grid-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) {
        .grid-cards { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .grid-cards { grid-template-columns: 1fr; }
    }
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 12px -5px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        animation: fadeInUp 0.6s ease;
        animation-fill-mode: both;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -8px rgba(0,0,0,0.1);
        border-color: rgba(226, 232, 240, 0);
    }
    .stat-card.premium {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        border: none;
        box-shadow: 0 10px 25px -8px rgba(15, 23, 42, 0.4);
    }
    .stat-card.gradient-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .stat-card.gradient-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .stat-card.gradient-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        z-index: 2;
        position: relative;
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .stat-card:not(.premium):not(.gradient-green):not(.gradient-blue):not(.gradient-purple) .stat-icon {
        background: #f8fafc;
        color: var(--primary);
        border: 1px solid #e2e8f0;
    }
    .stat-card.premium .stat-icon, .stat-card.gradient-green .stat-icon, .stat-card.gradient-blue .stat-icon, .stat-card.gradient-purple .stat-icon {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .stat-value {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 4px;
        z-index: 2;
        position: relative;
    }
    .stat-card:not(.premium):not(.gradient-green):not(.gradient-blue):not(.gradient-purple) .stat-value {
        color: #0f172a;
    }
    .stat-title {
        font-size: 12.5px;
        font-weight: 600;
        z-index: 2;
        position: relative;
    }
    .stat-card:not(.premium):not(.gradient-green):not(.gradient-blue):not(.gradient-purple) .stat-title {
        color: #64748b;
    }
    .stat-card.premium .stat-title, .stat-card.gradient-green .stat-title, .stat-card.gradient-blue .stat-title, .stat-card.gradient-purple .stat-title {
        color: rgba(255,255,255,0.8);
    }
    
    /* Decorative blobs */
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(20px);
        z-index: 1;
        opacity: 0.6;
    }
    .blob-1 {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.1);
        top: -40px;
        right: -40px;
    }
    .blob-2 {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.05);
        bottom: -15px;
        left: -15px;
    }
    
    /* Smaller stats cards for messages */
    .grid-cards-small {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) {
        .grid-cards-small { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .grid-cards-small { grid-template-columns: 1fr; }
    }
    
    .stat-card-small {
        background: #ffffff;
        border-radius: 12px;
        padding: 12px 16px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 10px -5px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: fadeInUp 0.6s ease;
        animation-fill-mode: both;
    }
    
    .stat-card-small:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px -5px rgba(0,0,0,0.06);
    }
    
    .stat-small-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .stat-small-content {
        flex: 1;
    }
    
    .stat-small-value {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 2px;
    }
    
    .stat-small-title {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }

    /* Activity Section */
    .dashboard-sections {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .dashboard-sections {
            grid-template-columns: 1fr;
        }
    }
    
    .panel {
        background: #ffffff;
        border-radius: 20px;
        padding: 28px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.03);
        animation: fadeInUp 0.8s ease;
    }
    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .panel-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-title i {
        color: var(--primary);
    }
    
    /* Animations */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="dashboard-header">
    <div>
        <div class="welcome-text">Welcome back, {{ auth()->user()->name }} 👋</div>
        <div class="welcome-sub">Here is what's happening with your WABA account today.</div>
    </div>
    <div class="date-badge">
        <i class="fa-regular fa-calendar"></i>
        {{ \Carbon\Carbon::now()->format('l, M d, Y') }}
    </div>
</div>

<div class="grid-cards">
    <!-- Profile Card -->
    <div class="stat-card premium" style="animation-delay: 0.1s;">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="stat-header">
            <div class="stat-title" style="text-transform: uppercase; letter-spacing: 1px; font-size: 12px; font-weight: 800; color: #38bdf8;">
                {{ auth()->user()->role ?? 'User' }} Account
            </div>
            <div class="stat-icon" style="background: rgba(56, 189, 248, 0.2); border-color: rgba(56, 189, 248, 0.3); color: #38bdf8;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>
        <div>
            <div class="stat-value" style="font-size: 24px; margin-bottom: 4px;">{{ auth()->user()->name }}</div>
            <div class="stat-title" style="color: rgba(255,255,255,0.6);">{{ auth()->user()->email }}</div>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin())
        <div class="stat-card gradient-blue" style="animation-delay: 0.2s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">Total Users</div>
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['total_users'] }}</div>
                <div class="stat-title">Active across the platform</div>
            </div>
        </div>
        
        <div class="stat-card gradient-green" style="animation-delay: 0.3s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">Total Admins</div>
                <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['total_admins'] ?? 0 }}</div>
                <div class="stat-title">System administrators</div>
            </div>
        </div>

        <div class="stat-card gradient-purple" style="animation-delay: 0.4s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">Total Clients</div>
                <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['total_clients'] ?? 0 }}</div>
                <div class="stat-title">Registered clients</div>
            </div>
        </div>
    @endif

    @if(auth()->user()->isAdmin())
        <div class="stat-card gradient-purple" style="animation-delay: 0.2s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">My Clients</div>
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['my_clients'] ?? 0 }}</div>
                <div class="stat-title">Clients managed by you</div>
            </div>
        </div>

        <div class="stat-card" style="animation-delay: 0.3s;">
            <div class="stat-header">
                <div class="stat-title">Total Messages</div>
                <div class="stat-icon" style="color: #f59e0b; background: #fef3c7; border-color: #fde68a;"><i class="fa-solid fa-gauge-high"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['total_credits'] ?? (auth()->user()->daily_limit ?? 'Unlimited') }}</div>
                <div class="stat-title">Daily message limit</div>
            </div>
        </div>

        <div class="stat-card gradient-blue" style="animation-delay: 0.4s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">Messages Assigned</div>
                <div class="stat-icon"><i class="fa-solid fa-share-nodes"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['credits_assigned'] ?? 0 }}</div>
                <div class="stat-title">Assigned to clients</div>
            </div>
        </div>
    @endif

    @if(auth()->user()->isClient())
        <div class="stat-card gradient-blue" style="animation-delay: 0.2s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">Messages Sent</div>
                <div class="stat-icon"><i class="fa-regular fa-paper-plane"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ $roleStats['credits_used_today'] ?? 0 }}</div>
                <div class="stat-title">Total messages sent today</div>
            </div>
        </div>

        <div class="stat-card" style="animation-delay: 0.3s;">
            <div class="stat-header">
                <div class="stat-title">Daily Limit</div>
                <div class="stat-icon" style="color: #f59e0b; background: #fef3c7; border-color: #fde68a;"><i class="fa-solid fa-gauge-high"></i></div>
            </div>
            <div>
                <div class="stat-value">{{ auth()->user()->daily_limit ?? 'Unlimited' }}</div>
                <div class="stat-title">Messages allowed today</div>
            </div>
        </div>
        
        <div class="stat-card gradient-green" style="animation-delay: 0.4s;">
            <div class="blob blob-1"></div>
            <div class="stat-header">
                <div class="stat-title">Managed By</div>
                <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
            </div>
            <div>
                <div class="stat-value" style="font-size: 20px; line-height: 1.4; margin-bottom: 0;">
                    {{ auth()->user()->parent ? auth()->user()->parent->name : 'System Admin' }}
                </div>
                <div class="stat-title" style="margin-top: 8px;">Your assigned administrator</div>
            </div>
        </div>
    @endif
</div>

<div class="grid-cards-small">
    <!-- Messages Sent / Total -->
    <div class="stat-card-small" style="animation-delay: 0.5s;">
        <div class="stat-small-icon" style="background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe;">
            <i class="fa-regular fa-paper-plane"></i>
        </div>
        <div class="stat-small-content">
            <div class="stat-small-value">{{ $waTotal ?? 0 }}</div>
            <div class="stat-small-title">Total Messages Sent</div>
        </div>
    </div>

    <!-- Messages Delivered / Success -->
    <div class="stat-card-small" style="animation-delay: 0.6s;">
        <div class="stat-small-icon" style="background: #f0fdf4; color: #10b981; border: 1px solid #bbf7d0;">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <div class="stat-small-content">
            <div class="stat-small-value">{{ $waSuccess ?? 0 }}</div>
            <div class="stat-small-title">Messages Delivered</div>
        </div>
    </div>

    <!-- Messages Pending -->
    <div class="stat-card-small" style="animation-delay: 0.7s;">
        <div class="stat-small-icon" style="background: #fef3c7; color: #f59e0b; border: 1px solid #fde68a;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="stat-small-content">
            <div class="stat-small-value">{{ $waPending ?? 0 }}</div>
            <div class="stat-small-title">Messages Pending</div>
        </div>
    </div>

    <!-- Messages Failed -->
    <div class="stat-card-small" style="animation-delay: 0.8s;">
        <div class="stat-small-icon" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="stat-small-content">
            <div class="stat-small-value">{{ $waFailed ?? 0 }}</div>
            <div class="stat-small-title">Messages Failed</div>
        </div>
    </div>
</div>

<div class="dashboard-sections">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-bolt"></i> Quick Actions</div>
        </div>
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <a href="{{ route('send.wa.form') }}" class="btn-primary" style="text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 700;">
                <i class="fa-brands fa-whatsapp"></i> Send New Message
            </a>
            @if(Auth::user()->hasPermission('contacts.view'))
            <a href="{{ route('contacts.index') }}" style="background: #f1f5f9; color: #334155; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e2e8f0; transition: all 0.2s;">
                <i class="fa-solid fa-address-book"></i> View Contacts
            </a>
            @endif
        </div>
    </div>
    
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-circle-check"></i> System Status</div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px rgba(16,185,129,0.5);"></div>
                <span style="font-weight: 600; color: #334155;">WhatsApp API Connected</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 12px; height: 12px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px rgba(16,185,129,0.5);"></div>
                <span style="font-weight: 600; color: #334155;">Webhooks Active</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 12px; height: 12px; background: #3b82f6; border-radius: 50%; box-shadow: 0 0 10px rgba(59,130,246,0.5);"></div>
                <span style="font-weight: 600; color: #334155;">Database Synced</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Force header style adjustment if overlapping (fixes visual issues on certain resolutions)
    document.addEventListener('DOMContentLoaded', function() {
        const topbarLeft = document.querySelector('.topbar-left');
        const headerTabs = document.querySelector('.header-tabs');
        if (headerTabs && topbarLeft) {
            headerTabs.style.maxWidth = 'calc(100vw - 400px)';
        }
    });
</script>
@endsection
