<?php

namespace App\Support;

class Format
{
    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public const BULAN_SINGKAT = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUN',
        7 => 'JUL', 8 => 'AGU', 9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
    ];

    /** Format rupiah lengkap: 19902000 -> "Rp 19.902.000". */
    public static function rupiah(int|float|null $n): string
    {
        return 'Rp ' . number_format((int) $n, 0, ',', '.');
    }

    /** Angka saja: 19902000 -> "19.902.000". */
    public static function angka(int|float|null $n): string
    {
        return number_format((int) $n, 0, ',', '.');
    }

    /** Rupiah ringkas untuk kartu ringkasan: 19902000 -> "19,9 jt". */
    public static function rupiahRingkas(int|float|null $n): string
    {
        $n = (int) $n;
        if ($n >= 1_000_000) {
            return 'Rp ' . rtrim(rtrim(number_format($n / 1_000_000, 1, ',', '.'), '0'), ',') . ' jt';
        }
        if ($n >= 1_000) {
            return 'Rp ' . number_format($n / 1_000, 0, ',', '.') . ' rb';
        }
        return self::rupiah($n);
    }

    public static function namaBulan(?int $bulan): string
    {
        return self::BULAN[$bulan] ?? '-';
    }

    public static function namaBulanSingkat(?int $bulan): string
    {
        return self::BULAN_SINGKAT[$bulan] ?? '-';
    }
}
