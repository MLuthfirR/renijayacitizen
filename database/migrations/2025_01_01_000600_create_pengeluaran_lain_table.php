<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengeluaran lain-lain di luar santunan (administrasi, dsb).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_lain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(0);
            $table->string('deskripsi');
            $table->unsignedBigInteger('nominal');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['periode_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_lain');
    }
};
