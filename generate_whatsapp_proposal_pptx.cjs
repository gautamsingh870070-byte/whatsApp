const pptxgen = require('c:/xampp/htdocs/nppkandhla/node_modules/pptxgenjs');
const path = require('path');
const fs = require('fs');

console.log("Generating Professional Widescreen WhatsApp Enterprise Proposal PPTX...");

const pres = new pptxgen();
pres.layout = 'LAYOUT_16x9'; // 10 x 5.625 inches
pres.author = 'STM Developments';
pres.company = 'STM Developments';
pres.title = 'Enterprise WhatsApp Business Cloud API & Omnichannel SaaS Platform';
pres.subject = 'Comprehensive Client Proposal & System Architecture';

// Corporate Color Palette (WhatsApp Emerald + Deep Slate)
const C_EMERALD = '059669';       // WhatsApp Primary Emerald
const C_VIBRANT = '10B981';       // Light Vibrant Green
const C_LIGHT_GREEN = 'ECFDF5';   // Background Emerald Tint
const C_NAVY = '0F172A';          // Deep Slate Navy
const C_SLATE_DARK = '1E293B';    // Card Dark
const C_SLATE_TEXT = '334155';    // Main Body Text
const C_SLATE_MUTED = '64748B';   // Subtitle Muted Gray
const C_SLATE_LIGHT = 'F8FAFC';   // Soft Card Background
const C_WHITE = 'FFFFFF';
const C_BORDER = 'E2E8F0';        // Clean Border
const C_AMBER = 'D97706';         // Warning/Highlight
const C_BLUE = '2563EB';          // Accent Blue

const SCREENSHOTS_DIR = 'C:/xampp/htdocs/whatsupApp/screenshots/';
const OUTPUT_DIR_1 = 'C:/Users/HP/Documents/Proposal/PPT WhatsApp proposal/';
const OUTPUT_DIR_2 = 'C:/Users/HP/Documents/Proposal/';
const OUTPUT_FILENAME = 'WhatsApp_Business_Platform_Proposal_STM_Developments.pptx';

// Helper: Standard Slide Layout
function applyStandardLayout(slide, slideNumber, category, title, subtitle) {
    // Top Emerald Brand Bar
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0, y: 0, w: 10, h: 0.12,
        fill: { color: C_EMERALD },
        line: { color: C_EMERALD }
    });

    // Top Category Tag
    slide.addText(category.toUpperCase(), {
        x: 0.6, y: 0.22, w: 6.5, h: 0.22,
        fontSize: 8.5, bold: true, color: C_EMERALD, fontFace: 'Calibri'
    });

    // Slide Main Title
    slide.addText(title, {
        x: 0.6, y: 0.42, w: 8.8, h: 0.45,
        fontSize: 17, bold: true, color: C_NAVY, fontFace: 'Calibri'
    });

    // Slide Subtitle
    if (subtitle) {
        slide.addText(subtitle, {
            x: 0.6, y: 0.82, w: 8.8, h: 0.28,
            fontSize: 9.5, color: C_SLATE_MUTED, fontFace: 'Calibri'
        });
    }

    // Bottom Footer Line
    slide.addShape(pres.shapes.LINE, {
        x: 0.6, y: 5.25, w: 8.8, h: 0,
        line: { color: C_BORDER, width: 1 }
    });

    // Footer Left
    slide.addText('Enterprise WhatsApp Business Cloud API & Omnichannel SaaS Platform  |  STM Developments', {
        x: 0.6, y: 5.30, w: 7.0, h: 0.25,
        fontSize: 8, color: '94A3B8', fontFace: 'Calibri'
    });

    // Footer Right
    slide.addText(`Slide ${slideNumber} of 22`, {
        x: 7.8, y: 5.30, w: 1.6, h: 0.25,
        fontSize: 8, color: '94A3B8', align: 'right', fontFace: 'Calibri'
    });
}

// Helper: Screenshot Slide with Browser Window & Analytical Highlight Cards
function addScreenshotSlide(slideNumber, category, title, subtitle, imgFile, windowLabel, highlights) {
    const slide = pres.addSlide();
    applyStandardLayout(slide, slideNumber, category, title, subtitle);

    const imgPath = path.join(SCREENSHOTS_DIR, imgFile);
    const imgExists = fs.existsSync(imgPath);

    // Simulated Browser Window Frame
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0.55, y: 1.22, w: 5.55, h: 3.88,
        fill: { color: C_WHITE },
        line: { color: C_BORDER, width: 1.5 }
    });

    // Browser Header Bar
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0.55, y: 1.22, w: 5.55, h: 0.35,
        fill: { color: 'F1F5F9' },
        line: { color: C_BORDER, width: 1 }
    });

    // Window Dots (Mac style)
    const dotColors = ['EF4444', 'F59E0B', '10B981'];
    dotColors.forEach((color, i) => {
        slide.addShape(pres.shapes.OVAL, {
            x: 0.70 + (i * 0.16), y: 1.34, w: 0.10, h: 0.10,
            fill: { color: color },
            line: { color: color }
        });
    });

    // Browser Address / Module Pill
    slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
        x: 1.25, y: 1.27, w: 4.65, h: 0.24,
        rectRadius: 0.05,
        fill: { color: C_WHITE },
        line: { color: 'CBD5E1', width: 0.8 }
    });

    slide.addText(`🔒 https://portal.business.com • ${windowLabel}`, {
        x: 1.35, y: 1.28, w: 4.5, h: 0.22,
        fontSize: 7.5, color: '64748B', bold: true, fontFace: 'Calibri'
    });

    // Image Placement
    if (imgExists) {
        slide.addImage({
            path: imgPath,
            x: 0.57, y: 1.58, w: 5.51, h: 3.50
        });
    } else {
        slide.addText(`[Screenshot Preview: ${imgFile}]`, {
            x: 0.6, y: 2.5, w: 5.5, h: 1.0,
            fontSize: 12, color: '94A3B8', align: 'center'
        });
    }

    // Right Column: Key Capabilities & Business Impact Cards
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 6.25, y: 1.22, w: 3.2, h: 3.88,
        fill: { color: C_SLATE_LIGHT },
        line: { color: C_BORDER, width: 1 }
    });

    // Right Column Header
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 6.25, y: 1.22, w: 3.2, h: 0.35,
        fill: { color: C_LIGHT_GREEN },
        line: { color: C_BORDER, width: 0.8 }
    });

    slide.addText('KEY CAPABILITIES & VALUE', {
        x: 6.35, y: 1.27, w: 3.0, h: 0.25,
        fontSize: 9, bold: true, color: C_EMERALD, fontFace: 'Calibri'
    });

    // Render Highlights
    const maxItems = Math.min(highlights.length, 5);
    const itemHeight = maxItems === 5 ? 0.64 : 0.78;
    const spacing = maxItems === 5 ? 0.67 : 0.82;

    for (let i = 0; i < maxItems; i++) {
        const h = highlights[i];
        const itemY = 1.65 + (i * spacing);

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: 6.35, y: itemY, w: 3.0, h: itemHeight,
            rectRadius: 0.04,
            fill: { color: C_WHITE },
            line: { color: C_BORDER, width: 0.6 }
        });

        // Left accent indicator
        slide.addShape(pres.shapes.RECTANGLE, {
            x: 6.35, y: itemY, w: 0.08, h: itemHeight,
            fill: { color: C_EMERALD },
            line: { color: C_EMERALD }
        });

        // Title
        slide.addText(h.title, {
            x: 6.50, y: itemY + 0.03, w: 2.8, h: 0.22,
            fontSize: 9, bold: true, color: C_NAVY, fontFace: 'Calibri'
        });

        // Description
        slide.addText(h.desc, {
            x: 6.50, y: itemY + 0.24, w: 2.8, h: itemHeight - 0.26,
            fontSize: 7.8, color: C_SLATE_TEXT, fontFace: 'Calibri'
        });
    }
}

