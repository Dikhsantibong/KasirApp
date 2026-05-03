@extends('layouts.app')

@section('title', 'Bahan Baku')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bahan-baku.css') }}">
@endpush

@section('content')
<div class="ingredient-layout">
    <x-sidebar />

    <main class="main-content">
        <x-header title="Manajemen Bahan Baku">
            <x-slot:search>
                <form action="{{ route('ingredients.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" placeholder="Cari Bahan Baku..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daftar Bahan Baku</h1>
                    <p class="page-subtitle">Kelola stok bahan mentah untuk produksi minuman.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-add" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Tambah Bahan Baku
                    </button>
                </div>
            </div>

            <!-- STATS -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon icon-amber"><i class="fas fa-leaf"></i></div>
                    <div class="stat-info">
                        <h3>Total Bahan Baku</h3>
                        <p>{{ $ingredients->total() }} Item</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <h3>Stok Menipis</h3>
                        <p>{{ \App\Models\Ingredient::whereColumn('stock', '<=', 'min_stock')->count() }} Item</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-coins"></i></div>
                    <div class="stat-info">
                        <h3>Nilai Inventaris</h3>
                        <p>Rp {{ number_format(\App\Models\Ingredient::selectRaw('SUM(stock * cost_per_unit) as total')->value('total') ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <table class="ingredient-table">
                    <thead>
                        <tr>
                            <th>NAMA BAHAN</th>
                            <th>STOK SAAT INI</th>
                            <th>BATAS MINIMUM</th>
                            <th>HARGA / SATUAN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingredients as $item)
                        <tr>
                            <td>
                                <div class="ingredient-name">
                                    <div class="name-icon"><i class="fas fa-leaf"></i></div>
                                    <span>{{ $item->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="stock-badge {{ $item->stock <= $item->min_stock ? 'stock-low' : 'stock-ok' }}">
                                    {{ number_format($item->stock, 1) }} {{ $item->unit }}
                                </span>
                            </td>
                            <td>
                                <span class="min-stock-text">{{ number_format($item->min_stock, 1) }} {{ $item->unit }}</span>
                            </td>
                            <td>
                                <span class="price-text">Rp {{ number_format($item->cost_per_unit, 0, ',', '.') }}</span>
                            </td>
                            <td class="actions-cell">
                                <button class="btn-edit" onclick="openEditModal({{ $item }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('ingredients.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus bahan baku ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-leaf"></i>
                                    Belum ada data bahan baku. Klik <strong>"Tambah Bahan Baku"</strong> untuk memulai.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-area">
                    {{ $ingredients->links() }}
                </div>
            </div>
        </div>
    </main>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="addModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle" style="color: var(--primary);"></i> Tambah Bahan Baku</h2>
            <button class="modal-close" onclick="closeAddModal()"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('ingredients.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Bahan *</label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: Kopi Arabika, Susu Full Cream" required>
            </div>
            <div class="form-group">
                <label class="form-label">Satuan *</label>
                <input type="text" name="unit" class="form-input" placeholder="gr, ml, pcs, liter" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok Awal *</label>
                    <input type="number" step="0.01" name="stock" class="form-input" value="0" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Minimum *</label>
                    <input type="number" step="0.01" name="min_stock" class="form-input" value="0" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Harga per Satuan (Rp) *</label>
                <input type="number" step="0.01" name="cost_per_unit" class="form-input" value="0" min="0" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="editModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Bahan Baku</h2>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Bahan *</label>
                <input type="text" name="name" id="edit-name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Satuan *</label>
                <input type="text" name="unit" id="edit-unit" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok Saat Ini *</label>
                    <input type="number" step="0.01" name="stock" id="edit-stock" class="form-input" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Minimum *</label>
                    <input type="number" step="0.01" name="min_stock" id="edit-min-stock" class="form-input" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Harga per Satuan (Rp) *</label>
                <input type="number" step="0.01" name="cost_per_unit" id="edit-cost" class="form-input" min="0" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit" style="background: #f59e0b;"><i class="fas fa-save"></i> Update</button>
            </div>
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

    // === EDIT MODAL ===
    function openEditModal(item) {
        document.getElementById('editForm').action = `/bahan-baku/${item.id}`;
        document.getElementById('edit-name').value = item.name;
        document.getElementById('edit-unit').value = item.unit;
        document.getElementById('edit-stock').value = item.stock;
        document.getElementById('edit-min-stock').value = item.min_stock;
        document.getElementById('edit-cost').value = item.cost_per_unit;
        document.getElementById('editModal').classList.add('show');
    }
    function closeEditModal() { document.getElementById('editModal').classList.remove('show'); }

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
