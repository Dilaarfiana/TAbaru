<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PetugasUKSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('petugas_uks')->insert([
            [
                'NIP' => '123456789012345678',
                'nama_petugas_uks' => 'Admin UKS',
                'alamat' => 'Jl. Admin No. 1',
                'no_telp' => '081234567890',
                'status_aktif' => true,
                'level' => 'admin',
                'password' => Hash::make('admin123'), // Ganti dengan password yang aman
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NIP' => '876543210987654321',
                'nama_petugas_uks' => 'Petugas UKS',
                'alamat' => 'Jl. Petugas No. 2',
                'no_telp' => '089876543210',
                'status_aktif' => true,
                'level' => 'petugas',
                'password' => Hash::make('petugas123'), // Ganti dengan password yang aman
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
