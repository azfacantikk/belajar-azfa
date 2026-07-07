# UAS PWL: Checkout & Perhitungan Tambahan (Capstone Project)

Proyek ini adalah implementasi sistem checkout dengan perhitungan tambahan untuk **Ujian Akhir Semester (UAS) Genap 2025/2026** mata kuliah Pemrograman Web Lanjut (PWL).

- **Nama**: Na'ilah Azfa Zarqarida
- **NIM**: A11.2024.15549
- **Kelompok**: A11.4408
- **Dosen Pengampu**: Danny Oka Ratmana, M.Kom

---

## 🛠️ Fitur Perhitungan Tambahan
Aplikasi menghitung dan menyimpan komponen keuangan transaksi berikut ke database secara otomatis:
1. **Biaya Admin**:
   - Total Belanja $\le$ Rp 20.000.000 $\rightarrow$ Tarif **0.5%**
   - Total Belanja $>$ Rp 20.000.000 $\rightarrow$ Tarif **0.75%**
2. **Kupon Diskon**:
   - `HEMAT` $\rightarrow$ Potongan **15%**
   - `SUPER` $\rightarrow$ Potongan **20%**
   - Kode Lainnya / Invalid $\rightarrow$ Potongan **0%**
3. **Cashback**:
   - Total Belanja $>$ Rp 10.000.000 $\rightarrow$ Cashback **2%**
   - Total Belanja $\le$ Rp 10.000.000 $\rightarrow$ Cashback **0%**

---

## 📂 Struktur File Implementasi UAS
Berikut adalah file utama yang dimodifikasi dan ditambahkan untuk mengimplementasikan fitur UAS:

```text
belajar-azfa/
├── app/
│   ├── Controllers/
│   │   └── TransaksiController.php  <-- Update buy() & checkout() logic
│   ├── Helpers/
│   │   └── TransaksiHelper.php      <-- Core logic hitung biaya admin, diskon, cashback
│   ├── Models/
│   │   └── TransactionModel.php     <-- Update $allowedFields untuk menyimpan data uas
│   ├── Database/
│   │   └── Migrations/
│   │       └── 2026-07-03-063753_UpdateTransactionTableUas.php <-- Migration field baru
│   └── Views/
│       └── v_checkout.php           <-- Input kupon + tabel realtime breakdown harga (jQuery)
└── README.md                        <-- Dokumentasi proyek
```

---

## ⚙️ Cara Menjalankan & Migrasi Database
Jika Anda menggunakan XAMPP, jalankan MySQL & Apache terlebih dahulu.

### 1. Migrasi Database
Gunakan PHP CLI versi 8.2 atau di atasnya untuk menjalankan migrasi:
```bash
php spark migrate
```

Jika CLI PHP menggunakan versi lama (di bawah 8.2), jalankan query SQL berikut di menu **SQL** phpMyAdmin database Anda:
```sql
ALTER TABLE `transaction`
ADD COLUMN `biaya_admin` DOUBLE NULL AFTER `ongkir`,
ADD COLUMN `kupon_code` VARCHAR(20) NULL AFTER `biaya_admin`,
ADD COLUMN `diskon_kupon` DOUBLE NULL AFTER `kupon_code`,
ADD COLUMN `cashback` DOUBLE NULL AFTER `diskon_kupon`;
```

### 2. Jalankan Aplikasi
```bash
php spark serve
```
Buka `http://localhost:8080` di browser Anda.

---

## 🧪 Skenario Pengujian (Test Cases)
Verifikasi perhitungan checkout dilakukan dengan skenario berikut:

| # | Skenario Produk | Total Belanja | Kupon | Biaya Admin | Diskon Kupon | Cashback |
|---|---|---|---|---|---|---|
| **1** | 1x Lenovo | Rp 6.299.000 | - | Rp 31.495 (0.5%) | Rp 0 (0%) | Rp 0 (0%) |
| **2** | 2x Vivo | Rp 13.798.000 | `HEMAT` | Rp 68.990 (0.5%) | Rp 2.069.700 (15%) | Rp 0 (0%) |
| **3** | 3x ASUS TUF | Rp 32.697.000 | `SUPER` | Rp 245.228 (0.75%) | Rp 6.539.400 (20%) | Rp 653.940 (2%) |
| **4** | 3x ASUS TUF | Rp 32.697.000 | `KODESALAH` | Rp 245.228 (0.75%) | Rp 0 (0%) | Rp 653.940 (2%) |
| **5** | 5x ASUS TUF | Rp 54.495.000 | `SUPER` | Rp 408.713 (0.75%) | Rp 10.899.000 (20%) | Rp 1.089.900 (2%) |
