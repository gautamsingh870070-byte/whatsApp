<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        // Auto-detect if we are on local server or live server to fix the public folder path issue
        $assetPrefix = in_array(request()->getHost(), ['127.0.0.1', 'localhost']) ? '' : 'public/';
    @endphp
    <title>WABA</title>
    <link rel="icon" type="image/jpeg" href="{{ asset($assetPrefix . 'images/WABA.jpeg') }}?v={{ time() }}">
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">



    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Modern Palette */
            --bg-sidebar: #0f172a;        /* Slate 900 */
            --bg-sidebar-hover: #1e293b;  /* Slate 800 */
            --bg-sidebar-active: #1e293b;
            
            --primary: #10b981;           /* Emerald 500 */
            --primary-hover: #059669;     /* Emerald 600 */
            --primary-light: #ecfdf5;     /* Emerald 50 */
            
            --bg-app: #f8fafc;            /* Slate 50 */
            --bg-card: #ffffff;
            
            --text-dark: #0f172a;         /* Slate 900 */
            --text-main: #334155;         /* Slate 700 */
            --text-muted: #64748b;        /* Slate 500 */
            --text-light: #94a3b8;        /* Slate 400 */
            
            --border-color: #e2e8f0;      /* Slate 200 */
            --border-dark: #334155;
            
            --sidebar-width: 240px;
            --sidebar-collapsed: 72px;
            --topbar-height: 64px;
            
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: var(--font-main);
            background-color: var(--bg-app);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ─── APP LAYOUT CONTAINER ───────────────────────────── */
        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            position: relative;
        }

        /* ─── PREMIUM SIDEBAR ──────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-collapsed);
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.15);
        }

        .sidebar.is-expanded {
            width: var(--sidebar-width);
        }

        /* Brand Logo Area */
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.1);
        }

        .brand-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid var(--primary);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        
        .sidebar-brand:hover .brand-avatar {
            transform: scale(1.05) rotate(5deg);
        }

        .brand-info {
            display: none;
            white-space: nowrap;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar.is-expanded .brand-info {
            display: block;
            opacity: 1;
            animation: fadeIn 0.4s ease forwards;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-title span {
            color: var(--primary);
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
        }

        .brand-subtitle {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Navigation List */
        .sidebar-nav {
            flex: 1;
            padding: 20px 12px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .nav-label-group {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #64748b;
            margin: 20px 8px 10px 8px;
            display: none;
            white-space: nowrap;
            opacity: 0;
        }

        .sidebar.is-expanded .nav-label-group {
            display: block;
            opacity: 1;
            animation: fadeIn 0.5s ease forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-5px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 14px;
            color: var(--text-light);
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            white-space: nowrap;
            overflow: hidden;
            z-index: 1;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.08) 0%, transparent 100%);
            transition: width 0.3s ease;
            z-index: -1;
            border-radius: var(--radius-md);
        }

        .sidebar:not(.is-expanded) .nav-link {
            justify-content: center;
            padding: 12px 0;
            flex-direction: column;
            gap: 6px;
        }

        .nav-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-shrink: 0;
        }

        .nav-text {
            font-size: 14px;
            font-weight: 500;
            margin-left: 12px;
            display: none;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .sidebar:not(.is-expanded) .nav-text {
            display: block;
            font-size: 10px;
            font-weight: 600;
            margin-left: 0;
            line-height: 1;
            opacity: 0.8;
        }

        .sidebar.is-expanded .nav-text {
            display: block;
        }

        .nav-link:hover {
            color: #ffffff;
            transform: translateX(4px);
        }
        
        .nav-link:hover::before {
            width: 100%;
        }

        .sidebar:not(.is-expanded) .nav-link:hover {
            transform: translateY(-2px);
        }

        .nav-link:hover i {
            color: var(--primary);
            transform: scale(1.15);
            filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.3));
        }

        /* Active Navigation Item */
        .nav-link.active {
            color: #ffffff !important;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.1);
            box-shadow: inset 3px 0 0 var(--primary);
        }
        
        .sidebar.is-expanded .nav-link.active {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.02) 100%);
            border-left: 4px solid var(--primary);
            box-shadow: none;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            transform: translateX(2px);
        }

        .nav-link.active i {
            color: var(--primary) !important;
            filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.4));
        }
        
        .sidebar:not(.is-expanded) .nav-link.active {
            border-left: none;
            border-bottom: 3px solid var(--primary);
            background: linear-gradient(0deg, rgba(16, 185, 129, 0.15) 0%, transparent 100%);
            box-shadow: none;
            border-radius: var(--radius-md);
        }

        /* Badge Indicator */
        .nav-badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--primary), #059669);
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 12px;
            display: none;
            box-shadow: 0 2px 5px rgba(16, 185, 129, 0.4);
            letter-spacing: 0.5px;
        }

        .sidebar.is-expanded .nav-badge {
            display: inline-block;
            animation: pulse-badge 2s infinite;
        }
        
        @keyframes pulse-badge {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Sidebar Footer Toggle Button */
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.1);
        }

        .toggle-btn {
            width: 100%;
            height: 42px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            color: var(--text-light);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: var(--font-main);
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }
        
        .toggle-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.4s ease;
            border-radius: 50%;
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .toggle-btn:hover::after {
            transform: translate(-50%, -50%) scale(2);
        }
        
        .toggle-btn i {
            transition: transform 0.3s ease;
        }
        
        .toggle-btn:active i {
            transform: scale(0.9);
        }

        /* ─── MAIN CONTENT WRAPPER ───────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-collapsed);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: calc(100vw - var(--sidebar-collapsed));
        }

        .sidebar.is-expanded ~ .main-wrapper {
            margin-left: var(--sidebar-width);
            max-width: calc(100vw - var(--sidebar-width));
        }

        /* ─── TOP HEADER BAR ─────────────────────────────────── */
        .topbar {
            height: var(--topbar-height);
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: var(--shadow-sm);
            flex-wrap: nowrap !important;
            overflow: visible;
        }

        /* Left Header Section */
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .mobile-toggle-btn {
            display: none;
            background: none;
            border: none;
            font-size: 18px;
            color: var(--text-main);
            cursor: pointer;
            padding: 4px;
        }

        /* Horizontal Sub-Navigation Tabs in Header */
        .header-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            overflow-x: auto;
            scrollbar-width: none;
            padding: 4px 6px;
            background: rgba(241, 245, 249, 0.5);
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .header-tabs::-webkit-scrollbar {
            display: none;
        }

        .tab-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 16px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .tab-item:hover {
            color: #0f172a;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .tab-item.active {
            background-color: #ffffff;
            color: var(--primary);
            font-weight: 700;
            box-shadow: 0 4px 12px -2px rgba(0,0,0,0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .tab-item i {
            font-size: 13px;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .tab-item:hover i {
            color: var(--primary);
        }

        .tab-item.active i {
            color: var(--primary);
        }

        /* Right Header Section */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }

        /* Search Input Box */
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #f1f5f9;
            padding: 7px 14px;
            border-radius: 20px;
            width: 220px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .search-box:focus-within {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
            width: 260px;
        }

        .search-box i {
            color: var(--text-muted);
            font-size: 13px;
        }

        .search-box input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-family: var(--font-main);
            font-size: 12.5px;
            color: var(--text-dark);
        }

        .search-box input::placeholder {
            color: var(--text-light);
        }

        /* Status Badge */
        .status-badge {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            background-color: var(--primary);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px var(--primary);
            animation: pulse 2s infinite;
        }

        /* User Profile Dropdown */
        .user-dropdown {
            position: relative;
            z-index: 1050;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px;
            border-radius: 30px;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 48px;
            background-color: var(--bg-card);
            min-width: 200px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            z-index: 1000;
            overflow: hidden;
            animation: slideDown 0.2s ease;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            background-color: #f8fafc;
        }

        .dropdown-header .user-name {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .dropdown-header .user-email {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .dropdown-item {
            padding: 11px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            text-align: left;
            transition: background 0.15s ease;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--text-dark);
        }

        .dropdown-item i {
            color: var(--text-muted);
            width: 16px;
        }

        .dropdown-item:hover i {
            color: var(--primary);
        }

        /* ─── DYNAMIC PAGE CONTENT ───────────────────────────── */
        .content-area {
            padding: 28px;
            max-width: 1440px;
            background-color: #f4eeee;
            width: 100%;
            margin: 0 auto;
            flex: 1;
        }

        /* Grid System */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        /* Cards */
        .card {
            background-color: var(--bg-card);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            padding: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .form-card {
            background-color: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            padding: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 13.5px;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 11px 16px;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            font-family: var(--font-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            padding: 11px 22px;
            font-size: 14px;
            font-weight: 700;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }

        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-weight: 500;
            font-size: 14px;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* Backdrop Overlay for Mobile */
        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 950;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            background-color: #ffffff;
            border-left: 4px solid var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 4px;
            padding: 16px 20px;
            min-width: 300px;
            max-width: 400px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-icon {
            font-size: 20px;
            color: var(--primary);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 16px;
        }

        .toast.error { border-left-color: #ef4444; }
        .toast.error .toast-icon { color: #ef4444; }
        .toast.warning { border-left-color: #f59e0b; }
        .toast.warning .toast-icon { color: #f59e0b; }

        /* Keyframes */
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom Profile Modal Styles */
        .profile-modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .profile-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .profile-custom-modal {
            background-color: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transform: scale(0.95);
            transition: transform 0.2s ease;
            overflow: hidden;
        }

        .profile-modal-overlay.active .profile-custom-modal {
            transform: scale(1);
        }

        .profile-modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafc;
        }

        .profile-modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .profile-modal-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }

        .profile-modal-close:hover {
            color: #ef4444;
        }

        .profile-modal-body {
            padding: 20px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .search-box {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .mobile-overlay.active {
                display: block;
            }

            .main-wrapper {
                margin-left: 0 !important;
                max-width: 100vw !important;
            }

            .mobile-toggle-btn {
                display: block;
            }

            .sidebar-brand .brand-info {
                display: block !important;
            }

            .nav-link {
                flex-direction: row !important;
                justify-content: flex-start !important;
                padding: 11px 14px !important;
            }

            .nav-text {
                display: block !important;
                font-size: 13.5px !important;
                margin-left: 12px !important;
            }
        }
    </style>
</head>
<body>

    <div class="app-container">
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>

        <!-- ─── SIDEBAR NAVIGATION ─────────────────────────── -->
        <aside class="sidebar is-expanded" id="sidebar">
            <!-- Brand Logo -->
            <div class="sidebar-brand">
                <img src="{{ asset($assetPrefix . 'images/WABA.jpeg') }}?v={{ time() }}" alt="WABA Logo" class="brand-avatar">
                <div class="brand-info">
                    <div class="brand-title"><span>WhatsApp</span><span style="color:white;"> STM</span></div>
                   
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="sidebar-nav">
                <div class="nav-label-group">MAIN NAVIGATION</div>
                <ul class="nav-menu">
                    <!-- Dashboard -->
                    @if(Auth::user()->hasPermission('dashboard.view'))
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    @endif

                    <!-- Live Chat -->
                    @if(Auth::user()->hasPermission('livechat.view'))
                    <li>
                        <a href="{{ route('live.chat') }}" class="nav-link {{ request()->routeIs('live.chat') ? 'active' : '' }}" title="Live Chat">
                            <i class="fa-solid fa-comments"></i>
                            <span class="nav-text">Live Chat</span>
                            <span class="nav-badge">Live</span>
                        </a>
                    </li>
                    @endif

                    <!-- History -->
                    @if(Auth::user()->hasPermission('history.view'))
                    <li>
                        <a href="{{ route('history') }}" class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}" title="History">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span class="nav-text">History</span>
                        </a>
                    </li>
                    @endif

                    <!-- Contacts -->
                    @if(Auth::user()->hasPermission('contacts.view'))
                    <li>
                        <a href="{{ route('contacts.index') }}" class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" title="Contacts">
                            <i class="fa-solid fa-address-book"></i>
                            <span class="nav-text">Contacts</span>
                        </a>
                    </li>
                    @endif

                    <!-- Campaigns -->
                    @if(Auth::user()->hasPermission('messages.send'))
                    <li>
                        <a href="{{ route('send.wa.form') }}" class="nav-link {{ request()->routeIs('send.wa.*') || request()->routeIs('campaigns') ? 'active' : '' }}" title="Campaigns">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span class="nav-text">Campaigns</span>
                        </a>
                    </li>
                    @endif

                    <!-- WA Templates -->
                    @if(Auth::user()->hasPermission('templates.view'))
                    <li>
                        <a href="{{ route('templates.index') }}" class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" title="WA Templates">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span class="nav-text">WA Templates</span>
                        </a>
                    </li>
                    @endif

                    <!-- RCS SMS -->
                    <div class="nav-label-group">RCS SMS</div>
                    <li>
                        <a href="{{ route('rcs.dashboard') }}" class="nav-link {{ request()->routeIs('rcs.dashboard') ? 'active' : '' }}" title="RCS Dashboard">
                            <i class="fa-solid fa-chart-line"></i>
                            <span class="nav-text">RCS Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rcs.send.form') }}" class="nav-link {{ request()->routeIs('rcs.send.*') ? 'active' : '' }}" title="Send RCS Message">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span class="nav-text">Send RCS</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rcs.contacts.index') }}" class="nav-link {{ request()->routeIs('rcs.contacts.*') ? 'active' : '' }}" title="RCS Contacts">
                            <i class="fa-solid fa-address-book"></i>
                            <span class="nav-text">RCS Contacts</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rcs.history') }}" class="nav-link {{ request()->routeIs('rcs.history') ? 'active' : '' }}" title="RCS History">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span class="nav-text">RCS History</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rcs.templates.index') }}" class="nav-link {{ request()->routeIs('rcs.templates.*') ? 'active' : '' }}" title="RCS Templates">
                            <i class="fa-solid fa-file-lines"></i>
                            <span class="nav-text">RCS Templates</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rcs.settings.index') }}" class="nav-link {{ request()->routeIs('rcs.settings.*') ? 'active' : '' }}" title="RCS Settings">
                            <i class="fa-solid fa-gear"></i>
                            <span class="nav-text">RCS Settings</span>
                        </a>
                    </li>

                    <!-- Manage -->
                    @if(Auth::user()->hasPermission('users.view'))
                    <li>
                        <a href="{{ route('manage.user') }}" class="nav-link {{ request()->routeIs('manage.user') ? 'active' : '' }}" title="Manage Users">
                            <i class="fa-solid fa-users-gear"></i>
                            <span class="nav-text">Manage Users</span>
                        </a>
                    </li>
                    @endif
                </ul>

                <!-- Dev & Projects -->
                @if(Auth::user()->hasPermission('developer.view'))
                <div class="nav-label-group">DEVELOPMENT</div>
                <ul class="nav-menu">
                    <li>
                        <a href="{{ route('developer') }}" class="nav-link {{ request()->routeIs('developer') ? 'active' : '' }}" title="Developer">
                            <i class="fa-solid fa-code"></i>
                            <span class="nav-text">Developer</span>
                        </a>
                    </li>
                </ul>
                @endif

                <!-- Logs & Credits -->
                @if(Auth::user()->hasPermission('schedule_log.view') || Auth::user()->hasPermission('credits.manage') || Auth::user()->hasPermission('credit_history.view'))
                <div class="nav-label-group">MANAGEMENT</div>
                <ul class="nav-menu">
                    @if(Auth::user()->hasPermission('schedule_log.view'))
                    <li>
                        <a href="{{ route('schedule.log') }}" class="nav-link {{ request()->routeIs('schedule.log') ? 'active' : '' }}" title="Schedule Log">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span class="nav-text">Schedule Log</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->hasPermission('credits.manage'))
                    <li>
                        <a href="{{ route('credit.manage') }}" class="nav-link {{ request()->routeIs('credit.manage') ? 'active' : '' }}" title="Credit Manage">
                            <i class="fa-solid fa-credit-card"></i>
                            <span class="nav-text">Credit Manage</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->hasPermission('credit_history.view'))
                    <li>
                        <a href="{{ route('credit.history') }}" class="nav-link {{ request()->routeIs('credit.history') ? 'active' : '' }}" title="Credit History">
                            <i class="fa-solid fa-receipt"></i>
                            <span class="nav-text">Credit History</span>
                        </a>
                    </li>
                    @endif
                </ul>
                @endif
            </nav>

            <!-- Toggle Footer Button -->
            <div class="sidebar-footer">
                <button class="toggle-btn" id="sidebarToggle" title="Expand / Collapse Sidebar">
                    <i class="fa-solid fa-angles-left" id="toggleIcon"></i>
                    <span id="toggleLabel" style="display: inline;">Minimize</span>
                </button>
            </div>
        </aside>

        <!-- ─── MAIN CONTENT WRAPPER ───────────────────────── -->
        <div class="main-wrapper">
            <!-- Top Header Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <!-- Mobile Hamburger -->
                    <button class="mobile-toggle-btn" id="mobileToggle" aria-label="Toggle Navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <!-- Horizontal Quick Nav Tabs -->
                    <ul class="header-tabs">
                        @if(Auth::user()->hasPermission('dashboard.view'))
                        <li><a href="{{ route('dashboard') }}" class="tab-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('messages.send'))
                        <li><a href="{{ route('send.wa.form') }}" class="tab-item {{ request()->routeIs('send.wa.*') ? 'active' : '' }}"><i class="fa-regular fa-paper-plane"></i> Send WA</a></li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('templates.view'))
                        <li><a href="{{ route('templates.index') }}" class="tab-item {{ request()->routeIs('templates.*') ? 'active' : '' }}"><i class="fa-brands fa-whatsapp"></i> WA Templates</a></li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('history.view'))
                        <li><a href="{{ route('history') }}" class="tab-item {{ request()->routeIs('history') ? 'active' : '' }}"><i class="fa-regular fa-clipboard"></i> WAPP History</a></li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('schedule_log.view'))
                        <li><a href="{{ route('schedule.log') }}" class="tab-item {{ request()->routeIs('schedule.log') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i> Schedule Log</a></li>
                        @endif
                        
                        @if(Auth::user()->hasPermission('users.view'))
                        <li><a href="{{ route('manage.user') }}" class="tab-item {{ request()->routeIs('manage.user') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Manage User</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Right Header Actions -->
                <div class="topbar-right">
                    <!-- Search Input -->
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search name / mobile...">
                    </div>

                    <!-- Active Badge -->
                    <span class="status-badge">
                        <span class="pulse-dot"></span> ACTIVE (1)
                    </span>

                    @auth
                    <!-- User Profile Dropdown -->
                    <div class="user-dropdown">
                        <button class="profile-btn">
                            <img src="{{ asset($assetPrefix . 'images/profile.jpg') }}?v={{ time() }}" onerror="this.src='{{ asset($assetPrefix . 'images/WABA.jpeg') }}?v={{ time() }}'" alt="User Avatar" class="profile-avatar">
                            <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                                <div class="user-email">{{ Auth::user()->email ?? 'admin@example.com' }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="fa-solid fa-user-pen"></i> Update Profile
                            </a>
                            <a href="{{ route('profile.logo') }}" class="dropdown-item">
                                <i class="fa-solid fa-image"></i> Update App Logo
                            </a>
                            <a href="{{ route('password.change') }}" class="dropdown-item">
                                <i class="fa-solid fa-key"></i> Change Password
                            </a>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fa-solid fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Dynamic View Output -->
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>
    <!-- Global Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Smooth Sidebar & Mobile Script -->
    <script>
        // Toast Notification System
        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let icon = 'fa-check-circle';
            if (type === 'error') icon = 'fa-circle-xmark';
            if (type === 'warning') icon = 'fa-triangle-exclamation';

            toast.innerHTML = `
                <i class="fa-solid ${icon} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 10);
            
            // Auto remove after 5s
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleLabel = document.getElementById('toggleLabel');
            
            const mobileBtn = document.getElementById('mobileToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');

            // Sidebar Expand / Collapse Toggle
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    const isExpanded = sidebar.classList.toggle('is-expanded');
                    if (isExpanded) {
                        toggleIcon.classList.remove('fa-angles-right');
                        toggleIcon.classList.add('fa-angles-left');
                        toggleLabel.style.display = 'inline';
                    } else {
                        toggleIcon.classList.remove('fa-angles-left');
                        toggleIcon.classList.add('fa-angles-right');
                        toggleLabel.style.display = 'none';
                    }
                });
            }

            // Mobile Menu Toggle
            if (mobileBtn && sidebar && mobileOverlay) {
                mobileBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                    mobileOverlay.classList.toggle('active');
                });

                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    mobileOverlay.classList.remove('active');
                });
            }

            // User Profile Dropdown Toggle
            const profileBtn = document.querySelector('.profile-btn');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (profileBtn && dropdownMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>
