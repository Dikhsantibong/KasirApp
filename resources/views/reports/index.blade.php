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
                <form action="#" method="GET" style="width:100%;">
                    <input type="text" placeholder="Cari laporan atau data...">
                </form>
            </div>
            
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-sync-alt"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="text-align:right;">
                        <span style="display:block; font-weight:700; font-size:0.85rem; color:#1e293b;">Admin Utama</span>
                        <span style="display:block; font-size:0.75rem; color:#94a3b8;">Owner</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random&color=fff" style="width:36px; height:36px; border-radius:50%;">
                </div>
            </div>
        </header>

        <div class="page-content" style="padding-left: 2.5rem;">
            <div class="breadcrumb-reports">
                MENU UTAMA &nbsp;&rsaquo;&nbsp; <span>LAPORAN BISNIS</span>
            </div>
            
            <div class="page-header">
                <div>
                    <h1 class="page-title">Laporan Bisnis</h1>
                </div>
                
                <div class="header-right">
                    <div class="date-filters">
                        <button class="date-btn active">Harian</button>
                        <button class="date-btn">Mingguan</button>
                        <button class="date-btn">Bulanan</button>
                        <button class="date-btn"><i class="far fa-calendar-alt"></i> Custom Tanggal</button>
                    </div>
                    <button class="btn-primary-solid">
                        <i class="fas fa-download"></i> Download Laporan
                    </button>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="kpi-trend">+12.5%</div>
                    </div>
                    <div class="kpi-title">TOTAL PENJUALAN</div>
                    <h2 class="kpi-amount">Rp {{ number_format($totalPenjualan > 0 ? $totalPenjualan : 42500000, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">Dibandingkan kemarin: <strong>Rp 37.8M</strong></div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <div class="kpi-icon icon-purple"><i class="fas fa-percentage"></i></div>
                        <div class="kpi-trend">+4.2%</div>
                    </div>
                    <div class="kpi-title">TOTAL MARGIN</div>
                    <h2 class="kpi-amount">Rp 12.840.000</h2>
                    <div class="kpi-footer">Rata-rata margin: <strong>30.2%</strong></div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <div class="kpi-icon icon-orange"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div class="kpi-trend danger">+18%</div>
                    </div>
                    <div class="kpi-title">TOTAL PENGELUARAN</div>
                    <h2 class="kpi-amount">Rp {{ number_format($totalPengeluaran > 0 ? $totalPengeluaran : 8200000, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">Operasional utama: <strong>Gaji & Listrik</strong></div>
                </div>

                <div class="kpi-card blue-theme">
                    <div class="kpi-header">
                        <div class="kpi-icon" style="background:rgba(255,255,255,0.2); color:white;"><i class="fas fa-wallet"></i></div>
                        <div class="target-badge">Target: 85%</div>
                    </div>
                    <div class="kpi-title">KEUNTUNGAN BERSIH</div>
                    <h2 class="kpi-amount">Rp {{ number_format($keuntunganBersih != 0 ? $keuntunganBersih : 34300000, 0, ',', '.') }}</h2>
                    <div class="kpi-footer">Profitabilitas: <strong>Sangat Baik</strong></div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- CHART VISUALIZATION (CSS Mockup perfectly mimicking screenshot) -->
                <div class="chart-container">
                    <div class="chart-header">
                        <div>
                            <h3>Penjualan vs Pengeluaran</h3>
                            <p>Visualisasi arus kas operasional 7 hari terakhir</p>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item"><div class="dot-blue"></div> Penjualan</div>
                            <div class="legend-item"><div class="dot-orange"></div> Pengeluaran</div>
                        </div>
                    </div>
                    
                    <div style="padding-left: 45px; margin-top: 30px;">
                        <div class="css-bar-chart">
                            <!-- Y-Axis grids -->
                            <div class="y-axis-bg">
                                <div class="y-line"><span>50M</span></div>
                                <div class="y-line"><span>40M</span></div>
                                <div class="y-line"><span>30M</span></div>
                                <div class="y-line"><span>20M</span></div>
                                <div class="y-line"><span>10M</span></div>
                            </div>
                            
                            <!-- X-Axis Bars base on visual design proportion -->
                            <div class="chart-day">
                                <div class="bar-blue" style="height: 90%;"></div>
                                <div class="bar-orange" style="height: 0%;"></div>
                                <div class="day-label">SEN</div>
                            </div>
                            <div class="chart-day">
                                <div class="bar-blue" style="height: 55%;"></div>
                                <div class="bar-orange" style="height: 40%;"></div>
                                <div class="day-label">SEL</div>
                            </div>
                            <div class="chart-day">
                                <div class="bar-blue" style="height: 100%;"></div>
                                <div class="bar-orange" style="height: 80%;"></div>
                                <div class="day-label">RAB</div>
                            </div>
                            <div class="chart-day">
                                <div class="bar-blue" style="height: 75%;"></div>
                                <div class="bar-orange" style="height: 55%;"></div>
                                <div class="day-label">KAM</div>
                            </div>
                            <div class="chart-day">
                                <div class="bar-blue" style="height: 45%;"></div>
                                <div class="bar-orange" style="height: 35%;"></div>
                                <div class="day-label">JUM</div>
                            </div>
                            <div class="chart-day">
                                <div class="bar-blue" style="height: 80%;"></div>
                                <div class="bar-orange" style="height: 45%;"></div>
                                <div class="day-label">SAB</div>
                            </div>
                            <div class="chart-day active">
                                <div class="bar-blue" style="height: 70%;"></div>
                                <div class="bar-orange" style="height: 30%;"></div>
                                <div class="day-label">MIN</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANELS -->
                <div class="side-panel">
                    <div class="margin-card">
                        <h3>Produk Margin Tertinggi <i class="fas fa-arrow-trend-up" style="color:#d1d5db;"></i></h3>
                        <div class="margin-list">
                            <div class="margin-item">
                                <div class="margin-item-left">
                                    <div class="badge-gray">A1</div>
                                    <div class="margin-item-info">
                                        <h4>Kopi Gula Aren</h4>
                                        <p>Margin: 65%</p>
                                    </div>
                                </div>
                                <div class="margin-val">Rp 12,5k</div>
                            </div>
                            <div class="margin-item">
                                <div class="margin-item-left">
                                    <div class="badge-gray">B2</div>
                                    <div class="margin-item-info">
                                        <h4>Croissant Keju</h4>
                                        <p>Margin: 48%</p>
                                    </div>
                                </div>
                                <div class="margin-val">Rp 18,2k</div>
                            </div>
                            <div class="margin-item">
                                <div class="margin-item-left">
                                    <div class="badge-gray">C3</div>
                                    <div class="margin-item-info">
                                        <h4>Matcha Latte</h4>
                                        <p>Margin: 52%</p>
                                    </div>
                                </div>
                                <div class="margin-val">Rp 15,0k</div>
                            </div>
                        </div>
                    </div>

                    <div class="ai-card">
                        <div class="ai-header">
                            <div class="ai-icon"><i class="fas fa-magic"></i></div>
                            <h4>Analisa AI</h4>
                        </div>
                        <p>Pengeluaran operasional Anda meningkat <strong>12%</strong> pada akhir pekan. Disarankan untuk optimasi stok bahan baku kopi untuk meningkatkan profit.</p>
                        <a href="#" class="btn-outline-blue">Lihat Rekomendasi</a>
                    </div>
                </div>
            </div>

            <!-- RECENT TRANSACTIONS TABLE -->
            <div class="recent-transactions-card">
                <div class="recent-header">
                    <div>
                        <h3>Transaksi Terbaru</h3>
                        <p>Daftar 10 transaksi terakhir hari ini</p>
                    </div>
                    <a href="#" class="link-all">Lihat Semua Transaksi</a>
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
                            @php
                                $statusClass = strtolower($rt->status) == 'pending' ? 'pending' : '';
                            @endphp
                            <tr>
                                <td><span class="td-id">#POS-{{ substr($rt->id, 0, 5) }}</span></td>
                                <td><span class="td-time">{{ \Carbon\Carbon::parse($rt->created_at)->format('H:i WIB') }}</span></td>
                                <td>
                                    <div class="pay-method">
                                        <i class="fas fa-credit-card"></i> {{ $rt->payment_method ?? 'Tunai' }}
                                    </div>
                                </td>
                                <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($rt->status ?? 'Lunas') }}</span></td>
                                <td style="text-align:right;"><span class="td-total">Rp {{ number_format($rt->total_amount, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <!-- EXACT MATCH DUMMY IF DB EMPTY -->
                            <tr>
                                <td><span class="td-id">#POS-82910</span></td>
                                <td><span class="td-time">14:20 WIB</span></td>
                                <td><div class="pay-method"><i class="fas fa-qrcode"></i> QRIS</div></td>
                                <td><span class="status-badge">Lunas</span></td>
                                <td style="text-align:right;"><span class="td-total">Rp 124.000</span></td>
                            </tr>
                            <tr>
                                <td><span class="td-id">#POS-82909</span></td>
                                <td><span class="td-time">14:05 WIB</span></td>
                                <td><div class="pay-method"><i class="fas fa-money-bill-wave" style="color:#10b981;"></i> Tunai</div></td>
                                <td><span class="status-badge">Lunas</span></td>
                                <td style="text-align:right;"><span class="td-total">Rp 45.000</span></td>
                            </tr>
                            <tr>
                                <td><span class="td-id">#POS-82908</span></td>
                                <td><span class="td-time">13:50 WIB</span></td>
                                <td><div class="pay-method"><i class="fas fa-credit-card" style="color:#3b82f6;"></i> Kartu Debit</div></td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td style="text-align:right;"><span class="td-total">Rp 820.500</span></td>
                            </tr>
                            <tr>
                                <td><span class="td-id">#POS-82907</span></td>
                                <td><span class="td-time">13:32 WIB</span></td>
                                <td><div class="pay-method"><i class="fas fa-qrcode"></i> QRIS</div></td>
                                <td><span class="status-badge">Lunas</span></td>
                                <td style="text-align:right;"><span class="td-total">Rp 56.000</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>
@endsection
