@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelanggan.css') }}">
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
        }
        @keyframes modalIn { from { transform: translateY(30px); opacity:0; } to { transform: translateY(0); opacity:1; } }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .modal-header h2 { font-size:1.3rem; font-weight:800; color:#1e293b; margin:0; }
        .modal-close { background:none; border:none; font-size:1.25rem; color:#94a3b8; cursor:pointer; }
        
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display:block; font-size:0.8rem; font-weight:700; color:#475569; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.5px; }
        .form-input {
            width:100%; padding:0.75rem 1rem; border:1px solid #e2e8f0;
            border-radius:10px; font-size:0.95rem; color:#1e293b;
            transition: border 0.2s;
        }
        .form-input:focus { outline:none; border-color:#6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        .btn-submit {
            width:100%; background:#6366f1; color:white; border:none;
            padding:0.85rem; border-radius:10px; font-weight:700; font-size:0.95rem;
            cursor:pointer; margin-top:1rem;
        }
        .btn-submit:hover { background:#4f46e5; }

        .toast-notification {
            position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999;
            background: #6366f1; color: white; padding: 1rem 1.5rem;
            border-radius: 12px; font-weight: 600; font-size: 0.9rem;
            box-shadow: 0 10px 25px -5px rgba(99,102,241,0.4);
            transform: translateX(120%); transition: transform 0.4s ease;
        }
        .toast-notification.show { transform: translateX(0); }
        .toast-notification.error { background: #ef4444; box-shadow: 0 10px 25px -5px rgba(239,68,68,0.4); }
    </style>
@endpush

@section('content')
<div class="pelanggan-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="{{ route('pelanggan.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari pelanggan..." value="{{ request('search') }}">
                </form>
            </div>
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-check-circle"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" style="width:36px; height:36px; border-radius:50%;">
            </div>
        </header>

        <div class="page-content">
            <div class="breadcrumb">Manajemen &rsaquo; <span>Pelanggan</span></div>
            
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daftar Pelanggan</h1>
                    <p class="page-subtitle">Kelola data pelanggan setia dan pantau kewajiban pembayaran.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-outline-primary"><i class="fas fa-download"></i> Ekspor Data</button>
                    <button class="btn-solid-primary" onclick="openCustomerModal()"><i class="fas fa-user-plus"></i> Tambah Pelanggan</button>
                </div>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card-blue">
                    <div>
                        <h4>Total Piutang Berjalan</h4>
                        <h1>Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h1>
                    </div>
                    <div class="stat-blue-footer">
                        <span class="stat-subtext">Dari {{ $totalCustomer }} pelanggan terdaftar</span>
                    </div>
                </div>
                
                <div class="stat-card-white">
                    <div class="stat-icon icon-orange"><i class="far fa-calendar-alt"></i></div>
                    <h4>JATUH TEMPO</h4>
                    <h2>{{ $jatuhTempoCount }} Pelanggan</h2>
                    @if($jatuhTempoCount > 0)
                        <p class="text-danger">! Butuh pengingat segera</p>
                    @else
                        <p class="text-success">Semua aman</p>
                    @endif
                </div>

                <div class="stat-card-white">
                    <div class="stat-icon icon-green"><i class="fas fa-users"></i></div>
                    <h4>TOTAL PELANGGAN</h4>
                    <h2>{{ $totalCustomer }} Orang</h2>
                    <p class="text-success">Terdaftar</p>
                </div>
            </div>

            <div class="section-header">
                <h2>Hutang Pelanggan & Jatuh Tempo</h2>
            </div>

            <div class="table-container">
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>NAMA PELANGGAN</th>
                            <th>NO. WHATSAPP</th>
                            <th>TOTAL BELANJA</th>
                            <th>SISA HUTANG</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                            @php
                                $initials = strtoupper(substr($c->name ?? 'A', 0, 2));
                                $hasHutang = ($c->total_hutang ?? 0) > 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="customer-cell">
                                        <div class="cust-avatar blue">{{ $initials }}</div>
                                        <div class="cust-info">
                                            <h4>{{ $c->name }}</h4>
                                            <p>Pelanggan</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="phone-text">{{ $c->phone ?? '-' }}</span></td>
                                <td><span class="amount-text">Rp {{ number_format($c->total_belanja ?? 0, 0, ',', '.') }}</span></td>
                                <td>
                                    <span class="{{ $hasHutang ? ($c->total_hutang > 1000000 ? 'amount-text-danger' : 'amount-text-blue') : '' }}">
                                        Rp {{ number_format($c->total_hutang ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="aksi-cell">
                                        @if($hasHutang)
                                            <button class="btn-remind"><i class="fas fa-bell"></i> Ingatkan</button>
                                        @endif
                                        <button class="icon-btn icon-blue-light"><i class="fas fa-eye"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    <i class="fas fa-users" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada pelanggan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-info">
                    @if($customers->total() > 0)
                        Menampilkan {{ ($customers->currentPage()-1)*$customers->perPage()+1 }}-{{ min($customers->currentPage()*$customers->perPage(), $customers->total()) }} dari {{ $customers->total() }} pelanggan
                    @else
                        Tidak ada data pelanggan
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>

<!-- FLOATING CREDIT WIDGET -->
<div class="credit-widget">
        <div class="credit-title-flex">
            <div class="credit-icon"><i class="fas fa-chart-pie"></i></div>
            <div>
                <h4>Ringkasan Kredit</h4>
                <p>Data real-time</p>
            </div>
        </div>
        <button class="credit-close" onclick="toggleCreditWidget()"><i class="fas fa-times"></i></button>
    </div>
    <div class="credit-body">
        <div class="credit-item">
            <span>Piutang Berjalan</span>
            <span class="credit-val">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
        </div>
        <div class="credit-item">
            <span>Pelanggan Terdaftar</span>
            <span class="credit-val-green">{{ $totalCustomer }}</span>
        </div>
        <div class="credit-total">
            <span>Jatuh Tempo</span>
            <span>{{ $jatuhTempoCount }} pelanggan</span>
        </div>
    </div>
</div>

<!-- LAUNCHER WIDGET -->
<div class="credit-launcher" id="creditLauncher" onclick="toggleCreditWidget()">
    <i class="fas fa-chart-pie"></i>
</div>

@endsection

<!-- MODAL TAMBAH PELANGGAN -->
<div class="modal-overlay" id="customerModal" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-user-plus" style="color:#6366f1; margin-right:8px;"></i> Pelanggan Baru</h2>
            <button class="modal-close" onclick="closeCustomerModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('pelanggan.store') }}" method="POST" id="customerForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-input" placeholder="Nama Pelanggan" required>
            </div>
            <div class="form-group">
                <label class="form-label">No. WhatsApp / HP</label>
                <input type="text" name="phone" class="form-input" placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pelanggan</button>
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

    function openCustomerModal() {
        document.getElementById('customerModal').classList.add('show');
    }

    function closeCustomerModal() {
        document.getElementById('customerModal').classList.remove('show');
    }

    function toggleCreditWidget() {
        const widget = document.querySelector('.credit-widget');
        const launcher = document.getElementById('creditLauncher');
        
        if (widget.classList.contains('hidden')) {
            widget.classList.remove('hidden');
            launcher.classList.remove('show');
        } else {
            widget.classList.add('hidden');
            launcher.classList.show = launcher.classList.add('show');
        }
    }

    // AJAX SUBMISSION
    document.getElementById('customerForm').addEventListener('submit', function(e) {
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
                closeCustomerModal();
                form.reset();
                // Refresh list after a short delay
                setTimeout(() => { window.location.reload(); }, 1000);
            } else {
                showToast(data.message || 'Gagal menyimpan data', true);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pelanggan';
            }
        })
        .catch(err => {
            showToast('Terjadi kesalahan jaringan!', true);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pelanggan';
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
