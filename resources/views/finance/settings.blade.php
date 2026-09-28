@extends('finance.layout')

@section('title', 'Settings')

@section('styles')
<style>
    /* ── Profile banner ── */
    .settings-banner {
        background: linear-gradient(135deg, var(--blue), var(--blue-light));
        border-radius: 16px;
        padding: 32px 28px;
        display: flex;
        align-items: center;
        gap: 22px;
        color: #fff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .settings-banner::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -30px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }

    .settings-banner::after {
        content: '';
        position: absolute;
        bottom: -60px;
        right: 80px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .settings-banner-avatar {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        border: 3px solid rgba(255,255,255,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .settings-banner-info { position: relative; z-index: 1; }

    .settings-banner-name {
        font-size: 21px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .settings-banner-meta {
        font-size: 13px;
        opacity: 0.88;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .settings-banner-badge {
        background: rgba(255,255,255,0.2);
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    /* ── Cards ── */
    .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        max-width: 640px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid #f0f0f0;
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--blue);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-sub {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .card-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 7px;
    }

    /* Icon-prefixed inputs */
    .input-icon-wrap {
        position: relative;
    }

    .input-icon-wrap > i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 15px;
        pointer-events: none;
    }

    .form-control {
        width: 100%;
        padding: 11px 14px 11px 40px;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        font-size: 14px;
        transition: border-color 0.15s, background 0.15s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--gold);
    }

    .form-control:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
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
        padding: 11px 28px;
        background: linear-gradient(135deg, var(--blue), var(--blue-light));
        color: #fff;
        border: none;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .btn-save:hover {
        opacity: 0.92;
    }

    .password-requirements {
        background: #f8fafc;
        border-radius: 9px;
        padding: 16px 18px;
        margin-bottom: 22px;
    }

    .password-requirements h5 {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--blue);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .password-requirements ul {
        margin: 0;
        padding-left: 20px;
        font-size: 12.5px;
        color: #64748b;
    }

    .password-requirements li {
        margin-bottom: 4px;
    }

    .security-tips {
        background: #fff8e8;
        border-left: 3px solid var(--gold);
        border-radius: 9px;
        padding: 14px 16px;
    }

    .security-tips h5 {
        font-size: 12.5px;
        font-weight: 700;
        color: #92620a;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .security-tips p {
        margin: 0;
        font-size: 12px;
        color: #78716c;
    }
</style>
@endsection

@section('skeleton')
<div class="skel skel-header-title"></div>
<div class="skel-card" style="max-width:640px;height:120px;margin-bottom:24px;border-radius:16px;"></div>
<div class="skel-card" style="max-width:640px;">
    <div class="skel skel-card-header"></div>
    <div class="skel-row-gap" style="margin-bottom:16px;">
        <div style="flex:1;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
        <div style="flex:1;"><div class="skel skel-form-lbl"></div><div class="skel skel-form-fld"></div></div>
    </div>
</div>
<div class="skel-card" style="max-width:640px;">
    <div class="skel skel-card-header"></div>
    <div class="skel skel-line" style="width:100%;height:60px;border-radius:8px;margin-bottom:20px;"></div>
    <div class="skel skel-form-lbl"></div>
    <div class="skel skel-form-fld"></div>
    <div class="skel skel-form-lbl"></div>
    <div class="skel skel-form-fld"></div>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Settings</h1>
</div>

{{-- Profile banner --}}
<div class="settings-banner">
    <div class="settings-banner-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
    <div class="settings-banner-info">
        <div class="settings-banner-name">{{ $user->name }}</div>
        <div class="settings-banner-meta">
            <span class="settings-banner-badge">{{ $user->role }}</span>
            <span><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
            <span><i class="bi bi-calendar3 me-1"></i>Member since {{ $user->created_at->format('F Y') }}</span>
        </div>
    </div>
</div>

{{-- Profile Information --}}
<div class="content-card">
    <div class="card-header">
        <div>
            <h3 class="card-title"><i class="bi bi-person-lines-fill" style="color: var(--gold);"></i> Profile Information</h3>
            <div class="card-sub">Update your name and contact number.</div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('finance.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        <small style="color: #94a3b8; font-size: 11.5px;">Email cannot be changed</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Phone Number</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-telephone"></i>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}" placeholder="Not set">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Role</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-shield-check"></i>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end" style="margin-top:20px;">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Security --}}
<div class="content-card">
    <div class="card-header">
        <div>
            <h3 class="card-title"><i class="bi bi-shield-lock" style="color: var(--gold);"></i> Security</h3>
            <div class="card-sub">Change your account password.</div>
        </div>
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
                <div class="input-icon-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                </div>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-key"></i>
                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                </div>
                @error('new_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-key-fill"></i>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="security-tips">
                <h5><i class="bi bi-shield-exclamation"></i> Security Tips</h5>
                <p>Never share your password with anyone. Use a unique password that you don't use on other websites. Consider using a password manager to generate and store strong passwords securely.</p>
            </div>

            <div class="text-end" style="margin-top: 20px;">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
