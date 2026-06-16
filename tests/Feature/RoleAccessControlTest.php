<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_karyawan_kasir_bisa_membuka_kasir_stok_dan_riwayat_penjualan(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('penjualan.create'))
            ->assertOk()
            ->assertSee('Kasir')
            ->assertSee('Riwayat Penjualan')
            ->assertSee(route('penjualan.index'), false)
            ->assertSee(route('pelanggan.index'), false)
            ->assertDontSee('Dashboard')
            ->assertDontSee('Laporan Penjualan')
            ->assertDontSee('Pembelian Barang')
            ->assertDontSee(route('supplier.index'), false);

        $this->actingAs($user)
            ->get(route('barang.index'))
            ->assertOk()
            ->assertDontSee('Tambah Barang')
            ->assertDontSee('Aksi');

        $this->actingAs($user)
            ->get(route('penjualan.index'))
            ->assertOk()
            ->assertSee('Riwayat Penjualan');
    }

    public function test_karyawan_kasir_ditolak_dari_menu_owner(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('supplier.index'))->assertForbidden();
        $this->actingAs($user)->get(route('pembelian.index'))->assertForbidden();
        $this->actingAs($user)->get(route('piutang.index'))->assertForbidden();
        $this->actingAs($user)->get(route('laporan.penjualan'))->assertForbidden();
        $this->actingAs($user)->get(route('barang.create'))->assertForbidden();
    }

    public function test_karyawan_kasir_bisa_menambah_dan_edit_pelanggan_tapi_tidak_bisa_hapus(): void
    {
        $user = User::factory()->create();
        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => 'Dani',
            'no_telepon' => '081111222333',
        ]);

        $this->actingAs($user)
            ->get(route('pelanggan.index'))
            ->assertOk()
            ->assertSee('Pelanggan Baru')
            ->assertSee(route('pelanggan.edit', $pelanggan), false)
            ->assertDontSee('value="DELETE"', false)
            ->assertDontSee('Hapus');

        $this->actingAs($user)
            ->post(route('pelanggan.store'), [
                'nama_pelanggan' => 'Siti Aminah',
                'no_telepon' => '082222333444',
            ])
            ->assertRedirect(route('pelanggan.index'));

        $this->assertDatabaseHas('pelanggan', [
            'nama_pelanggan' => 'Siti Aminah',
            'no_telepon' => '082222333444',
        ]);

        $this->actingAs($user)
            ->put(route('pelanggan.update', $pelanggan), [
                'nama_pelanggan' => 'Dani Saputra',
                'no_telepon' => '081999888777',
            ])
            ->assertRedirect(route('pelanggan.index'));

        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'nama_pelanggan' => 'Dani Saputra',
            'no_telepon' => '081999888777',
        ]);

        $this->actingAs($user)
            ->delete(route('pelanggan.destroy', $pelanggan))
            ->assertForbidden();

        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'nama_pelanggan' => 'Dani Saputra',
        ]);
    }

    public function test_owner_bisa_membuka_menu_owner(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->actingAs($user)->get(route('pelanggan.index'))->assertOk();
        $this->actingAs($user)->get(route('supplier.index'))->assertOk();
        $this->actingAs($user)->get(route('pembelian.index'))->assertOk();
        $this->actingAs($user)->get(route('penjualan.index'))->assertOk();
        $this->actingAs($user)->get(route('piutang.index'))->assertOk();
        $this->actingAs($user)->get(route('laporan.penjualan'))->assertOk();
        $this->actingAs($user)->get(route('barang.create'))->assertOk();
    }
}
