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
                        <div class="product-card" onclick="openCustomModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->has_customization ? 'true' : 'false' }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')">
                            <div class="product-img-wrapper">
                                @if($product->image && Storage::disk('public')->exists($product->image))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="object-fit: cover; height: 100%; width: 100%;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=random&color=fff&size=200" alt="{{ $product->name }}">
                                @endif
                                @if(!$product->is_recipe_based)
                                <span class="stock-badge {{ $product->stock <= ($product->min_stock ?: 5) ? 'stock-low' : 'stock-ok' }}">
                                    Stok: {{ $product->stock }}
                                </span>
                                @endif
                            </div>
                            <div class="product-info">
                                <span class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                <span class="product-name">{{ $product->name }}</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                    <button class="btn-add-product" onclick="event.stopPropagation(); openCustomModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->has_customization ? 'true' : 'false' }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')">
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
                    <h2>Pesanan Aktif</h2>
                    <div style="display:flex; gap: 8px;">
                        <button class="btn-clear-cart" onclick="holdCart()" title="Hold Order" style="background: #fefce8; color: #d97706; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-pause"></i> Hold
                        </button>
                        <button class="btn-clear-cart" onclick="recallCart()" title="Recall Order" style="background: #eff6ff; color: #3b82f6; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-play"></i> Recall
                        </button>
                        <button class="btn-clear-cart" onclick="clearCart()" title="Kosongkan">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
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

<!-- CUSTOM DRINK MODAL -->
<div class="receipt-overlay" id="custom-modal" style="z-index: 10000;">
    <div class="receipt-card" style="width: 500px; text-align: left; padding: 2rem;">
        <h2 id="modal-product-name" style="font-size: 1.2rem; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem; margin-bottom: 1.5rem;">Kustomisasi Minuman</h2>
        
        <input type="hidden" id="modal-product-id">
        <input type="hidden" id="modal-product-price">
        <input type="hidden" id="modal-product-image">

        <div style="margin-bottom: 1rem;">
            <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 0.5rem;">Ukuran</label>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn-custom-option size-option active" data-value="Small" onclick="selectOption(this, 'size')">Small</button>
                <button type="button" class="btn-custom-option size-option" data-value="Medium" onclick="selectOption(this, 'size')">Medium (+5000)</button>
                <button type="button" class="btn-custom-option size-option" data-value="Large" onclick="selectOption(this, 'size')">Large (+8000)</button>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 0.5rem;">Temperature</label>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn-custom-option temp-option active" data-value="Hot" onclick="selectOption(this, 'temp')">Hot</button>
                <button type="button" class="btn-custom-option temp-option" data-value="Iced" onclick="selectOption(this, 'temp')">Iced</button>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 0.5rem;">Gula</label>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn-custom-option sugar-option active" data-value="Normal" onclick="selectOption(this, 'sugar')">Normal</button>
                <button type="button" class="btn-custom-option sugar-option" data-value="Less" onclick="selectOption(this, 'sugar')">Less Sugar</button>
                <button type="button" class="btn-custom-option sugar-option" data-value="No Sugar" onclick="selectOption(this, 'sugar')">No Sugar</button>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 0.5rem;">Catatan Khusus</label>
            <textarea id="modal-notes" rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.5rem; font-family: inherit;" placeholder="Contoh: Jangan terlalu panas..."></textarea>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 2px solid #f1f5f9; padding-top: 1.5rem;">
            <button type="button" onclick="closeCustomModal()" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: 1px solid #cbd5e1; background: white; font-weight: 600; cursor: pointer;">Batal</button>
            <button type="button" onclick="confirmCustomAddToCart()" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: none; background: #d97706; color: white; font-weight: 600; cursor: pointer;">Tambah ke Pesanan</button>
        </div>
    </div>
</div>

