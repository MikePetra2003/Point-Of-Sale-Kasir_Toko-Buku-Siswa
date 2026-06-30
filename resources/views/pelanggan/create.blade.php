@extends('layouts.pos')

@section('title', 'Tambah Pelanggan - POS Toko Buku')
@section('page-title', 'Tambah Pelanggan')

@section('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 36px;
        max-width: 620px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    .form-card h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 26px;
    }
    .form-card .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 7px;
    }
    .form-card .form-control {
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        padding: 11px 16px;
        font-size: 0.875rem;
        background: #f8fafc;
    }
    .form-card .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        background: #fff;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 11px 26px;
        border-radius: 11px;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(16,185,129,0.2);
    }
    .btn-save:hover { background: #059669; color: #fff; transform: translateY(-1px); }
    .btn-back {
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        padding: 11px 26px;
        border-radius: 11px;
        font-size: 0.875rem;
        text-decoration: none;
    }
    .btn-back:hover { background: #f8fafc; color: #334155; }
    .alert-pos { border-radius: 14px; border: none; padding: 15px 22px; font-size: 0.875rem; }
</style>
@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-pos mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi kesalahan input.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">
    <h3><i class="bi bi-person-plus-fill me-2"></i>Tambah Pelanggan Baru</h3>
        <p class="text-muted small mb-4">No ID pelanggan dibuat otomatis. Pelanggan baru belum boleh kredit sampai owner mengaktifkan di halaman edit.</p>

    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
            <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan') }}" placeholder="Masukkan nama pelanggan" required>
        </div>

        <div class="mb-4">
            <label class="form-label">No Telepon</label>
            <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}" placeholder="Contoh: 08123456789">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-save">
                <i class="bi bi-check-lg me-1"></i> Simpan
            </button>
            <a href="{{ route('pelanggan.index') }}" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
