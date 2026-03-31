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
                    <input type="text" name="search" placeholder="Cari pelanggan..." value="{{ request('search') }}">
                </form>
            </div>
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-check-circle"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" style="width:36px; height:36px; border-radius:50%;">
            </div>
        </header>

        <div class="page-content">
            <div class="breadcrumb">Manajemen &rsaquo; <span>Pelanggan</span></div>
            
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daftar Pelanggan</h1>
                    <p class="page-subtitle">Kelola data pelanggan setia dan pantau kewajiban pembayaran.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-outline-primary"><i class="fas fa-download"></i> Ekspor Data</button>
                    <button class="btn-solid-primary"><i class="fas fa-user-plus"></i> Tambah Pelanggan</button>
                </div>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card-blue">
                    <div>
                        <h4>Total Piutang Berjalan</h4>
                        <h1>Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h1>
                    </div>
                    <div class="stat-blue-footer">
                        <span class="stat-subtext">Dari {{ $totalCustomer }} pelanggan terdaftar</span>
                    </div>
                </div>
                
                <div class="stat-card-white">
                    <div class="stat-icon icon-orange"><i class="far fa-calendar-alt"></i></div>
                    <h4>JATUH TEMPO</h4>
                    <h2>{{ $jatuhTempoCount }} Pelanggan</h2>
                    @if($jatuhTempoCount > 0)
                        <p class="text-danger">! Butuh pengingat segera</p>
                    @else
                        <p class="text-success">Semua aman</p>
                    @endif
                </div>

                <div class="stat-card-white">
                    <div class="stat-icon icon-green"><i class="fas fa-users"></i></div>
                    <h4>TOTAL PELANGGAN</h4>
                    <h2>{{ $totalCustomer }} Orang</h2>
                    <p class="text-success">Terdaftar</p>
                </div>
            </div>

            <div class="section-header">
                <h2>Hutang Pelanggan & Jatuh Tempo</h2>
            </div>

            <div class="table-container">
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>NAMA PELANGGAN</th>
                            <th>NO. WHATSAPP</th>
                            <th>TOTAL BELANJA</th>
                            <th>SISA HUTANG</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            @php
                                $initials = strtoupper(substr($c->name ?? 'A', 0, 2));
                                $hasHutang = ($c->total_hutang ?? 0) > 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="cust-avatar blue">{{ $initials }}</div>
                                        <div class="cust-info">
                                            <h4>{{ $c->name }}</h4>
                                            <p>Pelanggan</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="phone-text">{{ $c->phone ?? '-' }}</span></td>
                                <td><span class="amount-text">Rp {{ number_format($c->total_belanja ?? 0, 0, ',', '.') }}</span></td>
                                <td>
                                    <span class="{{ $hasHutang ? ($c->total_hutang > 1000000 ? 'amount-text-danger' : 'amount-text-blue') : '' }}">
                                        Rp {{ number_format($c->total_hutang ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="aksi-cell">
                                        @if($hasHutang)
                                            <button class="btn-remind"><i class="fas fa-bell"></i> Ingatkan</button>
                                        @endif
                                        <button class="icon-btn icon-blue-light"><i class="fas fa-eye"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    <i class="fas fa-users" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada pelanggan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-info">
                    @if($customers->total() > 0)
                        Menampilkan {{ ($customers->currentPage()-1)*$customers->perPage()+1 }}-{{ min($customers->currentPage()*$customers->perPage(), $customers->total()) }} dari {{ $customers->total() }} pelanggan
                    @else
                        Tidak ada data pelanggan
                    @endif
                </div>
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
                <p>Data real-time</p>
            </div>
        </div>
    </div>
    <div class="credit-body">
        <div class="credit-item">
            <span>Piutang Berjalan</span>
            <span class="credit-val">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
        </div>
        <div class="credit-item">
            <span>Pelanggan Terdaftar</span>
            <span class="credit-val-green">{{ $totalCustomer }}</span>
        </div>
        <div class="credit-total">
            <span>Jatuh Tempo</span>
            <span>{{ $jatuhTempoCount }} pelanggan</span>
        </div>
    </div>
</div>
@endsection
