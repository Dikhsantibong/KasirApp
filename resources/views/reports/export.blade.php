<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bisnis - {{ date('F Y') }}</title>
    <style>
        body { font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.6; margin: 0; padding: 60px; background: #fff; }
        .report-container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 30px; margin-bottom: 50px; }
        .company-info h1 { margin: 0; color: #0052cc; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        .company-info p { margin: 8px 0 0 0; color: #64748b; font-size: 15px; }
        .report-meta { text-align: right; }
        .report-meta h2 { margin: 0; font-size: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: #1e293b; }
        .report-meta p { margin: 8px 0 0 0; color: #64748b; font-size: 13px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 60px; }
        .kpi-card { padding: 25px 15px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; transition: all 0.3s ease; }
        .kpi-card span { display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; }
        .kpi-card strong { font-size: 20px; color: #1e293b; display: block; }
        .kpi-card.highlight { background: #f0f7ff; border-color: #0052cc; box-shadow: 0 10px 15px -3px rgba(0, 82, 204, 0.05); }
        .kpi-card.highlight strong { color: #0052cc; }

        .section-title { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-left: 5px solid #0052cc; padding-left: 15px; margin: 50px 0 25px 0; color: #1e293b; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { text-align: left; font-size: 12px; font-weight: 700; color: #64748b; padding: 15px 10px; border-bottom: 2px solid #f1f5f9; background: #fafafa; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 18px 10px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        
        .status-badge { font-size: 11px; padding: 2px 8px; border-radius: 20px; background: #ecfdf5; color: #10b981; }

        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px; }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #0052cc; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <p style="font-size: 12px; color: #64748b;">Gunakan dialog cetak untuk menyimpan sebagai PDF</p>
    </div>

    <div class="report-container">
        <div class="header">
            <div class="company-info">
                <h1>KASIR APP</h1>
                <p>Laporan Keuangan & Operasional Bisnis</p>
            </div>
            <div class="report-meta">
                <h2>LAPORAN BULANAN</h2>
                <p>Periode: {{ date('F Y') }}</p>
                <p>Dicetak: {{ date('d M Y, H:i') }}</p>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <span>Total Penjualan</span>
                <strong>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</strong>
            </div>
            <div class="kpi-card">
                <span>Total Pengeluaran</span>
                <strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
            </div>
            <div class="kpi-card">
                <span>Total Transaksi</span>
                <strong>{{ $totalTransaksi }}</strong>
            </div>
            <div class="kpi-card highlight">
                <span>Keuntungan Bersih</span>
                <strong>Rp {{ number_format($keuntunganBersih, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="section-title">Ringkasan Margin Produk</div>
        <table>
            <thead>
                <tr>
                    <th>NAMA PRODUK</th>
                    <th>HARGA BELI</th>
                    <th>HARGA JUAL</th>
                    <th>MARGIN (Rp)</th>
                    <th class="text-right">MARGIN (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topMarginProducts as $tp)
                <tr>
                    <td>{{ $tp->name }}</td>
                    <td>Rp {{ number_format($tp->cost_price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($tp->selling_price, 0, ',', '.') }}</td>
                    <td class="font-bold">Rp {{ number_format($tp->margin, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $tp->margin_pct }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">Daftar Pengeluaran</div>
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>KETERANGAN</th>
                    <th>KATEGORI</th>
                    <th class="text-right">NOMINAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $ex)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ex->created_at)->format('d M Y') }}</td>
                    <td>{{ $ex->description }}</td>
                    <td>{{ $ex->category }}</td>
                    <td class="text-right">Rp {{ number_format($ex->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;">Tidak ada pengeluaran periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Riwayat Transaksi Terbaru</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>WAKTU</th>
                    <th>METODE</th>
                    <th>STATUS</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $rt)
                <tr>
                    <td>#{{ strtoupper(substr($rt->id, 0, 8)) }}</td>
                    <td>{{ \Carbon\Carbon::parse($rt->created_at)->format('d M, H:i') }}</td>
                    <td>{{ $rt->payment_method }}</td>
                    <td><span class="status-badge">Lunas</span></td>
                    <td class="text-right font-bold">Rp {{ number_format($rt->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Laporan ini dihasilkan secara otomatis oleh Kasir App.</p>
            <p>&copy; {{ date('Y') }} Kasir App Professional POS System</p>
        </div>
    </div>

    <script>
        // Auto trigger print dialog
        window.onload = function() {
            // setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
