@extends('layouts.app')

@section('title', 'Catatan Pengeluaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pengeluaran.css') }}">
    <style>
        /* CRITICAL CSS TO PREVENT FOUC */
        .modal-overlay {
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.6); z-index: 9998;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.show { display: flex !important; }
        
        .modal-card {
            background: white; border-radius: 20px; padding: 2rem;
            width: 450px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            animation: modalIn 0.3s ease;
            color: #1e293b;
        }
        @keyframes modalIn { from { transform: translateY(30px); opacity:0; } to { transform: translateY(0); opacity:1; } }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .modal-header h2 { font-size:1.3rem; font-weight:800; color:#1e293b; margin:0; }
        .modal-close { background:none; border:none; font-size:1.25rem; color:#94a3b8; cursor:pointer; }
        
        .form-group { margin-bottom: 1.25rem; text-align: left; }
        .form-label { display:block; font-size:0.8rem; font-weight:700; color:#475569; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.5px; }
        .form-input {
            width:100%; padding:0.75rem 1rem; border:1px solid #e2e8f0;
            border-radius:10px; font-size:0.95rem; color:#1e293b;
            transition: border 0.2s;
        }
        .form-input:focus { outline:none; border-color:#0052cc; box-shadow: 0 0 0 3px rgba(0,82,204,0.1); }
        .btn-submit {
            width:100%; background:#0052cc; color:white; border:none;
            padding:0.85rem; border-radius:10px; font-weight:700; font-size:0.95rem;
            cursor:pointer; margin-top:1rem;
        }
        .btn-submit:hover { background:#003d99; }

        /* TOAST */
        .toast-notification {
            position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999;
            background: #0052cc; color: white; padding: 1rem 1.5rem;
            border-radius: 12px; font-weight: 600; font-size: 0.9rem;
            box-shadow: 0 10px 25px -5px rgba(0,82,204,0.4);
            transform: translateX(120%); transition: transform 0.4s ease;
        }
        .toast-notification.show { transform: translateX(0); }
        .toast-notification.error { background: #ef4444; box-shadow: 0 10px 25px -5px rgba(239,68,68,0.4); }
        
        /* THEME HARMONIZATION */
        body { background-color: #f8fafc !important; color: #1e293b !important; }
    </style>
@endpush

@section('content')
<div class="pengeluaran-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Catatan Pengeluaran">
            <x-slot:search>
                <form action="{{ route('pengeluaran.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari transaksi pengeluaran..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Catatan Pengeluaran</h1>
                    <p class="page-subtitle">Kelola dan pantau setiap aliran kas keluar bisnis Anda.</p>
                </div>
                <div><button class="btn-primary" onclick="openExpenseModal()"><i class="fas fa-plus-circle"></i> Catat Pengeluaran</button></div>
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
                        <p>Kategori terbesar: {{ $topExpense->category }}</p>
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
                                    <span class="desc-val">{{ $e->category ?? '-' }}</span>
                                    <span class="invoice-val">{{ $e->description }}</span>
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

<!-- MODAL CATAT PENGELUARAN -->
<div class="modal-overlay" id="expenseModal" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-receipt" style="color:#0052cc; margin-right:8px;"></i> Catat Pengeluaran</h2>
            <button class="modal-close" onclick="closeExpenseModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('pengeluaran.store') }}" method="POST" id="expenseForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Kategori *</label>
                <input type="text" name="category" class="form-input" placeholder="Contoh: Operasional, Gaji, Listrik" required>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan / Detail</label>
                <textarea name="description" class="form-input" placeholder="Detail pengeluaran..." style="height: 80px;"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Nominal (Rp) *</label>
                <input type="number" name="amount" class="form-input" placeholder="0" required>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pengeluaran</button>
        </form>
    </div>
</div>

<!-- TOAST -->
<div class="toast-notification" id="toast"></div>

@push('scripts')
<script>
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast-notification show' + (isError ? ' error' : '');
        setTimeout(() => { toast.className = 'toast-notification'; }, 3000);
    }

    function openExpenseModal() {
        document.getElementById('expenseModal').classList.add('show');
    }

    function closeExpenseModal() {
        document.getElementById('expenseModal').classList.remove('show');
    }

    // AJAX SUBMISSION
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showToast(data.message);
                closeExpenseModal();
                form.reset();
                // Refresh list after a short delay
                setTimeout(() => { window.location.reload(); }, 1000);
            } else {
                showToast(data.message || 'Gagal menyimpan data', true);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pengeluaran';
            }
        })
        .catch(err => {
            showToast('Terjadi kesalahan jaringan!', true);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pengeluaran';
        });
    });

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });
</script>
@endpush
