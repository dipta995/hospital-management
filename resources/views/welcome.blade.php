<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hospital Management Software</title>
    <link rel="icon" href="{{ asset('backend/assets/images/favicon.svg') }}" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #0b6e6e;
            --brand-primary-dark: #085656;
            --brand-accent: #14b8a6;
            --brand-surface: #f4f8fb;
            --brand-text: #0f172a;
            --brand-muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--brand-surface);
            color: var(--brand-text);
        }

        .landing-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--brand-text);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .brand-text-wrap strong {
            display: block;
            font-size: 1rem;
            line-height: 1.2;
        }

        .brand-text-wrap small {
            color: var(--brand-muted);
            font-size: 0.78rem;
        }

        .hero-section {
            padding: 72px 0 56px;
            background:
                radial-gradient(circle at 15% 20%, rgba(20, 184, 166, 0.16), transparent 28%),
                radial-gradient(circle at 85% 10%, rgba(11, 110, 110, 0.12), transparent 24%),
                linear-gradient(180deg, #ffffff 0%, var(--brand-surface) 100%);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #ecfeff;
            border: 1px solid #99f6e4;
            color: var(--brand-primary);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .hero-subtitle {
            font-size: 1.08rem;
            color: var(--brand-muted);
            max-width: 620px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.8rem 1.6rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 12px 28px rgba(11, 110, 110, 0.22);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-brand:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(11, 110, 110, 0.28);
        }

        .btn-outline-brand {
            border: 1px solid #cbd5e1;
            color: var(--brand-text);
            background: #fff;
            font-weight: 600;
            padding: 0.8rem 1.6rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-brand:hover {
            color: var(--brand-primary);
            border-color: #99f6e4;
            background: #f0fdfa;
        }

        .hero-panel {
            background: linear-gradient(160deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            color: #fff;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 24px 50px rgba(11, 110, 110, 0.22);
            position: relative;
            overflow: hidden;
        }

        .hero-panel::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -80px;
            right: -60px;
        }

        .hero-panel-content { position: relative; z-index: 1; }

        .hero-stat {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .hero-stat strong {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 2px;
        }

        .hero-stat span {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .section-title {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .section-subtitle {
            color: var(--brand-muted);
            margin-bottom: 28px;
        }

        .feature-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            font-size: 1.2rem;
        }

        .feature-icon.teal { background: #ecfeff; color: #0f766e; }
        .feature-icon.blue { background: #dbeafe; color: #2563eb; }
        .feature-icon.green { background: #dcfce7; color: #16a34a; }
        .feature-icon.amber { background: #fef3c7; color: #d97706; }
        .feature-icon.purple { background: #ede9fe; color: #7c3aed; }
        .feature-icon.rose { background: #ffe4e6; color: #e11d48; }

        .support-section {
            padding: 56px 0;
        }

        .support-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 28px;
            height: 100%;
        }

        .support-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .support-item:last-child { border-bottom: none; }

        .support-item i {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ecfeff;
            color: #0f766e;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .landing-footer {
            background: #0f172a;
            color: #cbd5e1;
            padding: 24px 0;
            font-size: 0.9rem;
        }

        .landing-footer a {
            color: #99f6e4;
            text-decoration: none;
        }
    </style>
</head>
<body>
    @php
        $companyName = \App\Models\Setting::getGuest('company_name');
    @endphp

    <nav class="landing-nav py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="brand-mark">
                <span class="brand-icon"><i class="fas fa-hospital-alt"></i></span>
                <span class="brand-text-wrap">
                    <strong>Hospital Management Software</strong>
                    @if($companyName)
                        <small>{{ $companyName }}</small>
                    @else
                        <small>Complete hospital operations platform</small>
                    @endif
                </span>
            </a>
            <div class="d-flex gap-2">
                @auth('admin')
                    <a href="{{ route('admin.home') }}" class="btn-brand btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn-brand btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Admin Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="fas fa-shield-heart"></i>
                        Trusted hospital management platform
                    </div>
                    <h1 class="hero-title">
                        Manage your hospital with confidence and clarity
                    </h1>
                    <p class="hero-subtitle">
                        One secure system for patients, billing, laboratory, pharmacy, admissions,
                        employee attendance, salary, and daily financial reporting.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @auth('admin')
                            <a href="{{ route('admin.home') }}" class="btn-brand">
                                <i class="fas fa-gauge-high"></i> Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('admin.login') }}" class="btn-brand">
                                <i class="fas fa-right-to-bracket"></i> Login to Admin Panel
                            </a>
                        @endauth
                        <a href="https://wa.me/8801632315608" target="_blank" rel="noopener" class="btn-outline-brand">
                            <i class="fab fa-whatsapp"></i> Contact Support
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-panel">
                        <div class="hero-panel-content">
                            <h3 class="h4 mb-3">Everything in one place</h3>
                            <div class="hero-stat">
                                <strong>Patient & Billing</strong>
                                <span>Invoices, receipts, due collection, and reports</span>
                            </div>
                            <div class="hero-stat">
                                <strong>Clinical Operations</strong>
                                <span>Lab, pharmacy, admission, and doctor serials</span>
                            </div>
                            <div class="hero-stat">
                                <strong>HR & Finance</strong>
                                <span>Attendance, salary sheet, costs, and dashboards</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Core Modules</h2>
            <p class="section-subtitle text-center">Designed for hospitals, clinics, and diagnostic centers</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon teal"><i class="fas fa-user-injured"></i></div>
                        <h5>Patient Management</h5>
                        <p class="text-muted mb-0">Register patients, manage records, and track visits efficiently.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon blue"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h5>Billing & Invoices</h5>
                        <p class="text-muted mb-0">Create invoices, manage dues, payments, and printable reports.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon green"><i class="fas fa-flask"></i></div>
                        <h5>Laboratory</h5>
                        <p class="text-muted mb-0">Handle test reports, lab workflows, and result delivery.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon amber"><i class="fas fa-pills"></i></div>
                        <h5>Pharmacy</h5>
                        <p class="text-muted mb-0">Manage products, purchases, sales, and inventory control.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon purple"><i class="fas fa-bed-pulse"></i></div>
                        <h5>Admissions</h5>
                        <p class="text-muted mb-0">Track admitted patients, cabins, and hospital stays.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon rose"><i class="fas fa-users-gear"></i></div>
                        <h5>HR & Attendance</h5>
                        <p class="text-muted mb-0">Employee schedules, leave days, attendance, and salary sheets.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="support-section bg-white border-top">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <h2 class="section-title">Need help getting started?</h2>
                    <p class="section-subtitle mb-0">
                        Our support team can help with installation, training, customization, and technical issues.
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="support-card">
                        <div class="support-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <small class="text-muted d-block">Phone / WhatsApp</small>
                                <a href="tel:+8801632315608" class="fw-semibold text-decoration-none text-dark">+88 01632315608</a>
                            </div>
                        </div>
                        <div class="support-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:info@dreammake-soft.com" class="fw-semibold text-decoration-none text-dark">info@dreammake-soft.com</a>
                            </div>
                        </div>
                        <div class="support-item">
                            <i class="fas fa-globe"></i>
                            <div>
                                <small class="text-muted d-block">Website</small>
                                <a href="https://dreammake-soft.com" target="_blank" rel="noopener" class="fw-semibold text-decoration-none text-dark">dreammake-soft.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="landing-footer">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} Hospital Management Software{{ $companyName ? ' — ' . $companyName : '' }}</span>
            <span>Powered by <a href="https://dreammake-soft.com" target="_blank" rel="noopener">DreamMake Soft</a></span>
        </div>
    </footer>
</body>
</html>
