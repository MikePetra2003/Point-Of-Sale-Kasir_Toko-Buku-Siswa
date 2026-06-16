<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\PembayaranPiutang;
use App\Models\Penjualan;
use App\Models\PiutangPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    /**
     * Daftar semua piutang pelanggan.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->status;

        $piutang = PiutangPelanggan::with([
            'pelanggan',
            'penjualan.user',
            'penjualan.detailPenjualan.barang',
            'penjualan.pembayaran',
        ])
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('pelanggan', function ($q) use ($keyword) {
                    $q->where('nama_pelanggan', 'like', "%{$keyword}%");
                })->orWhereHas('penjualan', function ($q) use ($keyword) {
                    $q->where('nomor_invoice', 'like', "%{$keyword}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status === 'belum_lunas') {
                    $query->whereIn('status', ['belum_lunas', 'sebagian']);
                } else {
                    $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(15);

        return view('piutang.index', compact('piutang', 'keyword', 'status'));
    }

    /**
     * Detail piutang beserta riwayat pembayaran cicilan.
     */
    public function show(PiutangPelanggan $piutang)
    {
        $piutang->load([
            'pelanggan',
            'penjualan.user',
            'penjualan.detailPenjualan.barang',
            'penjualan.pembayaran',
            'pembayaranPiutang',
        ]);

        return view('piutang.show', compact('piutang'));
    }

    /**
     * Form pelengkap piutang hasil transaksi kredit kasir.
     */
    public function edit(PiutangPelanggan $piutang)
    {
        $piutang->load([
            'pelanggan',
            'penjualan.user',
            'penjualan.pembayaran',
            'pembayaranPiutang',
        ]);

        abort_unless($this->isPiutangKredit($piutang), 404);
        abort_if($piutang->pembayaranPiutang->isNotEmpty(), 404);

        return view('piutang.edit', compact('piutang'));
    }

    /**
     * Simpan data pelengkap piutang hasil transaksi kredit.
     */
    public function update(Request $request, PiutangPelanggan $piutang)
    {
        $piutang->load([
            'pelanggan',
            'penjualan.pembayaran',
            'pembayaranPiutang',
        ]);

        abort_unless($this->isPiutangKredit($piutang), 404);

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:50',
            'no_telepon' => 'required|string|max:20',
            'tanggal_jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string|max:100',
            'jumlah_bayar_awal' => 'required|numeric|min:1',
            'metode_pembayaran_awal' => 'required|in:tunai,qris,transfer',
        ]);

        if ($piutang->pembayaranPiutang->isNotEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Data piutang kredit sudah memiliki pembayaran awal.');
        }

        if ((float) $validated['jumlah_bayar_awal'] > (float) $piutang->total_piutang) {
            return back()
                ->withInput()
                ->with('error', 'Bayar awal tidak boleh melebihi total piutang.');
        }

        DB::transaction(function () use ($piutang, $validated) {
            $jumlahBayarAwal = (float) $validated['jumlah_bayar_awal'];
            $sisaPiutang = max(0, (float) $piutang->total_piutang - $jumlahBayarAwal);
            $statusPiutang = $sisaPiutang <= 0 ? 'lunas' : 'sebagian';

            $pelanggan = $piutang->pelanggan;
            $pelangganData = [
                'nama_pelanggan' => trim($validated['nama_pelanggan']),
                'no_telepon' => $validated['no_telepon'],
            ];

            if ($pelanggan) {
                $pelanggan->update($pelangganData);
            } else {
                $pelanggan = Pelanggan::create($pelangganData);

                $piutang->penjualan?->update([
                    'pelanggan_id' => $pelanggan->id,
                ]);
            }

            PembayaranPiutang::create([
                'piutang_id' => $piutang->id,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $jumlahBayarAwal,
                'metode_pembayaran' => $validated['metode_pembayaran_awal'],
                'keterangan' => 'Pembayaran awal saat membuat piutang kredit',
            ]);

            $piutang->update([
                'pelanggan_id' => $pelanggan->id,
                'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                'keterangan' => $validated['keterangan'] ?? null,
                'total_dibayar' => $jumlahBayarAwal,
                'sisa_piutang' => $sisaPiutang,
                'status' => $statusPiutang,
            ]);

            $piutang->penjualan?->pembayaran()->first()?->update([
                'jumlah_bayar' => $jumlahBayarAwal,
            ]);

            if ($statusPiutang === 'lunas') {
                $piutang->penjualan?->update(['status_pembayaran' => 'lunas']);
            }
        });

        return redirect()
            ->route('piutang.show', $piutang->id)
            ->with('success', 'Data piutang pelanggan berhasil dilengkapi.');
    }

    /**
     * Simpan pembayaran cicilan piutang.
     */
    public function bayar(Request $request, PiutangPelanggan $piutang)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,qris,transfer',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Validasi jumlah bayar tidak melebihi sisa piutang
        if ($request->jumlah_bayar > $piutang->sisa_piutang) {
            return back()->with('error', 'Jumlah bayar melebihi sisa piutang (Rp '.number_format($piutang->sisa_piutang, 0, ',', '.').')');
        }

        DB::beginTransaction();

        try {
            // Simpan pembayaran cicilan
            PembayaranPiutang::create([
                'piutang_id' => $piutang->id,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
            ]);

            // Update piutang
            $totalDibayar = $piutang->total_dibayar + $request->jumlah_bayar;
            $sisaPiutang = $piutang->total_piutang - $totalDibayar;

            $status = 'sebagian';
            if ($sisaPiutang <= 0) {
                $status = 'lunas';
                $sisaPiutang = 0;
            }

            $piutang->update([
                'total_dibayar' => $totalDibayar,
                'sisa_piutang' => $sisaPiutang,
                'status' => $status,
            ]);

            // Update status pembayaran di penjualan juga
            if ($status === 'lunas') {
                $piutang->penjualan->update(['status_pembayaran' => 'lunas']);
            }

            DB::commit();

            return back()->with('success', 'Pembayaran cicilan sebesar Rp '.number_format($request->jumlah_bayar, 0, ',', '.').' berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan form edit general piutang.
     */
    public function editGeneral(PiutangPelanggan $piutang)
    {
        $piutang->load(['pelanggan', 'penjualan']);

        return view('piutang.edit-general', compact('piutang'));
    }

    /**
     * Perbarui data general piutang.
     */
    public function updateGeneral(Request $request, PiutangPelanggan $piutang)
    {
        $request->validate([
            'total_piutang' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $totalPiutang = (float) $request->total_piutang;
        $totalDibayar = (float) $piutang->total_dibayar;
        $sisaPiutang = max(0, $totalPiutang - $totalDibayar);

        $status = 'belum_lunas';
        if ($sisaPiutang <= 0) {
            $status = 'lunas';
            $sisaPiutang = 0;
        } elseif ($totalDibayar > 0) {
            $status = 'sebagian';
        }

        DB::beginTransaction();
        try {
            $piutang->update([
                'total_piutang' => $totalPiutang,
                'sisa_piutang' => $sisaPiutang,
                'status' => $status,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'keterangan' => $request->keterangan,
            ]);

            // Synchronize status_pembayaran in Penjualan table if it exists
            if ($piutang->penjualan) {
                $piutang->penjualan->update([
                    'status_pembayaran' => $status === 'lunas' ? 'lunas' : ($status === 'sebagian' ? 'sebagian' : 'belum_lunas'),
                ]);
            }

            DB::commit();

            return redirect()->route('piutang.show', $piutang->id)
                ->with('success', 'Data piutang pelanggan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Hapus data piutang.
     */
    public function destroy(PiutangPelanggan $piutang)
    {
        DB::beginTransaction();
        try {
            $piutang->delete();

            DB::commit();

            return redirect()->route('piutang.index')
                ->with('success', 'Data piutang pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    private function isPiutangKredit(PiutangPelanggan $piutang): bool
    {
        return $piutang->penjualan?->pembayaran->first()?->metode_pembayaran === 'kredit';
    }
}
