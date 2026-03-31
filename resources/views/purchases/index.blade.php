@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pembelian.css') }}">
@endpush

@section('content')
<div class="pembelian-layout">
   <x-sidebar />

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari transaksi atau supplier...">
            </div>

            <div class="topbar-actions">
                <div class="status-sync">
                    <i class="fas fa-sync-alt" style="margin-right:4px;"></i>
                    <span>Sinkronisasi Berhasil</span>
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;">
                    <i class="far fa-bell"></i>
                </div>
                <!-- Profile Snippet -->
                <div style="display:flex; align-items:center;">
                    <img src="https://ui-avatars.com/api/?name=Admin+Toko&background=0D8ABC&color=fff" class="user-thumb" alt="User" style="width:36px; height:36px;">
                </div>
            </div>
        </header>

        <div class="page-content">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Riwayat Pembelian ke Supplier</h1>
                    <p class="page-subtitle">Kelola pengadaan barang dan pantau status pembayaran kepada mitra pemasok Anda dengan efisien.</p>
                </div>
                <div class="header-actions">
                    <a href="#" class="btn-primary">
                        <i class="fas fa-shopping-cart"></i> Catat Pembelian Baru
                    </a>
                </div>
            </div>

            <!-- TOP SECTION GRID -->
            <div class="top-section-grid">
                
                <!-- Suppliers Section -->
                <div class="suppliers-section">
                    <div class="section-header-flex">
                        <h3>Daftar Supplier</h3>
                        <a href="#" class="btn-link">Lihat Semua</a>
                    </div>
                    
                    <div class="suppliers-grid">
                        @forelse($suppliers->take(3) as $key => $supplier)
                            @php
                                // Mock data variations
                                $tag = $key == 1 ? 'BARU' : ($key == 2 ? 'ELEKTRONIK' : 'MITRA UTAMA');
                                $iconClass = $key == 1 ? 'icon-orange' : ($key == 2 ? 'icon-purple' : '');
                                $icon = $key == 1 ? 'fa-clipboard-list' : ($key == 2 ? 'fa-cube' : 'fa-truck');
                            @endphp
                            <div class="supplier-card">
                                <div>
                                    <div class="supplier-card-top">
                                        <div class="supplier-icon {{ $iconClass }}">
                                            <i class="fas {{ $icon }}"></i>
                                        </div>
                                        <span class="supplier-tag">{{ $tag }}</span>
                                    </div>
                                    <h4 class="supplier-name">{{ $supplier->name }}</h4>
                                    <!-- Assume location strings are somehow structured, mocking visually -->
                                    <p class="supplier-loc">{{ $supplier->address ?? 'Jakarta Selatan, DKI Jakarta' }}</p>
                                </div>
                                <div class="supplier-stats">
                                    <span>Total Transaksi</span>
                                    <span>{{ $supplier->total_transaksi ?? rand(10, 150) }}</span>
                                </div>
                            </div>
                        @empty
                            <!-- Dummy if DB empty -->
                            <div class="supplier-card">
                                <div>
                                    <div class="supplier-card-top">
                                        <div class="supplier-icon"><i class="fas fa-truck"></i></div>
                                        <span class="supplier-tag">MITRA UTAMA</span>
                                    </div>
                                    <h4 class="supplier-name">PT Sinar Distribusi</h4>
                                    <p class="supplier-loc">Jakarta Selatan, DKI Jakarta</p>
                                </div>
                                <div class="supplier-stats"><span>Total Transaksi</span><span>128</span></div>
                            </div>
                            <div class="supplier-card">
                                <div>
                                    <div class="supplier-card-top">
                                        <div class="supplier-icon icon-orange"><i class="fas fa-clipboard-list"></i></div>
                                        <span class="supplier-tag">BARU</span>
                                    </div>
                                    <h4 class="supplier-name">CV Sumber Pangan</h4>
                                    <p class="supplier-loc">Surabaya, Jawa Timur</p>
                                </div>
                                <div class="supplier-stats"><span>Total Transaksi</span><span>42</span></div>
                            </div>
                            <div class="supplier-card">
                                <div>
                                    <div class="supplier-card-top">
                                        <div class="supplier-icon icon-purple"><i class="fas fa-cube"></i></div>
                                        <span class="supplier-tag">ELEKTRONIK</span>
                                    </div>
                                    <h4 class="supplier-name">Agung Tech Supply</h4>
                                    <p class="supplier-loc">Bandung, Jawa Barat</p>
                                </div>
                                <div class="supplier-stats"><span>Total Transaksi</span><span>15</span></div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Debt Summary Card -->
                <div class="debt-card">
                    <div class="debt-title">TOTAL HUTANG JATUH TEMPO</div>
                    <div class="debt-amount">Rp 12,8Jt</div>
                    <div class="debt-info-list">
                        <div class="debt-info-item">
                            <i class="far fa-clock"></i>
                            <span>4 tagihan melewati batas waktu</span>
                        </div>
                        <div class="debt-info-item">
                            <i class="far fa-money-bill-alt"></i>
                            <span>3 menunggu konfirmasi pembayaran</span>
                        </div>
                    </div>
                    <button class="btn-white-full">Bayar Tagihan Sekarang</button>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="table-container">
                <div class="table-controls">
                    <h3>Transaksi Terbaru</h3>
                    <div class="filters-wrapper">
                        <button class="btn-outline">Filter</button>
                        <button class="btn-outline"><i class="fas fa-download"></i> Ekspor</button>
                    </div>
                </div>

                <table class="purchases-table">
                    <thead>
                        <tr>
                            <th>ID TRANSAKSI</th>
                            <th>TANGGAL</th>
                            <th>SUPPLIER</th>
                            <th>TOTAL BELANJA</th>
                            <th>STATUS BAYAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <!-- Mock status assignment for layout simulation since no status field in DB -->
                            @php 
                              $isHutang = rand(0,1); 
                              $statusClass = $isHutang ? 'status-hutang' : 'status-lunas';
                              $statusLabel = $isHutang ? 'Hutang' : 'Lunas';
                              $initials = strtoupper(substr($p->supplier->name ?? 'A', 0, 2));
                            @endphp
                            <tr>
                                <td><span class="p-id">#PUR-{{ substr($p->id, 0, 8) }}</span></td>
                                <td>
                                    <span class="p-date">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</span>
                                    <span class="p-time">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i WIB') }}</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar">{{ $initials }}</div>
                                        <span class="sup-name">{{ $p->supplier->name ?? 'Unknown Supplier' }}</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</span></td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            </tr>
                        @empty
                            <!-- Dummy Layout match -->
                            <tr>
                                <td><span class="p-id">#PUR-20230501</span></td>
                                <td>
                                    <span class="p-date">12 Mei 2023</span>
                                    <span class="p-time">14:20 WIB</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar">SD</div>
                                        <span class="sup-name">PT Sinar Distribusi</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp 4.500.000</span></td>
                                <td><span class="status-badge status-lunas">Lunas</span></td>
                            </tr>
                            <tr>
                                <td><span class="p-id">#PUR-20230504</span></td>
                                <td>
                                    <span class="p-date">08 Mei 2023</span>
                                    <span class="p-time">09:15 WIB</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar orange">SP</div>
                                        <span class="sup-name">CV Sumber Pangan</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp 12.250.000</span></td>
                                <td><span class="status-badge status-hutang">Hutang</span></td>
                            </tr>
                            <tr>
                                <td><span class="p-id">#PUR-20230492</span></td>
                                <td>
                                    <span class="p-date">05 Mei 2023</span>
                                    <span class="p-time">16:45 WIB</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar red">AT</div>
                                        <span class="sup-name">Agung Tech Supply</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp 8.900.000</span></td>
                                <td><span class="status-badge status-lunas">Lunas</span></td>
                            </tr>
                            <tr>
                                <td><span class="p-id">#PUR-20230485</span></td>
                                <td>
                                    <span class="p-date">01 Mei 2023</span>
                                    <span class="p-time">11:00 WIB</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar">SD</div>
                                        <span class="sup-name">PT Sinar Distribusi</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp 1.150.000</span></td>
                                <td><span class="status-badge status-lunas">Lunas</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-info">Menampilkan {{ count($purchases ?? []) > 0 ? count($purchases) : 4 }} dari {{ $purchases->total() ?? 150 }} transaksi</div>
            </div>
            
        </div>
    </main>
</div>

<!-- DRAFT PEMBELIAN FLOAT WIDGET -->
<div class="draft-widget">
    <div class="draft-header">
        <h4>Draft Pembelian</h4>
        <span class="draft-badge">3 Item</span>
    </div>
    <div class="draft-body">
        <div class="draft-item">
            <span>Minyak Goreng 2L (x10)</span>
            <span class="draft-item-val">Rp 350rb</span>
        </div>
        <div class="draft-item">
            <span>Beras Premium 5kg (x5)</span>
            <span class="draft-item-val">Rp 425rb</span>
        </div>
        <div class="draft-item">
            <span>Gula Pasir 1kg (x20)</span>
            <span class="draft-item-val">Rp 280rb</span>
        </div>
    </div>
    <div class="draft-footer">
        <div class="draft-total-row">
            <span class="draft-total-text">Estimasi Total</span>
            <span class="draft-total-amount">Rp 1,05Jt</span>
        </div>
        <button class="btn-draft">Lanjutkan Draft</button>
    </div>
</div>
@endsection
