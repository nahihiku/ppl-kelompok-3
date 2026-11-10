# Sumber dan Atribusi Data Stasiun Pengisian Daya (SPKLU)

Data sebaran stasiun pengisian kendaraan listrik di Indonesia pada folder ini bersumber dari dua kumpulan data terbuka terverifikasi:

## 1. Dataset Publik petaspklu.id / PT PLN (Persero) via Mendeley Data v2.0 (Opsi C)
- File: `petaspklu_stations.csv` (2.454 stasiun unik di 34 provinsi) dan `petaspklu_chargers.csv` (3.767 unit pengisi daya / charger box).
- Judul dataset: *A Geospatial Dataset of Public Electric Vehicle Charging Infrastructure in Indonesia (SPKLU)*, Version 2.0 (25 Agustus 2026).
- Penyusun: Richard Wiputra & Sherly Santiadi (Binus University).
- DOI: `https://doi.org/10.17632/z2bvd5r574.2`
- Sumber primer: `petaspklu.id` (`/api/v1/spklu/all`, snapshot 16 Juni 2026), dioperasikan oleh Solopos bersama PT PLN (Persero).
- Lisensi: Creative Commons Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0) — diizinkan untuk penggunaan non-komersial/akademis dengan mencantumkan atribusi.
- Catatan integritas: Dataset Mendeley mencantumkan arus (`AC`/`DC`) dan daya (`kW`) per kotak pengisi daya tanpa merinci tipe soket fisik per port (serta terdapat salah ketik pemisah desimal `74.0 kW` pada sejumlah unit wallbox AC 7,4 kW yang dinormalisasi menjadi `7.4 kW`). Untuk mengisi tipe soket fisik yang akurat, perintah `stations:import-pln` memadankan stasiun dan dispenser terhadap `pln_esdm_connectors.csv` yang bersumber dari data registrasi resmi PLN Mobile / Kementerian ESDM (`CHAdeMO`, `CCS2`, `AC Type 2`), setelah menyaring baris uji coba (`Merek Dummy`) dan koordinat tidak valid. Stasiun atau dispenser terbaru yang belum tercakup dalam data ESDM maupun OpenStreetMap tetap disimpan secara faktual sebagai `DC Connector` atau `AC Connector`. Tarif non-PLN dibiarkan kosong apabila tidak tercantum resmi di sumber.

## 2. Pemetaan Soket PLN Mobile / Kementerian ESDM
- File: `pln_esdm_connectors.csv` (2.024 pemetaan port terverifikasi pada 1.234 stasiun yang cocok dengan katalog Mendeley/PLN).
- Cakupan: Rincian port fisik per dispenser (`CCS2`, `CHAdeMO`, `AC Type 2`) termasuk stasiun multi-konektor seperti `SPKLU PLN ULP Embong Wungu` (`CCS2` + `CHAdeMO`) dan `SPKLU PLN WTC Surabaya Mall` (`CCS2` + `CHAdeMO` + `AC Type 2`).

## 3. OpenStreetMap via Overpass API (Opsi B)
- File: `osm_charging_stations.json` (142 elemen `amenity=charging_station` di Indonesia).
- Sumber: Kontributor OpenStreetMap (`https://www.openstreetmap.org/copyright`), diunduh melalui Overpass API (`https://overpass-api.de/api/interpreter`).
- Lisensi: Open Database License (ODbL) — `(c) kontributor OpenStreetMap`.
- Catatan integritas: Titik tanpa nama maupun operator diabaikan secara bawaan. Titik yang berada dalam radius 100 meter dari stasiun terkurasi atau stasiun dataset PLN dianggap sebagai entri ganda sehingga tidak diduplikasi, tetapi tag `socket:*` terverifikasi pada lokasi yang sama dipakai untuk melengkapi konektor impor yang masih berstatus generik (`DC Connector` / `AC Connector`).

## 4. Memperbarui atau Mengimpor Ulang Data
Jalankan perintah Artisan berikut dari folder `casin`:

```bash
php artisan stations:import-pln
php artisan stations:import-osm --file=database/data/osm_charging_stations.json
```

Untuk mengambil snapshot terbaru langsung dari server Overpass OpenStreetMap:

```bash
php artisan stations:import-osm
```

Untuk mengimpor file CSV ekspor PLN atau operator secara mandiri, gunakan format pada `template_import_spklu.csv` dan jalankan:

```bash
php artisan stations:import-csv path/ke/file.csv --source=pln
```
