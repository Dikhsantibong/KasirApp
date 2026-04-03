@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pembelian.css') }}">
    <style>
        /* CRITICAL CSS TO PREVENT FOUC */
        .modal-overlay {
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.6); z-index: 9998;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.show { display: flex !important; }
        
        /* THEME HARMONIZATION */
        body { background-color: #f8fafc !important; color: #1e293b !important; }
        
        /* MODAL CARD */
        .modal-card {
            background: white; border-radius: 20px; padding: 2rem;
            width: 700px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            animation: modalIn 0.3s ease;
        }
        @keyframes modalIn { from { transform: translateY(30px); opacity:0; } to { transform: translateY(0); opacity:1; } }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .modal-header h2 { font-size:1.3rem; font-weight:800; color:#1e293b; margin:0; }
        .modal-close { background:none; border:none; font-size:1.25rem; color:#94a3b8; cursor:pointer; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display:block; font-size:0.8rem; font-weight:700; color:#475569; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.5px; }
        .form-input, .form-select {
            width:100%; padding:0.75rem 1rem; border:1px solid #e2e8f0;
            border-radius:10px; font-size:0.95rem; color:#1e293b;
            transition: border 0.2s;
        }
        .form-input:focus, .form-select:focus { outline:none; border-color:#0052cc; box-shadow: 0 0 0 3px rgba(0,82,204,0.1); }
        .btn-submit {
            width:100%; background:#0052cc; color:white; border:none;
            padding:0.85rem; border-radius:10px; font-weight:700; font-size:0.95rem;
            cursor:pointer; margin-top:1rem;
        }
        .btn-submit:hover { background:#003d99; }
        
        .item-list { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-top: 1rem; }
        .item-row { display: grid; grid-template-columns: 2fr 1fr 1fr 40px; gap: 0.75rem; align-items: end; margin-bottom: 1rem; }
        .btn-remove { color: #ef4444; background: none; border: none; cursor: pointer; padding-bottom: 10px; }

        .toast-notification {
            position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999;
            background: #0052cc; color: white; padding: 1rem 1.5rem;
            border-radius: 12px; font-weight: 600; font-size: 0.9rem;
            box-shadow: 0 10px 25px -5px rgba(0,82,204,0.4);
            transform: translateX(120%); transition: transform 0.4s ease;
        }
        .toast-notification.show { transform: translateX(0); }
        .toast-notification.error { background: #ef4444; box-shadow: 0 10px 25px -5px rgba(239,68,68,0.4); }
    </style>
@endpush

@section('content')
<div class="pembelian-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Manajemen Pembelian Stok">
            <x-slot:search>
                <form action="{{ route('pembelian.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari transaksi atau supplier..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>


        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Riwayat Pembelian ke Supplier</h1>
                    <p class="page-subtitle">Kelola pengadaan barang dan pantau status pembayaran kepada mitra pemasok Anda.</p>
                </div>
                <div class="header-actions" style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="openSupplierModal()" style="background: #10b981;"><i class="fas fa-truck"></i> Tambah Supplier</button>
                    <button class="btn-primary" onclick="openPurchaseModal()"><i class="fas fa-shopping-cart"></i> Catat Pembelian Baru</button>
                </div>
            </div>

            <!-- TOP SECTION GRID -->
            <div class="top-section-grid">
                <div class="suppliers-section">
                    <div class="section-header-flex">
                        <h3>Daftar Supplier</h3>
                        <span style="font-size:0.8rem; color:#64748b;">{{ $suppliers->count() }} supplier terdaftar</span>
                    </div>
                    <div class="suppliers-grid">
                        @forelse($suppliers->take(3) as $key => $supplier)
                            @php
                                $icons = ['fa-truck', 'fa-clipboard-list', 'fa-cube'];
                                $classes = ['', 'icon-orange', 'icon-purple'];
                            @endphp
                            <div class="supplier-card">
                                <div>
                                    <div class="supplier-card-top">
                                        <div class="supplier-icon {{ $classes[$key % 3] }}"><i class="fas {{ $icons[$key % 3] }}"></i></div>
                                        <span class="supplier-tag">SUPPLIER</span>
                                    </div>
                                    <h4 class="supplier-name">{{ $supplier->name }}</h4>
                                    <p class="supplier-loc">{{ $supplier->phone ?? '-' }}</p>
                                </div>
                                <div class="supplier-stats">
                                    <span>Total Transaksi</span>
                                    <span>{{ $supplier->purchases_count }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; color:#94a3b8; padding:2rem; grid-column:1/-1;">
                                <i class="fas fa-truck" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                Belum ada supplier terdaftar.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="debt-card">
                    <div class="debt-title">TOTAL PEMBELIAN</div>
                    <div class="debt-amount">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</div>
                    <div class="debt-info-list">
                        <div class="debt-info-item">
                            <i class="fas fa-box"></i>
                            <span>{{ $purchases->total() }} transaksi tercatat</span>
                        </div>
                        <div class="debt-info-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $suppliers->count() }} supplier aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <div class="table-controls">
                    <h3>Transaksi Terbaru</h3>
                    <div class="filters-wrapper">
                        <span style="font-size:0.85rem; color:#64748b;">Total: <strong>{{ $purchases->total() }} transaksi</strong></span>
                    </div>
                </div>

                <table class="purchases-table">
                    <thead>
                        <tr>
                            <th>ID TRANSAKSI</th>
                            <th>TANGGAL</th>
                            <th>SUPPLIER</th>
                            <th>TOTAL BELANJA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <tr>
                                <td><span class="p-id">#PUR-{{ strtoupper(substr($p->id, 0, 8)) }}</span></td>
                                <td>
                                    <span class="p-date">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</span>
                                    <span class="p-time">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }} WIB</span>
                                </td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="sup-avatar">{{ strtoupper(substr($p->supplier->name ?? 'A', 0, 2)) }}</div>
                                        <span class="sup-name">{{ $p->supplier->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td><span class="total-val">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    <i class="fas fa-receipt" style="font-size:2rem; opacity:0.2; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada transaksi pembelian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-info">
                    @if($purchases->total() > 0)
                        Menampilkan {{ ($purchases->currentPage()-1)*$purchases->perPage()+1 }}-{{ min($purchases->currentPage()*$purchases->perPage(), $purchases->total()) }} dari {{ $purchases->total() }} transaksi
                    @else
                        Tidak ada data
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

<!-- MODAL CATAT PEMBELIAN -->
<div class="modal-overlay" id="purchaseModal" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle" style="color:#0052cc; margin-right:8px;"></i> Catat Pembelian Stok</h2>
            <button class="modal-close" onclick="closePurchaseModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Pilih Supplier *</label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="item-list">
                <div class="form-label" style="margin-bottom: 1rem;">Daftar Barang Belanja</div>
                <div id="purchase-items-container">
                    <div class="item-row">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Produk</label>
                            <select name="items[0][product_id]" class="form-select" required>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ $p->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Qty</label>
                            <input type="number" name="items[0][qty]" class="form-input" min="1" value="1" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Harga Beli</label>
                            <input type="number" name="items[0][cost_price]" class="form-input" min="0" placeholder="0" required>
                        </div>
                        <div></div>
                    </div>
                </div>
                <button type="button" class="btn-outline" onclick="addPurchaseRow()" style="border: 1px dashed #cbd5e1; width:100%; padding:0.5rem; border-radius:8px; color:#64748b; cursor:pointer;">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>

            <button type="submit" class="btn-submit"><i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pembelian</button>
        </form>
    </div>
</div>

</div>

<!-- MODAL TAMBAH SUPPLIER -->
<div class="modal-overlay" id="supplierModal" style="display: none;">
    <div class="modal-card" style="width: 450px;">
        <div class="modal-header">
            <h2><i class="fas fa-truck" style="color:#10b981; margin-right:8px;"></i> Tambah Supplier Baru</h2>
            <button class="modal-close" onclick="closeSupplierModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('pembelian.supplier.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Supplier *</label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: PT. Sumber Makmur" required>
            </div>
            <div class="form-group">
                <label class="form-label">No. Telepon / WhatsApp</label>
                <input type="text" name="phone" class="form-input" placeholder="Contoh: 08123456789">
            </div>
            <button type="submit" class="btn-submit" style="background: #10b981;"><i class="fas fa-save" style="margin-right:8px;"></i> Simpan Supplier</button>
        </form>
    </div>
</div>

<!-- TOAST -->
<div class="toast-notification" id="toast"></div>

@push('scripts')
<script>
    let rowIndex = 1;

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast-notification show' + (isError ? ' error' : '');
        setTimeout(() => { toast.className = 'toast-notification'; }, 3000);
    }

    function openSupplierModal() {
        document.getElementById('supplierModal').classList.add('show');
    }

    function closeSupplierModal() {
        document.getElementById('supplierModal').classList.remove('show');
    }

    function openPurchaseModal() {
        document.getElementById('purchaseModal').classList.add('show');
    }

    function closePurchaseModal() {
        document.getElementById('purchaseModal').classList.remove('show');
    }

    function addPurchaseRow() {
        const container = document.getElementById('purchase-items-container');
        const div = document.createElement('div');
        div.className = 'item-row';
        div.innerHTML = `
            <div class="form-group" style="margin:0;">
                <select name="items[${rowIndex}][product_id]" class="form-select" required>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ $p->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <input type="number" name="items[${rowIndex}][qty]" class="form-input" min="1" value="1" required>
            </div>
            <div class="form-group" style="margin:0;">
                <input type="number" name="items[${rowIndex}][cost_price]" class="form-input" min="0" placeholder="0" required>
            </div>
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(div);
        rowIndex++;
    }

    // AJAX SUPPLIER
    document.getElementById('supplierModal').querySelector('form').addEventListener('submit', function(e) {
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
                closeSupplierModal();
                form.reset();
                // Update select in Purchase Modal
                const selects = document.querySelectorAll('select[name="supplier_id"]');
                selects.forEach(select => {
                    select.innerHTML = '<option value="">-- Pilih Supplier --</option>';
                    data.suppliers.forEach(s => {
                        select.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                    });
                });
            } else {
                showToast(data.message || 'Gagal menyimpan supplier', true);
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i> Simpan Supplier';
        });
    });

    // AJAX PURCHASE
    document.getElementById('purchaseModal').querySelector('form').addEventListener('submit', function(e) {
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
                closePurchaseModal();
                setTimeout(() => { window.location.reload(); }, 1000);
            } else {
                showToast(data.message || 'Gagal menyimpan pembelian', true);
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i> Simpan Pembelian';
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
