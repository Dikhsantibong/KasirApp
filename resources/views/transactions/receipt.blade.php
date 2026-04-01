<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->id }}</title>
    <style>
        @page { size: 58mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 58mm; 
            margin: 0; 
            padding: 5px; 
            font-size: 10px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .header { margin-bottom: 10px; }
        .store-name { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .table { width: 100%; border-collapse: collapse; }
        .row-item td { padding: 2px 0; vertical-align: top; }
        .total-section { margin-top: 5px; font-weight: bold; }
        .footer { margin-top: 15px; font-size: 8px; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header text-center">
        <div class="store-name">{{ $store->name ?? 'KASIRAPP POS' }}</div>
        <div>{{ $store->address ?? 'Jl. Merdeka No. 123, Indonesia' }}</div>
        <div>Telp: 0812-3456-7890</div>
    </div>

    <div class="divider"></div>

    <div style="margin-bottom: 5px;">
        ID: {{ strtoupper(substr($transaction->id, 0, 8)) }}<br>
        Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
        Kasir: {{ $transaction->user->name ?? 'Admin' }}<br>
        Plgn: {{ $transaction->customer->name ?? 'Umum' }}
    </div>

    <div class="divider"></div>

    <table class="table">
        @foreach($transaction->items as $item)
        <tr class="row-item">
            <td colspan="2">{{ $item->product->name }}</td>
        </tr>
        <tr class="row-item">
            <td>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="table total-section">
        <tr>
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>METODE</td>
            <td class="text-right">{{ $transaction->payment_method }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer text-center">
        TERIMA KASIH TELAH BERBELANJA<br>
        Barang yang sudah dibeli<br>
        tidak dapat ditukar/dikembalikan
    </div>

    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.close()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">Tutup Halaman</button>
    </div>
</body>
</html>
