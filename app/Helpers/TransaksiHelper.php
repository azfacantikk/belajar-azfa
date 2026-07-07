<?php

/**
 * Menghitung biaya admin berdasarkan total harga pembelian.
 * <= Rp 20.000.000 -> 0.5%
 * > Rp 20.000.000 -> 0.75%
 */
if (!function_exists('hitung_biaya_admin')) {
    function hitung_biaya_admin($total_harga)
    {
        if ($total_harga <= 20000000) {
            return $total_harga * 0.005;
        } else {
            return $total_harga * 0.0075;
        }
    }
}

/**
 * Menghitung diskon kupon berdasarkan total harga pembelian awal.
 * HEMAT -> 15%
 * SUPER -> 20%
 */
if (!function_exists('hitung_diskon_kupon')) {
    function hitung_diskon_kupon($total_harga, $kupon_code)
    {
        $kupon = strtoupper(trim($kupon_code));
        
        if ($kupon === 'HEMAT') {
            return $total_harga * 0.15;
        } elseif ($kupon === 'SUPER') {
            return $total_harga * 0.20;
        }
        
        return 0;
    }
}

/**
 * Menghitung cashback sebesar 2% jika total harga > Rp 10.000.000.
 */
if (!function_exists('hitung_cashback')) {
    function hitung_cashback($total_harga)
    {
        if ($total_harga > 10000000) {
            return $total_harga * 0.02;
        }
        return 0;
    }
}