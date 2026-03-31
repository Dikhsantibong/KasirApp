@extends('layouts.app')

@section('title', 'Kasir')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
    <style>
        /* Small overrides for icons & custom spacing */
        .sidebar { overflow-y: auto; scrollbar-width: none; }
        .sidebar::-webkit-scrollbar { display: none; }
        .nav-link i { margin-right: 8px; }
    </style>
@endpush

@section('content')
<div class="kasir-layout">
   <x-sidebar />

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="global-search" placeholder="Cari Produk atau Scan Barcode...">
            </div>

            <div class="topbar-actions">
                <div class="status-sync">
                    <i class="fas fa-circle"></i>
                    <span>Sinkronisasi Berhasil</span>
                </div>
                <div style="font-size: 1.25rem; color: #5e6c84; position: relative; cursor: pointer;">
                    <i class="far fa-bell"></i>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin+Kasir&background=0D8ABC&color=fff" class="user-thumb" alt="User">
            </div>
        </header>

        <!-- Kasir Split Layout -->
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
                <!-- Hardcoded display wrapper if DB is empty, otherwise looping dynamic data -->
                <div class="products-grid">
                    @forelse($products as $product)
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <!-- using a generic placeholder if image missing, random images for aesthetic UI -->
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=random&color=fff&size=200" alt="{{ $product->name }}">
                            </div>
                            <div class="product-info">
                                <span class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                <span class="product-name">{{ $product->name }}</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                    <button class="btn-add-product" onclick="addToCart('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->selling_price }}, 'https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=random&color=fff&size=50')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Dummy Items for aesthetic showcase matching image -->
                        <div class="product-card">
                            <div class="product-img-wrapper" style="background:#111;">
                                <img src="https://images.unsplash.com/photo-1559525839-b184a4d698c7?ixlib=rb-1.2.1&auto=format&fit=crop&w=256&q=80" alt="Kopi Hitam Arabika">
                            </div>
                            <div class="product-info">
                                <span class="product-category">MINUMAN</span>
                                <span class="product-name">Kopi Hitam Arabika</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp 25.000</span>
                                    <button class="btn-add-product" onclick="addToCart('d1', 'Kopi Hitam Arabika', 25000, 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80')"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-img-wrapper" style="background:#111;">
                                <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=256&q=80" alt="Roti Gandum Premium">
                            </div>
                            <div class="product-info">
                                <span class="product-category">MAKANAN</span>
                                <span class="product-name">Roti Gandum Premium</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp 18.500</span>
                                    <button class="btn-add-product" onclick="addToCart('d2', 'Roti Gandum Premium', 18500, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80')"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-img-wrapper" style="background:#111;">
                                <img src="https://images.unsplash.com/photo-1622483767028-3f66f32aef97?ixlib=rb-1.2.1&auto=format&fit=crop&w=256&q=80" alt="Jus Jeruk Segar">
                            </div>
                            <div class="product-info">
                                <span class="product-category">MINUMAN</span>
                                <span class="product-name">Jus Jeruk Segar</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp 15.000</span>
                                    <button class="btn-add-product" onclick="addToCart('d3', 'Jus Jeruk Segar', 15000, 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80')"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-img-wrapper" style="background:#111;">
                                <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?ixlib=rb-1.2.1&auto=format&fit=crop&w=256&q=80" alt="Nasi Goreng Spesial">
                            </div>
                            <div class="product-info">
                                <span class="product-category">MAKANAN</span>
                                <span class="product-name">Nasi Goreng Spesial</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp 32.000</span>
                                    <button class="btn-add-product" onclick="addToCart('d4', 'Nasi Goreng Spesial', 32000, 'https://images.unsplash.com/photo-1512058564366-18510be2db19?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80')"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-img-wrapper" style="background:#111;">
                                <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=256&q=80" alt="Burger Ayam">
                            </div>
                            <div class="product-info">
                                <span class="product-category">MAKANAN</span>
                                <span class="product-name">Burger Ayam Pedas</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp 28.000</span>
                                    <button class="btn-add-product" onclick="addToCart('d5', 'Burger Ayam Pedas', 28000, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80')"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-card">
                            <div class="product-img-wrapper" style="background:#111;">
                                <img src="https://images.unsplash.com/photo-1599599810769-bcde5a160d32?ixlib=rb-1.2.1&auto=format&fit=crop&w=256&q=80" alt="Kacang Mede">
                            </div>
                            <div class="product-info">
                                <span class="product-category">SNACK</span>
                                <span class="product-name">Kacang Oven 100gr</span>
                                <div class="product-price-row">
                                    <span class="product-price">Rp 12.000</span>
                                    <button class="btn-add-product" onclick="addToCart('d6', 'Kacang Oven 100gr', 12000, 'https://images.unsplash.com/photo-1599599810769-bcde5a160d32?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80')"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- CART SECTION -->
            <div class="cart-section">
                <!-- Header -->
                <div class="cart-header">
                    <h2>Keranjang Belanja</h2>
                    <button class="btn-clear-cart" onclick="clearCart()" title="Kosongkan Keranjang">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <!-- Items -->
                <div class="cart-items" id="cart-container">
                    <!-- Dynamic Cart Items injected by JS -->
                </div>

                <!-- Summary & Payment -->
                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="summary-subtotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Diskon</span>
                        <a href="#" class="btn-add-discount"><i class="fas fa-plus-circle"></i> Tambah Diskon</a>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span class="total-value" id="summary-total">Rp 0</span>
                    </div>

                    <div class="payment-methods">
                        <div class="payment-methods-title">METODE PEMBAYARAN</div>
                        <div class="payment-grid">
                            <div class="payment-btn active" onclick="setPaymentMethod('tunai', this)">
                                <i class="fas fa-money-bill-wave text-blue-600"></i>
                                <span>Tunai</span>
                            </div>
                            <div class="payment-btn" onclick="setPaymentMethod('qris', this)">
                                <i class="fas fa-qrcode text-gray-500"></i>
                                <span>QRIS</span>
                            </div>
                            <div class="payment-btn" onclick="setPaymentMethod('transfer', this)">
                                <i class="fas fa-university text-gray-500"></i>
                                <span>Transfer</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn-pay-now" onclick="processPayment()">
                        <i class="fas fa-shopping-cart"></i>
                        Bayar Sekarang
                    </button>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    // Cart Data Structure
    let cart = [];
    let paymentMethod = 'tunai';

    function addToCart(id, name, price, img) {
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({ id, name, price, img, qty: 1 });
        }
        
        renderCart();
    }

    function updateQty(id, change) {
        const itemIndex = cart.findIndex(item => item.id === id);
        if (itemIndex > -1) {
            cart[itemIndex].qty += change;
            if (cart[itemIndex].qty <= 0) {
                cart.splice(itemIndex, 1);
            }
            renderCart();
        }
    }

    function clearCart() {
        if(confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            cart = [];
            renderCart();
        }
    }

    function setPaymentMethod(method, element) {
        paymentMethod = method;
        // removing active class from all
        document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('active'));
        // adding active class to clicked
        element.classList.add('active');
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function renderCart() {
        const container = document.getElementById('cart-container');
        let html = '';
        let subtotal = 0;

        if (cart.length === 0) {
            html = '<div style="text-align:center; color:#94a3b8; margin-top: 2rem;"><i class="fas fa-shopping-basket fa-3x" style="opacity:0.2; margin-bottom:1rem; display:block;"></i> Keranjang masih kosong</div>';
        } else {
            cart.forEach(item => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                html += `
                    <div class="cart-item">
                        <img src="${item.img}" class="cart-item-img" alt="${item.name}">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="qty-control">
                                <button class="btn-qty" onclick="updateQty('${item.id}', -1)">-</button>
                                <input type="text" class="qty-input" value="${item.qty}" readonly>
                                <button class="btn-qty" onclick="updateQty('${item.id}', 1)">+</button>
                            </div>
                        </div>
                        <div class="cart-item-price">Rp ${formatNumber(itemTotal)}</div>
                    </div>
                `;
            });
        }

        container.innerHTML = html;
        document.getElementById('summary-subtotal').textContent = `Rp ${formatNumber(subtotal)}`;
        document.getElementById('summary-total').textContent = `Rp ${formatNumber(subtotal)}`; // Assuming no dynamic discount logic yet
    }

    function processPayment() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        
        // Mocking payment success
        alert('Pembayaran ' + paymentMethod.toUpperCase() + ' Berhasil diproses!');
        cart = []; // clear after success
        renderCart();
    }

    // Default Initialization Render (Dummy items like the image)
    document.addEventListener('DOMContentLoaded', function() {
        // Pre-fill some Dummy Data to match User's Image requirements
        @if(count($products) == 0)
            cart = [
                { id: 'd1', name: 'Kopi Hitam Arabika', price: 50000/2, qty: 2, img: 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80' },
                { id: 'd2', name: 'Roti Gandum Premium', price: 18500, qty: 1, img: 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=64&q=80' }
            ];
            renderCart();
        @endif
        
        // Implement simple search interaction simulation
        document.getElementById('global-search').addEventListener('keyup', function(e) {
            if(e.key === 'Enter') {
                window.location.href = `{{ route('kasir.index') }}?search=${this.value}`;
            }
        });
    });
</script>
@endpush
