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
                'NIP' => '20400162',
                'nama_petugas_uks' => 'Admin UKS',
                'alamat' => 'Jl. Wates 147 KalibayemRT 0RW 0',
                'no_telp' => '(0274) 374410',
                'status_aktif' => true,
                'level' => 'admin',
                'password' => Hash::make('SLBN1Bantul2025@'), // Ganti dengan password yang aman
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
