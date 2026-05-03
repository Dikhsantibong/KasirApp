 <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <!-- Logo Utama (Muncul saat sidebar terbuka) -->
                <img src="{{ asset('main_logo.png') }}" alt="Logo" class="sidebar-logo logo-full">
                
                <!-- Logo Ikon (Muncul saat sidebar ditutup) -->
                <img src="{{ asset('main_logo.png') }}" alt="Icon" class="sidebar-logo logo-collapsed">
            </div>
        </div>


        <style>
            .sidebar { padding-top: 0 !important; }
            .sidebar-brand { 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                padding: 0.25rem 0 !important; 
                margin-bottom: -0.5rem !important; 
                width: 100%;
            }
            .sidebar-logo { height: 130px; width: auto; object-fit: contain; }
            .brand-logo { 
                background: transparent; 
                padding: 0; 
                display: flex; 
                align-items: center; 
                justify-content: center;
                transition: all 0.3s ease;
                overflow: visible;
            }

            /* Collapsed State Styles */
            .sidebar, .main-content {
                transition: width 0.3s ease, margin-left 0.3s ease;
            }
            .sidebar .nav-link span {
                transition: opacity 0.3s ease, width 0.3s ease;
                white-space: nowrap;
            }
            .sidebar .nav-link i {
                transition: margin-right 0.3s ease, font-size 0.3s ease;
            }
            .sidebar-logo {
                transition: height 0.3s ease;
            }
            
            .logo-collapsed {
                display: none;
            }

            body.sidebar-collapsed .sidebar {
                width: 80px;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            body.sidebar-collapsed .main-content {
                margin-left: 80px;
            }
            body.sidebar-collapsed .sidebar .nav-link span {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }
            body.sidebar-collapsed .sidebar .nav-link {
                justify-content: center;
                padding: 12px;
            }
            body.sidebar-collapsed .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            body.sidebar-collapsed .sidebar .brand-logo {
                padding: 0;
            }
            body.sidebar-collapsed .sidebar .sidebar-logo {
                height: 45px;
            }
            body.sidebar-collapsed .logo-full {
                display: none;
            }
            body.sidebar-collapsed .logo-collapsed {
                display: block;
            }
        </style>


        <nav class="nav-menu" style="position: relative; z-index: 10;">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('kasir.index') }}" class="nav-link {{ request()->routeIs('kasir.index') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i>
                <span>Kasir</span>
            </a>
            <a href="{{ route('barista.index') }}" class="nav-link {{ request()->routeIs('barista.index') ? 'active' : '' }}">
                <i class="fas fa-coffee"></i>
                <span>Barista Queue</span>
            </a>
            <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Transaksi</span>
            </a>

            @if(in_array(auth()->user()->role, ['Owner', 'Manager']))
            <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.index') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Produk / Menu</span>
            </a>
            <a href="{{ route('ingredients.index') }}" class="nav-link {{ request()->routeIs('ingredients.index') ? 'active' : '' }}">
                <i class="fas fa-leaf"></i>
                <span>Bahan Baku</span>
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
            @endif

            @if(auth()->user()->role === 'Owner')
            <a href="{{ route('hutang.index') }}" class="nav-link {{ request()->routeIs('hutang.index') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt"></i>
                <span>Hutang</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="nav-link {{ request()->routeIs('pengeluaran.index') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i>
                <span>Pengeluaran</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            @endif
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