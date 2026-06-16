<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.35;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 16px;
            padding-bottom: 10px;
            text-align: center;
        }

        .header h1 {
            font-size: 20px;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        .header p {
            color: #4b5563;
            margin: 0;
        }

        .summary {
            margin-bottom: 16px;
            width: 100%;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            width: 33.33%;
        }

        .summary .label {
            color: #6b7280;
            display: block;
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .summary .value {
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 14px 0 6px;
            text-transform: uppercase;
        }

        table.data {
            border-collapse: collapse;
            width: 100%;
        }

        table.data th,
        table.data td {
            border: 1px solid #d1d5db;
            padding: 6px;
        }

        table.data th {
            background: #f3f4f6;
            font-size: 8px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            background: #f9fafb;
            font-weight: bold;
        }

        .empty {
            color: #6b7280;
            padding: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>Periode {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Transaksi</span>
                <span class="value">{{ number_format($totalTransaksi) }}</span>
            </td>
            <td>
                <span class="label">Total Pendapatan</span>
                <span class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </td>
            <td>
                <span class="label">Lunas / Belum Lunas</span>
                <span class="value">{{ $totalLunas }} / {{ $totalBelumLunas }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Detail Transaksi Penjualan</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-right">Total Harga</th>
                <th class="text-right">Total Akhir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penjualan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor_invoice }}</td>
                    <td>{{ $item->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->nama_pelanggan_display }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                    <td>{{ $item->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Lunas' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">Tidak ada transaksi di periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($penjualan->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($penjualan->sum('total_harga'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($penjualan->sum('total_akhir'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if ($barangTerlaris->count() > 0)
        <div class="section-title">Top 10 Barang Terlaris</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Total Terjual</th>
                    <th class="text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangTerlaris as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->total_terjual }} pcs</td>
                        <td class="text-right">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
