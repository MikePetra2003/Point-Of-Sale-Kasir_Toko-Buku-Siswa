<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\PiutangPelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PiutangInvoiceLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_piutang_membuka_modal_detail_penjualan(): void
    {
        $user = User::factory()->owner()->create();
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Budi Santoso',
            'no_telepon' => '08123456789',
        ]);

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
            'kode_barang' => 'BK-TJ-001',
            'nama_barang' => 'Buku Paket Matematika',
            'kategori_id' => $kategoriId,
            'satuan_id' => $satuanId,
            'harga_beli' => 25000,
            'harga_jual' => 50000,
            'stok' => 8,
        ]);

        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'pelanggan_id' => $pelanggan->id,
            'nomor_invoice' => 'TJ-2005-001',
            'tanggal_penjualan' => now(),
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'sebagian',
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'harga_jual' => 50000,
            'subtotal' => 50000,
        ]);

        Pembayaran::create([
            'penjualan_id' => $penjualan->id,
            'tanggal_pembayaran' => now(),
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 45000,
            'status_pembayaran' => 'berhasil',
        ]);

        $piutang = PiutangPelanggan::create([
            'penjualan_id' => $penjualan->id,
            'pelanggan_id' => $pelanggan->id,
            'total_piutang' => 50000,
            'total_dibayar' => 45000,
            'sisa_piutang' => 5000,
            'status' => 'sebagian',
            'tanggal_jatuh_tempo' => now()->addDays(30),
            'keterangan' => 'Piutang otomatis dari transaksi TJ-2005-001',
        ]);

        $this->actingAs($user)
            ->get(route('piutang.index'))
            ->assertOk()
            ->assertSee('TJ-2005-001')
            ->assertSee('data-bs-target="#penjualanModal'.$penjualan->id.'"', false)
            ->assertSee('Buku Paket Matematika')
            ->assertSee('Rincian Pembayaran')
            ->assertSee('Rp 45.000')
            ->assertDontSee(route('penjualan.show', $penjualan->id), false);

        $this->actingAs($user)
            ->get(route('piutang.show', $piutang->id))
            ->assertOk()
            ->assertSee('data-bs-target="#penjualanModal'.$penjualan->id.'"', false)
            ->assertSee('Buku Paket Matematika')
            ->assertSee('Rincian Pembayaran')
            ->assertSee('Rp 45.000')
            ->assertDontSee(route('penjualan.show', $penjualan->id), false);
    }

    public function test_owner_can_access_edit_general_piutang_page(): void
    {
        $user = User::factory()->owner()->create();
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Budi Santoso',
            'no_telepon' => '08123456789',
        ]);
        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'pelanggan_id' => $pelanggan->id,
            'nomor_invoice' => 'TJ-TEST-001',
            'tanggal_penjualan' => now(),
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'belum_lunas',
        ]);
        $piutang = PiutangPelanggan::create([
            'penjualan_id' => $penjualan->id,
            'pelanggan_id' => $pelanggan->id,
            'total_piutang' => 50000,
            'total_dibayar' => 0,
            'sisa_piutang' => 50000,
            'status' => 'belum_lunas',
            'tanggal_jatuh_tempo' => now()->addDays(30),
            'keterangan' => 'Piutang Test',
        ]);

        $response = $this->actingAs($user)->get(route('piutang.edit-general', $piutang->id));

        $response->assertOk();
        $response->assertSee('Edit Data Piutang Pelanggan');
        $response->assertSee($penjualan->nomor_invoice);
    }

    public function test_owner_can_update_general_piutang(): void
    {
        $user = User::factory()->owner()->create();
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Budi Santoso',
            'no_telepon' => '08123456789',
        ]);
        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'pelanggan_id' => $pelanggan->id,
            'nomor_invoice' => 'TJ-TEST-002',
            'tanggal_penjualan' => now(),
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'belum_lunas',
        ]);
        $piutang = PiutangPelanggan::create([
            'penjualan_id' => $penjualan->id,
            'pelanggan_id' => $pelanggan->id,
            'total_piutang' => 50000,
            'total_dibayar' => 0,
            'sisa_piutang' => 50000,
            'status' => 'belum_lunas',
            'tanggal_jatuh_tempo' => now()->addDays(30),
            'keterangan' => 'Piutang Test',
        ]);

        $response = $this->actingAs($user)->put(route('piutang.update-general', $piutang->id), [
            'total_piutang' => 60000,
            'tanggal_jatuh_tempo' => '2026-08-02',
            'keterangan' => 'Keterangan Baru',
        ]);

        $response->assertRedirect(route('piutang.show', $piutang->id));
        $piutang->refresh();

        $this->assertEquals(60000, $piutang->total_piutang);
        $this->assertEquals(60000, $piutang->sisa_piutang);
        $this->assertEquals('2026-08-02', $piutang->tanggal_jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('Keterangan Baru', $piutang->keterangan);
    }

    public function test_owner_can_delete_piutang(): void
    {
        $user = User::factory()->owner()->create();
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Budi Santoso',
            'no_telepon' => '08123456789',
        ]);
        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'pelanggan_id' => $pelanggan->id,
            'nomor_invoice' => 'TJ-TEST-003',
            'tanggal_penjualan' => now(),
            'total_harga' => 50000,
            'diskon' => 0,
            'total_akhir' => 50000,
            'status_pembayaran' => 'belum_lunas',
        ]);
        $piutang = PiutangPelanggan::create([
            'penjualan_id' => $penjualan->id,
            'pelanggan_id' => $pelanggan->id,
            'total_piutang' => 50000,
            'total_dibayar' => 0,
            'sisa_piutang' => 50000,
            'status' => 'belum_lunas',
            'tanggal_jatuh_tempo' => now()->addDays(30),
            'keterangan' => 'Piutang Test',
        ]);

        $response = $this->actingAs($user)->delete(route('piutang.destroy', $piutang->id));

        $response->assertRedirect(route('piutang.index'));
        $this->assertDatabaseMissing('piutang_pelanggan', ['id' => $piutang->id]);
    }
}
