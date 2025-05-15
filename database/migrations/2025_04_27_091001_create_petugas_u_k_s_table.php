<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetugasUKSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petugas_uks', function (Blueprint $table) {
            $table->string('NIP', 18)->primary();
            $table->string('nama_petugas_uks', 50);
            $table->text('alamat')->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->boolean('status_aktif')->default(true);  // Menambahkan nilai default
            $table->string('password', 255);
            $table->timestamps(); // This creates dibuat_pada and diperbarui_pada
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('petugas_uks');
    }
}