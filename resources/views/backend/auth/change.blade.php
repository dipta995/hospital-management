<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Diagnosis Login</title>
    <!-- plugins:css -->
    @include('backend.layouts.partials.style')
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
