@extends('layouts.app')

@section('title', 'Laporan Bisnis')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@section('content')
<div class="laporan-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="#" method="GET" style="width:100%;"><input type="text" placeholder="Cari laporan atau data..."></form>
            </div>
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-sync-alt"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="text-align:right;">
                        <span style="display:block; font-weight:700; font-size:0.85rem; color:#1e293b;">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span style="display:block; font-size:0.75rem; color:#94a3b8;">Owner</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=random&color=fff" style="width:36px; height:36px; border-radius:50%;">
                </div>
            </div>
        </header>

        <div class="page-content" style="padding-left: 2.5rem;">
            <div class="breadcrumb-reports">MENU UTAMA &nbsp;&rsaquo;&nbsp; <span>LAPORAN BISNIS</span></div>
            
            <div class="page-header">
                <div><h1 class="page-title">Laporan Bisnis</h1></div>
                <div class="header-right">
                    <button class="btn-primary-solid"><i class="fas fa-download"></i> Download Laporan</button>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                    <div class="kpi-title">TOTAL PENJUALAN</div>
                    <h2 class="kpi-amount">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">{{ $totalTransaksi }} transaksi bulan ini</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <div class="kpi-icon icon-purple"><i class="fas fa-percentage"></i></div>
                    </div>
                    <div class="kpi-title">TOTAL MARGIN</div>
                    @php
                        $margin = $totalPenjualan > 0 ? $totalPenjualan - $totalPengeluaran : 0;
                        $marginPct = $totalPenjualan > 0 ? round(($margin / $totalPenjualan) * 100, 1) : 0;
                    @endphp
                    <h2 class="kpi-amount">Rp {{ number_format($margin, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">Margin: <strong>{{ $marginPct }}%</strong></div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <div class="kpi-icon icon-orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    </div>
                    <div class="kpi-title">TOTAL PENGELUARAN</div>
                    <h2 class="kpi-amount">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">Operasional bulan ini</div>
                </div>

                <div class="kpi-card blue-theme">
                    <div class="kpi-header">
                        <div class="kpi-icon" style="background:rgba(255,255,255,0.2); color:white;"><i class="fas fa-wallet"></i></div>
                    </div>
                    <div class="kpi-title">KEUNTUNGAN BERSIH</div>
                    <h2 class="kpi-amount">Rp {{ number_format($keuntunganBersih, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">{{ $keuntunganBersih >= 0 ? 'Profitable' : 'Rugi' }}</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- CHART -->
                <div class="chart-container">
                    <div class="chart-header">
                        <div>
                            <h3>Penjualan vs Pengeluaran</h3>
                            <p>Visualisasi arus kas 7 hari terakhir</p>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item"><div class="dot-blue"></div> Penjualan</div>
                            <div class="legend-item"><div class="dot-orange"></div> Pengeluaran</div>
                        </div>
                    </div>
                    <div style="padding-left: 45px; margin-top: 30px;">
                        <div class="css-bar-chart">
                            <div class="y-axis-bg">
                                @for($y = 5; $y >= 1; $y--)
                                    <div class="y-line"><span>{{ round(($maxChart / 5) * $y / 1000000, 1) }}M</span></div>
                                @endfor
                            </div>
                            @foreach($dailySales as $i => $sale)
                                @php
                                    $saleH = $maxChart > 0 ? round(($sale / $maxChart) * 100) : 0;
                                    $expH = $maxChart > 0 ? round(($dailyExpenses[$i] / $maxChart) * 100) : 0;
                                    $todayIdx = Carbon\Carbon::now()->subDays(6 - $i)->dayOfWeekIso;
                                    $labels = ['','SEN','SEL','RAB','KAM','JUM','SAB','MIN'];
                                @endphp
                                <div class="chart-day {{ $i == 6 ? 'active' : '' }}">
                                    <div class="bar-blue" style="height: {{ $saleH }}%;"></div>
                                    <div class="bar-orange" style="height: {{ $expH }}%;"></div>
                                    <div class="day-label">{{ $labels[$todayIdx] ?? 'N/A' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- SIDE PANELS -->
                <div class="side-panel">
                    <div class="margin-card">
                        <h3>Produk Margin Tertinggi</h3>
                        <div class="margin-list">
                            @forelse($topMarginProducts as $tp)
                                <div class="margin-item">
                                    <div class="margin-item-left">
                                        <div class="badge-gray">{{ strtoupper(substr($tp->name, 0, 2)) }}</div>
                                        <div class="margin-item-info">
                                            <h4>{{ $tp->name }}</h4>
                                            <p>Margin: {{ $tp->margin_pct }}%</p>
                                        </div>
                                    </div>
                                    <div class="margin-val">Rp {{ number_format($tp->margin, 0, ',', '.') }}</div>
                                </div>
                            @empty
                                <div style="text-align:center; color:#94a3b8; padding:1rem;">Belum ada data margin produk.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="ai-card">
                        <div class="ai-header">
                            <div class="ai-icon"><i class="fas fa-magic"></i></div>
                            <h4>Analisa AI</h4>
                        </div>
                        <p>
                            @if($keuntunganBersih > 0)
                                Bisnis Anda berjalan <strong>positif</strong> bulan ini dengan keuntungan Rp {{ number_format($keuntunganBersih, 0, ',', '.') }}.
                            @else
                                Perhatian: pengeluaran melebihi pendapatan. Pertimbangkan efisiensi biaya operasional.
                            @endif
                        </p>
                        <a href="{{ route('insight.index') }}" class="btn-outline-blue">Lihat Insight AI</a>
                    </div>
                </div>
            </div>

            <!-- RECENT TRANSACTIONS -->
            <div class="recent-transactions-card">
                <div class="recent-header">
                    <div>
                        <h3>Transaksi Terbaru</h3>
                        <p>10 transaksi terakhir</p>
                    </div>
                    <a href="{{ route('transaksi.index') }}" class="link-all">Lihat Semua Transaksi</a>
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID TRANSAKSI</th>
                            <th>WAKTU</th>
                            <th>METODE BAYAR</th>
                            <th>STATUS</th>
                            <th style="text-align:right;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $rt)
                            <tr>
                                <td><span class="td-id">#POS-{{ strtoupper(substr($rt->id, 0, 5)) }}</span></td>
                                <td><span class="td-time">{{ \Carbon\Carbon::parse($rt->created_at)->format('d M, H:i') }}</span></td>
                                <td><div class="pay-method"><i class="fas fa-credit-card"></i> {{ $rt->payment_method ?? 'Tunai' }}</div></td>
                                <td><span class="status-badge {{ strtolower($rt->status ?? '') == 'pending' ? 'pending' : '' }}">{{ ucfirst($rt->status ?? 'Lunas') }}</span></td>
                                <td style="text-align:right;"><span class="td-total">Rp {{ number_format($rt->total_amount, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:2rem; color:#94a3b8;">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
