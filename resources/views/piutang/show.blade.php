@extends('layouts.pos')

@section('title', 'Detail Piutang - POS Toko Buku')
@section('page-title', 'Detail Piutang')

@section('styles')
<style>
    .detail-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 30px;
        margin-bottom: 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .detail-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 26px;
    }
    .info-item { margin-bottom: 14px; }
    .info-item .label { font-size: 0.73rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 3px; letter-spacing: 0.04em; }
    .info-item .value { font-size: 0.92rem; font-weight: 600; color: #0f172a; }
    .info-item .value.danger { color: #ef4444; }
    .info-item .value.success { color: #10b981; }
    .invoice-link {
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        background: transparent;
        font-weight: 600;
        padding: 0;
    }
    .invoice-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
    .penjualan-modal .modal-content { border: none; border-radius: 14px; }
    .penjualan-modal .modal-header { border-bottom: 1px solid #e5e7eb; padding: 20px 24px; }
    .penjualan-modal .modal-title { color: #1f2937; font-size: 1rem; font-weight: 800; }
    .penjualan-modal .modal-subtitle { color: #6b7280; font-size: 0.8rem; margin-top: 4px; }
    .modal-info-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
    .modal-info-item { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .modal-info-item span { color: #6b7280; display: block; font-size: 0.72rem; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }
    .modal-info-item strong { color: #1f2937; font-size: 0.88rem; }
    .modal-section-title { color: #1f2937; font-size: 0.92rem; font-weight: 800; margin: 18px 0 10px; }
    .modal-detail-table { border-collapse: collapse; font-size: 0.84rem; width: 100%; }
    .modal-detail-table thead th { background: #f9fafb; border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 0.72rem; font-weight: 700; padding: 10px 12px; text-transform: uppercase; }
    .modal-detail-table tbody td { border-bottom: 1px solid #f3f4f6; color: #374151; padding: 10px 12px; }
    .modal-summary { border-top: 1px solid #e5e7eb; margin: 14px 0 4px auto; max-width: 320px; padding-top: 10px; }
    .modal-summary div { display: flex; justify-content: space-between; padding: 5px 0; }
    .modal-summary span { color: #6b7280; }
    .modal-summary-total { border-top: 2px solid #e5e7eb; margin-top: 6px; padding-top: 10px !important; }
    .modal-summary-total strong { color: #10b981; font-size: 1.05rem; }

    .progress-bar-custom {
        height: 10px;
        border-radius: 6px;
        background: #f3f4f6;
        overflow: hidden;
        margin-top: 8px;
    }
    .progress-bar-custom .fill {
        height: 100%;
        border-radius: 6px;
        background: linear-gradient(90deg, #10b981, #059669);
        transition: width 0.4s;
    }

    .badge-status { font-size: 0.75rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; }
    .badge-lunas { background: #d1fae5; color: #065f46; }
    .badge-belum { background: #fee2e2; color: #991b1b; }
    .badge-sebagian { background: #fef3c7; color: #92400e; }

    .history-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .history-table thead th {
        background: #f9fafb; padding: 12px 16px; font-weight: 700;
        color: #6b7280; text-transform: uppercase; font-size: 0.72rem;
        letter-spacing: 0.04em; border-bottom: 2px solid #e5e7eb;
    }
    .history-table tbody td {
        padding: 12px 16px; border-bottom: 1px solid #f3f4f6; color: #374151;
    }
    .history-table tbody tr:last-child td { border-bottom: none; }
    .history-table tbody tr:hover { background: #f9fafb; }

    .payment-form {
        background: #fff;
        border-radius: 18px;
        border: 2px solid #3b82f6;
        padding: 30px;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.08);
    }
    .payment-form h5 { font-size: 1rem; font-weight: 700; color: #3b82f6; margin-bottom: 22px; }
    .payment-form .form-label { font-size: 0.8rem; font-weight: 600; color: #475569; }
    .payment-form .form-control,
    .payment-form .form-select {
        border-radius: 11px; border: 1px solid #e2e8f0; padding: 11px 15px; font-size: 0.875rem; background: #f8fafc;
    }
    .payment-form .form-control:focus,
    .payment-form .form-select:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); background: #fff;
    }
    .btn-pay {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none; color: #fff; font-weight: 600;
        padding: 11px 26px; border-radius: 11px; font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    .btn-pay:hover { background: #2563eb; color: #fff; transform: translateY(-1px); }

    .action-bar {
        display: flex; gap: 10px; margin-bottom: 22px;
    }
    .btn-action-bar {
        padding: 9px 18px; border-radius: 11px; font-size: 0.85rem; font-weight: 600;
        border: 1px solid #e2e8f0; background: #fff; color: #334155; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-action-bar:hover { background: #f8fafc; border-color: #3b82f6; color: #3b82f6; transform: translateY(-1px); }

    .btn-action-bar.btn-warning-custom {
        color: #d97706;
        border-color: #f59e0b;
    }
    .btn-action-bar.btn-warning-custom:hover {
        background: #fffbeb;
        border-color: #d97706;
        color: #b45309;
    }

    .btn-action-bar.btn-danger-custom {
        color: #dc2626;
        border-color: #f87171;
        background: #fff;
        cursor: pointer;
    }
    .btn-action-bar.btn-danger-custom:hover {
        background: #fef2f2;
        border-color: #dc2626;
        color: #b91c1c;
    }

    .alert-pos { border-radius: 14px; border: none; padding: 15px 22px; font-size: 0.875rem; }

    @media (max-width: 767.98px) {
        .info-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-pos alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-pos alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@php
    $backUrl = auth()->user()?->role === 'owner' ? route('piutang.index') : route('penjualan.create');
    $isPiutangKredit = $piutang->penjualan?->pembayaran->first()?->metode_pembayaran === 'kredit';
@endphp

<!-- Action Bar -->
<div class="action-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="{{ $backUrl }}" class="btn-action-bar">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if ($isPiutangKredit && $piutang->pembayaranPiutang->isEmpty())
            <a href="{{ route('piutang.edit', $piutang->id) }}" class="btn-action-bar">
                <i class="bi bi-pencil-square me-1"></i> Lengkapi Data
            </a>
        @endif
    </div>
    @if (auth()->user()?->role === 'owner')
        <div class="d-flex gap-2">
            <a href="{{ route('piutang.edit-general', $piutang->id) }}" class="btn-action-bar btn-warning-custom">
                <i class="bi bi-pencil-square me-1"></i> Edit Piutang
            </a>
            <form action="{{ route('piutang.destroy', $piutang->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data piutang ini? Semua riwayat pembayaran cicilan terkait juga akan terhapus.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action-bar btn-danger-custom">
                    <i class="bi bi-trash3 me-1"></i> Hapus Piutang
                </button>
            </form>
        </div>
    @endif
</div>

@php
    $persen = $piutang->total_piutang > 0 ? ($piutang->total_dibayar / $piutang->total_piutang) * 100 : 0;
@endphp

<!-- Detail Info -->
<div class="detail-card">
    <h5><i class="bi bi-wallet2"></i> Informasi Piutang</h5>

    <div class="info-grid">
        <div>
            <div class="info-item">
                <div class="label">Invoice</div>
                <div class="value">
                    @if ($piutang->penjualan)
                        <button type="button" class="invoice-link" data-bs-toggle="modal" data-bs-target="#penjualanModal{{ $piutang->penjualan->id }}" title="Lihat detail penjualan {{ $piutang->penjualan->nomor_invoice }}">
                            <i class="bi bi-receipt"></i>{{ $piutang->penjualan->nomor_invoice }}
                        </button>
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Pelanggan</div>
                <div class="value">{{ $piutang->pelanggan->nama_pelanggan_display ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="label">No. Telepon</div>
                <div class="value">{{ $piutang->pelanggan->no_telepon ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Jatuh Tempo</div>
                <div class="value {{ $piutang->tanggal_jatuh_tempo && $piutang->tanggal_jatuh_tempo->isPast() && $piutang->status !== 'lunas' ? 'danger' : '' }}">
                    {{ $piutang->tanggal_jatuh_tempo ? $piutang->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                    @if($piutang->tanggal_jatuh_tempo && $piutang->tanggal_jatuh_tempo->isPast() && $piutang->status !== 'lunas')
                        <i class="bi bi-exclamation-circle-fill ms-1"></i> Lewat jatuh tempo
                    @endif
                </div>
            </div>
        </div>
        <div>
            <div class="info-item">
                <div class="label">Total Piutang</div>
                <div class="value">Rp {{ number_format($piutang->total_piutang, 0, ',', '.') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Total Dibayar</div>
                <div class="value success">Rp {{ number_format($piutang->total_dibayar, 0, ',', '.') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Sisa Piutang</div>
                <div class="value {{ $piutang->sisa_piutang > 0 ? 'danger' : 'success' }}">
                    Rp {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}
                </div>
            </div>
            <div class="info-item">
                <div class="label">Status</div>
                <div class="value">
                    @if ($piutang->status === 'lunas')
                        <span class="badge-status badge-lunas">Lunas</span>
                    @else
                        <span class="badge-status badge-belum">Belum Lunas</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Progress -->
    <div style="margin-top:16px;">
        <div style="display:flex;justify-content:space-between;font-size:0.78rem;color:#6b7280;">
            <span>Progress Pembayaran</span>
            <span class="fw-bold">{{ number_format($persen, 0) }}%</span>
        </div>
        <div class="progress-bar-custom">
            <div class="fill" style="width:{{ $persen }}%"></div>
        </div>
    </div>
</div>

<!-- Riwayat Pembayaran -->
<div class="detail-card">
    <h5><i class="bi bi-clock-history"></i> Riwayat Pembayaran Cicilan</h5>

    @if ($piutang->pembayaranPiutang->count() > 0)
    <table class="history-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Metode</th>
                <th style="text-align:right;">Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($piutang->pembayaranPiutang as $bayar)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $bayar->tanggal_bayar->format('d/m/Y H:i') }}</td>
                <td><span class="badge-status badge-lunas">{{ strtoupper($bayar->metode_pembayaran) }}</span></td>
                <td style="text-align:right;font-weight:600;">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                <td class="text-muted">{{ $bayar->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="text-center py-4 text-muted">
        <i class="bi bi-inbox fs-2"></i>
        <p class="mt-2 mb-0">Belum ada pembayaran cicilan</p>
    </div>
    @endif
</div>

<!-- Form Bayar (jika belum lunas) -->
@if ($piutang->status !== 'lunas')
<div class="payment-form">
    <h5><i class="bi bi-cash-coin me-2"></i>Bayar Cicilan</h5>

    <form method="POST" action="{{ route('piutang.bayar', $piutang->id) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Jumlah Bayar (Rp) *</label>
                <input type="number" name="jumlah_bayar" class="form-control" required min="1"
                    max="{{ $piutang->sisa_piutang }}" placeholder="Maks: {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Metode *</label>
                <select name="metode_pembayaran" class="form-select" required>
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" class="form-control" placeholder="Opsional...">
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-pay" onclick="return confirm('Simpan pembayaran cicilan ini?')">
                <i class="bi bi-check-circle me-1"></i> Simpan Pembayaran
            </button>
        </div>
    </form>
</div>
@endif

@if ($piutang->penjualan)
    @include('piutang.partials.penjualan-modal', [
        'penjualan' => $piutang->penjualan,
        'modalId' => 'penjualanModal' . $piutang->penjualan->id,
    ])
@endif
@endsection
