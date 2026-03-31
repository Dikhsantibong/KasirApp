@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
    <style>
        .detail-overlay {
            position:fixed; top:0; left:0; right:0; bottom:0;
            background:rgba(0,0,0,0.5); z-index:9998;
            display:none; align-items:center; justify-content:center;
        }
        .detail-overlay.show { display:flex; }
        .detail-card {
            background:white; border-radius:20px; padding:2rem;
            width:480px; max-height:80vh; overflow-y:auto;
            box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }
        .detail-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .detail-header h2 { font-size:1.2rem; font-weight:800; color:#1e293b; margin:0; }
        .detail-close { background:none; border:none; font-size:1.25rem; color:#94a3b8; cursor:pointer; }
        .detail-meta { display:flex; gap:1.5rem; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px dashed #e2e8f0; }
        .detail-meta-item span { display:block; font-size:0.7rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; }
        .detail-meta-item strong { font-size:0.9rem; color:#1e293b; }
        .detail-items { margin-bottom:1.5rem; }
        .detail-item-row { display:flex; justify-content:space-between; align-items:center; padding:0.75rem 0; border-bottom:1px solid #f1f5f9; }
        .detail-item-name { font-weight:600; color:#1e293b; font-size:0.9rem; }
        .detail-item-qty { font-size:0.8rem; color:#94a3b8; }
        .detail-item-price { font-weight:700; color:#0052cc; }
        .detail-total { display:flex; justify-content:space-between; padding:1rem 0; border-top:2px solid #1e293b; font-weight:800; font-size:1.1rem; color:#1e293b; }
    </style>
@endpush

@section('content')
<div class="transaksi-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="{{ route('transaksi.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari ID Transaksi..." value="{{ request('search') }}">
                </form>
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
                        <span style="display:block; font-weight:700; font-size:0.85rem; color:#1e293b;">{{ auth()->user()->name ?? 'Admin Toko' }}</span>
                        <span style="display:block; font-size:0.75rem; color:#64748b;">Premium Plan</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" class="user-thumb" alt="User" style="width:36px; height:36px; border-radius:50%;">
                </div>
            </div>
        </header>

        <div class="page-content">
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
                    <div class="stat-icon icon-blue"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3>Pemasukan Hari Ini</h3>
                        <p>Rp {{ number_format($todayTotal, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-info">
                        <h3>Pemasukan Bulan Ini</h3>
                        <p>Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-info">
                        <h3>Transaksi Hari Ini</h3>
                        <p>{{ $todayCount }} Trx</p>
                    </div>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="table-container">
                <div class="table-controls">
                    <div class="filters">
                        <form action="{{ route('transaksi.index') }}" method="GET" style="display:flex; gap:0.75rem; align-items:center;">
                            <select name="status" class="filter-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            <select name="method" class="filter-select" onchange="this.form.submit()">
                                <option value="">Metode: Semua</option>
                                <option value="Tunai" {{ request('method') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="QRIS" {{ request('method') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                <option value="Transfer" {{ request('method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            <input type="date" name="date" value="{{ request('date') }}" class="filter-select" style="background:white; color:#1e293b;" onchange="this.form.submit()">
                            @if(request('status') || request('method') || request('date'))
                                <a href="{{ route('transaksi.index') }}" style="color:#ef4444; font-size:0.8rem; font-weight:700; text-decoration:none;">✕ Reset</a>
                            @endif
                        </form>
                    </div>
                    <div>
                        <span style="font-size:0.85rem; color:#64748b;">Total: <strong>{{ $transactions->total() }} transaksi</strong></span>
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
                                $statusClass = 'status-success'; $statusText = 'Lunas';
                                if(strtolower($trx->status ?? '') == 'pending') { $statusClass = 'status-pending'; $statusText = 'Pending'; }
                                elseif(in_array(strtolower($trx->status ?? ''), ['failed', 'dibatalkan', 'gagal'])) { $statusClass = 'status-failed'; $statusText = 'Gagal'; }
                            @endphp
                            <tr>
                                <td>
                                    <span class="trx-id">#POS-{{ strtoupper(substr($trx->id, 0, 8)) }}</span>
                                    <span class="trx-date">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}</span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($trx->user->name ?? 'Kasir') }}&background=random&color=fff" alt="Kasir">
                                        <span>{{ $trx->user->name ?? 'Kasir' }}</span>
                                    </div>
                                </td>
                                <td class="price-text">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                                <td><span class="payment-method">{{ $trx->payment_method ?? 'Tunai' }}</span></td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td style="text-align:center;">
                                    <button class="action-btn" onclick='showDetail(@json($trx))'><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 3rem; color:#94a3b8;">
                                    <i class="fas fa-receipt" style="font-size:2.5rem; opacity:0.2; display:block; margin-bottom:1rem;"></i>
                                    Belum ada transaksi. Lakukan penjualan melalui menu <strong>Kasir</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- REAL PAGINATION -->
                <div class="pagination-area">
                    <div class="pagination-info">
                        @if($transactions->total() > 0)
                            Menampilkan {{ ($transactions->currentPage()-1)*$transactions->perPage()+1 }}-{{ min($transactions->currentPage()*$transactions->perPage(), $transactions->total()) }} dari {{ $transactions->total() }} transaksi
                        @else
                            Tidak ada data transaksi
                        @endif
                    </div>
                    <div class="pagination-controls">
                        @if($transactions->onFirstPage())
                            <span class="page-btn" style="opacity:0.4;"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $transactions->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                        @endif
                        @foreach($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $transactions->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="page-btn" style="opacity:0.4;"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- DETAIL MODAL -->
<div class="detail-overlay" id="detailOverlay">
    <div class="detail-card">
        <div class="detail-header">
            <h2><i class="fas fa-receipt" style="color:#0052cc; margin-right:8px;"></i> Detail Transaksi</h2>
            <button class="detail-close" onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>
        <div class="detail-meta">
            <div class="detail-meta-item"><span>ID</span><strong id="det-id"></strong></div>
            <div class="detail-meta-item"><span>Kasir</span><strong id="det-kasir"></strong></div>
            <div class="detail-meta-item"><span>Metode</span><strong id="det-method"></strong></div>
            <div class="detail-meta-item"><span>Status</span><strong id="det-status"></strong></div>
        </div>
        <div class="detail-items" id="det-items"></div>
        <div class="detail-total">
            <span>TOTAL</span>
            <span id="det-total"></span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n); }

    function showDetail(trx) {
        document.getElementById('det-id').textContent = '#POS-' + trx.id.substring(0, 8).toUpperCase();
        document.getElementById('det-kasir').textContent = trx.user ? trx.user.name : 'Kasir';
        document.getElementById('det-method').textContent = trx.payment_method || 'Tunai';
        document.getElementById('det-status').textContent = trx.status || 'Lunas';
        document.getElementById('det-total').textContent = formatRp(trx.total_amount);

        let itemsHtml = '';
        if (trx.items && trx.items.length > 0) {
            trx.items.forEach(function(item) {
                const prodName = item.product ? item.product.name : 'Produk #' + item.product_id.substring(0,6);
                itemsHtml += '<div class="detail-item-row">' +
                    '<div><div class="detail-item-name">' + prodName + '</div>' +
                    '<div class="detail-item-qty">' + item.qty + ' x ' + formatRp(item.price) + '</div></div>' +
                    '<div class="detail-item-price">' + formatRp(item.subtotal) + '</div></div>';
            });
        } else {
            itemsHtml = '<div style="text-align:center; color:#94a3b8; padding:1rem;">Detail item tidak tersedia</div>';
        }
        document.getElementById('det-items').innerHTML = itemsHtml;

        document.getElementById('detailOverlay').classList.add('show');
    }

    function closeDetail() {
        document.getElementById('detailOverlay').classList.remove('show');
    }

    document.getElementById('detailOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });
</script>
@endpush
