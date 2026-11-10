# Panduan Praktikum & Laporan Minggu 11
## Acara 35, 36, 37, dan 38 (Laravel Print ESC/POS & Midtrans Payment Gateway)

---

### A. Capaian Pembelajaran & Ringkasan Acara

1. **Acara 35: Laravel Print (Dasar ESC/POS)**
   - Menggunakan library `mike42/escpos-php`.
   - Mengirim perintah cetak struk kasir melalui protokol ESC/POS (Epson Standard Code for Point of Sale).
   - Format teks: Tebal (Bold), rata tengah (Center), underline, pemotongan kertas (Cut), dan QR Code.
   - Mode operasi: `FilePrintConnector` (simulasi file tanpa printer fisik) dan `WindowsPrintConnector` (printer hardware USB/Windows).

2. **Acara 36: Laravel Print (2) (Studi Kasus Toko "Kopi Kita" & Auto-Print POS)**
   - Studi kasus toko kopi dan sistem kasir POS web.
   - Pencetakan struk transaksi secara otomatis dari backend PHP setelah transaksi disimpan (tanpa dialog browser).
   - Format struk minimal: Nama Toko, Tanggal & Waktu, Daftar Item (Qty & Harga), Total Pembayaran, dan Diskon.
   - Terintegrasi langsung dengan menu POS Kasir Minimarket Polije.

3. **Acara 37: Payment Gateway Midtrans (Konfigurasi & SDK)**
   - Menggunakan library `midtrans/midtrans-php`.
   - Konfigurasi environment Sandbox (`.env`) dan file konfigurasi `config/midtrans.php`.
   - Inisialisasi parameter Midtrans Config dan verifikasi algoritma Signature SHA-512.

4. **Acara 38: Payment Gateway Midtrans (2) (Snap Checkout & Webhook Orders)**
   - Database: Tabel `orders` untuk mencatat status transaksi digital (`pending`, `PAID`, `FAILED`).
   - Endpoint `/checkout`: Menghasilkan Midtrans Snap Token untuk pop-up pembayaran.
   - Endpoint `/midtrans/notification`: Webhook notifikasi server-to-server untuk verifikasi otomatis status pembayaran dari Midtrans (dikecualikan dari proteksi CSRF di `bootstrap/app.php`).
   - Frontend interaktif: Katalog produk digital dengan tombol "Bayar Sekarang" dan integrasi `snap.js`.

---

### B. Endpoint & Cara Pengujian

Jalankan server Laravel:
```bash
php artisan serve
```

| Acara | Fitur / Halaman | URL / Endpoint | Method | Keterangan |
| :--- | :--- | :--- | :---: | :--- |
| **Acara 35** | Cetak Struk Dasar | `http://localhost:8000/print-receipt` | `GET` | Cetak struk toko contoh via ESC/POS |
| **Acara 35** | Custom Formatting | `http://localhost:8000/print-receipt/custom` | `GET` | Format Bold, Center, Underline, dan QR Code |
| **Acara 36** | Struk Toko Kopi Kita | `http://localhost:8000/kopi-kita/print` | `GET` | Cetak struk Kopi Kita sesuai ketentuan modul |
| **Acara 36** | Transaksi Kasir POS | `http://localhost:8000/transactions/create` | `GET/POST` | POS Kasir (struk tercetak otomatis saat bayar) |
| **Acara 37** | Konfigurasi SDK | `config/midtrans.php` | - | Pengaturan Server Key & Client Key |
| **Acara 38** | Katalog Produk Digital | `http://localhost:8000/digital-products` | `GET` | Halaman demo checkout produk digital Snap |
| **Acara 38** | Request Snap Token | `http://localhost:8000/checkout` | `POST` | Generate token transaksi dari backend |
| **Acara 38** | Webhook Notifikasi | `http://localhost:8000/midtrans/notification` | `POST` | Webhook status pembayaran Midtrans |

---

### C. Lokasi File Struk Hasil Cetak (Mode Simulasi)

Karena pengujian dilakukan tanpa printer fisik, semua byte ESC/POS tersimpan langsung di folder:
```
minimarket/storage/app/receipts/
```
File yang dihasilkan antara lain:
- `struk_acara35.txt`
- `struk_custom_formatting.txt`
- `struk_kopi_kita_*.txt`
- `struk_pos_TRX-*.txt`

#### Contoh Tampilan Struk Toko "Kopi Kita" (Acara 36):
```
          KOPI KITA
 Jl. Danau Toba No. 45, Jember
      Telp: 0812-9876-5432
================================
No Struk : KK-20261110-001
Kasir    : Budi Setiawan
Waktu    : 10/11/2026 15:46:16
--------------------------------
Kopi Susu Gula Aren             
  2 x 18.000              36.000
Americano Ice                   
  1 x 15.000              15.000
Croissant Cokelat               
  2 x 12.500              25.000
--------------------------------
Subtotal                Rp76.000
Diskon                  -Rp5.000
TOTAL BAYAR             Rp71.000
Tunai/Bayar            Rp100.000
Kembalian               Rp29.000
================================
Terima Kasih atas Kunjungan Anda!
Nikmati Harimu Bersama Kopi Kita
[QR Code & Cut Paper]
```

---

### D. Perintah Menjalankan Unit & Feature Test

Untuk memverifikasi semua pengujian otomatis modul:
```bash
php artisan test --filter="ReceiptTest|KopiKitaTest|MidtransConfigTest|MidtransCheckoutTest"
```
Hasil: **12 tests passed, 31 assertions, 0 failure**.

---

### E. Kesimpulan untuk Laporan Praktikum

1. **ESC/POS & Laravel Print (`mike42/escpos-php`)**:
   Penggunaan protokol ESC/POS memungkinkan aplikasi web Laravel berkomunikasi secara langsung ke printer thermal struk dari backend tanpa memerlukan dialog cetak browser (`window.print()`). Fleksibilitas konektor (`FilePrintConnector` dan `WindowsPrintConnector`) memudahkan proses pengembangan dan pengujian otomatis tanpa ketergantungan wajib pada perangkat keras printer fisik.

2. **Payment Gateway Midtrans (`midtrans/midtrans-php`)**:
   Integrasi payment gateway pada Laravel memisahkan alur transaksi menjadi dua sisi: pembuatan token transaksi (`Snap::getSnapToken`) di sisi frontend dan penanganan callback asinkron melalui endpoint webhook (`/midtrans/notification`) di sisi backend. Pengecualian CSRF pada endpoint webhook dan verifikasi status serta signature key menjamin pencatatan transaksi yang aman, otomatis, dan *real-time*.

