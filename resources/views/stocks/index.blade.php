@extends('layouts.app')

@section('title', 'Monitoring Stock')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
<div class="stock-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Monitoring Stock">
            <x-slot:search>
                <form action="{{ route('stock.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari berdasarkan nama atau SKU..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Monitoring Stock</h1>
                    <p class="page-subtitle">Pantau ketersediaan barang, SKU, dan status persediaan di gudang/toko.</p>
                </div>
                <div>
                    <a href="{{ route('produk.index') }}" class="btn-primary">
                        <i class="fas fa-boxes"></i> Kelola Produk
                    </a>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Identitas Barang</h3>
                        <p>{{ number_format($products->total(), 0, ',', '.') }} SKU</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Peringatan Stock Minimum</h3>
                        <p>{{ $lowStockItems ?? 0 }} Barang</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Nilai Inventaris (Modal)</h3>
                        <p>Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="stock-table">
                    <thead>
                        <tr>
                            <th>NAMA & SKU</th>
                            <th>KATEGORI</th>
                            <th>SISA STOCK</th>
                            <th>HARGA JUAL</th>
                            <th>NILAI ASSET</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            @php
                                $isDanger = $p->stock <= $p->min_stock;
                            @endphp
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=random" class="product-img">
                                        <div class="product-info-cell">
                                            <h4>{{ $p->name }}</h4>
                                            <p>{{ $p->barcode ?? 'SKU-0000' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="category-badge">{{ $p->category->name ?? 'Uncategorized' }}</span></td>
                                <td>
                                    <span class="stock-val {{ $isDanger ? 'danger' : 'success' }}">{{ $p->stock }} Pcs</span>
                                    <span class="min-stock-badge">Min. {{ $p->min_stock }} Pcs</span>
                                </td>
                                <td style="font-weight:600; color:#475569;">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                <td style="font-weight:700; color:#1e293b;">Rp {{ number_format($p->selling_price * $p->stock, 0, ',', '.') }}</td>
                                <td>
                                    @if($isDanger)
                                        <span class="status-badge status-danger"><i class="fas fa-exclamation-circle"></i> Restock</span>
                                    @else
                                        <span class="status-badge status-safe"><i class="fas fa-check-circle"></i> Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 2rem; color:#94a3b8;">
                                    Belum ada data barang. Silakan tambahkan barang di menu Produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-area">
                    <div style="font-size:0.85rem; color:#64748b;">
                        Menampilkan {{ count($products) }} item
                    </div>
                    <div>
                        {{ $products->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