// -------------------------------------------------------------
// SLIDE 1: COVER SLIDE
// -------------------------------------------------------------
{
    const slide = pres.addSlide();

    // Dark Slate Background
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0, y: 0, w: 10, h: 5.625,
        fill: { color: C_NAVY },
        line: { color: C_NAVY }
    });

    // Emerald Left Accent Pillar
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0, y: 0, w: 0.35, h: 5.625,
        fill: { color: C_EMERALD },
        line: { color: C_EMERALD }
    });

    // Top Category
    slide.addText('ENTERPRISE DIGITAL TRANSFORMATION PROPOSAL', {
        x: 0.9, y: 0.85, w: 8.5, h: 0.3,
        fontSize: 10.5, bold: true, color: C_VIBRANT, fontFace: 'Calibri'
    });

    // Main Title
    slide.addText('Enterprise WhatsApp Business Cloud API\n& Omnichannel Messaging Platform', {
        x: 0.9, y: 1.25, w: 8.5, h: 1.35,
        fontSize: 27, bold: true, color: C_WHITE, fontFace: 'Calibri'
    });

    // Subtitle
    slide.addText('Automated Customer Support • Smart Bulk Broadcasting • Real-Time Live Chat Desk • Meta Cloud API & RCS Hub', {
        x: 0.9, y: 2.70, w: 8.5, h: 0.38,
        fontSize: 12, color: '94A3B8', fontFace: 'Calibri'
    });

    // Feature Badges
    const badges = [
        '✓ Official Meta Cloud API v25.0',
        '✓ Live 2-Way Chat Desk',
        '✓ Rich Media & CTA Buttons',
        '✓ Dynamic Template Engine',
        '✓ Multi-Tenant Credit Wallet'
    ];
    badges.forEach((b, idx) => {
        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: 0.9 + (idx * 1.68), y: 3.25, w: 1.60, h: 0.36,
            rectRadius: 0.05,
            fill: { color: '1E293B' },
            line: { color: C_EMERALD, width: 1 }
        });
        slide.addText(b, {
            x: 0.9 + (idx * 1.68), y: 3.25, w: 1.60, h: 0.36,
            fontSize: 7.8, bold: true, color: 'A7F3D0', align: 'center', fontFace: 'Calibri'
        });
    });

    // Metadata Card
    slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
        x: 0.9, y: 3.95, w: 8.25, h: 1.05,
        rectRadius: 0.06,
        fill: { color: '112240' },
        line: { color: '334155', width: 1 }
    });

    const metaFields = [
        { label: 'PREPARED FOR', val: 'Enterprise Clients & Corporate Partners' },
        { label: 'DEVELOPED BY', val: 'STM Developments' },
        { label: 'TECHNOLOGY STACK', val: 'PHP 8.2 • Laravel • MySQL • Meta Cloud API' },
        { label: 'PROPOSAL STATUS', val: 'Official Commercial Proposal Deck • Nov 2026' }
    ];

    metaFields.forEach((m, idx) => {
        const xPos = 1.1 + (idx * 2.05);
        slide.addText(m.label, {
            x: xPos, y: 4.10, w: 2.0, h: 0.22,
            fontSize: 7.5, bold: true, color: C_VIBRANT, fontFace: 'Calibri'
        });
        slide.addText(m.val, {
            x: xPos, y: 4.35, w: 2.0, h: 0.55,
            fontSize: 9, bold: true, color: C_WHITE, fontFace: 'Calibri'
        });
    });
}

// -------------------------------------------------------------
// SLIDE 2: EXECUTIVE SUMMARY & ROI
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 2, 'Strategic Value', 'Executive Summary: Why WhatsApp Business Platform?', 'WhatsApp delivers 10x higher engagement and instant customer conversion compared to traditional channels.');

    // 4 Big Stat Cards
    const stats = [
        { num: '98%', label: 'Average Open Rate', desc: 'Read within 3 minutes of delivery vs 18% for email.' },
        { num: '45-60%', label: 'Click-Through Rate', desc: 'Interactive buttons drive unmatched response & sales.' },
        { num: '< 60s', label: 'Average Response Time', desc: 'Instant automated answers via pre-approved flows.' },
        { num: '75%', label: 'Support Cost Reduction', desc: 'Replaces repetitive manual calling with digital workflows.' }
    ];

    stats.forEach((s, idx) => {
        const cardX = 0.6 + (idx * 2.25);
        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: cardX, y: 1.30, w: 2.15, h: 1.30,
            rectRadius: 0.05,
            fill: { color: C_LIGHT_GREEN },
            line: { color: 'A7F3D0', width: 1 }
        });

        slide.addText(s.num, {
            x: cardX, y: 1.40, w: 2.15, h: 0.45,
            fontSize: 22, bold: true, color: C_EMERALD, align: 'center', fontFace: 'Calibri'
        });

        slide.addText(s.label, {
            x: cardX, y: 1.85, w: 2.15, h: 0.25,
            fontSize: 9.5, bold: true, color: C_NAVY, align: 'center', fontFace: 'Calibri'
        });

        slide.addText(s.desc, {
            x: cardX + 0.1, y: 2.10, w: 1.95, h: 0.45,
            fontSize: 7.8, color: C_SLATE_TEXT, align: 'center', fontFace: 'Calibri'
        });
    });

    // Comparison Table: Traditional vs STM WhatsApp Platform
    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0.6, y: 2.85, w: 8.8, h: 2.25,
        fill: { color: C_SLATE_LIGHT },
        line: { color: C_BORDER, width: 1 }
    });

    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0.6, y: 2.85, w: 8.8, h: 0.35,
        fill: { color: C_NAVY }
    });

    slide.addText('BUSINESS CHALLENGE / METRIC', {
        x: 0.8, y: 2.90, w: 2.8, h: 0.25,
        fontSize: 8.5, bold: true, color: C_WHITE, fontFace: 'Calibri'
    });
    slide.addText('TRADITIONAL CHANNELS (SMS / EMAIL / CALLS)', {
        x: 3.7, y: 2.90, w: 2.8, h: 0.25,
        fontSize: 8.5, bold: true, color: 'CBD5E1', fontFace: 'Calibri'
    });
    slide.addText('STM WHATSAPP BUSINESS PLATFORM', {
        x: 6.6, y: 2.90, w: 2.6, h: 0.25,
        fontSize: 8.5, bold: true, color: 'A7F3D0', fontFace: 'Calibri'
    });

    const rows = [
        { metric: 'Customer Reach & Trust', old: 'Ignored spam SMS & unverified numbers with low trust', stm: 'Official Green Tick / Meta Verified Business Profile' },
        { metric: 'Interactivity & Engagement', old: 'Static plain text with zero actions or high bounce rates', stm: 'Rich Media, Instant Reply Chips & CTA Action Buttons' },
        { metric: 'Support Availability', old: 'Limited to 9-to-6 manual staff with long queue wait times', stm: '24/7 Automated Response + Real-Time Live Support Desk' },
        { metric: 'Audit & Delivery Transparency', old: 'Opaque telecom gateway reports with unknown read rates', stm: 'Real-time Sent, Delivered, and Read Tick Receipts' }
    ];

    rows.forEach((r, idx) => {
        const rowY = 3.25 + (idx * 0.45);
        slide.addShape(pres.shapes.LINE, {
            x: 0.6, y: rowY, w: 8.8, h: 0,
            line: { color: C_BORDER, width: 0.8 }
        });

        slide.addText(r.metric, {
            x: 0.8, y: rowY + 0.08, w: 2.8, h: 0.35,
            fontSize: 8.5, bold: true, color: C_NAVY, fontFace: 'Calibri'
        });
        slide.addText(r.old, {
            x: 3.7, y: rowY + 0.08, w: 2.8, h: 0.35,
            fontSize: 8, color: '64748B', fontFace: 'Calibri'
        });
        slide.addText(`✓ ${r.stm}`, {
            x: 6.6, y: rowY + 0.08, w: 2.6, h: 0.35,
            fontSize: 8, bold: true, color: C_EMERALD, fontFace: 'Calibri'
        });
    });
}

