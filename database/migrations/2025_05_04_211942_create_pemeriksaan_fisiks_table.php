<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_fisiks', function (Blueprint $table) {
            $table->string('id_prefisik', 5)->primary();
            $table->string('id_detprx', 5);
            $table->decimal('tinggi_badan', 4, 1)->nullable();
            $table->decimal('berat_badan', 4, 1)->nullable();
            $table->decimal('lingkar_kepala', 4, 1)->nullable();
            $table->decimal('lingkar_lengan_atas', 3, 1)->nullable();
            $table->string('dada', 50)->nullable();
            $table->string('jantung', 50)->nullable();
            $table->string('paru', 50)->nullable();
            $table->string('perut', 50)->nullable();
            $table->string('hepar', 50)->nullable();
            $table->string('anogenital', 50)->nullable();
            $table->string('ekstremitas', 50)->nullable();
            $table->string('kepala', 50)->nullable();
            $table->text('pemeriksaan_penunjang')->nullable();
            $table->string('masalah_aktif', 50)->nullable();
            $table->string('rencana_medis_dan_terapi', 50)->nullable();
            $table->timestamps();
            
            $table->foreign('id_detprx')->references('id_detprx')->on('detail_pemeriksaans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_fisiks');
    }
};