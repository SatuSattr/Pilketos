# Pilketos

![Pilketos](https://i.imgur.com/g4Em7K9.png)

Sistem pemilihan Ketua OSIS berbasis web. Sistem ini memiliki dua bagian:

* **Halaman Voting** (`/`) — *candidate card* yang ramah layar sentuh untuk siswa, dilindungi oleh kunci tampilan per-bilik suara
* **Panel Admin** (`/admin`) — dashboard untuk satu pengguna yang digunakan untuk mengelola kandidat, pemilih, kunci bilik suara, serta memantau hasil secara langsung

> ⚠️ Sistem ini masih dalam tahap pengembangan. Beberapa bug telah terdeteksi, tetapi belum semuanya berhasil diselesaikan. Gunakan dengan pertimbangan yang sesuai dan lakukan pengujian terlebih dahulu sebelum digunakan dalam pemilihan sebenarnya.

## Tech Stack

| Layer      | Teknologi                      |
| ---------- | ------------------------------ |
| Backend    | Laravel 13, PHP 8.4            |
| Database   | SQLite                         |
| CSS        | Tailwind CSS v4                |
| JavaScript | Alpine.js, Vite 8              |
| Grafik     | Chart.js + chartjs-plugin-zoom |
| Ikon       | Lucide                         |
| Alert      | SweetAlert2 + Notyf.js         |
| Font       | Montserrat                     |

## Local Development

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
composer run dev   # menjalankan PHP, queue worker, dan Vite secara bersamaan
```
