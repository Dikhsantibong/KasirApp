@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
@endpush

@section('content')
<div class="transaksi-layout">
   <x-sidebar />

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari ID Transaksi...">
            </div>

            <div class="topbar-actions">
                <div class="status-sync">
                    <i class="fas fa-sync-alt" style="margin-right:4px;"></i>
                    <span>Sinkronisasi Berhasil</span>
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;">
                    <i class="far fa-bell"></i>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="text-align:right;">
                        <span style="display:block; font-weight:700; font-size:0.85rem; color:#1e293b;">Admin Toko</span>
                        <span style="display:block; font-size:0.75rem; color:#64748b;">Premium Plan</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin+Toko&background=0D8ABC&color=fff" class="user-thumb" alt="User" style="width:36px; height:36px; border-radius:50%;">
                </div>
            </div>
        </header>

        <div class="page-content">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Riwayat Transaksi</h1>
                    <p class="page-subtitle">Pantau semua aktivitas penjualan dan detail transaksi yang terjadi.</p>
                </div>
                <div class="header-actions">
                    <a href="#" class="btn-outline">
                        <i class="fas fa-file-export"></i> Ekspor Laporan
                    </a>
                </div>
            </div>

            <!-- STATS ROW -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pemasukan Hari Ini</h3>
                        <p>Rp {{ number_format($todayTotal ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pemasukan Bulan Ini</h3>
                        <p>Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Transaksi</h3>
                        <p>{{ collect($transactions->items() ?? [])->count() }} Trx</p>
                    </div>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="table-container">
                <div class="table-controls">
                    <div class="filters">
                        <select class="filter-select">
                            <option>Semua Status</option>
                            <option>Sukses</option>
                            <option>Pending</option>
                            <option>Dibatalkan</option>
                        </select>
                        <select class="filter-select">
                            <option>Metode: Semua</option>
                            <option>Tunai</option>
                            <option>QRIS</option>
                            <option>Transfer</option>
                        </select>
                    </div>
                    <div>
                        <input type="date" class="filter-select" style="background:white; color:#1e293b;">
                    </div>
                </div>

                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>ID TRANSAKSI</th>
                            <th>KASIR</th>
                            <th>TOTAL BAYAR</th>
                            <th>METODE</th>
                            <th>STATUS</th>
                            <th style="text-align:center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            @php
                                $statusClass = 'status-success';
                                $statusText = 'Sukses';
                                
                                if(strtolower($trx->status) == 'pending') {
                                    $statusClass = 'status-pending';
                                    $statusText = 'Pending';
                                } elseif(strtolower($trx->status) == 'failed' || strtolower($trx->status) == 'dibatalkan') {
                                    $statusClass = 'status-failed';
                                    $statusText = 'Gagal';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <span class="trx-id">#{{ substr($trx->id, 0, 8) }}</span>
                                    <span class="trx-date">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($trx->user->name ?? 'Kasir') }}&background=random" alt="Kasir">
                                        <span>{{ $trx->user->name ?? 'User Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="price-text">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="payment-method">{{ $trx->payment_method ?? 'Cash' }}</span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <button class="action-btn"><i class="fas fa-print"></i></button>
                                </td>
                            </tr>
                        @empty
                            <!-- Dummy Data if empty to show the design -->
                            <tr>
                                <td>
                                    <span class="trx-id">#INV-2023001</span>
                                    <span class="trx-date">12 Okt 2023, 14:30</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name=Ahmad+Fauzi&background=random&color=fff" alt="Kasir">
                                        <span>Ahmad Fauzi</span>
                                    </div>
                                </td>
                                <td class="price-text">Rp 125.000</td>
                                <td><span class="payment-method">QRIS</span></td>
                                <td><span class="status-badge status-success">Sukses</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-print"></i></button></td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="trx-id">#INV-2023002</span>
                                    <span class="trx-date">12 Okt 2023, 15:05</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name=Ahmad+Fauzi&background=random&color=fff" alt="Kasir">
                                        <span>Ahmad Fauzi</span>
                                    </div>
                                </td>
                                <td class="price-text">Rp 45.000</td>
                                <td><span class="payment-method">Tunai</span></td>
                                <td><span class="status-badge status-success">Sukses</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-print"></i></button></td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="trx-id">#INV-2023003</span>
                                    <span class="trx-date">12 Okt 2023, 16:20</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=random&color=fff" alt="Kasir">
                                        <span>Budi Santoso</span>
                                    </div>
                                </td>
                                <td class="price-text">Rp 310.000</td>
                                <td><span class="payment-method">Transfer Bank</span></td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-print"></i></button></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-area">
                    <div class="pagination-info">Menampilkan {{ count($transactions ?? []) > 0 ? (($transactions->currentPage()-1)*$transactions->perPage()+1) . '-' . min($transactions->currentPage()*$transactions->perPage(), $transactions->total()) . ' dari ' . $transactions->total() : '1-3 dari 3' }} transaksi</div>
                    <div class="pagination-controls">
                        <a href="#" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                        <a href="#" class="page-btn active">1</a>
                        <a href="#" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>
@endsection
