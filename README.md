# CasIn.id - Direktori Stasiun Pengisian Kendaraan Listrik Interaktif

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Sanctum](https://img.shields.io/badge/Laravel_Sanctum-Auth_API-blue?style=for-the-badge)](https://laravel.com/docs/sanctum)
[![Socialite](https://img.shields.io/badge/Google_OAuth-Socialite-4285F4?style=for-the-badge&logo=google&logoColor=white)](https://laravel.com/docs/socialite)

**CasIn.id** adalah platform direktori interaktif berbasis web untuk memudahkan para pemilik dan pengendara kendaraan listrik (Electric Vehicle / EV) di Indonesia dalam menemukan titik Stasiun Pengisian Kendaraan Listrik Umum (SPKLU) dan Stasiun Penukaran Baterai Kendaraan Listrik Umum (SPBKLU) terdekat secara real-time, akurat, dan sesuai dengan spesifikasi soket/daya kendaraannya.

---

## Tim Pengembang (Kelompok 3 - PPL)

| Nama | NIM | Peran |
|---|---|---|
| **Firyal Jihan Haura** | 102062400119 | Product Owner |
| **Alifia Mareta Cahyani** | 102062400086 | Scrum Master |
| **Muh Naufal Rabbani Marun** | 102062400038 | Developer |
| **Nadhif Maulana Fayzalty** | 102062400016 | Developer |
| **Berlian Sepasya Rekardian** | 102062400088 | Developer |

---

## Fitur Utama

- **Autentikasi Multi-Metode**:
  - Registrasi dan Login email/kata sandi konvensional dengan password hashing yang aman (Bcrypt/Argon2).
  - Login dan Registrasi via Google OAuth 2.0 (Laravel Socialite).
  - RESTful API Authentication menggunakan Laravel Sanctum Bearer Token untuk integrasi mobile atau frontend SPA.
- **Peta Direktori SPKLU / SPBKLU Interaktif**:
  - Pelacakan koordinat pengguna secara real-time dengan HTML5 Geolocation API.
  - Visualisasi titik stasiun pengisian pada peta interaktif Leaflet.js dan OpenStreetMap.
  - Penyaringan otomatis berdasarkan soket dan kategori daya yang kompatibel dengan kendaraan aktif pengguna.
- **Manajemen Profil Kendaraan**:
  - Master data spesifikasi kendaraan listrik (mobil/motor, kapasitas baterai, jenis colokan).
  - Penetapan kendaraan aktif pengendara.

---

## Tech Stack dan Dependensi

- **Backend Framework**: [Laravel 12](https://laravel.com)
- **Runtime**: PHP 8.2+
- **API Security & Auth**: [Laravel Sanctum](https://laravel.com/docs/sanctum) dan [Laravel Socialite](https://laravel.com/docs/socialite)
- **Database**: SQLite (default lokal/testing) / MySQL / MariaDB
- **Frontend**: Blade Templating, Tailwind CSS, JavaScript (Leaflet.js dan OpenStreetMap)
- **Testing**: PHPUnit

---

## Panduan Instalasi dan Menjalankan Proyek

### 1. Prasyarat Sistem
- PHP >= 8.2
- Composer >= 2.x
- Node.js dan npm (opsional untuk asset compile)
- Ekstensi PHP: `pdo_sqlite` (atau `pdo_mysql`), `curl`, `mbstring`, `openssl`

### 2. Kloning Repository
```bash
git clone https://github.com/muhnaufal11/ppl-kelompok-3.git
cd ppl-kelompok-3/casin
```

### 3. Instal Dependensi
```bash
composer install
npm install
```

### 4. Konfigurasi Lingkungan (.env)
Salin file konfigurasi contoh dan buat Application Key:
```bash
cp .env.example .env
php artisan key:generate
```

Lengkapi kredensial Google OAuth pada `.env` (diperoleh dari Google Cloud Console):
```dotenv
APP_URL=http://localhost:8000

GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 5. Jalankan Migrasi Database
```bash
php artisan migrate
```

### 6. Jalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## Dokumentasi Endpoint REST API

Semua respons API menggunakan format JSON standar:
```json
{
  "success": true,
  "message": "Pesan deskriptif status",
  "data": { }
}
```

### Ringkasan Rute API

| Method | Endpoint | Proteksi | Deskripsi |
|---|---|---|---|
| `POST` | `/api/register` | Public | Registrasi akun baru dan dapatkan Sanctum Bearer token |
| `POST` | `/api/login` | Public | Login akun dan dapatkan Sanctum Bearer token |
| `GET` | `/api/user` / `/api/me` | `Bearer Token` | Ambil data profil pengguna yang terautentikasi |
| `POST` | `/api/logout` | `Bearer Token` | Cabut token aktif (revoke current token) |
| `GET` | `/api/auth/google/redirect` | Public | Dapatkan URL redirect Google OAuth2 |
| `GET` | `/api/auth/google/callback` | Public | Callback Google OAuth2, buat/link user dan return token |
| `POST` | `/api/auth/google` | Public | Login Google via token (id_token, access_token, atau code) |

---

### Contoh Payload API

#### 1. Registrasi Akun (`POST /api/register`)
Request Body:
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_name": "mobile_app"
}
```
Response (201 Created):
```json
{
  "success": true,
  "message": "Registrasi akun berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "created_at": "2026-10-26T04:13:00.000000Z",
      "updated_at": "2026-10-26T04:13:00.000000Z"
    },
    "token": "1|AbCdEf123456...",
    "token_type": "Bearer"
  }
}
```

#### 2. Login Akun (`POST /api/login`)
Request Body:
```json
{
  "email": "budi@example.com",
  "password": "password123",
  "device_name": "mobile_app"
}
```
Response (200 OK):
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com"
    },
    "token": "2|XyZ987654...",
    "token_type": "Bearer"
  }
}
```

#### 3. Autentikasi Google via Token Client (`POST /api/auth/google`)
Ideal untuk klien Flutter / React Native / SPA yang menggunakan Google Sign-In SDK di sisi klien:
```json
{
  "id_token": "<GOOGLE_ID_TOKEN>",
  "device_name": "android_phone"
}
```
Response (200 OK):
```json
{
  "success": true,
  "message": "Autentikasi Google berhasil.",
  "data": {
    "user": {
      "id": 2,
      "name": "Pengendara EV",
      "email": "pengendara@gmail.com",
      "google_id": "109823901923..."
    },
    "token": "3|GooGleAuthToken...",
    "token_type": "Bearer"
  }
}
```

---

#### 4. Nomor HP saat Registrasi

Field `phone` bersifat opsional untuk menjaga kompatibilitas client lama, dan digunakan oleh form registrasi web:

```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "phone": "+628123456789",
  "password": "password123",
  "password_confirmation": "password123"
}
```

---

## Menjalankan Pengujian Otomatis

Proyek ini telah dilengkapi dengan serangkaian unit dan feature test yang mencakup autentikasi konvensional, token Sanctum, dan Google OAuth:

```bash
cd casin
php artisan test
```

Semua 16 unit dan feature tests teruji 100% PASS (77 assertions).

---

## Lisensi

Proyek ini dikembangkan untuk keperluan akademik mata kuliah Praktikum Perangkat Lunak (PPL) di bawah lisensi terbuka [MIT License](https://opensource.org/licenses/MIT).
