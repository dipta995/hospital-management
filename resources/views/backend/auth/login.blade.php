<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Diagnostic General Hospital — Admin Login</title>
    <!-- plugins:css -->
    @include('backend.layouts.partials.style')

    <style>
        body.authentication-bg {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #e2f5ff 0, #f7fbff 40%, #ffffff 100%);
            background-image: radial-gradient(circle at top left, rgba(14, 165, 233, 0.14) 0%, transparent 26%),
                              radial-gradient(circle at right bottom, rgba(15, 118, 110, 0.12) 0%, transparent 22%);
        }

        .auth-card {
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(14, 165, 233, 0.15);
            overflow: hidden;
        }

        .auth-logo {
            color: #066f68;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .auth-hero {
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.6rem;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.96), rgba(16, 185, 129, 0.95));
            color: #ffffff;
            overflow: hidden;
            position: relative;
        }

        .auth-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: -10%;
            width: 260px;
            height: 260px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
        }

        .auth-hero .hero-heading {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .auth-hero .hero-text {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
        }

        .auth-hero .hero-logo {
            max-width: 160px;
            display: block;
            margin-bottom: 1rem;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #0f766e;
            margin-bottom: 0.75rem;
        }

        .auth-brand .brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(14, 165, 233, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            font-size: 1.2rem;
        }

        .auth-title {
            color: #0f172a;
            letter-spacing: 0.03em;
        }

        .authentication-form .form-label {
            font-weight: 600;
            color: #334155;
        }

        .authentication-form .form-control {
            border-radius: 999px;
            padding-left: 1.1rem;
            padding-right: 1.1rem;
            border: 1px solid rgba(148, 163, 184, 0.35);
        }

        .authentication-form .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .authentication-form .password-toggle-btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .authentication-form .btn-primary {
            border-radius: 999px;
            background: linear-gradient(135deg, #0f766e, #22c55e);
            border: none;
            box-shadow: 0 12px 22px rgba(34, 197, 94, 0.18);
        }

        .authentication-form .btn-primary:hover {
            background: linear-gradient(135deg, #115e59, #16a34a);
        }

        .authentication-form .btn-primary:disabled {
            opacity: 0.8;
            cursor: not-allowed;
        }

        .auth-card .card-body {
            position: relative;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background: rgba(14, 165, 233, 0.08);
            border-bottom-left-radius: 120px;
        }

        .auth-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 100px;
            background: rgba(34, 197, 94, 0.08);
            border-top-right-radius: 100px;
        }
    </style>
</head>

<body class="authentication-bg">

<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5">
                <div class="card auth-card">
                    <div class="card-body px-3 py-5">
                        <div class="auth-hero text-center">
                            <img src="{{ asset('backend/assets/images/logo-light-full.png') }}" alt="Diagnostic General Hospital" class="hero-logo">
                            <div class="hero-heading">Diagnostic General Hospital</div>
                            <div class="hero-text">Secure admin access for hospital management, billing, patients, and staff.</div>
                            <div><i class="fas fa-hospital-symbol fa-2x"></i></div>
                        </div>

                        <h2 class="fw-bold text-center fs-18 auth-title">Admin Sign In</h2>

                        <div>
                            <h4 class="error-message text-danger text-center mt-1 mb-4"></h4>
                            <h4 class="success-message text-success text-center mt-1 mb-4"></h4>
                        </div>

                        <div class="px-4">
                            <form  id="data-insert" method="post" action="{{ route('admin.login.submit') }}" class="authentication-form">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email">
                                    <x-input-error  :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" id="password" name="password" class="form-control input" placeholder="Enter your password">
                                        <button class="btn btn-outline-secondary password-toggle-btn" type="button" id="password-toggle" aria-label="Show password">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                    </div>
                                </div>

                                <div class="mb-1 text-center d-grid">
                                    <button class="btn btn-primary submit" type="submit">Sign In</button>
                                </div>
                            </form>


                        </div> <!-- end col -->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->


            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>
</div>

@include('backend.layouts.partials.script')

<script>
    $(function () {
        // Password visibility toggle
        $('#password-toggle').on('click', function () {
            const $passwordInput = $('#password');
            const $icon = $(this).find('i');
            const isPassword = $passwordInput.attr('type') === 'password';

            $passwordInput.attr('type', isPassword ? 'text' : 'password');
            $(this).attr('aria-label', isPassword ? 'Hide password' : 'Show password');
            $icon.toggleClass('fa-eye fa-eye-slash');
        });

        // Disable submit button for 5 seconds after click
        $('.authentication-form').on('submit', function () {
            const $button = $(this).find('button[type="submit"]');

            if ($button.prop('disabled')) {
                return false;
            }

            const originalText = $button.text();
            $button.data('original-text', originalText);
            $button.prop('disabled', true).text('Please wait...');

            setTimeout(function () {
                $button.prop('disabled', false).text($button.data('original-text'));
            }, 5000);
        });
    });
</script>

<!-- endinject -->
</body>

</html>
