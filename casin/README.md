# CasIn.id - Backend & Web Service

Folder ini berisi *source code* aplikasi utama **CasIn.id** berbasis framework Laravel 12.

Untuk dokumentasi lengkap proyek, spesifikasi RESTful API, dan identitas tim pengembang, silakan lihat [README utama di root repository](../README.md).

---

## Ringkasan Menjalankan Proyek Lokal

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
