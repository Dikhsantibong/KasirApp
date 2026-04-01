@extends('layouts.app')

@section('title', 'Dashboard Utama')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Small overrides for icons & custom spacing */
        .sidebar { overflow-y: auto; scrollbar-width: none; }
        .sidebar::-webkit-scrollbar { display: none; }
        .nav-link i { margin-right: 8px; }
    </style>
@endpush

@section('content')
<div class="dashboard-layout">
   <x-sidebar />

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari transaksi, produk, atau pelanggan...">
            </div>

            <div class="topbar-actions">
                <div class="status-sync">
                    <i class="fas fa-sync-alt"></i>
                    <span>Sinkronisasi Berhasil</span>
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; position: relative; cursor: pointer;">
                    <i class="far fa-bell"></i>
                    <span style="position:absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid white;"></span>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff" class="user-thumb" alt="User">
            </div>
        </header>

        <!-- Page Header -->
        <div class="section-title">
            <span class="subtitle">Dashboard Utama</span>
            <div class="main-heading">
                <h1>Ringkasan Hari Ini</h1>
                <div class="header-btns">
                    <a href="#" class="btn-white">
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
                    </a>
                    <a href="{{ route('kasir.index') }}" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Transaksi Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="card-stat">
                <div class="card-icon-title">
                    <div class="card-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <span class="card-label">Total Penjualan Hari Ini</span>
                <span class="card-value">Rp {{ number_format($todaySales, 0, ',', '.') }}</span>
            </div>

            <div class="card-stat">
                <div class="card-icon-title">
                    <div class="card-icon" style="background: rgba(45, 212, 191, 0.1); color: #2dd4bf;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <span class="card-label">Estimasi Keuntungan</span>
                <span class="card-value" style="color: #059669;">Rp {{ number_format($todayProfit, 0, ',', '.') }}</span>
            </div>

            <div class="card-stat">
                <div class="card-icon-title">
                    <div class="card-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    @if($lowStockCount > 0)
                        <span class="badge-tag" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Peringatan</span>
                    @endif
                </div>
                <span class="card-label">Stok Menipis</span>
                <span class="card-value">{{ $lowStockCount }} Produk</span>
            </div>

            <div class="card-stat">
                <div class="card-icon-title">
                    <div class="card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    @if($totalDebt > 0)
                        <span class="badge-tag" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Penting</span>
                    @endif
                </div>
                <span class="card-label">Hutang Pelanggan</span>
                <span class="card-value">Rp {{ number_format($totalDebt, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Dashboard Columns -->
        <div class="dashboard-columns">
            <!-- Left Column: Chart -->
            <div class="chart-card">
                <div style="display:flex; justify-content: space-between; margin-bottom: 2rem;">
                    <div>
                        <h3 style="font-weight: 700;">Grafik Penjualan Mingguan</h3>
                        <p style="color: #5e6c84; font-size: 0.85rem;">Performa transaksi 7 hari terakhir</p>
                    </div>
                </div>
                <div style="height: 300px; position:relative;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-stack" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="ai-card">
                    <h3><i class="fas fa-brain"></i> Insight Bisnis (AI)</h3>
                    <div class="busy-box">
                        <span style="font-size: 0.8rem; opacity: 0.9; display:block; margin-bottom: 8px;">JAM TERSIBUK HARI INI</span>
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                            <i class="far fa-clock"></i>
                            <span style="font-size: 1.1rem;">{{ $busyHour ? str_pad($busyHour->hour, 2, '0', STR_PAD_LEFT).':00' : '--:--' }}</span>
                        </div>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.8rem; opacity: 0.9; display:block; margin-bottom: 8px;">PRODUK TERLARIS</span>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($topProduct ? $topProduct->name : 'N/A') }}&background=000&color=fff" style="width: 40px; height:40px; border-radius: 8px;" alt="Product">
                            <div>
                                <span style="font-weight: 700; display:block;">{{ $topProduct->name ?? 'Belum ada transaksi' }}</span>
                                <span style="font-size: 0.75rem; opacity: 0.8;">{{ $topProduct->total_qty ?? 0 }} Terjual hari ini</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('insight.index') }}" class="btn-white-full">Lihat Semua Rekomendasi</a>
                </div>

                <div class="activity-card" style="background: white; padding: 1.5rem; border-radius: 16px; box-shadow: var(--shadow);">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                        <h3 style="font-size: 1rem; font-weight: 700;">Aktivitas Terakhir</h3>
                        <a href="{{ route('transaksi.index') }}"><i class="fas fa-external-link-alt" style="color: #94a3b8; cursor:pointer;"></i></a>
                    </div>
                    <div class="timeline" style="display: flex; flex-direction: column; gap: 1rem;">
                        @forelse($recentActivities as $activity)
                            <div style="display:flex; justify-content: space-between; align-items: flex-start; border-left: 3px solid #38a169; padding-left: 12px;">
                                <div>
                                    <span style="font-weight: 700; font-size: 0.9rem; display:block;">Penjualan Berhasil</span>
                                    <span style="font-size: 0.8rem; color: #5e6c84;">{{ strtoupper(substr($activity->id, 0, 8)) }} • {{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                                <span style="color: #0052cc; font-weight: 700; font-size: 0.9rem;">Rp {{ number_format($activity->total_amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p style="font-size: 0.85rem; color: #94a3b8; text-align: center;">Belum ada aktivitas hari ini</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(0, 82, 204, 0.2)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Penjualan',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#0052cc',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0052cc',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        backgroundColor: gradient,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { 
                                callback: function(value) {
                                    if (value >= 1000000) return value / 1000000 + 'M';
                                    if (value >= 1000) return value / 1000 + 'rb';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold', size: 11 }, color: '#5e6c84' }
                        }
                    }
                }
            });
        });
    </script>
@endpush
