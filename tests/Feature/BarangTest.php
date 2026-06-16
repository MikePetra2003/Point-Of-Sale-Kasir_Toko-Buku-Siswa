<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function createDependencies(): array
    {
        $kategoriId = DB::table('kategori')->insertGetId([
            'nama_kategori' => 'Buku',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $satuanId = DB::table('satuan')->insertGetId([
            'nama_satuan' => 'pcs',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $supplierId = DB::table('supplier')->insertGetId([
            'nama_supplier' => 'Gramedia',
            'no_telepon'    => '021123456',
            'alamat'        => 'Jakarta',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return compact('kategoriId', 'satuanId', 'supplierId');
    }

    private function createBarang(string $kode = 'BK-001', string $nama = 'Buku Test'): Barang
    {
        $dep = $this->createDependencies();

        return Barang::create([
            'kode_barang'  => $kode,
            'nama_barang'  => $nama,
            'kategori_id'  => $dep['kategoriId'],
            'satuan_id'    => $dep['satuanId'],
            'supplier_id'  => $dep['supplierId'],
            'harga_beli'   => 50000,
            'harga_jual'   => 75000,
            'stok'         => 20,
        ]);
    }

    // -----------------------------------------------------------------------
    // Akses halaman
    // -----------------------------------------------------------------------

    public function test_owner_bisa_membuka_halaman_daftar_barang(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get(route('barang.index'))
            ->assertOk()
            ->assertSee('Tambah Barang');
    }

    public function test_kasir_bisa_membuka_halaman_daftar_barang_tapi_tanpa_aksi_edit_hapus(): void
    {
        $kasir = User::factory()->create();

        $this->actingAs($kasir)
            ->get(route('barang.index'))
            ->assertOk()
            ->assertDontSee('Tambah Barang')
            ->assertDontSee('Aksi');
    }

    public function test_kasir_tidak_bisa_membuka_form_tambah_barang(): void
    {
        $kasir = User::factory()->create();

        $this->actingAs($kasir)
            ->get(route('barang.create'))
            ->assertForbidden();
    }

    public function test_owner_bisa_membuka_form_tambah_barang(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get(route('barang.create'))
            ->assertOk()
            ->assertSee('Tambah Barang');
    }

    // -----------------------------------------------------------------------
    // Tambah barang
    // -----------------------------------------------------------------------

    public function test_owner_bisa_menambah_barang_baru(): void
    {
        $owner = User::factory()->owner()->create();
        $dep   = $this->createDependencies();

        $response = $this->actingAs($owner)->post(route('barang.store'), [
            'kode_barang'  => 'BK-NEW',
            'nama_barang'  => 'Novel Terbaru',
            'kategori_id'  => $dep['kategoriId'],
            'satuan_id'    => $dep['satuanId'],
            'supplier_id'  => $dep['supplierId'],
            'harga_beli'   => 60000,
            'harga_jual'   => 90000,
            'stok'         => 15,
        ]);

        $response->assertRedirect(route('barang.index'));
        $this->assertDatabaseHas('barang', [
            'kode_barang' => 'BK-NEW',
            'nama_barang' => 'Novel Terbaru',
            'stok'        => 15,
        ]);
    }

    public function test_kode_barang_duplikat_ditolak_saat_tambah(): void
    {
        $owner  = User::factory()->owner()->create();
        $barang = $this->createBarang('BK-DUP');
        $dep    = $this->createDependencies();

        $response = $this->actingAs($owner)->post(route('barang.store'), [
            'kode_barang'  => 'BK-DUP',
            'nama_barang'  => 'Barang Lain',
            'kategori_id'  => $dep['kategoriId'],
            'satuan_id'    => $dep['satuanId'],
            'harga_beli'   => 10000,
            'harga_jual'   => 15000,
            'stok'         => 5,
        ]);

        $response->assertSessionHasErrors('kode_barang');
        $this->assertDatabaseCount('barang', 1);
    }

    public function test_nama_barang_wajib_diisi(): void
    {
        $owner = User::factory()->owner()->create();
        $dep   = $this->createDependencies();

        $this->actingAs($owner)->post(route('barang.store'), [
            'kode_barang' => 'BK-X',
            'nama_barang' => '',
            'kategori_id' => $dep['kategoriId'],
            'satuan_id'   => $dep['satuanId'],
            'harga_beli'  => 10000,
            'harga_jual'  => 15000,
            'stok'        => 1,
        ])->assertSessionHasErrors('nama_barang');
    }

    public function test_kasir_tidak_bisa_menambah_barang(): void
    {
        $kasir = User::factory()->create();
        $dep   = $this->createDependencies();

        $this->actingAs($kasir)->post(route('barang.store'), [
            'kode_barang' => 'BK-KASIR',
            'nama_barang' => 'Buku Kasir',
            'kategori_id' => $dep['kategoriId'],
            'satuan_id'   => $dep['satuanId'],
            'harga_beli'  => 10000,
            'harga_jual'  => 15000,
            'stok'        => 5,
        ])->assertForbidden();

        $this->assertDatabaseMissing('barang', ['kode_barang' => 'BK-KASIR']);
    }

    // -----------------------------------------------------------------------
    // Edit & update barang
    // -----------------------------------------------------------------------

    public function test_owner_bisa_mengedit_barang(): void
    {
        $owner  = User::factory()->owner()->create();
        $barang = $this->createBarang('BK-EDIT');

        $this->actingAs($owner)
            ->get(route('barang.edit', $barang))
            ->assertOk()
            ->assertSee('Edit Barang');
    }

    public function test_owner_bisa_memperbarui_data_barang(): void
    {
        $owner  = User::factory()->owner()->create();
        $barang = $this->createBarang('BK-UPD');

        $this->actingAs($owner)->put(route('barang.update', $barang), [
            'kode_barang'  => 'BK-UPD',
            'nama_barang'  => 'Nama Diubah',
            'kategori_id'  => $barang->kategori_id,
            'satuan_id'    => $barang->satuan_id,
            'supplier_id'  => $barang->supplier_id,
            'harga_beli'   => 55000,
            'harga_jual'   => 80000,
            'stok'         => 30,
        ])->assertRedirect(route('barang.index'));

        $this->assertDatabaseHas('barang', [
            'id'          => $barang->id,
            'nama_barang' => 'Nama Diubah',
            'harga_jual'  => 80000,
            'stok'        => 30,
        ]);
    }

    public function test_update_barang_tidak_bentrok_dengan_kode_sendiri(): void
    {
        $owner  = User::factory()->owner()->create();
        $barang = $this->createBarang('BK-SAME');

        // Update dengan kode yang sama (milik dirinya sendiri) harus berhasil
        $this->actingAs($owner)->put(route('barang.update', $barang), [
            'kode_barang'  => 'BK-SAME',
            'nama_barang'  => 'Nama Update',
            'kategori_id'  => $barang->kategori_id,
            'satuan_id'    => $barang->satuan_id,
            'harga_beli'   => 50000,
            'harga_jual'   => 75000,
            'stok'         => 20,
        ])->assertRedirect(route('barang.index'));
    }

    public function test_kasir_tidak_bisa_memperbarui_barang(): void
    {
        $kasir  = User::factory()->create();
        $barang = $this->createBarang('BK-KASIR2');

        $this->actingAs($kasir)->put(route('barang.update', $barang), [
            'kode_barang'  => 'BK-KASIR2',
            'nama_barang'  => 'Diubah Kasir',
            'kategori_id'  => $barang->kategori_id,
            'satuan_id'    => $barang->satuan_id,
            'harga_beli'   => 55000,
            'harga_jual'   => 80000,
            'stok'         => 30,
        ])->assertForbidden();

        $this->assertDatabaseMissing('barang', ['nama_barang' => 'Diubah Kasir']);
    }

    // -----------------------------------------------------------------------
    // Hapus barang
    // -----------------------------------------------------------------------

    public function test_owner_bisa_menghapus_barang(): void
    {
        $owner  = User::factory()->owner()->create();
        $barang = $this->createBarang('BK-DEL');

        $this->actingAs($owner)
            ->delete(route('barang.destroy', $barang))
            ->assertRedirect(route('barang.index'));

        $this->assertDatabaseMissing('barang', ['id' => $barang->id]);
    }

    public function test_kasir_tidak_bisa_menghapus_barang(): void
    {
        $kasir  = User::factory()->create();
        $barang = $this->createBarang('BK-KDEL');

        $this->actingAs($kasir)
            ->delete(route('barang.destroy', $barang))
            ->assertForbidden();

        $this->assertDatabaseHas('barang', ['id' => $barang->id]);
    }

    // -----------------------------------------------------------------------
    // Pencarian barang
    // -----------------------------------------------------------------------

    public function test_pencarian_barang_berdasarkan_nama_menampilkan_hasil_yang_cocok(): void
    {
        $owner = User::factory()->owner()->create();
        $this->createBarang('BK-SRC1', 'Kamus Bahasa Indonesia');
        $this->createBarang('BK-SRC2', 'Novel Laskar Pelangi');

        $this->actingAs($owner)
            ->get(route('barang.index', ['keyword' => 'Kamus']))
            ->assertOk()
            ->assertSee('Kamus Bahasa Indonesia')
            ->assertDontSee('Novel Laskar Pelangi');
    }

    public function test_pencarian_barang_berdasarkan_kode_menampilkan_hasil_yang_cocok(): void
    {
        $owner = User::factory()->owner()->create();
        $this->createBarang('KM-001', 'Kamus Besar');
        $this->createBarang('NV-001', 'Novel Misteri');

        $this->actingAs($owner)
            ->get(route('barang.index', ['keyword' => 'KM-001']))
            ->assertOk()
            ->assertSee('Kamus Besar')
            ->assertDontSee('Novel Misteri');
    }
}
