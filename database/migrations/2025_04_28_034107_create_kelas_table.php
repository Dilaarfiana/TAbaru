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
        Schema::create('Kelas', function (Blueprint $table) {
            $table->string('Kode_Kelas', 5)->primary();
            $table->string('Nama_Kelas', 20)->notNullable();
            $table->string('Tahun_Ajaran', 10)->nullable();
            $table->char('Kode_Jurusan', 1)->nullable();
            $table->integer('Jumlah_Siswa')->nullable();
            
            $table->foreign('Kode_Jurusan')
                  ->references('Kode_Jurusan')
                  ->on('Jurusan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Kelas');
    }
};