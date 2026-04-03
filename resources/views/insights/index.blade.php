@extends('layouts.app')

@section('title', 'Insight Bisnis (AI)')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/insight.css') }}">
@endpush

@section('content')
<div class="insight-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Insight Bisnis (AI)">
            <x-slot:search>
                <form action="#" method="GET" style="width:100%;"><input type="text" placeholder="Cari insight atau data..."></form>
            </x-slot:search>
        </x-header>

        <div class="page-content">
            <div class="ai-badge"><i class="fas fa-sparkles"></i> POWERED BY AI ANALYSIS</div>
            <h1 class="page-title">Insight Bisnis (AI)</h1>
            <p class="page-subtitle">Kami menganalisis data Anda untuk memberikan saran strategis yang dapat meningkatkan profitabilitas toko Anda hari ini.</p>

            <div class="bento-grid">
                <!-- 1. Peluang Penjualan -->
                <div class="bento-card col-span-2">
                    <div class="peluang-header">
                        <div>
                            <h2>Peluang Penjualan Hari Ini</h2>
                            <p>Berdasarkan data transaksi dan produk aktif</p>
                        </div>
                        <div class="update-badge">Live Data</div>
                    </div>
                    <div class="peluang-stats">
                        <div class="p-stat-item">
                            <h5>PENDAPATAN HARI INI</h5>
                            <h3>Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                            <div class="p-stat-sub {{ $revenueChange >= 0 ? '' : 'gray' }}">
                                <i class="fas fa-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }}"></i> 
                                {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}% vs bulan lalu
                            </div>
                        </div>
                        <div class="p-stat-item">
                            <h5>TRANSAKSI HARI INI</h5>
                            <h3 class="dark">{{ $todayTransactionsCount }}</h3>
                            <div class="p-stat-sub gray">Total transaksi masuk</div>
                        </div>
                        <div class="p-stat-item">
                            <h5>TOTAL PRODUK</h5>
                            <h3 class="dark">{{ $totalProducts }}</h3>
                            <div class="p-stat-sub gray">Produk aktif di toko</div>
                        </div>
                    </div>
                    <div class="peluang-footer">
                        <div style="font-size:0.85rem; color:#64748b;">
                            <i class="fas fa-store"></i> {{ $totalCustomers }} pelanggan terdaftar
                        </div>
                        <a href="{{ route('laporan.index') }}" class="btn-blue">Lihat Laporan Detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- 2. Performa -->
                <div class="bento-card col-span-1 {{ $revenueChange < 0 ? 'performa-card' : '' }}" style="{{ $revenueChange >= 0 ? 'background:#ecfdf5; border:1px solid #d1fae5;' : '' }}">
                    @if($revenueChange >= 0)
                        <div style="width:36px; height:36px; background:#d1fae5; color:#10b981; border-radius:8px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Performa Positif!</h3>
                        <p>Pendapatan bulan ini <strong>naik {{ $revenueChange }}%</strong> dibanding bulan lalu. Tren penjualan sangat baik!</p>
                        <div style="margin-top:auto; color:#10b981; font-weight:800; font-size:0.75rem;"><i class="fas fa-check-circle"></i> PERFORMANYA BAGUS</div>
                    @else
                        <div class="icon-red-light"><i class="fas fa-chart-line"></i></div>
                        <h3>Performa Mingguan</h3>
                        <p>Penjualan menurun <strong>{{ $revenueChange }}%</strong> dibanding bulan lalu. Perlu evaluasi strategi.</p>
                        <div class="alert-text"><i class="fas fa-exclamation-triangle"></i> BUTUH PERHATIAN</div>
                    @endif
                </div>

                <!-- 3. Restock Cerdas -->
                <div class="bento-card col-span-1">
                    <div class="icon-box-orange"><i class="fas fa-box-open"></i></div>
                    <h3>Restock Cerdas</h3>
                    @if($lowStockProduct)
                        <p>Produk <strong>{{ $lowStockProduct->name }}</strong> hanya tersisa <strong>{{ $lowStockProduct->stock }} unit</strong>. Segera lakukan restock untuk menghindari kekosongan.</p>
                    @else
                        <p>Semua stok produk dalam kondisi aman. Tidak ada produk yang perlu restock saat ini.</p>
                    @endif
                    <a href="{{ route('stock.index') }}" class="btn-outline">CEK STOK</a>
                </div>

                <!-- 4. Waktu Teramai -->
                <div class="bento-card col-span-1">
                    <div class="icon-box-blue"><i class="far fa-clock"></i></div>
                    <h3>Waktu Teramai</h3>
                    @if($peakHour)
                        <p>Pelanggan paling sering bertransaksi pada jam <strong>{{ str_pad($peakHour->hour, 2, '0', STR_PAD_LEFT) }}:00</strong> ({{ $peakHour->cnt }} transaksi). Pastikan staf siap di jam tersebut.</p>
                    @else
                        <p>Belum ada data transaksi yang cukup untuk menganalisis jam ramai.</p>
                    @endif
                </div>

                <!-- 5. Optimasi / Saran -->
                <div class="bento-card col-span-1 optimasi-card">
                    <div class="saran-ai-badge"><i class="fas fa-star"></i> SARAN AI</div>
                    <h3>Optimasi Stok</h3>
                    @if($topProduct)
                        <p>Produk <u>{{ $topProduct->name }}</u> memiliki stok terbanyak ({{ $topProduct->stock }} unit). Pertimbangkan promo untuk mempercepat perputarannya.</p>
                    @else
                        <p>Tambahkan produk ke toko Anda dulu untuk mendapatkan rekomendasi AI.</p>
                    @endif
                </div>

                <!-- 6. Ringkasan -->
                <div class="bento-card col-span-2 sentimen-card">
                    <h2>Ringkasan Bisnis</h2>
                    <p>
                        Bulan ini toko memiliki <strong>{{ $totalProducts }} produk</strong> aktif, 
                        <strong>{{ $totalCustomers }} pelanggan</strong> terdaftar, 
                        dan <strong>{{ $overdueDebts }}</strong> hutang belum lunas.
                        Total pengeluaran operasional: <strong>Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</strong>.
                    </p>
                    <div class="mood-badges">
                        <div class="mood-badge happy"><i class="fas fa-box"></i> {{ $totalProducts }} Produk</div>
                        <div class="mood-badge neutral"><i class="fas fa-users"></i> {{ $totalCustomers }} Pelanggan</div>
                    </div>
                </div>

                <!-- 7. Hutang Alert -->
                <div class="bento-card col-span-1 ekspansi-card">
                    <div class="icon-box-rocket">
                        <i class="fas {{ $overdueDebts > 0 ? 'fa-exclamation-triangle' : 'fa-rocket' }}"></i>
                    </div>
                    <h3>{{ $overdueDebts > 0 ? 'Hutang Jatuh Tempo' : 'Hutang Aman' }}</h3>
                    <p>
                        @if($overdueDebts > 0)
                            Ada <strong>{{ $overdueDebts }}</strong> hutang pelanggan yang sudah melewati jatuh tempo. Segera lakukan penagihan.
                        @else
                            Tidak ada hutang yang melewati jatuh tempo. Keuangan pelanggan terkontrol!
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="ai-float-widget">
    <div class="ai-float-icon"><i class="fas fa-robot"></i></div>
    <div class="ai-float-text">
        <h5>TANYA AI</h5>
        <p>Pendapatan hari ini: Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="ai-float-arrow"><i class="fas fa-comment-dots" style="font-size:1.25rem;"></i></div>
</div>
@endsection
