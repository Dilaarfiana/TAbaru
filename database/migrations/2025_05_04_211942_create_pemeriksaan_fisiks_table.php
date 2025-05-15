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
        Schema::create('pemeriksaan_fisiks', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('id_pasien');
            $table->date('tanggal_pemeriksaan');
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('suhu_badan', 5, 2)->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('hasil_pemeriksaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_fisiks');
    }
};