// -------------------------------------------------------------
// SLIDE 3: PLATFORM ARCHITECTURE & 4 PILLARS
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 3, 'System Architecture', 'Core Functional Pillars of STM WhatsApp Platform', 'A robust, multi-tenant enterprise software designed for high-throughput messaging, automation, and live customer resolution.');

    const pillars = [
        {
            icon: '01',
            title: 'Bulk Broadcasting & Campaigns',
            subtitle: 'High-Volume Engagement Engine',
            points: [
                'Official Meta Cloud API message dispatch',
                'Excel / CSV bulk phone number importer',
                'Dynamic body variable personalization',
                'Click-to-Call & Visit-Website CTA buttons',
                'Smart scheduling for off-peak/festive drops'
            ]
        },
        {
            icon: '02',
            title: 'Omnichannel Live Chat Desk',
            subtitle: 'Unified Conversational Support',
            points: [
                'WhatsApp Web-style live agent console',
                '2-Way real-time chat with instant updates',
                'Quick Support reply chips for fast resolution',
                '24-Hour customer window policy tracker',
                'Forward, mark read, block, and export chats'
            ]
        },
        {
            icon: '03',
            title: 'Meta Template Management',
            subtitle: 'WABA Direct Cloud Synchronization',
            points: [
                'Live sync with Meta Business Portfolio',
                'Marketing, Utility & Auth HSM templates',
                'Image banner, document & video headers',
                'Submit new custom templates to Meta',
                'Strict compliance with Meta anti-spam rules'
            ]
        },
        {
            icon: '04',
            title: 'Enterprise Multi-Tenant SaaS',
            subtitle: 'Billing, RBAC & RCS Engine',
            points: [
                'Superadmin, Admin & Client user roles',
                'Prepaid credit wallet with real-time debit',
                'Granular feature-by-feature permission matrix',
                'Detailed 30-day message logs & audit trail',
                'Next-Gen Google RCS Verified messaging'
            ]
        }
    ];

    pillars.forEach((p, idx) => {
        const xPos = 0.6 + (idx * 2.25);

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: xPos, y: 1.30, w: 2.15, h: 3.75,
            rectRadius: 0.05,
            fill: { color: C_WHITE },
            line: { color: C_BORDER, width: 1 }
        });

        // Top Header
        slide.addShape(pres.shapes.RECTANGLE, {
            x: xPos, y: 1.30, w: 2.15, h: 0.85,
            fill: { color: C_SLATE_DARK }
        });

        slide.addText(p.icon, {
            x: xPos + 0.15, y: 1.38, w: 0.45, h: 0.35,
            fontSize: 16, bold: true, color: C_VIBRANT, fontFace: 'Calibri'
        });

        slide.addText(p.title, {
            x: xPos + 0.15, y: 1.70, w: 1.9, h: 0.42,
            fontSize: 9.5, bold: true, color: C_WHITE, fontFace: 'Calibri'
        });

        slide.addText(p.subtitle, {
            x: xPos + 0.15, y: 2.22, w: 1.85, h: 0.25,
            fontSize: 8, bold: true, color: C_EMERALD, fontFace: 'Calibri'
        });

        // Points
        p.points.forEach((pt, pIdx) => {
            const ptY = 2.55 + (pIdx * 0.50);
            slide.addText(`•  ${pt}`, {
                x: xPos + 0.12, y: ptY, w: 1.92, h: 0.45,
                fontSize: 7.8, color: C_SLATE_TEXT, fontFace: 'Calibri'
            });
        });
    });
}

