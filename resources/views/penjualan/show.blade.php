@extends('layouts.pos')

@section('title', 'Detail Transaksi - ' . $penjualan->nomor_invoice)
@section('page-title', 'Detail Transaksi')

@section('styles')
<style>
    .receipt-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 36px;
        max-width: 820px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    .receipt-header {
        text-align: center;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .receipt-header h4 { font-weight: 800; color: #0f172a; margin-bottom: 5px; }
    .receipt-header p { color: #64748b; font-size: 0.875rem; margin: 0; }
    .receipt-title-block {
        text-align: center;
        padding: 14px 0;
        margin: 18px 0 26px;
        border-top: 2px dashed #e2e8f0;
        border-bottom: 2px dashed #e2e8f0;
    }
    .receipt-title-block p {
        color: #475569;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin: 0;
    }

    .receipt-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 26px;
    }
    .receipt-info .info-item { display: flex; gap: 8px; margin-bottom: 7px; font-size: 0.85rem; }
    .receipt-info .info-item .label { color: #64748b; min-width: 100px; }
    .receipt-info .info-item .value { font-weight: 600; color: #0f172a; }

    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; font-size: 0.85rem; }
    .items-table thead th {
        background: #f8fafc; padding: 13px 18px; font-weight: 600;
        color: #64748b; text-transform: uppercase; font-size: 0.73rem;
        letter-spacing: 0.06em; border-bottom: 1px solid #e2e8f0;
    }
    .items-table tbody td {
        padding: 14px 18px; border-bottom: 1px solid #f1f5f9; color: #334155;
    }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .items-table tbody tr:hover { background: #f8fafc; }

    .receipt-summary {
        border-top: 2px solid #e2e8f0;
        padding-top: 18px;
        max-width: 320px;
        margin-left: auto;
    }
    .summary-row { display: flex; justify-content: space-between; padding: 7px 0; font-size: 0.875rem; }
    .summary-row .label { color: #64748b; }
    .summary-row .value { font-weight: 600; color: #0f172a; }
    .summary-row.total { font-size: 1.2rem; padding-top: 14px; border-top: 2px solid #e2e8f0; margin-top: 10px; }
    .summary-row.total .value { color: #10b981; font-weight: 800; }
    .summary-row.kembalian .value { color: #3b82f6; font-weight: 800; }

    .badge-status { font-size: 0.73rem; font-weight: 600; padding: 5px 12px; border-radius: 20px; }
    .badge-lunas { background: rgba(16, 185, 129, 0.1); color: #065f46; }
    .badge-sebagian { background: rgba(245, 158, 11, 0.1); color: #92400e; }
    .badge-belum { background: rgba(239, 68, 68, 0.1); color: #991b1b; }

    .receipt-footer {
        text-align: center;
        margin-top: 26px;
        padding-top: 18px;
        border-top: 2px dashed #e2e8f0;
        color: #94a3b8;
        font-size: 0.82rem;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }
    .action-bar form { margin: 0; }
    .btn-action-bar {
        padding: 9px 18px;
        border-radius: 11px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-action-bar:hover { background: #f8fafc; border-color: #3b82f6; color: #3b82f6; transform: translateY(-1px); }
    .btn-action-bar.primary { background: #3b82f6; border-color: #3b82f6; color: #fff; }
    .btn-action-bar.primary:hover { background: #2563eb; }
    .btn-action-bar.success { background: #16a34a; border-color: #16a34a; color: #fff; }
    .btn-action-bar.success:hover { background: #15803d; border-color: #15803d; color: #fff; }
    .btn-action-bar:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .alert-pos { border-radius: 14px; border: none; padding: 15px 22px; font-size: 0.875rem; }

    @media print {
        .pos-sidebar, .pos-topbar, .pos-footer, .action-bar { display: none !important; }
        .pos-main { margin-left: 0 !important; }
        .pos-content { padding: 0 !important; }
        .receipt-card { border: none; box-shadow: none; padding: 0; }
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
<!-- Action Bar -->
<div class="action-bar">
    <a href="{{ route('penjualan.index') }}" class="btn-action-bar">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <form action="{{ route('penjualan.print-thermal', $penjualan) }}" method="POST">
        @csrf
        <button type="submit" class="btn-action-bar primary">
            <i class="bi bi-receipt-cutoff me-1"></i> Cetak Struk
        </button>
    </form>
    @if ($whatsappReceiptUrl)
        <a href="{{ $whatsappReceiptUrl }}" target="_blank" rel="noopener" class="btn-action-bar success">
            <i class="bi bi-whatsapp me-1"></i> Kirim WhatsApp
        </a>
    @else
        <button type="button" class="btn-action-bar" disabled>
            <i class="bi bi-whatsapp me-1"></i> Kirim WhatsApp
        </button>
    @endif
</div>

<!-- Receipt -->
<div class="receipt-card">
    <div class="receipt-header">
        <h4><i class="bi bi-book me-2"></i>TOKO BUKU SISWA 2</h4>
        <p>Jl. Haji Agus Salim No 49</p>
    </div>
    <div class="receipt-title-block">
        <p>Struk Penjualan</p>
    </div>

    <!-- Info -->
    <div class="receipt-info">
        <div class="info-group">
            <div class="info-item"><span class="label">No. Invoice</span><span class="value">{{ $penjualan->nomor_invoice }}</span></div>
            <div class="info-item"><span class="label">Tanggal</span><span class="value">{{ $penjualan->tanggal_penjualan->format('d/m/Y H:i') }}</span></div>
            <div class="info-item"><span class="label">Kasir</span><span class="value">{{ $penjualan->user->name ?? '-' }}</span></div>
        </div>
        <div class="info-group">
            <div class="info-item"><span class="label">Pelanggan</span><span class="value">{{ $penjualan->nama_pelanggan_display }}</span></div>
            <div class="info-item">
                <span class="label">Status</span>
                <span class="value">
                    @if ($penjualan->status_pembayaran === 'lunas')
                        <span class="badge-status badge-lunas">Lunas</span>
                    @else
                        <span class="badge-status badge-belum">Belum Lunas</span>
                    @endif
                </span>
            </div>
            @if ($penjualan->pembayaran->count() > 0)
            <div class="info-item">
                <span class="label">Metode</span>
                <span class="value">{{ match ($penjualan->pembayaran->first()->metode_pembayaran) {
                    'tunai' => 'Tunai',
                    'qris' => 'QRIS',
                    'kredit' => 'Kredit',
                    default => ucfirst($penjualan->pembayaran->first()->metode_pembayaran),
                } }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th style="text-align:center;">Jumlah</th>
                <th style="text-align:right;">Harga</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->detailPenjualan as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $detail->barang->nama_barang ?? '-' }}</td>
                <td style="text-align:center;">{{ $detail->jumlah }}</td>
                <td style="text-align:right;">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                <td style="text-align:right;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    @php
        $totalBayar = (float) $penjualan->pembayaran->sum('jumlah_bayar');
        $kembalian = max(0, $totalBayar - (float) $penjualan->total_akhir);
    @endphp
    <div class="receipt-summary">
        <div class="summary-row">
            <span class="label">Total Harga</span>
            <span class="value">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
        </div>
        @if ($totalBayar > 0)
        <div class="summary-row">
            <span class="label">Jumlah Bayar</span>
            <span class="value">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
        </div>
        @endif
        @if ($kembalian > 0)
        <div class="summary-row kembalian">
            <span class="label">Kembalian</span>
            <span class="value">Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="receipt-footer">
        Terima kasih atas pembelian Anda!
    </div>
</div>
@endsection
