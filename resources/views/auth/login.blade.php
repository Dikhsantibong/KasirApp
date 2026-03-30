@extends('layouts.app')

@section('title', 'Login Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-container">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    
    <div class="login-card">
        <div class="login-header">
            <div class="logo-wrapper">
                <i class="fas fa-cash-register"></i>
            </div>
            <h1>KasirAPP</h1>
            <p>Silahkan login ke panel admin</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px; border: 2px solid #ef4444;">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" id="loginForm">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">E-mail Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@kasirapp.com" value="{{ old('email', 'admin@kasirapp.com') }}" required>
                    <i class="fas fa-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                </div>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                <span id="btnText">Sign In to Dashboard</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('loginForm').onsubmit = function() {
        document.getElementById('btnText').innerText = 'Authenticating...';
        document.getElementById('submitBtn').style.opacity = '0.7';
        document.getElementById('submitBtn').disabled = true;
        return true;
    };
</script>
@endsection
