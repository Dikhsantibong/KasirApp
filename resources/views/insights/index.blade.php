@extends('layouts.app')

@section('title', 'Insight Bisnis (AI)')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/insight.css') }}">
@endpush

@section('content')
<div class="insight-layout">
   <x-sidebar />

    <main class="main-content">
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <form action="#" method="GET" style="width:100%;">
                    <input type="text" placeholder="Cari insight atau data...">
                </form>
            </div>
            
            <div style="display:flex; align-items:center; gap:15px;">
                <div style="display:flex; align-items:center; gap:6px; color:#10b981; font-weight:600; font-size:0.85rem;">
                    <i class="fas fa-sync-alt"></i> Sinkronisasi Berhasil
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; cursor: pointer;"><i class="far fa-bell"></i></div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="text-align:right;">
                        <span style="display:block; font-weight:700; font-size:0.85rem; color:#1e293b;">Budi Santoso</span>
                        <span style="display:block; font-size:0.75rem; color:#94a3b8;">Pemilik Toko</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=111&color=fff" style="width:36px; height:36px; border-radius:50%;">
                </div>
            </div>
        </header>

        <div class="page-content">
            <div class="ai-badge">
                <i class="fas fa-sparkles"></i> POWERED BY AI ANALYSIS
            </div>
            <h1 class="page-title">Insight Bisnis (AI)</h1>
            <p class="page-subtitle">Kami menganalisis data Anda untuk memberikan saran strategis yang dapat meningkatkan profitabilitas toko Anda hari ini.</p>

            <div class="bento-grid">
                <!-- 1. Peluang Penjualan Hari Ini -->
                <div class="bento-card col-span-2">
                    <div class="peluang-header">
                        <div>
                            <h2>Peluang Penjualan Hari Ini</h2>
                            <p>Berdasarkan pola transaksi 30 hari terakhir</p>
                        </div>
                        <div class="update-badge">Update: 5 Menit Lalu</div>
                    </div>

                    <div class="peluang-stats">
                        <div class="p-stat-item">
                            <h5>PREDIKSI PENDAPATAN</h5>
                            <h3>Rp 4.2M</h3>
                            <div class="p-stat-sub"><i class="fas fa-arrow-trend-up"></i> +12.4%</div>
                        </div>
                        <div class="p-stat-item">
                            <h5>TARGET TRANSAKSI</h5>
                            <h3 class="dark">{{ $targetTransactions ?? 142 }}</h3>
                            <div class="p-stat-sub gray">Sisa {{ $remainingTarget ?? 34 }} lagi untuk target</div>
                        </div>
                        <div class="p-stat-item">
                            <h5>EFIKASI AI</h5>
                            <h3 class="dark">94%</h3>
                            <div class="p-stat-sub gray">Akurasi prediksi minggu ini</div>
                        </div>
                    </div>

                    <div class="peluang-footer">
                        <div class="avatar-group">
                            <div class="avatar-item first"><img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?w=100&h=100&fit=crop" alt="Coffee"></div>
                            <div class="avatar-item"><img src="https://images.unsplash.com/photo-1550617931-e17a7b70dce2?w=100&h=100&fit=crop" alt="Cake"></div>
                            <div class="avatar-item"><img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?w=100&h=100&fit=crop" alt="Latte"></div>
                            <div class="avatar-item more">+5</div>
                        </div>
                        <button class="btn-blue">Lihat Laporan Detail <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- 2. Performa Mingguan -->
                <div class="bento-card col-span-1 performa-card">
                    <div class="icon-red-light"><i class="fas fa-chart-line-down"></i></div>
                    <h3>Performa Mingguan</h3>
                    <p>Penjualan Anda menurun <strong>5%</strong> dibanding minggu lalu. AI mendeteksi penurunan volume pada kategori Minuman Dingin.</p>
                    <div class="alert-text"><i class="fas fa-exclamation-triangle"></i> BUTUH PERHATIAN SEGERA</div>
                </div>

                <!-- 3. Restock Cerdas -->
                <div class="bento-card col-span-1">
                    <div class="icon-box-orange"><i class="fas fa-box-open"></i></div>
                    <h3>Restock Cerdas</h3>
                    <p>Produk <strong>{{ $lowStockProduct->name ?? 'Kopi Arabika 250g' }}</strong> hampir habis, segera pesan ke <a href="#" class="link-blue">{{ $lowStockProduct->category->name ?? 'Supplier X' }}</a> untuk menghindari kekosongan stok di akhir pekan.</p>
                    <button class="btn-outline">BUAT PO SEKARANG</button>
                </div>

                <!-- 4. Waktu Teramai -->
                <div class="bento-card col-span-1">
                    <div class="icon-box-blue"><i class="far fa-clock"></i></div>
                    <h3>Waktu Teramai</h3>
                    <p>Pelanggan paling sering berkunjung jam <strong>19:00</strong>. Pastikan jumlah staf mencukupi untuk menjaga kecepatan pelayanan.</p>
                    
                    <div class="mini-chart">
                        <div class="m-bar" style="height: 15%;"></div>
                        <div class="m-bar" style="height: 25%;"></div>
                        <div class="m-bar" style="height: 35%;"></div>
                        <div class="m-bar active" style="height: 100%;"></div>
                        <div class="m-bar" style="height: 45%;"></div>
                        <div class="m-bar" style="height: 20%;"></div>
                    </div>
                </div>

                <!-- 5. Optimasi Stok -->
                <div class="bento-card col-span-1 optimasi-card">
                    <div class="saran-ai-badge"><i class="fas fa-star"></i> SARAN AI</div>
                    <h3>Optimasi Stok</h3>
                    <p>Berikan promo "Buy 1 Get 1" untuk <u>{{ $topProduct->name ?? 'Kopi Susu Gula Aren' }}</u> mulai pukul 15:00 untuk menghabiskan stok harian yang berlebih.</p>
                    <button class="btn-white">AKTIFKAN PROMO</button>
                </div>

                <!-- 6. Analisis Sentimen -->
                <div class="bento-card col-span-2 sentimen-card">
                    <h2>Analisis Sentimen</h2>
                    <p>Pelanggan menyukai kecepatan pelayanan Anda minggu ini, namun ada beberapa catatan mengenai <strong>suasana musik</strong> di area indoor.</p>
                    
                    <div class="mood-badges">
                        <div class="mood-badge happy"><i class="fas fa-laugh-beam"></i> 88% Puas</div>
                        <div class="mood-badge neutral"><i class="fas fa-meh"></i> 10% Netral</div>
                    </div>
                </div>

                <!-- 7. Ekspansi -->
                <div class="bento-card col-span-1 ekspansi-card">
                    <div class="icon-box-rocket"><i class="fas fa-rocket"></i></div>
                    <h3>Siap untuk Ekspansi?</h3>
                    <p>Data menunjukkan Anda bisa membuka cabang baru dalam 6 bulan ke depan jika tren ini berlanjut.</p>
                </div>
            </div>
            
        </div>
    </main>
</div>

<!-- FLOATING WIDGET -->
<div class="ai-float-widget">
    <div class="ai-float-icon"><i class="fas fa-robot"></i></div>
    <div class="ai-float-text">
        <h5>TANYA AI</h5>
        <p>Butuh bantuan analisis data lainnya?</p>
    </div>
    <div class="ai-float-arrow"><i class="fas fa-comment-dots" style="font-size:1.25rem;"></i></div>
</div>
@endsection
