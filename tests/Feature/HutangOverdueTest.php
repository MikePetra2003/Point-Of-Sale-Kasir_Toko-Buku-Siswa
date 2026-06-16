<?php

namespace Tests\Feature;

use App\Models\HutangSupplier;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HutangOverdueTest extends TestCase
{
    use RefreshDatabase;

    private function createHutangSupplier($supplier, $dueDate): HutangSupplier
    {
        $user = User::factory()->create();

        $pembelian = Pembelian::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'nomor_faktur' => 'PB-TEST-0001-'.uniqid(),
            'tanggal_pembelian' => now(),
            'total_harga' => 1000000,
            'status' => 'selesai',
        ]);

        return HutangSupplier::create([
            'pembelian_id' => $pembelian->id,
            'supplier_id' => $supplier->id,
            'total_hutang' => 1000000,
            'total_dibayar' => 0,
            'sisa_hutang' => 1000000,
            'status' => 'belum_lunas',
            'tanggal_jatuh_tempo' => Carbon::parse($dueDate),
        ]);
    }

    public function test_hutang_tidak_overdue_dan_tidak_berbunga_sebelum_jatuh_tempo(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier A',
            'alamat' => 'Alamat A',
            'no_telepon' => '08123',
        ]);

        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');

        // Simulasikan hari ini adalah 1 Juli 2026 (sebelum jatuh tempo)
        Carbon::setTestNow(Carbon::parse('2026-07-01 12:00:00'));

        $this->assertFalse($hutang->is_overdue);
        $this->assertEquals(0, $hutang->bunga);
        $this->assertEquals(1000000, $hutang->total_harus_bayar);

        Carbon::setTestNow(); // Reset time
    }

    public function test_hutang_overdue_dan_berbunga_pada_hari_jatuh_tempo(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier B',
            'alamat' => 'Alamat B',
            'no_telepon' => '08123',
        ]);

        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');

        // Simulasikan hari ini adalah 2 Juli 2026 pukul 16:00 (tepat hari jatuh tempo)
        Carbon::setTestNow(Carbon::parse('2026-07-02 16:00:00'));

        $this->assertTrue($hutang->is_overdue);
        $this->assertEquals(50000, $hutang->bunga);
        $this->assertEquals(1050000, $hutang->total_harus_bayar);

        Carbon::setTestNow(); // Reset time
    }

    public function test_hutang_overdue_dan_berbunga_setelah_hari_jatuh_tempo(): void
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier C',
            'alamat' => 'Alamat C',
            'no_telepon' => '08123',
        ]);

        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');

        // Simulasikan hari ini adalah 3 Juli 2026 (sehari setelah jatuh tempo)
        Carbon::setTestNow(Carbon::parse('2026-07-03 00:00:01'));

        $this->assertTrue($hutang->is_overdue);
        // Bunga 5% dari 1.000.000 = 50.000
        $this->assertEquals(50000, $hutang->bunga);
        $this->assertEquals(1050000, $hutang->total_harus_bayar);

        Carbon::setTestNow(); // Reset time
    }

    public function test_hutang_index_merender_modal_faktur_dengan_tombol_tutup(): void
    {
        $owner = User::factory()->owner()->create();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Modal',
            'alamat' => 'Alamat Modal',
            'no_telepon' => '08123456789',
        ]);
        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');
        $modalId = 'pembelianModalHutang'.$hutang->id;

        $response = $this->actingAs($owner)->get(route('hutang.index'));

        $response->assertOk();
        $response->assertSee('data-bs-target="#'.$modalId.'"', false);
        $response->assertSee('id="'.$modalId.'"', false);
        $response->assertSee('class="btn-close"', false);
        $response->assertSee('data-bs-dismiss="modal"', false);
        $response->assertSee('Tutup');
    }

    public function test_owner_can_access_edit_hutang_page(): void
    {
        $owner = User::factory()->owner()->create();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat' => 'Alamat Test',
            'no_telepon' => '08123456789',
        ]);
        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');

        $response = $this->actingAs($owner)->get(route('hutang.edit', $hutang->id));

        $response->assertOk();
        $response->assertSee('Edit Data Hutang Supplier');
        $response->assertSee($hutang->pembelian->nomor_faktur);
    }

    public function test_owner_can_update_hutang(): void
    {
        $owner = User::factory()->owner()->create();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat' => 'Alamat Test',
            'no_telepon' => '08123456789',
        ]);
        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');

        $response = $this->actingAs($owner)->put(route('hutang.update', $hutang->id), [
            'total_hutang' => 1500000,
            'tanggal_jatuh_tempo' => '2026-08-02',
            'keterangan' => 'Keterangan Baru',
        ]);

        $response->assertRedirect(route('hutang.show', $hutang->id));
        $hutang->refresh();

        $this->assertEquals(1500000, $hutang->total_hutang);
        $this->assertEquals(1500000, $hutang->sisa_hutang);
        $this->assertEquals('2026-08-02', $hutang->tanggal_jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('Keterangan Baru', $hutang->keterangan);
    }

    public function test_owner_can_delete_hutang(): void
    {
        $owner = User::factory()->owner()->create();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat' => 'Alamat Test',
            'no_telepon' => '08123456789',
        ]);
        $hutang = $this->createHutangSupplier($supplier, '2026-07-02');

        $response = $this->actingAs($owner)->delete(route('hutang.destroy', $hutang->id));

        $response->assertRedirect(route('hutang.index'));
        $this->assertDatabaseMissing('hutang_supplier', ['id' => $hutang->id]);
    }
}
