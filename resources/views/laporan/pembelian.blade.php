<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .report-navbar {
            background: #0f172a !important;
            padding: 14px 0;
            border-bottom: 1px solid #1e293b;
        }
        .report-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        .stat-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        .stat-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }
        .stat-box.blue::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .stat-box.red::before { background: linear-gradient(90deg, #ef4444, #f43f5e); }
        .stat-box h3 { font-weight: 800; font-size: 1.8rem; }
        .report-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 13px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .report-table td {
            padding: 13px 16px;
            font-size: 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .report-table tbody tr:hover { background: #f8fafc; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>

<body class="bg-light">

<nav class="navbar navbar-dark report-navbar no-print">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="bi bi-book me-2"></i> POS Toko Buku
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm fw-semibold px-3">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-outline-light btn-sm fw-semibold px-3">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
        </div>
    </div>
</nav>

<div class="container mt-4 pb-5" style="max-width: 900px;">
    <h3 class="fw-bold mb-1" style="color: #0f172a;"><i class="bi bi-graph-down me-2"></i>Laporan Pembelian</h3>
    <p class="text-muted small mb-4">Ringkasan dan detail pembelian supplier dalam periode terpilih.</p>

    <div class="report-card p-3 mb-4 no-print">
        <form action="{{ route('laporan.pembelian') }}" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label small fw-semibold mb-1" style="color: #64748b;">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control form-control-sm" style="border-radius: 8px; border-color: #e2e8f0;" value="{{ $tanggalMulai }}">
            </div>
            <div>
                <label class="form-label small fw-semibold mb-1" style="color: #64748b;">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control form-control-sm" style="border-radius: 8px; border-color: #e2e8f0;" value="{{ $tanggalAkhir }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm fw-semibold" style="border-radius: 8px;"><i class="bi bi-funnel me-1"></i> Filter</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-box blue">
                <h3 class="mb-1" style="color: #3b82f6;">{{ $totalTransaksi }}</h3>
                <small class="text-muted fw-semibold" style="text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">Total Pembelian</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box blue">
                <h3 class="mb-1" style="color: #3b82f6;">Rp {{ number_format($totalHargaAwal, 0, ',', '.') }}</h3>
                <small class="text-muted fw-semibold" style="text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">Total Awal</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box red">
                <h3 class="mb-1" style="color: #ef4444;">Rp {{ number_format($totalDiskonPembelian, 0, ',', '.') }}</h3>
                <small class="text-muted fw-semibold" style="text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">Total Diskon</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box red">
                <h3 class="mb-1" style="color: #ef4444;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                <small class="text-muted fw-semibold" style="text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">Total Akhir</small>
            </div>
        </div>
    </div>

    <div class="report-card mb-4">
        <div class="p-4 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
            <h5 class="fw-bold mb-1" style="color: #0f172a;"><i class="bi bi-list-ul me-2"></i>Detail Pembelian</h5>
            <small class="text-muted">Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</small>
        </div>
        <div class="table-responsive">
            <table class="table report-table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Pencatat</th>
                        <th class="text-end">Total Awal</th>
                        <th class="text-end">Diskon (%)</th>
                        <th class="text-end">Nominal Diskon</th>
                        <th class="text-end">Total Akhir</th>
                        <th class="text-end">Dibayar</th>
                        <th class="text-end">Sisa Hutang</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembelian as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold" style="color: #1e293b;">{{ $item->nomor_faktur }}</td>
                        <td>{{ $item->tanggal_pembelian->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($item->diskon_persen, 2, ',', '.') }}%</td>
                        <td class="text-end text-danger">Rp {{ number_format($item->diskon, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                        <td class="text-end text-primary">Rp {{ number_format($item->total_dibayar_supplier, 0, ',', '.') }}</td>
                        <td class="text-end {{ $item->sisa_hutang_supplier > 0 ? 'text-warning' : 'text-success' }}">Rp {{ number_format($item->sisa_hutang_supplier, 0, ',', '.') }}</td>
                        <td>
                            @if ($item->status_pembayaran === 'lunas')
                                <span class="badge" style="background: rgba(16,185,129,0.1); color: #065f46; font-weight: 600; padding: 5px 12px; border-radius: 20px;">Lunas</span>
                            @else
                                <span class="badge" style="background: rgba(245,158,11,0.12); color: #92400e; font-weight: 600; padding: 5px 12px; border-radius: 20px;">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">Tidak ada pembelian di periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if ($pembelian->count() > 0)
                <tfoot style="background: #f8fafc;">
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end py-3">TOTAL:</td>
                        <td class="text-end py-3">Rp {{ number_format($pembelian->sum('total_harga'), 0, ',', '.') }}</td>
                        <td></td>
                        <td class="text-end py-3 text-danger">Rp {{ number_format($pembelian->sum('diskon'), 0, ',', '.') }}</td>
                        <td class="text-end py-3" style="color: #ef4444;">Rp {{ number_format($pembelian->sum('total_akhir'), 0, ',', '.') }}</td>
                        <td class="text-end py-3 text-primary">Rp {{ number_format($pembelian->sum('total_dibayar_supplier'), 0, ',', '.') }}</td>
                        <td class="text-end py-3 text-warning">Rp {{ number_format($pembelian->sum('sisa_hutang_supplier'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
