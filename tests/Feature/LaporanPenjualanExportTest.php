<?php

namespace Tests\Feature;

use App\Exports\LaporanPenjualanExport;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class LaporanPenjualanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_laporan_penjualan_menampilkan_export_dan_tidak_menampilkan_cetak_laporan(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('laporan.penjualan'))
            ->assertOk()
            ->assertSee('Export Excel')
            ->assertSee('Export PDF')
            ->assertDontSee('Export CSV')
            ->assertDontSee('Cetak Laporan');
    }

    public function test_laporan_penjualan_bisa_export_xlsx(): void
    {
        $user = User::factory()->owner()->create();
        Penjualan::create([
            'user_id' => $user->id,
            'nomor_invoice' => 'TJ-2405-001',
            'tanggal_penjualan' => '2026-05-24 10:00:00',
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'lunas',
        ]);

        $response = $this->actingAs($user)
            ->get(route('laporan.penjualan.export.xlsx', [
                'tanggal_mulai' => '2026-05-24',
                'tanggal_akhir' => '2026-05-24',
            ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('laporan-penjualan-2026-05-24-2026-05-24.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_laporan_penjualan_export_xlsx_memiliki_judul_periode_dan_lebar_kolom_yang_lebih_lega(): void
    {
        $user = User::factory()->owner()->create();
        Penjualan::create([
            'user_id' => $user->id,
            'nomor_invoice' => 'TJ-2405-001',
            'tanggal_penjualan' => '2026-05-24 10:00:00',
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'lunas',
        ]);

        $temporaryPath = storage_path('app/laravel-excel');
        File::ensureDirectoryExists($temporaryPath);
        config(['excel.temporary_files.local_path' => $temporaryPath]);

        $binary = Excel::raw(
            new LaporanPenjualanExport('2026-05-24', '2026-05-24'),
            ExcelWriter::XLSX
        );

        $temporaryFile = tempnam(sys_get_temp_dir(), 'laporan-penjualan');
        file_put_contents($temporaryFile, $binary);

        $spreadsheet = IOFactory::load($temporaryFile);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('LAPORAN PENJUALAN', $sheet->getCell('A1')->getValue());
        $this->assertSame('Periode 24/05/2026 - 24/05/2026', $sheet->getCell('A2')->getValue());
        $this->assertSame('Invoice', $sheet->getCell('A4')->getValue());
        $this->assertSame('TJ-2405-001', $sheet->getCell('A5')->getValue());
        $this->assertSame(22.0, $sheet->getColumnDimension('A')->getWidth());
        $this->assertSame(20.0, $sheet->getColumnDimension('B')->getWidth());
        $this->assertSame(26.0, $sheet->getColumnDimension('C')->getWidth());
        $this->assertArrayHasKey('A1:G1', $sheet->getMergeCells());
        $this->assertArrayHasKey('A2:G2', $sheet->getMergeCells());

        $spreadsheet->disconnectWorksheets();
        @unlink($temporaryFile);
    }

    public function test_laporan_penjualan_bisa_export_pdf(): void
    {
        $user = User::factory()->owner()->create();
        Penjualan::create([
            'user_id' => $user->id,
            'nomor_invoice' => 'TJ-2405-001',
            'tanggal_penjualan' => '2026-05-24 10:00:00',
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'lunas',
        ]);

        $response = $this->actingAs($user)
            ->get(route('laporan.penjualan.export.pdf', [
                'tanggal_mulai' => '2026-05-24',
                'tanggal_akhir' => '2026-05-24',
            ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('laporan-penjualan-2026-05-24-2026-05-24.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
