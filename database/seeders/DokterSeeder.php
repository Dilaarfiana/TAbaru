<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DokterSeeder extends Seeder
{
    public function run()
    {
        DB::table('dokters')->insert([
            [
                'Id_Dokter'    => 'DO001',
                'Nama_Dokter'  => 'dr. Andi Wijaya',
                'Spesialisasi' => 'Anak',
                'No_Telp'      => '081234567890',
                'Alamat'       => 'Jl. Kenanga No. 12',
                'status_aktif' => 1,
                'password'     => Hash::make('dokter123'), // Password default
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
            [
                'Id_Dokter'    => 'DO002',
                'Nama_Dokter'  => 'dr. Maria Sari',
                'Spesialisasi' => 'Umum',
                'No_Telp'      => '089876543210',
                'Alamat'       => 'Jl. Melati No. 7',
                'status_aktif' => 1,
                'password'     => Hash::make('dokter123'),
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]
        ]);
    }
}
