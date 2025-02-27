# Aplikasi Perpustakaan

Aplikasi Perpustakaan ini dibuat sebagai Uji Sertifikasi Kompetensi (USK) dalam pengembangan aplikasi berbasis web menggunakan PHP (Native). Sistem ini memungkinkan pengguna untuk melakukan peminjaman dan pengelolaan buku dengan dua peran utama, yaitu sebagai Petugas dan Pengunjung.

## Hak Akses Aplikasi

### Petugas

- Petugas dapat akses dalam melakukan pengelolaan buku (Create, Read, Update, Delete).
- Petugas dapat mengatur apakah buku sudah dikembalikan atau belum pada halaman laporan Daftar Peminjaman buku.

### Pengunjung

- Pengunjung dapat melihat daftar buku yang masih tersedia.
- Pengunjung dapat meminjam buku.
- Pengunjung dapat melihat riwayat peminjaman buku.

## Fitur Utama

### Petugas:

- CRUD (Create, Read, Update, Delete) data buku.
- Melihat daftar peminjaman buku.
- Mengelola status peminjaman buku.

### Pengunjung:

- Melihat daftar buku yang tersedia.
- Meminjam buku yang tersedia.
- Melihat riwayat peminjaman.

## Teknologi yang Digunakan

- Frontend: HTML, CSS, Bootstrap
- Backend: PHP (Native)
- Database: MySQL

# Instalasi dan Penggunaan

### 1. Import Database

- Buka phpMyAdmin di browser.
- Buat database baru dengan nama perpustakaan.
- Import file perpustakaan.sql yang tersedia dalam folder databases diatas.

### 2. Jalankan Server

- Gunakan XAMPP, laragon, atau server PHP lokal.
- Pindahkan proyek ke dalam folder htdocs (jika menggunakan XAMPP) atau ke dalam folder www (jika menggunakan laragon).
- Aktifkan dengan cara klik "Start All"

### 3. Akses Aplikasi

- Buka browser dan ketik http://localhost/perpustakaan/ untuk mengakses aplikasi.
- Login sebagai Petugas atau Pengunjung untuk mengakses fitur masing-masing.

### Akun

- Petugas:
  - Username: Petugas
  - Password: admin
- Pengunjung:
  - Username: Pengunjung
  - Password: tamu

<hr>
_Proyek ini dibuat untuk keperluan latihan Uji Sertifikasi Kompetensi (USK) dan dapat digunakan serta dimodifikasi sesuai kebutuhan._
