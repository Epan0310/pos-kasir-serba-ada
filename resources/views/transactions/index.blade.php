@extends('layouts.app')

@section('title', 'Kasir POS - SerbaAda Mart')

@push('styles')
<style>
    /* Split Screen Layout & Arasta Style */
    .pos-wrapper {
        display: flex;
        height: calc(100vh - 58px);
        overflow: hidden;
    }
    .left-cart {
        width: 360px;
        background: var(--arasta-surface);
        border-end: 1px solid var(--arasta-border);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .right-products {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }
    
    /* Grid Produk Compact (Muat Lebih Banyak Per Baris) */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }

    /* Shopee / Retail Compact Card Style */
    .product-card {
        background: var(--arasta-surface);
        border: 1px solid var(--arasta-border);
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        border-color: var(--arasta-accent);
    }
    .product-image-box {
        width: 100%;
        aspect-ratio: 1 / 1;
        background-color: #FFFFFF;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-image-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
        transition: transform 0.2s ease;
    }
    .product-card:hover .product-image-box img {
        transform: scale(1.06);
    }
</style>
@endpush

@section('content')
<div class="pos-wrapper">
    <!-- PANEL KIRI: Keranjang Pesanan -->
    <div class="left-cart">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-cart3 me-2"></i>Pesanan Aktif</h6>
            <button class="btn btn-sm btn-outline-danger py-0 px-2 fs-7" onclick="clearCart()">Clear All</button>
        </div>

        <!-- Alert Notifikasi Flash (Tombol X Berfungsi) -->
        @if(session('success'))
            <div class="px-3 pt-2">
                <div class="alert alert-success alert-dismissible fade show mb-0 p-3 fs-7 border-0 shadow-sm position-relative" role="alert">
                    <div class="pe-4">
                        <div class="fw-bold mb-2">{{ session('success') }}</div>
                        @if(session('print_id'))
                            <a href="{{ route('transactions.print', session('print_id')) }}" target="_blank" class="btn btn-sm btn-dark w-100">
                                <i class="bi bi-printer me-1"></i> Cetak Struk Penjualan
                            </a>
                        @endif
                    </div>
                    <button type="button" class="btn-close p-3 position-absolute top-0 end-0" data-bs-dismiss="alert" aria-label="Close" onclick="this.closest('.alert').remove()"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-3 pt-2">
                <div class="alert alert-danger alert-dismissible fade show mb-0 p-3 fs-7 border-0 shadow-sm position-relative" role="alert">
                    <div class="fw-bold pe-4">{{ session('error') }}</div>
                    <button type="button" class="btn-close p-3 position-absolute top-0 end-0" data-bs-dismiss="alert" aria-label="Close" onclick="this.closest('.alert').remove()"></button>
                </div>
            </div>
        @endif

        <!-- List Item Belanja -->
        <div class="flex-grow-1 overflow-y-auto p-3" id="cartContainer">
            <div class="text-center text-muted py-5" id="emptyCartNotice">
                <i class="bi bi-box-seam display-4 opacity-50 d-block mb-2"></i>
                <small>Keranjang masih kosong.<br>Klik produk di kanan untuk memilih.</small>
            </div>
            <div id="cartItemsList"></div>
        </div>

        <!-- Form Bayar -->
        <form action="{{ route('transactions.store') }}" method="POST" id="checkoutForm">
            @csrf
            <div id="hiddenCartInputs"></div>
            
            <div class="p-3 bg-light border-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Total Tagihan</span>
                    <span class="fw-bold fs-5" id="displayTotal">Rp 0</span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">Uang Dibayar (Rp)</label>
                    <input type="number" name="pay_amount" id="payAmountInput" class="form-control form-control-lg fw-bold" placeholder="0" required oninput="calculateChange()">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Kembalian</span>
                    <span class="fw-bold fs-6 text-success" id="displayChange">Rp 0</span>
                </div>

                <button type="submit" class="btn btn-lg w-100 fw-bold text-white shadow-sm" style="background-color: var(--arasta-accent);" id="btnPay" disabled>
                    <i class="bi bi-wallet2 me-2"></i> Proses Bayar
                </button>
            </div>
        </form>
    </div>

    <!-- PANEL KANAN: Katalog Produk Compact -->
    <div class="right-products">
        <!-- Search & Filter -->
        <div class="row g-2 mb-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari barang atau barcode..." onkeyup="filterProducts()">
                </div>
            </div>
            <div class="col-md-7 d-flex gap-2 align-items-center overflow-x-auto">
                <button class="btn btn-dark btn-sm rounded-pill px-3 filter-cat-btn active" onclick="filterCategory('all', this)">Semua Barang</button>
                @foreach($categories as $cat)
                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-cat-btn" onclick="filterCategory({{ $cat->id }}, this)">{{ $cat->name }}</button>
                @endforeach
            </div>
        </div>

        <!-- Grid Cards Ringkas 150px -->
        <div class="products-grid" id="productsGrid">
            @foreach($products as $prod)
                <div class="product-item-wrapper" data-category="{{ $prod->category_id }}" data-name="{{ strtolower($prod->name) }}" data-code="{{ strtolower($prod->code) }}">
                    <div class="product-card" onclick="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->sell_price }}, {{ $prod->stock }})">
                        
                        <div class="product-image-box border-bottom">
                            @if($prod->image)
                                <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}">
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center text-muted opacity-50">
                                    <i class="bi bi-image fs-3 mb-1"></i>
                                    <small style="font-size: 10px;">Belum ada foto</small>
                                </div>
                            @endif
                            <span class="badge bg-dark bg-opacity-75 position-absolute top-0 start-0 m-1 rounded-pill px-2 py-1" style="font-size: 9px;">{{ $prod->code }}</span>
                        </div>

                        <div class="p-2 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.825rem;" title="{{ $prod->name }}">{{ $prod->name }}</h6>
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">Stok: <strong>{{ $prod->stock }}</strong></small>
                            </div>
                            
                            <div class="pt-1 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold" style="color: var(--arasta-accent) !important; font-size: 0.85rem;">
                                    Rp {{ number_format($prod->sell_price, 0, ',', '.') }}
                                </span>
                                <span class="btn btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; background-color: #FEF3C7; color: var(--arasta-accent); font-size: 10px;">
                                    <i class="bi bi-plus-lg"></i>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];

    function addToCart(id, name, price, maxStock) {
        let item = cart.find(i => i.id === id);
        if (item) {
            if (item.qty < maxStock) {
                item.qty++;
            } else {
                alert('Stok barang tidak mencukupi!');
            }
        } else {
            cart.push({ id: id, name: name, price: price, qty: 1, maxStock: maxStock });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            } else if (item.qty > item.maxStock) {
                item.qty = item.maxStock;
                alert('Mencapai batas stok tersedia!');
            }
        }
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const listEl = document.getElementById('cartItemsList');
        const emptyEl = document.getElementById('emptyCartNotice');
        const inputsEl = document.getElementById('hiddenCartInputs');
        const btnPay = document.getElementById('btnPay');
        
        listEl.innerHTML = '';
        inputsEl.innerHTML = '';

        if (cart.length === 0) {
            emptyEl.style.display = 'block';
            document.getElementById('displayTotal').innerText = 'Rp 0';
            btnPay.disabled = true;
            calculateChange();
            return;
        }

        emptyEl.style.display = 'none';
        let total = 0;

        cart.forEach((item, index) => {
            let subtotal = item.price * item.qty;
            total += subtotal;

            listEl.innerHTML += `
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <div class="fw-bold small">${item.name}</div>
                        <div class="text-muted" style="font-size: 11px;">Rp ${item.price.toLocaleString('id-ID')} x ${item.qty}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="fw-bold small">${item.qty}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQty(${item.id}, 1)">+</button>
                    </div>
                </div>
            `;

            inputsEl.innerHTML += `
                <input type="hidden" name="items[${index}][id]" value="${item.id}">
                <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
            `;
        });

        document.getElementById('displayTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
        btnPay.disabled = false;
        calculateChange();
    }

    function calculateChange() {
        let total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        let pay = parseFloat(document.getElementById('payAmountInput').value) || 0;
        let change = pay - total;
        let displayEl = document.getElementById('displayChange');

        if (change >= 0 && total > 0) {
            displayEl.innerText = 'Rp ' + change.toLocaleString('id-ID');
            displayEl.className = 'fw-bold fs-6 text-success';
        } else {
            displayEl.innerText = 'Uang Kurang';
            displayEl.className = 'fw-bold fs-6 text-danger';
        }
    }

    function filterProducts() {
        let keyword = document.getElementById('searchInput').value.toLowerCase();
        let items = document.querySelectorAll('.product-item-wrapper');
        items.forEach(item => {
            let name = item.getAttribute('data-name');
            let code = item.getAttribute('data-code');
            if (name.includes(keyword) || code.includes(keyword)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterCategory(catId, btn) {
        document.querySelectorAll('.filter-cat-btn').forEach(b => b.classList.remove('active', 'btn-dark'));
        document.querySelectorAll('.filter-cat-btn').forEach(b => b.classList.add('btn-outline-secondary'));
        btn.classList.add('active', 'btn-dark');
        btn.classList.remove('btn-outline-secondary');

        let items = document.querySelectorAll('.product-item-wrapper');
        items.forEach(item => {
            if (catId === 'all' || item.getAttribute('data-category') == catId) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endpush