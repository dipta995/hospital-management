@extends('backend.auth.layout')

@section('auth-content')
    <h1 class="auth-form-title">Change Password</h1>
    <p class="auth-form-subtitle">Update your password to keep your account secure.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.change-pw') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="old_password" class="form-control" required>
            @error('old_password')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required>
            <small class="text-muted d-block mt-2">
                Password must include at least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 special character.
            </small>
            @error('password')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-auth">Update Password</button>
    </form>
@endsection
