# Capstone Project: E-Commerce & Shipping System (CodeIgniter 4)

Proyek ini adalah aplikasi web E-Commerce lengkap yang dibangun menggunakan framework **CodeIgniter 4**, terintegrasi dengan **RajaOngkir API** untuk perhitungan ongkos kirim secara dinamis, sistem Keranjang Belanja, manajemen produk (CRUD), Autentikasi, RESTful API, dan perhitungan checkout tambahan untuk UAS.

## 👤 Identitas Pengembang
- **Nama**: Na'ilah Azfa Zarqarida
- **NIM**: A11.2024.15549
- **Kelompok**: A11.4408
- **Dosen Pengampu**: Danny Oka Ratmana, M.Kom

---

## 🚀 Fitur Utama Aplikasi

1. **Sistem Autentikasi (Auth Filter)**:
   - Login & Logout dengan multi-user.
   - Filter keamanan (`Filters/Auth.php`) untuk membatasi akses halaman checkout, keranjang, profile, dan manajemen produk hanya untuk user yang sudah login.
   
2. **Manajemen Produk (CRUD & PDF)**:
   - Fitur Tambah, Edit, Hapus, dan Tampil produk untuk admin.
   - Fitur ekspor/download daftar produk ke format PDF (`produk/download_pdf.php`).

3. **Keranjang Belanja (Shopping Cart)**:
   - Menambahkan produk ke keranjang belanja secara interaktif.
   - Memperbarui jumlah (quantity) produk, menghapus item tertentu, atau mengosongkan keranjang sekaligus.

4. **Integrasi RajaOngkir API**:
   - Pencarian kota/kelurahan tujuan pengiriman secara dinamis menggunakan library **Select2** pada halaman checkout.
   - Mengambil tarif ongkos kirim (ongkir) kurir JNE secara realtime menggunakan web service RajaOngkir (`Services/RajaOngkirService.php`).

5. **Perhitungan Tambahan Checkout (Fitur UAS)**:
   - **Biaya Admin**: Dihitung otomatis (0.5% jika total belanja $\le$ Rp 20jt, atau 0.75% jika total belanja $>$ Rp 20jt).
   - **Kupon Promo**: Memotong harga belanja (Kupon `HEMAT` memotong 15%, kupon `SUPER` memotong 20%).
   - **Cashback**: Memberikan cashback 2% ke pelanggan jika total belanja awal lebih dari Rp 10.000.000.
   - Perhitungan berjalan secara dinamis menggunakan frontend JavaScript (jQuery) dan disimpan secara aman di database oleh Backend Controller.

6. **Riwayat Transaksi (History)**:
   - Menampilkan daftar transaksi masa lalu yang pernah dilakukan oleh user yang sedang login beserta status pembayarannya.

7. **Web Service (RESTful API)**:
   - Menyediakan endpoint API untuk data produk (`api/products`) dan data transaksi (`api/transactions`).

---

## 📂 Penjelasan Struktur Folder & File Utama

Berikut adalah penjelasan fungsi folder dan berkas utama di dalam proyek ini:

