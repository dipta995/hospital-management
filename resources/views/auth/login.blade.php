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

                        <h2 class="fw-bold text-center fs-18">Sign In</h2>
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
                                    <input type="password" id="password" name="password" class="form-control input" placeholder="Enter your password">
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


<!-- endinject -->
</body>

</html>
