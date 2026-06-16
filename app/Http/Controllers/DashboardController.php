<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PiutangPelanggan;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $totalSupplier = Supplier::count();
        $totalPelanggan = Pelanggan::count();
        $stokMenipis = Barang::where('stok', '<=', 5)->get();
        $stokMenipisCount = $stokMenipis->count();

        // Statistik Penjualan
        $totalPenjualan = Penjualan::count();
        $pendapatanHariIni = Penjualan::whereDate('tanggal_penjualan', today())
            ->where('status_pembayaran', 'lunas')
            ->sum('total_akhir');
        $pendapatanBulanIni = Penjualan::whereMonth('tanggal_penjualan', now()->month)
            ->whereYear('tanggal_penjualan', now()->year)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_akhir');

        // Statistik Pembelian
        $totalPembelian = Pembelian::count();
        $pengeluaranBulanIni = Pembelian::whereMonth('tanggal_pembelian', now()->month)
            ->whereYear('tanggal_pembelian', now()->year)
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Statistik Piutang
        $totalPiutangAktif = PiutangPelanggan::where('status', '!=', 'lunas')->count();
        $totalNilaiPiutang = PiutangPelanggan::where('status', '!=', 'lunas')->sum('sisa_piutang');

        // Hutang ke Supplier (pembelian belum lunas)
        $totalHutang = Pembelian::where('status', '!=', 'selesai')->sum('total_harga');

        // Penjualan Terbaru (5 terakhir)
        $penjualanTerbaru = Penjualan::with('pelanggan')
            ->orderBy('tanggal_penjualan', 'desc')
            ->limit(5)
            ->get();

        // Grafik Penjualan Harian (7 hari terakhir)
        $chartPenjualan = Penjualan::select(
            DB::raw('DATE(tanggal_penjualan) as tanggal'),
            DB::raw('SUM(total_akhir) as total_penjualan'),
            DB::raw('COUNT(*) as jumlah_transaksi')
        )
            ->where('tanggal_penjualan', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(tanggal_penjualan)'))
            ->orderBy('tanggal')
            ->get();

        // Grafik Keuntungan Harian (7 hari terakhir)
        // Keuntungan = total_akhir penjualan - total harga_beli barang yang terjual
        $chartKeuntungan = DB::table('penjualan')
            ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
            ->join('barang', 'detail_penjualan.barang_id', '=', 'barang.id')
            ->select(
                DB::raw('DATE(penjualan.tanggal_penjualan) as tanggal'),
                DB::raw('SUM(detail_penjualan.subtotal) as pendapatan'),
                DB::raw('SUM(detail_penjualan.jumlah * barang.harga_beli) as modal'),
                DB::raw('SUM(detail_penjualan.subtotal) - SUM(detail_penjualan.jumlah * barang.harga_beli) as keuntungan')
            )
            ->where('penjualan.tanggal_penjualan', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(penjualan.tanggal_penjualan)'))
            ->orderBy('tanggal')
            ->get();

        // Siapkan data chart untuk 7 hari terakhir (isi 0 jika tidak ada data)
        $labels = [];
        $dataPenjualan = [];
        $dataKeuntungan = [];
        $dataTransaksi = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->translatedFormat('d M');

            $penjualanHari = $chartPenjualan->firstWhere('tanggal', $date);
            $dataPenjualan[] = $penjualanHari ? (float) $penjualanHari->total_penjualan : 0;
            $dataTransaksi[] = $penjualanHari ? (int) $penjualanHari->jumlah_transaksi : 0;

            $keuntunganHari = $chartKeuntungan->firstWhere('tanggal', $date);
            $dataKeuntungan[] = $keuntunganHari ? (float) $keuntunganHari->keuntungan : 0;
        }

        // Keuntungan total bulan ini
        $keuntunganBulanIni = DB::table('penjualan')
            ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
            ->join('barang', 'detail_penjualan.barang_id', '=', 'barang.id')
            ->whereMonth('penjualan.tanggal_penjualan', now()->month)
            ->whereYear('penjualan.tanggal_penjualan', now()->year)
            ->selectRaw('COALESCE(SUM(detail_penjualan.subtotal) - SUM(detail_penjualan.jumlah * barang.harga_beli), 0) as keuntungan')
            ->value('keuntungan') ?? 0;

        return view('dashboard', compact(
            'totalBarang',
            'totalSupplier',
            'totalPelanggan',
            'stokMenipis',
            'stokMenipisCount',
            'totalPenjualan',
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'totalPembelian',
            'pengeluaranBulanIni',
            'totalPiutangAktif',
            'totalNilaiPiutang',
            'totalHutang',
            'penjualanTerbaru',
            'labels',
            'dataPenjualan',
            'dataKeuntungan',
            'dataTransaksi',
            'keuntunganBulanIni'
        ));
    }
}
