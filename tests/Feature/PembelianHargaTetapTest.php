<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PembelianHargaTetapTest extends TestCase
{
    use RefreshDatabase;

    private function buatBarangPembelian(int $hargaBeli = 30000, int $stok = 5): array
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Buku',
            'alamat' => 'Jl. Buku No. 1',
            'no_telepon' => '08123456789',
        ]);

        $kategoriId = DB::table('kategori')->insertGetId([
            'nama_kategori' => 'Buku',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $satuanId = DB::table('satuan')->insertGetId([
            'nama_satuan' => 'pcs',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $barang = Barang::create([
            'kode_barang' => 'BK-FIX-001',
            'nama_barang' => 'Buku Harga Tetap',
            'kategori_id' => $kategoriId,
            'satuan_id' => $satuanId,
            'supplier_id' => $supplier->id,
            'harga_beli' => $hargaBeli,
            'harga_jual' => 50000,
            'stok' => $stok,
        ]);

        return [$supplier, $barang];
    }

    public function test_harga_pembelian_diambil_dari_master_barang_bukan_request(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian();

        $response = $this->actingAs($user)->post(route('pembelian.store'), [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 24,
                    'harga_beli' => 1,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pembelian', [
            'supplier_id' => $supplier->id,
            'total_harga' => 720000,
        ]);

        $this->assertDatabaseHas('detail_pembelian', [
            'barang_id' => $barang->id,
            'jumlah_pack' => 0,
            'isi_per_pack' => 1,
            'jumlah' => 24,
            'harga_beli' => 30000,
            'subtotal' => 720000,
        ]);

        $this->assertDatabaseHas('barang', [
            'id' => $barang->id,
            'stok' => 29,
        ]);
    }

    public function test_pembelian_menolak_barang_dari_supplier_lain(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(stok: 5);
        $supplierLain = Supplier::create([
            'nama_supplier' => 'Supplier Lain',
            'alamat' => 'Jl. Lain No. 1',
            'no_telepon' => '081111111111',
        ]);
        $barangLain = Barang::create([
            'kode_barang' => 'BK-LAIN-001',
            'nama_barang' => 'Buku Supplier Lain',
            'kategori_id' => $barang->kategori_id,
            'satuan_id' => $barang->satuan_id,
            'supplier_id' => $supplierLain->id,
            'harga_beli' => 40000,
            'harga_jual' => 60000,
            'stok' => 7,
        ]);

        $response = $this->actingAs($user)
            ->from(route('pembelian.create'))
            ->post(route('pembelian.store'), [
                'supplier_id' => $supplier->id,
                'items' => [
                    [
                        'barang_id' => $barangLain->id,
                        'jumlah' => 10,
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('pembelian.create'))
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('pembelian', 0);
        $this->assertDatabaseCount('detail_pembelian', 0);
        $this->assertDatabaseCount('hutang_supplier', 0);
        $this->assertDatabaseCount('pembayaran_hutang', 0);
        $this->assertDatabaseHas('barang', [
            'id' => $barangLain->id,
            'stok' => 7,
        ]);
    }

    public function test_pembelian_menolak_barang_tanpa_supplier(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(stok: 5);
        $barang->update(['supplier_id' => null]);

        $response = $this->actingAs($user)
            ->from(route('pembelian.create'))
            ->post(route('pembelian.store'), [
                'supplier_id' => $supplier->id,
                'items' => [
                    [
                        'barang_id' => $barang->id,
                        'jumlah' => 10,
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('pembelian.create'))
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('pembelian', 0);
        $this->assertDatabaseCount('detail_pembelian', 0);
        $this->assertDatabaseCount('hutang_supplier', 0);
        $this->assertDatabaseCount('pembayaran_hutang', 0);
        $this->assertDatabaseHas('barang', [
            'id' => $barang->id,
            'stok' => 5,
        ]);
    }

    public function test_halaman_detail_pembelian_menampilkan_export_faktur_pdf(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian();

        $this->actingAs($user)->post(route('pembelian.store'), [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 12,
                ],
            ],
        ]);

        $pembelian = Pembelian::firstOrFail();

        $this->actingAs($user)
            ->get(route('pembelian.show', $pembelian))
            ->assertOk()
            ->assertSee('Export Faktur')
            ->assertDontSee('Cetak Faktur');
    }

    public function test_faktur_pembelian_bisa_export_pdf(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian();

        $this->actingAs($user)->post(route('pembelian.store'), [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 12,
                ],
            ],
        ]);

        $pembelian = Pembelian::firstOrFail();

        $response = $this->actingAs($user)
            ->get(route('pembelian.export.pdf', $pembelian));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('faktur-pembelian-'.$pembelian->nomor_faktur.'.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_diskon_pembelian_dihitung_dari_persen_dan_hutang_memakai_total_akhir(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(hargaBeli: 50000);

        $response = $this->actingAs($user)->post(route('pembelian.store'), [
            'supplier_id' => $supplier->id,
            'diskon_persen' => 10,
            'diskon' => 99999,
            'status_pembayaran' => 'belum_lunas',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 24,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pembelian', [
            'supplier_id' => $supplier->id,
            'total_harga' => 1200000,
            'diskon_persen' => 10,
            'diskon' => 120000,
            'status_pembayaran' => 'belum_lunas',
        ]);

        $pembelianId = DB::table('pembelian')->where('supplier_id', $supplier->id)->value('id');

        $this->assertDatabaseHas('hutang_supplier', [
            'pembelian_id' => $pembelianId,
            'supplier_id' => $supplier->id,
            'total_hutang' => 1080000,
            'total_dibayar' => 0,
            'sisa_hutang' => 1080000,
            'status' => 'belum_lunas',
        ]);
    }

    public function test_bayar_awal_pembelian_supplier_masuk_ke_hutang_dan_riwayat_pembayaran(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(hargaBeli: 50000);

        $response = $this->actingAs($user)->post(route('pembelian.store'), [
            'supplier_id' => $supplier->id,
            'diskon_persen' => 20,
            'jumlah_bayar_awal' => 300000,
            'metode_pembayaran_awal' => 'tunai',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 24,
                ],
            ],
        ]);

        $response->assertRedirect();

        $pembelianId = DB::table('pembelian')->where('supplier_id', $supplier->id)->value('id');
        $hutangId = DB::table('hutang_supplier')->where('pembelian_id', $pembelianId)->value('id');

        $this->assertDatabaseHas('pembelian', [
            'id' => $pembelianId,
            'total_harga' => 1200000,
            'diskon_persen' => 20,
            'diskon' => 240000,
            'status_pembayaran' => 'belum_lunas',
        ]);

        $this->assertDatabaseHas('hutang_supplier', [
            'id' => $hutangId,
            'pembelian_id' => $pembelianId,
            'supplier_id' => $supplier->id,
            'total_hutang' => 960000,
            'total_dibayar' => 300000,
            'sisa_hutang' => 660000,
            'status' => 'belum_lunas',
        ]);

        $this->assertDatabaseHas('pembayaran_hutang', [
            'hutang_id' => $hutangId,
            'jumlah_bayar' => 300000,
            'bunga' => 0,
            'metode_pembayaran' => 'tunai',
            'keterangan' => 'Pembayaran awal saat pembelian',
        ]);
    }

    public function test_bayar_awal_penuh_membuat_pembelian_lunas_tanpa_hutang_supplier(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(hargaBeli: 50000);

        $response = $this->actingAs($user)->post(route('pembelian.store'), [
            'supplier_id' => $supplier->id,
            'diskon_persen' => 20,
            'jumlah_bayar_awal' => 960000,
            'metode_pembayaran_awal' => 'transfer',
            'status_pembayaran' => 'belum_lunas',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 24,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pembelian', [
            'supplier_id' => $supplier->id,
            'total_harga' => 1200000,
            'diskon' => 240000,
            'status_pembayaran' => 'lunas',
        ]);

        $this->assertDatabaseCount('hutang_supplier', 0);
        $this->assertDatabaseCount('pembayaran_hutang', 0);
    }

    public function test_bayar_awal_tidak_boleh_melebihi_total_setelah_diskon(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(hargaBeli: 50000);

        $response = $this->actingAs($user)
            ->from(route('pembelian.create'))
            ->post(route('pembelian.store'), [
                'supplier_id' => $supplier->id,
                'diskon_persen' => 20,
                'jumlah_bayar_awal' => 960001,
                'items' => [
                    [
                        'barang_id' => $barang->id,
                        'jumlah' => 24,
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('pembelian.create'))
            ->assertSessionHasErrors('jumlah_bayar_awal');

        $this->assertDatabaseCount('pembelian', 0);
        $this->assertDatabaseCount('hutang_supplier', 0);
        $this->assertDatabaseCount('pembayaran_hutang', 0);
    }

    public function test_diskon_persen_tidak_boleh_lebih_dari_seratus(): void
    {
        $user = User::factory()->owner()->create();
        [$supplier, $barang] = $this->buatBarangPembelian(hargaBeli: 50000);

        $response = $this->actingAs($user)
            ->from(route('pembelian.create'))
            ->post(route('pembelian.store'), [
                'supplier_id' => $supplier->id,
                'diskon_persen' => 101,
                'items' => [
                    [
                        'barang_id' => $barang->id,
                        'jumlah' => 24,
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('pembelian.create'))
            ->assertSessionHasErrors('diskon_persen');

        $this->assertDatabaseCount('pembelian', 0);
        $this->assertDatabaseCount('hutang_supplier', 0);
    }
}
