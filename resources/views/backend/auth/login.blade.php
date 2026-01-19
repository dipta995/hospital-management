<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hospital Admin Login</title>
    <!-- plugins:css -->
    @include('backend.layouts.partials.style')

    <style>
        body.authentication-bg {
            background: radial-gradient(circle at top left, #e0f2fe 0, #ecfeff 40%, #f9fafb 100%);
        }

        .auth-card {
            border-radius: 20px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.25);
        }

        .auth-logo {
            color: #0f766e;
        }

        .auth-title {
            color: #0f172a;
            letter-spacing: 0.03em;
        }

        .authentication-form .form-label {
            font-weight: 500;
            color: #374151;
        }

        .authentication-form .form-control {
            border-radius: 999px;
            padding-left: 1rem;
            padding-right: 1rem;
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
            background: linear-gradient(135deg, #0f766e, #0ea5e9);
            border: none;
        }

        .authentication-form .btn-primary:disabled {
            opacity: 0.75;
            cursor: not-allowed;
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
                        <div class="mx-auto mb-4 text-center auth-logo">
                            {{--                            <a href="{{ route('admin.home') }}" class="logo-dark">--}}
                            {{--                                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" height="30" class="me-1" alt="logo sm">--}}
                            {{--                                <img src="{{ asset('backend/assets/images/logo-dark-full.png') }}" height="24" alt="logo dark">--}}
                            {{--                            </a>--}}

                            {{--                            <a href="{{ route('admin.home') }}" class="logo-light">--}}
                            {{--                                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" height="30" class="me-1" alt="logo sm">--}}
                            {{--                                <img src="{{ asset('backend/assets/images/logo-dark-full.png') }}" height="24" alt="logo light">--}}
                            {{--                            </a>--}}
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
