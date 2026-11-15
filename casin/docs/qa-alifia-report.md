# Laporan Pengujian Kualitas Perangkat Lunak (QA) - CasIn.id

Penguji: Alifia Mareta Cahyani
Peran: QA Engineer & Frontend Map Picker
Sprint: Sprint 2 / Milestone Integrasi SPKLU

---

## 1. Ringkasan Pengujian

Dokumen ini memuat hasil verifikasi pengujian fungsional dan otomatis untuk fitur-fitur berikut:
1. SCRUM-137: Pengujian skenario penolakan izin geolokasi GPS (denied permission).
2. SCRUM-145: Pengujian keakuratan pengurutan Card List berdasarkan jarak terdekat (Haversine formula).
3. SCRUM-158: Pengujian tombol navigasi rute ke Google Maps pada perangkat mobile.
4. SCRUM-198: Pengujian penambahan data stasiun baru via panel Filament Admin.
5. SCRUM-199: Pengujian pengubahan (Edit) dan penghapusan (Delete) data stasiun via panel Filament Admin.

---

## 2. Rincian Kasus Uji

### Kasus Uji 1: SCRUM-137 - Penanganan Penolakan Izin Lokasi GPS
- Deskripsi: Memastikan sistem tidak mengalami kegagalan (crash) saat pengguna memilih opsi "Block" / "Tolak" pada dialog izin geolokasi browser.
- Langkah Uji:
  1. Buka peramban pada mode penyamaran (Incognito/Private).
  2. Kunjungi halaman utama `/` atau `/stasiun`.
  3. Tekan tombol aktivasi geolokasi pada antarmuka.
  4. Ketika peramban menampilkan dialog izin lokasi, pilih opsi "Block" atau "Don't Allow".
- Hasil yang Diharapkan:
  - Antarmuka tidak mengalami error atau halaman blank.
  - Sistem beralih menggunakan koordinat fallback Monas Jakarta (-6.2088, 106.8456).
  - Katalog dan peta tetap menampilkan daftar stasiun secara normal dengan status sumber lokasi default.
- Hasil Pengujian: LULUS (PASS).
- Bukti Otomatis: `Tests\Feature\StationDistanceTest::test_api_distance_fallback_to_monas_when_no_coordinates_sent`

### Kasus Uji 2: SCRUM-145 - Keakuratan Pengurutan Card List Berdasarkan Jarak
- Deskripsi: Memastikan stasiun dengan jarak fisik terdekat tampil di urutan paling atas.
- Langkah Uji:
  1. Kirim koordinat pengguna ke endpoint `/api/stations?lat=-6.2088&lng=106.8456`.
  2. Verifikasi urutan array stasiun pada respons JSON.
- Hasil yang Diharapkan:
  - Jarak stasiun terhitung menggunakan formula Haversine.
  - Stasiun berjarak 3.5 km (Gambir) berada pada indeks 0 sebelum stasiun berjarak 118 km (Bandung).
- Hasil Pengujian: LULUS (PASS).
- Bukti Otomatis: `Tests\Feature\StationDistanceTest::test_api_returns_stations_sorted_by_distance_ascending`

### Kasus Uji 3: SCRUM-158 - Tombol Navigasi Google Maps Mobile
- Deskripsi: Memastikan tautan tombol "Navigasi" memicu pengalihan URL yang valid untuk membuka aplikasi Google Maps di perangkat bergerak.
- Langkah Uji:
  1. Buka halaman detail stasiun `/stasiun/{id}` pada peramban mobile (Chrome Android / Safari iOS).
  2. Tekan tombol "Navigasi ke Lokasi".
- Hasil yang Diharapkan:
  - URL mengarah ke skema `https://www.google.com/maps/dir/?api=1&destination={lat},{lng}`.
  - Perangkat mobile secara langsung membuka aplikasi Google Maps dengan rute terisi.
- Hasil Pengujian: LULUS (PASS).

### Kasus Uji 4: SCRUM-198 - Tambah Data Stasiun Baru via Dashboard Admin
- Deskripsi: Memastikan admin dapat menambahkan stasiun baru lengkap dengan pemilihan koordinat dan data langsung muncul di katalog publik.
- Langkah Uji:
  1. Masuk sebagai akun admin di `/login`.
  2. Buka dashboard admin di `/admin#stations`.
  3. Tekan tombol "Tambah Data Stasiun".
  4. Masukkan nama, alamat, klik titik pada Map Picker Leaflet untuk mengisi latitude/longitude, dan simpan.
- Hasil yang Diharapkan:
  - Respons HTTP status 201 Created.
  - Data tersimpan pada tabel `stations` di database.
  - Stasiun baru langsung muncul pada endpoint `/api/stations` dan katalog pencarian pengguna.
- Hasil Pengujian: LULUS (PASS).
- Bukti Otomatis: `Tests\Feature\AdminStationCrudTest::test_admin_can_create_new_station_record_and_see_it_in_catalog`

### Kasus Uji 5: SCRUM-199 - Edit dan Hapus Data Stasiun
- Deskripsi: Memastikan data stasiun dapat diperbarui dan dihapus permanen oleh admin.
- Langkah Uji:
  1. Di tabel data stasiun admin, pilih tombol "Edit" pada stasiun target.
  2. Ubah tarif dan status, lalu simpan.
  3. Pilih tombol "Hapus" dan konfirmasi dialog penghapusan.
- Hasil yang Diharapkan:
  - Data yang diedit langsung terbarui di database.
  - Data yang dihapus terhapus dari tabel `stations` dan tidak lagi muncul di katalog.
- Hasil Pengujian: LULUS (PASS).
- Bukti Otomatis: `Tests\Feature\AdminStationCrudTest::test_admin_can_update_and_delete_station_record`
