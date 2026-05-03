@extends('layouts.app')

@section('title', 'Selamat Datang Kembali')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-page-wrapper">
    <!-- Header Section -->
    <div class="login-header-section">
        <div class="login-logo-box" style="background: white; border: 1px solid #f1f5f9; box-shadow: 0 10px 30px rgba(0,0,0,0.08); width: 180px; height: 70px; border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="{{ asset('main_logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; transform: scale(1.6);">
        </div>
        <h1>Selamat Datang Kembali</h1>
        <p>Kelola bisnismu dengan lebih mudah hari ini</p>
    </div>

    <!-- Login Card -->
    <div class="login-card-container">
        <!-- Error Alerts -->
        @if($errors->any())
            <div class="alert-box">
                <i class="fas fa-exclamation-circle"></i>
                <div class="alert-text">{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" id="loginForm">
            @csrf
            
            <!-- Email Field -->
            <div class="login-form-group">
                <label class="login-form-label" for="email">ALAMAT EMAIL</label>
                <div class="login-input-box">
                    <i class="far fa-envelope"></i>
                    <input type="email" name="email" id="email" 
                           placeholder="nama@toko.com" 
                           value="{{ old('email') }}" 
                           required autocomplete="email" autofocus>
                </div>
            </div>

            <!-- Password Field -->
            <div class="login-form-group" style="margin-bottom: 1rem;">
                <label class="login-form-label" for="password">KATA SANDI</label>
                <div class="login-input-box">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" 
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="far fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Forgot Password -->
            <div class="forgot-link-wrapper">
                <a href="#" class="forgot-link">Lupa kata sandi?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary-masuk" id="submitBtn">
                <span id="btnText">Masuk</span>
                <i class="fas fa-arrow-right"></i>
            </button>

            <!-- Register Section -->
            <div class="divider-box">
                <span class="divider-text">Belum punya akun?</span>
            </div>

            <a href="#" class="btn-register-new">
                Daftar Toko Baru
            </a>
        </form>
    </div>

    <!-- Footer Links -->
    <div class="login-footer-nav">
        <div class="footer-brand">LAYANAN OLEH KASIRAPP POS</div>
        <div class="footer-links-row">
            <a href="#">Privasi</a>
            <a href="#">Bantuan</a>
            <a href="#">Syarat & Ketentuan</a>
        </div>
    </div>

    <!-- Security Badge -->
    <div class="security-badge">
        <div class="security-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="security-text">
            <h5>KEAMANAN TERJAMIN</h5>
            <p>Enkripsi 256-bit AES aktif</p>
        </div>
    </div>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');

    loginForm.onsubmit = function() {
        btnText.innerText = 'Menghubungkan...';
        submitBtn.style.opacity = '0.7';
        submitBtn.disabled = true;
        return true;
    };

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection
