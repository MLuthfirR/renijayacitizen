<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Periode = satu tahun buku iuran duka cita.
 * Saldo akhir sebuah periode menjadi saldo awal periode berikutnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->unique();
            $table->unsignedBigInteger('saldo_awal')->default(0);
            $table->unsignedInteger('iuran_default')->default(5000);
            // aktif = boleh diedit; terkunci = final/read-only
            $table->enum('status', ['aktif', 'terkunci'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};
