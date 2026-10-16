<?php

namespace App\Support;

/**
 * Consistent Rupiah number formatting throughout the application.
 */
class Money
{
    /**
     * Full format: 1500000 => "Rp 1.500.000".
     */
    public static function idr(float|int|null $value): string
    {
        $value = (float) ($value ?? 0);
        $sign = $value < 0 ? '-' : '';

        return $sign.'Rp '.number_format(abs($value), 0, ',', '.');
    }

    /**
     * Compact format for statistic cards; the output depends on the selected language.
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
     * Convert numeric input to a safe float.
     *
     * Supports Indonesian formats ("1.500.000", "1.500,50"), English/global
     * formats ("1,500,000", "1,500.50"), and plain numbers.
     */
    public static function parse(mixed $value): float
    {
        // Native numeric values (int/float) are unambiguous, so use them directly.
        // A single-separator string is not treated as final because "450.000" is
        // numeric in PHP but means 450000, not 450, in Indonesian formatting.
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
     * Remove thousands separators and leave only one decimal point.
     */
    private static function normalizeSeparators(string $number): string
    {
        $lastDot = strrpos($number, '.');
        $lastComma = strrpos($number, ',');
        $hasDot = $lastDot !== false;
        $hasComma = $lastComma !== false;

        if ($hasDot && $hasComma) {
            // The last-occurring symbol is the decimal separator.
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

        // More than one occurrence means a thousands separator, not a decimal.
        // One occurrence followed by other than 1–2 digits means a thousands separator.
        if ($count > 1 || $positionFromEnd > 2) {
            return str_replace($separator, '', $number);
        }

        return str_replace($separator, '.', $number);
    }

    /**
     * Ensure only the rightmost decimal separator remains.
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
