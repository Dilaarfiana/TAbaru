<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_awals', function (Blueprint $table) {
            $table->string('id_preawal', 5)->primary();
            $table->string('id_detprx', 5);
            $table->text('pemeriksaan')->nullable();
            $table->string('keluhan_dahulu', 255)->nullable();
            $table->decimal('suhu', 3, 1)->nullable();
            $table->decimal('nadi', 3, 0)->nullable();
            $table->string('tegangan', 7)->nullable();
            $table->integer('pernapasan')->nullable();
            $table->integer('tipe')->nullable();
            $table->integer('status_nyeri')->nullable();
            $table->string('karakteristik', 50)->nullable();
            $table->string('lokasi', 50)->nullable();
            $table->string('durasi', 30)->nullable();
            $table->string('frekuensi', 30)->nullable();
            $table->timestamps();
            
            $table->foreign('id_detprx')->references('id_detprx')->on('detail_pemeriksaans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_awals');
    }
};