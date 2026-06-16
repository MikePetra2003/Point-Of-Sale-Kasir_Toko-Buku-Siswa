<?php

namespace App\Http\Controllers;

use App\Models\HutangSupplier;
use App\Models\PembayaranHutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HutangController extends Controller
{
    /**
     * Halaman pantau hutang ke supplier.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->status;

        $hutang = HutangSupplier::with([
            'supplier',
            'pembelian.supplier',
            'pembelian.user',
            'pembelian.hutangSupplier',
            'pembelian.detailPembelian.barang',
            'pembayaranHutang',
        ])
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('supplier', function ($q) use ($keyword) {
                    $q->where('nama_supplier', 'like', "%{$keyword}%");
                })->orWhereHas('pembelian', function ($q) use ($keyword) {
                    $q->where('nomor_faktur', 'like', "%{$keyword}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status === 'terlambat') {
                    $query->where('status', 'belum_lunas')
                        ->whereDate('tanggal_jatuh_tempo', '<=', today());
                } else {
                    $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(15);

        $stats = [
            'aktif' => HutangSupplier::where('status', 'belum_lunas')->count(),
            'nilai' => HutangSupplier::where('status', 'belum_lunas')->sum('sisa_hutang'),
            'terlambat' => HutangSupplier::where('status', 'belum_lunas')
                ->whereDate('tanggal_jatuh_tempo', '<=', today())->count(),
            'lunas' => HutangSupplier::where('status', 'lunas')->count(),
        ];

        return view('hutang.index', compact('hutang', 'keyword', 'status', 'stats'));
    }

    /**
     * Detail hutang ke supplier.
     */
    public function show(HutangSupplier $hutang)
    {
        $hutang->load([
            'supplier',
            'pembelian.supplier',
            'pembelian.user',
            'pembelian.hutangSupplier',
            'pembelian.detailPembelian.barang',
            'pembayaranHutang',
        ]);

        return view('hutang.show', compact('hutang'));
    }

    /**
     * Lunasi hutang (sisa hutang + bunga keterlambatan bila ada).
     */
    public function bayar(Request $request, HutangSupplier $hutang)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:tunai,qris,transfer',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($hutang->status === 'lunas') {
            return back()->with('error', 'Hutang ini sudah lunas.');
        }

        DB::beginTransaction();

        try {
            $bunga = $hutang->bunga;
            $jumlahBayar = (float) $hutang->sisa_hutang + $bunga;

            PembayaranHutang::create([
                'hutang_id' => $hutang->id,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $jumlahBayar,
                'bunga' => $bunga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
            ]);

            $hutang->update([
                'total_dibayar' => $hutang->total_hutang,
                'sisa_hutang' => 0,
                'status' => 'lunas',
            ]);

            $hutang->pembelian?->update(['status_pembayaran' => 'lunas']);

            DB::commit();

            return back()->with('success', 'Hutang berhasil dilunasi sebesar Rp '.number_format($jumlahBayar, 0, ',', '.').
                ($bunga > 0 ? ' (termasuk bunga keterlambatan Rp '.number_format($bunga, 0, ',', '.').')' : '').'.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan form edit hutang.
     */
    public function edit(HutangSupplier $hutang)
    {
        $hutang->load(['supplier', 'pembelian']);

        return view('hutang.edit', compact('hutang'));
    }

    /**
     * Perbarui data hutang.
     */
    public function update(Request $request, HutangSupplier $hutang)
    {
        $request->validate([
            'total_hutang' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $totalHutang = (float) $request->total_hutang;
        $totalDibayar = (float) $hutang->total_dibayar;
        $sisaHutang = max(0, $totalHutang - $totalDibayar);

        $status = 'belum_lunas';
        if ($sisaHutang <= 0) {
            $status = 'lunas';
            $sisaHutang = 0;
        }

        DB::beginTransaction();
        try {
            $hutang->update([
                'total_hutang' => $totalHutang,
                'sisa_hutang' => $sisaHutang,
                'status' => $status,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'keterangan' => $request->keterangan,
            ]);

            // Synchronize status_pembayaran in Pembelian table if it exists
            if ($hutang->pembelian) {
                $hutang->pembelian->update([
                    'status_pembayaran' => $status === 'lunas' ? 'lunas' : 'belum_lunas',
                ]);
            }

            DB::commit();

            return redirect()->route('hutang.show', $hutang->id)
                ->with('success', 'Data hutang supplier berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Hapus data hutang.
     */
    public function destroy(HutangSupplier $hutang)
    {
        DB::beginTransaction();
        try {
            $hutang->delete();

            DB::commit();

            return redirect()->route('hutang.index')
                ->with('success', 'Data hutang supplier berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
