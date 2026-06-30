<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian - {{ $pembelian->nomor_faktur }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .pos-navbar {
            background-color: #0f172a !important;
            padding: 14px 0;
            border-bottom: 1px solid #1e293b;
        }
        .pos-brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #10b981);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-right: 10px;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .invoice-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            padding: 44px;
            position: relative;
        }
        .invoice-header-title {
            letter-spacing: 0.1em;
            color: #0f172a;
            font-weight: 800;
        }

        .invoice-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 13px 18px;
            border-bottom: 1px solid #e2e8f0;
        }
        .invoice-table td {
            padding: 15px 18px;
            font-size: 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-selesai {
            background-color: rgba(16, 185, 129, 0.1);
            color: #065f46;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 6px 14px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #92400e;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 6px 14px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge-batal {
            background-color: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 6px 14px;
            border-radius: 20px;
            display: inline-block;
        }

        .info-label {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 0.95rem;
            color: #0f172a;
            font-weight: 600;
        }

        /* Print Media styling */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff;
                color: #000000;
                font-size: 12px;
            }
            .invoice-card {
                border: none !important;
                box-shadow: none !important;
                padding: 10px !important;
            }
            .invoice-table th {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
            }
        }
    </style>
</head>

<body>

<!-- Top Navigation Bar -->
<nav class="navbar navbar-dark pos-navbar no-print">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
            <div class="pos-brand-icon">
                <i class="bi bi-book"></i>
            </div>
            <span>Buku Siswa 2</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pembelian.index') }}" class="btn btn-outline-light btn-sm fw-semibold px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('pembelian.export.pdf', $pembelian) }}" class="btn btn-outline-light btn-sm fw-semibold px-3">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export Faktur
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4 pb-5" style="max-width: 850px;">

    <!-- Success message alert -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 no-print" role="alert" style="background-color: rgba(16, 185, 129, 0.12); color: #065f46;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Invoice Card Paper -->
    <div class="invoice-card">
        <!-- Logo Header -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-2">
                <div class="pos-brand-icon" style="width: 42px; height: 42px; border-radius: 10px; font-size: 1.2rem;">
                    <i class="bi bi-book"></i>
                </div>
                <h4 class="invoice-header-title mb-0 ms-1">TOKO BUKU SISWA 2</h4>
            </div>
            <p class="text-muted small mb-0">Faktur Pembelian Barang </p>
            <hr class="my-4 text-muted">
        </div>

        <!-- Information Grid -->
        <div class="row g-4 mb-4">
            <!-- Left Info Block -->
            <div class="col-sm-6">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="info-label">No. Faktur</div>
                        <div class="info-value text-primary fs-5">{{ $pembelian->nomor_faktur }}</div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">Tanggal Masuk</div>
                        <div class="info-value">{{ $pembelian->tanggal_pembelian->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">Pencatat Transaksi</div>
                        <div class="info-value">{{ $pembelian->user->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Info Block -->
            <div class="col-sm-6">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="info-label">Supplier</div>
                        <div class="info-value text-dark">{{ $pembelian->supplier->nama_supplier ?? '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="mt-1">
                            @if ($pembelian->status_pembayaran === 'lunas')
                                <span class="badge-selesai">Lunas</span>
                            @else
                                <span class="badge-pending">Belum Lunas</span>
                            @endif
                        </div>
                    </div>
                    @if ($pembelian->keterangan)
                    <div class="col-12">
                        <div class="info-label">Keterangan</div>
                        <div class="info-value fw-medium text-secondary" style="font-size: 0.9rem;">{{ $pembelian->keterangan }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive border rounded-3 overflow-hidden mb-4">
            <table class="table invoice-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">No</th>
                        <th>Nama Barang Pasokan</th>
                        <th class="text-center" style="width: 130px;">Jumlah</th>
                        <th class="text-end" style="width: 180px;">Harga Beli</th>
                        <th class="text-end" style="width: 200px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelian->detailPembelian as $detail)
                    <tr>
                        <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="text-center fw-bold">
                            {{ $detail->jumlah_satuan ?: $detail->jumlah }} {{ $detail->satuan->nama_satuan ?? 'pcs' }}
                        </td>
                        <td class="text-end">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Price summary block -->
        <div class="row justify-content-end">
            <div class="col-md-6">
                <div class="border rounded-3 p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-dark">Total Awal</span>
                        <span class="fw-bold">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</span>
                    </div>
                    @if ($pembelian->diskon > 0)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-dark">Diskon ({{ number_format($pembelian->diskon_persen, 2, ',', '.') }}%)</span>
                        <span class="fw-bold text-danger">- Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                        <span class="fw-bold text-dark fs-5">Total Akhir</span>
                        <span class="fw-extrabold text-success fs-4" style="font-weight: 800;">Rp {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                        <span class="fw-semibold text-dark">Dibayar</span>
                        <span class="fw-bold text-primary">Rp {{ number_format($pembelian->total_dibayar_supplier, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-dark">Sisa Hutang</span>
                        <span class="fw-bold {{ $pembelian->sisa_hutang_supplier > 0 ? 'text-warning' : 'text-success' }}">
                            Rp {{ number_format($pembelian->sisa_hutang_supplier, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
