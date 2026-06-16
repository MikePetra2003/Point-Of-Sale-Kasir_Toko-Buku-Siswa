<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\PiutangPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer as ThermalPrinter;
use Throwable;

class PenjualanController extends Controller
{
    private const MINIMUM_TOTAL_KREDIT = 500000;

    /**
     * Daftar semua transaksi penjualan.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $penjualan = Penjualan::with(['user', 'pelanggan'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('nomor_invoice', 'like', "%{$keyword}%")
                    ->orWhereHas('pelanggan', function ($q) use ($keyword) {
                        $q->where('nama_pelanggan', 'like', "%{$keyword}%");
                    });
            })
            ->latest()
            ->paginate(15);

        return view('penjualan.index', compact('penjualan', 'keyword'));
    }

    /**
     * Tampilkan halaman Kasir POS — form transaksi baru.
     */
    public function create()
    {
        $barangs = Barang::with(['kategori', 'satuan'])
            ->where('stok', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();

        $minimumTotalKredit = self::MINIMUM_TOTAL_KREDIT;

        return view('penjualan.create', compact('barangs', 'pelanggans', 'minimumTotalKredit'));
    }

    /**
     * Simpan transaksi penjualan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'diskon' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,qris,kredit',
            'jumlah_bayar' => 'required_if:metode_pembayaran,tunai|nullable|numeric|min:0',
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

        $diskon = (float) ($request->diskon ?? 0);

        try {
            $result = DB::transaction(function () use ($request, $items, $diskon) {
                $barangIds = $items->pluck('barang_id');
                $barangs = Barang::whereIn('id', $barangIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($barangs->count() !== $barangIds->count()) {
                    throw new \RuntimeException('Barang yang dipilih tidak valid.');
                }

                $totalHarga = 0;
                foreach ($items as $item) {
                    $barang = $barangs[$item['barang_id']];

                    if ($barang->stok < $item['jumlah']) {
                        throw new \RuntimeException("Stok {$barang->nama_barang} tidak mencukupi. Tersisa: {$barang->stok}");
                    }

                    $totalHarga += $item['jumlah'] * $barang->harga_jual;
                }

                if ($diskon > $totalHarga) {
                    throw new \RuntimeException('Diskon tidak boleh lebih besar dari total harga.');
                }

                $totalAkhir = $totalHarga - $diskon;
                $isKredit = $request->metode_pembayaran === 'kredit';
                $jumlahBayar = match ($request->metode_pembayaran) {
                    'qris' => $totalAkhir,
                    'kredit' => 0,
                    default => (float) $request->jumlah_bayar,
                };
                $pelangganId = $request->pelanggan_id;

                if ($request->metode_pembayaran === 'tunai' && $jumlahBayar < $totalAkhir) {
                    throw new \RuntimeException('Pembayaran tunai tidak boleh kurang dari total belanja.');
                }

                if ($isKredit && $totalAkhir < self::MINIMUM_TOTAL_KREDIT) {
                    throw new \RuntimeException('Transaksi kredit hanya tersedia untuk total belanja minimal Rp '.number_format(self::MINIMUM_TOTAL_KREDIT, 0, ',', '.').'.');
                }

                $sisaPiutang = $isKredit ? $totalAkhir : 0;
                $statusPembayaran = $isKredit ? 'sebagian' : 'lunas';

                $tanggalTransaksi = now();
                $invoiceDate = $tanggalTransaksi->format('dm');
                $lastInvoice = Penjualan::where('nomor_invoice', 'like', "TJ-{$invoiceDate}-%")
                    ->lockForUpdate()
                    ->orderBy('nomor_invoice', 'desc')
                    ->first();

                $lastNumber = $lastInvoice ? (int) substr($lastInvoice->nomor_invoice, -3) : 0;
                $nomorInvoice = "TJ-{$invoiceDate}-".str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

                $penjualan = Penjualan::create([
                    'user_id' => Auth::id(),
                    'pelanggan_id' => $pelangganId,
                    'nomor_invoice' => $nomorInvoice,
                    'tanggal_penjualan' => $tanggalTransaksi,
                    'total_harga' => $totalHarga,
                    'diskon' => $diskon,
                    'total_akhir' => $totalAkhir,
                    'status_pembayaran' => $statusPembayaran,
                ]);

                Pembayaran::create([
                    'penjualan_id' => $penjualan->id,
                    'tanggal_pembayaran' => $tanggalTransaksi,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'jumlah_bayar' => $jumlahBayar,
                    'status_pembayaran' => 'berhasil',
                ]);

                $piutang = null;
                if ($isKredit) {
                    $piutang = PiutangPelanggan::create([
                        'penjualan_id' => $penjualan->id,
                        'pelanggan_id' => $pelangganId,
                        'total_piutang' => $totalAkhir,
                        'total_dibayar' => $jumlahBayar,
                        'sisa_piutang' => $sisaPiutang,
                        'status' => 'belum_lunas',
                        'tanggal_jatuh_tempo' => $tanggalTransaksi->copy()->addDays(30),
                        'keterangan' => 'Piutang otomatis dari transaksi '.$nomorInvoice,
                    ]);
                }

                foreach ($items as $item) {
                    $barang = $barangs[$item['barang_id']];
                    $subtotal = $item['jumlah'] * $barang->harga_jual;

                    DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $barang->id,
                        'jumlah' => $item['jumlah'],
                        'harga_jual' => $barang->harga_jual,
                        'subtotal' => $subtotal,
                    ]);

                    $barang->decrement('stok', $item['jumlah']);
                }

                return [
                    'penjualan' => $penjualan,
                    'piutang' => $piutang,
                ];
            });

            $penjualan = $result['penjualan'];
            $piutang = $result['piutang'];

            if ($request->metode_pembayaran === 'kredit' && $piutang) {
                return redirect()
                    ->route('piutang.edit', $piutang->id)
                    ->with('success', "Transaksi {$penjualan->nomor_invoice} berhasil disimpan. Lengkapi data piutang pelanggan.");
            }

            return redirect()
                ->route('penjualan.show', $penjualan->id)
                ->with('success', "Transaksi {$penjualan->nomor_invoice} berhasil disimpan.");
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan detail / struk transaksi.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'pelanggan', 'detailPenjualan.barang', 'pembayaran']);

        $whatsappReceiptUrl = $this->whatsappReceiptUrl($penjualan);

        return view('penjualan.show', compact('penjualan', 'whatsappReceiptUrl'));
    }

    /**
     * Kirim struk transaksi ke printer thermal ESC/POS.
     */
    public function printThermal(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'pelanggan', 'detailPenjualan.barang', 'pembayaran']);

