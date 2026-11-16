# Laporan Pengujian Kualitas Perangkat Lunak (QA) - CasIn.id

Penguji: Berlian Sepasya Rekardian
Peran: QA Engineer & Frontend Geolocation Indicator
Sprint: Sprint 2 / Milestone Integrasi SPKLU

---

## 1. Ringkasan Pengujian

Dokumen ini memuat hasil verifikasi pengujian fungsional dan otomatis untuk fitur-fitur berikut:
1. SCRUM-135: Tampilan indikator status pembacaan lokasi GPS pada bilah aplikasi.
2. SCRUM-136: Pengujian akurasi penangkapan koordinat GPS di berbagai peramban web (browser).
3. SCRUM-146: Pengujian logika filter otomatis stasiun berdasarkan jenis konektor kendaraan aktif.
4. SCRUM-159: Verifikasi koordinat lokasi tujuan yang dikirimkan ke Google Maps agar tidak ada kesalahan titik.

---

## 2. Rincian Kasus Uji

### Kasus Uji 1: SCRUM-135 - Indikator Status Lokasi pada Bilah Aplikasi
- Deskripsi: Memastikan bilah navigasi antarmuka menampilkan badge status GPS yang informatif sesuai kondisi pembacaan lokasi.
- Kondisi Indikator:
  - Sedang Membaca: Menampilkan status proses pembacaan sensor GPS.
  - Berhasil Didapat: Menampilkan indikator lokasi GPS aktif.
  - Izin Ditolak / Galat: Menampilkan keterangan lokasi default Monas aktif.
- Hasil Pengujian: LULUS (PASS).

### Kasus Uji 2: SCRUM-136 - Akurasi Koordinat GPS di Berbagai Peramban
- Matriks Pengujian Browser:
  - Google Chrome (Windows Desktop): LULUS (akurasi < 15 meter).
  - Mozilla Firefox (Windows Desktop): LULUS (akurasi < 20 meter).
  - Microsoft Edge (Windows Desktop): LULUS (akurasi < 15 meter).
  - Google Chrome Mobile (Android): LULUS (akurasi hardware GPS < 8 meter).
  - Safari Mobile (iOS): LULUS (akurasi hardware GPS < 10 meter).
- Hasil Pengujian: LULUS (PASS).

### Kasus Uji 3: SCRUM-146 - Filter Otomatis Konektor Kendaraan Aktif
- Deskripsi: Memastikan stasiun yang muncul pada katalog pengguna hanya stasiun yang memiliki tipe colokan/konektor yang sesuai dengan kendaraan aktif yang dipilih.
- Skenario Uji:
  1. Pilih kendaraan aktif dengan konektor CCS2 (misal Hyundai Ioniq 5).
  2. Panggil API `/api/stations?connector=CCS2`.
  3. Verifikasi hasil bahwa stasiun yang hanya memiliki konektor CHAdeMO tidak ikut terambil.
  4. Beralih ke kendaraan aktif CHAdeMO (misal Nissan Leaf) dengan panggil API `/api/stations?connector=CHAdeMO`.
  5. Verifikasi hasil bahwa hanya stasiun ber-konektor CHAdeMO yang muncul.
- Hasil yang Diharapkan:
  - Penyaringan stasiun akurat sesuai relasi `station_connectors`.
  - Tidak terjadi kebocoran tipe konektor yang tidak kompatibel.
- Hasil Pengujian: LULUS (PASS).
- Bukti Otomatis: `Tests\Feature\StationConnectorFilterTest::test_api_filters_stations_matching_active_vehicle_connector`

### Kasus Uji 4: SCRUM-159 - Verifikasi Koordinat Lokasi Tujuan Google Maps
- Deskripsi: Memastikan koordinat latitude dan longitude yang diteruskan ke parameter `destination` pada URL Google Maps bertipe float valid dan tidak terbalik atau tertukar.
- Langkah Uji:
  1. Ambil record stasiun dari basis data (misal SPKLU ULP Embong Wungu, Surabaya: latitude -7.265551, longitude 112.742352).
  2. Buka URL navigasi yang digenerasi oleh sistem: `https://www.google.com/maps/dir/?api=1&destination=-7.265551,112.742352`.
  3. Periksa pin tujuan pada peta Google Maps.
- Hasil yang Diharapkan:
  - Pin tujuan tepat berada di lokasi fisik stasiun yang bersangkutan.
  - Parameter latitude dan longitude tidak mengalami pemotongan desimal atau nilai NaN/null.
- Hasil Pengujian: LULUS (PASS).
- Bukti Otomatis: `Tests\Feature\StationConnectorTest::test_station_distance_and_direct_navigation_url_methods_work_per_proposal`
