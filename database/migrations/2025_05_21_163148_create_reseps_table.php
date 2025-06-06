<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->string('Id_Resep', 5)->primary();
            $table->string('Id_Siswa', 10);
            $table->string('Id_Dokter', 5);
            $table->date('Tanggal_Resep');
            $table->string('Nama_Obat', 30);
            $table->string('Dosis', 30);
            $table->string('Durasi', 30);
            
            // Untuk foto/PDF - butuh MEDIUMBLOB
            $table->binary('Dokumen')->nullable();
            
            $table->timestamp('dibuat_pada')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('diperbarui_pada')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                     
            $table->foreign('Id_Siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('Id_Dokter')->references('Id_Dokter')->on('dokters')->onDelete('cascade');
        });
        
        // Upgrade ke MEDIUMBLOB untuk foto/PDF
        DB::statement('ALTER TABLE resep MODIFY COLUMN Dokumen MEDIUMBLOB');
    }

    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};