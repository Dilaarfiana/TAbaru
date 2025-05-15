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
        Schema::create('pemeriksaan_awals', function (Blueprint $table) {
            $table->string('Id_PreAwal', 5)->primary();
            $table->string('Id_DetPrx', 5);
            $table->text('Pemeriksaan')->nullable();
            $table->string('Keluhan_Dahulu', 255)->nullable();
            $table->decimal('Suhu', 3, 1)->nullable();
            $table->decimal('Nadi', 3, 0)->nullable();
            $table->string('Tegangan', 7)->nullable();
            $table->integer('Pernapasan')->nullable();
            $table->integer('Tipe')->nullable();
            $table->integer('Status_Nyeri')->nullable();
            $table->string('Karakteristik', 50)->nullable();
            $table->string('Lokasi', 50)->nullable();
            $table->string('Durasi', 30)->nullable();
            $table->string('Frekuensi', 30)->nullable();
            $table->timestamps();
            
            $table->foreign('Id_DetPrx')->references('Id_DetPrx')->on('detail_pemeriksaans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_awals');
    }
};