```text
belajar-azfa/
├── app/
│   ├── Config/
│   │   ├── Autoload.php             # Registrasi namespace, library, dan autoload helper
│   │   ├── Database.php             # Konfigurasi koneksi database MySQL
│   │   ├── Filters.php              # Mendaftarkan filter auth keamanan rute aplikasi
│   │   └── Routes.php               # Konfigurasi pemetaan URL/rute ke Controller
│   │
│   ├── Controllers/
│   │   ├── AuthController.php       # Mengatur alur login admin & user serta logout
│   │   ├── ProdukController.php     # Logika CRUD produk dan export PDF
│   │   ├── TransaksiController.php  # Proses checkout, checkout logic, riwayat, & AJAX RajaOngkir
│   │   ├── Keranjang.php            # Logika manipulasi item belanja (Cart library)
│   │   └── Api/
│   │       ├── ProdukController.php    # RESTful API Endpoint untuk data Produk
│   │       └── TransaksiController.php # RESTful API Endpoint untuk data Transaksi
│   │
│   ├── Database/
│   │   ├── Migrations/              # File blueprint untuk membuat & mengubah tabel DB
│   │   │   ├── ..._Product.php
│   │   │   ├── ..._Transaction.php
│   │   │   ├── ..._TransactionDetail.php
│   │   │   ├── ..._User.php
│   │   │   └── ..._UpdateTransactionTableUas.php # Migration field perhitungan tambahan
│   │   └── Seeds/                   # Data awal (seeder) database untuk user & produk
│   │       ├── ProductSeeder.php
│   │       └── UserSeeder.php
│   │
│   ├── Filters/
│   │   └── Auth.php                 # Middleware/Filter pengecekan session login user
│   │
│   ├── Helpers/
│   │   ├── DiskonHelper.php         # Helper pembantu kalkulasi diskon retail
│   │   └── TransaksiHelper.php      # Helper UAS untuk biaya admin, kupon, dan cashback
│   │
│   ├── Models/
│   │   ├── ProductModel.php         # Representasi data tabel produk
│   │   ├── TransactionModel.php     # Representasi data tabel transaksi
│   │   ├── TransactionDetailModel.php # Representasi data tabel detail transaksi
│   │   └── UserModel.php            # Representasi data tabel pengguna/users
│   │
│   ├── Services/
│   │   └── RajaOngkirService.php    # Penghubung aplikasi ke API RajaOngkir (cURL)
│   │
│   └── Views/
│       ├── layout.php               # Template layout utama aplikasi (Navbar, Sidebar)
│       ├── v_login.php              # Halaman login user/admin
│       ├── v_home.php               # Catalog produk & tombol beli
│       ├── v_keranjang.php          # Review item keranjang belanja
│       ├── v_checkout.php           # Form isi alamat + ongkir + kupon & total belanja
│       ├── v_history.php            # Tampilan riwayat pembelian user
│       └── produk/
│           ├── index.php            # Panel admin list produk (CRUD)
│           └── download_pdf.php     # Tampilan cetak PDF laporan produk
│
├── public/                          # Folder publik (Aset CSS, JS, Image, dan index.php)
├── writable/                        # Folder log, session data, dan cache sistem CI4
└── README.md                        # Panduan dokumentasi proyek
```

---

## 🛠️ Panduan Instalasi & Konfigurasi

### 1. Prasyarat Sistem
- Web Server (Apache & MySQL) seperti **XAMPP**.
- PHP Versi 8.2 atau di atasnya.
- Composer terinstall di perangkat Anda.

### 2. Kloning & Pemasangan Dependensi
Buka terminal di folder web server Anda (misal `htdocs/`) dan lakukan instalasi composer:
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `env` menjadi `.env` di root project Anda:
```bash
cp env .env
```
Buka file `.env` tersebut dan sesuaikan konfigurasi database dan API Anda:
```env
# Mode Aplikasi
CI_ENVIRONMENT = development

# URL Aplikasi
app.baseURL = 'http://localhost:8080/'

# Konfigurasi Database
database.default.hostname = localhost
database.default.database = nama_database_anda
database.default.username = root
database.default.password = 

# RajaOngkir API Key (Masukkan API Key Anda di sini)
rajaongkir.key = 'isi_dengan_api_key_rajaongkir_anda'
```

### 4. Migrasi & Pengisian Data Awal (Seeding)
Jalankan migrasi untuk membuat seluruh tabel dan seeder untuk data awal:
```bash
php spark migrate
php spark db:seed UserSeeder
php spark db:seed ProductSeeder
```

*Catatan: Jika php CLI Anda di bawah versi 8.2, Anda bisa mengimpor struktur database secara manual lewat file SQL di phpMyAdmin.*

### 5. Menjalankan Server Lokal
Jalankan perintah berikut untuk mengaktifkan server bawaan CodeIgniter:
```bash
php spark serve
```
Akses aplikasi melalui browser di alamat: `http://localhost:8080`

### 🔑 Akun Login Bawaan (Default Seeder)
- **Username**: `admin`
- **Password**: `admin123`
