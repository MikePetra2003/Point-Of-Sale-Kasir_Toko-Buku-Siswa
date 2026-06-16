<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PenjualanInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_invoice_baru_memakai_format_tj_tanggal_bulan_dan_nomor_urut(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 21, 10, 15, 0, 'Asia/Jakarta'));

        $user = User::factory()->create();
        $barang = $this->buatBarang();

        $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 1,
                ],
            ],
            'diskon' => 0,
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 50000,
        ]);

        $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 1,
                ],
            ],
            'diskon' => 0,
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 50000,
        ]);

        $this->assertDatabaseHas('penjualan', [
            'nomor_invoice' => 'TJ-2105-001',
            'tanggal_penjualan' => '2026-05-21 10:15:00',
        ]);
        $this->assertDatabaseHas('penjualan', [
            'nomor_invoice' => 'TJ-2105-002',
        ]);
    }

    private function buatBarang(): Barang
    {
        $kategoriId = DB::table('kategori')->insertGetId([
            'nama_kategori' => 'Buku',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $satuanId = DB::table('satuan')->insertGetId([
            'nama_satuan' => 'Pcs',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Barang::create([
            'kode_barang' => 'BK-TJ',
            'nama_barang' => 'Buku Invoice',
            'kategori_id' => $kategoriId,
            'satuan_id' => $satuanId,
            'harga_beli' => 30000,
            'harga_jual' => 50000,
            'stok' => 10,
        ]);
    }
}
