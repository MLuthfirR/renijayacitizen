<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Iuran = pembayaran seorang peserta untuk satu bulan tertentu.
 * nominal > 0 berarti sudah membayar (dianggap "lunas" pada kartu).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->unsignedInteger('nominal')->default(0);
            $table->date('dibayar_pada')->nullable();
            $table->timestamps();

            $table->unique(['peserta_id', 'bulan']);
            $table->index(['periode_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran');
    }
};
