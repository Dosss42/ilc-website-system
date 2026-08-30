@extends('finance.layout')

@section('title', 'My Profile')

@section('styles')
<style>
    .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
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

    .form-control:disabled {
        background: #f8f9fa;
        color: #666;
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

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--gold), var(--blue));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #fff;
        margin: 0 auto 20px;
    }

    .role-badge {
        display: inline-block;
        background: var(--gold);
        color: #fff;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">My Profile</h1>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-body" style="text-align: center;">
                <div class="profile-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <h4 style="margin-bottom: 8px;">{{ $user->name }}</h4>
                <span class="role-badge">{{ $user->role }}</span>
                <div style="margin-top: 16px; color: #666; font-size: 14px;">
                    <p style="margin-bottom: 4px;"><i class="bi bi-envelope"></i> {{ $user->email }}</p>
                    @if($user->phone)
                        <p style="margin-bottom: 4px;"><i class="bi bi-phone"></i> {{ $user->phone }}</p>
                    @endif
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                    <p style="font-size: 12px; color: #666; margin-bottom: 4px;">Member Since</p>
                    <p style="font-weight: 600; color: var(--blue);">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pencil-square" style="color: var(--gold);"></i>
                    Edit Profile
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('finance.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                                <small style="color: #666; font-size: 12px;">Email cannot be changed</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
