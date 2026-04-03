@extends('layouts.app')

@section('title', 'Kasir')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
    <style>
        .sidebar { overflow-y: auto; scrollbar-width: none; }
        .sidebar::-webkit-scrollbar { display: none; }
        .nav-link i { margin-right: 8px; }
        .empty-products { text-align: center; padding: 4rem 2rem; color: #94a3b8; grid-column: 1 / -1; }
        .empty-products i { font-size: 4rem; opacity: 0.2; margin-bottom: 1rem; display: block; }
        .empty-products p { font-size: 1rem; }
        .stock-badge { font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; position: absolute; top: 12px; right: 12px; z-index: 10; }
        .stock-ok { background: #ecfdf5; color: #10b981; }
        .stock-low { background: #fef2f2; color: #ef4444; }
        .toast-notification {
            position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999;
            background: #0052cc; color: white; padding: 1rem 1.5rem;
            border-radius: 12px; font-weight: 600; font-size: 0.9rem;
            box-shadow: 0 10px 25px -5px rgba(0,82,204,0.4);
            transform: translateX(120%); transition: transform 0.4s ease;
        }
        .toast-notification.show { transform: translateX(0); }
        .toast-notification.error { background: #ef4444; box-shadow: 0 10px 25px -5px rgba(239,68,68,0.4); }
        .product-card { cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; }
        .product-card:active { transform: scale(0.97); }
        .receipt-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 9998;
            display: none; align-items: center; justify-content: center;
        }
        .receipt-overlay.show { display: flex; }
        .receipt-card {
            background: white; border-radius: 20px; padding: 2rem;
            width: 440px; max-height: 80vh; overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            text-align: center;
        }
        .receipt-card h2 { font-size: 1.5rem; font-weight: 800; color: #10b981; margin-bottom: 0.5rem; }
        .receipt-card .receipt-icon { font-size: 3rem; color: #10b981; margin-bottom: 1rem; }
        .receipt-items { text-align: left; margin: 1.5rem 0; }
        .receipt-item-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.85rem; }
        .receipt-total-row { display: flex; justify-content: space-between; padding: 1rem 0; font-weight: 800; font-size: 1.1rem; color: #1e293b; border-top: 2px solid #1e293b; }
        .receipt-close-btn { margin-top: 1rem; background: #0052cc; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
    </style>
@endpush

@section('content')
<div class="kasir-layout">
   <x-sidebar />

    <main class="main-content">
        <x-header title="Terminal Kasir">
            <x-slot:search>
                <form action="{{ route('kasir.index') }}" method="GET" style="width:100%;">
                    <input type="text" name="search" id="global-search" placeholder="Cari Produk atau Scan Barcode..." value="{{ request('search') }}">
                </form>
            </x-slot:search>
        </x-header>


        <div class="kasir-wrapper">

            <!-- PRODUCT CATALOG -->
            <div class="product-catalog-section">
                <!-- Categories -->
                <div class="category-tabs">
                    <a href="{{ route('kasir.index') }}" class="category-tab {{ !request('category_id') ? 'active' : '' }}">
                        Semua Produk
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('kasir.index', ['category_id' => $category->id]) }}" class="category-tab {{ request('category_id') == $category->id ? 'active' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Products Grid -->
                <div class="products-grid">
                    @forelse($products as $product)
                        <div class="product-card" onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')">
                            <div class="product-img-wrapper">
                                @if($product->image && Storage::disk('public')->exists($product->image))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="object-fit: cover; height: 100%; width: 100%;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=random&color=fff&size=200" alt="{{ $product->name }}">
                                @endif
                                <span class="stock-badge {{ $product->stock <= ($product->min_stock ?: 5) ? 'stock-low' : 'stock-ok' }}">
                                    Stok: {{ $product->stock }}
                                </span>
                            </div>
                            <div class="product-info">
                                <span class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                <span class="product-name">{{ $product->name }}</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                    <button class="btn-add-product" onclick="event.stopPropagation(); addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-products">
                            <i class="fas fa-box-open"></i>
                            <p>Belum ada produk tersedia.<br>Silakan tambahkan produk terlebih dahulu melalui menu <strong>Produk</strong>.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- CART SECTION -->
            <div class="cart-section">
                <div class="cart-header">
                    <h2>Keranjang Belanja</h2>
                    <button class="btn-clear-cart" onclick="clearCart()" title="Kosongkan Keranjang">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <div class="cart-items" id="cart-container">
                    <!-- Dynamic Cart Items injected by JS -->
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="summary-subtotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Item</span>
                        <span id="summary-item-count">0 produk</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span class="total-value" id="summary-total">Rp 0</span>
                    </div>

                    <div class="payment-methods">
                        <div class="payment-methods-title">METODE PEMBAYARAN</div>
                        <div class="payment-grid">
                            <div class="payment-btn active" onclick="setPaymentMethod('Tunai', this)">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tunai</span>
                            </div>
                            <div class="payment-btn" onclick="setPaymentMethod('QRIS', this)">
                                <i class="fas fa-qrcode"></i>
                                <span>QRIS</span>
                            </div>
                            <div class="payment-btn" onclick="setPaymentMethod('Transfer', this)">
                                <i class="fas fa-university"></i>
                                <span>Transfer</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn-pay-now" id="btn-pay" onclick="processPayment()">
                        <i class="fas fa-shopping-cart"></i>
                        Bayar Sekarang
                    </button>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- TOAST NOTIFICATION -->
<div class="toast-notification" id="toast"></div>

<!-- RECEIPT OVERLAY -->
<div class="receipt-overlay" id="receipt-overlay">
    <div class="receipt-card">
        <div class="receipt-icon"><i class="fas fa-check-circle"></i></div>
        <h2>Pembayaran Berhasil!</h2>
        <p style="color:#64748b; font-size:0.85rem;" id="receipt-method">Metode: Tunai</p>
        <div class="receipt-items" id="receipt-items"></div>
        <div class="receipt-total-row">
            <span>TOTAL</span>
            <span id="receipt-total">Rp 0</span>
        </div>
        <p style="font-size:0.75rem; color:#94a3b8; margin-top:1rem;" id="receipt-id"></p>
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
            <button class="receipt-close-btn" onclick="printReceipt()" style="background: #10b981;">
                <i class="fas fa-print" style="margin-right:8px;"></i> Cetak Struk
            </button>
            <button class="receipt-close-btn" onclick="closeReceipt()">Transaksi Baru</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // === STATE ===
    let cart = [];
    let paymentMethod = 'Tunai';
    let isProcessing = false;
    let currentTransactionId = null;

    // === TOAST ===
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast-notification show' + (isError ? ' error' : '');
        setTimeout(() => { toast.className = 'toast-notification'; }, 3000);
    }

    // === FORMAT ===
    function formatRupiah(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
    }

    // === ADD TO CART ===
    function addToCart(id, name, price, stock, image = '') {
        const existing = cart.find(i => i.id === id);

        if (existing) {
            if (existing.qty >= stock) {
                showToast('Stok produk "' + name + '" tidak mencukupi!', true);
                return;
            }
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, stock, image, qty: 1 });
        }

        showToast(name + ' ditambahkan ke keranjang');
        renderCart();
    }

    // === UPDATE QTY ===
    function updateQty(id, change) {
        const idx = cart.findIndex(i => i.id === id);
        if (idx > -1) {
            const newQty = cart[idx].qty + change;
            if (newQty <= 0) {
                cart.splice(idx, 1);
            } else if (newQty > cart[idx].stock) {
                showToast('Stok tidak mencukupi!', true);
                return;
            } else {
                cart[idx].qty = newQty;
            }
            renderCart();
        }
    }

    // === CLEAR CART ===
    function clearCart() {
        if (cart.length === 0) return;
        if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            cart = [];
            renderCart();
        }
    }

    // === SET PAYMENT ===
    function setPaymentMethod(method, el) {
        paymentMethod = method;
        document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
    }

    // === RENDER CART ===
    function renderCart() {
        const container = document.getElementById('cart-container');
        let html = '';
        let subtotal = 0;
        let totalItems = 0;

        if (cart.length === 0) {
            html = '<div style="text-align:center; color:#94a3b8; padding: 3rem 1rem;">' +
                   '<i class="fas fa-shopping-basket fa-3x" style="opacity:0.15; margin-bottom:1rem; display:block;"></i>' +
                   '<p style="font-weight:600;">Keranjang masih kosong</p>' +
                   '<p style="font-size:0.8rem;">Klik produk untuk menambahkan</p></div>';
        } else {
            cart.forEach(item => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                totalItems += item.qty;
                
                const imgSrc = item.image ? item.image : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=random&color=fff&size=50`;
                
                html += `
                    <div class="cart-item">
                        <img src="${imgSrc}" class="cart-item-img" alt="${item.name}" style="object-fit: cover;">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="qty-control">
                                <button class="btn-qty" onclick="updateQty('${item.id}', -1)">-</button>
                                <input type="text" class="qty-input" value="${item.qty}" readonly>
                                <button class="btn-qty" onclick="updateQty('${item.id}', 1)">+</button>
                            </div>
                        </div>
                        <div class="cart-item-price">${formatRupiah(itemTotal)}</div>
                    </div>
                `;
            });
        }

        container.innerHTML = html;
        document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
        document.getElementById('summary-total').textContent = formatRupiah(subtotal);
        document.getElementById('summary-item-count').textContent = totalItems + ' produk';
    }

    // === PROCESS PAYMENT (AJAX TO BACKEND) ===
    function processPayment() {
        if (cart.length === 0) {
            showToast('Keranjang masih kosong!', true);
            return;
        }

        if (isProcessing) return;
        isProcessing = true;

        const btn = document.getElementById('btn-pay');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        const payload = {
            items: cart.map(item => ({
                product_id: item.id,
                qty: item.qty,
                price: item.price
            })),
            payment_method: paymentMethod,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("kasir.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showReceipt(data);
                // Save current cart data to render on the receipt view
                const receiptItemsHtml = cart.map(item => `
                    <div class="receipt-item-row">
                        <span>${item.name} x${item.qty}</span>
                        <span>${formatRupiah(item.price * item.qty)}</span>
                    </div>
                `).join('');
                document.getElementById('receipt-items').innerHTML = receiptItemsHtml;
                
                cart = [];
                renderCart();
                showToast('Transaksi berhasil!');
            } else {
                showToast(data.message || 'Gagal memproses transaksi!', true);
            }
        })
        .catch(err => {
            showToast('Terjadi kesalahan jaringan!', true);
            console.error(err);
        })
        .finally(() => {
            isProcessing = false;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Bayar Sekarang';
        });
    }

    // === RECEIPT ===
    function showReceipt(data) {
        document.getElementById('receipt-method').textContent = 'Metode: ' + paymentMethod;
        document.getElementById('receipt-total').textContent = formatRupiah(data.total);
        document.getElementById('receipt-id').textContent = 'ID: ' + data.transaction_id.substring(0, 8).toUpperCase();
        currentTransactionId = data.transaction_id;
        document.getElementById('receipt-overlay').classList.add('show');
    }

    function printReceipt() {
        if (!currentTransactionId) return;
        const printUrl = `/transaksi/${currentTransactionId}/cetak`;
        window.open(printUrl, '_blank', 'width=400,height=600');
    }

    function closeReceipt() {
        document.getElementById('receipt-overlay').classList.remove('show');
        window.location.reload();
    }

    // === INIT ===
    document.addEventListener('DOMContentLoaded', function() {
        renderCart();
    });
</script>
@endpush
