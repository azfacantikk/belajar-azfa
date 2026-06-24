<?php

if (!function_exists('hitung_diskon')) {
    /**
     * Hitung diskon berdasarkan total pembelian kotor (sebelum diskon)
     *
     * @param int $totalHarga
     * @return array
     */
    function hitung_diskon(int $totalHarga): array
    {
        $persen = 0;

        if ($totalHarga >= 50000000) {
            $persen = 15;
        } elseif ($totalHarga >= 30000000) {
            $persen = 10;
        } elseif ($totalHarga >= 10000000) {
            $persen = 5;
        } else {
            $persen = 0;
        }

        $nominalDiskon = ($persen / 100) * $totalHarga;

        return [
            'persen'  => $persen,
            'nominal' => $nominalDiskon
        ];
    }
}