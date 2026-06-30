<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['pcs', 'pack', 'box', 'rim', 'lusin'] as $namaSatuan) {
            DB::table('satuan')->updateOrInsert(
                ['nama_satuan' => $namaSatuan],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
