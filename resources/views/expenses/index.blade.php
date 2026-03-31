@extends('layouts.app')

@section('title', 'Catatan Pengeluaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pengeluaran.css') }}">
@endpush

@section('content')
<div class="pengeluaran-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="{{ route('pengeluaran.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari transaksi pengeluaran..." value="{{ request('search') }}">
                </form>
            </div>
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#94a3b8; font-size:0.85rem;">
                    <i class="fas fa-sync-alt" style="color:#10b981;"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" style="width:36px; height:36px; border-radius:50%;">
            </div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Catatan Pengeluaran</h1>
                    <p class="page-subtitle">Kelola dan pantau setiap aliran kas keluar bisnis Anda.</p>
                </div>
                <div><button class="btn-primary"><i class="fas fa-plus-circle"></i> Catat Pengeluaran</button></div>
            </div>

            <!-- WIDGETS -->
            <div class="widget-grid">
                <div class="widget-card">
                    <div class="widget-chip">RINGKASAN UTAMA</div>
                    <div class="widget-title-small">Total Pengeluaran Bulan Ini</div>
                    <div class="widget-amount-flex">
                        <div class="widget-amount">Rp {{ number_format($totalMonth, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="widget-card-blue">
                    <div class="icon-top-blue"><i class="fas fa-receipt"></i></div>
                    <h3>Terbanyak</h3>
                    @if($topExpense)
                        <p>Pengeluaran terbesar: {{ $topExpense->name }}</p>
                        <h1>Rp {{ number_format($topExpense->total_amount, 0, ',', '.') }}</h1>
                    @else
                        <p>Belum ada data pengeluaran bulan ini.</p>
                        <h1>Rp 0</h1>
                    @endif
                    <div class="cat-tag">Pengeluaran Utama</div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <div class="table-header-flex">
                    <h3>Daftar Transaksi</h3>
                    <div class="table-tools">
                        <span style="font-size:0.85rem; color:#64748b;">Total: <strong>{{ $expenses->total() }}</strong></span>
                    </div>
                </div>

                <table class="expenses-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>NAMA PENGELUARAN</th>
                            <th>NOMINAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr>
                                <td><span class="date-val">{{ \Carbon\Carbon::parse($e->created_at)->format('d M Y') }}</span></td>
                                <td>
                                    <span class="desc-val">{{ $e->name ?? '-' }}</span>
                                    <span class="invoice-val">Invoice: #EXP-{{ strtoupper(substr($e->id, 0, 4)) }}</span>
                                </td>
                                <td><span class="amount-val">Rp {{ number_format($e->amount, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    <i class="fas fa-receipt" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada pengeluaran tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-area">
                    @if($expenses->total() > 0)
                        Menampilkan {{ ($expenses->currentPage()-1)*$expenses->perPage()+1 }}-{{ min($expenses->currentPage()*$expenses->perPage(), $expenses->total()) }} dari {{ $expenses->total() }} transaksi
                    @else
                        Tidak ada data
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>

<div class="health-score-widget">
    <div class="health-icon"><i class="fas fa-chart-line"></i></div>
    <div class="health-text">
        <h5>HEALTH SCORE</h5>
        <p>Total pengeluaran bulan ini: Rp {{ number_format($totalMonth, 0, ',', '.') }}</p>
    </div>
</div>
@endsection
