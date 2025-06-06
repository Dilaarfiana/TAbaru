<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_harians', function (Blueprint $table) {
            $table->string('Id_Harian', 5)->primary();
            $table->dateTime('Tanggal_Jam');
            $table->text('Hasil_Pemeriksaan');
            $table->string('Id_Siswa', 10);
            $table->string('NIP', 18);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('Id_Siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('NIP')->references('NIP')->on('petugas_uks')->onDelete('cascade');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_harians');
    }
};