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
     * Bentuk masukan angka menjadi float yang aman.
     *
     * Mendukung format Indonesia ("1.500.000", "1.500,50") maupun
     * format Inggris/global ("1,500,000", "1,500.50") serta angka polos.
     */
    public static function parse(mixed $value): float
    {
        // Nilai numerik asli (int/float) sudah pasti bentuknya, gunakan langsung.
        // String tunggal tidak dianggap final karena "450.000" secara PHP numeric
        // tetapi dalam format Indonesia berarti 450000, bukan 450.
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^0-9,.\-]/', '', (string) $value) ?? '';

        if ($clean === '' || $clean === '-' || ! preg_match('/\d/', $clean)) {
            return 0.0;
        }

        $negative = str_starts_with($clean, '-');
        $clean = ltrim($clean, '-');
        $clean = self::normalizeSeparators($clean);

        return (float) ($negative && $clean !== '' ? '-'.$clean : ($clean === '' ? '0' : $clean));
    }

    /**
     * Ubah pemisah ribuan menjadi hilang dan hanya sisakan satu titik desimal.
     */
    private static function normalizeSeparators(string $number): string
    {
        $lastDot = strrpos($number, '.');
        $lastComma = strrpos($number, ',');
        $hasDot = $lastDot !== false;
        $hasComma = $lastComma !== false;

        if ($hasDot && $hasComma) {
            // Simbol yang muncul paling akhir adalah pemisah desimal.
            [$decimal, $grouping] = $lastDot > $lastComma ? ['.', ','] : [',', '.'];
            $number = str_replace($grouping, '', $number);
            $number = str_replace($decimal, '.', $number);

            return self::keepTrailingDecimals($number);
        }

        $separator = $hasDot ? '.' : ($hasComma ? ',' : null);

        if ($separator === null) {
            return $number;
        }

        $count = substr_count($number, $separator);
        $positionFromEnd = strlen($number) - 1 - strrpos($number, $separator);

        // Lebih dari satu kemunculan => pemisah ribuan, bukan desimal.
        // Satu kemunculan dengan digit setelahnya bukan 1-2 => pemisah ribuan.
        if ($count > 1 || $positionFromEnd > 2) {
            return str_replace($separator, '', $number);
        }

        return str_replace($separator, '.', $number);
    }

    /**
     * Pastikan hanya menyisakan satu pemisah desimal paling kanan.
     */
    private static function keepTrailingDecimals(string $number): string
    {
        $firstDot = strpos($number, '.');

        if ($firstDot === false) {
            return $number;
        }

        $integer = substr($number, 0, $firstDot);
        $decimals = str_replace('.', '', substr($number, $firstDot + 1));

        return $integer.'.'.$decimals;
    }
}
