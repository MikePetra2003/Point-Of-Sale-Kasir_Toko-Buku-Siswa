<?php

namespace App\Http\Controllers;

use App\Models\HutangSupplier;
use App\Models\PembayaranHutang;
use App\Models\Supplier;
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
        $status = $request->input('status', 'belum_lunas');

        $hutang = HutangSupplier::with([
            'supplier',
            'pembelian.supplier',
            'pembelian.user',
            'pembelian.hutangSupplier',
            'pembelian.detailPembelian.barang',
            'pembelian.detailPembelian.satuan',
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
            'belum_lunas' => HutangSupplier::where('status', 'belum_lunas')->count(),
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
            'pembelian.detailPembelian.satuan',
            'pembayaranHutang',
        ]);

        return view('hutang.show', compact('hutang'));
    }

    /**
     * Kartu hutang berisi ringkasan saldo per supplier.
     */
    public function kartu(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->input('status', 'semua');

        $kartuHutang = HutangSupplier::query()
            ->select([
                'supplier_id',
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_hutang) as total_hutang'),
                DB::raw('SUM(total_dibayar) as total_dibayar'),
                DB::raw('SUM(sisa_hutang) as sisa_hutang'),
                DB::raw('MAX(tanggal_jatuh_tempo) as jatuh_tempo_terakhir'),
            ])
            ->with('supplier')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('supplier', function ($q) use ($keyword) {
                    $q->where('nama_supplier', 'like', "%{$keyword}%")
                        ->orWhere('no_telepon', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($status !== 'semua', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->groupBy('supplier_id')
            ->orderByRaw('SUM(sisa_hutang) DESC')
            ->paginate(15);

        $stats = [
            'supplier' => HutangSupplier::distinct('supplier_id')->count('supplier_id'),
            'total_hutang' => HutangSupplier::sum('total_hutang'),
            'total_dibayar' => HutangSupplier::sum('total_dibayar'),
            'sisa_hutang' => HutangSupplier::sum('sisa_hutang'),
        ];

        return view('hutang.kartu.index', compact('kartuHutang', 'keyword', 'status', 'stats'));
    }

    /**
     * Detail mutasi kartu hutang per supplier dengan saldo berjalan.
     */
    public function kartuDetail(Supplier $supplier)
    {
        $hutangList = HutangSupplier::with([
            'pembelian',
            'pembayaranHutang' => fn ($query) => $query->orderBy('tanggal_bayar')->orderBy('id'),
        ])
            ->where('supplier_id', $supplier->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        abort_if($hutangList->isEmpty(), 404);

        $mutasi = collect();

        foreach ($hutangList as $hutang) {
            $tanggalHutang = $hutang->pembelian?->tanggal_pembelian ?? $hutang->created_at ?? now();
            $bukti = $hutang->pembelian?->nomor_faktur ?? 'HUTANG-'.$hutang->id;

            $mutasi->push([
                'tanggal' => $tanggalHutang,
                'urutan' => $tanggalHutang->format('YmdHis').'000'.$hutang->id,
                'bukti' => $bukti,
                'keterangan' => 'Hutang dari transaksi pembelian',
                'hutang' => (float) $hutang->total_hutang,
                'pembayaran' => 0,
                'bunga' => 0,
                'saldo' => 0,
                'hutang_id' => $hutang->id,
            ]);

            foreach ($hutang->pembayaranHutang as $bayar) {
                $bunga = (float) $bayar->bunga;
                $pembayaranPokok = max(0, (float) $bayar->jumlah_bayar - $bunga);

                $mutasi->push([
                    'tanggal' => $bayar->tanggal_bayar,
                    'urutan' => $bayar->tanggal_bayar->format('YmdHis').'100'.$bayar->id,
                    'bukti' => $bukti,
                    'keterangan' => trim('Pembayaran hutang '.strtoupper($bayar->metode_pembayaran).' '.($bayar->keterangan ? '- '.$bayar->keterangan : '')),
                    'hutang' => 0,
                    'pembayaran' => $pembayaranPokok,
                    'bunga' => $bunga,
                    'saldo' => 0,
                    'hutang_id' => $hutang->id,
                ]);
            }
        }

        $saldo = 0;
        $mutasi = $mutasi
            ->sortBy('urutan')
            ->values()
            ->map(function (array $item) use (&$saldo) {
                $saldo += $item['hutang'] - $item['pembayaran'];
                $item['saldo'] = max(0, $saldo);

                return $item;
            });

        $summary = [
            'jumlah_transaksi' => $hutangList->count(),
            'total_hutang' => $hutangList->sum('total_hutang'),
            'total_dibayar' => $hutangList->sum('total_dibayar'),
            'sisa_hutang' => $hutangList->sum('sisa_hutang'),
        ];

        return view('hutang.kartu.show', compact('supplier', 'hutangList', 'mutasi', 'summary'));
    }

    /**
     * Simpan pembayaran hutang (cicilan atau pelunasan).
     */
    public function bayar(Request $request, HutangSupplier $hutang)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,qris,transfer',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($hutang->status === 'lunas') {
            return back()->with('error', 'Hutang ini sudah lunas.');
        }

        $sisaHutang = (float) $hutang->sisa_hutang;
        $maxBayar = (float) $hutang->total_harus_bayar;

        if ((float) $request->jumlah_bayar > $maxBayar) {
            return back()->with('error', 'Jumlah bayar melebihi sisa hutang (Rp '.number_format($maxBayar, 0, ',', '.').')');
        }

        DB::beginTransaction();

        try {
            $jumlahBayar = (float) $request->jumlah_bayar;
            $principalPaid = min($jumlahBayar, $sisaHutang);
            $bungaDibayar = max(0, $jumlahBayar - $sisaHutang);

            PembayaranHutang::create([
                'hutang_id' => $hutang->id,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $jumlahBayar,
                'bunga' => $bungaDibayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
            ]);

            $sisaHutangBaru = max(0, $sisaHutang - $principalPaid);
            $status = $sisaHutangBaru <= 0 ? 'lunas' : 'belum_lunas';

            $hutang->update([
                'total_dibayar' => (float) $hutang->total_dibayar + $principalPaid,
                'sisa_hutang' => $sisaHutangBaru,
                'status' => $status,
            ]);

            if ($status === 'lunas') {
                $hutang->pembelian?->update(['status_pembayaran' => 'lunas']);
            }

            DB::commit();

            return back()->with('success', 'Pembayaran hutang sebesar Rp '.number_format($jumlahBayar, 0, ',', '.').
                ($bungaDibayar > 0 ? ' (termasuk bunga keterlambatan Rp '.number_format($bungaDibayar, 0, ',', '.').')' : '').
                ' berhasil dicatat.');

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
                    'status_pembayaran' => $status,
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
