<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionDetailModel extends Model
{
    // 1. PERBAIKAN: Ubah nama tabel agar sesuai dengan phpMyAdmin
    protected $table            = 'transaction_detail'; 
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Ubah ke false jika di phpMyAdmin tidak ada kolom deleted_at
    protected $protectFields    = true;
    
    // Pastikan field diizinkan sesuai dengan kebutuhan aplikasi kamu
    protected $allowedFields    = ['transaction_id', 'product_id', 'jumlah', 'diskon', 'subtotal_harga'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Mengambil detail produk berdasarkan array dari transaction_id
     */
    public function getProductsByTransactionIds(array $transactionIds)
    {
        if (empty($transactionIds)) {
            return [];
        }

        // 2. PENYEMPURNAAN: Menggunakan $this->table agar otomatis dinamis mengikuti properti model
        $details = $this->select($this->table . '.*, product.nama, product.harga, product.foto')
            ->join('product', $this->table . '.product_id = product.id')
            ->whereIn('transaction_id', $transactionIds)
            ->findAll();

        $products = [];

        foreach ($details as $detail) {
            $products[$detail['transaction_id']][] = $detail;
        }

        return $products;
    }
}