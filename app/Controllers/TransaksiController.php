<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface; 
use App\Services\RajaOngkirService;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        helper(['number', 'form', 'TransaksiHelper']);
        $this->cart = service('cart');
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel(); 
    }

    public function index()
    {  
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total() 
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert([
            'id'      => $this->request->getPost('id'),
            'qty'     => 1,
            'price'   => $this->request->getPost('harga'),
            'name'    => $this->request->getPost('nama'),
            'options' => [
                'foto' => $this->request->getPost('foto')
            ]
        ]);
        
        session()->setFlashdata(
            'success',
            'Produk berhasil ditambahkan ke keranjang. 
            <a href="' . base_url('keranjang') . '">Lihat</a>'
        );
        
        return redirect()->to(base_url('/'));
    } 

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $item) {
            $qty = $this->request->getPost('qty' . $i++);

            $this->cart->update([
                'rowid' => $item['rowid'],
                'qty'   => $qty
            ]);
        }

        session()->setFlashdata(
            'success',
            'Keranjang berhasil diperbarui'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);

        session()->setFlashdata(
            'success',
            'Produk berhasil dihapus dari keranjang'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();

        session()->setFlashdata(
            'success',
            'Keranjang berhasil dikosongkan'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {  
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total() 
        ];

        return view('v_checkout', $data);
    }

    // Fungsi Destinations: Format baku Select2 (id & text)
    public function destinations()
    {
        $search = $this->request->getGet('q'); 

        $service = new RajaOngkirService();
        $response = $service->getDestination($search);
        
        $results = [];
        $data = $response['data'] ?? [];
        
        foreach ($data as $item) {
            $results[] = [
                'id'   => $item['id'],
                'text' => $item['label']
            ];
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    // Fungsi Services: Dikembalikan penuh ke Format Panjang bawaan Modul Dosen
    public function services()
    {
        $destination = $this->request->getGet('destination');

        if (!$destination) {
            return $this->response->setJSON([]);
        }

        $origin      = '65005'; // Pendrikan Kidul, Semarang
        $weight      = 1000;    
        $courier     = 'jne'; 

        $service = new RajaOngkirService();
        $response = $service->getCost($origin, $destination, (int)$weight, $courier); 
        
        $results = [];
        $data = $response['data'] ?? [];

        if (!empty($data)) {
            foreach ($data as $item) {
                // FORMAT PANJANG: Menggabungkan Nama Kurir + Kode Layanan + Estimasi Hari
                $results[] = [
                    'service' => $item['service'],
                    'name'    => $item['name'] . ' (' . $item['service'] . ') : estimasi ' . $item['etd'],
                    'cost'    => $item['cost']
                ];
            }
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function buy()
{ 
    $cartItems = $this->cart->contents();

    if (empty($cartItems)) {
        return redirect()->back();
    }

    $db = \Config\Database::connect();
    $db->transStart(); 

    $total_harga = 0;
    foreach ($cartItems as $item) {
        $total_harga += $item['qty'] * $item['price'];
    }

    $ongkir = (int) $this->request->getPost('ongkir');
    $kupon_code = $this->request->getPost('kupon_code');

    // Hitung komponen UAS
    $biaya_admin   = hitung_biaya_admin($total_harga);
    $diskon_kupon  = hitung_diskon_kupon($total_harga, $kupon_code);
    $cashback      = hitung_cashback($total_harga);
    $grand_total   = $total_harga - $diskon_kupon + $biaya_admin + $ongkir;

    $transaction = [
        'username'     => $this->request->getPost('username'),
        'alamat'       => $this->request->getPost('alamat'),
        'ongkir'       => $ongkir,
        'total_harga'  => $total_harga,
        'biaya_admin'  => $biaya_admin,
        'kupon_code'   => $kupon_code ?: null,
        'diskon_kupon' => $diskon_kupon,
        'cashback'     => $cashback,
        'status'       => 0, 
    ];

    // insert transaction
    if (!$this->transactionModel->insert($transaction)) {
        $db->transRollback();
        return redirect()->back()->with('error', 'Gagal membuat transaksi');
    }

    $transactionId = $this->transactionModel->getInsertID();

    // insert transaction detail
    foreach ($cartItems as $item) {
        $this->transactionDetailModel->insert([
            'transaction_id' => $transactionId,
            'product_id'     => $item['id'],
            'jumlah'         => $item['qty'],
            'diskon'         => 0,
            'subtotal_harga' => $item['qty'] * $item['price'] 
        ]);
    }

    $db->transComplete();

    if (!$db->transStatus()) {
        return redirect()->back()->with('error', 'Gagal membuat transaksi');
    }
    

		//hapus session keranjang belanja 
    $this->cart->destroy();
    return redirect()->to(base_url());
}

public function history()
{
    $username = session()->get('username'); 
    $transactions = $this->transactionModel->where('username', $username)->findAll();
    $transactionIds = array_column($transactions, 'id');

    $products = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);

    $data = [
        'username'      => $username,
        'transactions'  => $transactions,
        'products'      => $products
    ]; 

    return view('v_history', $data);
}

}