        $printerName = config('printer.receipt_printer_name', 'EP80PLUS');
        $lineWidth = (int) config('printer.receipt_line_width', 48);
        $printer = null;

        try {
            $connector = new WindowsPrintConnector($printerName);
            $printer = new ThermalPrinter($connector);

            $this->writeThermalReceipt($printer, $penjualan, $lineWidth);
            $printer->cut();
            $printer->close();

            return back()->with('success', "Struk {$penjualan->nomor_invoice} berhasil dikirim ke printer {$printerName}.");
        } catch (Throwable $e) {
            if ($printer instanceof ThermalPrinter) {
                try {
                    $printer->close();
                } catch (Throwable) {
                    // Abaikan error saat menutup koneksi printer yang gagal.
                }
            }

            $message = $e->getMessage();

            if (str_contains($message, 'Failed to open stream') || str_contains($message, 'No such file or directory')) {
                $message = "Printer {$printerName} belum bisa diakses sebagai shared printer. Aktifkan Printer Sharing di Windows dengan Share name {$printerName}.";
            }

            return back()->with('error', "Gagal mencetak ke printer {$printerName}: ".$message);
        }
    }

    private function writeThermalReceipt(ThermalPrinter $printer, Penjualan $penjualan, int $lineWidth): void
    {
        $separator = str_repeat('-', $lineWidth);
        $totalBayar = (float) $penjualan->pembayaran->sum('jumlah_bayar');
        $kembalian = max(0, $totalBayar - (float) $penjualan->total_akhir);
        $pembayaran = $penjualan->pembayaran->first();

        $printer->initialize();
        $printer->setJustification(ThermalPrinter::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text("TOKO BUKU SISWA 2\n");
        $printer->text("Jl. Haji Agus Salim No 49\n");
        $printer->setEmphasis(false);
        $printer->text("Struk Penjualan\n");
        $printer->text($separator."\n");

        $printer->setJustification(ThermalPrinter::JUSTIFY_LEFT);
        $printer->text('Invoice   :'.$penjualan->nomor_invoice."\n");
        $printer->text('Tanggal   :'.$penjualan->tanggal_penjualan->format('d/m/Y H:i')."\n");
        $printer->text('Kasir     :'.($penjualan->user->name ?? '-')."\n");
        $printer->text('Pelanggan :'.$penjualan->nama_pelanggan_display."\n");
        $printer->text($separator."\n");

        foreach ($penjualan->detailPenjualan as $detail) {
            foreach ($this->wrapReceiptText($detail->barang->nama_barang ?? '-', $lineWidth) as $line) {
                $printer->text($line."\n");
            }

            $qtyPrice = $detail->jumlah.' x '.$this->rupiah((float) $detail->harga_jual);
            $printer->text($this->receiptRow($qtyPrice, $this->rupiah((float) $detail->subtotal), $lineWidth)."\n");
        }

        $printer->text($separator."\n");
        $printer->text($this->receiptRow('Total Harga', $this->rupiah((float) $penjualan->total_harga), $lineWidth)."\n");

        if ($pembayaran) {
            $metodePembayaran = strtolower($pembayaran->metode_pembayaran);

            $printer->text($this->receiptRow('Metode Pembayaran', strtoupper($pembayaran->metode_pembayaran), $lineWidth)."\n");

            if ($metodePembayaran === 'tunai' && $kembalian > 0) {
                $printer->text($this->receiptRow('Bayar', $this->rupiah($totalBayar), $lineWidth)."\n");
                $printer->text($this->receiptRow('Kembalian', $this->rupiah($kembalian), $lineWidth)."\n");
            }
        }

        $printer->text($separator."\n");
        $printer->setJustification(ThermalPrinter::JUSTIFY_CENTER);
        $printer->text("Terima kasih atas pembelian Anda!\n");
        $printer->feed(3);
    }

    private function receiptRow(string $left, string $right, int $lineWidth): string
    {
        $left = trim($left);
        $right = trim($right);
        $space = $lineWidth - strlen($left) - strlen($right);

        if ($space < 1) {
            return $left."\n".$right;
        }

        return $left.str_repeat(' ', $space).$right;
    }

    private function wrapReceiptText(string $text, int $lineWidth): array
    {
        return explode("\n", wordwrap($text, $lineWidth, "\n", true));
    }

    private function whatsappReceiptUrl(Penjualan $penjualan): ?string
    {
        $number = $this->whatsappNumber($penjualan->pelanggan->no_telepon ?? null);

        if (! $number) {
            return null;
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode($this->whatsappReceiptMessage($penjualan));
    }

    private function whatsappNumber(?string $phone): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $phone);

        if ($number === '') {
            return null;
        }

        if (str_starts_with($number, '0')) {
            return '62'.substr($number, 1);
        }

        if (str_starts_with($number, '8')) {
            return '62'.$number;
        }

        return $number;
    }

    private function whatsappReceiptMessage(Penjualan $penjualan): string
    {
        $lines = [
            'TOKO BUKU SISWA 2',
            'Jl. Haji Agus Salim No 49',
            '',
            'Struk Penjualan',
            '',
            'Invoice    :'.$penjualan->nomor_invoice."",
            'Tanggal    :'.$penjualan->tanggal_penjualan->format('d/m/Y H:i')."",
            'Kasir      :'.($penjualan->user->name ?? '-')."",
            'Pelanggan  :'.$penjualan->nama_pelanggan_display,
            '',
            'Rincian Barang:',
        ];

        foreach ($penjualan->detailPenjualan as $detail) {
            $lines[] = '- '.($detail->barang->nama_barang ?? '-');
            $lines[] = '  '.$detail->jumlah.' x '.$this->rupiah((float) $detail->harga_jual).' = '.$this->rupiah((float) $detail->subtotal);
        }

        $totalBayar = (float) $penjualan->pembayaran->sum('jumlah_bayar');
        $kembalian = max(0, $totalBayar - (float) $penjualan->total_akhir);
        $pembayaran = $penjualan->pembayaran->first();

        $lines = array_merge($lines, [
            '',
            $this->whatsappReceiptRow('Total Harga', $this->rupiah((float) $penjualan->total_harga)),
        ]);

        if ($pembayaran) {
            $lines[] = $this->whatsappReceiptRow('Metode', strtoupper($pembayaran->metode_pembayaran));
            $lines[] = $this->whatsappReceiptRow('Bayar', $this->rupiah($totalBayar));
        }

        if ($kembalian > 0) {
            $lines[] = $this->whatsappReceiptRow('Kembalian', $this->rupiah($kembalian));
        }

        $lines[] = '';
        $lines[] = 'Terima kasih atas pembelian Anda!';

        return implode("\n", $lines);
    }

    private function whatsappReceiptRow(string $label, string $value): string
    {
        return str_pad($label, 23).' : '.$value;
    }

    private function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
