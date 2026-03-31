@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}">
@endpush

@section('content')
<div class="pelanggan-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="{{ route('pelanggan.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari pelanggan atau transaksi..." value="{{ request('search') }}">
                </form>
            </div>
            
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-check-circle"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <img src="https://ui-avatars.com/api/?name=Admin+Toko&background=0D8ABC&color=fff" style="width:36px; height:36px; border-radius:50%;">
            </div>
        </header>

        <div class="page-content">
            <div class="breadcrumb">
                Manajemen &rsaquo; <span>Pelanggan</span>
            </div>
            
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daftar Pelanggan</h1>
                    <p class="page-subtitle">Kelola data pelanggan setia dan pantau kewajiban pembayaran secara efisien dalam satu dasbor.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-outline-primary">
                        <i class="fas fa-download"></i> Ekspor Data
                    </button>
                    <button class="btn-solid-primary">
                        <i class="fas fa-user-plus"></i> Tambah Pelanggan
                    </button>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card-blue">
                    <div>
                        <h4>Total Piutang Berjalan</h4>
                        <!-- Mockup format or dynamic piutang -->
                        <h1>Rp {{ number_format($totalPiutang ?? 42850000, 0, ',', '.') }}</h1>
                    </div>
                    <div class="stat-blue-footer">
                        <div class="stat-badge-trend">
                            <i class="fas fa-arrow-trend-up"></i> +12.5% bln ini
                        </div>
                        <span class="stat-subtext">Dari {{ $totalCustomer ?? 24 }} pelanggan aktif</span>
                    </div>
                </div>
                
                <div class="stat-card-white">
                    <div class="stat-icon icon-orange"><i class="far fa-calendar-alt"></i></div>
                    <h4>JATUH TEMPO</h4>
                    <h2>{{ $jatuhTempoCount ?? 8 }} Pelanggan</h2>
                    <p class="text-danger">! Butuh pengingat segera</p>
                </div>

                <div class="stat-card-white">
                    <div class="stat-icon icon-green"><i class="fas fa-check"></i></div>
                    <h4>PELANGGAN LOYAL</h4>
                    <h2>156 Orang</h2>
                    <p class="text-success">Status Platinum</p>
                </div>
            </div>

            <div class="section-header">
                <h2>Hutang Pelanggan & Jatuh Tempo</h2>
                <a href="#" class="link-all">Lihat Semua Kalender &rarr;</a>
            </div>

            <div class="table-container">
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>NAMA PELANGGAN</th>
                            <th>NO. WHATSAPP</th>
                            <th>TOTAL BELANJA</th>
                            <th>SISA HUTANG</th>
                            <th>STATUS / TEMPO</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            @php
                                $initials = strtoupper(substr($c->name ?? 'A', 0, 2));
                                $hasHutang = ($c->total_hutang ?? 0) > 0;
                                $hutangClass = $hasHutang ? (($c->total_hutang > 1000000) ? 'amount-text-danger' : 'amount-text-blue') : '';
                            @endphp
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="cust-avatar blue">{{ $initials }}</div>
                                        <div class="cust-info">
                                            <h4>{{ $c->name }}</h4>
                                            <p>{{ $c->type ?? 'Pelanggan Umum' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="phone-text">{{ $c->phone ?? '08xx-xxxx-xxxx' }}</span></td>
                                <td><span class="amount-text">Rp {{ number_format($c->total_belanja ?? 0, 0, ',', '.') }}</span></td>
                                <td>
                                    <span class="{{ $hutangClass }}">
                                        {{ $hasHutang ? 'Rp '.number_format($c->total_hutang, 0, ',', '.') : 'Rp 0' }}
                                    </span>
                                </td>
                                <td>
                                    @if($hasHutang)
                                        <span class="status-badge"><i class="fas fa-exclamation-square"></i> LEWAT 3 HARI</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="aksi-cell">
                                        @if($hasHutang)
                                            <button class="btn-remind"><i class="fab fa-telegram-plane"></i> Kirim Pengingat</button>
                                        @endif
                                        <button class="icon-btn icon-blue-light"><i class="fas fa-eye"></i></button>
                                        <button class="icon-btn icon-green-light"><i class="fab fa-whatsapp"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <!-- DUMMY DATA ALIGNED WITH THE DESIGN/SCREENSHOT -->
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="cust-avatar blue">BP</div>
                                        <div class="cust-info">
                                            <h4>Bambang Pamungkas</h4>
                                            <p>Pelanggan Grosir</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="phone-text">0812-3456-7890</span></td>
                                <td><span class="amount-text">Rp 12.450.000</span></td>
                                <td><span class="amount-text-danger">Rp 4.200.000</span></td>
                                <td><span class="status-badge"><i class="fas fa-circle" style="font-size:4px;"></i> LEWAT 3 HARI</span></td>
                                <td>
                                    <div class="aksi-cell">
                                        <button class="btn-remind"><i class="fab fa-telegram-plane"></i> Kirim Pengingat</button>
                                        <button class="icon-btn icon-blue-light"><i class="fas fa-eye"></i></button>
                                        <button class="icon-btn icon-green-light"><i class="fas fa-comment"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="cust-avatar blue">SN</div>
                                        <div class="cust-info">
                                            <h4>Siti Nurhaliza</h4>
                                            <p>Retail Member</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="phone-text">0877-9988-1122</span></td>
                                <td><span class="amount-text">Rp 2.100.000</span></td>
                                <td><span class="amount-text-blue">Rp 500.000</span></td>
                                <td></td>
                                <td>
                                    <div class="aksi-cell">
                                        <button class="icon-btn icon-blue-light"><i class="fas fa-eye"></i></button>
                                        <button class="icon-btn icon-green-light"><i class="fas fa-comment"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="cust-avatar blue" style="background:#e0f2fe; color:#0284c7;">AS</div>
                                        <div class="cust-info">
                                            <h4>Andi Saputra</h4>
                                            <p>Pelanggan Baru</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="phone-text">0813-1122-3344</span></td>
                                <td><span class="amount-text">Rp 850.000</span></td>
                                <td><span class="phone-text" style="font-weight:600;">Rp 0</span></td>
                                <td></td>
                                <td>
                                    <div class="aksi-cell">
                                        <button class="icon-btn icon-blue-light"><i class="fas fa-eye"></i></button>
                                        <button class="icon-btn icon-green-light"><i class="fas fa-comment"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-info">Menampilkan {{ count($customers) > 0 ? count($customers) : 3 }} dari 156 pelanggan</div>
            </div>
            
        </div>
    </main>
</div>

<!-- FLOATING CREDIT WIDGET -->
<div class="credit-widget">
    <div class="credit-header">
        <div class="credit-title-flex">
            <div class="credit-icon"><i class="fas fa-chart-pie"></i></div>
            <div>
                <h4>Ringkasan Kredit</h4>
                <p>Data real-time hari ini</p>
            </div>
        </div>
        <div class="credit-close"><i class="fas fa-times"></i></div>
    </div>
    <div class="credit-body">
        <div class="credit-item">
            <span>Piutang Tertunda</span>
            <span class="credit-val">Rp 12.800.000</span>
        </div>
        <div class="credit-item">
            <span>Pelunasan Masuk</span>
            <span class="credit-val-green">Rp 5.200.000</span>
        </div>
        <div class="credit-total">
            <span>Net Saldo</span>
            <span>Rp 7.600.000</span>
        </div>
    </div>
    <div class="credit-footer">
        <button class="btn-full-blue"><i class="fas fa-print"></i> Cetak Laporan Piutang</button>
    </div>
</div>
@endsection
