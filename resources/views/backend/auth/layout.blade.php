<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Admin Login' }} — {{ $softwareName ?? 'Hospital Management Software' }}</title>
    <link rel="icon" href="{{ asset('backend/assets/images/favicon.svg') }}" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --auth-primary: #0b6e6e;
            --auth-primary-dark: #085656;
            --auth-accent: #14b8a6;
            --auth-bg: #f4f8fb;
            --auth-card: #ffffff;
            --auth-text: #1e293b;
            --auth-muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--auth-bg);
            color: var(--auth-text);
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 10% 15%, rgba(20, 184, 166, 0.12), transparent 28%),
                radial-gradient(circle at 90% 85%, rgba(11, 110, 110, 0.1), transparent 24%),
                var(--auth-bg);
        }

        .auth-shell {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--auth-card);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .auth-brand-panel {
            background: linear-gradient(160deg, var(--auth-primary) 0%, var(--auth-accent) 100%);
            color: #fff;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .auth-brand-panel::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -60px;
            right: -60px;
        }

        .auth-brand-panel::after {
            content: "";
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            bottom: -40px;
            left: -30px;
        }

        .brand-content { position: relative; z-index: 1; }

        .brand-logo-wrap {
            width: 96px;
            height: 96px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
        }

        .brand-logo-wrap i {
            font-size: 2.8rem;
            color: #ffffff;
            filter: drop-shadow(0 4px 10px rgba(15, 23, 42, 0.18));
        }

        .brand-title {
            font-size: 1.55rem;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .brand-hospital-name {
            display: inline-block;
            margin-bottom: 14px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .brand-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.95rem;
        }

        .brand-features i {
            width: 18px;
            text-align: center;
        }

        .auth-form-panel {
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .auth-form-subtitle {
            color: var(--auth-muted);
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.72rem 0.95rem;
        }

        .form-control:focus {
            border-color: var(--auth-accent);
            box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.15);
        }

        .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .password-toggle-btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-auth {
            background: linear-gradient(135deg, var(--auth-primary), var(--auth-accent));
            border: none;
            color: #fff;
            border-radius: 10px;
            padding: 0.78rem 1rem;
            font-weight: 600;
            width: 100%;
        }

        .btn-auth:hover,
        .btn-auth:focus {
            background: linear-gradient(135deg, var(--auth-primary-dark), #0d9488);
            color: #fff;
        }

        .btn-auth:disabled {
            opacity: 0.75;
        }

        .auth-footer-note {
            margin-top: 24px;
            text-align: center;
            color: var(--auth-muted);
            font-size: 0.85rem;
        }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
                max-width: 480px;
            }

            .auth-brand-panel {
                padding: 32px 28px;
            }

            .brand-features { display: none; }

            .auth-form-panel {
                padding: 32px 28px 36px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-page">
        <div class="auth-shell">
            <div class="auth-brand-panel">
                <div class="brand-content">
                    <div class="brand-logo-wrap" aria-hidden="true">
                        <i class="fas fa-hospital-alt"></i>
                    </div>
                    <div class="brand-title">{{ $softwareName ?? 'Hospital Management Software' }}</div>
                    @if(!empty($companyName))
                        <div class="brand-hospital-name">{{ $companyName }}</div>
                    @endif
                    <p class="brand-subtitle">
                        Secure admin panel for hospital operations, billing, patients, pharmacy, and staff management.
                    </p>
                    <ul class="brand-features">
                        <li><i class="fas fa-check-circle"></i> Patient & billing management</li>
                        <li><i class="fas fa-check-circle"></i> Lab, pharmacy & admission</li>
                        <li><i class="fas fa-check-circle"></i> Staff attendance & salary</li>
                    </ul>
                </div>
            </div>

            <div class="auth-form-panel">
                @yield('auth-content')

                <div class="auth-footer-note">
                    &copy; {{ date('Y') }} {{ $softwareName ?? 'Hospital Management Software' }}@if(!empty($companyName)) — {{ $companyName }}@endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
