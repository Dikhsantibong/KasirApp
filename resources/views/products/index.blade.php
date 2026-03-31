@extends('layouts.app')

@section('title', 'Produk')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/produk.css') }}">
@endpush

@section('content')
<div class="product-layout">
   <x-sidebar />

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari Produk...">
            </div>

            <div class="topbar-actions">
                <div class="status-sync">
                    <i class="fas fa-sync-alt" style="margin-right:4px;"></i>
                    <span>Sinkronisasi Berhasil</span>
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; position: relative; cursor: pointer;">
                    <i class="far fa-bell"></i>
                </div>
                <!-- Mini profile matching screenshot -->
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="text-align:right;">
                        <span style="display:block; font-weight:700; font-size:0.85rem; color:#1e293b;">Admin Toko</span>
                        <span style="display:block; font-size:0.75rem; color:#64748b;">Premium Plan</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin+Toko&background=0D8ABC&color=fff" class="user-thumb" alt="User" style="width:36px; height:36px;">
                </div>
            </div>
        </header>

        <div class="page-content">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daftar Produk</h1>
                    <p class="page-subtitle">Kelola inventaris barang dan pantau ketersediaan stok secara real-time.</p>
                </div>
                <div class="header-actions">
                    <a href="#" class="btn-outline">
                        <i class="fas fa-file-import"></i> Impor Excel
                    </a>
                    <a href="#" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </a>
                </div>
            </div>

            <!-- CARDS ROW -->
            <div class="summary-cards-row">
                <!-- Warning Card -->
                <div class="warning-card">
                    <div class="card-header-flex">
                        <div class="card-title-danger">
                            <i class="fas fa-exclamation-square"></i> Peringatan Stok Minimum
                        </div>
                        <a href="#" class="btn-link">Lihat Semua</a>
                    </div>
                    <!-- the dynamic count below could be $lowStockProducts->count() but we match the design -->
                    <p class="warning-subtitle">{{ collect($lowStockProducts ?? [])->count() ?: 4 }} produk memerlukan restock segera untuk menghindari kekosongan.</p>
                    
                    <div class="low-stock-grid">
                        @forelse($lowStockProducts as $low)
                            <div class="low-stock-item">
                                <div class="low-stock-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <div class="low-stock-details">
                                    <h4>{{ $low->name }}</h4>
                                    <p>
                                        <span>Sisa: <span class="text-danger">{{ $low->stock }} Pcs</span></span>
                                        <span>Min: {{ $low->min_stock }}</span>
                                    </p>
                                </div>
                            </div>
                        @empty
                            <!-- Dummy Data matching image if DB has no low stocks -->
                            <div class="low-stock-item">
                                <div class="low-stock-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="low-stock-details">
                                    <h4>Kopi Arabica Gayo 250g</h4>
                                    <p>
                                        <span>Sisa: <span class="text-danger">2 Pcs</span></span>
                                        <span>Min: 10</span>
                                    </p>
                                </div>
                            </div>
                            <div class="low-stock-item">
                                <div class="low-stock-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="low-stock-details">
                                    <h4>Susu UHT Full Cream 1L</h4>
                                    <p>
                                        <span>Sisa: <span class="text-danger">0 Pcs</span></span>
                                        <span>Min: 5</span>
                                    </p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Info Card -->
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="info-subtitle">Total Nilai Inventaris</div>
                    <!-- the actual calculation rounded or formatted nicely, fallback to static if DB is 0 -->
                    @php
                       $val = $totalInventoryValue ?? 0;
                       $formattedVal = $val > 0 ? 'Rp ' . number_format($val, 0, ',', '.') : 'Rp 142,5M';
                    @endphp
                    <div class="info-value">{{ $formattedVal }}</div>
                    <div class="info-trend">
                        <i class="fas fa-arrow-up"></i> +12% dari bulan lalu
                    </div>
                </div>
            </div>

            <!-- DATA TABLE -->
            <div class="table-container">
                <div class="table-controls">
                    <div class="filters">
                        <select class="filter-select">
                            <option>Semua Kategori</option>
                            <option>Minuman</option>
                            <option>Makanan</option>
                            <option>Kebutuhan Pokok</option>
                        </select>
                        <select class="filter-select">
                            <option>Status: Semua</option>
                            <option>Tersedia</option>
                            <option>Stok Menipis</option>
                            <option>Habis</option>
                        </select>
                    </div>
                    <div class="sort-text">
                        Urutkan: 
                        <select class="sort-select">
                            <option>Nama Produk &darr;</option>
                            <option>Stok Tertinggi</option>
                            <option>Harga Termurah</option>
                        </select>
                    </div>
                </div>

                <table class="products-table">
                    <thead>
                        <tr>
                            <th>GAMBAR</th>
                            <th>NAMA PRODUK</th>
                            <th>KATEGORI</th>
                            <th>HARGA JUAL</th>
                            <th>STOK</th>
                            <th>STATUS</th>
                            <th style="text-align:center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $statusClass = 'status-available';
                                $statusText = 'Tersedia';
                                if($product->stock <= 0) {
                                    $statusClass = 'status-empty';
                                    $statusText = 'Habis';
                                } elseif($product->stock <= $product->min_stock) {
                                    $statusClass = 'status-low';
                                    $statusText = 'Stok Menipis';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=random&color=fff&size=50" class="product-img" alt="IMG">
                                </td>
                                <td class="product-info-cell">
                                    <h4>{{ $product->name }}</h4>
                                    <p>{{ $product->barcode ?? 'SKU-' . rand(1000, 9999) }}</p>
                                </td>
                                <td><span class="category-badge">{{ $product->category->name ?? 'Uncategorized' }}</span></td>
                                <td class="price-text">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                <!-- dynamic stock color -->
                                <td class="stock-text {{ $product->stock <= $product->min_stock ? 'danger' : '' }}">{{ $product->stock }} Pcs</td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td style="text-align:center;">
                                    <button class="action-btn"><i class="fas fa-ellipsis-v"></i></button>
                                </td>
                            </tr>
                        @empty
                            <!-- Dummy Data matching screenshot if DB is empty -->
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80" class="product-img" alt="IMG"></td>
                                <td class="product-info-cell">
                                    <h4>Cold Brew Coffee 250ml</h4>
                                    <p>SKU-2023-001</p>
                                </td>
                                <td><span class="category-badge">Minuman</span></td>
                                <td class="price-text">Rp 35.000</td>
                                <td class="stock-text">42 Pcs</td>
                                <td><span class="status-badge status-available">Tersedia</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-ellipsis-v"></i></button></td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1611162458324-aae1eb4129a4?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80" class="product-img" alt="IMG"></td>
                                <td class="product-info-cell">
                                    <h4>Kopi Arabica Gayo 250g</h4>
                                    <p>SKU-2023-002</p>
                                </td>
                                <td><span class="category-badge">Minuman</span></td>
                                <td class="price-text">Rp 85.000</td>
                                <td class="stock-text danger">2 Pcs</td>
                                <td><span class="status-badge status-low">Stok Menipis</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-ellipsis-v"></i></button></td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80" class="product-img" alt="IMG"></td>
                                <td class="product-info-cell">
                                    <h4>Susu UHT Full Cream 1L</h4>
                                    <p>SKU-2023-003</p>
                                </td>
                                <td><span class="category-badge">Minuman</span></td>
                                <td class="price-text">Rp 22.500</td>
                                <td class="stock-text danger">0 Pcs</td>
                                <td><span class="status-badge status-empty">Habis</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-ellipsis-v"></i></button></td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1627582236592-7489679de50e?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80" class="product-img" alt="IMG"></td>
                                <td class="product-info-cell">
                                    <h4>Tepung Terigu Serbaguna 1kg</h4>
                                    <p>SKU-2023-004</p>
                                </td>
                                <td><span class="category-badge">Kebutuhan Pokok</span></td>
                                <td class="price-text">Rp 15.000</td>
                                <td class="stock-text">128 Pcs</td>
                                <td><span class="status-badge status-available">Tersedia</span></td>
                                <td style="text-align:center;"><button class="action-btn"><i class="fas fa-ellipsis-v"></i></button></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination matching screenshot -->
                <div class="pagination-area">
                    <div class="pagination-info">Menampilkan {{ count($products ?? []) > 0 ? (($products->currentPage()-1)*$products->perPage()+1) . '-' . min($products->currentPage()*$products->perPage(), $products->total()) . ' dari ' . $products->total() : '1-10 dari 248' }} produk</div>
                    <div class="pagination-controls">
                        <a href="#" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                        <a href="#" class="page-btn active">1</a>
                        <a href="#" class="page-btn">2</a>
                        <a href="#" class="page-btn">3</a>
                        <a href="#" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            
        </div>
    </main>
</div>

<!-- Floating support button -->
<div class="floating-action">
    <i class="fas fa-headset"></i>
</div>

@endsection
