<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Pembelian {{ $pembelian->nomor_faktur }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 18px;
            padding-bottom: 12px;
            text-align: center;
        }

        .header h1 {
            font-size: 22px;
            letter-spacing: 2px;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        .header p {
            color: #4b5563;
            margin: 0;
        }

        .info {
            margin-bottom: 18px;
            width: 100%;
        }

        .info td {
            padding: 4px 0;
            vertical-align: top;
            width: 50%;
        }

        .label {
            color: #6b7280;
            display: block;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 0.6px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .value {
            font-size: 12px;
            font-weight: bold;
        }

        .status {
            border-radius: 10px;
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 9px;
        }

        .success {
            background: #d1fae5;
            color: #065f46;
        }

        .warning {
            background: #fef3c7;
            color: #92400e;
        }

        .danger {
            background: #fee2e2;
            color: #991b1b;
        }

        table.data {
            border-collapse: collapse;
            margin-bottom: 16px;
            width: 100%;
        }

        table.data th,
        table.data td {
            border: 1px solid #d1d5db;
            padding: 7px;
        }

        table.data th {
            background: #f3f4f6;
            color: #374151;
            font-size: 8px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #6b7280;
            font-size: 9px;
        }

        .summary {
            border-collapse: collapse;
            margin-left: auto;
            width: 45%;
        }

        .summary td {
            border-bottom: 1px solid #e5e7eb;
            padding: 6px 0;
        }

        .summary .total td {
            border-top: 2px solid #111827;
            color: #065f46;
            font-size: 13px;
            font-weight: bold;
            padding-top: 8px;
        }

        .note {
            border: 1px solid #e5e7eb;
            margin-bottom: 16px;
            padding: 8px 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>POS Toko Buku</h1>
        <p>Faktur Pembelian Barang dari Supplier</p>
    </div>

    <table class="info">
        <tr>
            <td>
                <span class="label">No. Faktur</span>
                <span class="value">{{ $pembelian->nomor_faktur }}</span>
            </td>
            <td>
                <span class="label">Pemasok / Supplier</span>
                <span class="value">{{ $pembelian->supplier->nama_supplier ?? '-' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Tanggal Masuk</span>
                <span class="value">{{ $pembelian->tanggal_pembelian->format('d/m/Y H:i') }}</span>
            </td>
            <td>
                <span class="label">Status Pembayaran</span>
                @if ($pembelian->status_pembayaran === 'lunas')
                    <span class="status success">Lunas</span>
                @else
                    <span class="status warning">Belum Lunas</span>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">Pencatat Transaksi</span>
                <span class="value">{{ $pembelian->user->name ?? '-' }}</span>
            </td>
        </tr>
    </table>

    @if ($pembelian->keterangan)
        <div class="note">
            <span class="label">Keterangan Catatan</span>
            {{ $pembelian->keterangan }}
        </div>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th style="width: 34px;">No</th>
                <th>Nama Barang Pasokan</th>
                <th class="text-center" style="width: 95px;">Jumlah</th>
                <th class="text-right" style="width: 105px;">Harga Beli</th>
                <th class="text-right" style="width: 115px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian->detailPembelian as $detail)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $detail->barang->nama_barang ?? '-' }}</strong></td>
                    <td class="text-center">
                        <strong>{{ $detail->jumlah }} pcs</strong>
                    </td>
                    <td class="text-right">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Total Awal</td>
            <td class="text-right"><strong>Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</strong></td>
        </tr>
        @if ($pembelian->diskon > 0)
            <tr>
                <td>Diskon ({{ number_format($pembelian->diskon_persen, 2, ',', '.') }}%)</td>
                <td class="text-right"><strong>- Rp {{ number_format($pembelian->diskon, 0, ',', '.') }}</strong></td>
            </tr>
        @endif
        <tr class="total">
            <td>Total Akhir</td>
            <td class="text-right">Rp {{ number_format($pembelian->total_akhir, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Dibayar</td>
            <td class="text-right"><strong>Rp {{ number_format($pembelian->total_dibayar_supplier, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Sisa Hutang</td>
            <td class="text-right"><strong>Rp {{ number_format($pembelian->sisa_hutang_supplier, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
</body>
</html>
