<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $guarded = [];

    /** Ambil baris pengaturan tunggal (buat bila belum ada). */
    public static function get(): self
    {
        $p = static::firstOrCreate(['id' => 1]);

        // Setelah dibuat, muat ulang agar nilai default kolom dari DB terisi.
        return $p->wasRecentlyCreated ? $p->fresh() : $p;
    }
}
