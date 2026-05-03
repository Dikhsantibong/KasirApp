@extends('layouts.app')

@section('title', 'Produk')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/produk.css') }}">
    <style>
        /* MODAL */
        .modal-overlay {
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.5); z-index: 9998;
            display: none; align-items: center; justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        .modal-card {
            background: white; border-radius: 20px; padding: 2rem;
            width: 520px; max-height: 90vh; overflow-y: auto;
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
        .form-row { display:grid; grid-template-columns: 1fr 1fr; gap:1rem; }
        .btn-submit {
            width:100%; background:#0052cc; color:white; border:none;
            padding:0.85rem; border-radius:10px; font-weight:700; font-size:0.95rem;
            cursor:pointer; margin-top:0.5rem;
        }
        .btn-submit:hover { background:#003d99; }
        /* TOAST */
        .toast { position:fixed; top:1.5rem; right:1.5rem; z-index:9999; background:#10b981; color:white; padding:1rem 1.5rem; border-radius:12px; font-weight:600; font-size:0.9rem; box-shadow:0 10px 25px -5px rgba(16,185,129,0.4); transform:translateX(120%); transition:transform 0.4s; }
        .toast.show { transform:translateX(0); }
        .toast.error { background:#ef4444; box-shadow:0 10px 25px -5px rgba(239,68,68,0.4); }
        /* ACTION DROPDOWN */
        .action-dropdown { position:relative; display:inline-block; }
        .action-menu { display:none; position:absolute; right:0; top:100%; background:white; border-radius:10px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.15); min-width:160px; z-index:50; overflow:hidden; border:1px solid #f1f5f9; }
        .action-menu.show { display:block; }
        .action-menu-item { display:flex; align-items:center; gap:8px; padding:0.75rem 1rem; font-size:0.85rem; font-weight:600; cursor:pointer; color:#475569; transition:background 0.15s; border:none; background:none; width:100%; text-align:left; }
        .action-menu-item:hover { background:#f8fafc; }
        .action-menu-item.danger { color:#ef4444; }
        .action-menu-item.danger:hover { background:#fef2f2; }
    </style>
@endpush

@section('content')
<div class="product-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Manajemen Produk">
            <x-slot:search>
                <form action="{{ route('produk.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari Produk..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>


        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daftar Produk</h1>
                    <p class="page-subtitle">Kelola inventaris barang dan pantau ketersediaan stok secara real-time.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-outline" onclick="openCategoryModal()" style="border:1px solid #0052cc; color:#0052cc; cursor:pointer;">
                        <i class="fas fa-folder-plus"></i> Tambah Kategori
                    </button>
                    <a href="#" class="btn-outline">
                        <i class="fas fa-file-import"></i> Impor Excel
                    </a>
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </div>
            </div>

            <!-- CARDS ROW -->
            <div class="summary-cards-row">
                <div class="warning-card">
                    <div class="card-header-flex">
                        <div class="card-title-danger">
                            <i class="fas fa-exclamation-square"></i> Peringatan Stok Minimum
                        </div>
                        <a href="#" class="btn-link">Lihat Semua</a>
                    </div>
                    <p class="warning-subtitle">{{ collect($lowStockProducts)->count() }} produk memerlukan restock segera.</p>
                    <div class="low-stock-grid">
                        @forelse($lowStockProducts as $low)
                            <div class="low-stock-item">
                                <div class="low-stock-icon"><i class="fas fa-box-open"></i></div>
                                <div class="low-stock-details">
                                    <h4>{{ $low->name }}</h4>
                                    <p><span>Sisa: <span class="text-danger">{{ $low->stock }} Pcs</span></span> <span>Min: {{ $low->min_stock }}</span></p>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; color:#94a3b8; padding:1rem; grid-column: 1 / -1;">
                                <i class="fas fa-check-circle" style="color:#10b981; margin-right:6px;"></i> Semua stok aman.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="info-subtitle">Total Nilai Inventaris</div>
                    <div class="info-value">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</div>
                    <div class="info-trend"><i class="fas fa-arrow-up"></i> Total dari seluruh produk</div>
                </div>
            </div>

            <!-- DATA TABLE -->
            <div class="table-container">
                <div class="table-controls">
                    <div class="filters">
                        <form action="{{ route('produk.index') }}" method="GET" style="display:flex; gap:1rem;">
                            <input type="text" name="search" placeholder="Filter nama produk..." value="{{ request('search') }}" class="filter-select" style="border:1px solid #e2e8f0; padding:0.5rem 1rem; border-radius:8px;">
                        </form>
                    </div>
                    <div class="sort-text">Total: <strong>{{ $products->total() }} produk</strong></div>
                </div>

                <table class="products-table">
                    <thead>
                        <tr>
                            <th>GAMBAR</th>
                            <th>NAMA PRODUK</th>
                            <th>KATEGORI</th>
                            <th>HARGA BELI</th>
                            <th>HARGA JUAL</th>
                            <th>STOK</th>
                            <th>STATUS</th>
                            <th style="text-align:center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $statusClass = 'status-available'; $statusText = 'Tersedia';
                                if($product->stock <= 0) { $statusClass = 'status-empty'; $statusText = 'Habis'; }
                                elseif($product->stock <= ($product->min_stock ?: 5)) { $statusClass = 'status-low'; $statusText = 'Stok Menipis'; }
                            @endphp
                            <tr>
                                <td>
                                    @if($product->image && Storage::disk('public')->exists($product->image))
                                        <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="IMG" style="object-fit: cover; width:50px; height:50px; border-radius:10px;">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=random&color=fff&size=50" class="product-img" alt="IMG">
                                    @endif
                                </td>
                                <td class="product-info-cell">
                                    <h4>{{ $product->name }}</h4>
                                    <p>{{ $product->sku ?? '-' }}</p>
                                </td>
                                <td><span class="category-badge">{{ $product->category->name ?? 'Uncategorized' }}</span></td>
                                <td class="price-text">Rp {{ number_format($product->buy_price ?? 0, 0, ',', '.') }}</td>
                                <td class="price-text">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                <td class="stock-text {{ $product->stock <= ($product->min_stock ?: 5) ? 'danger' : '' }}">
                                    @if($product->is_recipe_based)
                                        <span style="color:#0052cc; font-size:0.8rem;"><i class="fas fa-blender"></i> Berbasis Resep</span>
                                    @else
                                        {{ $product->stock }} Pcs
                                    @endif
                                </td>
                                <td>
                                    @if($product->has_customization)
                                        <span class="status-badge" style="background:#fefce8; color:#d97706; font-size:0.7rem;">Customizable</span>
                                    @else
                                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <div class="action-dropdown">
                                        <button class="action-btn" onclick="toggleMenu(this)"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="action-menu">
                                            <button class="action-menu-item" onclick="openEditModal('{{ $product->id }}', '{{ addslashes($product->name) }}', '{{ $product->category_id }}', '{{ $product->sku }}', {{ $product->buy_price ?? 0 }}, {{ $product->selling_price }}, {{ $product->stock }}, {{ $product->min_stock ?? 0 }}, {{ $product->is_recipe_based ? 'true' : 'false' }}, {{ $product->has_customization ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i> Edit Produk
                                            </button>
                                            <form action="{{ route('produk.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-menu-item danger">
                                                    <i class="fas fa-trash"></i> Hapus Produk
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 3rem; color:#94a3b8;">
                                    <i class="fas fa-box-open" style="font-size:2.5rem; opacity:0.2; display:block; margin-bottom:1rem;"></i>
                                    Belum ada produk. Klik <strong>"Tambah Produk"</strong> untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-area">
                    <div class="pagination-info">
                        Menampilkan {{ ($products->currentPage()-1)*$products->perPage()+1 }}-{{ min($products->currentPage()*$products->perPage(), $products->total()) }} dari {{ $products->total() }} produk
                    </div>
                    <div class="pagination-controls">
                        @if($products->onFirstPage())
                            <span class="page-btn" style="opacity:0.4;"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                        @endif
                        @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $products->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="page-btn" style="opacity:0.4;"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- ADD PRODUCT MODAL -->
<div class="modal-overlay" id="addModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle" style="color:#0052cc; margin-right:8px;"></i> Tambah Produk Baru</h2>
            <button class="modal-close" onclick="closeAddModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Gambar Produk</label>
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Produk *</label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: Kopi Arabika 250g" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Barcode / SKU</label>
                    <input type="text" name="sku" class="form-input" placeholder="Opsional">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Beli (Rp) *</label>
                    <input type="number" name="buy_price" class="form-input" placeholder="0" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual (Rp) *</label>
                    <input type="number" name="selling_price" class="form-input" placeholder="0" min="0" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok Awal *</label>
                    <input type="number" name="stock" class="form-input" placeholder="0" min="0">
                    <small style="color:#64748b; font-size:0.7rem;">Kosongkan jika ini minuman berbasis resep</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum *</label>
                    <input type="number" name="min_stock" class="form-input" placeholder="5" min="0">
                </div>
            </div>
            <div class="form-row" style="background:#f8fafc; padding:1rem; border-radius:10px; margin-bottom:1.5rem;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_recipe_based" value="1" style="width:18px; height:18px;">
                    <span style="font-weight:600; font-size:0.9rem;">Berbasis Resep (Stok ikut bahan baku)</span>
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="has_customization" value="1" style="width:18px; height:18px;">
                    <span style="font-weight:600; font-size:0.9rem;">Menu Customizable (Size, Suhu, dll)</span>
                </label>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-save" style="margin-right:8px;"></i> Simpan Produk</button>
        </form>
    </div>
</div>

<!-- EDIT PRODUCT MODAL -->
<div class="modal-overlay" id="editModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-edit" style="color:#f59e0b; margin-right:8px;"></i> Edit Produk</h2>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Ganti Gambar Produk</label>
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Produk *</label>
                <input type="text" name="name" id="edit-name" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" id="edit-category" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Barcode / SKU</label>
                    <input type="text" name="sku" id="edit-sku" class="form-input">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Beli (Rp) *</label>
                    <input type="number" name="buy_price" id="edit-cost" class="form-input" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual (Rp) *</label>
                    <input type="number" name="selling_price" id="edit-price" class="form-input" min="0" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok *</label>
                    <input type="number" name="stock" id="edit-stock" class="form-input" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum *</label>
                    <input type="number" name="min_stock" id="edit-minstock" class="form-input" min="0">
                </div>
            </div>
            <div class="form-row" style="background:#f8fafc; padding:1rem; border-radius:10px; margin-bottom:1.5rem;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_recipe_based" id="edit-is_recipe" value="1" style="width:18px; height:18px;">
                    <span style="font-weight:600; font-size:0.9rem;">Berbasis Resep</span>
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="has_customization" id="edit-has_custom" value="1" style="width:18px; height:18px;">
                    <span style="font-weight:600; font-size:0.9rem;">Customizable</span>
                </label>
            </div>
            <button type="submit" class="btn-submit" style="background:#f59e0b;"><i class="fas fa-save" style="margin-right:8px;"></i> Update Produk</button>
        </form>
    </div>
</div>

<!-- ADD CATEGORY MODAL -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-folder-plus" style="color:#0052cc; margin-right:8px;"></i> Tambah Kategori Baru</h2>
            <button class="modal-close" onclick="closeCategoryModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('produk.category.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: Snack, Minuman, Sembako" required>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-save" style="margin-right:8px;"></i> Simpan Kategori</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // === TOAST ===
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            const t = document.getElementById('toast');
            t.textContent = '{{ session("success") }}';
            t.className = 'toast show';
            setTimeout(() => t.className = 'toast', 3000);
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            const t = document.getElementById('toast');
            t.textContent = '{{ session("error") }}';
            t.className = 'toast show error';
            setTimeout(() => t.className = 'toast', 3000);
        });
    @endif

    // === ADD MODAL ===
    function openAddModal() { document.getElementById('addModal').classList.add('show'); }
    function closeAddModal() { document.getElementById('addModal').classList.remove('show'); }

    // === CATEGORY MODAL ===
    function openCategoryModal() { document.getElementById('categoryModal').classList.add('show'); }
    function closeCategoryModal() { document.getElementById('categoryModal').classList.remove('show'); }

    // === EDIT MODAL ===
    function openEditModal(id, name, catId, barcode, cost, price, stock, minStock, isRecipe, hasCustom) {
        // Close any open action menus
        document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('show'));

        document.getElementById('editForm').action = '/produk/' + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-category').value = catId;
        document.getElementById('edit-sku').value = barcode;
        document.getElementById('edit-cost').value = cost;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-stock').value = stock;
        document.getElementById('edit-minstock').value = minStock;
        document.getElementById('edit-is_recipe').checked = isRecipe;
        document.getElementById('edit-has_custom').checked = hasCustom;
        document.getElementById('editModal').classList.add('show');
    }
    function closeEditModal() { document.getElementById('editModal').classList.remove('show'); }

    // === ACTION DROPDOWN ===
    function toggleMenu(btn) {
        // Close all first
        document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('show'));
        btn.nextElementSibling.classList.toggle('show');
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown')) {
            document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('show'));
        }
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
