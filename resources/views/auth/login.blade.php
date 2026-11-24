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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --bg-left: #e3e5e8;
            --bg-left-gradient: linear-gradient(135deg, #e4e6ea 0%, #dadde2 100%);
            --bg-right: #ffffff;
            --text-dark: #0f172a;
            --text-heading: #1b3846;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --primary-green: #25D366;
            --primary-dark: #0f172a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: var(--font-family);
            background-color: var(--bg-right);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }

        /* LEFT SIDE - HERO BANNER */
        .left-banner {
            flex: 1;
            background: var(--bg-left-gradient);
            padding: 48px 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            max-width: 48%;
        }

        /* User Logo Container */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .brand-logo-img {
            height: 52px;
            width: 52px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border: 2px solid #ffffff;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-green);
            letter-spacing: -0.03em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-name span.badge-green {
            color: black;
        }

        /* Banner Heading */
        .banner-heading {
            margin-top: 8px;
            max-width: 460px;
        }

        .banner-heading h1 {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.25;
            color: var(--text-heading);
            letter-spacing: -0.02em;
        }

        /* Illustration Graphic */
        .illustration-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
            position: relative;
        }

        .illustration-img {
            max-width: 90%;
            max-height: 460px;
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.08));
            transition: transform 0.4s ease;
        }

        .illustration-container:hover .illustration-img {
            transform: translateY(-4px);
        }

        /* RIGHT SIDE - FORM CONTAINER */
        .right-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            background-color: var(--bg-right);
        }

        .form-card {
            width: 100%;
            max-width: 440px;
            padding: 10px;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .eyebrow-text {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #8894a4;
            margin-bottom: 8px;
            display: block;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-green);
            letter-spacing: -0.03em;
        }

        /* Alert error */
        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Input Controls */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            height: 52px;
            padding: 0 18px;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-family: var(--font-family);
            font-size: 14.5px;
            font-weight: 500;
            color: var(--text-dark);
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-control:focus {
            border-color: var(--text-dark);
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.06);
        }

        /* Password Field Specifics */
        .password-toggle-btn {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 8px;
            font-size: 15px;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: var(--text-dark);
        }

        .form-control.has-toggle {
            padding-right: 48px;
        }

        /* Action Buttons */
        .btn-submit {
            width: 100%;
            height: 50px;
            background-color: #336015;
    color: #ffffff;
            border: none;
            border-radius: 12px;
            font-family: var(--font-family);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .btn-submit:hover, .btn-submit:focus {
            background-color: #0f172a;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Footer Links */
        .form-footer {
            text-align: center;
            margin-top: 24px;
        }

        .forgot-link {
            color: #475569;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #0f172a;
            text-decoration: underline;
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .left-banner {
                max-width: 42%;
                padding: 36px 32px;
            }
            .banner-heading h1 {
                font-size: 26px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }

            .left-banner {
                max-width: 100%;
                padding: 32px 24px;
                min-height: auto;
            }

            .banner-heading h1 {
                font-size: 24px;
            }

            .illustration-container {
                display: none;
            }

            .right-form-section {
                padding: 36px 24px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- LEFT BANNER SECTION -->
    <div class="left-banner">
        <div>
            <!-- User Brand Logo Container -->
            <div class="logo-container">
                <img src="{{ asset($assetPrefix . 'images/WABA.jpeg') }}" alt="Brand Logo" class="brand-logo-img">
                <div class="brand-name">
                    WhatsApp <span class="badge-green">Dashboard</span>
                </div>
            </div>

            <!-- Custom Project Headline -->
            <div class="banner-heading">
                <h1>Streamline & Automate Your WhatsApp Campaign</h1>
            </div>
        </div>

        <!-- Illustration Image -->
        <div class="illustration-container">
            <img src="{{ asset($assetPrefix . 'images/login-illustration.jpg') }}" alt="WhatsApp Campaign Illustration" class="illustration-img">
        </div>
    </div>

    <!-- RIGHT FORM SECTION -->
    <div class="right-form-section">
        <div class="form-card">
            <!-- Header -->
            <div class="form-header">
                <span class="eyebrow-text">WELCOME BACK</span>
                <h2><span style="color: black;">Login</span> WhatsApp <span style="color: black;">Dashboard</span></h2>
            </div>

            <!-- Validation Error Alert -->
            @if ($errors->any())
                <div class="alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login.submit') }}">
             @csrf

                <!-- Username / Email Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="Username / Email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control has-toggle" 
                            placeholder="Password" 
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Show / Hide Password" aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    <span>Continue</span>
                </button>
            </form>

            <!-- Footer Links -->
            <div class="form-footer">
                <a href="#" class="forgot-link" onclick="alert('Please contact your administrator to reset your password.'); return false;">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                
                if (isPassword) {
                    toggleIcon.classList.remove('fa-eye-slash', 'fa-regular');
                    toggleIcon.classList.add('fa-eye', 'fa-solid');
                } else {
                    toggleIcon.classList.remove('fa-eye', 'fa-solid');
                    toggleIcon.classList.add('fa-eye-slash', 'fa-regular');
                }
            });
        }
    });
</script>

</body>
</html>
