<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirPelangganInlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_kasir_tidak_menampilkan_form_pelanggan_baru(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('penjualan.create'))
            ->assertOk()
            ->assertSee('Umum')
            ->assertSee('Kredit')
            ->assertDontSee('Diskon (Rp)')
            ->assertDontSee('-- Umum / Tanpa Pelanggan --')
            ->assertDontSee('Pelanggan Baru')
            ->assertDontSee('pelanggan_baru_nama');
    }

    public function test_halaman_pelanggan_menampilkan_form_pelanggan_baru(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('pelanggan.index'))
            ->assertOk()
            ->assertSee('Pelanggan Baru')
            ->assertSee('nama_pelanggan')
            ->assertSee('no_telepon');
    }

    public function test_pelanggan_baru_bisa_disimpan_dari_halaman_pelanggan(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->post(route('pelanggan.store'), [
                'nama_pelanggan' => 'Siti Aminah',
                'no_telepon' => '081111222333',
            ])
            ->assertRedirect(route('pelanggan.index'));

        $this->assertDatabaseHas('pelanggan', [
            'no_id_pelanggan' => 'S001',
            'nama_pelanggan' => 'Siti Aminah',
            'no_telepon' => '081111222333',
        ]);
    }

    public function test_no_id_pelanggan_dibuat_berdasarkan_huruf_awal_nama(): void
    {
        $user = User::factory()->owner()->create();

        foreach (['Dani', 'Doni', 'Cherly'] as $namaPelanggan) {
            $this->actingAs($user)->post(route('pelanggan.store'), [
                'nama_pelanggan' => $namaPelanggan,
            ]);
        }

        $this->assertDatabaseHas('pelanggan', [
            'nama_pelanggan' => 'Dani',
            'no_id_pelanggan' => 'D001',
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'nama_pelanggan' => 'Doni',
            'no_id_pelanggan' => 'D002',
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'nama_pelanggan' => 'Cherly',
            'no_id_pelanggan' => 'C001',
        ]);
    }

    public function test_nama_pelanggan_yang_sama_ditolak(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)->post(route('pelanggan.store'), [
            'nama_pelanggan' => 'Dani',
        ]);

        $this->actingAs($user)
            ->post(route('pelanggan.store'), [
                'nama_pelanggan' => 'dani',
            ])
            ->assertSessionHasErrors('nama_pelanggan');
    }
}
