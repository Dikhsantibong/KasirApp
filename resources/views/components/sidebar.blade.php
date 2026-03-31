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
            <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Transaksi</span>
            </a>
            <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}">
                <i class="fas fa-archive"></i>
                <span>Stock</span>
            </a>
            <a href="{{ route('pembelian.index') }}" class="nav-link {{ request()->routeIs('pembelian.index') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Pembelian</span>
            </a>
            <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->routeIs('pelanggan.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Pelanggan</span>
            </a>
            <a href="{{ route('hutang.index') }}" class="nav-link {{ request()->routeIs('hutang.index') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt"></i>
                <span>Hutang</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="nav-link {{ request()->routeIs('pengeluaran.index') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i>
                <span>Pengeluaran</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Laporan</span>
            </a>
            <a href="{{ route('insight.index') }}" class="nav-link {{ request()->routeIs('insight.index') ? 'active' : '' }}">
                <i class="fas fa-magic"></i>
                <span style="display:flex; justify-content:space-between; width:100%;">
                    Insight Bisnis <small style="background:#0052cc; color:white; padding:2px 6px; border-radius:10px; font-size:0.6rem;">AI</small>
                </span>
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