<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Laporan Penjualan
     */
    public function penjualan(Request $request)
    {
        $data = $this->dataLaporanPenjualan($request);

        return view('laporan.penjualan', compact(
            'data'
        ));
    }

    public function exportPenjualanXlsx(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? now()->format('Y-m-d');
        $temporaryPath = storage_path('app/laravel-excel');

        File::ensureDirectoryExists($temporaryPath);
        config(['excel.temporary_files.local_path' => $temporaryPath]);

        return Excel::download(
            new LaporanPenjualanExport($tanggalMulai, $tanggalAkhir),
            "laporan-penjualan-{$tanggalMulai}-{$tanggalAkhir}.xlsx",
            ExcelWriter::XLSX,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="laporan-penjualan-'.$tanggalMulai.'-'.$tanggalAkhir.'.xlsx"; filename*=UTF-8\'\'laporan-penjualan-'.$tanggalMulai.'-'.$tanggalAkhir.'.xlsx',
            ]
        );
    }

    public function exportPenjualanPdf(Request $request)
    {
        $data = $this->dataLaporanPenjualan($request);
        $filename = "laporan-penjualan-{$data['tanggalMulai']}-{$data['tanggalAkhir']}.pdf";

        return Pdf::loadView('laporan.penjualan-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    /**
     * Laporan Pembelian
     */
    public function pembelian(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? now()->format('Y-m-d');

        $pembelian = Pembelian::with(['user', 'supplier', 'hutangSupplier'])
            ->whereDate('tanggal_pembelian', '>=', $tanggalMulai)
            ->whereDate('tanggal_pembelian', '<=', $tanggalAkhir)
            ->latest()
            ->get();

        $totalTransaksi = $pembelian->count();
        $totalHargaAwal = $pembelian->sum('total_harga');
        $totalDiskonPembelian = $pembelian->sum('diskon');
        $totalPengeluaran = $pembelian->sum('total_akhir');

        return view('laporan.pembelian', compact(
            'pembelian',
            'tanggalMulai',
            'tanggalAkhir',
            'totalTransaksi',
            'totalHargaAwal',
            'totalDiskonPembelian',
            'totalPengeluaran'
        ));
    }

    private function dataLaporanPenjualan(Request $request): array
    {
        $tanggalMulai = $request->tanggal_mulai ?? now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? now()->format('Y-m-d');

        $penjualan = Penjualan::with(['user', 'pelanggan', 'detailPenjualan'])
            ->whereDate('tanggal_penjualan', '>=', $tanggalMulai)
            ->whereDate('tanggal_penjualan', '<=', $tanggalAkhir)
            ->latest()
            ->get();

        $barangTerlaris = DetailPenjualan::select('barang_id', DB::raw('SUM(jumlah) as total_terjual'), DB::raw('SUM(subtotal) as total_pendapatan'))
            ->whereHas('penjualan', function ($q) use ($tanggalMulai, $tanggalAkhir) {
                $q->whereDate('tanggal_penjualan', '>=', $tanggalMulai)
                    ->whereDate('tanggal_penjualan', '<=', $tanggalAkhir);
            })
            ->groupBy('barang_id')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->with('barang')
            ->get();

        return [
            'penjualan' => $penjualan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
            'totalTransaksi' => $penjualan->count(),
            'totalPendapatan' => $penjualan->sum('total_akhir'),
            'totalLunas' => $penjualan->where('status_pembayaran', 'lunas')->count(),
            'totalBelumLunas' => $penjualan->where('status_pembayaran', '!=', 'lunas')->count(),
            'barangTerlaris' => $barangTerlaris,
        ];
    }
}
