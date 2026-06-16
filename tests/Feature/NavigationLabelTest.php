<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_piutang_menu_memakai_tulisan_dan_icon_yang_sama_dengan_halaman(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('piutang.index'))
            ->assertOk()
            ->assertSee('Piutang Pelanggan')
            ->assertSee('bi bi-wallet2', false)
            ->assertDontSee('Tagihan Pelanggan');
    }

    public function test_riwayat_penjualan_sidebar_memakai_icon_clock_history(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('penjualan.index'))
            ->assertOk()
            ->assertSee('Riwayat Penjualan')
            ->assertSee('bi bi-clock-history', false);
    }
}
