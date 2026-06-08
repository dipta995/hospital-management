@extends('backend.auth.layout')

@section('auth-content')
    <h1 class="auth-form-title">Admin Sign In</h1>
    <p class="auth-form-subtitle">Enter your credentials to access the dashboard.</p>

    @if(session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    <form method="post" action="{{ route('admin.login.submit') }}" class="authentication-form" id="login-form">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Enter your password" required>
                <button class="btn btn-outline-secondary password-toggle-btn" type="button"
                        id="password-toggle" aria-label="Show password">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
        </div>

        <button class="btn btn-auth" type="submit" id="login-submit">Sign In</button>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('password-toggle');
        const passwordInput = document.getElementById('password');
        const form = document.getElementById('login-form');
        const submitBtn = document.getElementById('login-submit');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                toggleBtn.querySelector('i').classList.toggle('fa-eye');
                toggleBtn.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                if (submitBtn.disabled) {
                    return;
                }
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Signing in...';
                setTimeout(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 5000);
            });
        }
    });
</script>
@endpush
