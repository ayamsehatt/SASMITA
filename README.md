# SASMITA — Sistem Asesmen, Pembelajaran, dan Analitik Kelas

**SASMITA** adalah aplikasi manajemen pembelajaran dan analitik kelas berbasis web yang dibangun menggunakan framework **CodeIgniter 4**. Aplikasi ini dirancang untuk membantu guru/pengajar dalam mengelola Perencanaan Pembelajaran (RPS), mencatat kehadiran serta nilai siswa, membuat dan mengekspor lembar asesmen ke format PDF, serta mengelola profil pengajar.

---

## 🚀 Fitur Utama

- 🔐 **Autentikasi & Dasbor**: Sistem masuk (login) terproteksi dan halaman dasbor ringkasan statistik kelas.
- 📚 **Manajemen RPS (Perencanaan Pembelajaran)**: Pengelolaan rencana pembelajaran, bab/materi, dan unduh sumber belajar.
- 📊 **Analitik Kelas**:
  - **Manajemen Siswa**: Tambah dan hapus data siswa per kelas.
  - **Kehadiran (Absensi)**: Input dan rekap kehadiran harian (Hadir, Sakit, Izin, Alfa).
  - **Penilaian**: Pencatatan dan rekap nilai siswa berdasarkan jenis asesmen (Tugas, Formatif, Sumatif).
- 📝 **Pembuat Asesmen & Cetak PDF**: Pembuatan draft lembar soal/asesmen dan fitur cetak/ekspor langsung ke format PDF (*Dompdf*).
- ⚙️ **Pengaturan Profil**: Manajemen profil pengajar yang tersimpan secara dinamis di database.

---

## 🗄️ Struktur Database

Database aplikasi (`db_sasmita`) terdiri dari tabel-tabel utama berikut:

| Tabel | Keterangan | Relasi |
| :--- | :--- | :--- |
| `siswa` | Menyimpan data identitas siswa (`nis`, `name`, `class_name`). | Berelasi ke `kehadiran` dan `nilai`. |
| `kehadiran` | Menyimpan rekap presensi harian siswa (`student_id`, `attendance_date`, `status`). | Foreign Key `student_id` $\rightarrow$ `siswa.id`. |
| `asesmen` | Menyimpan data judul dan tipe asesmen (`title`, `type`, `assessment_date`). | Berelasi ke `nilai`. |
| `nilai` | Menyimpan skor siswa per asesmen (`assessment_id`, `student_id`, `score`). | Foreign Key `assessment_id` $\rightarrow$ `asesmen.id`, `student_id` $\rightarrow$ `siswa.id`. |
| `pengaturan` | Menyimpan konfigurasi global dan profil guru (*Key-Value Store*). | Tabel independen (tanpa relasi). |
| `migrations` | Catatan riwayat versi migrasi database bawaan CodeIgniter 4. | Tabel internal framework (tanpa relasi). |

---

## 💻 Kebutuhan Sistem

- **PHP**: versi 8.2 atau lebih baru
  - Ekstensi PHP yang wajib aktif: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`
- **Composer**: versi 2.x
- **Database**: MySQL 5.7+ atau MariaDB 10.3+

---

## ⚙️ Panduan Instalasi (Lokal)

### 1. Clone / Unduh Repository
```bash
git clone <repository-url>
cd SASMITA
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `env` menjadi `.env`:
```bash
cp env .env
```
Buka file `.env` dan sesuaikan pengaturan database Anda:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = 127.0.0.1
database.default.database = db_sasmita
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 4. Persiapan Database & Migrasi
Buat database bernama `db_sasmita` di MySQL / phpMyAdmin Anda, kemudian jalankan perintah migrasi dan seeder:

```bash
# Jalankan migrasi tabel
php spark migrate

# (Opsional) Jalankan seeder data awal
php spark db:seed KelasSeeder
```

### 5. Jalankan Server Lokal
```bash
php spark serve
```
Buka peramban (browser) Anda dan akses: `http://localhost:8080`

---

## 📦 Panduan Deployment (Production)

Saat meng-deploy ke server *production* (Shared Hosting / VPS):
1. **Optimasi Composer**:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
2. **Ubah Mode Environment**: Set `CI_ENVIRONMENT = production` di file `.env` server.
3. **Document Root**: Arahkan root web server (Apache/Nginx/cPanel) langsung ke folder **`public/`** demi keamanan file aplikasi.
4. **Izin Folder**: Pastikan folder `writable/` memiliki izin akses *write* (`775` / `777`).

---

## 🛠️ Teknologi & Library

- **Framework**: [CodeIgniter 4.7](https://codeigniter.com)
- **PDF Generator**: [Dompdf 3.1](https://github.com/dompdf/dompdf)
- **Language**: PHP 8.2+ & JavaScript
- **Database**: MySQL / MariaDB
