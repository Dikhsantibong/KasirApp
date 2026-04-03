@extends('layouts.app')

@section('title', 'Monitoring Hutang / Piutang')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hutang.css') }}">
@endpush

@section('content')
<div class="hutang-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Monitoring Hutang Pelanggan">
            <x-slot:search>
                <form action="{{ route('hutang.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari nama pelanggan..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Monitoring Hutang Pelanggan</h1>
                    <p class="page-subtitle">Pantau pembayaran tertunda dan kelola jatuh tempo dengan detail.</p>
                </div>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h4>Total Piutang (Kredit Keluar)</h4>
                        <h2>Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-red"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-info">
                        <h4>Terlambat (Lewat Jatuh Tempo)</h4>
                        <h2 style="color:#ef4444;">Rp {{ number_format($overdueAmount, 0, ',', '.') }}</h2>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h4>Pelanggan Lewat Tempo</h4>
                        <h2>{{ $overdueCount }} Orang</h2>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- TABLE -->
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
                                    $isOverdue = $debt->due_date && \Carbon\Carbon::parse($debt->due_date)->isPast() && $debt->status != 'Lunas';
                                    $statusClass = $debt->status == 'Lunas' ? 'status-lunas' : ($isOverdue ? 'status-overdue' : 'status-hutang');
                                @endphp
                                <tr>
                                    <td>
                                        <span class="customer-name">{{ $debt->customer->name ?? 'Anonim' }}</span>
                                        <span class="transaction-id">TRX: {{ strtoupper(substr($debt->transaction_id ?? $debt->id, 0, 8)) }}</span>
                                    </td>
                                    <td><span class="amount-val">Rp {{ number_format($debt->amount, 0, ',', '.') }}</span></td>
                                    <td>
                                        <span class="date-val {{ $isOverdue ? 'date-overdue' : '' }}">
                                            {{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d M Y') : 'Tidak Ada' }}
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
                                    <td colspan="5" style="text-align:center; padding:3rem; color:#94a3b8;">
                                        <i class="fas fa-file-invoice-dollar" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                        Tidak ada catatan hutang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="pagination-area">
                        @if($allDebts->hasPages())
                            {{ $allDebts->links('pagination::bootstrap-4') }}
                        @endif
                    </div>
                </div>

                <!-- TIMELINE -->
                <div class="timeline-panel">
                    <h3 class="section-title"><i class="far fa-calendar-alt"></i> Timeline Jatuh Tempo</h3>
                    <div class="timeline-list">
                        @php
                            $upcoming = $allDebts->filter(function($d) { return $d->status != 'Lunas'; })->sortBy('due_date')->take(5);
                        @endphp
                        @forelse($upcoming as $u)
                            @php $dPast = $u->due_date && \Carbon\Carbon::parse($u->due_date)->isPast(); @endphp
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $dPast ? 'danger' : 'warning' }}"></div>
                                <div class="timeline-date">{{ $u->due_date ? \Carbon\Carbon::parse($u->due_date)->diffForHumans() : '-' }}</div>
                                <div class="timeline-card {{ $dPast ? 'urgent' : '' }}">
                                    <h4>{{ $u->customer->name ?? 'Anonim' }}</h4>
                                    <div class="timeline-card-flex">
                                        <span style="font-size:0.8rem;">{{ $u->due_date ? \Carbon\Carbon::parse($u->due_date)->format('d M Y') : '-' }}</span>
                                        <span class="timeline-amount">Rp {{ number_format($u->amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; color:#94a3b8; padding:2rem;">
                                <i class="fas fa-check-circle" style="color:#10b981; font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                                Tidak ada hutang yang belum lunas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
