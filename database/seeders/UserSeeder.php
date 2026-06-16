<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'michael.sugianto2040@gmail.com'],
            [
                'name' => 'Yudi',
                'password' => bcrypt('TokoSiswaNo49'),
                'role' => 'Pemilik',
            ]
        );

        User::updateOrCreate(
            ['email' => 'hitamman9088@gmail.com'],
            [
                'name' => 'Doni',
                'password' => bcrypt('KasirYangBaik'),
                'role' => 'Karyawan',
            ]
        );
    }
}
