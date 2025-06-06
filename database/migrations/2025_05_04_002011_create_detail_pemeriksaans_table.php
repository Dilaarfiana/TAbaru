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
            $table->string('id_detprx', 5)->primary();
            $table->dateTime('tanggal_jam');
            $table->string('id_siswa', 10);
            $table->enum('status_pemeriksaan', ['belum lengkap', 'lengkap']); // Kolom yang hilang
            $table->string('id_dokter', 5);
            $table->string('nip', 18);
            $table->timestamps();
                        
            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('id_dokter')->references('id_dokter')->on('dokters')->onDelete('cascade');
            $table->foreign('nip')->references('nip')->on('petugas_uks')->onDelete('cascade');
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