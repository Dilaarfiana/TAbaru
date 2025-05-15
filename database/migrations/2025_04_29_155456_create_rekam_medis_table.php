<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->string('No_Rekam_Medis', 5)->primary();
            $table->string('Id_Siswa', 10)->nullable();
            $table->string('Id_Dokter', 5)->nullable();
            $table->dateTime('Tanggal_Jam');
            $table->text('Keluhan_Utama');
            $table->text('Riwayat_Penyakit_Sekarang')->nullable();
            $table->text('Riwayat_Penyakit_Dahulu')->nullable();
            $table->text('Riwayat_Imunisasi')->nullable();
            $table->text('Riwayat_Penyakit_Keluarga')->nullable();
            $table->text('Silsilah_Keluarga')->nullable();
            $table->timestamps();
            
            $table->foreign('Id_Siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('Id_Dokter')->references('Id_Dokter')->on('dokters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};