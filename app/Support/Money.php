<?php

namespace App\Support;

/**
 * Pemformat angka Rupiah yang konsisten di seluruh halaman.
 */
class Money
{
    /**
     * Format penuh: 1500000 => "Rp 1.500.000".
     */
    public static function idr(float|int|null $value): string
    {
        $value = (float) ($value ?? 0);
        $sign = $value < 0 ? '-' : '';

        return $sign.'Rp '.number_format(abs($value), 0, ',', '.');
    }

    /**
     * Format ringkas untuk kartu statistik: 1500000 => "Rp 1,5 jt".
     */
    public static function compact(float|int|null $value, string $lang = 'id'): string
    {
        $value = (float) ($value ?? 0);
        $abs = abs($value);
        $sign = $value < 0 ? '-' : '';
        $suffix = $lang === 'en' ? 'M' : 'jt';
        $billion = $lang === 'en' ? 'B' : 'M';

        return match (true) {
            $abs >= 1_000_000_000 => $sign.'Rp '.self::trim($abs / 1_000_000_000).' '.$billion,
            $abs >= 1_000_000 => $sign.'Rp '.self::trim($abs / 1_000_000).' '.$suffix,
            $abs >= 1_000 => $sign.'Rp '.self::trim($abs / 1_000).' rb',
            default => $sign.'Rp '.number_format($abs, 0, ',', '.'),
        };
    }

    private static function trim(float $value): string
    {
        $rounded = round($value, 1);

        return rtrim(rtrim(number_format($rounded, 1, ',', '.'), '0'), ',');
    }

    /**
     * Bentuk masukan angka (tanpa pemisah) menjadi float yang aman.
     */
    public static function parse(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^0-9,.\-]/', '', (string) $value) ?? '';

        // Buang pemisah ribuan gaya Indonesia (titik) lalu ubah koma menjadi titik desimal.
        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } else {
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }
}