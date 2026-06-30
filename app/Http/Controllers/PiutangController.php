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
        $status = $request->input('status', 'belum_lunas');

        $piutang = PiutangPelanggan::with([
            'pelanggan',
            'penjualan.user',
            'penjualan.detailPenjualan.barang',
            'penjualan.detailPenjualan.satuan',
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
            'penjualan.detailPenjualan.satuan',
            'penjualan.pembayaran',
            'pembayaranPiutang',
        ]);

        return view('piutang.show', compact('piutang'));
    }

    /**
     * Kartu piutang berisi ringkasan saldo per pelanggan.
     */
    public function kartu(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->input('status', 'semua');

        $kartuPiutang = PiutangPelanggan::query()
            ->select([
                'pelanggan_id',
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_piutang) as total_piutang'),
                DB::raw('SUM(total_dibayar) as total_dibayar'),
                DB::raw('SUM(sisa_piutang) as sisa_piutang'),
                DB::raw('MAX(tanggal_jatuh_tempo) as jatuh_tempo_terakhir'),
            ])
            ->with('pelanggan')
            ->whereNotNull('pelanggan_id')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('pelanggan', function ($q) use ($keyword) {
                    $q->where('nama_pelanggan', 'like', "%{$keyword}%")
                        ->orWhere('no_id_pelanggan', 'like', "%{$keyword}%")
                        ->orWhere('no_telepon', 'like', "%{$keyword}%");
                });
            })
            ->when($status !== 'semua', function ($query) use ($status) {
                if ($status === 'belum_lunas') {
                    $query->whereIn('status', ['belum_lunas', 'sebagian']);
                } else {
                    $query->where('status', $status);
                }
            })
            ->groupBy('pelanggan_id')
            ->orderByRaw('SUM(sisa_piutang) DESC')
            ->paginate(15);

        $stats = [
            'pelanggan' => PiutangPelanggan::whereNotNull('pelanggan_id')->distinct('pelanggan_id')->count('pelanggan_id'),
            'total_piutang' => PiutangPelanggan::whereNotNull('pelanggan_id')->sum('total_piutang'),
            'total_dibayar' => PiutangPelanggan::whereNotNull('pelanggan_id')->sum('total_dibayar'),
            'sisa_piutang' => PiutangPelanggan::whereNotNull('pelanggan_id')->sum('sisa_piutang'),
        ];

        return view('piutang.kartu.index', compact('kartuPiutang', 'keyword', 'status', 'stats'));
    }

    /**
     * Detail mutasi kartu piutang per pelanggan dengan saldo berjalan.
     */
    public function kartuDetail(Pelanggan $pelanggan)
    {
        $piutangList = PiutangPelanggan::with([
            'penjualan',
            'pembayaranPiutang' => fn ($query) => $query->orderBy('tanggal_bayar')->orderBy('id'),
        ])
            ->where('pelanggan_id', $pelanggan->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        abort_if($piutangList->isEmpty(), 404);

        $mutasi = collect();

        foreach ($piutangList as $piutang) {
            $tanggalPiutang = $piutang->penjualan?->tanggal_penjualan ?? $piutang->created_at ?? now();
            $bukti = $piutang->penjualan?->nomor_invoice ?? 'PIUTANG-'.$piutang->id;

            $mutasi->push([
                'tanggal' => $tanggalPiutang,
                'urutan' => $tanggalPiutang->format('YmdHis').'000'.$piutang->id,
                'bukti' => $bukti,
                'keterangan' => 'Piutang dari transaksi penjualan',
                'piutang' => (float) $piutang->total_piutang,
                'pembayaran' => 0,
                'saldo' => 0,
                'piutang_id' => $piutang->id,
            ]);

            foreach ($piutang->pembayaranPiutang as $bayar) {
                $mutasi->push([
                    'tanggal' => $bayar->tanggal_bayar,
                    'urutan' => $bayar->tanggal_bayar->format('YmdHis').'100'.$bayar->id,
                    'bukti' => $bukti,
                    'keterangan' => trim('Pembayaran piutang '.strtoupper($bayar->metode_pembayaran).' '.($bayar->keterangan ? '- '.$bayar->keterangan : '')),
                    'piutang' => 0,
                    'pembayaran' => (float) $bayar->jumlah_bayar,
                    'saldo' => 0,
                    'piutang_id' => $piutang->id,
                ]);
            }
        }

        $saldo = 0;
        $mutasi = $mutasi
            ->sortBy('urutan')
            ->values()
            ->map(function (array $item) use (&$saldo) {
                $saldo += $item['piutang'] - $item['pembayaran'];
                $item['saldo'] = max(0, $saldo);

                return $item;
            });

        $summary = [
            'jumlah_transaksi' => $piutangList->count(),
            'total_piutang' => $piutangList->sum('total_piutang'),
            'total_dibayar' => $piutangList->sum('total_dibayar'),
            'sisa_piutang' => $piutangList->sum('sisa_piutang'),
        ];

        return view('piutang.kartu.show', compact('pelanggan', 'piutangList', 'mutasi', 'summary'));
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
        abort_unless($piutang->pelanggan_id && $piutang->pelanggan, 404);

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
            'no_telepon' => 'required|string|max:20',
            'tanggal_jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string|max:100',
            'jumlah_bayar_awal' => 'required|numeric|min:1',
            'metode_pembayaran_awal' => 'required|in:tunai,qris,transfer',
        ]);

        if (! $piutang->pelanggan) {
            return back()
                ->withInput()
                ->with('error', 'Piutang kredit harus terhubung ke pelanggan terdaftar.');
        }

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

            $piutang->pelanggan->update([
                'no_telepon' => $validated['no_telepon'],
            ]);

            PembayaranPiutang::create([
                'piutang_id' => $piutang->id,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $jumlahBayarAwal,
                'metode_pembayaran' => $validated['metode_pembayaran_awal'],
                'keterangan' => 'Pembayaran awal saat membuat piutang kredit',
            ]);

            $piutang->update([
                'pelanggan_id' => $piutang->pelanggan->id,
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
