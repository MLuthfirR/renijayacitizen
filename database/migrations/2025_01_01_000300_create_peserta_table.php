<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Peserta (Kepala Keluarga) untuk sebuah periode.
 * Roster disalin dari tahun sebelumnya lalu diedit seperlunya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(0); // kolom "NO" pada kartu
            $table->string('nama');
            $table->string('blok')->nullable();   // mis. "M 1", "L 2"
            $table->string('nomor')->nullable();  // mis. "14", "6"
            $table->boolean('luar_lingkungan')->default(false); // peserta di luar RT.02/RW.06
            $table->enum('status', ['aktif', 'pindah', 'berhenti'])->default('aktif');
            $table->unsignedTinyInteger('mulai_bulan')->nullable();   // 1-12, bila masuk di tengah tahun
            $table->unsignedTinyInteger('selesai_bulan')->nullable(); // 1-12, bila pindah/berhenti
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['periode_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
