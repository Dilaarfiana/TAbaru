<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('id_orang_tua', 5);
            $table->string('id_siswa', 10);
            $table->enum('type', [
                'rekam_medis',
                'pemeriksaan_awal', 
                'pemeriksaan_fisik',
                'pemeriksaan_harian',
                'resep'
            ]);
            $table->string('title', 100);
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('created_by', 50)->nullable();
            $table->string('created_by_role', 20)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['id_orang_tua', 'is_read']);
            $table->index(['id_siswa']);
            $table->index(['created_at']);
            
            $table->foreign('id_orang_tua')->references('id_orang_tua')->on('orang_tuas')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};