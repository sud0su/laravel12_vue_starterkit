# Laravel Starter KIT

Aplikasi ini dibangun dengan Laravel, Vue.js, dan Inertia.js.

## Tentang Proyek

Proyek ini adalah sebuah starter kit untuk membangun aplikasi web modern dengan fungsionalitas dasar yang sudah terpasang, termasuk manajemen pengguna, peran (roles), dan hak akses (permissions). Aplikasi ini menggunakan stack TALL (Tailwind, Alpine.js - digantikan oleh Vue.js, Laravel, Livewire - digantikan oleh Inertia.js) yang dimodifikasi, dengan Vue.js sebagai framework frontend.

## Fitur Utama

-   **Backend Laravel 12**: Menggunakan versi terbaru dari framework PHP paling populer.
-   **Frontend Vue.js 3 & Vite**: Dibangun dengan Vue.js 3 Composition API dan di-bundle dengan Vite untuk development yang super cepat.
-   **Inertia.js**: Membuat aplikasi single-page (SPA) tanpa perlu membangun API terpisah.
-   **Manajemen Hak Akses**: Sudah terintegrasi dengan `spatie/laravel-permission` untuk manajemen peran dan hak akses.
-   **Styling dengan Tailwind CSS**: Desain antarmuka yang modern dan responsif.
-   **Testing dengan Pest**: Konfigurasi pengujian yang elegan dan minimalis.
-   **Autentikasi**: Sistem login, registrasi, dan manajemen sesi yang sudah siap pakai.

## Teknologi yang Digunakan

-   **Backend**:
    -   PHP 8.2
    -   Laravel 12
    -   Spatie Laravel Permission
    -   Inertia.js (Adapter Laravel)
-   **Frontend**:
    -   Vue.js 3
    -   Vite
    -   TypeScript
    -   Tailwind CSS
    -   Inertia.js (Adapter Vue 3)
-   **Database**:
    -   SQLite (default)
    -   MySQL, PostgreSQL (dapat dikonfigurasi)

## Panduan Instalasi

Berikut adalah langkah-langkah untuk menginstal dan menjalankan proyek ini di lingkungan lokal Anda.

### 1. Prasyarat

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   Git

### 2. Instalasi

1.  **Clone repository:**
    ```bash
    git clone <URL_REPOSITORY_ANDA>
    cd nama-folder-proyek
    ```

2.  **Salin file environment:**
    ```bash
    cp .env.example .env
    ```

3.  **Install dependensi PHP:**
    ```bash
    composer install
    ```

4.  **Install dependensi JavaScript:**
    ```bash
    npm install
    ```

5.  **Generate kunci aplikasi:**
    ```bash
    php artisan key:generate
    ```

6.  **Konfigurasi Database:**
    Buka file `.env` dan sesuaikan konfigurasi database Anda (misalnya `DB_CONNECTION`, `DB_DATABASE`, dll). Jika Anda menggunakan SQLite (default), buat file databasenya:
    ```bash
    touch database/database.sqlite
    ```

7.  **Jalankan migrasi dan seeder:**
    Perintah ini akan membuat struktur tabel database dan mengisi data awal (jika ada seeder).
    ```bash
    php artisan migrate --seed
    ```

## Menjalankan Aplikasi

Untuk menjalankan server development (termasuk Vite, server PHP, dan queue listener), gunakan perintah berikut:

```bash
npm run dev
```

Aplikasi akan tersedia di alamat yang ditampilkan oleh `php artisan serve` (biasanya `http://127.0.0.1:8000`).

## Menjalankan Pengujian

Untuk menjalankan rangkaian pengujian otomatis (Pest), gunakan perintah:

```bash
php artisan test
```
