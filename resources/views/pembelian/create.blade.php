@extends('layouts.pos')

@section('title', 'Pembelian Barang - POS Toko Buku')
@section('page-title', 'Pembelian Barang')

@section('styles')
<style>
    .custom-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        height: 100%;
    }
    .card-header-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
        border-top-left-radius: 17px !important;
        border-top-right-radius: 17px !important;
        padding: 18px 22px;
        border: none;
    }
    .card-header-green {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        border-top-left-radius: 17px !important;
        border-top-right-radius: 17px !important;
        padding: 18px 22px;
        border: none;
    }

    .custom-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.875rem;
        color: #334155;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .custom-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
        background: #fff;
    }
    .custom-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 11px 15px;
        font-size: 0.875rem;
        color: #334155;
        font-weight: 500;
        background: #f8fafc;
    }
    .custom-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        background: #fff;
    }

    .compact-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.73rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 11px 15px;
        border-bottom: 1px solid #e2e8f0;
    }
    .compact-table td {
        padding: 11px 15px;
        font-size: 0.84rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .barang-row {
        transition: background-color 0.15s ease;
    }
    .barang-row:hover {
        background-color: #f8fafc;
    }

    .btn-add-item {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
        transition: all 0.2s;
    }
    .btn-add-item:hover {
        background-color: #10b981;
        color: #ffffff;
        border-color: #10b981;
        transform: scale(1.1);
    }

    .btn-del-item {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 1px solid rgba(239, 68, 68, 0.2);
        background-color: rgba(239, 68, 68, 0.06);
        color: #ef4444;
        transition: all 0.2s;
    }
    .btn-del-item:hover {
        background-color: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    .cart-qty-input {
        width: 64px !important;
        padding: 5px 8px;
        text-align: center;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 0.84rem;
    }
    .cart-fixed-price {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .total-display-box {
        background: rgba(16, 185, 129, 0.06);
        border-radius: 14px;
        padding: 18px 22px;
        border: 1px dashed rgba(16, 185, 129, 0.3);
    }
    .total-display-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #059669;
    }

    .btn-save-purchase {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: #ffffff;
        font-weight: 700;
        border-radius: 14px;
        padding: 15px;
        font-size: 1.05rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.2s;
    }
    .btn-save-purchase:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        background: linear-gradient(135deg, #059669, #047857);
    }
    .btn-save-purchase:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
    }

    .scrollable-card-body {
        max-height: 480px;
        overflow-y: auto;
    }
    .purchase-summary-panel {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        margin: 0 -16px -16px;
        padding: 16px;
    }
    .purchase-submit-bar {
        position: sticky;
        bottom: 0;
        z-index: 5;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
        margin-top: 12px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0 pb-5">
    <!-- Header/Title Block -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-cart-plus text-success me-2"></i>Pembelian Barang</h3>
                            <p class="text-muted small mb-0">Tambah pasokan dari supplier dalam satuan pcs.</p>
        </div>
        <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold px-3" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
    </div>

    <!-- Success/Error alert box -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert" style="background-color: rgba(239, 68, 68, 0.12); color: #991b1b;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- KOLOM KIRI: Daftar Barang --}}
        <div class="col-lg-7">
            <div class="custom-card overflow-hidden">
                <div class="card-header-blue d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam fs-5"></i>
                    <h5 class="mb-0 fw-bold">Pilih Barang untuk Dibeli</h5>
                </div>
                <div class="card-body">
                    <!-- Search Input -->
                    <div class="position-relative mb-3">
                        <i class="bi bi-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" id="searchBarang" class="form-control custom-input ps-5" placeholder="Cari kode atau nama barang...">
                    </div>
                    <!-- Product table scroll box -->
                    <div class="table-responsive scrollable-card-body border rounded-3">
                        <table class="table compact-table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Harga Beli</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center" width="80">Pilih</th>
                                </tr>
                            </thead>
                            <tbody id="listBarang">
                                <tr id="barangStatus">
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Pilih supplier terlebih dahulu untuk melihat barang.
                                    </td>
                                </tr>
                                @foreach ($barangs as $barang)
                                <tr class="barang-row d-none"
                                    data-kode="{{ $barang->kode_barang }}"
                                    data-nama="{{ $barang->nama_barang }}"
                                    data-supplier-id="{{ $barang->supplier_id }}">
                                    <td class="text-primary fw-semibold"><small>{{ $barang->kode_barang }}</small></td>
                                    <td class="fw-semibold text-dark">{{ $barang->nama_barang }}</td>
                                    <td class="fw-semibold">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                                    <td class="text-center"><span class="badge bg-light text-secondary border fw-medium px-2 py-1">{{ $barang->stok }} pcs</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-add-item"
                                            data-barang-id="{{ $barang->id }}"
                                            data-barang-nama="{{ $barang->nama_barang }}"
                                            data-harga-beli="{{ $barang->harga_beli }}">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Keranjang Pembelian --}}
        <div class="col-lg-5">
            <form method="POST" action="{{ route('pembelian.store') }}" id="formPembelian" class="h-100">
                @csrf

                <div class="custom-card overflow-hidden">
                    <div class="card-header-green d-flex align-items-center gap-2">
                        <i class="bi bi-cart-check fs-5"></i>
                        <h5 class="mb-0 fw-bold">Keranjang Pembelian</h5>
                    </div>
                    <div class="card-body d-flex flex-column h-100 justify-content-between">
                        <div>
                            {{-- Supplier Selection --}}
                            <div class="mb-3">
                                <label class="form-label text-dark small fw-bold mb-1">Pilih Supplier *</label>
                                <select name="supplier_id" id="supplierSelect" class="form-select custom-select" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Cart Items --}}
                            <label class="form-label text-dark small fw-bold mb-2">Item Pembelian</label>
                            <div class="table-responsive border rounded-3 scrollable-card-body mb-3" style="max-height: 240px;">
                                <table class="table compact-table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Detail Barang</th>
                                            <th class="text-center" width="90">Pcs</th>
                                            <th class="text-end">Subtotal</th>
                                            <th class="text-center" width="40"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="keranjang">
                                        <tr id="keranjangKosong">
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-cart-x fs-3 d-block mb-1 text-muted"></i>
                                                Belum ada barang dipilih.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="purchase-summary-panel">
                            <!-- Divider -->
                            <!-- Total displays -->
                            <div class="total-display-box d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-dark fs-5">Total Pembelian:</span>
                                <span class="total-display-value" id="displayTotal">Rp 0</span>
                            </div>

                            {{-- Diskon --}}
                            <div class="mb-3">
                                <label class="form-label text-dark small fw-bold mb-1">Diskon (%)</label>
                                <input type="number" name="diskon_persen" id="inputDiskon" class="form-control custom-input"
                                    min="0" max="100" step="0.01" value="0" oninput="updateNetTotal()" placeholder="0">
                                <div class="form-text small text-danger d-none" id="diskonWarning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Diskon tidak diproses karena melebihi batas 100%.
                                </div>
                            </div>

                            {{-- Nominal diskon --}}
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <span class="fw-semibold text-dark">Nominal Diskon:</span>
                                <span class="fw-bold text-danger" id="displayDiskonNominal">Rp 0</span>
                            </div>

                            {{-- Total setelah diskon --}}
                            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                                <span class="fw-semibold text-dark">Total Setelah Diskon:</span>
                                <span class="fw-bold text-success fs-5" id="displayNet">Rp 0</span>
                            </div>

                            {{-- Pembayaran awal --}}
                            <div class="mb-3">
                                <label class="form-label text-dark small fw-bold mb-1">Jumlah Dibayar Sekarang</label>
                                <input type="number" name="jumlah_bayar_awal" id="inputBayarAwal" class="form-control custom-input"
                                    min="0" step="1" value="0" oninput="updateNetTotal()" placeholder="0">
                                <div class="form-text small">
                                    <i class="bi bi-info-circle me-1"></i>Jika dibayar sebagian, sisanya otomatis masuk ke Hutang Supplier.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark small fw-bold mb-1">Metode Pembayaran</label>
                                <select name="metode_pembayaran_awal" class="form-select custom-select">
                                    <option value="tunai">Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <span class="fw-semibold text-dark">Dibayar Sekarang:</span>
                                <span class="fw-bold text-primary" id="displayBayarAwal">Rp 0</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                                <span class="fw-semibold text-dark">Sisa Hutang:</span>
                                <span class="fw-bold text-warning" id="displaySisaHutang">Rp 0</span>
                            </div>

                            {{-- Status Pembayaran ke Supplier --}}
                            <div class="mb-3">
                                <label class="form-label text-dark small fw-bold mb-1">Status Pembayaran *</label>
                                <select name="status_pembayaran" id="inputStatusPembayaran" class="form-select custom-select" required onchange="handleStatusPembayaranChange()">
                                    <option value="lunas">Lunas (Bayar Sekarang)</option>
                                    <option value="belum_lunas">Belum Lunas (Hutang)</option>
                                </select>
                                <div class="form-text small">
                                    <i class="bi bi-info-circle me-1"></i>Jika "Belum Lunas", otomatis tercatat sebagai hutang dengan jatuh tempo 1 bulan. Bunga 5% berlaku bila melewati jatuh tempo.
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div class="mb-5 pb-3">
                                <label class="form-label text-dark small fw-bold mb-1">Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control custom-input" rows="2" placeholder="Catatan tambahan pasokan..."></textarea>
                            </div>

                            <div class="purchase-submit-bar">
                                <button type="submit" class="btn btn-save-purchase w-100" id="btnSimpan" disabled>
                                    <i class="bi bi-check-circle-fill me-1"></i> Simpan Transaksi Pembelian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let keranjang = [];

    function formatRupiah(angka) {
        return 'Rp ' + Math.round(Number(angka)).toLocaleString('id-ID');
    }

    function tambahKeKeranjang(id, nama, hargaBeli) {
        if (!document.getElementById('supplierSelect').value) {
            alert('Pilih supplier terlebih dahulu.');
            return;
        }

        let existing = keranjang.find(item => item.id === id);
        if (existing) {
            existing.jumlah++;
            existing.subtotal = existing.jumlah * existing.harga;
        } else {
            keranjang.push({
                id: id,
                nama: nama,
                harga: hargaBeli,
                jumlah: 1,
                subtotal: hargaBeli
            });
        }
        renderKeranjang();
    }

    function ubahJumlah(id, newQty) {
        let item = keranjang.find(i => i.id === id);
        if (!item) return;
        newQty = parseInt(newQty);
        if (isNaN(newQty) || newQty < 1) { hapus(id); return; }
        item.jumlah = newQty;
        item.subtotal = item.jumlah * item.harga;
        renderKeranjang();
    }

    function hapus(id) {
        keranjang = keranjang.filter(item => item.id !== id);
        renderKeranjang();
    }

    function kosongkanKeranjang() {
        keranjang = [];
        renderKeranjang();
    }

    function renderKeranjang() {
        let tbody = document.getElementById('keranjang');
        tbody.innerHTML = '';

        if (keranjang.length === 0) {
            tbody.innerHTML = '<tr id="keranjangKosong"><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-cart-x fs-3 d-block mb-1 text-muted"></i>Belum ada barang dipilih.</td></tr>';
            document.getElementById('btnSimpan').disabled = true;
        } else {
            keranjang.forEach(function(item, index) {
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark small">${item.nama}</div>
                            <div class="cart-fixed-price mt-1">
                                @ ${formatRupiah(item.harga)} / pcs
                            </div>
                            <div class="text-muted small mt-1">${item.jumlah} pcs</div>
                            <input type="hidden" name="items[${index}][barang_id]" value="${item.id}">
                        </td>
                        <td>
                            <input type="number" name="items[${index}][jumlah]" value="${item.jumlah}"
                                min="1" class="form-control cart-qty-input mx-auto"
                                onchange="ubahJumlah(${item.id}, this.value)">
                        </td>
                        <td class="text-end fw-semibold text-dark"><small>${formatRupiah(item.subtotal)}</small></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-del-item" onclick="hapus(${item.id})">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>`;
            });
            document.getElementById('btnSimpan').disabled = false;
        }

        let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
        document.getElementById('displayTotal').textContent = formatRupiah(total);
        updateNetTotal();
    }

    function filterBarang() {
        let supplierId = document.getElementById('supplierSelect').value;
        let keyword = document.getElementById('searchBarang').value.toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.barang-row').forEach(function(row) {
            let kode = row.getAttribute('data-kode').toLowerCase();
            let nama = row.getAttribute('data-nama').toLowerCase();
            let rowSupplierId = row.getAttribute('data-supplier-id');
            let cocokSupplier = supplierId !== '' && rowSupplierId === supplierId;
            let cocokKeyword = kode.includes(keyword) || nama.includes(keyword);
            let tampil = cocokSupplier && cocokKeyword;

            row.classList.toggle('d-none', !tampil);
            if (tampil) visibleCount++;
        });

        let statusRow = document.getElementById('barangStatus');
        statusRow.classList.toggle('d-none', visibleCount > 0);
        statusRow.querySelector('td').textContent = supplierId === ''
            ? 'Pilih supplier terlebih dahulu untuk melihat barang.'
            : 'Tidak ada barang untuk supplier atau kata kunci tersebut.';
    }

    function updateNetTotal() {
        let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
        let inputDiskon = document.getElementById('inputDiskon');
        let diskonWarning = document.getElementById('diskonWarning');
        let diskonPersen = parseFloat(inputDiskon.value) || 0;
        let diskonTidakValid = diskonPersen < 0 || diskonPersen > 100;

        diskonWarning.classList.toggle('d-none', !diskonTidakValid);
        inputDiskon.classList.toggle('is-invalid', diskonTidakValid);

        if (diskonPersen < 0) diskonPersen = 0;
        if (diskonPersen > 100) diskonPersen = 0;

        let diskonNominal = total * (diskonPersen / 100);
        let totalAkhir = Math.max(0, total - diskonNominal);
        let inputBayarAwal = document.getElementById('inputBayarAwal');
        let statusPembayaran = document.getElementById('inputStatusPembayaran');

        inputBayarAwal.max = Math.round(totalAkhir);

        if (statusPembayaran.value === 'lunas') {
            inputBayarAwal.value = Math.round(totalAkhir);
            inputBayarAwal.readOnly = true;
        } else {
            inputBayarAwal.readOnly = false;
            let bayarAwal = parseFloat(inputBayarAwal.value) || 0;
            if (bayarAwal > totalAkhir) {
                inputBayarAwal.value = Math.round(totalAkhir);
            }
        }

        let bayarAwal = parseFloat(inputBayarAwal.value) || 0;
        if (bayarAwal < 0) bayarAwal = 0;
        if (bayarAwal > totalAkhir) bayarAwal = totalAkhir;
        let sisaHutang = Math.max(0, totalAkhir - bayarAwal);

        document.getElementById('displayDiskonNominal').textContent = formatRupiah(diskonNominal);
        document.getElementById('displayNet').textContent = formatRupiah(totalAkhir);
        document.getElementById('displayBayarAwal').textContent = formatRupiah(bayarAwal);
        document.getElementById('displaySisaHutang').textContent = formatRupiah(sisaHutang);
    }

    function handleStatusPembayaranChange() {
        let statusPembayaran = document.getElementById('inputStatusPembayaran');
        let inputBayarAwal = document.getElementById('inputBayarAwal');

        if (statusPembayaran.value === 'belum_lunas' && Number(inputBayarAwal.value) === Number(inputBayarAwal.max)) {
            inputBayarAwal.value = 0;
        }

        updateNetTotal();
    }

    document.querySelectorAll('.btn-add-item').forEach(function(button) {
        button.addEventListener('click', function() {
            tambahKeKeranjang(
                parseInt(this.dataset.barangId),
                this.dataset.barangNama,
                parseFloat(this.dataset.hargaBeli)
            );
        });
    });

    document.getElementById('supplierSelect').addEventListener('change', function() {
        document.getElementById('searchBarang').value = '';
        kosongkanKeranjang();
        filterBarang();
    });

    document.getElementById('searchBarang').addEventListener('input', function() {
        filterBarang();
    });

    filterBarang();

    // Validasi submit
    document.getElementById('formPembelian').addEventListener('submit', function(e) {
        if (keranjang.length === 0) {
            e.preventDefault();
            alert('Keranjang masih kosong!');
            return;
        }

        let diskonPersen = parseFloat(document.getElementById('inputDiskon').value) || 0;
        if (diskonPersen < 0 || diskonPersen > 100) {
            e.preventDefault();
            alert('Diskon tidak diproses karena melebihi batas 100%.');
        }
    });
</script>
</div>
@endsection
