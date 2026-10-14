<?php

namespace App\Support;

/**
 * Pembungkus teks dua bahasa (Indonesia / Inggris).
 *
 * Semua teks yang dihasilkan server dikembalikan sebagai pasangan
 * ['id' => ..., 'en' => ...] agar dapat dirender menjadi atribut
 * data-id / data-en dan ditangani oleh pengalih bahasa di sisi klien.
 */
class Bi
{
    /**
     * @return array{id: string, en: string}
     */
    public static function of(string $id, string $en): array
    {
        return ['id' => $id, 'en' => $en];
    }

    /**
     * Bungkus daftar kalimat menjadi daftar pasangan dua bahasa.
     *
     * @param  array<int, array{0: string, 1: string}>  $pairs
     * @return array<int, array{id: string, en: string}>
     */
    public static function many(array $pairs): array
    {
        return array_map(fn (array $pair) => self::of($pair[0], $pair[1]), $pairs);
    }
}
