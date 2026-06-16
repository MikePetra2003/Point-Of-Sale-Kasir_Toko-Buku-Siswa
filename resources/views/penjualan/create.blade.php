@extends('layouts.pos')

@section('title', 'Kasir POS - Transaksi Baru')
@section('page-title', 'Penjualan') 

@section('styles')
<style>
    .pos-container { display: flex; gap: 22px; min-height: calc(100vh - 180px); }

    .product-panel {
        flex: 1;
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .product-panel .panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fafbfb 0%, #f8fafc 100%);
    }
    .product-panel .panel-header h5 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0 0 14px;
        color: #0f172a;
    }
    .product-search {
        position: relative;
    }
    .product-search input {
        width: 100%;
        padding: 11px 16px 11px 42px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .product-search input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        background: #fff;
    }
    .product-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .product-list {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
    }
    .product-list::-webkit-scrollbar { width: 5px; }
    .product-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .product-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 16px;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 8px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .product-item:hover {
        border-color: #3b82f6;
        background: rgba(239, 246, 255, 0.5);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.06);
    }
    .product-item .product-info { flex: 1; }
    .product-item .product-name { font-size: 0.875rem; font-weight: 600; color: #0f172a; }
    .product-item .product-code { font-size: 0.73rem; color: #94a3b8; }
    .product-item .product-price { font-size: 0.85rem; font-weight: 700; color: #3b82f6; margin-top: 3px; }
    .product-item .product-stock {
        font-size: 0.73rem;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 8px;
        min-width: 34px;
        text-align: center;
    }
    .stock-ok { background: rgba(16, 185, 129, 0.1); color: #065f46; }
    .stock-low { background: rgba(245, 158, 11, 0.1); color: #92400e; }
    .stock-empty { background: rgba(239, 68, 68, 0.1); color: #991b1b; }

    .btn-add-cart {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        background: #3b82f6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-left: 10px;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
    }
    .btn-add-cart:hover { background: #2563eb; transform: scale(1.08); }
    .btn-add-cart:disabled { background: #e2e8f0; cursor: not-allowed; transform: none; box-shadow: none; }

    .cart-panel {
        width: 430px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .cart-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .cart-card .cart-header {
        padding: 18px 22px;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cart-card .cart-header h5 { font-size: 0.95rem; font-weight: 700; margin: 0; }
    .cart-card .cart-header .cart-count {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 0.73rem;
        font-weight: 700;
    }

    .cart-items {
        max-height: 250px;
        overflow-y: auto;
        padding: 14px 18px;
    }
    .cart-items::-webkit-scrollbar { width: 4px; }
    .cart-items::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-item .item-info { flex: 1; }
    .cart-item .item-name { font-size: 0.82rem; font-weight: 600; color: #0f172a; }
    .cart-item .item-price { font-size: 0.73rem; color: #64748b; }
    .cart-item .item-qty {
        width: 54px;
        padding: 5px 8px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .cart-item .item-subtotal { font-size: 0.82rem; font-weight: 700; color: #0f172a; min-width: 80px; text-align: right; }
    .cart-item .btn-remove {
        width: 30px; height: 30px; border-radius: 8px;
        border: none; background: rgba(239, 68, 68, 0.08); color: #ef4444;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; cursor: pointer; transition: all 0.2s;
    }
    .cart-item .btn-remove:hover { background: rgba(239, 68, 68, 0.15); }

    .cart-empty {
        padding: 36px 16px;
        text-align: center;
        color: #94a3b8;
    }
    .cart-empty i { font-size: 2.2rem; margin-bottom: 10px; }

    .payment-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .payment-card h6 { font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-bottom: 16px; }

    .payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        font-size: 0.85rem;
    }
    .payment-row .label { color: #64748b; }
    .payment-row .value { font-weight: 700; color: #0f172a; }
    .payment-row.total { font-size: 1.15rem; padding: 14px 0; border-top: 2px solid #e2e8f0; margin-top: 8px; }
    .payment-row.total .value { color: #10b981; }

    .payment-input { margin-top: 14px; }
    .payment-input label { font-size: 0.76rem; font-weight: 600; color: #64748b; margin-bottom: 5px; display: block; text-transform: uppercase; letter-spacing: 0.04em; }
    .payment-input select,
    .payment-input input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.85rem;
        background: #f8fafc;
    }
    .payment-input select:focus,
    .payment-input input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        background: #fff;
    }
    .payment-input input[readonly] {
        background: #f1f5f9;
        color: #475569;
        cursor: not-allowed;
    }
    .kembalian-display {
        margin-top: 14px;
        padding: 12px 16px;
        border-radius: 10px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        text-align: center;
    }
    .kembalian-display .label { font-size: 0.73rem; color: #64748b; }
    .kembalian-display .amount { font-size: 1.25rem; font-weight: 800; color: #10b981; }

    .piutang-warning {
        margin-top: 14px;
        padding: 12px 16px;
        border-radius: 10px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        font-size: 0.8rem;
        color: #92400e;
    }
    .piutang-warning.piutang-info {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }
    .payment-helper {
        margin-top: 8px;
        font-size: 0.76rem;
        color: #64748b;
    }

    .btn-submit-pos {
        width: 100%;
        margin-top: 18px;
        padding: 15px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(16,185,129,0.2);
    }
    .btn-submit-pos:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16,185,129,0.35);
        color: #fff;
    }
    .btn-submit-pos:disabled { opacity: 0.5; cursor: not-allowed; }

    .alert-pos {
        border-radius: 14px;
        border: none;
        padding: 15px 22px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 22px;
    }

    @media (max-width: 991.98px) {
        .pos-container { flex-direction: column; }
        .cart-panel { width: 100%; }
    }
</style>
@endsection

@section('content')
@if (session('error'))
    <div class="alert alert-danger alert-pos alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-pos alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Periksa kembali input transaksi.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="pos-container">
    <!-- Product Panel -->
    <div class="product-panel">
        <div class="panel-header">
            <h5><i class="bi bi-box-seam me-2"></i>Pilih Barang</h5>
            <div class="product-search">
                <i class="bi bi-search"></i>
                <input type="text" id="searchBarang" placeholder="Cari kode atau nama barang...">
            </div>
        </div>
        <div class="product-list" id="listBarang">
            @foreach ($barangs as $barang)
            <div class="product-item barang-row"
                data-id="{{ $barang->id }}"
                data-kode="{{ $barang->kode_barang }}"
                data-nama="{{ $barang->nama_barang }}"
                data-harga="{{ $barang->harga_jual }}"
                data-stok="{{ $barang->stok }}">
                <div class="product-info">
                    <div class="product-name">{{ $barang->nama_barang }}</div>
                    <div class="product-code">{{ $barang->kode_barang }}</div>
                    <div class="product-price">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</div>
                </div>
                <span class="product-stock {{ $barang->stok <= 5 ? ($barang->stok <= 0 ? 'stock-empty' : 'stock-low') : 'stock-ok' }}"
                    id="stok-badge-{{ $barang->id }}">{{ $barang->stok }}</span>
                <button type="button" class="btn-add-cart"
                    data-id="{{ $barang->id }}"
                    data-nama="{{ $barang->nama_barang }}"
                    data-harga="{{ $barang->harga_jual }}"
                    data-stok="{{ $barang->stok }}"
                    {{ $barang->stok <= 0 ? 'disabled' : '' }}>
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Panel -->
    <div class="cart-panel">
        <form method="POST" action="{{ route('penjualan.store') }}" id="formPenjualan">
            @csrf

            <!-- Cart -->
            <div class="cart-card">
                <div class="cart-header">
                    <h5><i class="bi bi-cart3 me-2"></i>Keranjang</h5>
                    <span class="cart-count" id="cartCount">0 item</span>
                </div>
                <div class="cart-items" id="keranjang">
                    <div class="cart-empty" id="keranjangKosong">
                        <i class="bi bi-cart-x d-block"></i>
                        <span>Belum ada barang</span>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="payment-card">
                <h6><i class="bi bi-calculator me-2"></i>Pembayaran</h6>

                <div class="payment-input">
                    <label>Pelanggan</label>
                    <select name="pelanggan_id" id="pelangganSelect" class="form-select">
                        <option value="">Umum</option>
                        @foreach ($pelanggans as $plg)
                            <option value="{{ $plg->id }}">{{ $plg->nama_pelanggan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="payment-row" style="margin-top:14px;">
                    <span class="label">Subtotal</span>
                    <span class="value" id="displayTotalHarga">Rp 0</span>
                </div>

                <div class="payment-row total">
                    <span class="label">Total Akhir</span>
                    <span class="value" id="displayTotalAkhir">Rp 0</span>
                </div>

                <div class="payment-input">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="metodePembayaran">
                        <option value="tunai">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="kredit" id="opsiKredit">Kredit</option>
                    </select>
                    <div class="payment-helper" id="paymentHelper">
                        Kredit aktif untuk belanja minimal Rp {{ number_format($minimumTotalKredit, 0, ',', '.') }}. Jika pelanggan masih Umum, data pelanggan wajib diisi di Form Piutang.
                    </div>
                </div>

                <div class="payment-input" id="paymentAmountWrapper">
                    <label>Jumlah Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" id="inputJumlahBayar" value="0" min="0">
                </div>

                <div class="kembalian-display" id="kembalianWrapper">
                    <div class="label" id="paymentStatusLabel">Kembalian</div>
                    <div class="amount" id="displayKembalian">Rp 0</div>
                </div>

                <div class="piutang-warning d-none" id="paymentWarning">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <span id="paymentWarningText"></span>
                </div>

                <div class="piutang-warning piutang-info d-none" id="creditInfo">
                    <i class="bi bi-info-circle me-1"></i>
                    Transaksi kredit akan disimpan terlebih dulu lalu diarahkan ke form piutang baru untuk mengisi bayar awal dan jatuh tempo.
                </div>

                <button type="submit" class="btn-submit-pos" id="btnSimpan" disabled>
                    <i class="bi bi-check-circle me-2"></i>Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let keranjang = [];
let stokProduk = {};
const MINIMUM_TOTAL_KREDIT = {{ (int) $minimumTotalKredit }};

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function getTotalAkhir() {
    return keranjang.reduce((sum, item) => sum + item.subtotal, 0);
}

function getJumlahBayar() {
    return parseFloat(document.getElementById('inputJumlahBayar').value) || 0;
}

function setPaymentWarning(message) {
    const warning = document.getElementById('paymentWarning');
    const warningText = document.getElementById('paymentWarningText');

    if (!message) {
        warning.classList.add('d-none');
        warningText.textContent = '';
        return;
    }

    warningText.textContent = message;
    warning.classList.remove('d-none');
}

function canUseKredit(totalAkhir) {
    return totalAkhir >= MINIMUM_TOTAL_KREDIT;
}

function syncKreditOption(totalAkhir) {
    const metodeSelect = document.getElementById('metodePembayaran');
    const kreditOption = document.getElementById('opsiKredit');
    const eligible = canUseKredit(totalAkhir);
    const wasKredit = metodeSelect.value === 'kredit';

    kreditOption.disabled = !eligible;

    if (!eligible && wasKredit) {
        metodeSelect.value = 'tunai';
    }

    return {
        eligible,
        switchedFromKredit: wasKredit && !eligible,
    };
}

function setSubmitState(canSubmit) {
    document.getElementById('btnSimpan').disabled = !canSubmit;
}

function setPaymentStatus(label, value, tone = 'success') {
    const wrapper = document.getElementById('kembalianWrapper');
    const labelEl = document.getElementById('paymentStatusLabel');
    const amountEl = document.getElementById('displayKembalian');

    labelEl.textContent = label;
    amountEl.textContent = value;

    if (tone === 'warning') {
        wrapper.style.background = '#fffbeb';
        wrapper.style.borderColor = '#fde68a';
        amountEl.style.color = '#f59e0b';
        return;
    }

    wrapper.style.background = '#f0fdf4';
    wrapper.style.borderColor = '#bbf7d0';
    amountEl.style.color = '#10b981';
}

function refreshStokBadge(id) {
    const badge = document.getElementById('stok-badge-' + id);
    const tombol = document.querySelector('.btn-add-cart[data-id="' + id + '"]');
    if (!badge || !tombol) return;

    const stok = stokProduk[id] ?? 0;
    badge.textContent = stok;
    badge.className = 'product-stock ' + (stok <= 0 ? 'stock-empty' : (stok <= 5 ? 'stock-low' : 'stock-ok'));
    tombol.disabled = stok <= 0;
}

function tambahKeKeranjang(id, nama, harga) {
    const stokTersedia = stokProduk[id] ?? 0;
    if (stokTersedia <= 0) { alert('Stok ' + nama + ' sudah habis!'); return; }

    let existing = keranjang.find(item => item.id === id);
    if (existing) {
        existing.jumlah++;
        existing.subtotal = existing.jumlah * existing.harga;
    } else {
        keranjang.push({ id, nama, harga, jumlah: 1, subtotal: harga });
    }

    stokProduk[id] = Math.max(0, stokProduk[id] - 1);
    refreshStokBadge(id);
    renderKeranjang();
}

function ubahQty(id, newQty) {
    let item = keranjang.find(i => i.id === id);
    if (!item) return;
    newQty = parseInt(newQty);
    if (isNaN(newQty) || newQty < 1) { hapusDariKeranjang(id); return; }

    let selisih = newQty - item.jumlah;
    if (selisih > 0 && (stokProduk[id] ?? 0) < selisih) {
        alert('Stok tidak mencukupi!');
        newQty = item.jumlah + (stokProduk[id] ?? 0);
        selisih = newQty - item.jumlah;
    }

    item.jumlah = newQty;
    item.subtotal = item.jumlah * item.harga;

    if (selisih > 0) { stokProduk[id] = Math.max(0, stokProduk[id] - selisih); }
    else if (selisih < 0) {
        const row = document.querySelector('.barang-row[data-id="' + id + '"]');
        const stokAwal = row ? parseInt(row.dataset.stok) : 0;
        stokProduk[id] = Math.min(stokAwal, stokProduk[id] + Math.abs(selisih));
    }
    refreshStokBadge(id);
    renderKeranjang();
}

function hapusDariKeranjang(id) {
    let item = keranjang.find(i => i.id === id);
    if (item) {
        const row = document.querySelector('.barang-row[data-id="' + id + '"]');
        const stokAwal = row ? parseInt(row.dataset.stok) : 0;
        stokProduk[id] = Math.min(stokAwal, (stokProduk[id] ?? 0) + item.jumlah);
        refreshStokBadge(id);
    }
    keranjang = keranjang.filter(i => i.id !== id);
    renderKeranjang();
}

function renderKeranjang() {
    const container = document.getElementById('keranjang');
    const countEl = document.getElementById('cartCount');

    if (keranjang.length === 0) {
        container.innerHTML = '<div class="cart-empty" id="keranjangKosong"><i class="bi bi-cart-x d-block"></i><span>Belum ada barang</span></div>';
        countEl.textContent = '0 item';
    } else {
        let html = '';
        keranjang.forEach((item, idx) => {
            html += `
            <div class="cart-item">
                <div class="item-info">
                    <div class="item-name">${item.nama}</div>
                    <div class="item-price">@ ${formatRupiah(item.harga)}</div>
                    <input type="hidden" name="items[${idx}][barang_id]" value="${item.id}">
                </div>
                <input type="number" name="items[${idx}][jumlah]" value="${item.jumlah}" min="1"
                    class="item-qty" onchange="ubahQty(${item.id}, this.value)">
                <div class="item-subtotal">${formatRupiah(item.subtotal)}</div>
                <button type="button" class="btn-remove" onclick="hapusDariKeranjang(${item.id})">
                    <i class="bi bi-x"></i>
                </button>
            </div>`;
        });
        container.innerHTML = html;
        const totalItems = keranjang.reduce((s, i) => s + i.jumlah, 0);
        countEl.textContent = totalItems + ' item';
    }
    hitungTotal();
}

function hitungTotal() {
    let totalHarga = getTotalAkhir();
    let totalAkhir = totalHarga;
    let jumlahBayarInput = document.getElementById('inputJumlahBayar');
    let paymentAmountWrapper = document.getElementById('paymentAmountWrapper');
    let metodeSelect = document.getElementById('metodePembayaran');

    document.getElementById('displayTotalHarga').textContent = formatRupiah(totalHarga);
    document.getElementById('displayTotalAkhir').textContent = formatRupiah(totalAkhir);

    const { eligible, switchedFromKredit } = syncKreditOption(totalAkhir);
    const metode = metodeSelect.value;

    if (metode === 'qris') {
        paymentAmountWrapper.classList.remove('d-none');
        jumlahBayarInput.value = totalAkhir;
        jumlahBayarInput.disabled = false;
        jumlahBayarInput.readOnly = true;
    } else if (metode === 'kredit') {
        paymentAmountWrapper.classList.add('d-none');
        jumlahBayarInput.value = 0;
        jumlahBayarInput.disabled = true;
        jumlahBayarInput.readOnly = true;
    } else {
        paymentAmountWrapper.classList.remove('d-none');
        jumlahBayarInput.disabled = false;
        jumlahBayarInput.readOnly = false;
    }

    hitungKembalian(eligible, switchedFromKredit);
}

function hitungKembalian(eligibleKredit = canUseKredit(getTotalAkhir()), switchedFromKredit = false) {
    let totalAkhir = getTotalAkhir();
    let jumlahBayar = getJumlahBayar();
    let metode = document.getElementById('metodePembayaran').value;
    let creditInfo = document.getElementById('creditInfo');
    let wrapper = document.getElementById('kembalianWrapper');

    creditInfo.classList.add('d-none');

    if (keranjang.length === 0) {
        wrapper.classList.remove('d-none');
        setPaymentStatus('Status Pembayaran', formatRupiah(0));
        setPaymentWarning('');
        setSubmitState(false);
        return;
    }

    if (metode === 'qris') {
        wrapper.classList.add('d-none');
        setPaymentWarning('');
        setSubmitState(true);
        return;
    }

    if (metode === 'kredit') {
        wrapper.classList.add('d-none');
        if (!eligibleKredit) {
            setPaymentWarning('Metode kredit hanya tersedia untuk total belanja minimal ' + formatRupiah(MINIMUM_TOTAL_KREDIT) + '.');
            setSubmitState(false);
            return;
        }

        setPaymentWarning('');
        creditInfo.classList.remove('d-none');
        setSubmitState(true);
        return;
    }

    wrapper.classList.remove('d-none');
    let selisih = jumlahBayar - totalAkhir;
    if (selisih < 0) {
        setPaymentStatus('Kekurangan', formatRupiah(Math.abs(selisih)), 'warning');
        setPaymentWarning(
            switchedFromKredit
                ? 'Metode kredit dinonaktifkan karena total belanja belum mencapai ' + formatRupiah(MINIMUM_TOTAL_KREDIT) + '.'
                : 'Pembayaran tunai harus minimal sama dengan total belanja.'
        );
        setSubmitState(false);
    } else if (selisih === 0) {
        setPaymentStatus('Status Pembayaran', 'Lunas');
        setPaymentWarning('');
        setSubmitState(true);
    } else {
        setPaymentStatus('Kembalian', formatRupiah(selisih));
        setPaymentWarning('');
        setSubmitState(true);
    }
}

// Init stok
document.querySelectorAll('.barang-row').forEach(row => {
    stokProduk[parseInt(row.dataset.id)] = parseInt(row.dataset.stok) || 0;
});

// Add to cart click
document.getElementById('listBarang').addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-add-cart');
    if (!btn || btn.disabled) return;
    e.preventDefault();
    tambahKeKeranjang(parseInt(btn.dataset.id), btn.dataset.nama, parseFloat(btn.dataset.harga));
});

// Search
document.getElementById('searchBarang').addEventListener('input', function() {
    const kw = this.value.toLowerCase();
    document.querySelectorAll('.barang-row').forEach(row => {
        const match = row.dataset.kode.toLowerCase().includes(kw) || row.dataset.nama.toLowerCase().includes(kw);
        row.style.display = match ? '' : 'none';
    });
});

// Bayar events
document.getElementById('inputJumlahBayar').addEventListener('input', hitungKembalian);
document.getElementById('metodePembayaran').addEventListener('change', function() {
    const isLocked = this.value === 'qris' || this.value === 'kredit';
    const jumlahBayarInput = document.getElementById('inputJumlahBayar');
    if (!isLocked) {
        jumlahBayarInput.value = 0;
    }
    hitungTotal();
});
document.getElementById('pelangganSelect').addEventListener('change', hitungTotal);

// Submit validation
document.getElementById('formPenjualan').addEventListener('submit', function(e) {
    if (keranjang.length === 0) { e.preventDefault(); alert('Keranjang masih kosong!'); return; }
    let totalAkhir = getTotalAkhir();
    let jumlahBayar = getJumlahBayar();
    let metode = document.getElementById('metodePembayaran').value;

    if (metode === 'qris') { document.getElementById('inputJumlahBayar').value = totalAkhir; return; }
    if (metode === 'kredit') {
        if (!canUseKredit(totalAkhir)) {
            e.preventDefault();
            setPaymentWarning('Metode kredit hanya tersedia untuk total belanja minimal ' + formatRupiah(MINIMUM_TOTAL_KREDIT) + '.');
            return;
        }
        return;
    }
    if (jumlahBayar <= 0) {
        e.preventDefault();
        setPaymentWarning('Pembayaran tunai harus minimal sama dengan total belanja.');
        return;
    }
    if (jumlahBayar < totalAkhir) {
        e.preventDefault();
        setPaymentWarning('Pembayaran tunai harus minimal sama dengan total belanja.');
    }
});

hitungTotal();
</script>
@endsection
