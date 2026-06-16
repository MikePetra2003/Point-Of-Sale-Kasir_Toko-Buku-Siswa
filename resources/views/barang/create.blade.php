@extends('layouts.pos')

@section('title', 'Tambah Produk Barang - POS SYSTEM')
@section('page-title', 'Tambah Produk Barang')

@section('styles')
<style>
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        padding: 44px;
        margin-top: 10px;
    }
    .form-section-title {
        font-size: 0.85rem;
        color: #2563eb;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 2px solid #eff6ff;
        padding-bottom: 10px;
        margin-bottom: 22px;
    }

    .custom-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.82rem;
        margin-bottom: 7px;
    }
    .custom-input, .custom-select, .custom-textarea {
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        padding: 11px 15px;
        font-size: 0.875rem;
        color: #334155;
        transition: all 0.2s;
        background-color: #f8fafc;
    }
    .custom-input:focus, .custom-select:focus, .custom-textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
        background-color: #fff;
    }

    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: #ffffff;
        font-weight: 700;
        border-radius: 12px;
        padding: 13px 30px;
        font-size: 0.925rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.2s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
    }
    .btn-back {
        border-radius: 12px;
        padding: 13px 26px;
        font-weight: 600;
        font-size: 0.925rem;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-back:hover {
        background-color: #f8fafc;
        color: #334155;
        border-color: #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 850px;">

    <!-- Back Button Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-plus-circle text-success me-2"></i>Tambah Produk</h3>
            <p class="text-muted small mb-0">Tambahkan barang inventaris baru ke sistem POS Anda.</p>
        </div>
        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold px-3" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Alert Errors box -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background-color: rgba(239, 68, 68, 0.12); color: #991b1b;">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan input:</div>
            <ul class="mb-0 mt-2 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Container Card -->
    <div class="form-card">
        <form action="{{ route('barang.store') }}" method="POST">
            @csrf

            <!-- SECTION 1: Detail Produk -->
            <div class="form-section-title">Informasi Produk</div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label custom-label">Kode Barang *</label>
                    <input type="text" name="kode_barang" class="form-control custom-input" placeholder="Contoh: BRC0001" value="{{ old('kode_barang') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label custom-label">Nama Barang *</label>
                    <input type="text" name="nama_barang" class="form-control custom-input" placeholder="Masukkan nama produk" value="{{ old('nama_barang') }}" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label custom-label">Kategori *</label>
                    <select name="kategori_id" class="form-select custom-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}" {{ old('kategori_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label custom-label">Satuan *</label>
                    <select name="satuan_id" class="form-select custom-select" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach ($satuan as $item)
                            <option value="{{ $item->id }}" {{ old('satuan_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label custom-label">Supplier</label>
                    <select name="supplier_id" class="form-select custom-select">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($supplier as $item)
                            <option value="{{ $item->id }}" {{ old('supplier_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECTION 2: Inventaris & Keuangan -->
            <div class="form-section-title">Inventaris & Keuangan</div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label custom-label">Harga Beli (Rp) *</label>
                    <input type="number" name="harga_beli" class="form-control custom-input" value="{{ old('harga_beli', 0) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label custom-label">Harga Jual (Rp) *</label>
                    <input type="number" name="harga_jual" class="form-control custom-input" value="{{ old('harga_jual', 0) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label custom-label">Stok Awal *</label>
                    <input type="number" name="stok" class="form-control custom-input" value="{{ old('stok', 0) }}" required>
                </div>
            </div>

            <!-- Action Buttons -->
            <hr class="text-muted my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('barang.index') }}" class="btn btn-back">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-check-circle-fill me-1"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
