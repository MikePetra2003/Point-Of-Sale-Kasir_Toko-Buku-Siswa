<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Buku', 'Alat Tulis', 'Lainnya'] as $namaKategori) {
            DB::table('kategori')->updateOrInsert(
                ['nama_kategori' => $namaKategori],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
