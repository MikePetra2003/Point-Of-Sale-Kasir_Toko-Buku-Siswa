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

    public function test_satuan_seeder_menyediakan_pilihan_multi_satuan(): void
    {
        $this->seed(SatuanSeeder::class);

        $this->assertSame(
            ['box', 'lusin', 'pack', 'pcs', 'rim'],
            DB::table('satuan')->orderBy('nama_satuan')->pluck('nama_satuan')->all()
        );
    }

    public function test_form_barang_menampilkan_pilihan_multi_satuan(): void
    {
        $user = User::factory()->owner()->create();

        foreach (['pcs', 'pack', 'box', 'rim', 'lusin'] as $namaSatuan) {
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
            ->assertSee('box')
            ->assertSee('rim')
            ->assertSee('lusin')
            ->assertSee('Multi Satuan');
    }
}
