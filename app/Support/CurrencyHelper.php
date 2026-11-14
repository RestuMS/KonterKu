<?php

namespace App\Support;

class CurrencyHelper
{
    /**
     * Bersihkan input nominal uang dari format titik/koma/spasi/Rp
     * menjadi angka float murni yang siap divalidasi dan disimpan ke database.
     *
     * Contoh:
     * - "579678" -> 579678.0
     * - "579,678" -> 579678.0
     * - "579.678" -> 579678.0
     * - "Rp 579.678" -> 579678.0
     * - "1.234.567" -> 1234567.0
     * - "1,234,567" -> 1234567.0
     * - "579678.50" -> 579678.5
     */
    public static function parse(mixed $value, float $default = 0.0): float
    {
        if (is_numeric($value) && !is_string($value)) {
            return (float) $value;
        }

        if ($value === null) {
            return $default;
        }

        $val = trim((string) $value);
        if ($val === '') {
            return $default;
        }

        $val = str_ireplace(['Rp', ' ', "\t"], '', $val);

        // Pola 3 digit ribuan tunggal: 579.678 atau 579,678
        if (preg_match('/^\d{1,3}\.\d{3}$/', $val)) {
            return (float) str_replace('.', '', $val);
        }
        if (preg_match('/^\d{1,3},\d{3}$/', $val)) {
            return (float) str_replace(',', '', $val);
        }

        // Ribuan berulang dengan koma (1,234,567 atau 1,234,567.89)
        if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $val)) {
            return (float) str_replace(',', '', $val);
        }

        // Ribuan berulang dengan titik (1.234.567 atau 1.234.567,89)
        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $val)) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
            return (float) $val;
        }

        // Desimal koma Indonesia (misal 579,5 -> 579.5)
        $val = str_replace(',', '.', $val);

        return is_numeric($val) ? (float) $val : $default;
    }
}
