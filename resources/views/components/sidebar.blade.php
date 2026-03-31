 <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <h2 class="brand-name">Toko Saya</h2>
                <p class="brand-subtitle">Premium POS</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('kasir.index') }}" class="nav-link {{ request()->routeIs('kasir.index') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i>
                <span>Kasir</span>
            </a>
            <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.index') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-archive"></i>
                <span>Stok</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                <span>Pembelian</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Pelanggan</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-credit-card"></i>
                <span>Hutang</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Pengeluaran</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-lightbulb"></i>
                <span>Insight Bisnis</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link" style="border:none; background:none; width: 100%; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>