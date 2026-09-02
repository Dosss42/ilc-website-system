@extends('finance.layout')

@section('title', 'Change Password')

@section('styles')
<style>
    .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        max-width: 600px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--blue);
    }

    .card-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .input-group {
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--gold);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 12px;
        margin-top: 6px;
    }

    .btn-save {
        padding: 12px 32px;
        background: linear-gradient(135deg, var(--blue), var(--blue-light));
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-save:hover {
        opacity: 0.9;
    }

    .password-requirements {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .password-requirements h5 {
        font-size: 13px;
        font-weight: 600;
        color: var(--blue);
        margin-bottom: 12px;
    }

    .password-requirements ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
        color: #666;
    }

    .password-requirements li {
        margin-bottom: 4px;
    }

    .security-tips {
        background: #fff3e0;
        border-left: 4px solid var(--gold);
        border-radius: 8px;
        padding: 16px;
    }

    .security-tips h5 {
        font-size: 13px;
        font-weight: 600;
        color: #e65100;
        margin-bottom: 8px;
    }

    .security-tips p {
        margin: 0;
        font-size: 12px;
        color: #666;
    }
</style>
@endsection

@section('skeleton')
<div class="skel skel-header-title"></div>
<div class="skel-card" style="max-width:600px;">
    <div class="skel skel-card-header"></div>
    <div class="skel skel-line" style="width:100%;height:60px;border-radius:8px;margin-bottom:20px;"></div>
    <div class="skel skel-form-lbl"></div>
    <div class="skel skel-form-fld"></div>
    <div class="skel skel-form-lbl"></div>
    <div class="skel skel-form-fld"></div>
    <div class="skel skel-form-lbl"></div>
    <div class="skel skel-form-fld"></div>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Change Password</h1>
</div>

<div class="content-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-shield-lock" style="color: var(--gold);"></i>
            Update Your Password
        </h3>
    </div>
    <div class="card-body">
        <div class="password-requirements">
            <h5><i class="bi bi-info-circle"></i> Password Requirements</h5>
            <ul>
                <li>Minimum 8 characters long</li>
                <li>Include at least one uppercase letter</li>
                <li>Include at least one lowercase letter</li>
                <li>Include at least one number</li>
                <li>Include at least one special character</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('finance.change-password') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Current Password</label>
                <div class="input-group">
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                </div>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                </div>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="security-tips">
                <h5><i class="bi bi-shield-exclamation"></i> Security Tips</h5>
                <p>Never share your password with anyone. Use a unique password that you don't use on other websites. Consider using a password manager to generate and store strong passwords securely.</p>
            </div>

            <div class="text-end" style="margin-top: 24px;">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
