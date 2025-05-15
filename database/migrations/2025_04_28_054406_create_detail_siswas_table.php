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
        Schema::create('detail_siswas', function (Blueprint $table) {
            $table->string('id_detsiswa', 5)->primary();
            $table->string('id_siswa', 10);
            $table->char('kode_jurusan', 1)->nullable();
            $table->string('kode_kelas', 5)->nullable();
            
            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('kode_jurusan')->references('Kode_Jurusan')->on('Jurusan')->onDelete('cascade');
            $table->foreign('kode_kelas')->references('Kode_Kelas')->on('Kelas')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_siswas');
    }
};