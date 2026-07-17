<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengaturan global organisasi & penanda tangan surat.
 * Cukup satu baris (id = 1) yang dipakai di seluruh laporan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_organisasi')->default('PENGURUS PKK RT.02/RW.06 RENI JAYA');
            $table->string('alamat_baris1')->default('PONDOK BENDA, PAMULANG');
            $table->string('alamat_baris2')->default('TANGERANG SELATAN');
            $table->string('nama_rt')->default('RT.02/RW.06');
            $table->string('nama_perumahan')->default('Perum Reni Jaya');
            $table->string('tempat')->default('Reni Jaya');

            // Penanda tangan surat pengantar
            $table->string('seksi_duka_cita')->default('Ny. Dewi Hairowati');
            $table->string('ketua_pkk')->default('Ny. Just Pangau');
            $table->string('ketua_rt')->default('Bp. Hasan Basri');

            // Default iuran per KK per bulan
            $table->unsignedInteger('iuran_default')->default(5000);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
