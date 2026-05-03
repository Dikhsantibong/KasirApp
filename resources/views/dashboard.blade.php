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
        <x-header title="Dashboard Utama" searchPlaceholder="Cari transaksi, produk, atau pelanggan..." />
        <!-- Top Header -->


        <div class="dashboard-content">
            <!-- Page Header -->
            <div class="section-title">
                <span class="subtitle">Dashboard Overview</span>
                <div class="main-heading" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <h1 style="margin: 0;">Analytics Data</h1>
                    <div class="header-btns" style="display: flex; gap: 10px; align-items: center;">
                        <select id="viewSelector" style="padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid #cbd5e1; background: white; font-weight: 600; color: #1e293b; cursor: pointer; outline: none;">
                            <option value="executive">Ringkasan Utama</option>
                            <option value="operational">Operasional & Inventaris</option>
                            <option value="customer">Analitik Pelanggan</option>
                            <option value="staff">Performa Staf</option>
                        </select>
                        <a href="{{ route('kasir.index') }}" class="btn-primary" style="margin-bottom: 0;">
                            <i class="fas fa-plus"></i>
                            <span>Transaksi Baru</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- SECTION: EXECUTIVE (Ringkasan Utama) -->
            <div id="view-executive" class="dashboard-view" style="display: block;">
                <div class="stats-grid">
                    <div class="card-stat">
                        <div class="card-icon-title">
                            <div class="card-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <span class="card-label">Penjualan Hari Ini</span>
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
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                        <span class="card-label">Status Order (Antrean)</span>
                        <span class="card-value">{{ $activeOrdersCount }} Order Aktif</span>
                    </div>

                    <div class="card-stat">
                        <div class="card-icon-title">
                            <div class="card-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                <i class="fas fa-user-clock"></i>
                            </div>
                        </div>
                        <span class="card-label">Total Hutang Pelanggan</span>
                        <span class="card-value">Rp {{ number_format($totalDebt, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="dashboard-columns">
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
                    
                    <div class="right-stack" style="display: flex; flex-direction: column; gap: 1.5rem;">
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
            </div>

            <!-- SECTION: OPERATIONAL (Operasional & Inventaris) -->
            <div id="view-operational" class="dashboard-view" style="display: none;">
                <div class="stats-grid" style="margin-bottom: 1.5rem;">
                    <div class="ai-card" style="grid-column: span 2;">
                        <h3><i class="fas fa-brain"></i> Insight Operasional</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div class="busy-box" style="background: rgba(255,255,255,0.5); padding: 1rem; border-radius: 8px;">
                                <span style="font-size: 0.8rem; opacity: 0.9; display:block; margin-bottom: 8px;">JAM TERSIBUK HARI INI</span>
                                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                                    <i class="far fa-clock"></i>
                                    <span style="font-size: 1.25rem;">{{ $busyHour ? str_pad($busyHour->hour, 2, '0', STR_PAD_LEFT).':00' : '--:--' }}</span>
                                </div>
                            </div>
                            <div class="busy-box" style="background: rgba(255,255,255,0.5); padding: 1rem; border-radius: 8px;">
                                <span style="font-size: 0.8rem; opacity: 0.9; display:block; margin-bottom: 8px;">PRODUK TERLARIS</span>
                                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                                    <i class="fas fa-fire" style="color: #ef4444;"></i>
                                    <span style="font-size: 1.1rem;">{{ $topProduct->name ?? 'Belum ada data' }} ({{ $topProduct->total_qty ?? 0 }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-columns" style="grid-template-columns: 1fr 1fr;">
                    <div class="chart-card">
                        <h3 style="font-weight: 700; margin-bottom: 1rem;"><i class="fas fa-leaf" style="color:#10b981; margin-right:8px;"></i>Bahan Baku Kritis ⚠️</h3>
                        @if($criticalIngredients->isEmpty())
                            <div style="padding: 2rem; text-align: center; color: #64748b; background: #f8fafc; border-radius: 12px;">
                                <i class="fas fa-check-circle" style="font-size: 2rem; color: #10b981; margin-bottom: 1rem; display: block;"></i>
                                Stok bahan baku aman!
                            </div>
                        @else
                            <ul style="list-style: none; padding: 0; margin: 0; display:flex; flex-direction:column; gap:0.75rem;">
                                @foreach($criticalIngredients as $ing)
                                <li style="display: flex; justify-content: space-between; padding: 1rem; border: 1px solid #f1f5f9; border-radius: 8px; background: #fff;">
                                    <div style="display:flex; flex-direction:column;">
                                        <span style="font-weight: 600;">{{ $ing->name }}</span>
                                        <span style="font-size: 0.8rem; color: #94a3b8;">Batas: {{ $ing->min_stock }} {{ $ing->unit }}</span>
                                    </div>
                                    <div style="font-weight: 700; color: #ef4444;">
                                        {{ $ing->stock }} {{ $ing->unit }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    
                    <div class="chart-card">
                        <h3 style="font-weight: 700; margin-bottom: 1rem;"><i class="fas fa-box" style="color:#f59e0b; margin-right:8px;"></i>Produk Kritis ⚠️</h3>
                        <div style="padding: 1.5rem; text-align: center; background: #f8fafc; border-radius: 12px;">
                            <span style="font-size: 2rem; font-weight: 800; color: #f59e0b; display:block;">{{ $lowStockCount }}</span>
                            <span style="color: #64748b;">Produk Retail/Satuan yang menipis. Cek menu <a href="{{ route('stock.index') }}" style="color:var(--primary); font-weight:600;">Stock</a> untuk detail.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION: CUSTOMER (Analitik Pelanggan) -->
            <div id="view-customer" class="dashboard-view" style="display: none;">
                <div class="dashboard-columns" style="grid-template-columns: 1fr 1fr;">
                    <div class="chart-card">
                        <h3 style="font-weight: 700; margin-bottom: 1rem;"><i class="fas fa-crown" style="color:#f59e0b; margin-right:8px;"></i>Top Pelanggan Hari Ini</h3>
                        @if($topCustomers->isEmpty())
                            <p style="color: #64748b; text-align: center; padding: 2rem;">Belum ada data pelanggan tercatat hari ini.</p>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @foreach($topCustomers as $index => $tc)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 10px;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <div style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b;">
                                                #{{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">{{ $tc->customer->name ?? 'Guest' }}</div>
                                                <div style="font-size: 0.8rem; color: #64748b;">{{ $tc->count }} Transaksi</div>
                                            </div>
                                        </div>
                                        <div style="font-weight: 700; color: #0052cc;">
                                            Rp {{ number_format($tc->total, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="chart-card">
                        <h3 style="font-weight: 700; margin-bottom: 1rem;"><i class="fas fa-credit-card" style="color:#3b82f6; margin-right:8px;"></i>Metode Pembayaran (Hari Ini)</h3>
                        @if($paymentMethods->isEmpty())
                            <p style="color: #64748b; text-align: center; padding: 2rem;">Belum ada transaksi.</p>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @foreach($paymentMethods as $pm)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f8fafc; border-radius: 10px;">
                                        <div style="display: flex; flex-direction: column;">
                                            <span style="font-weight: 600; text-transform: uppercase;">{{ $pm->payment_method }}</span>
                                            <span style="font-size: 0.8rem; color: #64748b;">{{ $pm->count }} Transaksi</span>
                                        </div>
                                        <div style="font-weight: 700;">
                                            Rp {{ number_format($pm->total, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECTION: STAFF (Performa Staf) -->
            <div id="view-staff" class="dashboard-view" style="display: none;">
                <div class="chart-card">
                    <h3 style="font-weight: 700; margin-bottom: 1rem;"><i class="fas fa-users" style="color:#0052cc; margin-right:8px;"></i>Transaksi per Staf (Hari Ini)</h3>
                    @if($staffPerformance->isEmpty())
                        <p style="color: #64748b; text-align: center; padding: 2rem;">Belum ada transaksi yang diproses staf hari ini.</p>
                    @else
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                            @foreach($staffPerformance as $sp)
                                <div style="border: 1px solid #e2e8f0; padding: 1.5rem; border-radius: 12px; background: white; text-align: center; transition: transform 0.2s;">
                                    <div style="width: 60px; height: 60px; background: var(--primary-light); color: var(--primary); font-size: 1.5rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 1rem auto;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h4 style="margin: 0 0 5px 0; font-size: 1.1rem; color: #1e293b;">{{ $sp->user->name ?? 'System' }}</h4>
                                    <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: #64748b;">{{ $sp->user->role ?? 'Staf' }}</p>
                                    
                                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                                        <div style="font-weight: 700; color: #0052cc; font-size: 1.1rem;">{{ $sp->count }} Transaksi</div>
                                        <div style="font-size: 0.85rem; color: #475569; margin-top: 4px;">Rp {{ number_format($sp->total, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
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
            // View Switching Logic
            const viewSelector = document.getElementById('viewSelector');
            const views = document.querySelectorAll('.dashboard-view');

            viewSelector.addEventListener('change', function(e) {
                const targetId = 'view-' + e.target.value;
                views.forEach(view => {
                    if(view.id === targetId) {
                        view.style.display = 'block';
                    } else {
                        view.style.display = 'none';
                    }
                });
            });

            // Chart Initialization
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
