<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SatuanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SatuanBarangTest extends TestCase
{
    use RefreshDatabase;

    public function test_satuan_seeder_hanya_menyediakan_pcs_dan_pack(): void
    {
        $this->seed(SatuanSeeder::class);

        $this->assertSame(
            ['pack', 'pcs'],
            DB::table('satuan')->orderBy('nama_satuan')->pluck('nama_satuan')->all()
        );
    }

    public function test_form_barang_hanya_menampilkan_satuan_pcs_dan_pack(): void
    {
        $user = User::factory()->owner()->create();

        foreach (['box', 'rim', 'lusin'] as $namaSatuan) {
            DB::table('satuan')->insert([
                'nama_satuan' => $namaSatuan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->get(route('barang.create'));

        $response
            ->assertOk()
            ->assertSee('pcs')
            ->assertSee('pack')
            ->assertDontSee('box</option>', false)
            ->assertDontSee('rim</option>', false)
            ->assertDontSee('lusin</option>', false);
    }
}
