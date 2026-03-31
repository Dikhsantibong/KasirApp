@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pembelian.css') }}">
@endpush

@section('content')
<div class="pembelian-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="{{ route('pembelian.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari transaksi atau supplier..." value="{{ request('search') }}">
                </form>
            </div>
            <div class="topbar-actions">
                <div class="status-sync">
                    <i class="fas fa-sync-alt" style="margin-right:4px;"></i>
                    <span>Sinkronisasi Berhasil</span>
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" class="user-thumb" alt="User" style="width:36px; height:36px;">
            </div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Riwayat Pembelian ke Supplier</h1>
                    <p class="page-subtitle">Kelola pengadaan barang dan pantau status pembayaran kepada mitra pemasok Anda.</p>
                </div>
                <div class="header-actions">
                    <a href="#" class="btn-primary"><i class="fas fa-shopping-cart"></i> Catat Pembelian Baru</a>
                </div>
            </div>

            <!-- TOP SECTION GRID -->
            <div class="top-section-grid">
                <div class="suppliers-section">
                    <div class="section-header-flex">
                        <h3>Daftar Supplier</h3>
                        <span style="font-size:0.8rem; color:#64748b;">{{ $suppliers->count() }} supplier terdaftar</span>
                    </div>
                    <div class="suppliers-grid">
                        @forelse($suppliers->take(3) as $key => $supplier)
                            @php
                                $icons = ['fa-truck', 'fa-clipboard-list', 'fa-cube'];
                                $classes = ['', 'icon-orange', 'icon-purple'];
                            @endphp
                            <div class="supplier-card">
                                <div>
                                    <div class="supplier-card-top">
                                        <div class="supplier-icon {{ $classes[$key % 3] }}"><i class="fas {{ $icons[$key % 3] }}"></i></div>
                                        <span class="supplier-tag">SUPPLIER</span>
                                    </div>
                                    <h4 class="supplier-name">{{ $supplier->name }}</h4>
                                    <p class="supplier-loc">{{ $supplier->phone ?? '-' }}</p>
                                </div>
                                <div class="supplier-stats">
                                    <span>Total Transaksi</span>
                                    <span>{{ $supplier->purchases_count }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; color:#94a3b8; padding:2rem; grid-column:1/-1;">
                                <i class="fas fa-truck" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                Belum ada supplier terdaftar.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="debt-card">
                    <div class="debt-title">TOTAL PEMBELIAN</div>
                    <div class="debt-amount">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</div>
                    <div class="debt-info-list">
                        <div class="debt-info-item">
                            <i class="fas fa-box"></i>
                            <span>{{ $purchases->total() }} transaksi tercatat</span>
                        </div>
                        <div class="debt-info-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $suppliers->count() }} supplier aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <div class="table-controls">
                    <h3>Transaksi Terbaru</h3>
                    <div class="filters-wrapper">
                        <span style="font-size:0.85rem; color:#64748b;">Total: <strong>{{ $purchases->total() }} transaksi</strong></span>
                    </div>
                </div>

                <table class="purchases-table">
                    <thead>
                        <tr>
                            <th>ID TRANSAKSI</th>
                            <th>TANGGAL</th>
                            <th>SUPPLIER</th>
                            <th>TOTAL BELANJA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <tr>
                                <td><span class="p-id">#PUR-{{ strtoupper(substr($p->id, 0, 8)) }}</span></td>
                                <td>
                                    <span class="p-date">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</span>
                                    <span class="p-time">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }} WIB</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar">{{ strtoupper(substr($p->supplier->name ?? 'A', 0, 2)) }}</div>
                                        <span class="sup-name">{{ $p->supplier->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    <i class="fas fa-receipt" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada transaksi pembelian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-info">
                    @if($purchases->total() > 0)
                        Menampilkan {{ ($purchases->currentPage()-1)*$purchases->perPage()+1 }}-{{ min($purchases->currentPage()*$purchases->perPage(), $purchases->total()) }} dari {{ $purchases->total() }} transaksi
                    @else
                        Tidak ada data
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
