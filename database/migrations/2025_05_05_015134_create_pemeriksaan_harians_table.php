<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemeriksaanHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemeriksaan_harian', function (Blueprint $table) {
            $table->string('Id_Harian', 5)->primary();
            $table->dateTime('Tanggal_Jam');
            $table->text('Hasil_Pemeriksaan');
            $table->string('Id_Siswa', 10);
            $table->string('Id_Dokter', 5);
            $table->string('NIP', 18);
            $table->timestamps();

            // Foreign keys
            $table->foreign('Id_Siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('Id_Dokter')->references('Id_Dokter')->on('dokters')->onDelete('cascade');
            $table->foreign('NIP')->references('NIP')->on('petugas_uks')->onDelete('cascade');
        });

        // Insert default record with ID PH001
        DB::table('pemeriksaan_harian')->insert([
            'Id_Harian' => 'PH001',
            'Tanggal_Jam' => now(),
            'Hasil_Pemeriksaan' => 'Pemeriksaan awal',
            'Id_Siswa' => DB::table('siswas')->first()->id_siswa ?? '',
            'Id_Dokter' => DB::table('dokters')->first()->Id_Dokter ?? '',
            'NIP' => DB::table('petugas_uks')->first()->NIP ?? '',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemeriksaan_harian');
    }
}