<style>
    .btn-custom-option { flex: 1; padding: 0.5rem; border: 1px solid #cbd5e1; background: white; border-radius: 8px; font-weight: 600; color: #64748b; cursor: pointer; transition: all 0.2s; }
    .btn-custom-option.active { background: #fffbeb; border-color: #d97706; color: #d97706; }
</style>

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
    let offlineQueue = JSON.parse(localStorage.getItem('kasir_offline_queue')) || [];
    let holdQueue = JSON.parse(localStorage.getItem('kasir_hold_queue')) || [];

    // === NETWORK LISTENERS ===
    window.addEventListener('online', () => {
        updateNetworkStatus(true);
        syncOfflineQueue();
    });
    window.addEventListener('offline', () => {
        updateNetworkStatus(false);
    });

    function updateNetworkStatus(isOnline, isSyncing = false) {
        const badge = document.getElementById('global-network-badge');
        const icon = document.getElementById('global-network-icon');
        const text = document.getElementById('global-network-text');
        
        if (!badge) return;

        if (isSyncing) {
            badge.className = 'system-status syncing';
            icon.className = 'fas fa-sync fa-spin';
            text.textContent = 'Menyinkronkan...';
        } else if (isOnline) {
            badge.className = 'system-status online';
            icon.className = 'fas fa-wifi';
            text.textContent = 'Online';
        } else {
            badge.className = 'system-status offline';
            icon.className = 'fas fa-wifi-slash';
            text.textContent = 'Offline';
        }
    }

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

    // === CUSTOM MODAL LOGIC ===
    function openCustomModal(id, name, price, hasCustom, image) {
        if (!hasCustom) {
            addToCart(id, name, price, image, {});
            return;
        }
        
        document.getElementById('modal-product-id').value = id;
        document.getElementById('modal-product-name').textContent = name;
        document.getElementById('modal-product-price').value = price;
        document.getElementById('modal-product-image').value = image;
        document.getElementById('modal-notes').value = '';
        
        // Reset selections
        document.querySelectorAll('.btn-custom-option').forEach(el => el.classList.remove('active'));
        document.querySelector('.size-option[data-value="Small"]').classList.add('active');
        document.querySelector('.temp-option[data-value="Hot"]').classList.add('active');
        document.querySelector('.sugar-option[data-value="Normal"]').classList.add('active');
        
        document.getElementById('custom-modal').classList.add('show');
    }

    function closeCustomModal() {
        document.getElementById('custom-modal').classList.remove('show');
    }

    function selectOption(btn, type) {
        document.querySelectorAll(`.${type}-option`).forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
    }

    function confirmCustomAddToCart() {
        const id = document.getElementById('modal-product-id').value;
        const name = document.getElementById('modal-product-name').textContent;
        let basePrice = parseFloat(document.getElementById('modal-product-price').value);
        const image = document.getElementById('modal-product-image').value;
        
        const size = document.querySelector('.size-option.active').getAttribute('data-value');
        const temp = document.querySelector('.temp-option.active').getAttribute('data-value');
        const sugar = document.querySelector('.sugar-option.active').getAttribute('data-value');
        const notes = document.getElementById('modal-notes').value;

        // Calculate price adjustment
        let finalPrice = basePrice;
        if (size === 'Medium') finalPrice += 5000;
        if (size === 'Large') finalPrice += 8000;

        const custom = { size, temperature: temp, sugar, notes };
        
        addToCart(id, name, finalPrice, image, custom);
        closeCustomModal();
    }

    // === ADD TO CART ===
    function addToCart(id, name, price, image, custom = {}) {
        // Generate a unique ID based on product + custom attributes to stack identical orders
        const customHash = JSON.stringify(custom);
        const cartItemId = id + '_' + btoa(customHash).substring(0, 10);

        const existing = cart.find(i => i.cartItemId === cartItemId);

        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ cartItemId, id, name, price, image, qty: 1, custom });
        }

        showToast(name + ' ditambahkan');
        renderCart();
    }

    // === UPDATE QTY ===
    function updateQty(cartItemId, change) {
        const idx = cart.findIndex(i => i.cartItemId === cartItemId);
        if (idx > -1) {
            const newQty = cart[idx].qty + change;
            if (newQty <= 0) {
                cart.splice(idx, 1);
            } else {
                cart[idx].qty = newQty;
            }
            renderCart();
        }
    }

    // === HOLD / RECALL CART ===
    function holdCart() {
        if (cart.length === 0) return;
        const name = prompt("Masukkan nama/meja pesanan untuk disimpan:");
        if (!name) return;
        
        holdQueue.push({ id: Date.now(), name, cart: [...cart] });
        localStorage.setItem('kasir_hold_queue', JSON.stringify(holdQueue));
        
        cart = [];
        renderCart();
        showToast(`Pesanan ${name} disimpan sementara`);
    }

    function recallCart() {
        if (holdQueue.length === 0) {
            showToast('Tidak ada pesanan yang tersimpan', true);
            return;
        }
        
        let text = "Pilih pesanan untuk dipanggil:\n";
        holdQueue.forEach((item, idx) => {
            text += `${idx + 1}. ${item.name} (${item.cart.length} item)\n`;
        });
        
        const selection = prompt(text + "\nMasukkan nomor:");
        const idx = parseInt(selection) - 1;
        
        if (idx >= 0 && idx < holdQueue.length) {
            if (cart.length > 0) {
                if (!confirm("Keranjang saat ini akan digantikan. Lanjutkan?")) return;
            }
            cart = holdQueue[idx].cart;
            holdQueue.splice(idx, 1);
            localStorage.setItem('kasir_hold_queue', JSON.stringify(holdQueue));
            renderCart();
            showToast(`Pesanan ${cart.name || 'dipanggil'} berhasil dikembalikan`);
        }
    }

    // === CLEAR CART ===
    function clearCart() {
        if (cart.length === 0) return;
        if (confirm('Apakah Anda yakin ingin mengosongkan pesanan?')) {
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
                   '<i class="fas fa-coffee fa-3x" style="opacity:0.15; margin-bottom:1rem; display:block;"></i>' +
                   '<p style="font-weight:600;">Belum ada pesanan</p>' +
                   '<p style="font-size:0.8rem;">Pilih menu untuk memulai</p></div>';
        } else {
            cart.forEach(item => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                totalItems += item.qty;
                
                const imgSrc = item.image ? item.image : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=random&color=fff&size=50`;
                
                let customText = '';
                if (item.custom && Object.keys(item.custom).length > 0) {
                    customText = `<div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                        ${item.custom.size ? item.custom.size + ', ' : ''}
                        ${item.custom.temperature ? item.custom.temperature + ', ' : ''}
                        ${item.custom.sugar ? item.custom.sugar : ''}
                        ${item.custom.notes ? '<br><i class="fas fa-comment-alt" style="margin-right:4px;"></i>'+item.custom.notes : ''}
                    </div>`;
                }

                html += `
                    <div class="cart-item">
                        <img src="${imgSrc}" class="cart-item-img" alt="${item.name}" style="object-fit: cover;">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            ${customText}
                            <div class="qty-control" style="margin-top: 6px;">
                                <button class="btn-qty" onclick="updateQty('${item.cartItemId}', -1)">-</button>
                                <input type="text" class="qty-input" value="${item.qty}" readonly>
                                <button class="btn-qty" onclick="updateQty('${item.cartItemId}', 1)">+</button>
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
        document.getElementById('summary-item-count').textContent = totalItems + ' item';
    }

    // === PROCESS PAYMENT ===
    function processPayment() {
        if (cart.length === 0) {
            showToast('Pesanan masih kosong!', true);
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
                price: item.price,
                name: item.name,
                customizations: item.custom
            })),
            payment_method: paymentMethod,
            _token: '{{ csrf_token() }}',
            offline_id: 'OFFLINE-' + Date.now() + Math.floor(Math.random() * 1000)
        };

        // Calculate total for local receipt
        const totalAmount = payload.items.reduce((sum, item) => sum + (item.price * item.qty), 0);

        if (!navigator.onLine) {
            // OFFLINE MODE: Save to queue immediately
            saveToOfflineQueue(payload);
            showOfflineReceipt(payload.offline_id, totalAmount, payload.items);
            resetCartUI();
            return;
        }

        // ONLINE MODE: Try fetching
        fetch('{{ route("kasir.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) throw new Error("Network response was not ok");
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showReceipt(data);
                
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
            // If fetch fails (maybe connection dropped right after check)
            console.warn('Fetch failed, switching to offline queue', err);
            saveToOfflineQueue(payload);
            showOfflineReceipt(payload.offline_id, totalAmount, payload.items);
            resetCartUI();
        })
        .finally(() => {
            isProcessing = false;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Bayar Sekarang';
        });
    }

    // === OFFLINE QUEUE LOGIC ===
    function saveToOfflineQueue(payload) {
        offlineQueue.push(payload);
        localStorage.setItem('kasir_offline_queue', JSON.stringify(offlineQueue));
        updateNetworkStatus(false);
        showToast('Offline: Transaksi disimpan di perangkat', true);
    }

    function syncOfflineQueue() {
        if (!navigator.onLine || offlineQueue.length === 0) return;
        
        updateNetworkStatus(true, true); // Show Syncing status

        const syncPromises = offlineQueue.map(payload => {
            return fetch('{{ route("kasir.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            }).then(res => res.json());
        });

        Promise.allSettled(syncPromises).then(results => {
            const successfulIndexes = [];
            results.forEach((result, idx) => {
                if (result.status === 'fulfilled' && result.value.success) {
                    successfulIndexes.push(idx);
                }
            });

            // Remove successful transactions from queue (reverse loop to not mess up indexes)
            for (let i = successfulIndexes.length - 1; i >= 0; i--) {
                offlineQueue.splice(successfulIndexes[i], 1);
            }

            localStorage.setItem('kasir_offline_queue', JSON.stringify(offlineQueue));
            
            if (offlineQueue.length === 0) {
                showToast('Sinkronisasi data offline berhasil!');
            } else {
                showToast(`Sinkronisasi sebagian berhasil. Tersisa ${offlineQueue.length} data.`, true);
            }
            
            updateNetworkStatus(true);
        });
    }

    function resetCartUI() {
        isProcessing = false;
        document.getElementById('btn-pay').disabled = false;
        document.getElementById('btn-pay').innerHTML = '<i class="fas fa-shopping-cart"></i> Bayar Sekarang';
        cart = [];
        renderCart();
    }

    // === RECEIPT ===
    function showReceipt(data) {
        document.getElementById('receipt-method').textContent = 'Metode: ' + paymentMethod;
        document.getElementById('receipt-total').textContent = formatRupiah(data.total);
        document.getElementById('receipt-id').textContent = 'ID: ' + data.transaction_id.substring(0, 8).toUpperCase();
        currentTransactionId = data.transaction_id;
        document.getElementById('receipt-overlay').classList.add('show');
    }

    function showOfflineReceipt(offlineId, total, items) {
        document.getElementById('receipt-method').textContent = 'Metode: ' + paymentMethod + ' (Offline)';
        document.getElementById('receipt-total').textContent = formatRupiah(total);
        document.getElementById('receipt-id').textContent = 'ID: ' + offlineId;
        currentTransactionId = offlineId; // Offline ID can't be printed via backend yet until synced
        
        const receiptItemsHtml = items.map(item => `
            <div class="receipt-item-row">
                <span>${item.name} x${item.qty}</span>
                <span>${formatRupiah(item.price * item.qty)}</span>
            </div>
        `).join('');
        document.getElementById('receipt-items').innerHTML = receiptItemsHtml;

        document.getElementById('receipt-overlay').classList.add('show');
    }

    function printReceipt() {
        if (!currentTransactionId) return;
        if (currentTransactionId.startsWith('OFFLINE-')) {
            alert('Tidak dapat mencetak struk offline. Harap tunggu hingga sinkronisasi selesai.');
            return;
        }
        const printUrl = `/transaksi/${currentTransactionId}/cetak`;
        window.open(printUrl, '_blank', 'width=400,height=600');
    }

    function closeReceipt() {
        document.getElementById('receipt-overlay').classList.remove('show');
        // Do not reload to maintain offline state, just reset
        resetCartUI();
    }

    // === INIT ===
    document.addEventListener('DOMContentLoaded', function() {
        renderCart();
        updateNetworkStatus(navigator.onLine);
        if (navigator.onLine && offlineQueue.length > 0) {
            syncOfflineQueue();
        }
    });
</script>
@endpush
