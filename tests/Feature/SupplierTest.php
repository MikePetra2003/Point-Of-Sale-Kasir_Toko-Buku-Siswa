<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_bisa_menambah_supplier_tanpa_alamat(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->post(route('supplier.store'), [
                'nama_supplier' => 'CV Buana Mas Madiun',
                'no_telepon' => '03512811028',
                'alamat' => null,
            ])
            ->assertRedirect(route('supplier.index'));

        $this->assertDatabaseHas('supplier', [
            'nama_supplier' => 'CV Buana Mas Madiun',
            'no_telepon' => '03512811028',
            'alamat' => null,
        ]);
    }

    public function test_owner_bisa_menambah_supplier_tanpa_nomor_telepon_dan_alamat(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->post(route('supplier.store'), [
                'nama_supplier' => 'Supplier Tanpa Kontak',
            ])
            ->assertRedirect(route('supplier.index'));

        $this->assertDatabaseHas('supplier', [
            'nama_supplier' => 'Supplier Tanpa Kontak',
            'no_telepon' => null,
            'alamat' => null,
        ]);
    }
}
