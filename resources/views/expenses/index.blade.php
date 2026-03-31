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
                <img src="https://ui-avatars.com/api/?name=Admin+Toko&background=0D8ABC&color=fff" style="width:36px; height:36px; border-radius:50%;">
            </div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Catatan Pengeluaran</h1>
                    <p class="page-subtitle">Kelola dan pantau setiap aliran kas keluar bisnis Anda secara mendetail.</p>
                </div>
                <div>
                    <button class="btn-primary">
                        <i class="fas fa-plus-circle"></i> Catat Pengeluaran
                    </button>
                </div>
            </div>

            <!-- WIDGETS SECTION -->
            <div class="widget-grid">
                <!-- Main Summary -->
                <div class="widget-card">
                    <div class="widget-chip">RINGKASAN UTAMA</div>
                    <div class="widget-title-small">Total Pengeluaran Bulan Ini</div>
                    <div class="widget-amount-flex">
                        <div class="widget-amount">Rp {{ number_format($totalMonth ?? 5000000, 0, ',', '.') }}</div>
                        <div class="widget-trend">~12%</div>
                    </div>
                    
                    <div class="budget-indicators">
                        <span>BUDGET TERPAKAI</span>
                        <span style="color:#1e293b; font-weight:700;">SISA SALDO OPERASIONAL <span style="margin-left:8px;">Rp 12.450.000</span></span>
                    </div>
                    <div class="budget-progress-bar">
                        <div class="budget-fill"></div>
                        <div class="budget-fill-2"></div>
                    </div>
                </div>

                <!-- Top Category Summary -->
                <div class="widget-card-blue">
                    <div class="icon-top-blue"><i class="fas fa-receipt"></i></div>
                    <h3>Terbanyak</h3>
                    <p>Bulan ini pengeluaran didominasi oleh {{ $topExpense->sample_desc ?? 'Gaji Karyawan' }}.</p>
                    <h1>Rp {{ number_format($topExpense->total_amount ?? 2500000, 0, ',', '.') }}</h1>
                    <div class="cat-tag">Kategori: {{ ucfirst($topExpense->category ?? 'Gaji') }}</div>
                </div>
            </div>

            <!-- DATA TABLE -->
            <div class="table-container">
                <div class="table-header-flex">
                    <h3>Daftar Transaksi</h3>
                    <div class="table-tools">
                        <i class="fas fa-filter"></i>
                        <i class="fas fa-download"></i>
                    </div>
                </div>

                <table class="expenses-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>NAMA PENGELUARAN</th>
                            <th>KATEGORI</th>
                            <th>NOMINAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            @php
                                $catLower = strtolower($e->category);
                                $catClass = '';
                                if($catLower == 'sewa') $catClass = 'sewa';
                                elseif($catLower == 'gaji') $catClass = 'gaji';
                            @endphp
                            <tr>
                                <td><span class="date-val">{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</span></td>
                                <td>
                                    <span class="desc-val">{{ $e->description ?? '-' }}</span>
                                    <span class="invoice-val">Invoice: #EXP-{{ substr($e->id, 0, 4) }}</span>
                                </td>
                                <td><span class="cat-pill {{ $catClass }}">{{ ucfirst($e->category ?? 'Umum') }}</span></td>
                                <td><span class="amount-val">Rp {{ number_format($e->amount, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <!-- DUMMY ROWS FOR VISUAL MATCHING IF DB IS EMPTY -->
                            <tr>
                                <td><span class="date-val">12 Okt 2023</span></td>
                                <td>
                                    <span class="desc-val">Tagihan Listrik Toko</span>
                                    <span class="invoice-val">Invoice: #EXP-9901</span>
                                </td>
                                <td><span class="cat-pill">Listrik</span></td>
                                <td><span class="amount-val">Rp 1.200.000</span></td>
                            </tr>
                            <tr>
                                <td><span class="date-val">10 Okt 2023</span></td>
                                <td>
                                    <span class="desc-val">Gaji Karyawan - Bagian Gudang</span>
                                    <span class="invoice-val">Periode Oktober 2023</span>
                                </td>
                                <td><span class="cat-pill gaji">Gaji</span></td>
                                <td><span class="amount-val">Rp 2.500.000</span></td>
                            </tr>
                            <tr>
                                <td><span class="date-val">05 Okt 2023</span></td>
                                <td>
                                    <span class="desc-val">Sewa Ruko Tahunan</span>
                                    <span class="invoice-val">Vendor: Properti Makmur</span>
                                </td>
                                <td><span class="cat-pill sewa">Sewa</span></td>
                                <td><span class="amount-val">Rp 1.300.000</span></td>
                            </tr>
                            <tr>
                                <td><span class="date-val">02 Okt 2023</span></td>
                                <td>
                                    <span class="desc-val">Biaya Internet & Telepon</span>
                                    <span class="invoice-val">Provider: BizNet</span>
                                </td>
                                <td><span class="cat-pill">Listrik</span></td>
                                <td><span class="amount-val">Rp 500.000</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-area">
                    Menampilkan {{ count($expenses ?? []) > 0 ? count($expenses) : 4 }} dari 12 transaksi pengeluaran
                </div>
            </div>
            
        </div>
    </main>
</div>

<!-- HEALTH SCORE WIDGET -->
<div class="health-score-widget">
    <div class="health-icon"><i class="fas fa-chart-line"></i></div>
    <div class="health-text">
        <h5>HEALTH SCORE</h5>
        <p>Pengeluaran terkendali, 5% di bawah budget operasional.</p>
    </div>
</div>
@endsection