// -------------------------------------------------------------
// SLIDE 4: EXECUTIVE DASHBOARD
// -------------------------------------------------------------
addScreenshotSlide(
    4,
    'Executive Command Center',
    'Executive Analytics & Real-Time Operations Dashboard',
    'High-level operational overview monitoring Meta API connection health, webhook readiness, and user account status.',
    '01_dashboard.png',
    'WABA Portal • Executive Dashboard & System Overview',
    [
        {
            title: 'Live Connection Health Monitoring',
            desc: 'Real-time status indicators for Meta WhatsApp API connection, active webhook listener, and MySQL sync.'
        },
        {
            title: 'Quick Actions Command Bar',
            desc: 'One-click shortcuts to initiate immediate campaigns, compose messages, and access customer phonebooks.'
        },
        {
            title: 'Unified User & Role Visibility',
            desc: 'Displays logged-in superadmin / client credentials, role authority badges, and assigned messaging quotas.'
        },
        {
            title: 'Omnichannel Navigation Hub',
            desc: 'Seamless single-click transitions between Live Chat, History Logs, Template Manager, and RCS Settings.'
        },
        {
            title: 'Zero Latency Performance',
            desc: 'Built on optimized Laravel architecture ensuring instant load times even under heavy broadcast workloads.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 5: BULK BROADCAST CAMPAIGN ENGINE
// -------------------------------------------------------------
addScreenshotSlide(
    5,
    'Broadcasting Engine',
    'Bulk WhatsApp Broadcast Studio & Smart Campaign Dispatcher',
    'Enterprise-grade messaging engine supporting pre-approved templates, rich media attachments, and interactive CTA buttons.',
    '02_send_wa.png',
    'WABA Portal • WhatsApp Bulk Message Dispatcher',
    [
        {
            title: 'Dual Messaging Capabilities',
            desc: 'Send approved Meta HSM templates for promotional drops or custom free-form messages within the 24-hr window.'
        },
        {
            title: 'Dynamic Body Personalization',
            desc: 'Pass comma-separated dynamic variables (e.g. {{1}} Customer Name, {{2}} Discount %, {{3}} Order ID).'
        },
        {
            title: 'Rich Media Attachments',
            desc: 'Direct drag-and-drop upload for promotional posters, PDF product catalogs, invoices, and multimedia brochures.'
        },
        {
            title: 'Interactive CTA Action Buttons',
            desc: 'Empowers recipients with 1-click "Visit Website" URLs and "Call Now" contact buttons, boosting conversions.'
        },
        {
            title: 'Bulk Excel Contact Importer',
            desc: 'Upload thousands of verified phone numbers directly via Excel/CSV demo templates with zero manual typing.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 6: LIVE CHAT & TWO-WAY SUPPORT DESK
// -------------------------------------------------------------
addScreenshotSlide(
    6,
    'Conversational Support',
    'Omnichannel Live Chat & Two-Way Customer Resolution Desk',
    'WhatsApp Web-style interactive inbox enabling support agents to review conversations, reply instantly, and manage leads.',
    '03_live_chat.png',
    'WABA Portal • Real-Time WhatsApp Live Chat Console',
    [
        {
            title: 'Unified Customer Conversation Inbox',
            desc: 'Centralized live message stream displaying all active conversations, customer names, and message timestamps.'
        },
        {
            title: 'Real-Time WhatsApp UI Bubbles',
            desc: 'Familiar WhatsApp look & feel with incoming/outgoing bubbles, delivery double-ticks, and sender verification.'
        },
        {
            title: 'Instant Quick Support Reply Chips',
            desc: 'Pre-configured 1-click quick replies ("Quick Support", "Campaign Update", "Request Info") for rapid handling.'
        },
        {
            title: '24-Hour Policy Window Tracking',
            desc: 'Automated compliance indicator tracks the 24h customer-initiated care window and alerts agents.'
        },
        {
            title: 'Advanced Conversation Controls',
            desc: 'Forward critical messages, mark read/unread, toggle spam blocklist, and export complete chat transcripts.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 7: META TEMPLATE MANAGEMENT & WABA SYNC
// -------------------------------------------------------------
addScreenshotSlide(
    7,
    'Meta Cloud API Sync',
    'Official Meta WhatsApp Template Management & Approval Hub',
    'Direct two-way synchronization with Meta Business Portfolio to manage, preview, and create approved HSM templates.',
    '04_manage_templates.png',
    'WABA Portal • Meta Approved Templates Repository',
    [
        {
            title: 'Direct Meta WABA Cloud Sync',
            desc: 'Automatically synchronizes with your WhatsApp Business Account (WABA) to reflect real-time approval status.'
        },
        {
            title: 'Multi-Category Classification',
            desc: 'Organizes templates into Marketing (festive sales, updates), Utility (order alerts), and Authentication (OTPs).'
        },
        {
            title: 'Rich Banner & Media Headers',
            desc: 'Supports high-impact visual banners, promotional graphics, and customized multilingual greetings (English, Hindi).'
        },
        {
            title: 'In-App Template Creator',
            desc: 'Submit new interactive template designs directly to Meta without needing to open Meta Business Manager.'
        },
        {
            title: 'Policy Compliance Assurance',
            desc: 'Guarantees strict alignment with Meta messaging policies, preventing account blocks and ensuring top quality score.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 8: CAMPAIGN DELIVERY TRACKING & AUDIT LOGS
// -------------------------------------------------------------
addScreenshotSlide(
    8,
    'Delivery Analytics',
    'Campaign Delivery Tracking & Granular Audit Reports',
    'Complete visibility into every outbound dispatch with real-time delivery confirmations, failure codes, and audit logs.',
    '05_history.png',
    'WABA Portal • 30-Day Campaign Reports & Delivery Logs',
    [
        {
            title: 'Granular Recipient-Level Tracking',
            desc: 'Logs every single sent message with exact phone number, campaign title, template utilized, and timestamp.'
        },
        {
            title: 'Real-Time Delivery Status Verification',
            desc: 'Instant visual tags for "Success" vs "Failed", confirming whether the message was accepted by Meta Cloud API.'
        },
        {
            title: 'Downloadable Diagnostic Logs',
            desc: 'One-click "Download Log" button for each message to inspect Meta response payloads and delivery receipts.'
        },
        {
            title: 'Date-Filtered Performance Audits',
            desc: 'Filter reports by customizable date ranges (e.g. last 30 days) to calculate campaign ROI and delivery ratios.'
        },
        {
            title: 'High-Throughput Pagination',
            desc: 'Effortlessly browses tens of thousands of records with rapid server-side pagination and search filtering.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 9: AUTOMATED CAMPAIGN SCHEDULER
// -------------------------------------------------------------
addScreenshotSlide(
    9,
    'Automated Dispatch',
    'Automated Campaign Scheduler & Queue Processing Engine',
    'Schedule marketing drops and critical transactional alerts for future delivery with automated queue workers.',
    '06_schedule_log.png',
    'WABA Portal • Scheduled Broadcasts & Dispatch Queue',
    [
        {
            title: 'Precision Time-Based Scheduling',
            desc: 'Set custom launch dates and times to reach customers during peak engagement hours (e.g. morning/evening).'
        },
        {
            title: 'Automated Background Worker',
            desc: 'Asynchronous cron daemons execute campaigns in the background without slowing down the active web portal.'
        },
        {
            title: 'Campaign Status Lifecycle',
            desc: 'Tracks progress across Pending, Running, and "Completed" states with target recipient count validation.'
        },
        {
            title: 'Auditable User Attribution',
            desc: 'Logs which administrator or client scheduled the campaign, providing end-to-end accountability.'
        },
        {
            title: 'Overrun Protection & Throttling',
            desc: 'Spreads high-volume dispatches evenly to comply with Meta tier limits and avoid network congestion.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 10: SMART CONTACT MANAGEMENT & PHONEBOOK
// -------------------------------------------------------------
addScreenshotSlide(
    10,
    'CRM & Phonebook',
    'Smart Customer Phonebook & Automated Lead Capture',
    'Centralized CRM contact database featuring instant lead capture, bulk CSV/Excel import/export, and customer tagging.',
    '07_contacts.png',
    'WABA Portal • Customer Contact Directory & Lead Management',
    [
        {
            title: 'Automated Inbound Lead Capture',
            desc: 'Inbound chat inquiries are automatically captured and recorded into the contact database with an "Auto" badge.'
        },
        {
            title: 'Bulk Excel / CSV Data Importer',
            desc: 'Quickly upload thousands of verified business leads, customer records, and phone numbers in seconds.'
        },
        {
            title: '1-Click Data Portability & Export',
            desc: 'Export the complete contact list to clean CSV format for external CRM, ERP, or marketing tool integration.'
        },
        {
            title: 'Customer Notes & Manual Tagging',
            desc: 'Attach customer-specific notes, purchase history flags, and manually verified status badges.'
        },
        {
            title: 'Spam Protection & Blacklisting',
            desc: 'Effortlessly toggle block status to prevent unwanted messaging and safeguard sender quality score.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 11: MULTI-TENANT CREDIT WALLET & QUOTAS
// -------------------------------------------------------------
addScreenshotSlide(
    11,
    'Billing & Credits',
    'Multi-Tenant Credit Wallet & Usage Quota Management',
    'Enterprise prepaid wallet model providing full financial control, per-message deduction, and client credit allocation.',
    '08_credit_manage.png',
    'WABA Portal • Messaging Credit Allocation & Limit Controls',
    [
        {
            title: 'Prepaid Wallet & Per-Message Debit',
            desc: 'Messages are debited in real time from the client balance, eliminating risk of unpaid overages.'
        },
        {
            title: 'Granular Credit Allocation',
            desc: 'Superadmins can assign custom credit packages and raise daily message limits for any client or reseller.'
        },
        {
            title: 'Network-Wide Limit Monitoring',
            desc: 'Tracks total network credits (e.g. 16,540 total limits) and active allocated credits across all sub-accounts.'
        },
        {
            title: 'Usage Caps & Fraud Prevention',
            desc: 'Automated safety thresholds prevent unauthorized broadcast blasts and enforce fair usage policies.'
        },
        {
            title: 'Reseller Monetization Ready',
            desc: 'Allows agency owners to resell WhatsApp messaging services at custom profit margins per credit.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 12: CREDIT TRANSACTION HISTORY & AUDITING
// -------------------------------------------------------------
addScreenshotSlide(
    12,
    'Financial Ledger',
    'Credit Transaction History & Real-Time Financial Ledger',
    'Audit-proof transaction ledger recording every credit top-up, admin allocation, and campaign consumption.',
    '09_credit_history.png',
    'WABA Portal • Credit Ledger & Billing Transaction History',
    [
        {
            title: 'Complete Financial Audit Trail',
            desc: 'Logs every credit movement with exact timestamp, user ID, transaction amount, and admin remarks.'
        },
        {
            title: 'Reconciliation & Billing Transparency',
            desc: 'Easily reconcile dispatched campaign numbers against wallet balance deductions for zero billing disputes.'
        },
        {
            title: 'Client-Specific Ledger Views',
            desc: 'Clients can view their own recharge histories and consumption rates, establishing complete trust.'
        },
        {
            title: 'Automated Low-Balance Alerts',
            desc: 'System notifies clients when credit reserves drop below safety thresholds to prevent campaign pauses.'
        },
        {
            title: 'Enterprise Accounting Ready',
            desc: 'Exportable financial statements compatible with corporate ERP, GST reporting, and tax accounting.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 13: ENTERPRISE USER MANAGEMENT
// -------------------------------------------------------------
addScreenshotSlide(
    13,
    'User Management',
    'Multi-Tier Enterprise User Management & Client Provisioning',
    'Role-based multi-user administration enabling agencies to provision admins, staff operators, and client sub-accounts.',
    '10_manage_user.png',
    'WABA Portal • Active Users & Role Provisioning Directory',
    [
        {
            title: '3-Tier Organizational Hierarchy',
            desc: 'Built-in Superadmin, Admin, and Client / Reseller roles structured for scalable enterprise delegation.'
        },
        {
            title: 'Creator & Reseller Attribution',
            desc: 'Tracks which administrator onboarded which client, supporting multi-branch or white-label agency setups.'
        },
        {
            title: 'Custom Daily Limit Throttling',
            desc: 'Assign individual daily dispatch limits (e.g. 5,500 / 5,000 / 500 / 20 msgs) based on client contract tier.'
        },
        {
            title: 'One-Click User Lifecycle Controls',
            desc: 'Instant user creation, password modification, permission customization, and immediate account suspension.'
        },
        {
            title: 'Multi-Tenant Data Isolation',
            desc: 'Guarantees that clients can only view and access their own contacts, chats, and campaign data.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 14: GRANULAR ROLE-BASED ACCESS CONTROL (RBAC)
// -------------------------------------------------------------
addScreenshotSlide(
    14,
    'Security & RBAC',
    'Granular Role-Based Access Control (RBAC Permission Matrix)',
    'Enterprise-grade security matrix enforcing least-privilege principles across every route, feature, and data asset.',
    '11_permissions.png',
    'WABA Portal • System Permissions & Role Defaults Matrix',
    [
        {
            title: 'Fine-Grained Module-Level Control',
            desc: 'Independent permission flags for Dashboard (`dashboard.view`), Campaigns (`messages.send`), Live Chat, and Users.'
        },
        {
            title: 'Conversational Security Enforcement',
            desc: 'Separate rights for viewing chats (`livechat.view`), replying (`livechat.reply`), and data export (`livechat.export`).'
        },
        {
            title: 'Client Data Privacy Protection',
            desc: 'Safeguards customer mobile numbers and sensitive communication records from unauthorized team members.'
        },
        {
            title: 'Custom Override Flexibility',
            desc: 'Superadmins can grant or revoke specific granular capabilities for individual users with one click.'
        },
        {
            title: 'Compliance & Audit Alignment',
            desc: 'Meets corporate IT governance standards, GDPR data-handling policies, and enterprise security mandates.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 15: NEXT-GEN RCS MESSAGING HUB
// -------------------------------------------------------------
addScreenshotSlide(
    15,
    'Next-Gen RCS SMS',
    'Next-Generation RCS Messaging Hub (Google Verified SMS)',
    'Future-ready Rich Communication Services (RCS) integration bringing branded, interactive app-like experiences to native Android SMS.',
    '12_rcs_dashboard.png',
    'WABA Portal • Google RCS Agent Dashboard & Analytics',
    [
        {
            title: 'Google Verified Business Profile',
            desc: 'Displays verified checkmark, company logo, and official description directly inside default Google Messages.'
        },
        {
            title: 'Dual-Channel Omnichannel Strategy',
            desc: 'Combines WhatsApp Business API with carrier-level RCS for 100% smartphone delivery redundancy.'
        },
        {
            title: 'Real-Time Delivery & Read Rates',
            desc: 'Dedicated analytics tracking total RCS messages, delivery confirmations, read ticks, and failure counts.'
        },
        {
            title: 'Zero App Installation Barrier',
            desc: 'Engages Android smartphone users natively without requiring them to install or open any third-party app.'
        },
        {
            title: 'Future-Proof Telco Integration',
            desc: 'Integrates with telecom RCS aggregators, keeping your business ahead of the global SMS-to-RCS evolution.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 16: RCS CAMPAIGN DISPATCHER & RICH CARDS
// -------------------------------------------------------------
addScreenshotSlide(
    16,
    'RCS Composer',
    'RCS Rich Card Campaign Composer & Interactive Action Hub',
    'Design engaging multimedia cards with suggested action chips, automated dialers, and embedded website links.',
    '13_rcs_send.png',
    'WABA Portal • Interactive RCS Broadcast Dispatcher',
    [
        {
            title: 'Interactive Carousel & Media Cards',
            desc: 'Send high-resolution visual cards featuring rich product images, descriptions, and dynamic callouts.'
        },
        {
            title: 'Suggested Quick Action Chips',
            desc: 'Empowers users with 1-tap responses such as "View Catalog", "Track Order", or "Connect with Agent".'
        },
        {
            title: 'Automated Dial & URL Triggers',
            desc: 'Native integration triggers phone dialers or opens secure web browsers with zero friction.'
        },
        {
            title: 'RCS Contact Segmentation',
            desc: 'Organize verified RCS phonebooks for targeted notifications, payment alerts, and OTP dispatches.'
        },
        {
            title: 'Smart Fallback to Standard SMS',
            desc: 'Automated fallback ensures non-RCS feature phones receive standard SMS if data is unavailable.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 17: ENTERPRISE AUTHENTICATION & LOGIN GATEWAY
// -------------------------------------------------------------
addScreenshotSlide(
    17,
    'Portal Security',
    'Secure Enterprise Authentication & Multi-Tenant Login Portal',
    'Clean, branded, and fortified gateway featuring JWT session encryption, CSRF protection, and responsive design.',
    '15_login.png',
    'WABA Portal • Secure Authentication Gateway & Brand Login',
    [
        {
            title: 'Fortified Security Architecture',
            desc: 'Bcrypt password hashing, JWT bearer tokens, and encrypted HTTP-only session cookies prevent session hijacking.'
        },
        {
            title: 'Branded Corporate Presentation',
            desc: 'Professional illustrated interface creating an immediate sense of enterprise quality and client trust.'
        },
        {
            title: 'Self-Serve Password Recovery',
            desc: 'Integrated password update and recovery workflow for authorized administrative and client users.'
        },
        {
            title: 'White-Label Branding Ready',
            desc: 'Easily custom-branded with client company logo, favicon, color scheme, and proprietary domain names.'
        },
        {
            title: 'Cross-Device Responsiveness',
            desc: 'Optimized for high-resolution desktop monitors, laptops, iPads, and mobile browser management.'
        }
    ]
);

// -------------------------------------------------------------
// SLIDE 18: TECHNICAL ARCHITECTURE & META CLOUD API
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 18, 'Technology Stack', 'Full-Stack Architecture & Meta Cloud API v25.0 Pipeline', 'Built with battle-tested enterprise technologies engineered for 99.9% uptime, scalability, and airtight security.');

    // Architecture Grid (6 Blocks)
    const techBlocks = [
        {
            layer: 'PRESENTATION LAYER',
            tech: 'Tailwind CSS • Blade • FontAwesome',
            desc: 'Lightweight, ultra-fast responsive frontend with Plus Jakarta typography and mobile-first live chat console.'
        },
        {
            layer: 'BACKEND CORE ENGINE',
            tech: 'PHP 8.2 • Laravel 11 Framework',
            desc: 'High-performance MVC architecture, artisan queue workers, middleware authentication, and RESTful routing.'
        },
        {
            layer: 'MESSAGING GATEWAY',
            tech: 'Meta Graph API v25.0 (Cloud API)',
            desc: 'Direct connection to Meta servers with zero middleman markup, high TPS throughput, and official WABA support.'
        },
        {
            layer: 'TWO-WAY WEBHOOK LISTENER',
            tech: 'Secure HTTPS Callback Handler',
            desc: 'Real-time event processing with SHA-256 webhook signature verification for instant message receipt and status ticks.'
        },
        {
            layer: 'PERSISTENCE & LEDGER',
            tech: 'MySQL Relational Database',
            desc: 'Optimized schema with indexed foreign keys, audit-proof credit transaction logs, and encrypted credentials.'
        },
        {
            layer: 'OMNICHANNEL EXTENSION',
            tech: 'Google RCS Messaging Hub',
            desc: 'Next-generation RCS agent integration delivering verified SMS cards and multi-channel communication fallback.'
        }
    ];

    techBlocks.forEach((tb, idx) => {
        const col = idx % 3;
        const row = Math.floor(idx / 3);
        const cardX = 0.6 + (col * 3.0);
        const cardY = 1.30 + (row * 1.85);

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: cardX, y: cardY, w: 2.85, h: 1.70,
            rectRadius: 0.05,
            fill: { color: C_WHITE },
            line: { color: C_BORDER, width: 1 }
        });

        // Top Header
        slide.addShape(pres.shapes.RECTANGLE, {
            x: cardX, y: cardY, w: 2.85, h: 0.32,
            fill: { color: C_SLATE_LIGHT },
            line: { color: C_BORDER, width: 0.8 }
        });

        slide.addText(tb.layer, {
            x: cardX + 0.1, y: cardY + 0.06, w: 2.65, h: 0.22,
            fontSize: 8, bold: true, color: C_EMERALD, fontFace: 'Calibri'
        });

        slide.addText(tb.tech, {
            x: cardX + 0.12, y: cardY + 0.40, w: 2.6, h: 0.38,
            fontSize: 10, bold: true, color: C_NAVY, fontFace: 'Calibri'
        });

        slide.addText(tb.desc, {
            x: cardX + 0.12, y: cardY + 0.82, w: 2.6, h: 0.78,
            fontSize: 8, color: C_SLATE_TEXT, fontFace: 'Calibri'
        });
    });
}

// -------------------------------------------------------------
// SLIDE 19: END-TO-END MESSAGE LIFECYCLE & COMPLIANCE
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 19, 'Process Workflow', 'End-to-End Message Lifecycle & Compliance Framework', 'Strict adherence to Meta guidelines, opt-in rules, and the official 24-hour customer care messaging window.');

    // 6-Step Horizontal Flow Cards
    const steps = [
        { step: '01', title: 'Trigger Event', desc: 'Campaign dispatch or inbound customer message initiates the lifecycle.' },
        { step: '02', title: 'Meta Cloud API', desc: 'Secure HTTPS JSON request transmitted to Meta Graph API v25.0 endpoint.' },
        { step: '03', title: 'Webhook Auth', desc: 'Meta validates HMAC verification challenge and dispatches status webhook.' },
        { step: '04', title: 'Route & Resolve', desc: 'Laravel engine routes event to Live Chat desk or triggers automated flow.' },
        { step: '05', title: 'Delivery Status', desc: 'Real-time receipts: Sent → Delivered (Grey) → Read (Blue double ticks).' },
        { step: '06', title: 'Ledger Audit', desc: 'Credit deducted from tenant wallet; delivery record logged into database.' }
    ];

    steps.forEach((st, idx) => {
        const cardX = 0.6 + (idx * 1.48);

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: cardX, y: 1.30, w: 1.40, h: 2.00,
            rectRadius: 0.05,
            fill: { color: C_SLATE_LIGHT },
            line: { color: C_BORDER, width: 1 }
        });

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: cardX + 0.45, y: 1.40, w: 0.50, h: 0.35,
            rectRadius: 0.05,
            fill: { color: C_EMERALD }
        });

        slide.addText(st.step, {
            x: cardX + 0.45, y: 1.42, w: 0.50, h: 0.32,
            fontSize: 11, bold: true, color: C_WHITE, align: 'center', fontFace: 'Calibri'
        });

        slide.addText(st.title, {
            x: cardX + 0.05, y: 1.85, w: 1.30, h: 0.35,
            fontSize: 9, bold: true, color: C_NAVY, align: 'center', fontFace: 'Calibri'
        });

        slide.addText(st.desc, {
            x: cardX + 0.08, y: 2.22, w: 1.24, h: 1.00,
            fontSize: 7.5, color: C_SLATE_TEXT, align: 'center', fontFace: 'Calibri'
        });
    });

    // Bottom Compliance & Policy Rules
    slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
        x: 0.6, y: 3.50, w: 8.8, h: 1.55,
        rectRadius: 0.06,
        fill: { color: C_LIGHT_GREEN },
        line: { color: 'A7F3D0', width: 1 }
    });

    slide.addText('META REGULATORY COMPLIANCE & BEST PRACTICES EMBEDDED', {
        x: 0.8, y: 3.65, w: 8.4, h: 0.25,
        fontSize: 9.5, bold: true, color: C_EMERALD, fontFace: 'Calibri'
    });

    const compliancePoints = [
        '✓ 24-Hour Messaging Window: Outbound free-form messages restricted to 24h after customer inquiry; pre-approved HSM templates used beyond.',
        '✓ Meta Opt-In Protection: Ensures all broadcast recipients have granted consent, safeguarding sender phone number tier and quality score.',
        '✓ Instant Unsubscribe / Opt-Out: Built-in block and opt-out support preventing spam reporting and maintaining enterprise brand reputation.',
        '✓ End-to-End Encryption & Security: All customer messages transmitted over encrypted TLS 1.3 channels with zero data leakage.'
    ];

    compliancePoints.forEach((cp, idx) => {
        slide.addText(cp, {
            x: 0.8, y: 3.95 + (idx * 0.26), w: 8.4, h: 0.25,
            fontSize: 8.2, color: C_NAVY, fontFace: 'Calibri'
        });
    });
}

// -------------------------------------------------------------
// SLIDE 20: INDUSTRY USE CASES & PROVEN APPLICATIONS
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 20, 'Industry Verticals', 'Versatile Industry Applications & High-Growth Use Cases', 'Tailored workflows that empower diverse sectors to automate customer engagement, sales, and support.');

    const verticals = [
        {
            sector: 'Retail & E-Commerce',
            color: '059669',
            examples: [
                '• Abandoned cart reminders with discount codes',
                '• Order confirmation & live dispatch tracking',
                '• High-res product catalog & festive flash sales'
            ]
        },
        {
            sector: 'Real Estate & Builders',
            color: '2563EB',
            examples: [
                '• Instant digital brochures & floor plan delivery',
                '• Automated site visit scheduling & reminders',
                '• Construction milestone updates & payment alerts'
            ]
        },
        {
            sector: 'Healthcare & Clinics',
            color: '0D9488',
            examples: [
                '• Doctor appointment booking & reminders',
                '• Automated digital lab test report delivery',
                '• Pre-treatment instructions & medication alerts'
            ]
        },
        {
            sector: 'Education & Coaching',
            color: '7C3AED',
            examples: [
                '• Automated admission enquiry handling & FAQs',
                '• Exam schedules, report cards & parent alerts',
                '• 1-Click fee collection reminders with pay links'
            ]
        },
        {
            sector: 'BFSI & Municipal Services',
            color: 'D97706',
            examples: [
                '• Property tax bills, challans & payment receipts',
                '• EMI payment alerts & loan approval notifications',
                '• Secure account balance & transaction inquiries'
            ]
        }
    ];

    verticals.forEach((v, idx) => {
        const cardX = 0.6 + (idx * 1.80);

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: cardX, y: 1.30, w: 1.72, h: 3.75,
            rectRadius: 0.05,
            fill: { color: C_WHITE },
            line: { color: C_BORDER, width: 1 }
        });

        // Top Sector Badge
        slide.addShape(pres.shapes.RECTANGLE, {
            x: cardX, y: 1.30, w: 1.72, h: 0.65,
            fill: { color: v.color }
        });

        slide.addText(v.sector, {
            x: cardX + 0.08, y: 1.40, w: 1.56, h: 0.45,
            fontSize: 9.5, bold: true, color: C_WHITE, align: 'center', fontFace: 'Calibri'
        });

        // Use Case Bullet points
        v.examples.forEach((ex, eIdx) => {
            const ey = 2.15 + (eIdx * 0.90);
            slide.addText(ex, {
                x: cardX + 0.10, y: ey, w: 1.52, h: 0.85,
                fontSize: 8, color: C_SLATE_TEXT, fontFace: 'Calibri'
            });
        });
    });
}

// -------------------------------------------------------------
// SLIDE 21: IMPLEMENTATION ROADMAP & TESTING PROTOCOL
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 21, 'Project Execution', 'Structured 4-Week Implementation Roadmap & QA Protocol', 'A transparent, milestone-driven rollout plan ensuring zero downtime, rigorous quality assurance, and seamless onboarding.');

    const phases = [
        {
            week: 'WEEK 1',
            title: 'Meta Onboarding & Discovery',
            days: 'Days 1 – 7',
            tasks: [
                'Meta Business Portfolio verification assistance',
                'WhatsApp Business Account (WABA) setup',
                'Dedicated phone number registration & display name review',
                'Requirement finalization & initial template inventory'
            ]
        },
        {
            week: 'WEEK 2',
            title: 'Platform Deployment & Templates',
            days: 'Days 8 – 14',
            tasks: [
                'Server setup, SSL configuration & Laravel installation',
                'Meta Cloud API & webhook endpoint configuration',
                'Creation & submission of custom HSM templates to Meta',
                'Database schema setup & initial user role provisioning'
            ]
        },
        {
            week: 'WEEK 3',
            title: 'End-to-End QA & System Testing',
            days: 'Days 15 – 21',
            tasks: [
                'Inbound & outbound message load testing',
                'Live chat 2-way latency & quick chip response validation',
                'Bulk broadcast stress test & delivery report audits',
                'Credit wallet debit & daily limit throttling verification'
            ]
        },
        {
            week: 'WEEK 4',
            title: 'Go-Live & Staff Handover',
            days: 'Days 22 – 28',
            tasks: [
                'Administrator & agent live walkthrough training',
                'Customer phonebook import & demo campaign launch',
                'Official commercial go-live sign-off',
                'Commencement of 30-Day dedicated hypercare warranty'
            ]
        }
    ];

    phases.forEach((ph, idx) => {
        const cardX = 0.6 + (idx * 2.25);

        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: cardX, y: 1.30, w: 2.15, h: 3.75,
            rectRadius: 0.05,
            fill: { color: C_WHITE },
            line: { color: C_BORDER, width: 1 }
        });

        // Top Accent
        slide.addShape(pres.shapes.RECTANGLE, {
            x: cardX, y: 1.30, w: 2.15, h: 0.85,
            fill: { color: C_SLATE_DARK }
        });

        slide.addText(ph.week, {
            x: cardX + 0.12, y: 1.38, w: 1.9, h: 0.25,
            fontSize: 10, bold: true, color: C_VIBRANT, fontFace: 'Calibri'
        });

        slide.addText(ph.title, {
            x: cardX + 0.12, y: 1.62, w: 1.9, h: 0.45,
            fontSize: 9, bold: true, color: C_WHITE, fontFace: 'Calibri'
        });

        slide.addText(ph.days, {
            x: cardX + 0.12, y: 2.22, w: 1.9, h: 0.22,
            fontSize: 7.8, bold: true, color: C_EMERALD, fontFace: 'Calibri'
        });

        // Tasks
        ph.tasks.forEach((t, tIdx) => {
            const ty = 2.50 + (tIdx * 0.62);
            slide.addText(`•  ${t}`, {
                x: cardX + 0.10, y: ty, w: 1.95, h: 0.58,
                fontSize: 7.8, color: C_SLATE_TEXT, fontFace: 'Calibri'
            });
        });
    });
}

// -------------------------------------------------------------
// SLIDE 22: COMMERCIAL PROPOSAL, MILESTONES & ACCEPTANCE
// -------------------------------------------------------------
{
    const slide = pres.addSlide();
    applyStandardLayout(slide, 22, 'Commercial Terms', 'Commercial Proposal, Investment Schedule & Next Steps', 'A clear, transparent investment schedule backed by dedicated development and post-launch technical support.');

    // Left Column: Scope & Investment Table
    slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
        x: 0.6, y: 1.25, w: 5.2, h: 3.85,
        rectRadius: 0.05,
        fill: { color: C_WHITE },
        line: { color: C_BORDER, width: 1 }
    });

    slide.addShape(pres.shapes.RECTANGLE, {
        x: 0.6, y: 1.25, w: 5.2, h: 0.35,
        fill: { color: C_NAVY }
    });

    slide.addText('PROJECT DELIVERABLES & MODULE BREAKDOWN', {
        x: 0.8, y: 1.30, w: 4.8, h: 0.25,
        fontSize: 8.5, bold: true, color: C_WHITE, fontFace: 'Calibri'
    });

    const deliverables = [
        { mod: 'Meta WhatsApp Cloud API Integration', desc: 'WABA setup, phone number binding & secure webhook callbacks' },
        { mod: 'Bulk Broadcasting & Scheduler Studio', desc: 'Campaign composer, dynamic variables, CTA buttons & Excel importer' },
        { mod: 'Omnichannel Live Chat Desk', desc: 'WhatsApp Web console, quick reply chips, message forwarding & export' },
        { mod: 'Meta Template Management Hub', desc: 'Cloud API template synchronization, rich banner previews & submission' },
        { mod: 'Multi-Tenant Credit & User System', desc: 'Prepaid wallet, per-message debit, RBAC matrix & client provisioning' },
        { mod: 'Next-Gen Google RCS Hub & Audits', desc: 'Verified RCS dashboard, rich card creator & 30-day delivery audit logs' },
        { mod: 'Testing, Deployment & Handover', desc: 'Production server deployment, staff walkthrough & 30 days warranty' }
    ];

    deliverables.forEach((d, idx) => {
        const dy = 1.65 + (idx * 0.44);
        slide.addShape(pres.shapes.LINE, {
            x: 0.6, y: dy, w: 5.2, h: 0,
            line: { color: C_BORDER, width: 0.6 }
        });

        slide.addText(`✓  ${d.mod}`, {
            x: 0.75, y: dy + 0.03, w: 4.8, h: 0.20,
            fontSize: 8.2, bold: true, color: C_NAVY, fontFace: 'Calibri'
        });
        slide.addText(d.desc, {
            x: 0.95, y: dy + 0.21, w: 4.6, h: 0.20,
            fontSize: 7.5, color: C_SLATE_MUTED, fontFace: 'Calibri'
        });
    });

    // Right Column: Payment Milestones & Sign-off Box
    slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
        x: 6.0, y: 1.25, w: 3.4, h: 3.85,
        rectRadius: 0.05,
        fill: { color: C_SLATE_LIGHT },
        line: { color: C_BORDER, width: 1 }
    });

    slide.addShape(pres.shapes.RECTANGLE, {
        x: 6.0, y: 1.25, w: 3.4, h: 0.35,
        fill: { color: C_EMERALD }
    });

    slide.addText('PAYMENT MILESTONES & NEXT STEPS', {
        x: 6.15, y: 1.30, w: 3.1, h: 0.25,
        fontSize: 8.5, bold: true, color: C_WHITE, fontFace: 'Calibri'
    });

    const milestones = [
        { pct: '40%', stage: 'Advance on Project Kickoff', detail: 'On proposal approval & Meta Business setup' },
        { pct: '30%', stage: 'Core Deployment & Integration', detail: 'After Cloud API, Live Chat & Broadcaster setup' },
        { pct: '30%', stage: 'Final Testing & Live Handover', detail: 'On live message verification & admin training' }
    ];

    milestones.forEach((m, mIdx) => {
        const my = 1.70 + (mIdx * 0.65);
        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            x: 6.15, y: my, w: 3.1, h: 0.58,
            rectRadius: 0.04,
            fill: { color: C_WHITE },
            line: { color: C_BORDER, width: 0.6 }
        });

        slide.addText(m.pct, {
            x: 6.25, y: my + 0.08, w: 0.65, h: 0.40,
            fontSize: 14, bold: true, color: C_EMERALD, fontFace: 'Calibri'
        });

        slide.addText(m.stage, {
            x: 6.95, y: my + 0.06, w: 2.2, h: 0.22,
            fontSize: 8.5, bold: true, color: C_NAVY, fontFace: 'Calibri'
        });

        slide.addText(m.detail, {
            x: 6.95, y: my + 0.28, w: 2.2, h: 0.25,
            fontSize: 7.5, color: C_SLATE_MUTED, fontFace: 'Calibri'
        });
    });

    // Acceptance Box
    slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
        x: 6.15, y: 3.75, w: 3.1, h: 1.25,
        rectRadius: 0.04,
        fill: { color: C_WHITE },
        line: { color: C_BORDER, width: 0.8 }
    });

    slide.addText('PROPOSAL ACCEPTANCE & CONTACT', {
        x: 6.25, y: 3.82, w: 2.9, h: 0.20,
        fontSize: 8, bold: true, color: C_NAVY, fontFace: 'Calibri'
    });

    slide.addText('STM Developments • Technology Partner\nEmail: contact@stmdevelopments.com\nWeb: https://stmdevelopments.com\nAuthorized Signatory: ________________________', {
        x: 6.25, y: 4.05, w: 2.9, h: 0.85,
        fontSize: 7.5, color: C_SLATE_TEXT, fontFace: 'Calibri'
    });
}

// -------------------------------------------------------------
// WRITE OUTPUT PPTX
// -------------------------------------------------------------
const file1 = path.join(OUTPUT_DIR_1, OUTPUT_FILENAME);
const file2 = path.join(OUTPUT_DIR_2, OUTPUT_FILENAME);

pres.writeFile({ fileName: file1 })
    .then(() => {
        console.log("Successfully generated Proposal PPTX at:", file1);
        fs.copyFileSync(file1, file2);
        console.log("Copied proposal PPTX to root Proposal directory:", file2);
    })
    .catch((err) => {
        console.error("Error creating PPTX:", err);
    });
