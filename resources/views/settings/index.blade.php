@extends('layouts.app')

@section('title', 'Pengaturan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pengaturan.css') }}">
@endpush

@section('content')
<div class="settings-layout">
    <x-sidebar />

    <main class="main-content">
        <x-header title="Pengaturan Sistem" />

        <div class="page-content">
            @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="settings-card">
                <div class="card-header">
                    <h2><i class="fas fa-cog"></i> Integrasi & Pengaturan Toko</h2>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('settings.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <h3 class="section-title"><i class="fas fa-store"></i> Informasi Toko</h3>
                            <div style="max-width: 400px;">
                                <label class="form-label">Nama Toko</label>
                                <input type="text" name="store_name" value="{{ $settings['store_name']->value ?? 'Coffee POS' }}" class="form-input" placeholder="Masukkan nama toko">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 2rem;">
                            <h3 class="section-title"><i class="fas fa-credit-card"></i> Pembayaran (QRIS)</h3>
                            
                            <label class="checkbox-group">
                                <input type="checkbox" name="payment_gateway_active" value="true" {{ ($settings['payment_gateway_active']->value ?? 'false') === 'true' ? 'checked' : '' }} class="checkbox-input">
                                <span class="checkbox-label">
                                    Aktifkan Integrasi Payment Gateway (QRIS Dinamis)
                                </span>
                            </label>
                            
                            <p class="helper-text">Jika dinonaktifkan, pembayaran QRIS di POS hanya akan mencatat transaksi sebagai berhasil tanpa verifikasi otomatis (Static QRIS).</p>
                            
                            <div style="margin-left: 28px; max-width: 400px;">
                                <label class="form-label">API Key (Midtrans/Xendit)</label>
                                <input type="password" name="payment_gateway_key" value="{{ $settings['payment_gateway_key']->value ?? '' }}" class="form-input" placeholder="••••••••••••••••">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
