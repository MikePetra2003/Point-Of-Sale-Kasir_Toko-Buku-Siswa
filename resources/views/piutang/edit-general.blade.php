@extends('layouts.pos')

@section('title', 'Edit Piutang - POS Toko Buku')
@section('page-title', 'Edit Piutang Pelanggan')

@section('styles')
<style>
    .page-shell {
        max-width: 980px;
        margin: 0 auto;
    }
    .action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }
    .btn-action-bar {
        padding: 9px 18px;
        border-radius: 11px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-action-bar:hover {
        background: #f8fafc;
        border-color: #3b82f6;
        color: #3b82f6;
        transform: translateY(-1px);
    }
    .alert-pos {
        border-radius: 14px;
        border: none;
        padding: 15px 22px;
        font-size: 0.875rem;
        margin-bottom: 22px;
    }
    .edit-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .edit-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .card-subtitle {
        color: #64748b;
        font-size: 0.86rem;
        margin-bottom: 24px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .info-box {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 18px;
        background: #f8fafc;
    }
    .info-box span {
        display: block;
        font-size: 0.74rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 5px;
    }
    .info-box strong {
        color: #0f172a;
        font-size: 0.92rem;
    }
    .edit-form .form-label {
        font-size: 0.79rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .edit-form .form-control,
    .edit-form .form-select,
    .edit-form .form-control[readonly] {
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        padding: 11px 14px;
        font-size: 0.88rem;
        background: #f8fafc;
    }
    .edit-form .form-control:focus,
    .edit-form .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: #fff;
    }
    .form-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }
    .btn-primary-pos {
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-primary-pos:hover {
        color: #fff;
        transform: translateY(-1px);
    }
    @media (max-width: 767.98px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="page-shell">
    @if (session('error'))
        <div class="alert alert-danger alert-pos alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="action-bar">
        <a href="{{ route('piutang.show', $piutang->id) }}" class="btn-action-bar">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="edit-card">
        <h5><i class="bi bi-pencil-square me-2"></i>Edit Data Piutang Pelanggan</h5>
        <div class="card-subtitle">
            Sesuaikan data piutang pelanggan, tanggal jatuh tempo, atau catatan keterangan di bawah ini.
        </div>

        <div class="info-grid">
            <div class="info-box">
                <span>Invoice</span>
                <strong>{{ $piutang->penjualan->nomor_invoice ?? '-' }}</strong>
            </div>
            <div class="info-box">
                <span>Pelanggan</span>
                <strong>{{ $piutang->pelanggan->nama_pelanggan_display ?? '-' }}</strong>
            </div>
            <div class="info-box">
                <span>Total Dibayar</span>
                <strong>Rp {{ number_format($piutang->total_dibayar, 0, ',', '.') }}</strong>
            </div>
            <div class="info-box">
                <span>Status Saat Ini</span>
                <strong>{{ strtoupper(str_replace('_', ' ', $piutang->status)) }}</strong>
            </div>
        </div>

        <form method="POST" action="{{ route('piutang.update-general', $piutang->id) }}" class="edit-form">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Total Piutang (Rp) *</label>
                    <input
                        type="number"
                        name="total_piutang"
                        class="form-control"
                        value="{{ old('total_piutang', (int)$piutang->total_piutang) }}"
                        min="{{ (int)$piutang->total_dibayar }}"
                        required>
                    <div class="form-text text-muted small mt-1">
                        Minimal harus sama dengan jumlah yang sudah dibayar: Rp {{ number_format($piutang->total_dibayar, 0, ',', '.') }}
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input
                        type="date"
                        name="tanggal_jatuh_tempo"
                        class="form-control"
                        value="{{ old('tanggal_jatuh_tempo', optional($piutang->tanggal_jatuh_tempo)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Keterangan</label>
                    <textarea
                        name="keterangan"
                        class="form-control"
                        rows="3"
                        placeholder="Catatan tambahan mengenai piutang ini...">{{ old('keterangan', $piutang->keterangan) }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary-pos">
                    <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
