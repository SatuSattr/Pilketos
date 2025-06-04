
# 🗳️ Pilketos - Platform Digital Pemilihan Ketua OSIS

**Pilketos** adalah platform digital sederhana untuk pemilihan ketua OSIS secara online.  
Dibuat dalam rangka Sertifikasi Kompetensi (Sertikom) di sekolah.

Proyek ini bertujuan untuk membuat proses pemilihan ketua OSIS jadi lebih modern, efisien, dan transparan, dengan fitur lengkap untuk admin dan siswa.

---

## ✨ Fitur Utama

- 👤 **Voting**: Memilih calon ketua OSIS dengan input NISN.
- 🧑‍💼 **Panel Admin**:
  - Menambahkan & mengedit **data calon ketua OSIS** dan **data admin**.
  - Menambahkan **data pemilih (hak suara/NISN)**.
  - Melihat **hasil laporan pemungutan suara** lengkap.
- 📊 **Laporan**: Grafik & persentase suara otomatis dari database.

---

## 🛠️ Teknologi yang Digunakan

- 🐘 **PHP** (Plain)
- 🧬 **MySQL** (Database)
- 🌐 **HTML + JavaScript**
- 🎨 **TailwindCSS** (Desain modern & responsif)

---

## 📸 Tampilan Antarmuka

<div style="display: flex; gap: 20px;">
  <img src="https://ux.appcloud.id/imaging/images/TwYOv7EdlX.png" alt="Halaman Voting" width="48%">
  <img src="https://ux.appcloud.id/imaging/images/qBJ79qHWto.png" alt="Halaman Admin" width="48%">
  <img src="https://ux.appcloud.id/imaging/images/ZtpPG2zfwT.png" alt="Halaman Laporan" width="48%">
  <img src="https://ux.appcloud.id/imaging/images/TauK3TTrYd.png" alt="Halaman Admin" width="48%">
</div>

> 🖼️ *Kiri-Atas: Halaman Voting — Kanan-Atas: Panel Dashboard | Kiri-Bawah: Panel Laporan — Kanan-Bawah: Panel Calon*

---

## 🚀 Cara Menjalankan

1. **Clone** repository ini ke folder `\www` *jika anda pakai Laragon atau `\htdocs` jika ada pakai XAMPP
 ```bash
   git clone https://github.com/SattrFev/Pilketos-Sertikom.git
   ```
2. **Buka Terminal** di dalam folder hasil clone repository nya.
3. **Jalankan Command** via terminal *pastikan kamu sudah memiliki node.js 
```bash
   npm i
   ```
4. **Import database** dari file `db_pilketos.sql` ke phpMyAdmin atau tool database favoritmu.
5. **Edit koneksi database** di `conn.php` sesuai dengan environment kamu.
6. **Setup Tailwind** dengan menjalankan command
  ```bash
   npm run dev
   ```
7. **Buka website** dari Laragon atau Xampp lalu menuju ke `\public`
8. **Akses admin** dengan membuka `public\admin.php` lewat url fields di browser mu
9. gunakan email `admin@gmail.com` dan password `admin1232`

## 📖 Cara Menggunakan (Basic)

Platform ini memiliki 6 halaman utama untuk admin, dan 1 halaman tambahan untuk siswa (voting). Berikut penjelasannya:

### 1. Dashboard
- Admin dapat melihat daftar **calon ketua OSIS** dalam bentuk **card**.
- Tersedia statistik seperti:
  - Jumlah calon ketos
  - Total suara terkumpul
  - Persentase partisipasi (%)

### 2. Kelola Calon
- Admin bisa melakukan **CRUD (Create, Read, Update, Delete)** pada data calon ketua OSIS.
- Menambahkan calon dilakukan dengan mengisi semua field yang tersedia.
- ⚠️ **Pastikan foto calon ketos sudah disiapkan dengan background transparan (PNG).**

### 3. Konfigurasi Admin
- Halaman ini untuk melakukan CRUD terhadap data admin.
- Admin bisa menambahkan, mengubah, menghapus admin, serta melihat list admin dalam bentuk tabel.

### 4. Kelola Hak Suara
- Admin dapat mengelola hak suara siswa dengan sistem CRUD.
- Setiap hak suara menggunakan **NISN siswa** sebagai identifier unik.
- Jika NISN sudah digunakan untuk voting, maka tidak bisa digunakan kembali.

### 5. Laporan
- Menampilkan hasil voting dalam bentuk grafik dan tabel.
- Admin dapat melihat total suara yang masuk dan siapa saja yang memperoleh suara terbanyak.

### 6. Halaman Voting (Akses Terpisah)
- Halaman ini bisa diakses oleh **siswa tanpa perlu login**.
- Siswa cukup memasukkan **NISN mereka** untuk memberikan suara.
- Sistem akan mengecek apakah NISN valid dan belum digunakan untuk voting sebelumnya.

---


