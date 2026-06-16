<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPembelian;
use App\Models\HutangSupplier;
use App\Models\PembayaranHutang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembelianController extends Controller
{
    /**
     * Daftar semua pembelian dari supplier.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $pembelian = Pembelian::with(['supplier', 'hutangSupplier'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('nomor_faktur', 'like', "%{$keyword}%")
                    ->orWhereHas('supplier', function ($q) use ($keyword) {
                        $q->where('nama_supplier', 'like', "%{$keyword}%");
                    });
            })
            ->latest()
            ->paginate(15);

        return view('pembelian.index', compact('pembelian', 'keyword'));
    }

    /**
     * Form tambah pembelian baru.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $barangs = Barang::with(['kategori', 'satuan'])
            ->orderBy('nama_barang')
            ->get();

        return view('pembelian.create', compact('suppliers', 'barangs'));
    }

    /**
     * Simpan pembelian baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'jumlah_bayar_awal' => 'nullable|numeric|min:0',
            'metode_pembayaran_awal' => 'nullable|in:tunai,qris,transfer',
            'status_pembayaran' => 'nullable|in:lunas,belum_lunas',
            'keterangan' => 'nullable|string',
        ]);

        $items = collect($request->items)
            ->map(function ($item) {
                return [
                    'barang_id' => (int) $item['barang_id'],
                    'jumlah' => (int) $item['jumlah'],
                ];
            })
            ->groupBy('barang_id')
            ->map(function ($rows, $barangId) {
                return [
                    'barang_id' => (int) $barangId,
                    'jumlah' => $rows->sum('jumlah'),
                ];
            })
            ->values();

        DB::beginTransaction();

        try {
            $barangIds = $items->pluck('barang_id');
            $barangs = Barang::whereIn('id', $barangIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($barangs->count() !== $barangIds->count()) {
                throw new \RuntimeException('Barang yang dipilih tidak valid.');
            }

            $supplierId = (int) $request->supplier_id;
            $barangTidakSesuaiSupplier = $barangs->first(function ($barang) use ($supplierId) {
                return (int) $barang->supplier_id !== $supplierId;
            });

            if ($barangTidakSesuaiSupplier) {
                throw ValidationException::withMessages([
                    'items' => 'Barang yang dipilih harus sesuai dengan supplier pembelian.',
                ]);
            }

            $totalHarga = $items->sum(function ($item) use ($barangs) {
                return $item['jumlah'] * $barangs[$item['barang_id']]->harga_beli;
            });

            $tanggalPembelian = now();

            // Generate nomor faktur: PB-YYYYMMDD-0001
            $today = $tanggalPembelian->format('Ymd');
            $lastFaktur = Pembelian::where('nomor_faktur', 'like', "PB-{$today}-%")
                ->lockForUpdate()
                ->orderBy('nomor_faktur', 'desc')
                ->first();

            if ($lastFaktur) {
                $lastNumber = (int) substr($lastFaktur->nomor_faktur, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $nomorFaktur = "PB-{$today}-".str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            // Diskon persen disimpan sebagai persen, nominal rupiah dihitung di server.
            $diskonPersen = round((float) ($request->diskon_persen ?? 0), 2);
            $diskon = round($totalHarga * ($diskonPersen / 100), 2);
            $totalAkhir = max(0, $totalHarga - $diskon);
            $jumlahBayarAwal = round((float) ($request->jumlah_bayar_awal ?? 0), 2);

            if (($request->status_pembayaran ?? null) === 'lunas' && ! $request->filled('jumlah_bayar_awal')) {
                $jumlahBayarAwal = $totalAkhir;
            }

            if ($jumlahBayarAwal > $totalAkhir) {
                throw ValidationException::withMessages([
                    'jumlah_bayar_awal' => 'Jumlah dibayar sekarang tidak boleh lebih besar dari total setelah diskon.',
                ]);
            }

            $statusPembayaran = $jumlahBayarAwal >= $totalAkhir ? 'lunas' : 'belum_lunas';

            // Simpan pembelian
            $pembelian = Pembelian::create([
                'user_id' => Auth::id(),
                'supplier_id' => $request->supplier_id,
                'nomor_faktur' => $nomorFaktur,
                'tanggal_pembelian' => $tanggalPembelian,
                'total_harga' => $totalHarga,
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'status_pembayaran' => $statusPembayaran,
                'status' => 'selesai',
                'keterangan' => $request->keterangan,
            ]);

            // Simpan detail pembelian + tambah stok
            foreach ($items as $item) {
                $barang = $barangs[$item['barang_id']];
                $subtotal = $item['jumlah'] * $barang->harga_beli;

                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'jumlah_pack' => 0,
                    'isi_per_pack' => 1,
                    'harga_beli' => $barang->harga_beli,
                    'subtotal' => $subtotal,
                ]);

                // Tambah stok barang
                $barang->increment('stok', $item['jumlah']);
            }

            // Jika belum lunas, catat sebagai hutang ke supplier (jatuh tempo 1 bulan).
            if ($statusPembayaran === 'belum_lunas') {
                $hutang = HutangSupplier::create([
                    'pembelian_id' => $pembelian->id,
                    'supplier_id' => $pembelian->supplier_id,
                    'total_hutang' => $totalAkhir,
                    'total_dibayar' => $jumlahBayarAwal,
                    'sisa_hutang' => $totalAkhir - $jumlahBayarAwal,
                    'status' => 'belum_lunas',
                    'tanggal_jatuh_tempo' => $tanggalPembelian->copy()->addMonth(),
                ]);

                if ($jumlahBayarAwal > 0) {
                    PembayaranHutang::create([
                        'hutang_id' => $hutang->id,
                        'tanggal_bayar' => $tanggalPembelian,
                        'jumlah_bayar' => $jumlahBayarAwal,
                        'bunga' => 0,
                        'metode_pembayaran' => $request->metode_pembayaran_awal ?? 'tunai',
                        'keterangan' => 'Pembayaran awal saat pembelian',
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('pembelian.show', $pembelian->id)
                ->with('success', "Pembelian {$nomorFaktur} berhasil disimpan. Stok barang telah bertambah.");

        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Detail pembelian.
     */
    public function show(Pembelian $pembelian)
    {
        $pembelian->load(['user', 'supplier', 'detailPembelian.barang', 'hutangSupplier.pembayaranHutang']);

        return view('pembelian.show', compact('pembelian'));
    }

    public function exportPdf(Pembelian $pembelian)
    {
        $pembelian->load(['user', 'supplier', 'detailPembelian.barang', 'hutangSupplier']);
        $filename = 'faktur-pembelian-'.$pembelian->nomor_faktur.'.pdf';

        return Pdf::loadView('pembelian.faktur-pdf', compact('pembelian'))
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    /**
     * Hapus pembelian dan kembalikan/sesuaikan stok.
     */
    public function destroy(Pembelian $pembelian)
    {
        DB::beginTransaction();

        try {
            // 1. Kurangi/kembalikan stok barang yang sudah dibeli
            $pembelian->load('detailPembelian.barang');
            foreach ($pembelian->detailPembelian as $detail) {
                if ($detail->barang) {
                    $detail->barang->decrement('stok', $detail->jumlah);
                }
            }

            // 2. Hapus hutang & riwayat pembayaran hutang jika ada
            if ($pembelian->hutangSupplier) {
                $pembelian->hutangSupplier->pembayaranHutang()->delete();
                $pembelian->hutangSupplier->delete();
            }

            // 3. Hapus detail pembelian
            $pembelian->detailPembelian()->delete();

            // 4. Hapus data pembelian
            $pembelian->delete();

            DB::commit();

            return redirect()
                ->route('pembelian.index')
                ->with('success', "Transaksi pembelian {$pembelian->nomor_faktur} berhasil dihapus/dibatalkan, stok barang telah disesuaikan kembali.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat menghapus transaksi: ' . $e->getMessage());
        }
    }
}
