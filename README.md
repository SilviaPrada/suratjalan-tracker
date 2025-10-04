# 🚚 Sistem Tracking Surat Jalan Pengiriman

Proyek ini adalah implementasi **Tes Coding Staff Programmer – Nomor 3**.  
Aplikasi berbasis **Laravel** untuk mengelola dan melacak **Surat Jalan Pengiriman**, dengan fitur **auto-generate kode unik, QR Code, update lokasi, upload bukti serah terima, dan tampilan peta lokasi terkini**.

---

## ✨ Fitur Utama
- Auto-generate **kode unik** surat jalan (mis: `SJ-20251004-ABCDE`).
- Generate **QR Code** untuk setiap surat jalan.
- Scan QR Code → update lokasi terbaru via **Geolocation API**.
- Simpan riwayat lokasi (tracking perjalanan).
- Upload bukti serah terima: **foto, nama penerima, tanggal & waktu**.
- Tampilkan **peta lokasi terkini** menggunakan **Leaflet (OpenStreetMap)**.
- Status pengiriman: `created → in_transit → delivered`.
- Dark mode frontend menggunakan Tailwind
---

## 🛠️ Teknologi
- **Framework**: Laravel 11.x
- **Frontend**: Blade + TailwindCSS
- **Database**: MySQL / MariaDB
- **QR Code**: [simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- **Peta**: Leaflet.js (OpenStreetMap)
- **File Upload**: Laravel Storage (Public)

---

## 📦 Requirement
- PHP 8.2+
- Composer
- MySQL / MariaDB

---

## ⚙️ Instalasi

1. **Clone repository**

   ```bash
   git clone https://github.com/SilviaPrada/suratjalan-tracker.git
   cd tracking-surat-jalan
   ```

2. **Install dependensi PHP & JavaScript**

   ```bash
   # Install library Laravel
   composer install

   # Install library frontend (Tailwind, Vite, dsb)
   npm install
   ```

3. **Install library tambahan yang diperlukan**

   ```bash
   # Library untuk generate QR Code
   composer require simplesoftwareio/simple-qrcode
   ```

4. **Salin & konfigurasikan file environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Buka file `.env` dan sesuaikan konfigurasi database:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tracking
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Siapkan database**
   Jalankan migrasi sekaligus seeder untuk membuat tabel dan contoh data:

   ```bash
   php artisan migrate --seed
   ```

   > Seeder otomatis akan membuat 1–2 contoh *surat jalan* lengkap dengan lokasi dummy.

6. **Link storage (untuk upload foto bukti)**

   ```bash
   php artisan storage:link
   ```

7. **Build Tailwind & Asset Frontend**
   Karena UI menggunakan TailwindCSS dengan Vite, jalankan:

   ```bash
   # Jalankan development server Vite (hot reload)
   npm run dev

   # Atau build production
   npm run build
   ```

8. **Jalankan server Laravel**

   ```bash
   php artisan serve
   ```

   Buka browser di:

   ```
   http://localhost:8000
   ```
---

## 🚀 Cara Penggunaan

### 1. Login ke Sistem
- **Admin**  
  - Email: `admin@example.com`  
  - Password: `password123`  
  - Hak akses: Membuat, melihat, dan menghapus Surat Jalan.  

- **Kurir**  
  - Email: `kurir@example.com`  
  - Password: `password123`  
  - Hak akses: Melihat Surat Jalan, update lokasi, dan upload bukti serah terima.  

---

### 2. Alur Admin
1. **Buat Surat Jalan**  
   - Akses: `+ Buat Surat Jalan`  
   - Isi data pengiriman (Sender, Receiver, Deskripsi, Lokasi( Auto generate)).  
   - Sistem akan menyimpan data ke database dan menghasilkan **kode unik + QR Code**.  

2. **Lihat Daftar & Detail Surat Jalan**  
   - Akses Dashboard dan pilih `Detail` salah satu surat jalan.  
   - Data ditampilkan: status, lokasi terakhir, riwayat lokasi, penerima (jika sudah terkirim).  
   - **Catatan:** Admin tidak bisa update lokasi.  

3. **Hapus Surat Jalan**  
   - Klik tombol **Delete** pada daftar surat jalan.   

4. **Logout**  
   - Klik **Logout** untuk mengakhiri sesi.  

---

### 3. Alur Kurir
1. **Scan QR Surat Jalan**  
   - Akses Dashboard dan pilih `Detail` salah satu surat jalan. `.  
   - Akan menampilkan detail Surat Jalan: status, qr code, lokasi, dan penerima.  

2. **Update Lokasi Terkini**  
   - Tekan tombol **Kirim Lokasi Saat Ini**.  
   - Data GPS akan dikirim ke `/api/surat/update-location` dan disimpan ke `delivery_locations`.  

3. **Upload Bukti Serah Terima (Proof of Delivery)**  
   - Akses: `/surat/{id}/upload-proof`.  
   - Upload foto, isi nama penerima, dan waktu.  
   - Sistem akan menyimpan data ke `delivery_proofs` dan status berubah menjadi **Delivered**.  

4. **Lihat Status Surat Jalan**  
   - Akses `Detail` untuk melihat status, lokasi terkini, dan bukti serah terima (jika sudah ada).  

5. **Logout**  
   - Klik **Logout** untuk mengakhiri sesi.  

---

### 4. Tracking via Peta
- Sistem menggunakan **Leaflet.js / Mapbox** untuk menampilkan lokasi kurir.  
- Lokasi terbaru kurir diambil dari route:  
```

/surat/{id}/locations/latest

```
- Peta akan menampilkan posisi kurir secara **real-time** berdasarkan data dari `delivery_locations`.  

## 📜 Struktur Database

* **surat_jalans**
  Menyimpan data utama surat jalan.
* **delivery_locations**
  Menyimpan riwayat lokasi update.
* **delivery_proofs**
  Menyimpan bukti serah terima (foto, nama penerima, waktu).

---
Project ini dibuat sebagai bagian dari **Tes Coding Staff Programmer (2025)**.

```
