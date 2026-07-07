<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
// Note: Jika nama model keranjang belanja di projectmu berbeda (misal KeranjangModel), sesuaikan baris di bawah ini:
// use App\Models\CartModel; 

class Keranjang extends BaseController
{
    protected $transactionModel;
    protected $transactionDetailModel;
    // protected $cartModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
        // $this->cartModel = new CartModel();
    }

    public function index()
    {
        return view('v_kalkulasi'); // Sesuai kode bawaanmu semula
    }

    // TARUH DI SINI: Fungsi pemroses data checkout UAS
    public function buy()
    {
        // 1. Load Helper Transaksi yang baru dibuat (Task 5.2)
        helper('Transaksi');

        // Mengambil kode kupon dari form input (Task 5.3a)
        $kupon_code = $this->request->getPost('kupon_code') ?? '';

        // Hitung subtotal kotor dari data items / keranjang belanja
        $total_harga = 0;
        
        // PANDUAN: Sesuai struktur view kuis sebelumnya, data item biasanya dikirim dari session atau model cart.
        // Jika kamu menggunakan session keranjang belanja bawaan CI4:
        $items = session()->get('cart') ?? []; 
        
        // JIKA kamu menggunakan database model untuk cart, aktifkan baris ini dan matikan baris session di atas:
        // $items = $this->cartModel->findAll(); 

        if (!empty($items)) {
            foreach ($items as $item) {
                $total_harga += ($item['price'] * $item['qty']);
            }
        } else {
            // Backup alternatif: Mengambil nilai total_harga langsung dari hidden input form checkout
            $total_harga = (double) ($this->request->getPost('total_harga') ?? 0);
        }

        // 2. Hitung semua komponen tambahan menggunakan helper (Task 5.3b)
        $biaya_admin  = hitung_biaya_admin($total_harga);
        $diskon_kupon = hitung_diskon_kupon($total_harga, $kupon_code);
        $cashback     = hitung_cashback($total_harga);
        
        $ongkir = (int) ($this->request->getPost('ongkir') ?? 0);

        // Rumus Grand Total UAS: Subtotal - Diskon Kupon + Biaya Admin + Ongkir
        $grand_total = $total_harga - $diskon_kupon + $biaya_admin + $ongkir;

        // 3. Menyimpan seluruh nilai komponen baru ke database tabel transaction (Task 4 & Task 5.3c)
        $this->transactionModel->insert([
            'username'     => session()->get('username'),
            'invoice'      => 'INV-' . date('YmdHis'),
            'total_harga'  => $total_harga,
            'ongkir'       => $ongkir,
            'biaya_admin'  => $biaya_admin,
            'kupon_code'   => !empty($kupon_code) ? strtoupper($kupon_code) : null,
            'diskon_kupon' => $diskon_kupon,
            'cashback'     => $cashback,
            'grand_total'  => $grand_total,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // Bersihkan session keranjang belanja setelah checkout sukses
        session()->remove('cart');
        // Jika pakai model: $this->cartModel->clear();

        return redirect()->to('transaksi')->with('success', 'Pesanan UAS berhasil dibuat!');
    }
}