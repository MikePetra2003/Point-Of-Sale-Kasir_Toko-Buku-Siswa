<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PenjualanQrisTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembayaran_qris_menggunakan_total_akhir_dari_sistem(): void
    {
        $user = User::factory()->create();

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

        $barang = Barang::create([
            'kode_barang' => 'BK-QRIS-001',
            'nama_barang' => 'Buku QRIS',
            'kategori_id' => $kategoriId,
            'satuan_id' => $satuanId,
            'harga_beli' => 25000,
            'harga_jual' => 50000,
            'stok' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ],
            ],
            'diskon' => 10000,
            'metode_pembayaran' => 'qris',
            'jumlah_bayar' => 0,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('penjualan', [
            'total_harga' => 100000,
            'diskon' => 10000,
            'total_akhir' => 90000,
            'status_pembayaran' => 'lunas',
        ]);

        $this->assertDatabaseHas('pembayaran', [
            'metode_pembayaran' => 'qris',
            'jumlah_bayar' => 90000,
            'status_pembayaran' => 'berhasil',
        ]);

        $this->assertDatabaseMissing('piutang_pelanggan', [
            'total_piutang' => 90000,
        ]);
    }
}
