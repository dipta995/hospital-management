<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Diagnostic General Hospital — Change Password</title>
    <!-- plugins:css -->
    @include('backend.layouts.partials.style')

    <style>
        body.authentication-bg {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #eff6ff 0, #ffffff 50%, #f8fafc 100%);
            background-image: radial-gradient(circle at top left, rgba(14, 165, 233, 0.1) 0%, transparent 28%),
                              radial-gradient(circle at right bottom, rgba(16, 185, 129, 0.1) 0%, transparent 22%);
        }

        .auth-card {
            border-radius: 22px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.12);
        }

        .auth-card .auth-hero {
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.6rem;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.96), rgba(16, 185, 129, 0.95));
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .auth-card .auth-hero::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: -10px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .auth-card .hero-heading {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .auth-card .hero-text {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
        }

        .auth-card .hero-logo {
            max-width: 150px;
            display: block;
            margin-bottom: 1rem;
        }

        .auth-logo {
            color: #0f766e;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: #0f766e;
            margin-bottom: 0.8rem;
        }

        .auth-brand .brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: rgba(14, 165, 233, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            font-size: 1.1rem;
        }

        .authentication-form .form-label {
            font-weight: 600;
            color: #334155;
        }

        .authentication-form .form-control {
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.35);
        }

        .authentication-form .btn-primary {
            border-radius: 999px;
            background: linear-gradient(135deg, #0f766e, #22c55e);
            border: none;
            box-shadow: 0 12px 22px rgba(34, 197, 94, 0.14);
        }

        .authentication-form .btn-primary:hover {
            background: linear-gradient(135deg, #115e59, #16a34a);
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
                            <div class="hero-text">Update your password safely for hospital admin access.</div>
                            <div><i class="fas fa-shield-alt fa-2x"></i></div>
                        </div>

                        <h2 class="fw-bold text-center fs-18">Change Password</h2>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="m-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div>
                            <h4 class="error-message text-danger text-center mt-1 mb-4"></h4>
                            <h4 class="success-message text-success text-center mt-1 mb-4"></h4>
                        </div>

                        <div class="px-4">
                            <form action="{{ route('admin.change-pw') }}" method="POST">
                                @csrf

                                <!-- Old Password -->
                                <div class="mb-3">
                                    <label class="form-label">Old Password</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                    @error('old_password')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required>

                                    <!-- Password Rules (Always Visible Under Input) -->
                                    <small class="text-muted d-block mt-1">
                                        Password must include:
                                        <ul class="small mb-1 mt-1 text-muted">
                                            <li>Minimum 8 characters</li>
                                            <li>At least 1 uppercase letter (A–Z)</li>
                                            <li>At least 1 lowercase letter (a–z)</li>
                                            <li>At least 1 number (0–9)</li>
                                            <li>At least 1 special character (@ $ ! % * # ? &)</li>
                                        </ul>
                                    </small>

                                    @error('password')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Update Password</button>
                            </form>



                        </div> <!-- end col -->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->


            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>
</div>


<!-- endinject -->
</body>

</html>
