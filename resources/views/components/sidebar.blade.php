 <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <!-- Logo Utama (Muncul saat sidebar terbuka) -->
                <img src="{{ asset('main_logo.png') }}" alt="Logo" class="sidebar-logo logo-full">
                
                <!-- Logo Ikon (Muncul saat sidebar ditutup) - Silakan ubah src-nya nanti -->
                <img src="{{ asset('icon_logo.png') }}" alt="Icon" class="sidebar-logo logo-collapsed">
            </div>
        </div>


        <style>
            .sidebar-brand { display: flex; align-items: center; gap: 12px; }
            .sidebar-logo { height: 38px; width: auto; object-fit: contain; }
            .brand-logo { 
                background: #fff; 
                padding: 8px; 
                border-radius: 12px; 
                box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
                display: flex; 
                align-items: center; 
                justify-content: center;
                border: 1px solid #f1f5f9;
                transition: padding 0.3s ease;
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
                padding: 4px;
            }
            body.sidebar-collapsed .sidebar .sidebar-logo {
                height: 24px;
            }
            body.sidebar-collapsed .logo-full {
                display: none;
            }
            body.sidebar-collapsed .logo-collapsed {
                display: block;
            }
        </style>


        <nav class="nav-menu">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('kasir.index') }}" class="nav-link {{ request()->routeIs('kasir.index') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i>
                <span>Kasir</span>
            </a>
            <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Transaksi</span>
            </a>

            @if(auth()->user()->role === 'Owner')
            <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.index') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Produk</span>
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