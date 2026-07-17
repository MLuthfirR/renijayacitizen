# Sistem Iuran Duka Cita — RT.02/RW.06 Reni Jaya

Aplikasi web untuk membantu pengurus PKK mengelola **iuran duka cita** dan menghasilkan
laporan tahunan resmi (surat pengantar, buku besar, dan kartu iuran) secara otomatis.

Dibangun dengan **Laravel 12 + Livewire 3 + Tailwind CSS + PostgreSQL**.
Antarmuka **mobile-first** (nyaman dipakai dari HP/iPad), berbahasa Indonesia, tema terang.

---

## Fitur

- **Dashboard** — ringkasan saldo, grafik arus kas per bulan, daftar peserta yang perlu ditagih.
- **Peserta** — kelola daftar KK (nama, alamat blok/nomor, status aktif/pindah/berhenti, penanda luar lingkungan). Bisa **disalin otomatis** dari tahun sebelumnya.
- **Kartu Iuran** — grid Jan–Des; di HP berupa kartu dengan chip bulan yang tinggal di-tap. Bisa isi nominal presisi per KK (rapel/sebagian) & tandai lunas satu bulan sekaligus.
- **Santunan & Pengeluaran** — catat dana keluar; tombol cepat Rp 500rb / Rp 1 jt.
- **Laporan** — pratinjau + **export PDF** tiga dokumen resmi (kode akun, tanda tangan) mengikuti format asli.
- **Halaman Publik** — transparansi keuangan untuk seluruh warga tanpa login (root `/`).
- **Multi-tahun** — saldo akhir mengalir otomatis jadi saldo awal tahun berikutnya.

---

## Menjalankan secara lokal (macOS/Linux)

Prasyarat: PHP 8.2+, Composer, Node 20+, PostgreSQL 15+.

```bash
# 1. Dependensi
composer install
npm install

# 2. Konfigurasi
cp .env.example .env         # lalu sesuaikan DB_* (lihat di bawah)
php artisan key:generate

# 3. Database
createdb renijaya_dukacita   # atau via psql
php artisan migrate --seed   # membuat tabel + data awal 2025

# 4. Build aset & jalankan
npm run build
php artisan serve            # http://localhost:8000
```

Konfigurasi database di `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=renijaya_dukacita
DB_USERNAME=renijaya
DB_PASSWORD=
```

### Akun pengurus (hasil seeder)

| Email                        | Kata sandi     |
|------------------------------|----------------|
| `pengurus@rt2renijaya.id`    | `dukacita2025` |

> **Segera ganti kata sandi** setelah login pertama (lihat catatan keamanan di bawah).

---

## Deploy ke `rt2.mluthfirr.id`

Contoh dengan Nginx + PHP-FPM di server sendiri.

```bash
# Di server
git clone <repo> /var/www/rt2
cd /var/www/rt2
composer install --no-dev --optimize-autoloader
npm ci && npm run build

cp .env.example .env
php artisan key:generate
# Set di .env:
#   APP_ENV=production
#   APP_DEBUG=false
#   APP_URL=https://rt2.mluthfirr.id
#   DB_* sesuai PostgreSQL server
#   SESSION_SECURE_COOKIE=true

php artisan migrate --seed --force
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Izin storage
chown -R www-data:www-data storage bootstrap/cache
```

Contoh server block Nginx:

```nginx
server {
    listen 80;
    server_name rt2.mluthfirr.id;
    root /var/www/rt2/public;

    index index.php;
    charset utf-8;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

Lalu pasang HTTPS: `certbot --nginx -d rt2.mluthfirr.id`.

Setiap update kode: `git pull && composer install --no-dev && npm ci && npm run build && php artisan migrate --force && php artisan optimize`.

---

## Alur kerja tahunan (untuk pengurus)

1. **Awal tahun** → menu **Pengaturan → Periode → Baru**. Tahun & saldo awal terisi otomatis dari tahun lalu; centang "Salin daftar peserta".
2. **Sepanjang tahun** → menu **Kartu Iuran**: tap bulan untuk menandai warga yang sudah bayar. Menu **Santunan** untuk mencatat dana keluar.
3. **Akhir periode** → menu **Laporan**: klik **Buka / Cetak** untuk mengunduh PDF surat, buku besar, dan kartu iuran — siap dicetak/ditandatangani.
4. Warga dapat memantau ringkasan kapan saja di halaman publik `https://rt2.mluthfirr.id`.

---

## Catatan keamanan

- Ganti kata sandi akun pengurus setelah instalasi. Untuk mengganti lewat server:
  ```bash
  php artisan tinker
  >>> $u = App\Models\User::where('email','pengurus@rt2renijaya.id')->first();
  >>> $u->update(['password' => Hash::make('KATA_SANDI_BARU')]);
  ```
- Jalankan produksi dengan `APP_DEBUG=false`.

## Pengujian

```bash
php artisan test
```

Mencakup smoke test seluruh halaman, ekspor PDF, dan alur interaktif (iuran, peserta, santunan, periode, login).

---

## Struktur data

| Tabel               | Isi                                                        |
|---------------------|------------------------------------------------------------|
| `periode`           | Tahun buku, saldo awal, iuran default, status kunci        |
| `peserta`           | KK per periode: nama, blok, nomor, status, luar lingkungan |
| `iuran`             | Pembayaran per peserta per bulan (nominal)                 |
| `santunan`          | Dana keluar untuk keluarga berduka                         |
| `pengeluaran_lain`  | Pengeluaran non-santunan                                   |
| `pengaturan`        | Identitas organisasi & penanda tangan surat                |
