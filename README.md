# Sistem Informasi Pengajuan Pembayaran

## Apa itu Sistem Informasi Pengajuan Pembayaran?

Sistem ini adalah aplikasi berbasis web untuk memudahkan pengajuan dan pengelolaan pembayaran secara elektronik, dibuat menggunakan framework CodeIgniter 4 yang ringan, cepat, dan aman.

## Tentang CodeIgniter 4

CodeIgniter 4 adalah framework PHP full-stack yang ringan, cepat, fleksibel, dan aman. Informasi lebih lengkap bisa dilihat di [situs resmi CodeIgniter](https://codeigniter.com).

Framework ini menggunakan struktur modern dengan folder _public_ sebagai root web server untuk keamanan yang lebih baik.

## Persyaratan Sistem

Agar dapat menjalankan aplikasi ini, pastikan server Anda memenuhi persyaratan berikut:

- PHP versi 8.1 atau lebih tinggi
- Ekstensi PHP berikut sudah aktif:
  - intl
  - mbstring
  - json (aktif secara default)
  - mysqlnd (jika menggunakan MySQL)
  - curl (jika menggunakan library HTTP\CURLRequest)

> **Catatan:**  
> PHP versi 7.4 dan 8.0 sudah tidak didukung dan sangat disarankan untuk segera melakukan upgrade ke versi 8.1 atau lebih tinggi.

## Cara Instalasi

Ikuti langkah-langkah berikut untuk menginstal sistem ini di server Anda:

1. **Unduh atau clone repository ini ke server Anda.**

2. **Konfigurasi Web Server:**  
   Atur root directory web server Anda untuk mengarah ke folder `public` di dalam project ini.  
   Contoh konfigurasi virtual host pada Apache atau Nginx bisa ditemukan di dokumentasi CodeIgniter 4.

3. **Instalasi dependencies menggunakan Composer:**  
   Jalankan perintah berikut di direktori project (jika menggunakan Composer):

   ```bash
   composer install
   ```

4. **Konfigurasi Database:**  
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database sesuai dengan lingkungan Anda:

   ```
   database.default.hostname = localhost
   database.default.database = nama_database
   database.default.username = user_database
   database.default.password = password_database
   ```

5. **Migrasi Database:**  
   Setelah konfigurasi database selesai, jalankan migrasi database:

   ```bash
   php spark migrate
   ```

6. **Jalankan aplikasi:**  
   Anda bisa menggunakan built-in server CodeIgniter untuk pengujian lokal:
   ```bash
   php spark serve
   ```
   Lalu buka browser dan akses `http://localhost:8080`.

## Struktur Folder Penting

- `app/` — Tempat file aplikasi utama (controller, model, view, dll)
- `public/` — Folder root webserver, berisi file index.php dan aset publik
- `writable/` — Folder tempat menyimpan file yang dapat ditulis oleh sistem (cache, logs, dll)

## Support dan Kontribusi

Jika ada pertanyaan atau masalah, silakan bergabung di forum resmi CodeIgniter Indonesia atau gunakan issue tracker di repository pengembangan CodeIgniter 4.

Kami juga terbuka untuk kontribusi dari komunitas. Silakan baca panduan kontribusi di repository utama CodeIgniter 4.
