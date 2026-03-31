@extends('layouts.app')

@section('title', 'Monitoring Hutang / Piutang')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hutang.css') }}">
@endpush

@section('content')
<div class="hutang-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="{{ route('hutang.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari nama pelanggan..." value="{{ request('search') }}">
                </form>
            </div>
            
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <img src="https://ui-avatars.com/api/?name=Admin+Toko&background=0D8ABC&color=fff" style="width:36px; height:36px; border-radius:50%;">
            </div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Monitoring Hutang Pelanggan</h1>
                    <p class="page-subtitle">Pantau pembayaran tertunda dan kelola jatuh tempo dengan detail kalender riwayat hutang.</p>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h4>Total Piutang (Kredit Keluar)</h4>
                        <h2>Rp {{ number_format($totalUnpaidAmount ?? 0, 0, ',', '.') }}</h2>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-red"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-info">
                        <h4>Terlambat (Lewat Jatuh Tempo)</h4>
                        <h2 style="color:#ef4444;">Rp {{ number_format($overdueAmount ?? 0, 0, ',', '.') }}</h2>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-orange"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h4>Pelanggan Lewat Tempo</h4>
                        <h2>{{ $overdueCount ?? 0 }} Orang</h2>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- MAIN TABLE -->
                <div class="table-container">
                    <h3 class="section-title"><i class="fas fa-table"></i> Daftar Riwayat Piutang</h3>
                    
                    <table class="debts-table">
                        <thead>
                            <tr>
                                <th>PELANGGAN & TRX</th>
                                <th>NOMINAL</th>
                                <th>JATUH TEMPO</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allDebts as $debt)
                                @php
                                    $isOverdue = \Carbon\Carbon::parse($debt->due_date)->isPast() && $debt->status != 'Lunas';
                                    $statusClass = $debt->status == 'Lunas' ? 'status-lunas' : ($isOverdue ? 'status-overdue' : 'status-hutang');
                                @endphp
                                <tr>
                                    <td>
                                        <span class="customer-name">{{ $debt->customer->name ?? 'Anonim' }}</span>
                                        <span class="transaction-id">TRX: {{ substr($debt->transaction_id ?? $debt->id, 0, 8) }}</span>
                                    </td>
                                    <td><span class="amount-val">Rp {{ number_format($debt->amount, 0, ',', '.') }}</span></td>
                                    <td>
                                        <span class="date-val {{ $isOverdue ? 'date-overdue' : '' }}">
                                            @if($debt->due_date)
                                                {{ \Carbon\Carbon::parse($debt->due_date)->format('d M Y') }}
                                            @else
                                                Tidak Ada
                                            @endif
                                        </span>
                                    </td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $debt->status ?? 'Menunggu' }}</span></td>
                                    <td>
                                        @if($debt->status != 'Lunas')
                                            <button class="action-btn action-btn-pay"><i class="fas fa-money-bill"></i> Tagih</button>
                                        @else
                                            <button class="action-btn"><i class="fas fa-check"></i> Selesai</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 2rem; color:#94a3b8;">Tidak ada catatan hutang.</td>
                                </tr>
                                <!-- Dummy Visual Rows for Demo Context -->
                                <tr>
                                    <td>
                                        <span class="customer-name">Bambang Pamungkas</span>
                                        <span class="transaction-id">TRX: 8b1n3kd</span>
                                    </td>
                                    <td><span class="amount-val">Rp 4.200.000</span></td>
                                    <td><span class="date-val date-overdue">{{ \Carbon\Carbon::now()->subDays(3)->format('d M Y') }}</span></td>
                                    <td><span class="status-badge status-overdue">Terlewat</span></td>
                                    <td><button class="action-btn action-btn-pay"><i class="fas fa-money-bill"></i> Tagih</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="customer-name">Siti Nurhaliza</span>
                                        <span class="transaction-id">TRX: 3kfj29</span>
                                    </td>
                                    <td><span class="amount-val">Rp 500.000</span></td>
                                    <td><span class="date-val">{{ \Carbon\Carbon::now()->addDays(5)->format('d M Y') }}</span></td>
                                    <td><span class="status-badge status-hutang">Berjalan</span></td>
                                    <td><button class="action-btn action-btn-pay"><i class="fas fa-money-bill"></i> Tagih</button></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="pagination-area">
                        {{ collect($allDebts ?? [])->isNotEmpty() ? $allDebts->links('pagination::bootstrap-4') : '' }}
                    </div>
                </div>

                <!-- TIMELINE SIDEBAR (VISUAL CALENDAR) -->
                <div class="timeline-panel">
                    <h3 class="section-title"><i class="far fa-calendar-alt"></i> Timeline Jatuh Tempo</h3>
                    
                    <div class="timeline-list">
                        <!-- Demonstrasi Data Timeline -->
                        @php
                            $upcoming = isset($allDebts) && count($allDebts) > 0 ? 
                                $allDebts->filter(function($d) { return $d->status != 'Lunas'; })->sortBy('due_date')->take(4) : [];
                        @endphp
                        
                        @forelse($upcoming as $u)
                            @php
                                $dPast = \Carbon\Carbon::parse($u->due_date)->isPast();
                            @endphp
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $dPast ? 'danger' : 'warning' }}"></div>
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($u->due_date)->diffForHumans() }}</div>
                                <div class="timeline-card {{ $dPast ? 'urgent' : '' }}">
                                    <h4>{{ $u->customer->name ?? 'Anonim' }}</h4>
                                    <div class="timeline-card-flex">
                                        <span style="font-size:0.8rem;">Jatuh Tempo: {{ \Carbon\Carbon::parse($u->due_date)->format('d M') }}</span>
                                        <span class="timeline-amount">Rp {{ number_format($u->amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Dummy Data for Visual Presentation -->
                            <div class="timeline-item">
                                <div class="timeline-dot danger"></div>
                                <div class="timeline-date">3 hari yang lalu</div>
                                <div class="timeline-card urgent">
                                    <h4>Bambang Pamungkas</h4>
                                    <div class="timeline-card-flex">
                                        <span style="font-size:0.8rem;">Jatuh Tempo: {{ \Carbon\Carbon::now()->subDays(3)->format('d M') }}</span>
                                        <span class="timeline-amount">Rp 4.200.000</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot warning"></div>
                                <div class="timeline-date">Minggu Depan</div>
                                <div class="timeline-card">
                                    <h4>Siti Nurhaliza</h4>
                                    <div class="timeline-card-flex">
                                        <span style="font-size:0.8rem;">Jatuh Tempo: {{ \Carbon\Carbon::now()->addDays(5)->format('d M') }}</span>
                                        <span class="timeline-amount">Rp 500.000</span>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-date">Bulan Depan</div>
                                <div class="timeline-card">
                                    <h4>Cafe Semesta</h4>
                                    <div class="timeline-card-flex">
                                        <span style="font-size:0.8rem;">Jatuh Tempo: {{ \Carbon\Carbon::now()->addDays(15)->format('d M') }}</span>
                                        <span class="timeline-amount">Rp 1.150.000</span>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                        
                        <div style="margin-top:1rem; text-align:center;">
                            <a href="#" style="color:#0052cc; font-weight:600; font-size:0.85rem; text-decoration:none;">Lihat Seluruh Kalender Kalender &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>
@endsection
