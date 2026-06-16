<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\PiutangPelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PenjualanPiutangTest extends TestCase
{
    use RefreshDatabase;

    public function test_penjualan_tunai_kurang_dari_total_ditolak(): void
    {
        $user = User::factory()->create();
        $barang = $this->createBarang('BK-001', 'Buku Test', 50000, 10);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 1,
                ],
            ],
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 45000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('penjualan', 0);
        $this->assertDatabaseCount('piutang_pelanggan', 0);
        $this->assertDatabaseCount('pembayaran_piutang', 0);

        $this->assertDatabaseHas('barang', [
            'id' => $barang->id,
            'stok' => 10,
        ]);
    }

    public function test_penjualan_tunai_pas_dengan_total_berhasil_tanpa_piutang(): void
    {
        $user = User::factory()->create();
        $barang = $this->createBarang('BK-002', 'Buku Tunai Pas', 125000, 8);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 1,
                ],
            ],
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 125000,
        ]);

        $response->assertRedirect();
        $penjualan = DB::table('penjualan')->first();

        $this->assertNotNull($penjualan);
        $this->assertSame('lunas', $penjualan->status_pembayaran);
        $this->assertDatabaseHas('pembayaran', [
            'penjualan_id' => $penjualan->id,
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 125000,
            'status_pembayaran' => 'berhasil',
        ]);
        $this->assertDatabaseCount('piutang_pelanggan', 0);
    }

    public function test_penjualan_tunai_lebih_dari_total_berhasil_dan_tetap_tanpa_piutang(): void
    {
        $user = User::factory()->create();
        $barang = $this->createBarang('BK-003', 'Buku Tunai Lebih', 125000, 7);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 1,
                ],
            ],
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 130000,
        ]);

        $response->assertRedirect();
        $penjualan = DB::table('penjualan')->first();

        $this->assertNotNull($penjualan);
        $this->assertSame('lunas', $penjualan->status_pembayaran);
        $this->assertDatabaseHas('pembayaran', [
            'penjualan_id' => $penjualan->id,
            'metode_pembayaran' => 'tunai',
            'jumlah_bayar' => 130000,
            'status_pembayaran' => 'berhasil',
        ]);
        $this->assertDatabaseCount('piutang_pelanggan', 0);
    }

    public function test_penjualan_kredit_dibawah_batas_minimum_ditolak(): void
    {
        $user = User::factory()->create();
        $pelanggan = $this->createPelanggan('Rina');
        $barang = $this->createBarang('BK-004', 'Buku Kredit Mini', 70000, 8);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 1,
                ],
            ],
            'pelanggan_id' => $pelanggan->id,
            'metode_pembayaran' => 'kredit',
            'jumlah_bayar' => 99999,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('penjualan', 0);
        $this->assertDatabaseCount('piutang_pelanggan', 0);
    }

    public function test_penjualan_kredit_diatas_batas_minimum_redirect_ke_form_pelengkapan_piutang(): void
    {
        $user = User::factory()->create();
        $pelanggan = $this->createPelanggan('Sinta');
        $barang = $this->createBarang('BK-005', 'Buku Kredit', 125000, 8);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 4,
                ],
            ],
            'pelanggan_id' => $pelanggan->id,
            'metode_pembayaran' => 'kredit',
            'jumlah_bayar' => 99999,
        ]);

        $piutang = PiutangPelanggan::firstOrFail();

        $response->assertRedirect(route('piutang.edit', $piutang->id));
        $this->assertDatabaseHas('penjualan', [
            'pelanggan_id' => $pelanggan->id,
            'total_akhir' => 500000,
            'status_pembayaran' => 'sebagian',
        ]);

        $this->assertDatabaseHas('pembayaran', [
            'metode_pembayaran' => 'kredit',
            'jumlah_bayar' => 0,
            'status_pembayaran' => 'berhasil',
        ]);

        $this->assertDatabaseHas('piutang_pelanggan', [
            'pelanggan_id' => $pelanggan->id,
            'total_piutang' => 500000,
            'total_dibayar' => 0,
            'sisa_piutang' => 500000,
            'status' => 'belum_lunas',
        ]);

        $this->assertDatabaseCount('pembayaran_piutang', 0);
    }

    public function test_form_piutang_kredit_menolak_bayar_awal_nol(): void
    {
        $user = User::factory()->create();
        $pelanggan = $this->createPelanggan('Tono');
        $barang = $this->createBarang('BK-006', 'Buku Paket', 250000, 6);

        $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ],
            ],
            'pelanggan_id' => $pelanggan->id,
            'metode_pembayaran' => 'kredit',
            'jumlah_bayar' => 0,
        ]);

        $piutang = PiutangPelanggan::firstOrFail();

        $response = $this->actingAs($user)->patch(route('piutang.update', $piutang->id), [
            'nama_pelanggan' => 'Tono',
            'no_telepon' => '08123456789',
            'tanggal_jatuh_tempo' => now()->addDays(45)->format('Y-m-d'),
            'keterangan' => 'Tempo khusus pelanggan langganan',
            'jumlah_bayar_awal' => 0,
            'metode_pembayaran_awal' => 'tunai',
        ]);

        $response->assertSessionHasErrors('jumlah_bayar_awal');
        $this->assertDatabaseCount('pembayaran_piutang', 0);
    }

    public function test_kasir_bisa_membuka_dan_menyimpan_form_pelengkapan_piutang_kredit(): void
    {
        $user = User::factory()->create();
        $pelanggan = $this->createPelanggan('Tono');
        $barang = $this->createBarang('BK-007', 'Buku Paket Besar', 250000, 6);

        $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ],
            ],
            'pelanggan_id' => $pelanggan->id,
            'metode_pembayaran' => 'kredit',
        ]);

        $piutang = PiutangPelanggan::firstOrFail();

        $this->actingAs($user)
            ->get(route('piutang.edit', $piutang->id))
            ->assertOk()
            ->assertSee('Lengkapi Piutang Pelanggan');

        $response = $this->actingAs($user)->patch(route('piutang.update', $piutang->id), [
            'nama_pelanggan' => 'Tono Update',
            'no_telepon' => '0812999000',
            'tanggal_jatuh_tempo' => now()->addDays(45)->format('Y-m-d'),
            'keterangan' => 'Tempo khusus pelanggan langganan',
            'jumlah_bayar_awal' => 125000,
            'metode_pembayaran_awal' => 'tunai',
        ]);

        $response->assertRedirect(route('piutang.show', $piutang->id));
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'nama_pelanggan' => 'Tono Update',
            'no_telepon' => '0812999000',
        ]);
        $this->assertDatabaseHas('piutang_pelanggan', [
            'id' => $piutang->id,
            'tanggal_jatuh_tempo' => now()->addDays(45)->startOfDay()->format('Y-m-d H:i:s'),
            'keterangan' => 'Tempo khusus pelanggan langganan',
            'total_dibayar' => 125000,
            'sisa_piutang' => 375000,
            'status' => 'sebagian',
        ]);
        $this->assertDatabaseHas('pembayaran_piutang', [
            'piutang_id' => $piutang->id,
            'jumlah_bayar' => 125000,
            'metode_pembayaran' => 'tunai',
        ]);
        $this->assertDatabaseHas('pembayaran', [
            'penjualan_id' => $piutang->penjualan_id,
            'metode_pembayaran' => 'kredit',
            'jumlah_bayar' => 125000,
        ]);
    }

    public function test_kredit_dari_umum_membuat_pelanggan_baru_saat_form_piutang_disimpan(): void
    {
        $user = User::factory()->create();
        $barang = $this->createBarang('BK-009', 'Buku Kredit Umum', 250000, 5);

        $response = $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ],
            ],
            'metode_pembayaran' => 'kredit',
        ]);

        $piutang = PiutangPelanggan::firstOrFail();

        $response->assertRedirect(route('piutang.edit', $piutang->id));
        $this->assertDatabaseHas('penjualan', [
            'id' => $piutang->penjualan_id,
            'pelanggan_id' => null,
            'total_akhir' => 500000,
            'status_pembayaran' => 'sebagian',
        ]);
        $this->assertDatabaseHas('piutang_pelanggan', [
            'id' => $piutang->id,
            'pelanggan_id' => null,
            'total_piutang' => 500000,
            'sisa_piutang' => 500000,
        ]);

        $this->actingAs($user)
            ->get(route('piutang.edit', $piutang->id))
            ->assertOk()
            ->assertSee('Pelanggan Baru');

        $response = $this->actingAs($user)->patch(route('piutang.update', $piutang->id), [
            'nama_pelanggan' => 'Ari Pembeli Kredit',
            'no_telepon' => '081277788899',
            'tanggal_jatuh_tempo' => now()->addDays(30)->format('Y-m-d'),
            'keterangan' => 'Piutang dari pelanggan baru',
            'jumlah_bayar_awal' => 100000,
            'metode_pembayaran_awal' => 'qris',
        ]);

        $pelanggan = Pelanggan::where('nama_pelanggan', 'Ari Pembeli Kredit')->firstOrFail();

        $response->assertRedirect(route('piutang.show', $piutang->id));
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'nama_pelanggan' => 'Ari Pembeli Kredit',
            'no_telepon' => '081277788899',
        ]);
        $this->assertDatabaseHas('penjualan', [
            'id' => $piutang->penjualan_id,
            'pelanggan_id' => $pelanggan->id,
        ]);
        $this->assertDatabaseHas('piutang_pelanggan', [
            'id' => $piutang->id,
            'pelanggan_id' => $pelanggan->id,
            'total_dibayar' => 100000,
            'sisa_piutang' => 400000,
            'status' => 'sebagian',
        ]);
        $this->assertDatabaseHas('pembayaran_piutang', [
            'piutang_id' => $piutang->id,
            'jumlah_bayar' => 100000,
            'metode_pembayaran' => 'qris',
        ]);
    }

    public function test_bayar_awal_sebesar_total_piutang_membuat_penjualan_lunas(): void
    {
        $user = User::factory()->create();
        $pelanggan = $this->createPelanggan('Lina');
        $barang = $this->createBarang('BK-008', 'Buku Paket Lunas', 250000, 4);

        $this->actingAs($user)->post(route('penjualan.store'), [
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ],
            ],
            'pelanggan_id' => $pelanggan->id,
            'metode_pembayaran' => 'kredit',
        ]);

        $piutang = PiutangPelanggan::firstOrFail();

        $response = $this->actingAs($user)->patch(route('piutang.update', $piutang->id), [
            'nama_pelanggan' => 'Lina',
            'no_telepon' => '08123456789',
            'tanggal_jatuh_tempo' => now()->addDays(30)->format('Y-m-d'),
            'keterangan' => 'Lunas saat membuat piutang',
            'jumlah_bayar_awal' => 500000,
            'metode_pembayaran_awal' => 'transfer',
        ]);

        $response->assertRedirect(route('piutang.show', $piutang->id));
        $this->assertDatabaseHas('piutang_pelanggan', [
            'id' => $piutang->id,
            'total_dibayar' => 500000,
            'sisa_piutang' => 0,
            'status' => 'lunas',
        ]);
        $this->assertDatabaseHas('penjualan', [
            'id' => $piutang->penjualan_id,
            'status_pembayaran' => 'lunas',
        ]);
    }

    private function createPelanggan(string $nama): Pelanggan
    {
        return Pelanggan::create([
            'nama_pelanggan' => $nama,
            'no_telepon' => '08123456789',
        ]);
    }

    private function createBarang(string $kode, string $nama, int $hargaJual, int $stok): Barang
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
            'kode_barang' => $kode,
            'nama_barang' => $nama,
            'kategori_id' => $kategoriId,
            'satuan_id' => $satuanId,
            'harga_beli' => (int) ($hargaJual * 0.7),
            'harga_jual' => $hargaJual,
            'stok' => $stok,
        ]);
    }
}
