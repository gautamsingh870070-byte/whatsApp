@extends('layouts.app')

@section('content')
<style>
    /* ═══════════════════════════════════════════════════════════
       DESIGN SYSTEM — WhatsApp Web + Premium CRM
       ═══════════════════════════════════════════════════════════ */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    :root {
        /* ── WhatsApp Brand Colors (Black & White Theme) ── */
        --wa-green: #000000;
        --wa-green-dark: #333333;
        --wa-green-light: #f5f5f5;
        --wa-green-hover: #444444;
        --wa-teal: #111111;

        /* ── Backgrounds ── */
        --wa-bg-app: #ffffff;
        --wa-bg-chat: #ffffff;
        --wa-bg-sidebar: #ffffff;
        --wa-bg-header: #f8f9fa;
        --wa-bg-panel: #f8f9fa;
        --wa-bg-input: #f0f2f5;
        --wa-bg-hover: #f8f9fa;

        /* ── Text ── */
        --wa-text-primary: #111b21;
        --wa-text-secondary: #4a4a4a;
        --wa-text-muted: #6b6b6b;
        --wa-text-header: #333333;

        /* ── Borders ── */
        --wa-border: #e0e0e0;
        --wa-border-light: #f0f2f5;

        /* ── Message Bubbles ── */
        --wa-bubble-out: #d9fdd3;
        --wa-bubble-in: #ffffff;
        --wa-bubble-border: 1px solid #e0e0e0;
        --wa-bubble-shadow: 0 1px 2px rgba(0,0,0,0.05);

        /* ── Danger ── */
        --wa-danger: #ea4335;
        --wa-danger-bg: #fce8e6;

        /* ── Layout ── */
        --chat-sidebar-width: 320px;
        --profile-width: 280px;
        --header-height: 64px;
        --reply-bar-height: auto;
        --border-radius-sm: 8px;
        --border-radius-md: 12px;
        --border-radius-lg: 20px;
        --border-radius-pill: 100px;

        /* ── Transitions ── */
        --transition-fast: 0.15s ease;
        --transition-normal: 0.2s ease;
        --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Override App Layout Padding for Full Bleed Chat */
    .content-area {
        padding: 0 !important;
        max-width: 100% !important;
        display: flex;
        flex-direction: column;
    }

    /* ── Global font for Chat Page (excluding icons) ── */
    .chat-page-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .chat-page-container input,
    .chat-page-container textarea,
    .chat-page-container button,
    .chat-page-container select,
    .chat-page-container span,
    .chat-page-container div,
    .chat-page-container p,
    .chat-page-container a,
    .chat-page-container label {
        font-family: inherit;
        box-sizing: border-box;
    }

    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }
        .chat-window, .chat-window * {
            visibility: visible;
        }
        .chat-window {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: auto !important;
            overflow: visible !important;
            background: #fff !important;
        }
        .chat-window-header, .chat-reply-bar, .top-active-avatars-bar {
            display: none !important;
        }
        .chat-messages-container {
            overflow: visible !important;
            height: auto !important;
            background: #fff !important;
            padding: 0 !important;
        }
        .message-bubble {
            background: #fff !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
    }

    /* ═══════ MAIN CONTAINER ═══════ */
    .chat-page-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 64px);
        background: var(--wa-bg-app);
        border-radius: 0;
        box-shadow: none;
        border: none;
        overflow: hidden;
        position: relative;
    }

    /* ═══════ TOP ACTIVE CHATS STRIP ═══════ */
    .top-active-avatars-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 20px;
        background-color: #ffffff;
        border-bottom: 1px solid var(--wa-border);
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .top-active-avatars-bar::-webkit-scrollbar {
        display: none;
    }

    .top-avatar-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: transform var(--transition-fast);
        flex-shrink: 0;
        text-decoration: none;
    }
    .top-avatar-item:hover {
        transform: translateY(-2px);
    }

    .top-avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        color: #ffffff;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        border: 2px solid transparent;
    }
    .top-avatar-item.active .top-avatar-circle {
        border-color: var(--wa-green);
        box-shadow: 0 0 10px rgba(0,168,132,0.3);
    }

    .top-avatar-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background-color: var(--wa-green);
        color: #ffffff;
        font-size: 9px;
        font-weight: 700;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #ffffff;
    }

    .top-avatar-label {
        font-size: 10.5px;
        font-weight: 500;
        color: #8696a0;
        max-width: 65px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .top-avatar-item.active .top-avatar-label {
        color: #e9edef;
        font-weight: 600;
    }

    /* Twemoji */
    img.emoji {
        height: 1.2em;
        width: 1.2em;
        margin: 0 .05em 0 .1em;
        vertical-align: -0.1em;
    }
    .message-bubble.single-emoji img.emoji {
        height: 48px;
        width: 48px;
        margin: 0;
        vertical-align: middle;
    }

    /* ═══════ MAIN SPLIT LAYOUT ═══════ */
    .chat-main-split {
        display: flex;
        flex: 1;
        overflow: hidden;
        background: var(--wa-bg-app);
    }

    /* ═══════════════════════════════════════════
       LEFT SIDEBAR — CONTACTS PANEL
       ═══════════════════════════════════════════ */
    .contacts-sidebar {
        width: var(--chat-sidebar-width);
        border-right: 1px solid var(--wa-border);
        display: flex;
        flex-direction: column;
        background-color: var(--wa-bg-sidebar);
        flex-shrink: 0;
        z-index: 10;
    }

    /* ── Sidebar Header ── */
    .filter-tabs-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: var(--wa-bg-header);
        padding: 14px 16px;
        border-bottom: none;
        min-height: var(--header-height);
    }

    .chat-header-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--wa-text-primary);
        letter-spacing: -0.3px;
    }

    .tab-count-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: var(--border-radius-pill);
    }
    .tab-count-green { background-color: #dcfce7; color: #15803d; }
    .tab-count-amber { background-color: #fef3c7; color: #b45309; }
    .tab-count-purple { background-color: #f3e8ff; color: #7e22ce; }

    /* ── Search Box ── */
    .contacts-search-area {
        padding: 8px 12px;
        background: var(--wa-bg-sidebar);
    }

    .search-input-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        background-color: var(--wa-bg-input);
        padding: 7px 14px;
        border-radius: var(--border-radius-sm);
        border: none;
        transition: background var(--transition-fast);
    }

    .search-input-wrap:focus-within {
        background-color: var(--wa-bg-sidebar);
        box-shadow: inset 0 0 0 1.5px var(--wa-green);
    }

    .search-input-wrap i {
        color: var(--wa-text-muted) !important;
        font-size: 13px !important;
        flex-shrink: 0;
    }

    .search-input-wrap input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 13px;
        color: var(--wa-text-primary);
    }
    .search-input-wrap input::placeholder {
        color: var(--wa-text-muted);
    }

    /* ── Contacts Scroll List ── */
    .contacts-scroll-list {
        flex: 1;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #c5c6c8 transparent;
    }
    .contacts-scroll-list::-webkit-scrollbar {
        width: 6px;
    }
    .contacts-scroll-list::-webkit-scrollbar-thumb {
        background: #c5c6c8;
        border-radius: 3px;
    }
    .contacts-scroll-list::-webkit-scrollbar-track {
        background: transparent;
    }

    /* ── Contact Item ── */
    .contact-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        border-bottom: none;
        cursor: pointer;
        transition: background var(--transition-fast);
        position: relative;
    }
    .contact-item::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 72px;
        right: 16px;
        height: 1px;
        background: var(--wa-border);
    }
    .contact-item:last-child::after {
        display: none;
    }

    .contact-item:hover {
        background-color: var(--wa-bg-hover);
    }

    .contact-item.active {
        background-color: var(--wa-bg-hover);
        border-left: none;
    }

    /* ── Contact Avatar ── */
    .contact-avatar-circle {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .contact-details {
        flex: 1;
        min-width: 0;
    }

    .contact-name-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .contact-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--wa-text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .contact-time {
        font-size: 12px;
        color: var(--wa-text-muted);
        flex-shrink: 0;
        margin-left: 8px;
    }
    .contact-item.active .contact-time,
    .contact-item:has(.unread-pill) .contact-time {
        color: var(--wa-green);
    }

    .contact-snippet-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .contact-snippet {
        font-size: 13.5px;
        color: var(--wa-text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        line-height: 1.3;
    }

    .unread-pill {
        background-color: #25D366;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.25);
        animation: unreadPulse 1.8s ease-in-out infinite;
    }

    @keyframes unreadPulse {
        0%   { box-shadow: 0 0 0 0px rgba(37, 211, 102, 0.4); }
        50%  { box-shadow: 0 0 0 5px rgba(37, 211, 102, 0); }
        100% { box-shadow: 0 0 0 0px rgba(37, 211, 102, 0); }
    }

    /* Unread contact item — green left border */
    .contact-item.has-unread {
        border-left: 3px solid #25D366;
    }
    
    /* ── Sidebar Hover Menu ── */
    .sidebar-menu-btn {
        display: none;
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--wa-bg-hover);
        border: none;
        color: var(--wa-text-secondary);
        padding: 5px 8px;
        border-radius: 4px;
        cursor: pointer;
        z-index: 10;
        box-shadow: -10px 0 10px var(--wa-bg-hover);
    }
    .contact-item:hover .sidebar-menu-btn {
        display: block;
    }
    .sidebar-menu-dropdown {
        display: none;
        position: absolute;
        right: 16px;
        top: 70%;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border-radius: 8px;
        z-index: 100;
        min-width: 170px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .chat-window.theme-dark ~ .contacts-sidebar .sidebar-menu-dropdown,
    .chat-window.theme-midnight ~ .contacts-sidebar .sidebar-menu-dropdown {
        background: #202c33;
        border-color: #111b21;
    }
    .sidebar-menu-dropdown.show {
        display: block;
    }
    .sidebar-dropdown-item {
        padding: 12px 16px;
        cursor: pointer;
        font-size: 14px;
        color: #334155;
        transition: background 0.1s;
    }
    .sidebar-dropdown-item:hover {
        background: #f8fafc;
    }

    /* ═══════════════════════════════════════════
       CENTER — CHAT WINDOW
       ═══════════════════════════════════════════ */
    .chat-window {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--wa-bg-chat);
        position: relative;
    }

    /* ── Chat Header ── */
    .chat-window-header {
        height: var(--header-height);
        background-color: var(--wa-bg-header);
        border-bottom: none;
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(11,20,26,0.04);
        z-index: 5;
    }

    .chat-header-user {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1;
        padding-right: 12px;
    }

    .header-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #ffffff;
        font-size: 15px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        flex-shrink: 0;
    }

    .header-user-info {
        min-width: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .header-user-info .name {
        font-size: 16px;
        font-weight: 600;
        color: var(--wa-text-primary);
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .header-user-info .status {
        font-size: 13px;
        color: var(--wa-text-secondary);
        display: flex;
        align-items: center;
        gap: 5px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .header-user-info .status i {
        color: var(--wa-green) !important;
    }

    /* ═══════ MESSAGES CONTAINER ═══════ */
    .chat-messages-container {
        flex: 1;
        padding: 20px 60px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 4px;
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.15) transparent;
    }
    .chat-messages-container::-webkit-scrollbar {
        width: 6px;
    }
    .chat-messages-container::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.15);
        border-radius: 3px;
    }
    .chat-messages-container::-webkit-scrollbar-track {
        background: transparent;
    }

    /* ═══════ DATE SEPARATOR ═══════ */
    .date-separator {
        display: flex;
        justify-content: center;
        margin: 16px 0;
        z-index: 2;
    }
    .date-separator span {
        background-color: rgba(255, 255, 255, 0.95);
        color: #54656f;
        font-size: 11.5px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(11,20,26,0.1);
        letter-spacing: 0.3px;
        backdrop-filter: blur(4px);
    }
    .chat-window.theme-dark .date-separator span,
    .chat-window.theme-midnight .date-separator span {
        background-color: rgba(32, 44, 51, 0.9);
        color: rgba(255,255,255,0.7);
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    /* ═══════ MESSAGE BUBBLES ═══════ */
    .message-bubble {
        max-width: 65%;
        padding: 6px 7px 8px 9px;
        border-radius: 7.5px;
        font-size: 14.2px;
        line-height: 1.42;
        position: relative;
        box-shadow: var(--wa-bubble-shadow);
        word-wrap: break-word;
        animation: msgSlideIn 0.2s ease-out;
    }

    @keyframes msgSlideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-incoming {
        align-self: flex-start;
        background-color: var(--wa-bubble-in);
        color: var(--wa-text-primary);
        border: var(--wa-bubble-border);
        border-top-left-radius: 0;
    }

    .message-outgoing {
        align-self: flex-end;
        background-color: var(--wa-bubble-out);
        color: var(--wa-text-primary);
        border-top-right-radius: 0;
    }

    .message-bubble.single-emoji {
        font-size: 40px;
        background-color: transparent !important;
        box-shadow: none !important;
        padding: 4px;
        animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    @keyframes popIn {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .message-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        font-size: 11px;
        color: var(--wa-text-muted);
        margin-top: 2px;
    }
    .message-meta i.fa-check-double {
        color: #53bdeb;
    }

    /* Template Formatting */
    .template-header {
        font-weight: 600;
        font-size: 15.5px;
        margin-bottom: 5px;
        line-height: 1.3;
    }
    .template-footer {
        font-size: 13px;
        opacity: 0.65;
        margin-top: 6px;
    }

    /* ═══════ THEME VARIANTS ═══════ */
    .chat-window.theme-dark {
        background-color: #0b141a;
        background-image: none;
    }
    .chat-window.theme-dark .message-incoming {
        background-color: #202c33;
        color: #e9edef;
        box-shadow: none;
    }
    .chat-window.theme-dark .message-outgoing {
        background-color: #005c4b;
        color: #e9edef;
        box-shadow: none;
    }
    .chat-window.theme-dark .message-meta {
        color: rgba(233,237,239,0.6);
    }

    .chat-window.theme-doodle {
        background-color: #e5ddd5;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png') !important;
        background-size: 400px;
        background-repeat: repeat;
    }

    .chat-window.theme-midnight {
        background-color: #172554 !important;
        background-image: none !important;
    }
    .chat-window.theme-midnight .message-incoming { background-color: #1e3a8a; color: #eff6ff; }
    .chat-window.theme-midnight .message-outgoing { background-color: #3b82f6; color: #ffffff; }
    .chat-window.theme-midnight .message-meta { color: rgba(255,255,255,0.5); }

    .chat-window.theme-sunset {
        background: linear-gradient(135deg, #fef3c7 0%, #ffedd5 50%, #fed7aa 100%) !important;
        background-image: none !important;
    }
    .chat-window.theme-sunset .message-incoming { background-color: #ffffff; color: #431407; }
    .chat-window.theme-sunset .message-outgoing { background-color: #f97316; color: #ffffff; }
    .chat-window.theme-sunset .message-meta { color: rgba(0,0,0,0.45); }

    .chat-window.theme-ocean {
        background: linear-gradient(to bottom right, #f0fdfa, #ccfbf1) !important;
        background-image: none !important;
    }
    .chat-window.theme-ocean .message-incoming { background-color: #ffffff; color: #134e4a; }
    .chat-window.theme-ocean .message-outgoing { background-color: #0d9488; color: #ffffff; }
    .chat-window.theme-ocean .message-meta { color: rgba(0,0,0,0.45); }

    .chat-window.theme-grey {
        background-color: #f8fafc !important;
        background-image: none !important;
    }
    .chat-window.theme-grey .message-incoming { background-color: #ffffff; color: #334155; border: 1px solid #e2e8f0; }
    .chat-window.theme-grey .message-outgoing { background-color: #475569; color: #ffffff; }
    .chat-window.theme-grey .message-meta { color: rgba(0,0,0,0.4); }

    /* ═══════ REPLY BAR / COMPOSER ═══════ */
    .chat-reply-bar {
        background-color: var(--wa-bg-header);
        padding: 8px 16px;
        border-top: none;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .quick-templates-row {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
    }
    .quick-templates-row::-webkit-scrollbar { display: none; }

    .quick-template-btn {
        background-color: var(--wa-bg-sidebar);
        border: 1px solid var(--wa-border);
        padding: 5px 12px;
        border-radius: var(--border-radius-pill);
        font-size: 12px;
        font-weight: 500;
        color: var(--wa-text-secondary);
        cursor: pointer;
        white-space: nowrap;
        transition: all var(--transition-fast);
    }
    .quick-template-btn:hover {
        background-color: var(--wa-green);
        color: #ffffff;
        border-color: var(--wa-green);
    }

    .reply-input-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .reply-textarea-wrap {
        flex: 1;
        background-color: var(--wa-bg-sidebar);
        border-radius: var(--border-radius-sm);
        padding: 9px 14px;
        display: flex;
        align-items: center;
        border: none;
        box-shadow: 0 1px 1px rgba(0,0,0,0.06);
    }
    .reply-textarea-wrap:focus-within {
        box-shadow: 0 1px 1px rgba(0,0,0,0.06);
        border-color: transparent;
    }

    .reply-input {
        width: 100%;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: 15px;
        background: transparent;
        color: var(--wa-text-primary);
    }
    .reply-input::placeholder {
        color: var(--wa-text-muted);
    }

    .reply-btn-send {
        width: 42px;
        height: 42px;
        background-color: var(--wa-green);
        color: #ffffff;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all var(--transition-fast);
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        flex-shrink: 0;
    }
    .reply-btn-send:hover {
        background-color: var(--wa-green-dark);
        transform: scale(1.05);
    }
    .reply-btn-send:active {
        transform: scale(0.95);
    }

    /* ═══════════════════════════════════════════
       RIGHT — PROFILE PANEL
       ═══════════════════════════════════════════ */
    .profile-pane {
        width: var(--profile-width);
        border-left: 1px solid var(--wa-border);
        background-color: var(--wa-bg-sidebar);
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #c5c6c8 transparent;
    }
    .profile-pane::-webkit-scrollbar { width: 5px; }
    .profile-pane::-webkit-scrollbar-thumb { background: #c5c6c8; border-radius: 3px; }

    .profile-pane-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        color: #ffffff;
        margin-top: 28px;
        margin-bottom: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .profile-pane-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--wa-text-primary);
    }

    .profile-pane-phone {
        font-size: 14px;
        color: var(--wa-text-secondary);
        margin-top: 3px;
        margin-bottom: 20px;
    }

    .profile-detail-card {
        width: calc(100% - 32px);
        background-color: var(--wa-bg-sidebar);
        border-radius: var(--border-radius-sm);
        padding: 14px 16px;
        margin-bottom: 2px;
        text-align: left;
        border-bottom: 1px solid var(--wa-border-light);
    }

    .profile-detail-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--wa-green);
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }

    .profile-detail-val {
        font-size: 14px;
        font-weight: 500;
        color: var(--wa-text-primary);
        line-height: 1.4;
    }

    /* ── Profile Action Buttons ── */
    .btn-save-contact {
        width: calc(100% - 32px);
        margin: 8px 16px;
        background-color: var(--wa-green);
        color: #fff;
        border: none;
        border-radius: var(--border-radius-sm);
        padding: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background var(--transition-fast);
    }
    .btn-save-contact:hover { background-color: var(--wa-green-dark); }

    .btn-edit-contact {
        background-color: var(--wa-bg-input);
        color: var(--wa-text-primary);
    }
    .btn-edit-contact:hover { background-color: var(--wa-border); }

    .btn-delete-chat {
        width: calc(100% - 32px);
        margin: 4px 16px;
        background-color: var(--wa-bg-sidebar);
        color: var(--wa-danger);
        border: 1px solid var(--wa-danger);
        border-radius: var(--border-radius-sm);
        padding: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all var(--transition-fast);
    }
    .btn-delete-chat:hover { background-color: var(--wa-danger-bg); }

    /* ═══════ MESSAGE DROPDOWN ═══════ */
    .message-dropdown {
        position: absolute;
        top: 4px;
        right: 6px;
        display: none;
    }
    .message-bubble {
        position: relative;
    }
    .message-bubble.single-emoji {
        padding-right: 0;
    }
    .message-bubble:hover .message-dropdown {
        display: block;
    }
    .msg-dropdown-btn {
        background: rgba(255,255,255,0.85);
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--wa-text-muted);
        transition: all var(--transition-fast);
        backdrop-filter: blur(4px);
    }
    .theme-dark .msg-dropdown-btn,
    .theme-midnight .msg-dropdown-btn {
        background: rgba(30,41,59,0.7);
        color: #cbd5e1;
    }
    .msg-dropdown-btn:hover {
        background: rgba(0,0,0,0.08);
    }

    .msg-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1), 0 0 2px rgba(0,0,0,0.15);
        z-index: 1000;
        min-width: 170px;
        padding: 6px;
        border: 1px solid var(--wa-border-light);
    }
    .msg-dropdown-menu.show { display: block; }

    .msg-dropdown-item {
        padding: 4px 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--wa-text-primary);
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        border: none;
        width: 100%;
        background: none;
        text-align: left;
        border-radius: 6px;
        margin-bottom: 2px;
    }
    .msg-dropdown-item:last-child { margin-bottom: 0; }
    .msg-dropdown-item:hover { 
        background-color: var(--wa-bg-hover); 
        transform: translateX(4px);
    }
    .msg-dropdown-item.danger { color: #dc2626; }
    .msg-dropdown-item.danger:hover { background-color: #fef2f2; }

    /* ═══════ PROFILE SETTINGS DROPDOWN ═══════ */
    .profile-settings-dropdown {
        position: relative;
        width: calc(100% - 32px);
        margin: 8px 16px;
    }
    .btn-profile-settings {
        width: 100%;
        background-color: var(--wa-bg-input);
        color: var(--wa-text-primary);
        border: none;
        border-radius: var(--border-radius-sm);
        padding: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background var(--transition-fast);
    }
    .btn-profile-settings:hover { background-color: var(--wa-border); }

    .profile-settings-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12), 0 0 1px rgba(0,0,0,0.1);
        z-index: 100;
        margin-top: 4px;
        padding: 8px;
        border: 1px solid var(--wa-border-light);
    }
    .profile-settings-menu.show { display: block; }

    .profile-settings-item {
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--wa-text-primary);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        border: none;
        width: 100%;
        background: none;
        text-align: left;
        border-radius: 8px;
        margin-bottom: 2px;
    }
    .profile-settings-item:last-child { margin-bottom: 0; }
    .profile-settings-item:hover { 
        background-color: var(--wa-bg-hover); 
        transform: translateX(4px);
    }
    .profile-settings-item.danger { color: #dc2626; }
    .profile-settings-item.danger:hover { background-color: var(--wa-danger-bg); }

    /* ═══════ MODALS ═══════ */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(11,20,26,0.45);
        backdrop-filter: blur(2px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 1;
        transition: opacity var(--transition-normal);
    }
    .modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .custom-modal {
        background-color: var(--wa-bg-sidebar);
        border-radius: var(--border-radius-md);
        width: 100%;
        max-width: 420px;
        box-shadow: 0 8px 32px rgba(11,20,26,0.12);
        transform: scale(0.96);
        transition: transform var(--transition-smooth);
        overflow: hidden;
    }
    .modal-overlay.active .custom-modal {
        transform: scale(1);
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--wa-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--wa-green);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #ffffff;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 18px;
        color: rgba(255,255,255,0.8);
        cursor: pointer;
        padding: 4px;
        transition: color var(--transition-fast);
    }
    .modal-close:hover { color: #ffffff; }

    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--wa-text-header);
        margin-bottom: 6px;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--wa-border);
        border-radius: var(--border-radius-sm);
        font-family: inherit;
        font-size: 15px;
        transition: border-color var(--transition-fast);
        background: var(--wa-bg-sidebar);
        color: var(--wa-text-primary);
    }
    .form-input:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--wa-green);
    }

    .modal-footer {
        padding: 16px 24px;
        background-color: var(--wa-bg-header);
        border-top: 1px solid var(--wa-border);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-modal {
        padding: 8px 20px;
        border-radius: var(--border-radius-sm);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all var(--transition-fast);
    }
    .btn-cancel {
        background-color: var(--wa-bg-sidebar);
        color: var(--wa-text-secondary);
        border: 1px solid var(--wa-border);
    }
    .btn-cancel:hover { background-color: var(--wa-bg-hover); }

    .btn-primary-save {
        background-color: var(--wa-green);
        color: #ffffff;
    }
    .btn-primary-save:hover { background-color: var(--wa-green-dark); }

    /* ═══════ MENU ICONS & ATTACHMENT MENU ═══════ */
    #attachmentMenu {
        background-color: #ffffff !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12), 0 0 1px rgba(0,0,0,0.1) !important;
        border: 1px solid var(--wa-border-light) !important;
        padding: 8px !important;
        width: 185px !important;
    }
    
    .attachment-item {
        padding: 3px 3px !important;
        border-radius: 8px;
        margin-bottom: 2px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .attachment-item:last-child { margin-bottom: 0; }
    
    .attachment-item:hover {
        background-color: var(--wa-bg-hover) !important;
        transform: translateX(4px);
    }
    
    /* Icon colorful badges for ALL menus */
    .msg-dropdown-item i, 
    .profile-settings-item i, 
    .attachment-item i {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 24px !important;
        height: 24px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        transition: all 0.2s ease !important;
        background-color: #f3f4f6; /* default gray */
        color: #4b5563;
        flex-shrink: 0;
    }
    
    .msg-dropdown-item:hover i, 
    .profile-settings-item:hover i, 
    .attachment-item:hover i {
        filter: brightness(0.95);
        transform: scale(1.08);
    }

    /* Specific Icon Colors - Pastel Backgrounds */
    /* Document */
    .attachment-item i.fa-file-lines { background-color: #eef2ff !important; color: #4f46e5 !important; }
    /* Image/Video */
    .attachment-item i.fa-image { background-color: #fdf4ff !important; color: #d946ef !important; }
    /* Contact */
    .attachment-item i.fa-user, .profile-settings-item i.fa-user { background-color: #f0fdf4 !important; color: #16a34a !important; }
    /* Sticker */
    .attachment-item i.fa-note-sticky { background-color: #fffbeb !important; color: #d97706 !important; }
    
    /* Header Dropdown Colors */
    .color-theme i, .color-theme:hover { background-color: #fff7ed !important; color: #ea580c !important; }
    .color-copy i, .color-copy:hover { background-color: #eef2ff !important; color: #4f46e5 !important; }
    .color-export i, .color-export:hover { background-color: #f0fdf4 !important; color: #16a34a !important; }
    .color-save i, .color-save:hover { background-color: #fdf4ff !important; color: #d946ef !important; }
    .color-block i, .color-block:hover { background-color: #fef2f2 !important; color: #dc2626 !important; }
    .color-forward i, .color-forward:hover { background-color: #f0fdfa !important; color: #0d9488 !important; }
    
    .color-theme:hover { background-color: rgba(234, 88, 12, 0.04) !important; color: #ea580c !important; }
    .color-copy:hover { background-color: rgba(79, 70, 229, 0.04) !important; color: #4f46e5 !important; }
    .color-export:hover { background-color: rgba(22, 163, 74, 0.04) !important; color: #16a34a !important; }
    .color-save:hover { background-color: rgba(217, 70, 239, 0.04) !important; color: #d946ef !important; }
    .color-block:hover { background-color: rgba(220, 38, 38, 0.04) !important; color: #dc2626 !important; }
    .color-forward:hover { background-color: rgba(13, 148, 136, 0.04) !important; color: #0d9488 !important; }

    /* Theme Submenu */
    .theme-submenu {
        display: none;
        position: absolute;
        top: 0;
        right: calc(100% + 4px);
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1), 0 0 2px rgba(0,0,0,0.15);
        z-index: 1001;
        min-width: 160px;
        padding: 4px;
        border: 1px solid var(--wa-border-light);
    }
    .theme-submenu.show {
        display: block;
    }
    .theme-option {
        padding: 6px 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--wa-text-primary);
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 6px;
        transition: background 0.2s;
        margin-bottom: 2px;
    }
    .theme-option:last-child { margin-bottom: 0; }
    .theme-option:hover {
        background-color: #f8fafc;
    }
    .theme-option.active {
        background-color: #fff7ed;
        color: #ea580c;
    }
    .theme-color-circle {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    /* Delete / Danger (Generic) */
    .msg-dropdown-item.danger i, .profile-settings-item.danger i, .msg-dropdown-item i.fa-trash, .profile-settings-item i.fa-trash { background-color: #fef2f2 !important; color: #dc2626 !important; }
    
    /* Assuming other common icons */
    .msg-dropdown-item i.fa-magnifying-glass, .profile-settings-item i.fa-magnifying-glass { background-color: #eff6ff !important; color: #3b82f6 !important; }
    .msg-dropdown-item i.fa-thumbtack, .profile-settings-item i.fa-thumbtack { background-color: #faf5ff !important; color: #9333ea !important; }
    .msg-dropdown-item i.fa-bell-slash, .profile-settings-item i.fa-bell-slash { background-color: #fff7ed !important; color: #ea580c !important; }
    .msg-dropdown-item i.fa-check-double, .profile-settings-item i.fa-check-double { background-color: #f0fdf4 !important; color: #16a34a !important; }
    .msg-dropdown-item i.fa-reply, .profile-settings-item i.fa-reply { background-color: #f3e8ff !important; color: #a855f7 !important; }
    .msg-dropdown-item i.fa-copy, .profile-settings-item i.fa-copy { background-color: #f1f5f9 !important; color: #475569 !important; }

    /* ═══════ STATUS BADGES ═══════ */
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        white-space: nowrap;
    }
    .status-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-badge.active {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1px solid #bbf7d0;
        color: #15803d;
    }
    .status-badge.active .dot {
        background-color: #22c55e;
        animation: pulse-green 2s infinite;
    }

    .status-badge.closed {
        background: linear-gradient(135deg, #fff1f2, #ffe4e6);
        border: 1px solid #fecdd3;
        color: #be123c;
    }
    .status-badge.closed .dot {
        background-color: #f43f5e;
    }

    .status-badge.blocked {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1px solid #fecaca;
        color: #b91c1c;
    }
    .status-badge.blocked i {
        font-size: 10px;
    }

    /* ═══════ RESPONSIVE ═══════ */
    @media (max-width: 1100px) {
        .profile-pane {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .contacts-sidebar {
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 20;
        }
        .chat-page-container {
            height: calc(100vh - 60px);
        }
        .chat-messages-container {
            padding: 12px 16px;
        }
        .message-bubble {
            max-width: 85%;
        }
    }

    /* ═══════ DATE SEPARATOR (WhatsApp style) ═══════ */
    .date-separator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 14px 0 10px;
        width: 100%;
    }
    .date-separator span {
        background-color: #ffffff;
        color: #54656f;
        font-size: 12.5px;
        font-weight: 500;
        padding: 5px 12px;
        border-radius: 7px;
        box-shadow: 0 1px 0.5px rgba(11,20,26,0.13);
        letter-spacing: 0.2px;
    }
    /* Dark theme support */
    .chat-window.theme-dark .date-separator span,
    .chat-window.theme-midnight .date-separator span {
        background-color: #182229;
        color: #8696a0;
    }
    .chat-window.theme-sunset .date-separator span,
    .chat-window.theme-ocean .date-separator span,
    .chat-window.theme-grey .date-separator span {
        background-color: rgba(255,255,255,0.85);
        color: #54656f;
    }

    /* ═══════ PRINT STYLES ═══════ */
    @media print {
        @page {
            margin: 0.5cm;
        }
        body, html {
            height: auto !important;
            background: #efeae2 !important;
        }
        .top-active-avatars-bar, 
        .contacts-sidebar, 
        .profile-pane,
        .chat-window-header, 
        .chat-reply-bar,
        #printActionBanner,
        .navbar,
        header,
        footer {
            display: none !important;
        }
        .chat-page-container, 
        .chat-main-split, 
        .chat-window {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
            position: static !important;
            width: 100% !important;
            box-shadow: none !important;
        }
        
        .chat-messages-container {
            display: flex !important;
            flex-direction: column !important;
            height: auto !important;
            overflow: visible !important;
            padding: 10px !important;
            background-color: #efeae2 !important;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .message-incoming {
            background-color: #ffffff !important;
            align-self: flex-start !important;
            border: 1px solid #e9edef !important;
            margin-bottom: 10px !important;
        }

        .message-outgoing {
            background-color: #d9fdd3 !important;
            align-self: flex-end !important;
            margin-bottom: 10px !important;
        }

        .message-bubble {
            max-width: 75% !important;
            page-break-inside: avoid;
        }

        img.emoji {
            height: 1.2em !important;
            width: 1.2em !important;
            max-width: 1.2em !important;
            max-height: 1.2em !important;
        }
        .message-bubble.single-emoji img.emoji {
            height: 48px !important;
            width: 48px !important;
            max-width: 48px !important;
            max-height: 48px !important;
        }
        
        .message-dropdown, .msg-dropdown-btn {
            display: none !important;
        }
    }
</style>

<div class="chat-page-container">
    <!-- CSRF Token for Dynamic AJAX Requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ─── CHAT MAIN SPLIT VIEW ───────────────────────────── -->
    <div class="chat-main-split">
        <!-- ─── LEFT CONTACTS PANEL ─────────────────────────── -->
        <div class="contacts-sidebar">
            <div class="filter-tabs-header" style="flex-direction: column; align-items: flex-start; gap: 8px; padding: 12px 16px;">
                <div style="font-size: 18px; font-weight: 700; color: var(--wa-text-primary);">Chats</div>
                <style>
                    .chat-filter-tab {
                        border: none;
                        background: none;
                        font-size: 13px;
                        font-weight: 600;
                        color: var(--wa-text-secondary);
                        padding: 6px 14px;
                        cursor: pointer;
                        border-radius: 16px;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                    }
                    .chat-filter-tab:hover {
                        background-color: #e2e8f0;
                        color: var(--wa-text-primary);
                    }
                    .chat-filter-tab.active {
                        background-color: #10b981;
                        color: white;
                    }
                    .chat-filter-tab.active:hover {
                        background-color: #059669;
                    }
                </style>
                <div style="display: flex; gap: 8px; width: 100%; overflow-x: auto; scrollbar-width: none; border-bottom: 1px solid var(--wa-border); padding-bottom: 6px;">
                    <button class="chat-filter-tab active" onclick="filterChats('live', this)">Chat</button>
                    <button class="chat-filter-tab" onclick="filterChats('sent', this)">Sent Messages</button>
                </div>
            </div>

            <!-- Search Box -->
            <div class="contacts-search-area">
                <div class="search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass" style="color: #94a3b8; font-size: 12px;"></i>
                    <input type="text" id="contactSearchInput" placeholder="Search name or mobile number..." onkeyup="searchContacts()">
                </div>
            </div>

            <!-- Contacts List -->
            <div class="contacts-scroll-list" id="contactsList">
                @php $isFirstContact = true; $firstPhone = null; @endphp
                @forelse($conversations as $phone => $conv)
                    @if($isFirstContact) @php $firstPhone = $phone; @endphp @endif
                    <div class="contact-item {{ $isFirstContact ? 'active' : '' }}" data-phone="{{ $phone }}" data-category="{{ $conv['category'] }}" data-unread="{{ $conv['unread_count'] }}" data-live="{{ $conv['last_incoming_at'] ? '1' : '0' }}" onclick="selectContact('{{ $phone }}')">
                        <div class="contact-avatar-circle" style="background-color: {{ $conv['color'] }};">
                            {{ $conv['initial'] }}
                        </div>
                        <div class="contact-details">
                            <div class="contact-name-row">
                                <span class="contact-name" style="font-weight: {{ $conv['unread_count'] > 0 ? '700' : '600' }};">{{ $conv['display_name'] }}</span>
                                <span class="contact-time">{{ $conv['last_time'] }}</span>
                            </div>
                            <div class="contact-snippet-row">
                                <span class="contact-snippet" style="font-weight: {{ $conv['unread_count'] > 0 ? '700' : 'normal' }}; color: {{ $conv['unread_count'] > 0 ? 'var(--wa-text-primary)' : 'var(--wa-text-secondary)' }}">{{ $conv['last_message'] }}</span>
                                @if($conv['unread_count'] > 0)
                                    <span class="unread-pill">{{ $conv['unread_count'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @php $isFirstContact = false; @endphp
                @empty
                    <div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13px;">
                        <i class="fa-solid fa-comments" style="font-size: 32px; margin-bottom: 8px; color: #cbd5e1;"></i>
                        <p>No campaign messages found in database.</p>
                        <a href="{{ route('send.wa.form') }}" style="color: #25D366; font-weight: 700; text-decoration: underline; margin-top: 6px; display: inline-block;">Send a Campaign Now</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ─── CENTER CHAT MESSAGES WINDOW ────────────────── -->
        <div class="chat-window theme-doodle">
            <!-- Header -->
            <div class="chat-window-header">
                <div class="chat-header-user">
                    <div class="header-user-avatar" id="activeHeaderAvatar" style="background-color: #6366f1;">C</div>
                    <div class="header-user-info">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="name" id="activeHeaderName">Select a Contact</div>
                            <!-- 24-Hour Window Status Indicator moved next to name -->
                            <div id="windowStatusContainer" style="font-size: 11.5px; font-weight: 700; white-space: nowrap;">
                                <!-- Dynamically Populated via JS -->
                            </div>
                        </div>
                        <div class="status" id="activeHeaderStatus">
                            <span style="display:inline-flex; align-items:center; gap:5px; color: #64748b;">
                                <span style="display:inline-block; width:8px; height:8px; background-color:#22c55e; border-radius:50%; box-shadow: 0 0 0 2px rgba(34,197,94,0.2);"></span>
                                WhatsApp
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Right Side Actions -->
                <div style="display: flex; align-items: center; gap: 16px; flex-shrink: 0;">
                    <!-- 3 Dot Menu Container -->
                    <div style="position: relative;" id="headerActionContainer">
                        <!-- Dynamically Populated via JS -->
                    </div>
                </div>
            </div>

            <!-- Print Action Banner (Hidden by default) -->
            <div id="printActionBanner" style="display: none; align-items: center; justify-content: space-between; background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 10px 20px; z-index: 10; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <input type="checkbox" id="printSelectAll" onclick="toggleSelectAllForPrint(this)" style="width: 16px; height: 16px; cursor: pointer;">
                    <label for="printSelectAll" style="color: #334155; font-weight: 600; cursor: pointer;">Select All</label>
                    <span id="printSelectionCount" style="color: #64748b; font-size: 13px; margin-left: 8px;">0 selected</span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="button" class="btn-modal btn-cancel" onclick="cancelPrintMode()" style="padding: 6px 12px; font-size: 13px;">Cancel</button>
                    <button type="button" class="btn-modal btn-primary-save" onclick="executePrintSelected()" style="padding: 6px 12px; font-size: 13px;"><i class="fa-solid fa-print"></i> Print Selected</button>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div class="chat-messages-container" id="chatMessagesStream">
                <!-- Messages appended dynamically -->
            </div>

            <!-- Interactive Reply Form Bar -->
            <div class="chat-reply-bar">
                <!-- Quick Templates Shortcuts -->
                <div class="quick-templates-row">
                    <button class="quick-template-btn" onclick="insertTemplate('Thank you for contacting us! How can we assist you today?')">⚡ Quick Support</button>
                    <button class="quick-template-btn" onclick="insertTemplate('Your campaign request has been received and is being processed.')">⚡ Campaign Update</button>
                    <button class="quick-template-btn" onclick="insertTemplate('Please share your details so our team can get in touch.')">⚡ Request Info</button>
                </div>

                <!-- Reply Input & Send Button -->
                <div class="reply-input-row" style="position: relative;">
                    <div style="position: relative;" id="attachmentMenuContainer">
                        <button type="button" id="attachmentBtn" style="background: none; border: none; font-size: 18px; color: #64748b; cursor: pointer; margin: 0; display: flex; align-items: center;" title="Attach File">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                        
                        <!-- Attachment Dropdown Menu -->
                        <div id="attachmentMenu" style="display: none; position: absolute; bottom: 50px; left: 0; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); width: 220px; z-index: 1000; padding: 12px 0; overflow: hidden; border: 1px solid #e2e8f0;">
                            
                            <label class="attachment-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; transition: background 0.2s; font-size: 14px; color: #334155; font-weight: 500;">
                                <i class="fa-solid fa-file-lines" style="color: #7f66ff; font-size: 18px; width: 24px; text-align: center;"></i>
                                Document
                                <input type="file" style="display: none;" onchange="handleMediaSelect(this)" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.cdr,.pmd,.psd" multiple>
                            </label>
                            
                            <label class="attachment-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; transition: background 0.2s; font-size: 14px; color: #334155; font-weight: 500;">
                                <i class="fa-solid fa-image" style="color: #007bf5; font-size: 18px; width: 24px; text-align: center;"></i>
                                Photos & videos
                                <input type="file" style="display: none;" onchange="handleMediaSelect(this)" accept="image/*,video/*" multiple>
                            </label>


                            <div class="attachment-item" onclick="openShareContactModal()" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; transition: background 0.2s; font-size: 14px; color: #334155; font-weight: 500;">
                                <i class="fa-solid fa-user" style="color: #00a884; font-size: 18px; width: 24px; text-align: center;"></i>
                                Contact
                            </div>

                            <div class="attachment-item" onclick="openStickerModal()" style="display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; transition: background 0.2s; font-size: 14px; color: #334155; font-weight: 500;">
                                <i class="fa-solid fa-note-sticky" style="color: #02c0b6; font-size: 18px; width: 24px; text-align: center;"></i>
                                Sticker
                            </div>
                        </div>
                    </div>
                    <div id="mediaPreviewContainer" style="display: none; align-items: center; gap: 8px; font-size: 11px; background: transparent; padding: 4px 8px; margin-left: 8px; flex-wrap: wrap; max-width: 300px; overflow-x: auto;">
                        <!-- Dynamically populated previews -->
                    </div>
                    <button type="button" id="emojiBtn" style="background: none; border: none; font-size: 18px; color: #64748b; cursor: pointer;" title="Add Emoji">
                        <i class="fa-regular fa-face-smile"></i>
                    </button>
                    <!-- Emoji Picker Container (Hidden by default) -->
                    <div id="emojiPickerContainer" style="display: none; position: absolute; bottom: 50px; left: 0; z-index: 1000;">
                        <!-- Emoji Mart will mount here -->
                    </div>
                    
                    <div class="reply-textarea-wrap">
                        <input type="text" id="replyInput" class="reply-input" placeholder="Type a message to reply on WhatsApp..." onkeypress="handleKeyPress(event)">
                    </div>
                    <!-- Unread Count before reply -->
                    <span id="replyUnreadBadge" class="unread-pill" style="display: none; margin-right: 8px; font-size: 13px; min-width: 24px; height: 24px;"></span>
                    
                    <button type="button" class="reply-btn-send" id="btnSendReply" onclick="sendReplyMessage()" title="Send WhatsApp Message">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Save Contact Modal -->
<div class="modal-overlay" id="saveContactModal">
    <div class="custom-modal">
        <div class="modal-header">
            <div class="modal-title" id="saveContactModalTitle">Save Contact</div>
            <button class="modal-close" onclick="closeSaveContactModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form id="saveContactForm" onsubmit="submitSaveContact(event)">
                <div class="form-group">
                    <label class="form-label">Phone Number (Required)</label>
                    <input type="text" id="scPhone" class="form-input" readonly style="background-color: #f1f5f9;">
                </div>
                <div class="form-group">
                    <label class="form-label">Name (Required)</label>
                    <input type="text" id="scName" class="form-input" required placeholder="Enter Name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email (Optional)</label>
                    <input type="email" id="scEmail" class="form-input" placeholder="Enter Email">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea id="scNotes" class="form-textarea" rows="3" placeholder="Enter Notes"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="closeSaveContactModal()">Cancel</button>
            <button type="submit" form="saveContactForm" class="btn-modal btn-primary-save" id="btnSubmitContact">Save Contact</button>
        </div>
    </div>
</div>

<!-- Share Contact Modal -->
<div class="modal-overlay" id="shareContactModal" style="display: none;">
    <div class="custom-modal" style="max-width: 400px; width: 100%;">
        <div class="modal-header">
            <div class="modal-title">Select Contact to Share</div>
            <button class="modal-close" onclick="closeShareContactModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 16px;">
            <div class="search-input-wrap" style="margin-bottom: 16px;">
                <i class="fa-solid fa-magnifying-glass" style="color: #94a3b8;"></i>
                <input type="text" id="shareContactSearch" placeholder="Search contacts..." onkeyup="filterShareContacts()">
            </div>
            <div id="shareContactList">
                @if(isset($allContacts) && count($allContacts) > 0)
                    @foreach($allContacts as $c)
                        <div class="contact-item share-contact-item" onclick="confirmShareContact('{{ addslashes($c->name) }}', '{{ $c->phone }}')" style="border-radius: 8px; margin-bottom: 4px; border: 1px solid #f1f5f9;">
                            <div class="contact-avatar-circle" style="background-color: #6366f1; width: 36px; height: 36px; font-size: 14px;">
                                {{ strtoupper(substr($c->name ?: 'U', 0, 1)) }}
                            </div>
                            <div class="contact-details">
                                <div class="contact-name" style="font-size: 14px;">{{ $c->name ?: 'Unknown' }}</div>
                                <div class="contact-snippet" style="font-size: 12px;">+{{ $c->phone }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; color: #94a3b8; padding: 20px;">No saved contacts found.</div>
                @endif
            </div>
        </div>
        <div class="modal-footer" style="padding: 12px 16px; border-top: 1px solid #e2e8f0; text-align: right; display: flex; justify-content: flex-end;">
            <button type="button" class="btn-modal btn-cancel" onclick="closeShareContactModal()" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Cancel</button>
        </div>
    </div>
</div>

<!-- Forward Message Modal -->
<div class="modal-overlay" id="forwardModal" style="display: none;">
    <div class="custom-modal" style="max-width: 420px; width: 100%; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <!-- Header -->
        <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: none;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <button class="modal-close" onclick="closeForwardModal()" style="font-size: 18px; color: #54656f; background: none; border: none; cursor: pointer; padding: 0;"><i class="fa-solid fa-xmark"></i></button>
                <div class="modal-title" style="font-size: 16px; font-weight: 600; color: #111b21;">Forward message to</div>
            </div>
            <button style="color: #54656f; background: none; border: none; cursor: pointer; font-size: 18px;"><i class="fa-solid fa-user-plus"></i></button>
        </div>
        
        <!-- Search Bar -->
        <div style="padding: 10px 20px 12px 20px;">
            <div class="search-input-wrap" style="position: relative; display: flex; align-items: center; border: 2px solid #25D366; border-radius: 24px; padding: 8px 16px; background: #fff;">
                <i class="fa-solid fa-magnifying-glass" style="color: #54656f; font-size: 14px; margin-right: 12px;"></i>
                <input type="text" id="forwardContactSearch" placeholder="Search name, number or @username" onkeyup="filterForwardContacts()" style="border: none; outline: none; width: 100%; font-size: 14px; color: #111b21;">
            </div>
        </div>

        <!-- Recent chats label -->
        <div style="padding: 0 20px; font-size: 13px; color: #8696a0; font-weight: 500; margin-bottom: 8px;">
            Recent chats
        </div>

        <!-- Body / Contact List -->
        <div class="modal-body" style="flex: 1; overflow-y: auto; padding: 0 12px; max-height: 350px;" id="forwardContactList">
            @if(isset($allContacts) && count($allContacts) > 0)
                @foreach($allContacts as $c)
                    <div class="contact-item forward-contact-item" style="border-radius: 12px; padding: 10px 12px; margin-bottom: 2px; cursor: pointer; display: flex; align-items: center; transition: background 0.1s;" onclick="toggleForwardSelection(this, '{{ $c->phone }}')" onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="if(!this.classList.contains('selected-forward')){this.style.backgroundColor='transparent'}">
                        <!-- Checkbox -->
                        <div class="forward-checkbox" style="margin-right: 16px; width: 18px; height: 18px; border: 2px solid #8696a0; border-radius: 4px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                            <i class="fa-solid fa-check" style="color: white; font-size: 10px; display: none;"></i>
                        </div>
                        <!-- Avatar -->
                        <div class="contact-avatar-circle" style="background-color: #6366f1; width: 44px; height: 44px; font-size: 18px; margin-right: 12px;">
                            {{ strtoupper(substr($c->name ?: 'U', 0, 1)) }}
                        </div>
                        <!-- Details -->
                        <div class="contact-details" style="flex: 1;">
                            <div class="contact-name" style="font-size: 16px; color: #111b21; display: flex; align-items: center; gap: 4px;">
                                {{ $c->name ?: 'Unknown' }}
                                @if($c->is_saved)
                                    <i class="fa-solid fa-circle-check" style="color: #007bff; font-size: 12px;"></i>
                                @endif
                            </div>
                            <div class="contact-snippet" style="font-size: 13px; color: #667781; margin-top: 2px;">+{{ $c->phone }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; color: #8696a0; padding: 20px;">No contacts found.</div>
            @endif
        </div>

        <!-- Selected Footer -->
        <div id="forwardFooter" style="background: #f0f2f5; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #e9edef; display: none;">
            <div style="display: flex; align-items: center; gap: 16px; color: #54656f;">
                <button onclick="clearForwardSelection()" style="background: none; border: none; cursor: pointer; padding: 0; color: #54656f; font-size: 16px;"><i class="fa-solid fa-xmark"></i></button>
                <span id="forwardSelectedCount" style="font-size: 14px; font-weight: 500;">1 selected</span>
            </div>
            <button class="btn-primary" onclick="submitForward()" id="btnSubmitForward" style="width: 44px; height: 44px; border-radius: 50%; background: #00a884; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
        <input type="hidden" id="forwardMsgId">
    </div>
</div>

<!-- Sticker Modal -->
<div class="modal-overlay" id="stickerModal" style="display: none;">
    <div class="custom-modal" style="max-width: 450px; width: 100%;">
        <div class="modal-header">
            <div class="modal-title">Select a Sticker</div>
            <button class="modal-close" onclick="closeStickerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="padding: 16px;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; max-height: 400px; overflow-y: auto;" id="stickerGrid">
                <!-- Stickers populated via JS -->
            </div>
        </div>
        <div class="modal-footer" style="padding: 12px 16px; border-top: 1px solid #e2e8f0; text-align: right; display: flex; justify-content: flex-end;">
            <button type="button" class="btn-modal btn-cancel" onclick="closeStickerModal()" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;">Cancel</button>
        </div>
    </div>
</div>

<!-- Dynamic Live Chat JavaScript -->
<!-- Scripts -->
<!-- Emoji Mart (Apple set like WhatsApp) -->
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>
<!-- Twemoji to parse native emojis into modern SVGs in the chat UI -->
<script src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
<script>
    const userPermissions = {
        canReply: {{ Auth::user()->hasPermission('livechat.reply') ? 'true' : 'false' }},
        canManage: {{ Auth::user()->hasPermission('livechat.manage') ? 'true' : 'false' }},
        canDelete: {{ Auth::user()->hasPermission('livechat.delete') ? 'true' : 'false' }},
        canExport: {{ Auth::user()->hasPermission('livechat.export') ? 'true' : 'false' }},
        canSaveContact: {{ Auth::user()->hasPermission('contacts.create') ? 'true' : 'false' }}
    };

    // Initial Conversations Data from PHP
    let conversationsData = @json($conversations);
    let orderedPhones = @json(array_keys($conversations));
    let activePhone = @json($firstPhone);
    // Phones marked as unread manually — poll will NOT reset their unread_count
    let pendingUnreadPhones = new Set();

    /**
     * After any conversationsData = data.conversations replacement,
     * restore unread_count for ALL phones that were manually marked unread.
     * This prevents polling from wiping out the manually-set unread state.
     */
    function restorePendingUnread() {
        pendingUnreadPhones.forEach(ph => {
            if (conversationsData[ph]) {
                conversationsData[ph].unread_count = Math.max(
                    conversationsData[ph].unread_count, 1
                );
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (activePhone && conversationsData[activePhone]) {
            selectContact(activePhone);
        } else {
            // Apply default theme if no contact selected
            applyTheme('light');
        }

        // Start Auto-Polling every 4 seconds
        setInterval(pollLiveData, 4000);
    });

    function toggleHeaderMenu(e) {
        e.stopPropagation();
        const menu = document.getElementById('headerDropdownMenu');
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    
    // hide on outside click
    document.addEventListener('click', function(e) {
        const headerMenu = document.getElementById('headerDropdownMenu');
        if (headerMenu && !e.target.closest('#headerActionContainer')) {
            headerMenu.classList.remove('show');
            const themeSubmenu = headerMenu.querySelector('.theme-submenu');
            if (themeSubmenu) themeSubmenu.classList.remove('show');
        }
        
        // Hide sidebar menus
        if (!e.target.closest('.sidebar-menu-btn') && !e.target.closest('.sidebar-menu-dropdown')) {
            document.querySelectorAll('.sidebar-menu-dropdown.show').forEach(el => el.classList.remove('show'));
        }
    });

    function toggleSidebarMenu(phone, event) {
        event.stopPropagation();
        
        // Close all other open sidebar menus
        document.querySelectorAll('.sidebar-menu-dropdown.show').forEach(el => {
            if (el.id !== 'sidebarMenu_' + phone) {
                el.classList.remove('show');
            }
        });

        const menu = document.getElementById('sidebarMenu_' + phone);
        if (menu) {
            menu.classList.toggle('show');
        }
    }

    // Theme Management
    function changeContactTheme(phone, theme) {
        if (theme) {
            localStorage.setItem('chatTheme_' + phone, theme);
            applyTheme(theme);
            
            const menu = document.getElementById('headerDropdownMenu');
            if (menu) menu.classList.remove('show');
            
            // Update active checkmarks directly without rebuilding the whole DOM
            document.querySelectorAll('.theme-option').forEach(el => {
                el.classList.remove('active');
                if (el.getAttribute('data-theme') === theme) {
                    el.classList.add('active');
                }
            });
        }
    }

    function applyTheme(theme) {
        if (!theme) theme = 'doodle';
        
        const chatWindow = document.querySelector('.chat-window');
        if (chatWindow) {
            chatWindow.classList.remove('theme-dark', 'theme-doodle', 'theme-light', 'theme-midnight', 'theme-sunset', 'theme-ocean', 'theme-grey');
            if (theme !== 'light') {
                chatWindow.classList.add(`theme-${theme}`);
            }
        }
    }

    // Check if the 24-hour window is active based on last incoming message time
    function check24hWindow(timeStr) {
        if (!timeStr) return false;
        const incomingDate = new Date(timeStr);
        const now = new Date();
        const diffMs = now - incomingDate;
        const diffHours = diffMs / (1000 * 60 * 60);
        return diffHours < 24;
    }

    // Select Contact Function
    function selectContact(phone) {
        if (typeof cancelPrintMode === 'function') {
            cancelPrintMode();
        }
        
        // Remove from pendingUnreadPhones so poll can resume normal behavior
        pendingUnreadPhones.delete(phone);

        if (!conversationsData[phone]) return;
        activePhone = phone;

        const conv = conversationsData[phone];

        // 1. Highlight Active Contact in Left List
        document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
        const activeItem = document.querySelector(`.contact-item[data-phone="${phone}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
            
            // Check unread count before resetting
            const unreadCountBefore = conversationsData[phone].unread_count;
            const replyUnreadBadge = document.getElementById('replyUnreadBadge');
            if (replyUnreadBadge) {
                if (unreadCountBefore > 0) {
                    replyUnreadBadge.innerText = unreadCountBefore;
                    replyUnreadBadge.style.display = 'flex';
                } else {
                    replyUnreadBadge.style.display = 'none';
                }
            }

            // Hide unread badge and notify server
            const badge = activeItem.querySelector('.unread-pill');
            if (badge && unreadCountBefore > 0) {
                badge.style.display = 'none';
                conversationsData[phone].unread_count = 0; // update local state
                
                // Remove bold styling
                const nameEl = activeItem.querySelector('.contact-name');
                const snippetEl = activeItem.querySelector('.contact-snippet');
                if (nameEl) nameEl.style.fontWeight = '600';
                if (snippetEl) {
                    snippetEl.style.fontWeight = 'normal';
                    snippetEl.style.color = 'var(--wa-text-secondary)';
                }
                
                // Notify server to mark as read
                fetch(`/live-chat/mark-read/${phone}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                }).catch(err => console.error('Error marking as read:', err));
            }
        }

        // 2. Highlight Active Top Avatar
        document.querySelectorAll('.top-avatar-item').forEach(el => el.classList.remove('active'));
        const activeTop = document.querySelector(`.top-avatar-item[data-top-phone="${phone}"]`);
        if (activeTop) activeTop.classList.add('active');

        // 3. Update Header & Profile Sidebar
        document.getElementById('activeHeaderName').innerText = conv.display_name;
        document.getElementById('activeHeaderAvatar').innerText = conv.initial;
        document.getElementById('activeHeaderAvatar').style.backgroundColor = conv.color;
        
        let headerStatusHtml = ``;
        if (conv.is_saved) {
            headerStatusHtml = `` + headerStatusHtml;
        }
        document.getElementById('activeHeaderStatus').innerHTML = headerStatusHtml;

        // Action Button logic (moved to Header 3-dot menu)
        const headerActionContainer = document.getElementById('headerActionContainer');

        let savedTheme = localStorage.getItem('chatTheme_' + conv.phone);
        if (!savedTheme || savedTheme === 'light') {
            savedTheme = 'doodle'; 
            // We force 'doodle' to be the default instead of 'light', unless they explicitly selected a plain white theme
            // Actually, to avoid confusion, let's just use 'doodle' if it's null, but if they had 'light' saved, let's upgrade it to 'doodle' 
            // since they probably just meant "default" and now default is doodle.
        }
        applyTheme(savedTheme);
        applyTheme(savedTheme);

        const themeSelectorHtml = `
        <div class="msg-dropdown-item color-theme" style="position: relative; overflow: visible;" onclick="event.stopPropagation(); this.querySelector('.theme-submenu').classList.toggle('show');">
            <i class="fa-solid fa-palette"></i> Chat Theme
            <i class="fa-solid fa-chevron-right" style="margin-left: auto; width: 12px !important; height: 12px !important; background: none !important; font-size: 10px !important;"></i>
            
            <div class="theme-submenu">
                <div class="theme-option ${savedTheme === 'doodle' ? 'active' : ''}" data-theme="doodle" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'doodle');">
                    <div class="theme-color-circle" style="background: #e2e8f0; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 4px 4px;"></div> WhatsApp Default
                </div>
                <div class="theme-option ${savedTheme === 'light' ? 'active' : ''}" data-theme="light" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'light');">
                    <div class="theme-color-circle" style="background: #f1f5f9;"></div> Plain White
                </div>
                <div class="theme-option ${savedTheme === 'dark' ? 'active' : ''}" data-theme="dark" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'dark');">
                    <div class="theme-color-circle" style="background: #1e293b;"></div> Dark Mode
                </div>
                <div class="theme-option ${savedTheme === 'midnight' ? 'active' : ''}" data-theme="midnight" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'midnight');">
                    <div class="theme-color-circle" style="background: #0f172a;"></div> Midnight Blue
                </div>
                <div class="theme-option ${savedTheme === 'sunset' ? 'active' : ''}" data-theme="sunset" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'sunset');">
                    <div class="theme-color-circle" style="background: linear-gradient(135deg, #f59e0b, #e11d48);"></div> Sunset Gradient
                </div>
                <div class="theme-option ${savedTheme === 'ocean' ? 'active' : ''}" data-theme="ocean" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'ocean');">
                    <div class="theme-color-circle" style="background: linear-gradient(135deg, #0ea5e9, #3b82f6);"></div> Ocean Breeze
                </div>
                <div class="theme-option ${savedTheme === 'grey' ? 'active' : ''}" data-theme="grey" onclick="event.stopPropagation(); changeContactTheme('${conv.phone}', 'grey');">
                    <div class="theme-color-circle" style="background: #94a3b8;"></div> Minimalist Grey
                </div>
            </div>
        </div>
        `;

        const copyBtnHtml = `<button class="msg-dropdown-item color-copy" onclick="copyContactNumber('${conv.phone}')">
            <i class="fa-regular fa-copy"></i> Copy Number
        </button>`;

        const blockBtnHtml = userPermissions.canManage ? `<button id="btnToggleBlock" class="msg-dropdown-item color-block" onclick="toggleBlockContact('${conv.phone}', event)">
            <i id="iconToggleBlock" class="fa-solid ${conv.is_blocked ? 'fa-unlock' : 'fa-ban'}"></i> <span id="textToggleBlock">${conv.is_blocked ? 'Unblock Contact' : 'Block Contact'}</span>
        </button>` : '';

        const exportBtnHtml = userPermissions.canExport ? `<a href="/live-chat/export/${conv.phone}" class="msg-dropdown-item color-export" target="_blank">
            <i class="fa-solid fa-download"></i> Export Chat
        </a>` : '';

        let saveEditBtnHtml = '';
        if (userPermissions.canSaveContact) {
            if (conv.is_saved) {
                saveEditBtnHtml = `<button class="msg-dropdown-item color-save" onclick="openSaveContactModal()">
                    <i class="fa-solid fa-pen"></i> Edit Contact
                </button>`;
            } else {
                saveEditBtnHtml = `<button class="msg-dropdown-item color-save" onclick="openSaveContactModal()">
                    <i class="fa-solid fa-user-plus"></i> Save Contact
                </button>`;
            }
        }

        let dropdownHeaderHtml = '';
        if (conv.is_saved) {
            dropdownHeaderHtml = `
            <div style="padding: 6px; color: var(--wa-text-primary); text-align: center; background-color: #f8fafc; border-radius: 6px; margin-bottom: 4px;">
                <div style="font-size: 12px; font-weight: 700; letter-spacing: 0.3px;">${conv.display_name}</div>
                <div style="font-size: 9.5px; color: #64748b; font-weight: 600; margin-top: 2px;">+${conv.phone}</div>
            </div>`;
        } else {
            dropdownHeaderHtml = `
            <div style="padding: 6px; color: var(--wa-text-primary); font-size: 11.5px; font-weight: 700; text-align: center; background-color: #f8fafc; border-radius: 6px; margin-bottom: 4px; letter-spacing: 0.3px;">
                +${conv.phone}
            </div>`;
        }

        headerActionContainer.innerHTML = `
            <button class="msg-dropdown-btn" onclick="toggleHeaderMenu(event)" style="width: 28px; height: 28px; font-size: 14px; border: none; background: transparent; cursor: pointer;">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div class="msg-dropdown-menu" id="headerDropdownMenu" style="right: 0; min-width: 170px; top: 110%; padding: 4px;">
                ${dropdownHeaderHtml}
                ${themeSelectorHtml}
                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 4px 0;">
                ${userPermissions.canExport ? `
                <button class="msg-dropdown-item color-print" onclick="enablePrintMode()">
                    <i class="fa-solid fa-print"></i> Print Messages
                </button>` : ''}
                ${userPermissions.canManage ? `
                <button class="msg-dropdown-item" onclick="markUnreadContact('${conv.phone}', event)">
                    <i class="fa-solid fa-envelope"></i> Mark Unread
                </button>` : ''}
                ${saveEditBtnHtml}
                ${copyBtnHtml}
                ${exportBtnHtml}
                ${blockBtnHtml}
            </div>
        `;

        // 4. Update 24-hour Window UI & Block State
        updateReplyBarState(conv);

        // 5. Render Conversation Messages
        renderMessages(conv.messages);
    }

    function updateReplyBarState(conv) {
        const isWindowActive = check24hWindow(conv.last_incoming_at);
        const windowStatusContainer = document.getElementById('windowStatusContainer');
        const replyInput = document.getElementById('replyInput');
        const btnSendReply = document.getElementById('btnSendReply');
        const quickTemplates = document.querySelectorAll('.quick-template-btn');

        if (!userPermissions.canReply) {
            windowStatusContainer.innerHTML = '<span class="status-badge closed"><i class="fa-solid fa-lock"></i> No Reply Permission</span>';
            replyInput.disabled = true;
            btnSendReply.disabled = true;
            replyInput.placeholder = "You do not have permission to reply.";
            quickTemplates.forEach(btn => btn.style.opacity = '0.5');
            quickTemplates.forEach(btn => btn.disabled = true);
        } else if (conv.is_blocked) {
            windowStatusContainer.innerHTML = '<span class="status-badge blocked"><i class="fa-solid fa-ban"></i> Blocked Contact</span>';
            replyInput.disabled = true;
            btnSendReply.disabled = true;
            replyInput.placeholder = "Unblock this contact to send a message.";
            quickTemplates.forEach(btn => btn.style.opacity = '0.5');
            quickTemplates.forEach(btn => btn.disabled = true);
        } else if (isWindowActive) {
            windowStatusContainer.innerHTML = '<span class="status-badge active"><span class="dot"></span> 24h Window Active</span>';
            replyInput.disabled = false;
            btnSendReply.disabled = false;
            replyInput.placeholder = "Type a message to reply on WhatsApp...";
            quickTemplates.forEach(btn => btn.style.opacity = '1');
            quickTemplates.forEach(btn => btn.disabled = false);
        } else {
            windowStatusContainer.innerHTML = '<span class="status-badge closed"><i class="fa-regular fa-clock" style="font-size: 10px;"></i> 24h Window Closed</span>';
            replyInput.disabled = true;
            btnSendReply.disabled = true;
            replyInput.placeholder = "24h window closed. Please use Send WA to send a template.";
            quickTemplates.forEach(btn => btn.style.opacity = '0.5');
            quickTemplates.forEach(btn => btn.disabled = true);
        }
    }

    // Format message text (Escape HTML, Linkify URLs, Convert Newlines, Render Media)
    function formatMessageText(text) {
        if (!text) return '';
        
        // 1. First escape HTML to prevent XSS
        let formatted = (text || '').replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
        
        // 2. Extract [Media Attachment: URL] and replace with placeholders to prevent linkify conflict
        const mediaAttachments = [];
        const mediaRegex = /\[Media Attachment:\s*([^\s\]]+)\]/g;
        formatted = formatted.replace(mediaRegex, function(match, url) {
            mediaAttachments.push(url);
            return `__MEDIA_ATTACHMENT_${mediaAttachments.length - 1}__`;
        });

        // 3. Convert normal URLs to clickable links
        const urlRegex = /(https?:\/\/[^\s<]+)/g;
        formatted = formatted.replace(urlRegex, function(url) {
            return `<a href="${url}" target="_blank" style="color: #3b82f6; text-decoration: underline; font-weight: 500;">${url}</a>`;
        });

        // 4. Restore media placeholders as actual HTML elements (images, videos, documents)
        mediaAttachments.forEach((url, index) => {
            let mediaHtml = '';
            if (url.match(/\.(jpeg|jpg|gif|png|webp)/i)) {
                mediaHtml = `
                <div class="message-media" style="margin-top: 2px; margin-bottom: 4px;">
                    <a href="${url}" target="_blank" style="display: block;">
                        <img src="${url}" style="width: 100%; max-width: 200px; height: auto; max-height: 200px; object-fit: cover; border-radius: 6px; border: 1px solid rgba(0,0,0,0.05); display: block;">
                    </a>
                </div>`;
            } else if (url.match(/\.(mp4|mov|webm)/i)) {
                mediaHtml = `
                <div class="message-media" style="margin-top: 2px; margin-bottom: 4px;">
                    <video src="${url}" controls style="width: 100%; max-width: 200px; height: auto; max-height: 200px; object-fit: cover; border-radius: 6px; border: 1px solid rgba(0,0,0,0.05); display: block;"></video>
                </div>`;
            } else {
                let filename = url.split('/').pop() || 'Document';
                filename = filename.replace(/^\d+_(?:[a-zA-Z0-9]+_)?/, ''); // remove timestamp and index/uniqid
                if (filename.length > 25) filename = filename.substring(0, 25) + '...';

                let ext = filename.split('.').pop().toLowerCase();
                let iconClass = 'fa-file-lines';
                if (['pdf'].includes(ext)) iconClass = 'fa-file-pdf';
                else if (['xls', 'xlsx', 'csv'].includes(ext)) iconClass = 'fa-file-excel';
                else if (['doc', 'docx'].includes(ext)) iconClass = 'fa-file-word';
                else if (['zip', 'rar'].includes(ext)) iconClass = 'fa-file-zipper';
                else if (['ppt', 'pptx'].includes(ext)) iconClass = 'fa-file-powerpoint';
                else if (['cdr', 'pmd', 'psd'].includes(ext)) iconClass = 'fa-pen-nib';

                mediaHtml = `
                <div class="message-media" style="margin-top: 8px; margin-bottom: 4px;">
                    <a href="${url}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(0,0,0,0.04); padding: 10px 14px; border-radius: 8px; text-decoration: none; color: #3b82f6; font-weight: 600; font-size: 13px; border: 1px solid rgba(0,0,0,0.05);">
                        <i class="fa-solid ${iconClass}" style="font-size: 16px;"></i> ${filename}
                    </a>
                </div>`;
            }
            formatted = formatted.replace(`__MEDIA_ATTACHMENT_${index}__`, mediaHtml);
        });

        // 5. Parse Template Header and Footer
        formatted = formatted.replace(/\[HEADER\]([\s\S]*?)\[\/HEADER\]/g, '<div class="template-header">$1</div>');
        formatted = formatted.replace(/\[FOOTER\]([\s\S]*?)\[\/FOOTER\]/g, '<div class="template-footer">$1</div>');

        // 6. Convert newlines to <br>
        formatted = formatted.replace(/\n/g, '<br>');

        return formatted;
    }

    // Helper for Message Status Ticks
    function getTickIcon(status) {
        if (status === 'read') {
            return `<i class="fa-solid fa-check-double" style="color: #53bdeb;"></i>`;
        } else if (status === 'delivered') {
            return `<i class="fa-solid fa-check-double" style="color: #94a3b8;"></i>`;
        } else if (status === 'sent' || status === 'success') {
            return `<i class="fa-solid fa-check" style="color: #94a3b8;"></i>`;
        } else if (status === 'failed') {
            return `<i class="fa-solid fa-circle-exclamation" style="color: #ef4444;" title="Failed to send"></i>`;
        }
        return `<i class="fa-regular fa-clock" style="color: #94a3b8;"></i>`;
    }

    // Render Messages Stream
    let lastMessagesState = '';
    function renderMessages(messages) {
        const stateToHash = activePhone + '|' + JSON.stringify(messages);
        if (lastMessagesState === stateToHash) return;
        lastMessagesState = stateToHash;

        const stream = document.getElementById('chatMessagesStream');
        stream.innerHTML = '';

        if (!messages || messages.length === 0) {
            stream.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #94a3b8; text-align: center;">
                <i class="fa-brands fa-whatsapp" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e1;"></i>
                <div style="font-size: 16px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Start a new conversation</div>
                <div style="font-size: 13px; max-width: 250px; line-height: 1.5;">Send a message below to start chatting with this contact.</div>
            </div>`;
            return;
        }

        const emojiRegex = /^[\p{Emoji_Presentation}\p{Extended_Pictographic}\uFE0F]+$/u;

        let lastDateKey = null;

        messages.forEach(msg => {

             // ── Date separator ──
            const msgDateKey = getDateKey(msg.created_at || msg.time);
            if (msgDateKey && msgDateKey !== lastDateKey) {
                const sep = document.createElement('div');
                sep.className = 'date-separator';
                sep.innerHTML = `<span>${formatDateLabel(msg.created_at || msg.time)}</span>`;
                stream.appendChild(sep);
                lastDateKey = msgDateKey;
            }

            const bubble = document.createElement('div');
            bubble.className = `message-bubble message-${msg.sender}`;
            
            // Check for single emoji
            const stripped = (msg.text || '').trim();
            const charCount = Array.from(stripped).length;
            if (charCount > 0 && charCount <= 3 && emojiRegex.test(stripped)) {
                bubble.classList.add('single-emoji');
            }

            // Apply formatting
            const formattedText = formatMessageText(msg.text);
            
            // Safe escape for text variable in JS string injection
            const safeText = (msg.text || '').replace(/\\/g, '\\\\').replace(/`/g, '\\`').replace(/\$/g, '\\$');

            const mediaUrlMatch = (msg.text || '').match(/\[Media Attachment:\s*([^\s\]]+)\]/i);
            const mediaUrl = mediaUrlMatch ? mediaUrlMatch[1] : null;

            bubble.innerHTML = `
                <div class="print-checkbox-container" style="display: none; float: left; margin-right: 12px; margin-top: 2px;">
                    <input type="checkbox" class="print-msg-checkbox" value="${msg.id}" onclick="updatePrintSelectionCount()" style="width: 18px; height: 18px; cursor: pointer;">
                </div>
                <div class="message-dropdown">
                    <button class="msg-dropdown-btn" onclick="toggleMsgDropdown(event, ${msg.id})">
                        <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                    </button>
                    <div class="msg-dropdown-menu" id="msgMenu_${msg.id}">
                        <button class="msg-dropdown-item" onclick="copyMessageText(event, \`${safeText}\`)">
                            <i class="fa-regular fa-copy"></i> Copy
                        </button>
                        ${(msg.sender === 'outgoing' && userPermissions.canManage) ? `
                        <button class="msg-dropdown-item" onclick="editMessage(event, ${msg.id}, \`${safeText}\`)">
                            <i class="fa-solid fa-pen"></i> Edit message
                        </button>
                        ` : ''}
                        ${mediaUrl ? `
                        <a href="${mediaUrl}" download target="_blank" class="msg-dropdown-item" style="text-decoration: none;">
                            <i class="fa-solid fa-download"></i> Download
                        </a>
                        ` : ''}
                        ${userPermissions.canReply ? `
                        <button class="msg-dropdown-item color-forward" onclick="openForwardModal(${msg.id})">
                            <i class="fa-solid fa-share"></i> Forward
                        </button>
                        ` : ''}
                        ${userPermissions.canDelete ? `
                        <button class="msg-dropdown-item danger" onclick="deleteIndividualMessage(${msg.id}, 'me')">
                            <i class="fa-regular fa-trash-can"></i> Delete
                        </button>
                        ` : ''}
                    </div>
                </div>
                <div class="bubble-content-wrap" style="overflow: hidden;">
                    ${formattedText}
                    <div class="message-meta">
                        <span>${msg.time}</span>
                        ${msg.sender === 'outgoing' ? getTickIcon(msg.status) : ''}
                    </div>
                </div>
            `;
            stream.appendChild(bubble);
        });

        stream.scrollTop = stream.scrollHeight;
        
        // Parse all text in the chat window to render modern emojis
        if (typeof twemoji !== 'undefined') {
            twemoji.parse(stream, { folder: 'svg', ext: '.svg' });
        }
    }

    // ── WhatsApp style date separator helpers ──
function getDateKey(dateStr) {
    if (!dateStr) return null;
    // Agar sirf time hai (jaise "10:45 AM"), aaj ki date use karo
    let d = new Date(dateStr);
    if (isNaN(d)) {
        // Try today + time
        const today = new Date().toISOString().split('T')[0];
        d = new Date(today + ' ' + dateStr);
    }
    if (isNaN(d)) return null;
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}
function formatDateLabel(dateStr) {
    const d = new Date(dateStr);
    if (isNaN(d)) return '';

    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);

    const sameDay = (a, b) =>
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate();

    if (sameDay(d, today))     return 'Today';
    if (sameDay(d, yesterday)) return 'Yesterday';

    // Same year → "19 September"
    const opts = { day: 'numeric', month: 'long' };
    if (d.getFullYear() === today.getFullYear()) {
        return d.toLocaleDateString('en-US', opts);
    }
    // Different year → "19 September 2025"
    return d.toLocaleDateString('en-US', { ...opts, year: 'numeric' });
}


    let selectedMediaFiles = [];
    let queuedContact = null;
    let queuedStickerUrl = null;

    function handleMediaSelect(input) {
        if (input.files && input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                selectedMediaFiles.push(input.files[i]);
            }
            renderMediaPreviews();
            // Close menu if open
            const menu = document.getElementById('attachmentMenu');
            if (menu) menu.style.display = 'none';
        }
        // Reset input so the same files can be selected again if removed
        input.value = '';
    }
    
    function renderMediaPreviews() {
        const container = document.getElementById('mediaPreviewContainer');
        container.innerHTML = '';
        if (selectedMediaFiles.length === 0 && !queuedContact && !queuedStickerUrl) {
            container.style.display = 'none';
            return;
        }
        container.style.display = 'flex';
        
        selectedMediaFiles.forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.style.cssText = 'background: #e2e8f0; padding: 4px 8px; border-radius: 12px; display: flex; align-items: center; gap: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;';
            previewItem.innerHTML = `
                <span title="${file.name}" style="overflow: hidden; text-overflow: ellipsis;">${file.name}</span>
                <i class="fa-solid fa-times" style="cursor: pointer; color: #ef4444;" onclick="removeMediaFile(${index})"></i>
            `;
            container.appendChild(previewItem);
        });

        if (queuedContact) {
            const previewItem = document.createElement('div');
            previewItem.style.cssText = 'background: #dbeafe; padding: 4px 8px; border-radius: 12px; display: flex; align-items: center; gap: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; color: #1e40af; border: 1px solid #bfdbfe;';
            previewItem.innerHTML = `
                <i class="fa-solid fa-address-card"></i>
                <span title="${queuedContact.name}" style="overflow: hidden; text-overflow: ellipsis;">${queuedContact.name}</span>
                <i class="fa-solid fa-times" style="cursor: pointer; color: #ef4444;" onclick="removeQueuedContact()"></i>
            `;
            container.appendChild(previewItem);
        }

        if (queuedStickerUrl) {
            const previewItem = document.createElement('div');
            previewItem.style.cssText = 'background: #f1f5f9; padding: 4px 8px; border-radius: 12px; display: flex; align-items: center; gap: 6px; border: 1px solid #e2e8f0;';
            previewItem.innerHTML = `
                <img src="${queuedStickerUrl}" style="height: 24px; width: auto;" alt="Sticker">
                <i class="fa-solid fa-times" style="cursor: pointer; color: #ef4444;" onclick="removeQueuedSticker()"></i>
            `;
            container.appendChild(previewItem);
        }
    }

    function removeMediaFile(index) {
        selectedMediaFiles.splice(index, 1);
        renderMediaPreviews();
    }
    
    function removeQueuedContact() {
        queuedContact = null;
        renderMediaPreviews();
    }
    
    function removeQueuedSticker() {
        queuedStickerUrl = null;
        renderMediaPreviews();
    }

    function clearMediaSelect() {
        selectedMediaFiles = [];
        queuedContact = null;
        queuedStickerUrl = null;
        renderMediaPreviews();
    }

    function showErrorPopup(msg) {
        if (typeof showToast === 'function') {
            showToast('Error', msg, 'danger');
        } else {
            // Fallback to a custom injected div if showToast is not available
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#ef4444;color:#fff;padding:12px 20px;border-radius:8px;z-index:9999;box-shadow:0 4px 6px rgba(0,0,0,0.1);font-family:sans-serif;font-size:14px;';
            toast.innerText = msg;
            document.body.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 4000);
        }
    }

    let isSendingMessage = false;

    function sendReplyMessage() {
        if (isSendingMessage) return;
        
        const input = document.getElementById('replyInput');
        const text = input.value.trim();
        const btnSend = document.getElementById('btnSendReply');
        const replyBadge = document.getElementById('replyUnreadBadge');
        if (replyBadge) replyBadge.style.display = 'none';

        let hasContent = false;
        if (text) hasContent = true;
        if (selectedMediaFiles.length > 0) hasContent = true;
        if (queuedContact) hasContent = true;
        if (queuedStickerUrl) hasContent = true;

        if (!hasContent) {
            showErrorPopup('Please enter a message text or attach a file.');
            return;
        }

        if (window.editingMessageId && text) {
            isSendingMessage = true;
            btnSend.disabled = true;
            btnSend.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/live-chat/message/${window.editingMessageId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ text: text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (activePhone && conversationsData[activePhone]) {
                        const msg = conversationsData[activePhone].messages.find(m => m.id === window.editingMessageId);
                        if (msg) msg.text = text;
                        renderMessages(conversationsData[activePhone].messages);
                    }
                    if (typeof showToast === 'function') showToast('Edited', 'Message edited successfully', 'success');
                } else {
                    showErrorPopup('Failed to edit: ' + data.message);
                }
            })
            .catch(err => showErrorPopup('Error connecting to server'))
            .finally(() => {
                input.value = '';
                btnSend.disabled = false;
                btnSend.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
                btnSend.style.backgroundColor = '#00a884';
                btnSend.style.borderColor = '#00a884';
                window.editingMessageId = null;
                isSendingMessage = false;
                input.focus();
            });
            
            return;
        }

        if (!activePhone) {
            const firstItem = document.querySelector('.contact-item');
            if (firstItem) {
                activePhone = firstItem.getAttribute('data-phone');
            }
        }

        if (!activePhone) {
            showErrorPopup('Please select a contact from the list first.');
            return;
        }

        isSendingMessage = true;
        btnSend.disabled = true;
        btnSend.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const headers = {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        };

        const promises = [];

        // 1. Send Text + Media
        if (text || selectedMediaFiles.length > 0) {
            const formData = new FormData();
            formData.append('phone_number', activePhone);
            if (text) formData.append('message_text', text);
            if (selectedMediaFiles.length > 0) {
                selectedMediaFiles.forEach(file => formData.append('media_files[]', file));
            }
            promises.push(
                fetch('{{ route("live.chat.send") }}', { method: 'POST', headers: headers, body: formData })
                .then(res => res.json())
                .catch(() => null)
            );
        }

        // 2. Send Contact
        if (queuedContact) {
            const formData = new FormData();
            formData.append('phone_number', activePhone);
            formData.append('type', 'contact');
            formData.append('contact_name', queuedContact.name || 'Unknown');
            formData.append('contact_phone', queuedContact.phone);
            promises.push(
                fetch('{{ route("live.chat.send") }}', { method: 'POST', headers: headers, body: formData })
                .then(res => res.json())
                .catch(() => null)
            );
        }

        // 3. Send Sticker
        if (queuedStickerUrl) {
            const formData = new FormData();
            formData.append('phone_number', activePhone);
            formData.append('type', 'sticker');
            formData.append('sticker_url', queuedStickerUrl);
            promises.push(
                fetch('{{ route("live.chat.send") }}', { method: 'POST', headers: headers, body: formData })
                .then(res => res.json())
                .catch(() => null)
            );
        }

        Promise.all(promises).then(results => {
            btnSend.disabled = false;
            btnSend.innerHTML = `<i class="fa-solid fa-paper-plane"></i>`;
            
            let anySuccess = false;
            let combinedData = [];
            let errorMsg = '';

            results.forEach(data => {
                if (data && data.success && data.data) {
                    anySuccess = true;
                    if (Array.isArray(data.data)) {
                        data.data.forEach(msg => combinedData.push(msg));
                    } else {
                        combinedData.push(data.data);
                    }
                } else if (data && data.message) {
                    errorMsg = data.message;
                }
            });

            if (anySuccess) {
                if (!conversationsData[activePhone]) {
                    conversationsData[activePhone] = { messages: [] };
                }
                if (!conversationsData[activePhone].messages) {
                    conversationsData[activePhone].messages = [];
                }
                combinedData.forEach(msg => conversationsData[activePhone].messages.push(msg));
                
                const snippetEl = document.querySelector(`.contact-item[data-phone="${activePhone}"] .contact-snippet`);
                if (snippetEl) snippetEl.innerText = text || (queuedContact ? 'Contact attached' : (queuedStickerUrl ? 'Sticker attached' : 'Media attached'));

                input.value = '';
                clearMediaSelect();
                renderMessages(conversationsData[activePhone].messages);
                
                const stream = document.getElementById('chatMessagesStream');
                if (stream) stream.scrollTop = stream.scrollHeight;
            } else {
                showErrorPopup('Error sending message: ' + (errorMsg || 'Failed to communicate with WhatsApp server.'));
            }
            
            isSendingMessage = false;
        });
    }


    // Active Chat Filter State
    let currentFilterType = 'live';

    function filterChats(type, btn) {
        currentFilterType = type;
        
        // Update active tab styling
        if (btn) {
            document.querySelectorAll('.chat-filter-tab').forEach(el => {
                el.classList.remove('active');
            });
            btn.classList.add('active');
        }

        renderSidebarContacts(); // Re-render with new filter
    }

    // Search Contacts Filter
    function searchContacts() {
        const query = document.getElementById('contactSearchInput').value.toLowerCase();
        document.querySelectorAll('.contact-item').forEach(item => {
            const name = item.querySelector('.contact-name').innerText.toLowerCase();
            const snippet = item.querySelector('.contact-snippet').innerText.toLowerCase();
            const phone = item.getAttribute('data-phone').toLowerCase();

            const matchesQuery = (name.includes(query) || snippet.includes(query) || phone.includes(query));

            if (matchesQuery) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Auto Polling Data from Database every 4s
    function pollLiveData() {
        fetch('{{ route("live.chat.fetch") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.conversations) {
                let activeConvData = null;
                if (activePhone && data.conversations[activePhone]) {
                    activeConvData = data.conversations[activePhone];
                }

                // We must always update other conversations
                if (activePhone && activeConvData && conversationsData[activePhone]) {
                    const currentLen = conversationsData[activePhone].messages.length;
                    const newLen = activeConvData.messages.length;
                    
                    // If message count changed, we take the server's version and re-render
                    if (newLen !== currentLen) {
                        data.conversations[activePhone] = activeConvData;
                        conversationsData = data.conversations;
                        restorePendingUnread(); // ← restore ALL pending phones after overwrite
                        renderMessages(conversationsData[activePhone].messages);
                        
                        // Auto-read ONLY if NOT manually marked unread
                        if (activeConvData.unread_count > 0 && !pendingUnreadPhones.has(activePhone)) {
                            activeConvData.unread_count = 0;
                            conversationsData[activePhone].unread_count = 0;
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            fetch(`/live-chat/mark-read/${activePhone}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            });
                        } else if (pendingUnreadPhones.has(activePhone)) {
                            // Keep the unread state — do not mark read
                            conversationsData[activePhone].unread_count = Math.max(activeConvData.unread_count, 1);
                        }
                    } else {
                        // Count is same. Keep local active messages to avoid overwriting edits mid-typing
                        for(let i = 0; i < currentLen; i++) {
                            if(activeConvData.messages[i].text !== conversationsData[activePhone].messages[i].text) {
                                conversationsData[activePhone].messages[i].text = activeConvData.messages[i].text;
                            }
                        }
                        data.conversations[activePhone].messages = conversationsData[activePhone].messages;
                        conversationsData = data.conversations;
                        restorePendingUnread(); // ← restore ALL pending phones after overwrite
                    }
                } else {
                    conversationsData = data.conversations;
                    restorePendingUnread(); // ← restore ALL pending phones after overwrite
                }

                if (data.orderedPhones) {
                    orderedPhones = data.orderedPhones;
                }

                // Re-render the contacts sidebar dynamically
                renderSidebarContacts();

                // Refresh current active conversation stream smoothly
                if (activePhone && conversationsData[activePhone]) {
                    renderMessages(conversationsData[activePhone].messages);
                }
            }
        })
        .catch(err => {
            // Background poll error handled silently
        });
    }

    let lastSidebarHtml = '';
    function renderSidebarContacts() {
        const list = document.getElementById('contactsList');
        if (!list) return;

        let html = '';
        const searchInput = document.getElementById('contactSearchInput');
        const query = searchInput ? searchInput.value.toLowerCase() : '';

        for (const phone of orderedPhones) {
            const conv = conversationsData[phone];
            const isActive = (phone === activePhone) ? 'active' : '';
            
            const nameLower = conv.display_name.toLowerCase();
            const snippetLower = conv.last_message.toLowerCase();
            const phoneLower = String(phone).toLowerCase();
            
            const matchesQuery = (nameLower.includes(query) || snippetLower.includes(query) || phoneLower.includes(query));
            const displayStyle = matchesQuery ? 'flex' : 'none';
            
            let isUnread = (conv.unread_count > 0);
            // Cap badge at 5, show 5+ if more
            let badgeHtml = '';
            if (isUnread) {
                const displayCount = conv.unread_count > 5 ? '5+' : conv.unread_count;
                badgeHtml = `<span class="unread-pill" style="background:#25D366;color:#fff;font-weight:700;animation: unreadPulse 1.5s ease-in-out infinite;">${displayCount}</span>`;
            }

            // Apply category/type filter
            let passesFilter = true;
            if (currentFilterType === 'unread' && !isUnread) passesFilter = false;
            if (currentFilterType === 'live' && !conv.last_incoming_at) passesFilter = false;
            if (currentFilterType === 'sent' && conv.last_incoming_at) passesFilter = false;

            if (!passesFilter) continue;

            const nameWeight   = isUnread ? '800' : '600';
            const nameColor    = isUnread ? '#111b21' : 'var(--wa-text-primary)';
            const snippetWeight= isUnread ? '600' : 'normal';
            const snippetColor = isUnread ? '#111b21' : 'var(--wa-text-secondary)';
            const timeColor    = isUnread ? '#25D366' : 'var(--wa-text-muted)';
            const borderLeft   = isUnread ? 'border-left: 3px solid #25D366;' : '';
            
            html += `
                <div class="contact-item ${isActive}" data-phone="${phone}" data-category="${conv.category}" data-unread="${conv.unread_count}" data-live="${conv.last_incoming_at ? '1' : '0'}" style="display: ${displayStyle}; position: relative; ${borderLeft}" onclick="selectContact('${phone}')">
                    <div class="contact-avatar-circle" style="background-color: ${conv.color};">
                        ${conv.initial}
                    </div>
                    <div class="contact-details">
                        <div class="contact-name-row">
                            <span class="contact-name" style="font-weight: ${nameWeight}; color: ${nameColor};">${conv.display_name}</span>
                            <span class="contact-time" style="color: ${timeColor}; font-weight: ${isUnread ? '600' : '400'};">${conv.last_time}</span>
                        </div>
                        <div class="contact-snippet-row">
                            <span class="contact-snippet" style="font-weight: ${snippetWeight}; color: ${snippetColor};">${conv.last_message}</span>
                            ${badgeHtml}
                        </div>
                    </div>
                    
                    <!-- Sidebar Context Menu -->
                    <button class="sidebar-menu-btn" onclick="toggleSidebarMenu('${phone}', event)">
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="sidebar-menu-dropdown" id="sidebarMenu_${phone}">
                        ${isUnread 
                            ? `<div class="sidebar-dropdown-item" onclick="markReadContact('${phone}', event)">
                                 <i class="fa-solid fa-envelope-open" style="color: #64748b; margin-right: 8px;"></i> Mark as Read
                               </div>`
                            : `<div class="sidebar-dropdown-item" onclick="markUnreadContact('${phone}', event)">
                                 <i class="fa-solid fa-envelope" style="color: #64748b; margin-right: 8px;"></i> Mark as Unread
                               </div>`
                        }
                    </div>
                </div>
            `;
        }
        
        if (orderedPhones.length === 0) {
            html = `<div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13px;">
                        <i class="fa-solid fa-comments" style="font-size: 32px; margin-bottom: 8px; color: #cbd5e1;"></i>
                        <p>No campaign messages found in database.</p>
                        <a href="{{ route('send.wa.form') }}" style="color: #25D366; font-weight: 700; text-decoration: underline; margin-top: 6px; display: inline-block;">Send a Campaign Now</a>
                    </div>`;
        }
        
        // Only skip re-render if html is identical AND no unread states changed
        if (lastSidebarHtml === html) return;
        lastSidebarHtml = html;

        list.innerHTML = html;

        if (typeof twemoji !== 'undefined') {
            twemoji.parse(list, { folder: 'svg', ext: '.svg' });
        }
    }

    function handleKeyPress(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent double triggering if it's part of a form or bubbling
            if (!isSendingMessage) {
                sendReplyMessage();
            }
        }
    }

    function insertTemplate(templateText) {
        const input = document.getElementById('replyInput');
        input.value = templateText;
        input.focus();
    }

    // Modal Functions
    function openSaveContactModal() {
        if (!activePhone || !conversationsData[activePhone]) return;
        
        const conv = conversationsData[activePhone];
        
        document.getElementById('scPhone').value = '+' + conv.phone;
        document.getElementById('scName').value = conv.is_saved ? conv.display_name : '';
        document.getElementById('scEmail').value = (conv.contact_model && conv.contact_model.email) ? conv.contact_model.email : '';
        document.getElementById('scNotes').value = (conv.contact_model && conv.contact_model.notes) ? conv.contact_model.notes : '';
        
        document.getElementById('saveContactModalTitle').innerText = conv.is_saved ? 'Edit Contact' : 'Save Contact';
        document.getElementById('saveContactModal').classList.add('active');
    }

    function closeSaveContactModal() {
        document.getElementById('saveContactModal').classList.remove('active');
    }

    function submitSaveContact(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitContact');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const phone = document.getElementById('scPhone').value;
        const name = document.getElementById('scName').value;
        const email = document.getElementById('scEmail').value;
        const notes = document.getElementById('scNotes').value;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("contacts.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                name: name,
                email: email,
                notes: notes
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Save Contact';
            
            if (data.success) {
                closeSaveContactModal();
                
                // Update local data
                if (conversationsData[activePhone]) {
                    conversationsData[activePhone].display_name = data.data.name;
                    conversationsData[activePhone].is_saved = true;
                    conversationsData[activePhone].contact_model = data.data;
                    
                    // Update UI initial
                    let initial = data.data.name.substring(0,1).toUpperCase();
                    if (!initial.match(/[A-Z0-9]/)) initial = 'C';
                    conversationsData[activePhone].initial = initial;
                    
                    // Re-render sidebar contact list item specifically
                    const sidebarName = document.querySelector(`.contact-item[data-phone="${activePhone}"] .contact-name`);
                    const sidebarInitial = document.querySelector(`.contact-item[data-phone="${activePhone}"] .contact-avatar-circle`);
                    if (sidebarName) sidebarName.innerText = data.data.name;
                    if (sidebarInitial) sidebarInitial.innerText = initial;
                    
                    // Re-select contact to update right pane and header completely
                    selectContact(activePhone);
                    
                    // Show a toast if function exists, else alert
                    if (typeof showToast === 'function') {
                        showToast('Success', 'Contact saved successfully!', 'success');
                    }
                }
            } else {
                alert('Failed to save contact: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Save Contact';
            alert('Error connecting to server.');
        });
    }

    function deleteChat(phone) {
        if (!confirm('Are you sure you want to delete this conversation? This will permanently delete all messages for this contact.')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/live-chat/${phone}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Deleted', 'Chat conversation deleted successfully', 'success');
                }
                
                // Remove from UI immediately
                delete conversationsData[phone];
                const item = document.querySelector(`.contact-item[data-phone="${phone}"]`);
                if (item) item.remove();
                
                const topItem = document.querySelector(`.top-avatar-item[data-top-phone="${phone}"]`);
                if (topItem) topItem.remove();

                if (activePhone === phone) {
                    activePhone = null;
                    document.getElementById('chatMessagesStream').innerHTML = '';
                    document.getElementById('activeHeaderName').innerText = 'Select a Contact';
                    document.getElementById('profilePaneName').innerText = 'Contact Details';
                    document.getElementById('profilePanePhone').innerText = '';
                    document.getElementById('profileContactAction').innerHTML = '';
                }
            } else {
                alert('Failed to delete chat: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error connecting to server to delete chat.');
        });
    }

    // --- Emoji Picker Logic ---
    const emojiBtn = document.getElementById('emojiBtn');
    const emojiPickerContainer = document.getElementById('emojiPickerContainer');
    const replyInputEl = document.getElementById('replyInput');

    if (emojiBtn && emojiPickerContainer) {
        // Initialize EmojiMart Picker
        const pickerOptions = {
            set: 'apple', // Looks exactly like WhatsApp
            theme: 'light',
            onEmojiSelect: event => {
                const cursorPosition = replyInputEl.selectionStart;
                const textBefore = replyInputEl.value.substring(0, cursorPosition);
                const textAfter = replyInputEl.value.substring(cursorPosition, replyInputEl.value.length);
                replyInputEl.value = textBefore + event.native + textAfter;
                
                // Move cursor right after the inserted emoji
                const newPos = cursorPosition + event.native.length;
                replyInputEl.setSelectionRange(newPos, newPos);
                replyInputEl.focus();
            }
        };
        const picker = new EmojiMart.Picker(pickerOptions);
        emojiPickerContainer.appendChild(picker);

        emojiBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // prevent document click from firing immediately
            emojiPickerContainer.style.display = emojiPickerContainer.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!emojiPickerContainer.contains(e.target) && e.target !== emojiBtn && !emojiBtn.contains(e.target)) {
                emojiPickerContainer.style.display = 'none';
            }
        });
    }

    // --- Attachment Picker Logic ---
    const attachmentBtn = document.getElementById('attachmentBtn');
    const attachmentMenu = document.getElementById('attachmentMenu');
    const attachmentMenuContainer = document.getElementById('attachmentMenuContainer');

    if (attachmentBtn && attachmentMenu) {
        attachmentBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            attachmentMenu.style.display = attachmentMenu.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!attachmentMenuContainer.contains(e.target)) {
                attachmentMenu.style.display = 'none';
            }
        });
        
        // Add hover styles dynamically
        document.querySelectorAll('.attachment-item').forEach(item => {
            item.addEventListener('mouseenter', () => item.style.backgroundColor = '#f1f5f9');
            item.addEventListener('mouseleave', () => item.style.backgroundColor = 'transparent');
        });
    }

    // --- Share Contact Logic ---
    function openShareContactModal() {
        document.getElementById('shareContactModal').style.display = 'flex';
        const attachmentMenu = document.getElementById('attachmentMenu');
        if (attachmentMenu) attachmentMenu.style.display = 'none';
    }

    function closeShareContactModal() {
        document.getElementById('shareContactModal').style.display = 'none';
        document.getElementById('shareContactSearch').value = '';
        filterShareContacts(); // reset filter
    }

    function filterShareContacts() {
        const input = document.getElementById('shareContactSearch').value.toLowerCase();
        const items = document.querySelectorAll('.share-contact-item');
        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(input) ? 'flex' : 'none';
        });
    }

    function confirmShareContact(name, phone) {
        closeShareContactModal();
        if (!activePhone) {
            alert('Please select a chat first.');
            return;
        }
        queuedContact = { name: name || 'Unknown', phone: phone };
        renderMediaPreviews();
        
        const attachmentMenu = document.getElementById('attachmentMenu');
        if (attachmentMenu) attachmentMenu.style.display = 'none';
    }

    // --- Sticker Modal Logic ---
    function openStickerModal() {
        document.getElementById('stickerModal').style.display = 'flex';
        const attachmentMenu = document.getElementById('attachmentMenu');
        if (attachmentMenu) attachmentMenu.style.display = 'none';

        const grid = document.getElementById('stickerGrid');
        if (grid.children.length === 0) {
            const stickers = [
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f600/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f602/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f923/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f60d/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f973/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f60e/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f4af/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f525/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/2764_fe0f/512.webp',
                'https://fonts.gstatic.com/s/e/notoemoji/latest/1f44d/512.webp'
            ];
            
            let html = '';
            stickers.forEach(url => {
                html += `
                    <div style="cursor: pointer; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; padding: 12px; background: #f8fafc; transition: all 0.2s; text-align: center;" 
                         onclick="sendSticker('${url}')"
                         onmouseenter="this.style.transform='scale(1.05)'; this.style.borderColor='#cbd5e1';"
                         onmouseleave="this.style.transform='scale(1)'; this.style.borderColor='#e2e8f0';">
                        <img src="${url}" style="width: 100%; height: auto; max-width: 80px;" alt="Sticker">
                    </div>
                `;
            });
            grid.innerHTML = html;
        }
    }

    function closeStickerModal() {
        document.getElementById('stickerModal').style.display = 'none';
    }

    function sendSticker(url) {
        closeStickerModal();
        if (!activePhone) {
            alert('Please select a chat first.');
            return;
        }
        queuedStickerUrl = url;
        renderMediaPreviews();
        
        const attachmentMenu = document.getElementById('attachmentMenu');
        if (attachmentMenu) attachmentMenu.style.display = 'none';
    }

    function markReadContact(phone, event) {
        if (event) event.stopPropagation();
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/live-chat/mark-read/${phone}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Success', 'Contact marked as read', 'success');
                }
                pollLiveData(); 
                document.querySelectorAll('.sidebar-menu-dropdown.show').forEach(el => el.classList.remove('show'));
            } else {
                alert('Failed to mark read: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error connecting to server.');
        });
    }

    function markUnreadContact(phone, event) {
        if (event) event.stopPropagation();
        
        // Instant UI update for "bold on unread" — force re-render by clearing cache
        if (conversationsData[phone]) {
            conversationsData[phone].unread_count = 1;
            pendingUnreadPhones.add(phone); // protect from poll overwrite
            lastSidebarHtml = ''; // bust cache so sidebar re-renders immediately
            renderSidebarContacts();
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/live-chat/mark-unread/${phone}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Success', 'Contact marked as unread', 'success');
                }
                // Just update sidebar — do NOT clear the chat window or deselect the contact
                // Only update the badge/bold in sidebar for the phone that was marked unread
                if (conversationsData[phone]) {
                    conversationsData[phone].unread_count = Math.max(conversationsData[phone].unread_count, 1);
                    pendingUnreadPhones.add(phone);
                    lastSidebarHtml = '';
                    renderSidebarContacts();
                }
                
                // Close dropdown
                document.querySelectorAll('.msg-dropdown-menu.show').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.sidebar-menu-dropdown.show').forEach(el => el.classList.remove('show'));
                // Refresh after short delay so server has committed the change
                setTimeout(() => { lastSidebarHtml = ''; pollLiveData(); }, 600);
            } else {
                alert('Failed to mark unread: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error connecting to server.');
        });
    }

    function toggleBlockContact(phone, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation(); // Keep the dropdown open so the user sees the button change immediately
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/live-chat/toggle-block/${phone}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast(data.is_blocked ? 'Blocked' : 'Unblocked', data.message, data.is_blocked ? 'warning' : 'success');
                }
                
                if (conversationsData[phone]) {
                    conversationsData[phone].is_blocked = data.is_blocked;
                    if (activePhone === phone) {
                        const btn = document.getElementById('btnToggleBlock');
                        const icon = document.getElementById('iconToggleBlock');
                        const text = document.getElementById('textToggleBlock');
                        if (btn && icon && text) {
                            if (data.is_blocked) {
                                btn.classList.remove('danger');
                                icon.className = 'fa-solid fa-unlock';
                                text.innerText = 'Unblock Contact';
                            } else {
                                btn.classList.add('danger');
                                icon.className = 'fa-solid fa-ban';
                                text.innerText = 'Block Contact';
                            }
                        }
                        updateReplyBarState(conversationsData[phone]);
                    }
                }
                
                // User requested to explicitly reload the page on block/unblock
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                alert('Failed to toggle block status: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error connecting to server.');
        });
    }

    // --- Delete / Edit Message ---
    function editMessage(event, msgId, oldText) {
        event.stopPropagation();
        document.getElementById(`msgMenu_${msgId}`).style.display = 'none';
        
        const input = document.getElementById('replyInput');
        input.value = oldText;
        input.focus();
        
        const btnSend = document.getElementById('btnSendReply');
        btnSend.innerHTML = '<i class="fa-solid fa-check"></i>';
        btnSend.style.backgroundColor = '#f59e0b';
        btnSend.style.borderColor = '#d97706';
        
        window.editingMessageId = msgId;
    }

    function deleteIndividualMessage(id, type) {
        const typeLabel = type === 'all' ? 'Delete for everyone' : 'Delete for me';
        if (!confirm(`Are you sure you want to ${typeLabel}?`)) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/live-chat/message/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Remove from local array instantly
                if (activePhone && conversationsData[activePhone]) {
                    conversationsData[activePhone].messages = conversationsData[activePhone].messages.filter(m => m.id !== id);
                    renderMessages(conversationsData[activePhone].messages);
                }
                if (typeof showToast === 'function') {
                    showToast('Deleted', `Message ${type === 'all' ? 'deleted for everyone' : 'deleted for you'}`, 'success');
                }
            } else {
                alert('Failed to delete message: ' + data.message);
            }
        })
        .catch(err => alert('Error connecting to server to delete message.'));
    }

    function toggleProfileSettings() {
        const menu = document.getElementById('profileSettingsMenu');
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    
    // Close profile settings dropdown on outside click
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('profileSettingsDropdown');
        const menu = document.getElementById('profileSettingsMenu');
        if (dropdown && menu && !dropdown.contains(e.target)) {
            menu.classList.remove('show');
        }

        // Close any open message dropdowns when clicking outside
        if (!e.target.closest('.message-dropdown')) {
            document.querySelectorAll('.msg-dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    function toggleMsgDropdown(event, id) {
        event.stopPropagation();
        
        // Close all other open menus first
        document.querySelectorAll('.msg-dropdown-menu.show').forEach(menu => {
            if (menu.id !== `msgMenu_${id}`) {
                menu.classList.remove('show');
            }
        });

        const menu = document.getElementById(`msgMenu_${id}`);
        if (menu) {
            menu.classList.toggle('show');
        }
    }

    function copyMessageText(event, text) {
        event.stopPropagation();
        navigator.clipboard.writeText(text).then(() => {
            if (typeof showToast === 'function') {
                showToast('Copied', 'Message copied to clipboard', 'success');
            }
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
        
        // Close the menu
        document.querySelectorAll('.msg-dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
    }

    function copyContactNumber(phone) {
        navigator.clipboard.writeText('+' + phone).then(() => {
            if (typeof showToast === 'function') {
                showToast('Copied', 'Phone number copied to clipboard', 'success');
            }
        });
        document.getElementById('profileSettingsMenu').classList.remove('show');
    }
    // --- Forward Feature Logic ---
    let selectedForwardPhones = [];

    function openForwardModal(msgId) {
        document.getElementById('forwardMsgId').value = msgId;
        document.getElementById('forwardModal').style.display = 'flex';
        selectedForwardPhones = [];
        
        clearForwardSelection();
        
        // Hide message dropdowns
        document.querySelectorAll('.msg-dropdown-menu.show').forEach(el => el.classList.remove('show'));
    }

    function closeForwardModal() {
        document.getElementById('forwardModal').style.display = 'none';
    }

    function updateForwardFooter() {
        const footer = document.getElementById('forwardFooter');
        const countSpan = document.getElementById('forwardSelectedCount');
        if (selectedForwardPhones.length > 0) {
            footer.style.display = 'flex';
            countSpan.innerText = selectedForwardPhones.length + (selectedForwardPhones.length > 1 ? ' selected' : ' selected');
        } else {
            footer.style.display = 'none';
        }
    }

    function clearForwardSelection() {
        selectedForwardPhones = [];
        document.querySelectorAll('.forward-contact-item').forEach(el => {
            el.classList.remove('selected-forward');
            el.style.backgroundColor = 'transparent';
            const checkbox = el.querySelector('.forward-checkbox');
            checkbox.style.backgroundColor = 'transparent';
            checkbox.style.borderColor = '#8696a0';
            el.querySelector('.fa-check').style.display = 'none';
        });
        updateForwardFooter();
    }

    function toggleForwardSelection(el, phone) {
        const idx = selectedForwardPhones.indexOf(phone);
        const checkbox = el.querySelector('.forward-checkbox');
        const checkIcon = el.querySelector('.fa-check');
        if (idx > -1) {
            selectedForwardPhones.splice(idx, 1);
            el.classList.remove('selected-forward');
            el.style.backgroundColor = 'transparent';
            checkbox.style.backgroundColor = 'transparent';
            checkbox.style.borderColor = '#8696a0';
            checkIcon.style.display = 'none';
        } else {
            selectedForwardPhones.push(phone);
            el.classList.add('selected-forward');
            el.style.backgroundColor = '#f3f4f6';
            checkbox.style.backgroundColor = '#00a884';
            checkbox.style.borderColor = '#00a884';
            checkIcon.style.display = 'block';
        }
        updateForwardFooter();
    }

    function filterForwardContacts() {
        const query = document.getElementById('forwardContactSearch').value.toLowerCase();
        document.querySelectorAll('.forward-contact-item').forEach(el => {
            const name = el.querySelector('.contact-name').innerText.toLowerCase();
            const phone = el.querySelector('.contact-snippet').innerText.toLowerCase();
            if (name.includes(query) || phone.includes(query)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    function submitForward() {
        if (selectedForwardPhones.length === 0) {
            alert("Please select at least one contact to forward to.");
            return;
        }
        const msgId = document.getElementById('forwardMsgId').value;
        const btn = document.getElementById('btnSubmitForward');
        
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Forwarding...';
        btn.disabled = true;

        fetch('/live-chat/forward', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phones: selectedForwardPhones,
                message_id: msgId
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Success', 'Message forwarded successfully', 'success');
                }
                closeForwardModal();
            } else {
                alert(data.message || 'Error forwarding message.');
            }
        })
        .catch(err => {
            console.error(err);
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Network error while forwarding.');
        });
    }

    // --- Selective Print Logic ---
    let isPrintMode = false;

    function enablePrintMode() {
        isPrintMode = true;
        document.getElementById('printActionBanner').style.display = 'flex';
        
        // Close dropdown menu
        const dropdowns = document.querySelectorAll('.msg-dropdown-menu.show');
        dropdowns.forEach(d => d.classList.remove('show'));

        // Show checkboxes
        const checkboxes = document.querySelectorAll('.print-checkbox-container');
        checkboxes.forEach(c => c.style.display = 'block');

        // Reset state
        document.getElementById('printSelectAll').checked = false;
        document.querySelectorAll('.print-msg-checkbox').forEach(c => c.checked = false);
        updatePrintSelectionCount();
    }

    function cancelPrintMode() {
        isPrintMode = false;
        document.getElementById('printActionBanner').style.display = 'none';
        
        // Hide checkboxes
        const checkboxes = document.querySelectorAll('.print-checkbox-container');
        checkboxes.forEach(c => c.style.display = 'none');
        
        // Reset state
        document.querySelectorAll('.print-msg-checkbox').forEach(c => c.checked = false);
        updatePrintSelectionCount();
    }

    function toggleSelectAllForPrint(el) {
        const checkboxes = document.querySelectorAll('.print-msg-checkbox');
        checkboxes.forEach(c => {
            // Only select if it's currently visible/in DOM
            c.checked = el.checked;
        });
        updatePrintSelectionCount();
    }

    function updatePrintSelectionCount() {
        const checkedCount = document.querySelectorAll('.print-msg-checkbox:checked').length;
        document.getElementById('printSelectionCount').innerText = checkedCount + ' selected';
    }

    function executePrintSelected() {
        const checkedBoxes = document.querySelectorAll('.print-msg-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select at least one message to print.');
            return;
        }

        // Create a hidden iframe
        let printFrame = document.getElementById('printFrame');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'printFrame';
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = 'none';
            document.body.appendChild(printFrame);
        }

        const doc = printFrame.contentWindow.document;
        doc.open();

        // Start HTML for print
        let printHtml = `
        <html>
        <head>
            <title>Chat Print</title>
            <style>
                @media print {
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
                body { font-family: 'Inter', sans-serif; padding: 20px; color: #000000; background: #ffffff; }
                .chat-header { font-size: 18px; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; background: #fff; padding: 10px; }
                .message { margin-bottom: 15px; padding: 10px 15px; border-radius: 8px; max-width: 80%; display: inline-block; clear: both; page-break-inside: avoid; border: 1px solid #000; box-shadow: none; color: #000; }
                .msg-incoming { background-color: #ffffff; float: left; border-bottom-left-radius: 0; }
                .msg-outgoing { background-color: #ffffff; float: right; border-bottom-right-radius: 0; }
                .msg-meta { font-size: 11px; color: #333; margin-top: 5px; text-align: right; display: block; }
                .clearfix::after { content: ""; clear: both; display: table; }
                .message-media img, .message-media video { max-width: 200px; max-height: 200px; display: block; margin-top: 5px; }
                
                /* Fix FontAwesome SVGs and emojis blowing up in print */
                svg, .svg-inline--fa, img.emoji {
                    width: 1em !important;
                    height: 1em !important;
                    max-width: 16px !important;
                    max-height: 16px !important;
                    display: inline-block !important;
                    vertical-align: -0.125em !important;
                }
                
                /* Hide UI elements that shouldn't be printed */
                .message-dropdown, .print-checkbox-container, .reply-quote {
                    display: none !important;
                }
                a { text-decoration: none; color: inherit; }
            </style>
        </head>
        <body>
            <div class="chat-header">
                Conversation with ${conversationsData[activePhone]?.display_name || 'Contact'} (+${activePhone})
            </div>
            <div class="clearfix">
        `;

        let lastDateText = null;

        // Extract selected messages
        checkedBoxes.forEach(box => {
            const bubbleEl = box.closest('.message-bubble');
            const isOutgoing = bubbleEl.classList.contains('message-outgoing');
            const contentWrap = bubbleEl.querySelector('.bubble-content-wrap');
            
            if (!contentWrap) return;

            // Find the closest previous date-separator for this message
            let prev = bubbleEl.previousElementSibling;
            let currentDateText = null;
            while(prev) {
                if (prev.classList.contains('date-separator')) {
                    currentDateText = prev.innerText.trim();
                    break;
                }
                prev = prev.previousElementSibling;
            }

            if (currentDateText && currentDateText !== lastDateText) {
                printHtml += `
                <div style="text-align: center; margin: 20px 0 10px 0; clear: both;">
                    <span style="background: #f1f5f9; padding: 6px 14px; border-radius: 12px; font-size: 12px; font-weight: 600; color: #475569; display: inline-block;">
                        ${currentDateText}
                    </span>
                </div>
                <div class="clearfix"></div>`;
                lastDateText = currentDateText;
            }

            // Clone content to manipulate safely
            const contentClone = contentWrap.cloneNode(true);
            
            // Extract meta
            const metaDiv = contentClone.querySelector('.message-meta');
            let metaHtml = '';
            if (metaDiv) {
                metaHtml = `<div class="msg-meta">${metaDiv.innerHTML}</div>`;
                metaDiv.remove();
            }

            printHtml += `
                <div class="message ${isOutgoing ? 'msg-outgoing' : 'msg-incoming'}">
                    ${contentClone.innerHTML}
                    ${metaHtml}
                </div>
                <div class="clearfix"></div>
            `;
        });

        printHtml += `
            </div>
            <script>
                window.onload = function() { window.print(); }
            <\/script>
        </body>
        </html>
        `;

        doc.write(printHtml);
        doc.close();
        
        // Exit print mode after initiating print
        cancelPrintMode();
    }

</script>
@endsection
