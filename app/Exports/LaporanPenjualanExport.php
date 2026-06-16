<?php

namespace App\Exports;

use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanPenjualanExport implements FromCollection, WithColumnWidths, WithCustomStartCell, WithEvents, WithHeadings
{
    public function __construct(
        private string $tanggalMulai,
        private string $tanggalAkhir
    ) {}

    public function collection()
    {
        return Penjualan::with(['pelanggan', 'user'])
            ->whereDate('tanggal_penjualan', '>=', $this->tanggalMulai)
            ->whereDate('tanggal_penjualan', '<=', $this->tanggalAkhir)
            ->get()
            ->map(function ($item) {
                return [
                    'invoice' => $item->nomor_invoice,
                    'tanggal' => $item->tanggal_penjualan->format('d/m/Y H:i'),
                    'pelanggan' => $item->nama_pelanggan_display,
                    'kasir' => $item->user->name ?? '-',
                    'total_harga' => $item->total_harga,
                    'total_akhir' => $item->total_akhir,
                    'status' => $item->status_pembayaran,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Invoice',
            'Tanggal',
            'Pelanggan',
            'Kasir',
            'Total Harga',
            'Total Akhir',
            'Status',
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 20,
            'C' => 26,
            'D' => 22,
            'E' => 16,
            'F' => 16,
            'G' => 18,
        ];
    }

    public function registerEvents(): array
    {
        $periode = sprintf(
            'Periode %s - %s',
            Carbon::parse($this->tanggalMulai)->format('d/m/Y'),
            Carbon::parse($this->tanggalAkhir)->format('d/m/Y')
        );

        return [
            AfterSheet::class => function (AfterSheet $event) use ($periode) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max($sheet->getHighestRow(), 4);

                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
                $sheet->setCellValue('A2', $periode);
                $sheet->freezePane('A5');

                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:G2')->applyFromArray([
                    'font' => [
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A4:G4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'D9EAF7'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A4:G{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(22);
            },
        ];
    }
}
