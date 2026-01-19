<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hospital Management System</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <style>
        :root {
            --brand-primary: #0f766e;
            --brand-primary-dark: #115e59;
            --brand-accent: #0ea5e9;
            --brand-bg: #e0f2fe;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, #e0f2fe 0, #ecfeff 40%, #f9fafb 100%);
            color: #020617;
        }

        .welcome-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
        }

        .welcome-layout {
            display: flex;
            flex-direction: column;
            gap: 24px;
            max-width: 960px;
            width: 100%;
        }

        .welcome-card,
        .support-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
            padding: 26px 30px;
            border: 1px solid rgba(148, 163, 184, 0.25);
        }

        .welcome-title {
            font-size: 2.1rem;
            margin: 0 0 12px;
            color: #0f172a;
        }

        .welcome-subtitle {
            font-size: 1rem;
            margin: 0 0 10px;
            color: #1f2937;
        }

        .welcome-muted {
            font-size: 0.95rem;
            color: #4b5563;
            margin-bottom: 24px;
        }

        .welcome-contact {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .welcome-contact span,
        .welcome-contact a {
            color: inherit;
            text-decoration: none;
        }

        .welcome-contact a:hover {
            text-decoration: underline;
        }

        .primary-button {
            display: inline-block;
            padding: 0.75rem 2.5rem;
            border-radius: 999px;
            border: none;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.02em;
            cursor: pointer;
            background: linear-gradient(135deg, #0f766e, #0ea5e9);
            color: #ffffff;
            box-shadow: 0 16px 36px rgba(14, 165, 233, 0.45);
            transition: transform 0.1s ease, box-shadow 0.1s ease, background 0.1s ease;
        }

        .primary-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 46px rgba(14, 165, 233, 0.65);
            background: linear-gradient(135deg, #0d9488, #0ea5e9);
        }

        .primary-button:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(15, 118, 110, 0.25);
        }

        .support-title {
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0 0 8px;
            color: #0f172a;
        }

        .support-caption {
            font-size: 0.9rem;
            color: #6b7280;
            margin: 0 0 16px;
        }

        .support-item-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .support-phone {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
            text-decoration: none;
        }

        .support-phone:hover {
            text-decoration: underline;
        }

        .support-actions {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .support-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
            background: rgba(14, 165, 233, 0.12);
            color: #0284c7;
            text-decoration: none;
        }

        .support-chip span {
            margin-left: 6px;
        }

        .support-note {
            margin-top: 10px;
            font-size: 0.8rem;
            color: #6b7280;
        }

        @media (min-width: 768px) {
            .welcome-layout {
                flex-direction: row;
                align-items: stretch;
            }

            .welcome-card {
                flex: 2;
            }

            .support-card {
                flex: 1;
                max-width: 320px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-wrapper">
        <div class="welcome-layout">
            <div class="welcome-card">
                <h1 class="welcome-title">Welcome to Hospital Management System</h1>
                <p class="welcome-subtitle">A complete solution for managing your hospital operations.</p>
                <p class="welcome-muted">
                    Sign in to manage patients, doctors, appointments, billing, pharmacy,
                    laboratory reports, and more from a single secure dashboard.
                </p>

                <div class="welcome-contact">
                    <span>📞 +88 01632315608</span>
                    <a href="mailto:info@dreammake-soft.com">📧 info@dreammake-soft.com</a>
                    <a href="https://dreammake-soft.com" target="_blank" rel="noopener">🌐 https://dreammake-soft.com</a>
                </div>

                @auth('admin')
                    <a href="{{ route('admin.home') }}" class="primary-button">Go to Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="primary-button">Login to Admin Panel</a>
                @endauth
            </div>

            <aside class="support-card">
                <p class="support-title">Need support?</p>
                <p class="support-caption">For setup, training, or technical issues, contact our support team anytime.</p>

                <div style="margin-bottom: 12px;">
                    <div class="support-item-label">Support &amp; WhatsApp</div>
                    <a href="tel:+8801632315608" class="support-phone">+88 01632315608</a>
                </div>

                <div style="margin-bottom: 12px;">
                    <div class="support-item-label">Email</div>
                    <a href="mailto:info@dreammake-soft.com" class="support-phone">info@dreammake-soft.com</a>
                </div>

                <div style="margin-bottom: 8px;">
                    <div class="support-item-label">Website</div>
                    <a href="https://dreammake-soft.com" target="_blank" rel="noopener" class="support-phone">https://dreammake-soft.com</a>
                </div>

                <div class="support-actions">
                    <a href="https://wa.me/8801632315608" target="_blank" rel="noopener" class="support-chip">
                        💬<span>Chat on WhatsApp</span>
                    </a>
                </div>

                <p class="support-note">Phone and WhatsApp are available using this same number.</p>
            </aside>
        </div>
    </div>
</body>
</html>
