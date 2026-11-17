# CasIn.id - Backend & Web Service

Folder ini berisi *source code* aplikasi utama **CasIn.id** berbasis framework Laravel 12.

Untuk dokumentasi lengkap proyek, spesifikasi RESTful API, dan identitas tim pengembang, silakan lihat [README utama di root repository](../README.md).

---

## Ringkasan Menjalankan Proyek Lokal

### Windows: PHP khusus proyek

Checkout ini menyediakan pembungkus PowerShell untuk runtime PHP 8.4 lokal di
`.runtime/php84`. Folder runtime diabaikan Git. Gunakan pembungkus ini jika PHP
global memakai 8.5: OpenSpout dalam `composer.lock` saat ini mendukung PHP 8.2 sampai
8.4, dan Filament membutuhkan ekstensi `intl`.

```powershell
# Dari folder casin, jalankan aplikasi lengkap di http://127.0.0.1:8000
.\scripts\serve.ps1

# Perintah Artisan dan tes memakai PHP yang sama
.\scripts\php.ps1 artisan migrate:status
.\scripts\php.ps1 artisan test
```

Server memakai `--no-reload` agar environment Windows diteruskan ke proses PHP.
Jika `.env` diubah, hentikan server dengan Ctrl+C lalu jalankan lagi.

Untuk database dari checkout lama, jalankan `.\scripts\php.ps1 artisan migrate`.
Migrasi kompatibilitas menyelaraskan `users.id/name` menjadi `id_user/nama`, serta
kolom identitas dan relasi di `station_connectors`, tanpa menghapus akun atau
stasiun. Hindari `migrate:fresh` pada database yang berisi data.

Runtime lokal menggunakan [PHP Windows resmi](https://www.php.net/downloads.php?os=windows&version=8.4),
versi 8.4.27 x64 NTS. Pada komputer ini, ekstensi `intl`, `mbstring`, `curl`,
`fileinfo`, `gd`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `sodium`, dan `zip`
diaktifkan dalam `.runtime/php84/php.ini`. PHP/XAMPP global tidak perlu diganti.

Untuk checkout di komputer lain, instal PHP 8.4 beserta ekstensi tersebut terlebih
dahulu, atau ekstrak distribusi PHP resmi ke `.runtime/php84` dan siapkan `php.ini`
dengan `extension_dir` yang menunjuk folder `ext` milik runtime itu. Sertifikat CA
harus dikonfigurasi untuk Composer; jangan menonaktifkan verifikasi TLS.

### Instalasi umum

1. **Instal Dependensi**:
   ```bash
   composer install
   npm install
   ```

2. **File Lingkungan**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Konfigurasi Google OAuth di `.env`**:
   ```dotenv
   GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-google-client-secret
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```

4. **Migrasi Database**:
   ```bash
   php artisan migrate
   ```

5. **Jalankan Automated Tests**:
   ```bash
   php artisan test
   ```

6. **Jalankan Server Lokal**:
   ```bash
    php artisan serve
    ```

Autentikasi login dan registrasi pada halaman web dikirim melalui `fetch()` ke API Sanctum.
Token disimpan pada `localStorage` browser dengan key `casin.auth_token`. Login Google memakai
Google Identity Services untuk memperoleh access token dan mengirimkannya ke `/api/auth/google`.

## Kendaraan dan Konektor Tambahan

Pada popup **Pilih Kendaraan Aktif**, tombol **Tambah Kendaraan / Konektor** membuka
menu tambah di dalam dialog yang sama. Pilih **Kendaraan** untuk mengisi merek,
model, dan konektor, atau **Tipe konektor saja** untuk mengisi konektor tanpa
kendaraan. Input konektor menerima saran maupun teks lain.

Setelah disimpan, pilihan tambahan muncul di dropdown. Klik **Gunakan Kendaraan**
untuk menerapkannya ke API pencarian stasiun. Data tambahan disimpan pada browser
di key `casin.custom_vehicles`; pilihan aktif tetap memakai `casin.active_vehicle`.
Penghapusan data situs dari browser juga menghapus pilihan lokal tersebut.

Tes penyimpanan, validasi, duplikat, dan data yang rusak:

```bash
npm run test:vehicles
```
