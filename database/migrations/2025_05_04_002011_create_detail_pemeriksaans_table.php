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
        Schema::create('detail_pemeriksaans', function (Blueprint $table) {
            $table->string('Id_DetPrx', 5)->primary();
            $table->dateTime('Tanggal_Jam');
            $table->text('Hasil_Pemeriksaan')->nullable();
            $table->string('Id_Siswa', 10);
            $table->string('Id_Dokter', 5);
            $table->string('NIP', 18);
            $table->timestamps();
            
            // Foreign keys yang benar
            $table->foreign('Id_Siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('Id_Dokter')->references('Id_Dokter')->on('dokters')->onDelete('cascade');
            $table->foreign('NIP')->references('NIP')->on('petugas_uks')->onDelete('cascade'); // Ubah ke petugas_uks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pemeriksaans');
    }
};