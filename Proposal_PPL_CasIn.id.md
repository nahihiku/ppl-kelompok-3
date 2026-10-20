







## Proposal Pengembangan
## Produk

CasIn.id

Disusun oleh
## 102062400119 – Firyal Jihan Haura
## 102062400086 – Alifia Mareta Cahyani
## 102062400038 - Muh Naufal Rabbani Marun
## 102062400016 – Nadhif Maulana Fayzalty
## 102062400088 – Berlian Sepasya Rekardian

## 22 September 2026



## Daftar Isi


- Pendahuluan.............................................................................................................................1
1.1 Latar Belakang .....................................................................................................1
1.2 Tujuan ..................................................................................................................3
1.3 Output...................................................................................................................3
- Deskripsi Produk .....................................................................................................................5
2.1 Usulan Solusi .......................................................................................................5
2.2 Deskripsi Produk ..................................................................................................5
2.3 Proses Bisnis .......................................................................................................6
- Kebutuhan Sistem ...................................................................................................................1
3.1 Kebutuhan Fungsional .........................................................................................1
3.1.1 Daftar Kebutuhan Admin .................................................................................1
3.1.2 Daftar Kebutuhan Pengguna Kendaraan Listrik .............................................1
3.1.3 Karakteristik Pengguna ...................................................................................3
3.2 Kebutuhan Non Fungsional .................................................................................4
3.3 Kebutuhan Teknis ................................................................................................5
- Rancangan Sistem ...................................................................................................................6
4.1 Use Case Diagram...............................................................................................6
4.2 Arsitektur Sistem ............................................................................................... 12
4.3 Rancangan ERD ............................................................................................... 14
4.4 Class Diagram .................................................................................................. 17
4.5 Mockup .............................................................................................................. 20
- Metode Pengembangan ....................................................................................................... 25
5.1 Jadwal Pengembangan .................................................................................... 26
5.2 Tim Pengembang .............................................................................................. 27




## 1. Pendahuluan
## 1.1 Latar Belakang
Pemanasan global dan perubahan iklim akibat aktivitas manusia telah menimbulkan dampak
buruk  terhadap  kelestarian  lingkungan  dan  kesehatan  masyarakat (Zola  et  al.,  2023).  Salah  satu
kontributor  utama  emisi  karbon  dioksida  (CO
## 2
)  dan  gas  rumah  kaca  secara  global  adalah
pembakaran bahan bakar fosil pada sektor transportasi (Zubaydah et al., 2024). Di Indonesia, sektor
transportasi merupakan penyumbang emisi terbesar kedua setelah sektor industri dengan kontribusi
sekitar  23%  hingga  28% dari  total  emisi  nasional (Nur  &  Kurniawan,  2021).  Dari  angka  tersebut,
pergerakan  penumpang  pada  transportasi  darat  memberikan  kontribusi  paling  besar,  yakni
mencapai 73% hingga 88% dari total emisi sektor transportasi (Masayu & Alfiah, 2024). Tingginya
emisi  ini  sejalan  dengan  populasi  kendaraan  bermesin bensin  di  Indonesia  yang  terus  meningkat
hingga melebihi 148 juta unit (Adittya, 2024). Ketergantungan tinggi pada bahan bakar minyak (BBM)
ini memicu penurunan kualitas udara perkotaan akibat cemaran polutan berbahaya seperti nitrogen
oksida  (NO
x
),  karbon  monoksida  (CO),  belerang  dioksida  (SO
## 2
),  dan  partikulat  halus  (PM
## 2,5
)  yang
membahayakan kesehatan pernapasan masyarakat (Zubaydah et al., 2024).
Kondisi  tersebut  bertolak  belakang  dengan  sasaran  Tujuan  Pembangunan  Berkelanjutan
atau  Sustainable  Development  Goals  nomor  7  (SDGs  7),  yaitu  menjamin  akses  energi  yang
terjangkau,  andal,  berkelanjutan,  dan  modern  bagi  semua  pihak. (Gebara  &  Laurent,  2023)
menegaskan  bahwa  evaluasi  kinerja  SDGs  7  tidak  cukup  dinilai  dari  capaian  relatif  antartahun,
melainkan harus diukur berdasarkan batas keberlanjutan mutlak agar peningkatan kebutuhan energi
tidak menimbulkan pergeseran beban dampak lingkungan pada sistem pendukung kehidupan bumi.
Dalam konteks tersebut, substitusi bahan bakar fosil di sektor transportasi jalan raya menjadi salah
satu langkah kunci untuk menekan jejak karbon sistem energi nasional (Masayu & Alfiah, 2024) .
Sebagai  langkah  nyata  mengatasi  pencemaran  udara  dan  ketergantungan  impor  BBM,
Pemerintah Indonesia menerbitkan Peraturan Presiden Nomor 55 Tahun 2019 tentang Percepatan
Program Kendaraan Bermotor Listrik Berbasis Baterai (KBLBB) untuk Transportasi Jalan (Zubaydah
et  al.,  2024).  Kendaraan  bermotor  listrik  berbasis baterai  memiliki  efisiensi  energi  3  hingga  5  kali
lebih hemat dibandingkan kendaraan bermesin pembakaran dalam, tidak menghasilkan emisi gas
buang  langsung  saat  dioperasikan,  dan  berpotensi  mereduksi  emisi CO
## 2
hingga  100% pada  fase
penggunaan (Zubaydah  et  al.,  2024).  Simulasi  teknis  menunjukkan  bahwa  peralihan  kendaraan



konvensional   menuju   mobil   listrik   mampu   memangkas   konsumsi   energi   dan   menghasilkan
penurunan emisi gas buang jutaan ton CO
## 2
per tahun (Adittya, 2024).
Meskipun  didukung  payung  hukum  serta  insentif  perpajakan  seperti  pembebasan  Pajak
Penjualan atas Barang Mewah (PPnBM), laju adopsi kendaraan listrik di Indonesia masih tergolong
lambat dan menghadapi kendala di lapangan (Regina & Ulmi, 2023). Berdasarkan temuan empiris
(Permana et al., 2023)  menggunakan kerangka Theory of Planned Behavior (TPB) dan Technology
Acceptance  Model  (TAM),  kendala  infrastruktur  dan  persepsi  risiko  berpengaruh  negatif  secara
signifikan  terhadap  niat  pembelian  kendaraan  listrik  di  Indonesia.  Calon  pengguna  masih  diliputi
kekhawatiran  terkait  keterbatasan  jarak  tempuh  serta  kesulitan  memperoleh  akses  fasilitas
pengisian daya yang memadai di tempat umum (Regina & Ulmi, 2023).
Kekhawatiran tersebut diperparah oleh tingginya fragmentasi ekosistem fasilitas pengisian daya
yang  ada  saat  ini (Kuswardani,  2024). Berdasarkan  regulasi  dan  implementasi  teknisnya,  sarana
pengisian  listrik  umum  di  Indonesia  terbagi  atas  Stasiun  Penyedia  Listrik  Umum  (SPLU),  Stasiun
Pengisian  Kendaraan  Listrik  Umum  (SPKLU),  dan  Stasiun  Penukaran  Baterai  Kendaraan  Listrik
Umum (SPBKLU) (Adittya, 2024). Namun, fasilitas ini dikelola secara terpisah-pisah oleh berbagai
pihak:
- Penyedia Negara (BUMN): PT PLN (Persero) membangun dan mengoperasikan unit SPLU
dan SPKLU secara mandiri melalui aplikasi internalnya, yaitu PLN Mobile (Dharmawan et al.,
## 2021).
- Penyedia  Swasta  dan  Agen  Pemegang  Merek  (APM): Melalui  terbukanya  skema
kemitraan swasta oleh PLN, berbagai pengelola swasta serta produsen otomotif mendirikan
fasilitas  pengisian  mandiri  dengan  sistem  aplikasi  digital  yang  terpisah  dan  tertutup  satu
sama lain (Kuswardani, 2024).
- Disparitas Standar Soket dan Daya Pengisian: Terdapat keberagaman jenis konektor fisik
serta  variasi  daya  pengisian  mulai  dari  skala  lambat,  sedang,  cepat,  hingga  sangat  cepat
(Masayu & Alfiah, 2024).
Kondisi  tersebut  menyulitkan  pengendara  kendaraan  listrik  karena  harus  mengunduh  dan
memeriksa banyak aplikasi yang berbeda hanya untuk mencari lokasi pengisian terdekat yang cocok
dengan  tipe  colokan  kendaraannya (Permana  et  al.,  2023).  Walaupun  revisi  regulasi  melalui
Peraturan  Menteri  ESDM  Nomor  1  Tahun  2023  telah  menggarisbawahi  urgensi  integrasi  aplikasi
dalam penyediaan infrastruktur pengisian listrik (Adittya, 2024), hingga saat ini belum tersedia sistem
agregator tunggal yang menyatukan seluruh informasi fasilitas dari penyedia negeri maupun swasta
secara  terpadu  bagi  publik (Dharmawan  et  al.,  2021).  Selain  itu,  ketiadaan  saluran  pembaruan



informasi kondisi fasilitas membuat pengendara kerap mendapati unit pengisian yang sedang tidak
aktif atau mengalami antrean panjang di lokasi (Permana et al., 2023).
Guna  mereduksi  kendala  sarana  dan  fragmentasi  sistem  tersebut,  proyek  perangkat  lunak  ini
merancang CasIn.id,  yaitu  aplikasi  perangkum  stasiun  pengisian  kendaraan  listrik  berbasis  web.
Sistem  ini  mengintegrasikan  seluruh  basis  data  sebaran  lokasi  stasiun  pengisian  daya  milik
pemerintah, swasta, maupun fasilitas komersial mandiri ke dalam satu peta digital terpadu. Aplikasi
ini dilengkapi fitur penyaring multi-parameter spesifikasi jenis konektor dan daya pengisian (Adittya,
2024), fitur pelaporan kondisi operasional fasilitas berbasis masukan pengguna, serta fitur navigasi
ke lokasi stasiun pengisian daya. Melalui kehadiran CasIn.id, hambatan infrastruktur dan kecemasan
operasional pengguna dapat ditekan, sehingga mendukung percepatan mobilitas energi bersih yang
berkelanjutan di Indonesia.
## 1.2 Tujuan
Berdasarkan   permasalahan   yang   telah   dipaparkan   pada   latar   belakang,   tujuan   dari
pengembangan aplikasi CasIn.id adalah:
- Mengintegrasikan  direktori  sebaran  stasiun  pengisian  daya  dari  berbagai  penyedia,  baik
pihak negara (PLN) maupun swasta, ke dalam satu peta digital terpadu yang memudahkan
pencarian berdasarkan spesifikasi soket dan daya listrik.
- Menyediakan kepastian informasi operasional dan rute perjalanan melalui panduan navigasi
serta  pembaruan  kondisi  stasiun  secara  langsung  berbasis  laporan  pengguna  guna
meminimalkan kendala di perjalanan.
## 1.3 Output
Hasil  akhir  utama  dari  kegiatan  ini  adalah  aplikasi  web  responsif  bernama  CasIn.id  yang
bertujuan untuk mempermudah pencarian tempat pengisian daya kendaraan listrik dalam satu pintu.
Aplikasi ini menampilkan peta digital terpadu yang menggabungkan lokasi titik pengisian daya milik
pemerintah  maupun  pihak  swasta.  Pengguna  dapat  mencari  dan  menyaring  lokasi  stasiun
berdasarkan  jenis  colokan  serta  kecepatan  pengisian  dayanya.  Selain  itu, aplikasi  menyediakan
petunjuk  rute  menuju  tempat  pengisian  yang  dipilih  serta  fitur  pelaporan  bersama  agar  para
pengendara dapat saling memberi kabar mengenai kondisi stasiun di lapangan, misalnya saat unit
cas sedang rusak, mati, atau antreannya padat.



Untuk menjaga agar pengerjaan proyek tetap terarah dan selesai tepat waktu, ruang lingkup
pengembangan sistem ini dibatasi pada beberapa hal. Aplikasi ini dibangun khusus dalam bentuk
web  responsif  yang  nyaman  dibuka  melalui  peramban  ponsel  pintar  maupun  komputer  desktop.
Pendataan  stasiun  pengisian  daya  pada  tahap  awal  dipusatkan  pada  wilayah  perkotaan  besar
dengan jumlah pengguna kendaraan listrik terbanyak, yaitu wilayah Jawa dan Bali. Kumpulan data
lokasi diperoleh dari data awal yang disiapkan oleh pengembang lalu diperbarui bersama oleh para
pengguna, sehingga sistem tidak terhubung langsung secara otomatis ke basis data internal masing-
masing penyedia. Di samping itu, fungsi aplikasi berfokus penuh pada penyediaan informasi lokasi,
penyaringan  spesifikasi,  petunjuk  arah,  dan  pembaruan  kondisi  tempat  cas,  tanpa  melayani
transaksi pembayaran listrik atau pemesanan antrean secara digital.
Sebagai  pelengkap  dari  perangkat  lunak  yang  dibangun,  proyek  ini  juga  menghasilkan
dokumen  rekayasa  perangkat  lunak  serta  berkas  pengelolaan  proyek  yang  lengkap.  Dokumen
tersebut meliputi berkas spesifikasi kebutuhan sistem, dokumen perancangan sistem yang memuat
diagram  alur  sistem,  struktur  basis  data,  serta  rancangan  tampilan  antarmuka.  Seluruh  tahapan
pembuatan  aplikasi  juga  dicatat  dalam  dokumen  metode  Scrum,  mulai  dari  daftar  rencana  kerja,
agenda  kerja  berkala  dalam  tiga  tahapan  iterasi,  evaluasi  hasil  kerja  mingguan,  hingga  laporan
pengujian sistem bersama pengguna.







## 2. Deskripsi Produk
## 2.1 Usulan Solusi
Peningkatan penggunaan  kendaraan bermotor  listrik  berbasis  baterai di  Indonesia  saat  ini
menghadapi  kendala  penyebaran  informasi  stasiun  pengisian  daya  yang  belum  terpadu. Setiap
pihak pengelola, baik instansi pemerintah (PLN) maupun badan usaha swasta yang menyediakan
informasi  melalui  aplikasi  masing-masing  secara  terpisah.  Pada  aplikasi  yang  tersedia  saat  ini,
stasiun  milik  pengelola  lain  kerap  kali  tidak  memuat  perkiraan  tarif  per  kilowatt-jam  (kWh)  secara
jelas serta kerap berstatus offline tanpa kepastian kondisi nyata di lapangan. Hal ini menimbulkan
keraguan bagi pengendara dalam menentukan lokasi pengisian daya.
Guna  mengatasi  persoalan  tersebut,  penyusun  mengusulkan  perancangan  CasIn.id,  yaitu
perangkat lunak berbasis situs web yang mengumpulkan informasi sebaran stasiun pengisian daya
kendaraan   listrik   secara   mandiri.   CasIn.id   berfungsi   sebagai   sistem   pendukung   keputusan
perjalanan  bagi  pengendara  sebelum  mengisi  daya.  Melalui  perpaduan  penyaringan  spesifikasi
teknis  serta  pemanfaatan laporan  sukarela  dari  sesama  pengguna,  platform  ini  memberikan
kepastian kelaikan mesin  pengisian,  kejelasan  perkiraan biaya, dan ulasan  fasilitas pendukung  di
sekitar tempat pengisian daya.
## 2.2 Deskripsi Produk
CasIn.id  merupakan  aplikasi  web  yang  berfungsi  sebagai  direktori  terpadu  dan  pusat
informasi  stasiun  pengecasan  kendaraan  listrik  berbasis  komunitas.  Pengendara  dapat  langsung
mengakses  layanan  ini  melalui  ponsel  pintar  maupun  komputer  tanpa  harus  mengunduh  atau
memasang aplikasi tambahan terlebih dahulu.
Fokus utama CasIn.id adalah menyediakan informasi yang lengkap, akurat, dan netral guna
membantu  pengendara  merencanakan  rute  perjalanan  secara  tenang,  tanpa  mencakup  fitur
transaksi pembayaran listrik maupun kendali mesin dispenser secara langsung.
Fitur-fitur utama yang disediakan oleh CasIn.id meliputi:
- Peta  Sebaran  Stasiun  Pengecasan:  Menampilkan  persebaran  titik  stasiun  pengecasan
terdekat  di  sekitar  pengguna  secara  langsung  dengan  memanfaatkan  koordinat  GPS
perangkat.
- Pengelolaan   Profil   Kendaraan: Memfasilitasi   pengendara   untuk   menambahkan   data
kendaraannya ke dalam akun, yang terbagi dalam dua kategori utama, yaitu motor listrik dan



mobil  listrik.  Pengguna  cukup  memilih  merek  dan  tipe  kendaraannya,  lalu  sistem  secara
otomatis menampilkan detail spesifikasi baterai dan mengunci jenis colokan yang sesuai.
- Penyaring Otomatis dan Cerdas: Menyaring titik stasiun di peta secara otomatis berdasarkan
kendaraan  yang  sedang  aktif  pada  profil  pengguna,  atau  melalui  penyaringan  manual
berdasarkan tipe colokan dan kategori daya pengisian.
- Detail  Informasi  dan  Transparansi  Biaya:  Menyajikan  rincian  data  stasiun  secara  lengkap,
mencakup nama operator penyedia, alamat lokasi, jam operasional, taksiran tarif listrik per
kWh,  perkiraan  biaya  layanan,  serta  informasi  aplikasi  bawaan  yang  dibutuhkan  untuk
menyalakan mesin.
- Laporan Kondisi Mesin Komunitas: Menyediakan sarana pelaporan cepat bagi pengendara
untuk membagikan status mesin terkini di lokasi.
- Ulasan   dan   Penilaian   Fasilitas:   Memberikan   ruang   bagi   sesama   pengendara   untuk
memberikan nilai bintang dan ulasan seputar fasilitas pendukung di sekitar stasiun, seperti
ketersediaan  atap  peneduh,  penerangan,  toilet,  tempat  peristirahatan,  maupun  warung
makan.
- Petunjuk  Rute  Perjalanan:  Menyediakan  tombol  navigasi  langsung  yang  mengalihkan  titik
koordinat stasiun ke aplikasi peta luar guna memandu pengendara sampai ke lokasi tujuan.

## 2.3 Proses Bisnis
A. Proses Bisnis Existing (As-Is)
Pada kondisi saat ini, pengendara kendaraan listrik menghadapi prosedur pencarian
tempat cas yang terfragmentasi dan berisiko tinggi salah pilih lokasi:
- Pengendara membuka aplikasi resmi milik salah satu operator untuk mencari stasiun
pengisian terdekat.
- Pengendara  harus  memeriksa  dan  mencocokkan  secara  manual  apakah  colokan
pada stasiun tersebut sesuai dengan tipe colokan pada kendaraannya.
- Jika  titik  pada  aplikasi  pertama  tidak  sesuai  kriteria  jarak  atau  tipe  colokan,
pengendara  harus  menutup  aplikasi  tersebut  dan  membuka  aplikasi  operator  lain
satu per satu.
- Pada  stasiun  milik  pihak  ketiga  atau  swasta,  pengendara  kesulitan  mengetahui
kejelasan  tarif  riil  per  kilowatt-jam  (kWh)  serta  status  kelaikan  alat  karena  data
antarsistem sering terputus.



- Pengendara  menuju  lokasi  dengan  rasa  cemas,  berisiko  mendapati  colokan  tidak
cocok, mesin dalam kondisi mati, atau antrean kendaraan yang padat.


Gambar 2. 1 BPMN As-Is

B. Proses Bisnis Usulan (To-Be)
Proses   bisnis   yang   diusulkan   (To-Be)   dirancang   untuk   mengintegrasikan   proses
pemilihan spesifikasi kendaraan, penemuan stasiun pengisian daya terdekat, pemanduan rute
perjalanan, hingga pelaporan kondisi lapangan ke dalam satu platform terpusat. Alur interaksi
antara  pengendara  kendaraan  listrik  dan  sistem  CasIn.id  dimodelkan  menggunakan  standar
Business Process Model and Notation (BPMN) sebagaimana disajikan pada Gambar 2.2 berikut:


Gambar 2. 2  BPMN To-Be

Berdasarkan Gambar 2.2 di atas, alur proses bisnis yang diusulkan (To-Be) pada sistem
informasi CasIn.id terbagi ke dalam tahapan-tahapan sebagai berikut:
## 1. Pemilihan Profil Kendaraan Aktif:
Proses   diawali   ketika   pengendara   mengakses   aplikasi   web   CasIn.id   dan
menentukan profil kendaraan listrik aktif yang sedang digunakan. Sistem merespons aksi
tersebut  dengan  membaca  basis  data  spesifikasi  kendaraan  dan  mengunci  filter  jenis
soket colokan yang kompatibel secara otomatis.




- Perizinan Geolokasi dan Pemetaan Stasiun:
Sistem  menampilkan permintaan akses lokasi  geospasial pada  peramban  web.
Setelah pengendara memberikan izin akses koordinat GPS perangkat, sistem membaca
titik koordinat pengguna, menyaring seluruh data stasiun pengisian daya terdekat yang
memiliki colokan yang cocok, lalu memvisualisasikan hasilnya ke dalam antarmuka peta
interaktif.

- Eksplorasi Informasi dan Evaluasi Kriteria:
Pengendara  mengeksplorasi  peta  persebaran  untuk  meninjau  rincian  informasi
stasiun, seperti nama operator, ketersediaan jenis soket, transparansi tarif listrik per kWh,
serta status operasional kelaikan alat di lapangan.
- Jika  informasi  stasiun  yang  ditampilkan  belum  memenuhi  kebutuhan,  pengendara
dapat menyesuaikan parameter filter pencarian secara manual.
- Jika  pengendara  menemukan  stasiun  yang  cocok,  pengendara  memilih  stasiun
tujuan tersebut lalu menekan tombol Rute Navigasi.

## 4. Pengalihan Koordinat Navigasi:
Sistem  CasIn.id  secara  otomatis  meneruskan  data  titik  koordinat  stasiun  yang
dipilih ke peranti lunak penunjuk arah eksternal (Google Maps) melalui skema deep link
guna memandu rute perjalanan fisik pengendara.

- Verifikasi Lapangan dan Pengecasan Daya:
Setibanya di lokasi stasiun pengisian, pengendara memeriksa ketersediaan dan
kelaikan fisik unit dispenser pengisian:
- Jika  unit  dapat  digunakan:  Pengendara  melakukan  proses  pengisian  daya  baterai
hingga selesai.
- Jika  unit  mengalami  kendala: Pengendara  dapat  mengirimkan  laporan  kendala
secara instan melalui sistem CasIn.id.

- Pembaruan Status dan Penyelesaian Alur:
Sistem  menerima  laporan  kendala  tersebut,  menyematkan timestamp,  dan
langsung  memperbarui  label  status  stasiun  pada  basis  data  agar  informasi  yang
ditampilkan kepada pengendara lain selalu terkini. Rangkaian proses pencarian stasiun
pengisian daya pun selesai.



## 3. Kebutuhan Sistem
## 3.1 Kebutuhan Fungsional
Kebutuhan  fungsional  mendefinisikan  seluruh  fungsi,  kapabilitas  layanan, dan  interaksi
yang harus disediakan oleh sistem informasi CasIn.id kepada setiap entitas pengguna.
## 3.1.1 Daftar Kebutuhan Admin
Tabel berikut menjelaskan rincian kebutuhan fungsional untuk pengguna dengan peran
## Admin:
## Tabel 3. 1 Daftar Kebutuhan Fungsional Admin
ID Kebutuhan Fungsional Deskripsi
KF-A01 Admin dapat melakukan login Sistem menyedikan fungsi login khusus admin secara
aman untuk mengakses menu pengelolaan data
internal.
KF-A02 Admin dapat mengelola data
pengguna
Admin dapat melihat daftar seluruh akun pengguna
CasIn.id serta berhak mendapat akses penuh
terhadap data pengguna.
KF-A03 Admin dapat mengelola data
stasiun penyedia pengisian
daya
Admin dapat menambah, mengubah, melihat, dan
menghapus data stasiun pengisian daya.
KF-A04 Admin dapat mengelola ulasan
dan laporan kendala dari
pengguna
Admin dapat membaca ulasan atau laporan
pengguna, serta berhak menghapus komentar yang
tidak sopan atau laporan yang terbukti palsu.
KF-A05 Admin dapat mengelola data
kendaraan
Admin dapat menambah, mengubah, dan
menghapus daftar merek, model, serta spesifikasi
colokan bawaan kendaraan listrik.
KF-A06 Admin dapat mengelola akun
profil
Admin dapat memperbarui nama akun dan
mengganti kata sandi kapan saja.



## 3.1.2 Daftar Kebutuhan Pengguna Kendaraan Listrik



Pengendara merupakan pengguna akhir yang memanfaatkan informasi direktori,
panduan navigasi, dan penyedia laporan lapangan.

## Tabel 3. 2 Daftar Kebutuhan Fungsional Pengguna Kendaraan Listrik
ID Kebutuhan Fungsional Deskripsi
KF-U01 Pengguna dapat melakukan
registrasi
Sistem harus memungkinkan pengguna dapat
mendaftarkan akun baru.
KF-U02 Pengguna dapat melakukan
login dan logout
Sistem memungkinkan pengguna untuk
melakukan login dan logout dari platform.
KF-U03 Pengguna dapat
mendaftarkan kendaraan
miliknya
Sistem harus memungkinkan pengguna
menambahkan kendaraan miliknya dengan
memilih kategori (motor/mobil), merek, dan
model, serta menetapkan satu kendaraan
sebagai kendaraan aktif.
KF-U04 Pengguna dapat melihat posisi
perangkat secara real-time
Sistem harus dapat membaca koordinat lokasi
perangkat pengguna secara real-time melalui
Geolocation API setelah memperoleh
persetujuan izin dari pengguna.
KF-U05 Pengguna dapat melihat
pesebaran lokasi stasiun
Sistem harus dapat menampilkan titik-titik
stasiun pengisian daya di sekitar posisi
pengguna berdasarkan filter otomatis terkait
lokasi dan spesifikasi kendaraan yang di
daftarkan.
KF-U06 Pengguna dapat melihat detail
informasi stasiun pengisian
daya
Sistem harus menyajikan halaman detail stasiun
yang memuat identitas operator, alamat, jam
operasional, transparansi tarif listrik per kWh,
jenis aplikasi operator yang dibutuhkan, serta
status operasional alat terkini.
KF-U07 Pengguna dapat
menggunakan petunjuk arah
guna menuju lokasi
Sistem harus menyediakan tautan terintegrasi
yang meneruskan koordinat stasiun yang dipilih
langsung ke Google Maps.
KF-U08 Pengguna dapat melakukan
pengaduan atau pelaporan
kendala
Sistem harus menyediakan sarana pelaporan
satu ketukan bagi pengguna yang berada di
lokasi stasiun untuk memilih status kondisi alat
(normal/lancar, terjadi kendala teknis, atau
antrean padat).



KF-U09 Pengguna dapat memberikan
rating dan ulasan stasiun
Sistem memungkinkan pengguna untuk
memberikan penilaian rating (bintang 1-5) serta
ulasan tertulis mengenai kebersihan, keamanan,
dan fasilitas sekitar stasiun.
KF-U10 Pengguna dapat menyimpan
stasiun favorit
Sistem memungkinkan pengguna untuk
menyimpan titik stasiun pengisian tertentu ke
dalam daftar favorit akun mereka.

## 3.1.3 Karakteristik Pengguna
Subbab   ini   mengidentifikasi   kategori   pengguna   yang   berinteraksi   dengan   sistem
informasi CasIn.id beserta peran, tanggung jawab, dan batasan hak akses masing-masing aktor.

## Tabel 3. 3 Kategori Pengguna
## Kategori
## Pengguna
## Deskripsi Hak Akses
Admin Pengelola sistem internal yang
bertugas menjaga keakuratan data
master stasiun, data kendaraan,
serta mengelola laporan dan ulasan
dari pengguna
CRUD: Data master stasiun
pengisian daya.
CRUD: Data master spesifikasi
kendaraan listrik.
Read & Delete: Data akun pengguna
terdaftar.
Read & Delete: Laporan kendala dan
ulasan pengguna.
Update: Profil dan kredensial akun
admin.
## Pengguna
## Kendaraan
## Listrik
Pemilik atau pengendara kendaraan
listrik yang menggunakan aplikasi
web untuk mencari tempat cas,
memilah tipe colokan yang sesuai,
melihat rute, serta berbagi informasi
kondisi alat dan ulasan di lokasi
CRUD: Akun registrasi, login/logout,
dan data profil kendaraan aktif.
Read: Peta lokasi stasiun, detail
informasi stasiun, posisi real-time,
dan petunjuk rute navigasi.
Create: Laporan status kondisi mesin
di lokasi.
CRUD: Rating dan ulasan fasilitas
stasiun.



## Kategori
## Pengguna
## Deskripsi Hak Akses
Create, Read, Delete: Daftar stasiun
favorit.

## 3.2 Kebutuhan Non Fungsional
Kebutuhan non-fungsional mendefinisikan batasan karakteristik kualitas, performa teknis,
keandalan,  dan  kepatuhan  sistem  yang  harus  dipenuhi  oleh  platform  CasIn.id  agar  dapat
beroperasi secara optimal dan aman saat digunakan.

Tabel 3. 4 Daftar Kebutuhan Non Fungsional CasIn.id
ID Kebutuhan Non
## Fungsional
## Deskripsi
KNF-01 Kinerja Sistem
Halaman web dapat terbuka cepat maksimal dalam 3
detik,  dan  hasil  pencarian  stasiun  di  peta  langsung
muncul  kurang  dari  1,5  detik  setelah  tombol  filter
ditekan.
KNF-02 Keamanan Data Kata  sandi  akun  pengguna  dan  admin  diacak  agar
tidak  bocor,  serta  seluruh  pengiriman  data  di  web
dilindungi dengan jalur aman.
KNF-03 Keandalan Web dapat dibuka kapan saja selama 24 jam penuh,
dan setiap info kondisi stasiun dari warga selalu
dilengkapi keterangan waktu agar infonya tetap
terpercaya.
KNF-04 Skalabilitas Sistem   tetap   lancar   dan   tidak   lambat   meskipun
dipakai  bersamaan  oleh  ratusan  pengendara  dalam
satu waktu.
KNF-05 Kemudahan    Akses    &
## Fleksibilitas
Tampilan web otomatis rapi dan pas di layar ponsel
pintar   maupun   laptop,   serta   lancar   dibuka   dari
peramban  populer  seperti  Google  Chrome,  Safari,
maupun browser bawaan HP.
KNF-06 Izin dan Privasi
## Pengguna
Lokasi  GPS  hanya  dibaca  jika  pengendara  sudah
menekan  tombol  izin  di  layar,  dan  aplikasi  tidak
melacak posisi pengguna secara diam-diam.



ID Kebutuhan Non
## Fungsional
## Deskripsi


## 3.3 Kebutuhan Teknis
Subbab ini mengidentifikasi kebutuhan teknis dengan menjelaskan secara rinci teknologi
yang akan digunakan dalam pengembangan sistem, termasuk bahasa pemrograman, basis data,
platform, dan perangkat keras yang diperlukan.

Tabel 3. 5 Daftar Kebutuhan Teknis CasIn.id
ID Kebutuhan Teknis Deskripsi
KT-01 Bahasa Pemograman &
## Framework
Menggunakan bahasa pemrograman PHP
(versi 8.2 ke atas) dengan framework Laravel
sebagai fondasi arsitektur backend dan routing
aplikasi web.
KT-02 Admin Dashboard Menggunakan FilamentPHP untuk
membangun panel manajemen data admin
secara cepat, efisien, dan aman guna
mengelola data stasiun, spesifikasi konektor,
serta moderasi laporan komunitas.
KT-03 Sistem Basis Data Menggunakan MySQL / MariaDB sebagai
sistem manajemen basis data relasional untuk
menyimpan tabel pengguna, data koordinat
stasiun, relasi spesifikasi daya/colokan, dan
rekaman status operasional, yang dikelola
melalui antarmuka phpMyAdmin.
KT-04 Antarmuka Peta Interaktif
(Mapping Engine)
Menggunakan pustaka JavaScript open-
source Leaflet.js dengan tile provider
OpenStreetMap (OSM) untuk merender peta
sebaran stasiun pengisian daya secara gratis,
ringan, dan tanpa batas kuota API.
KT-05 Layanan Geolokasi &
## Redireksi Navigasi
Menggunakan antarmuka bawaan HTML5
Geolocation API untuk mendeteksi koordinat
lintang/bujur pengguna secara real-time, serta



ID Kebutuhan Teknis Deskripsi
skema URI scheme redirect menuju Google
Maps untuk panduan rute fisik.
KT-06 Lingkungan Antarmuka
Pengguna (Frontend Styling)
Menggunakan Tailwind CSS untuk
membangun antarmuka web responsif yang
fleksibel dan optimal diakses melalui desktop
maupun mobile browser.
KT-07 Server Web & Lingkungan
## Komputasi
Menggunakan web server Nginx atau Apache
HTTP Server yang dijalankan pada sistem
operasi Linux (Ubuntu Server) atau lingkungan
pengujian lokal XAMPP.
KT-08 Kebutuhan Perangkat Keras
## Server
Lingkungan deployment server membutuhkan
prosesor minimal 2 vCPU, RAM 2 GB, dan
media penyimpanan berbasis SSD sebesar 20
GB untuk memastikan responsivitas kueri data
dan layanan web.
KT-09 Kebutuhan Perangkat
## Pengguna
Pengguna dapat menjalankan sistem melalui
browser modern (Google Chrome, Mozilla
Firefox, Safari, Microsoft Edge) pada
perangkat pintar Android/iOS atau PC/Laptop
dengan koneksi internet aktif.

## 4. Rancangan Sistem
## 4.1 Use Case Diagram
## A. Use Case Diagram
Use case diagram menggambarkan interaksi antara aktor dan fungsi-fungsi pada
sistem CasIn.id.  Terdapat  dua  aktor  yaitu  pengguna  kedaraan  listrik  yang  mencari dan
memanfaatkan stasiun pengisian daya serta admin yang mengelola data dan moderasi
konten.





Gambar 4. 1 Use Case Diagram CasIn.id

## B. Use Case Scenario
Berdasarkan  Use  Case  Diagram  yang  telah  dirancang,  setiap  fungsi  utama  dijabarkan
lebih  rinci  ke  dalam  skenario  interaksi  untuk  menggambarkan  alur  kerja  sistem  secara
teknis. Berikut adalah rincian skenario use case yang menjelaskan alur kerja utama pada
sisi Pengguna Kendaraan Listrik dan Admin untuk sistem CasIn.id:

## 1. Use Case Scenario Pengguna Kendaraan Listrik
Berikut  merupakan use  case  scenario pada  fungsi  utama pengguna
kendaraan listrik yaitu untuk pencarian dan navigasi stasiun:

## Tabel 3. 6 Use Case Scenario Pengguna Kendaraan Listrik
## Name  Pencarian Stasiun Sesuai Spesifikasi Kendaraan Hingga
## Navigasi
Description  Pengguna memanfaatkan peta interaktif untuk
menemukan titik stasiun yang cocok dengan spesifikasi
colokan kendaraan aktifnya, melihat rincian informasi
stasiun, serta meneruskan lokasi ke Google Maps untuk
rute perjalanan.
Precondition  1. Pengguna telah menentukan profil kendaraan aktif
pada akunnya.
- Perangkat pengguna mengaktifkan izin akses geolokasi
## (GPS).



Postcondition  Sistem berhasil membuka aplikasi Google Maps dengan
membawa koordinat latitude dan longitude dari stasiun
tujuan.
Error situation  1. Pengguna menolak izin akses geolokasi saat browser
meminta izin.
- Data koordinat stasiun tidak valid atau tidak terdaftar di
sistem.
System state in
event of an error
Sistem menampilkan pesan peringatan bahwa lokasi GPS
gagal didapatkan, lalu merender peta pada titik pusat
bawaan, serta mematikan fungsi pengalihan navigasi
untuk koordinat yang tidak valid.
Actor  Pengguna kendaraan listrik
Trigger  Pengguna mengakses halaman atau menu utama daftar
stasiun pada aplikasi web CasIn.id.
Standard in process  1. Pengguna membuka aplikasi web CasIn.id dan
mengakses menu daftar stasiun.
- Sistem membaca lokasi geolokasi GPS perangkat
pengguna.
- Sistem memeriksa jenis konektor colokan dari profil
kendaraan aktif pengguna.
- Sistem melakukan kueri dan menampilkan daftar
stasiun pengisian yang urutannya disesuaikan dari jarak
terdekat dan jenis colokan yang cocok.
- Pengguna memilih dan menekan salah satu kartu
stasiun yang diinginkan.
- Sistem mengarahkan pengguna ke halaman detail
stasiun dan menampilkan rincian informasi lengkap (nama
operator, alamat, tarif/kWh, status alat, dan aplikasi
bawaan).
- Pengguna menekan tombol "Navigasi" / "Petunjuk Arah"
pada halaman detail stasiun.
- Sistem mengalihkan pengguna ke aplikasi Google
Maps dengan membawa koordinat lokasi stasiun tujuan.
## Alternative
processes
- . Pada langkah 4, pengguna memilih opsi penyaring
manual.
- Pengguna memilih kriteria kustom (tipe konektor lain
atau daya kW tertentu).
- Sistem memperbarui tampilan daftar kartu stasiun
sesuai kriteria penyaringan baru.
- Alur kembali ke langkah 5 pada Standard In Process.




## 2. Use Case Scenario Admin
Berikut  merupakan use  case  scenario pada  fungsi  utama admin yaitu
untuk manajemen data stasiun pengisian daya:

## Tabel 3. 7 Use Case Scenario Admin
## Name  Manajemen Data Stasiun Pengisian Daya
Description  Admin melakukan penambahan, pembaruan, penampilan,
dan penghapusan data master lokasi stasiun pada
dashboard administrasi untuk memastikan informasi
direktori pada peta pengguna tetap akurat.
Precondition  Admin telah terautentikasi dan login ke Dashboard Admin
Postcondition  Perubahan data master stasiun tersimpan di basis data
MySQL dan secara langsung memperbarui tampilan daftar
kartu serta halaman detail stasiun di sisi pengguna.
Error situation  1. Admin mengosongkan kolom formulir yang bersifat wajib.
- Admin memasukkan format koordinat latitude/longitude
yang tidak valid.
- Terjadi kegagalan koneksi basis data saat penyimpanan.
System state in
event of an error
Sistem membatalkan proses penyimpanan, menampilkan
pesan kesalahan validasi di bawah kolom input yang
bermasalah, dan menjaga input formulir tetap terisi agar
Admin tidak perlu mengulang dari awal.
## Actor  Admin
Trigger  Admin memilih menu "Daftar Stasiun" pada sidebar
dashboard admin.
Standard in process  1. Admin mengakses menu "Daftar Stasiun" pada
dashboard admin.
- Sistem menampilkan tabel daftar seluruh data stasiun
yang terdaftar di basis data.
- Admin menekan tombol "Tambah Stasiun".
- Admin mengisi formulir data stasiun (nama stasiun, tipe
operator, alamat, koordinat latitude/longitude, jenis konektor
colokan, tarif/kWh, dan jam operasional).
- Admin menekan tombol "Simpan".
- Sistem memvalidasi input, menyimpan record baru ke
basis data, dan menampilkan notifikasi "Data Berhasil
## Disimpan".



## Alternative
processes
- ALT-1: Mengubah Data Stasiun
- Pada langkah 2, Admin memilih salah satu data stasiun
dan menekan tombol "Ubah".
- Admin memperbarui informasi yang diperlukan (seperti
tarif/kWh atau status mesin) lalu menekan "Simpan".
- Sistem memperbarui record di basis data dan
menampilkan pesan sukses.

- ALT-2: Menghapus Data Stasiun
- Pada langkah 2, Admin memilih data stasiun dan
menekan tombol "Hapus".
- Sistem menampilkan pop-up konfirmasi penghapusan.
- Admin menekan tombol "Konfirmasi Hapus".
- Sistem menghapus record dari basis data dan
memperbarui tampilan tabel.





## 4.2 Arsitektur Sistem
Sistem   informasi   CasIn.id   dirancang   dengan   mengadopsi   pola   arsitektur 3-Tier
Architecture yang dipadukan dengan pola desain Model-View-Controller (MVC). Pendekatan ini
memisahkan sistem ke dalam tiga tingkatan fungsional utama, yaitu lapisan penyajian, lapisan
logika  aplikasi,  serta  lapisan  basis  data.  Pemisahan  tugas  ini  menjaga  modularitas  kode,
mempermudah  pengujian,   serta   meningkatkan  aspek   keamanan   komunikasi  data   antar-
komponen sistem.
Struktur alur interaksi dan hubungan antar-komponen pada arsitektur CasIn.id dimodelkan pada
diagram berikut:

Gambar 4. 2 Diagram Arsitektur Sistem CasIn.id
Masing-masing tingkatan pada diagram arsitektur di atas memiliki peran spesifik:
- Presentation  Layer: Pengguna  akhir  mengakses  aplikasi  melalui  peramban  web
responsif berbasis Tailwind CSS. Pustaka JavaScript Leaflet.js digunakan bersama
OpenStreetMap  (OSM)  untuk  memvisualisasikan  sebaran  stasiun  pengisian  daya.
Peramban  membaca  titik  koordinat  pengguna  melalui  HTML5  Geolocation  API
setelah memperoleh izin pengguna. Untuk peran admin, FilamentPHP menyediakan



panel administrasi  yang  aman  dan  efisien  untuk  pengelolaan  master  data  stasiun,
data kendaraan, dan moderasi konten.
- Application  /  Logic  Layer: Beroperasi  pada lingkungan  web  server Nginx/Apache
dengan runtime PHP 8.2+ dan framework Laravel. Komponen Controller menerima
dan memvalidasi permintaan HTTP dari klien, lalu mengeksekusi logika pemrosesan
seperti penyaringan stasiun berdasarkan kecocokan soket kendaraan aktif, kalkulasi
jarak  stasiun  terdekat,  serta  pencatatan timestamp laporan  kendala. Komponen
Model menggunakan  Eloquent  ORM  untuk  memetakan  logika  objek  ke  tabel  basis
data sesuai perancangan Class Diagram.
- Data Layer: Menggunakan sistem manajemen basis data relasional MySQL/MariaDB
untuk menyimpan seluruh entitas sistem, mulai dari akun pengguna, data spesifikasi
kendaraan, titik koordinat stasiun, relasi tipe soket/daya, hingga riwayat ulasan serta
laporan kondisi mesin dari komunitas.
- Integrasi  Eksternal: Sistem  terhubung  dengan  server  ubin  OpenStreetMap  untuk
visualisasi  peta  tanpa  batasan  kuota  berbayar,  serta  memanfaatkan  skema  tautan
deep link ke Google Maps guna memandu rute fisik pengendara menuju lokasi stasiun
pengisian yang dipilih.
Pemilihan  arsitektur  dan  platform  di  atas  didasarkan  pada  tinjauan  literatur  rekayasa
perangkat lunak:
- Pola   3-Tier   dan   MVC   Framework   (Laravel): Penerapan   arsitektur Three-Tier
Architecture memisahkan  antarmuka  klien,  logika  bisnis,  dan  basis  data  sehingga
menghasilkan struktur kode yang modular. Hal ini sejalan dengan penelitian (Aipina &
Witriyono,  2022) yang  menunjukkan  bahwa  penggunaan framework Laravel  berbasis
pola  Model-View-Controller  (MVC)  mempermudah  pemeliharaan  sistem,  pengujian
bertahap,   dan   menjamin   efisiensi   pengembangan   sistem   informasi   web   secara
terstruktur.

- Pustaka  Leaflet.js  dan  OpenStreetMap: CasIn.id  dirancang  sebagai  direktori  ringan
yang   harus   dapat   dibuka   cepat   di   peramban   seluler.   Berdasarkan   penelitian
(Rahmayuda  et  al.,  2021),  pustaka  Leaflet  JavaScript  terbukti  andal  sebagai  platform
pemetaan  digital  interaktif  berbasis  web  karena  ringan,  fleksibel  dalam  menampilkan
marker, serta efisien tanpa batasan kuota akses API.


- Pemanfaatan Filament Panel untuk Administrasi Cepat: Penggunaan panel Filament
di atas framework Laravel mereduksi kebutuhan perancangan kode antarmuka CRUD



dan  tabel  data  dari  awal.  Hal  ini  sejalan  dengan  temuan (Sovian  et  al.,  2024)  yang
menunjukkan  bahwa  implementasi  Laravel  Filament  memangkas  waktu  pemrosesan
dan  mempercepat  pembangunan  modul  pendataan  operasional  secara  terstruktur,
sehingga alokasi siklus kerja tim pengembang dapat difokuskan pada penyelesaian fitur
utama sistem.

- Pendelegasian  Rute  Navigasi Eksternal (Google  Maps): Sesuai batasan  sistem
direktori, kalkulasi   rute   dinamis dialihkan   ke   peranti   peta   bawaan pengguna.
Sebagaimana  ditunjukkan  oleh (Karsana  &  Mahendra,  2021),  pengintegrasian titik
koordinat menuju  lokasi  fasilitas  publik  secara  langsung  ke  aplikasi  Google  Maps
memberikan  kepastian  panduan rute aktual  bagi  pengguna  sekaligus  meminimalkan
beban komputasi peladen sistem.

4.3 Rancangan ERD
Perancangan basis data pada sistem informasi CasIn.id dirancang menggunakan model
relasional database untuk  memastikan  integritas  data,  efisiensi  kueri  pencarian  peta,  serta
kemudahan pengelolaan relasi antar entitas. Berdasarkan analisis kebutuhan fungsional aplikasi,
basis data ini terdiri dari beberapa entitas utama yang saling terhubung.

- Entitas Utama dan Atribut
a. user:  Menyimpan data  informasi  akun  pengguna  terdaftar,  baik  pengendara  umum
maupun pengelola. Atribut yang dimiliki meliputi id_user (PK), nama, email, password,
dan created_at.
b. vehicle_masters: Menyimpan  data  master  spesifikasi  kendaraan  listrik  yang  ada  di
pasaran  (seperti  mobil  atau  motor  listrik). Atributnya  terdiri  dari  id_vehicle_masters
(PK), category, brand, model, default_connector_type, dan battery_capacity_kwh.
c. user_vehicle:  Menjadi tabel  relasi  penghubung  antara  pengguna  dan  kendaraan
pilihannya. Atributnya mencakup id_user_vehicle (PK), id_user (FK),
id_vehicle_masters (FK), serta penanda is_active untuk menentukan kendaraan yang
sedang digunakan sebagai acuan filter peta.
d. stations: Entitas yang menyimpan data titik lokasi Stasiun Pengisian Kendaraan Listrik
Umum (SPKLU). Atributnya meliputi id_stations (PK), name, operator_type, address,
operating_hours, price_per_kwh, require_app_name, dan current_status.



e. station_connectors: Menyimpan informasi rinci mengenai jenis colokan dan daya yang
tersedia  pada   suatu   stasiun. Atributnya   mencakup   id_station_connectord   (PK),
id_stations (FK), connector_type, power_kw, dan charging_speed_category.
f. user_favorites: Tabel perantara yang mencatat daftar stasiun favorit pilihan pengguna.
Atributnya  terdiri  dari  id_user_favorites  (PK),  id_user  (FK),  id_stations  (FK),  dan
created_at.
g. station_reports: Entitas yang mencatat riwayat pelaporan kondisi atau kendala cepat
dari komunitas pengguna di suatu stasiun. Atributnya meliputi id_station_report (PK),
id_stations (FK), connector_type, power_kw, dan charging_speed_category.
h. station_reviews: Entitas yang menampung penilaian ulasan dan rating fasilitas stasiun
dari pengguna. Atributnya mencakup id_station_review (PK), id_stations (FK), id_user
(FK), rating, comment, dan created_at.

-  Relasi dan Kardinalitas Antar Entitas
a. user ke user_favorites (1 ke N): Satu pengguna dapat menambahkan banyak stasiun
ke dalam daftar favorit mereka.
b. stations ke user_favorites (1 ke N): Satu titik stasiun dapat difavoritkan oleh banyak
pengguna yang berbeda.
c. user  ke  user_vehicle  (1  ke N):  Satu  pengguna  dapat  mendaftarkan beberapa profil
kendaraan pada akunnya.
d. vehicle_masters ke user_vehicle (1 ke N): Satu data master kendaraan dapat dipilih
oleh banyak pengguna pada profil mereka.
e. stations  ke  station_connectors  (1  ke N):  Satu  stasiun  pengecasan  dapat  memiliki
beberapa tipe konektor dan besaran daya kelistrikan yang berbeda.
f. stations  ke  station_reports  (1  ke N):  Satu  stasiun  dapat  menerima  banyak  laporan
kondisi dari waktu ke waktu.




g. stations ke station_reviews (1 ke N): Satu stasiun dapat memiliki banyak ulasan serta
penilaian rating dari berbagai pengguna.
h. user ke station_reviews (1 ke N): Satu pengguna dapat menuliskan ulasan dan rating
di berbagai stasiun yang pernah dikunjungi.


Gambar 4. 3 Rancangan ERD CasIn.id




## 4.4 Class Diagram
Class diagram menggambarkan struktur statis sistem perangkat lunak CasIn.id dengan
memetakan  entitas  kelas,  atribut,  operasi/metode,  serta  keterhubungan  antar-objek  dalam
domain sistem. Struktur kelas ini dirancang selaras dengan pola arsitektur Model-View-Controller
(MVC) pada framework Laravel yang digunakan.

Gambar 4. 4 Class Diagram CasIn.id

A. Deskripsi Kelas (Class Description)
## 1. User
Mewakili entitas pengguna sistem, baik pengendara kendaraan listrik maupun
administrator.  Kelas  ini  menyimpan  data  kredensial  akun  terenkripsi  serta
menyediakan metode untuk autentikasi dan pengelolaan profil akun.

## 2. Vehicle
Merupakan kelas master data katalog kendaraan listrik yang dikelola langsung
oleh  Administrator.  Kelas  ini  menyimpan  informasi  baku  pabrikan  seperti
kategori motor atau mobil, merek, model, kapasitas baterai, dan jenis colokan
standar bawaan kendaraan. Kelas ini independen dan tidak terikat langsung
dengan user_id.




- UserVehicle
Berfungsi  sebagai  kelas  asosiasi  yang  menghubungkan  User  dengan  data
master  Vehicle  yang  dipilihnya.  Atribut  is_active  digunakan  untuk  menandai
satu  kendaraan  yang  sedang  aktif  digunakan  pengendara,  sehingga  sistem
dapat  menyaring  stasiun di peta  secara  otomatis  sesuai  colokan  kendaraan
tersebut.

## 4. Operator
Menyimpan  data  master  badan  usaha  pengelola  SPKLU.  Memuat  nama
operator,  aset  logo,  dan  nama  aplikasi  bawaan  yang  dibutuhkan  pengguna
untuk menyalakan dispenser pengisian di lokasi.

## 5. Station
Merupakan  kelas  sentral  yang  merepresentasikan  data  fisik  titik  stasiun
pengisian daya. Memuat koordinat geospasial, alamat, jam operasional, serta
estimasi  tarif  per  kWh.  Kelas  ini  memiliki  fungsi  calculateDistance()  untuk
menghitung   jarak   dari   posisi   pengguna   dan   getDirectNavUrl()   untuk
menghasilkan tautan rute ke Google Maps.

- ConnectorType
Menyimpan  data  referensi  tipe  colokan  fisik  yang  umum  digunakan  pada
kendaraan listrik di Indonesia beserta tipe arusnya.

- StationConnector
Berfungsi   sebagai   kelas   perantara   yang   menghubungkan   Station   dan
ConnectorType.  Kelas  ini  mendefinisikan  spesifikasi  daya  pada  stasiun
tertentu.

- ConditionReport
Menampung   laporan   operasional   cepat   dari   pengguna   di   lapangan.
Menyimpan status kelaikan alat, catatan deskriptif, dan timestamp.

## 9. Review
Menyimpan data ulasan kualitatif dan penilaian kuantitatif berupa bintang dari
pengendara terkait fasilitas pendukung di sekitar stasiun.




## 10. Favorite
Menyimpan data penanda stasiun-stasiun pengisian daya yang disimpan oleh
pengguna ke dalam daftar favorit akun mereka.

B. Relasi Antar-Kelas
- User  ke  Vehicle  melalui  UserVehicle  (Many-to-Many): Pengguna  tidak
membuat  data  kendaraan  sendiri,  melainkan  memilih  dari  katalog  master
Vehicle. Satu pengguna dapat memilih beberapa kendaraan ke dalam garasi
akunnya,  dan  satu  model  kendaraan  di  katalog  master  dapat  dipilih  oleh
banyak pengguna berbeda.

- Vehicle ke ConnectorType (Many-to-Many): Satu tipe kendaraan pabrikan
dapat dibekali lebih dari satu jenis colokan.

- Operator  ke  Station  (One-to-Many): Satu  entitas  operator  mengelola  satu
atau banyak titik stasiun pengisian daya di lapangan.

- Station  ke  StationConnector  (Composition): Satu  stasiun  memiliki  satu
atau   lebih   spesifikasi   konektor   daya   listrik.   Eksistensi   data   konektor
bergantung langsung pada keberadaan data stasiun.

- ConnectorType  ke  StationConnector  (One-to-Many): Satu  tipe  colokan
standar  dapat  diterapkan  pada  banyak  dispenser  stasiun  pengisian  yang
berbeda.

- User  ke  ConditionReport  &  Review  (One-to-Many): Pengguna  terdaftar
dapat  mengirimkan  banyak  laporan  kondisi  maupun  ulasan  pada  berbagai
stasiun pengisian daya.

- Station  ke  ConditionReport  &  Review  (One-to-Many): Satu  titik  stasiun
dapat menerima akumulasi banyak laporan kondisi dan ulasan dari berbagai
pengendara.

- User   ke   Station   melalui   Favorite   (Many-to-Many): Pengguna   dapat
menandai  banyak  stasiun  favorit,  dan  satu  stasiun  dapat  difavoritkan  oleh
banyak pengguna sekaligus.




## 4.5 Mockup
Berikut adalah hasil perancangan User Interface Pengendara untuk sistem CasIn.id yang
mencakup alur kerja utama pengguna dari landing page hingga tahap navigasi lokasi:

## 1. Landing Page Pengendara

## Gambar 4. 5 Mockup Landing Page Pengendara Kendaraan Listrik

Gambar  4.5 menampilkan  halaman  utama  yang  pertama  kali  diakses  oleh  calon
pengguna atau pengendara kendaraan listrik. Halaman ini berfungsi mempromosikan nilai
utama  aplikasi  CasIn.id  dengan  menyajikan hero  banner bertuliskan "Temukan  Stasiun
Pengisian  Daya  Terdekat  &  Sesuai  Kendaraanmu",  tombol  aksi  utama  "Cari  Stasiun
Sekarang",  serta  widget  pemilih  kendaraan  aktif.  Pada  bagian  bawah,  halaman  ini
menyajikan  daftar  pratinjau  kartu  stasiun pengisian  daya terdekat  yang  diurutkan  secara
real-time berdasarkan lokasi GPS pengguna.




## 2. Login Pengendara

## Gambar 4. 6 Mockup Halaman Login Pengendara

Gambar  4.6 memperlihatkan  halaman login untuk  pengguna  terdaftar.  Antarmuka  ini
berfungsi sebagai sarana autentikasi keamanan sebelum pengguna dapat mengakses fitur
penuh  aplikasi.  Halaman  ini  memuat  formulir  input Email dan Password,  opsi "Lupa
Password?",  tombol  utama  "Masuk",  serta  fitur  autentikasi  cepat  menggunakan  tombol
"Masuk  dengan  Google".  Terdapat  pula  tautan  pengalihan  bagi  pengguna  yang  belum
memiliki akun untuk menuju ke halaman registrasi.




## 3. Fitur Daftar Stasiun

## Gambar 4. 7 Mockup Halaman Fitur Daftar Stasiun

Gambar   4.7 menyajikan   antarmuka   katalog   daftar   stasiun   pengisian   daya   yang
ditampilkan  setelah  pengguna  masuk  ke  sistem.  Halaman  ini  dilengkapi  dengan  bilah
pencarian nama stasiun/lokasi dan tombol filter. Fitur utama pada halaman ini adalah sajian
kartu  stasiun  yang  diurutkan  dari  jarak  terdekat  dari  lokasi  pengguna.  Setiap  kartu
menampilkan informasi penting berupa nama stasiun, alamat lengkap, status operasional,
jenis konektor, ketersediaan colokan, tarif pengisian per kWh, serta tombol "Lihat Detail".




## 4. Halaman Detail Stasiun

## Gambar 4. 8 Mockup Halaman Detail Stasiun

Gambar 4.8 menampilkan antarmuka rincian informasi teknis dari stasiun pengisian daya
yang dipilih oleh pengguna. Bagian atas halaman memperlihatkan tampilan peta interaktif
dengan pinpoint lokasi  stasiun.  Halaman  ini  memuat  informasi  lengkap  meliputi  nama
stasiun, alamat, operator, indikator ketersediaan mesin, rating ulasan, tabel rincian fasilitas,
tombol aksi "Navigate" untuk pengalihan ke peta navigasi, tombol simpan favorit, bagikan,
serta tombol "Lapor" untuk melaporkan kendala teknis pada lokasi tersebut.




## 5. Halaman Navigasi Google Maps

## Gambar 4. 9 Mockup Halaman Navigasi Google Maps

Gambar  4.9 memperlihatkan  antarmuka  pengalihan  ke  aplikasi  Google  Maps  setelah
pengguna  menekan   tombol  "Navigate"  pada  halaman  detail   stasiun.   Antarmuka   ini
memandu perjalanan pengendara secara real-time dari titik lokasi awal pengguna menuju
koordinat stasiun tujuan dengan menyajikan estimasi waktu tempuh, kondisi lalu lintas, rute
optimal, dan petunjuk arah jalan.







## 5. Metode Pengembangan
Pengembangan  platform  CasIn.id  menerapkan  metodologi  rekayasa  perangkat  lunak
Agile Software Development dengan menggunakan kerangka kerja Scrum. Metode Scrum dipilih
karena  memiliki  fleksibilitas  tinggi  dalam  merespons  perubahan  kebutuhan  sistem  serta
memungkinkan penyelesaian produk dilakukan secara bertahap dan terukur. Kerangka kerja ini
memfasilitasi pengawasan kualitas perangkat lunak melalui iterasi berkala yang terstruktur serta
transparansi koordinasi antar anggota tim.
Seluruh  siklus  pengembangan  dikelola  dan  dipantau  secara  digital  menggunakan
perangkat lunak manajemen proyek Atlassian Jira. Proses pengerjaan proyek dibagi ke dalam 4
siklus iterasi yang berlangsung selama rentang waktu pengerjaan mata kuliah Proyek Perangkat
Lunak, di mana setiap Sprint dialokasikan berdurasi tepat 2 minggu atau setara dengan 14 hari
kerja.
Dalam  setiap  iterasi Sprint,  tim  menerapkan  empat  aktivitas  utama  Scrum  secara  rinci
sebagai berikut:
- Sprint Planning: Pada awal siklus Sprint, Product Owner (PO) bersama seluruh anggota
tim pengembang menyelenggarakan rapat perencanaan untuk meninjau Product Backlog
yang  ada  di  Jira.  PO  menetapkan  prioritas  Kebutuhan  Fungsional  yang  paling  krusial
untuk  diselesaikan.  Tim  pengembang  kemudian  melakukan breakdown User  Story
menjadi  item  pekerjaan  teknis  seperti slicing  UI,  pembuatan  skema  database,  dan
integrasi  API.  Item  pekerjaan  yang  disepakati  bersama  selanjutnya  ditarik  dari  kolom
Backlog ke dalam papan kerja Sprint yang siap dieksekusi.

- Implementation  &  Daily  Standup  Meeting: Tim  pengembang  mengeksekusi  tugas-
tugas  teknis  yang  terdaftar  pada  papan  Scrum  Jira.  Untuk  menjaga  keselarasan  dan
mendeteksi  hambatan  secara  dini,  tim  menyelenggarakan  koordinasi  berkala  singkat.
Selama proses implementasi, seluruh pergerakan status Task diperbarui secara real-time
pada Board   Jira,  bergerak   secara  bertahap   dari   kolom To   Do, In   Progress, In
Review/Testing, hingga mencapai status Done.

- Sprint  Review: Di  akhir  masa Sprint,  tim  menyelenggarakan  sesi Sprint  Review untuk
mendemonstrasikan  unit  perangkat  lunak  yang  telah  selesai  dikembangkan  kepada
dosen pendamping. Product Owner melakukan pengujian kelaikan fungsi terhadap setiap
fitur  yang  telah  dibangun guna  memastikan  bahwa  fungsionalitas  sistem  telah  sesuai
dengan spesifikasi kebutuhan.



- Sprint Retrospective: Setelah Sprint Review selesai, tim mengalokasikan waktu khusus
untuk  melakukan  evaluasi  internal  mengenai  proses  kerja  tim.  Tahapan  ini  bertujuan
untuk mengidentifikasi hal-hal yang telah berjalan baik, menganalisis kendala teknis atau
komunikasi yang dialami selama Sprint, serta menyusun rencana perbaikan yang akan
diterapkan pada Sprint berikutnya agar efektivitas kerja tim terus meningkat.

## 5.1 Jadwal Pengembangan
Jadwal pengembangan platform CasIn.id diawali dengan Sprint 0 untuk tahap inisiasi dan
perancangan proposal, lalu dilanjutkan dengan 3 siklus Sprint pengembangan produk berbasis
Product Backlog Item (PBI) yang dikelola melalui Atlassian Jira:

Tabel 5. 1 Jadwal Pengembangan CasIn.id
## Tahapan Rentang Waktu Product Backlog Item Target Output
## Sprint 0: Inisiasi
## & Perancangan
## Proposal
## Minggu 2 – Minggu
## 3
Analisis domain
permasalahan & SDGs 7
Dokumen proposal
selesai dan Product
Backlog terstruktur
di Jira.
## Identifikasi Kebutuhan
## Sistem
Perancangan BPMN
## Perancangan Diagram
## Penyusunan Dokumen
## Proposal & Setup Jira
## Sprint 1: Sistem
## Utama &
## Pemetaan
## Minggu 4 – Minggu
## 5
[KF-U01] Pendaftaran Akun
## Baru Pengendara Kendaraan
## Listrik
## Terbangunnya
fondasi sistem
autentikasi,
rendering peta
dasar dengan
Leaflet.js, serta
panel administrasi
FilamentPHP untuk
data master
## SPKLU.
[KF-U02] Login dan logout
akun pengendara
[KF-U04] Pembacaan posisi
geolokasi perangkat secara
real-time
[KF-U05] Tampilan
pesebaran stasiun
[KF-A01] Masuk akun admin
[KF-A03] Pengelolaan data
stasiun pengecasan



## Spint 2: Profil
## Kendaraan &
## Navigasi
## Minggu 6 – Minggu
## 7
[KF-U03] Pendaftaran
kendaraan pribadi
Pengguna dapat
menetapkan
kendaraan aktif
untuk filter otomatis
jenis colokan,
melihat
tarif/operator
SPKLU, dan
membuka rute
lokasi ke Google
## Maps.

[KF-U06] Lihat detail
informasi stasiun
[KF-U07] Penggunaan
petunjuk arah
[KF-A05] Mengelola data
kendaraan
## Sprint 3:
## Partisipasi
## Komunitas &
## Moderasi
## Minggu 8 – Minggu
## 9
[KF-U08] Pelaporan kendala
Berjalan fungsi
pelaporan kendala,
fitur ulasan
komunitas,
bookmark stasiun,
modul moderasi
admin, serta
pengujian akhir
[KF-U09] Pemberian rating
dan ulasan
[KF-U10] Penyimpanan
stasiun favorit
[KF-A02] Mengelola akun
pengguna
[KF-A04] Mengelola ulasan
dan laporan kendala dari
pengguna
[KF-A06] Mengelola akun
admin

## 5.2 Tim Pengembang
Pengembangan  platform  CasIn.id  dilaksanakan  oleh  Kelompok  3  yang  terdiri  dari  5
mahasiswa.  Peran  Scrum  Master dalam proyek  ini diampu  langsung  oleh  Dosen  Pembimbing
Mata Kuliah, yang bertugas mengawasi jalannya alur kerja Scrum, memfasilitasi evaluasi, serta
memberikan arahan akademis.
Di tingkat kelompok mahasiswa, pembagian peran difokuskan pada Product Owner (PO)
dan Developers. Tim pengembang menerapkan pendekatan cross-functional atau biasa disebut
Fullstack Developer, di mana setiap pengembang memiliki fleksibilitas untuk berkontribusi secara
kolaboratif pada pengodean frontend, backend, pengelolaan basis data, hingga pengujian fungsi.



Rincian pembagian  peran  dan  tanggung  jawab  internal  kelompok  disajikan  pada  Tabel
5.2 berikut:

Tabel 5. 2 Daftar Tim Pengembang dan Pembagian Peran
No Nama Lengkap NIM Peran Tanggung Jawab
## Utama
## 1. Firyal Jihan Haura 102062400119 Product Owner
Memandu visi produk,
mendefinisikan dan
memprioritaskan Product
Backlog di Jira,
merumuskan User Story
dan Acceptance Criteria,
serta melakukan
pengujian kelaikan fungsi
pada setiap increment
fitur.
## 2.
## Alifia Mareta
## Cahyani
## 102062400086
## Fullstack
## Developer
Bekerja secara cross-
functional dalam
mengeksekusi item
backlog di Jira,
mencakup perancangan
UI/UX, pengodean
frontend & backend,
pengelolaan basis data,
serta pengujian fungsi
secara mandiri dan
kolaboratif.
## 3.
## Muh Naufal Rabbani
## Marun
## 102062400038
## Fullstack
## Developer
## 4.
## Nadhif Maulana
## Faizalty
## 102062400016
## Fullstack
## Developer
## 5.
## Berlian Sepasya
## Rekardian
## 102062400088
## Fullstack
## Developer





## DAFTAR PUSTAKA

Adittya,  A.  P.  (2024).  Kebijakan  Kendaraan  Bermotor  Listrik  Berbasis  Baterai  (KBLBB)  dalam
Transisi Energi di Indonesia. Jurnal Analisis Kebijakan Ekonomi.
Aipina,   D.,   &   Witriyono,   H.   (2022).   PEMANFAATAN   FRAMEWORK   LARAVEL   DAN
## FRAMEWORK  BOOTSTRAP  PADA  PEMBANGUNAN  APLIKASI  PENJUALAN  HIJAB
BERBASIS WEB. Jurnal Media Infotama, 18(1), 2022.
Dharmawan,   I.   P.,   Kumara,   I.   N.   S.,   &   Budiastra,   I.   N.   (2021).   PERKEMBANGAN
INFRASTRUKTUR PENGISIAN  BATERAI  KENDARAAN LISTRIK DI  INDONESIA. Jurnal
## SPEKTRUM, 8(3).
Gebara,  C.  H.,  &  Laurent,  A.  (2023).  National  SDG-7  performance  assessment  to  support
achieving  sustainable  energy  for  all  within  planetary  limits. Renewable  and  Sustainable
Energy Reviews, 173. https://doi.org/10.1016/j.rser.2022.112934
Karsana, I. W. W., & Mahendra, G. S. (2021). SISTEM INFORMASI GEOGRAFIS PEMETAAN
## LOKASI PUSKESMAS MENGGUNAKAN GOOGLE MAPS API DI KABUPATEN BADUNG.
Jurnal Komputer Dan Informatika, 9(2), 160–167. https://doi.org/10.35508/jicon.v9i2.5214
Kuswardani,  Y.  S.  (2024).  DINAMIKA  PERSAINGAN  DAN  PROSPEK  INDUSTRI  MOBIL
LISTRIK DI INDONESIA: ANALISIS TREN DAN IMPLIKASI. Journal Syntax Idea, 6.
Masayu,  R.  O.,  &  Alfiah,  Q.  A.  (2024).  Menuju  Energi  Berkelanjutan:  Dinamika  Penerapan
Kendaraan  Listrik  di  Indonesia. Jurnal  Ilmiah  Wahana  Pendidikan, 10(14),  835–846.
https://doi.org/10.5281/zenodo.13748937
Nur,  A.  I.,  &  Kurniawan,  A.  D.  (2021).  Proyeksi  Masa  Depan  Kendaraan  Listrik  di  Indonesia:
Analisis   Perspektif   Regulasi   dan   Pengendalian   Dampak   Perubahan   Iklim   yang
## Berkelanjutan. Jurnal Hukum Lingkungan Indonesia, 7(2), 197–220.
Permana,  R.,  Yuliati,  E.,  &  Wulandari,  P.  (2023).  Analisis  faktor-faktor  yang  mempengaruhi
konsumen terhadap purchase intention kendaraan listrik di indonesia. Jurnal Inovasi Bisnis
## Dan Manajemen Indonesia, 6(2).
Rahmayuda,  S.,  Suhery,  C.,  &  Ilhamsyah.  (2021).  PEMANFAATAN  LEAFLET  JAVASCRIPT
## SEBAGAI  PALTFORM  PENGEMBANGAN  SISTEM  INFORMASI  GEOGRAFIS  ASET
## PEMERINTAH. CYBERNETICS, 5(01), 26–37.
Regina, D., & Ulmi, N. M. (2023). Tantangan Pengembangan Mobil Listrik Menuju Transportasi
Berkelanjutan di Indonesia. Jurnal Penelitian Sekolah Tinggi Transportasi Darat, 14(1), 22–
## 29.
Sovian,  M.,  Pratiko,  S.,  Wardana,  T.  K.,  &  Asmiatun,  S.  (2024).  IMPLEMENTASI  LARAVEL
## FILAMENT  PADA  APLIKASI  MDIID  UNTUK  PENDATAAN  BILLING  DENGAN  METODE
AGILE    DI    TELKOM    INDONESIA. SEMINAR    NASIONAL    INOVASI    DAN    TREN
## TEKNOLOGI.



Zola, G., Siska, D. N., Andhien, A. R., Dzamar, A. P., & Agustanta, N. (2023). Inovasi kendaraan
listrik  sebagai  upaya  meningkatkan  kelestarian  lingkungan  dan  mendorong  pertumbuhan
ekonomi hijau di Indonesia. E-Jurnal Ekonomi Sumberdaya Dan Lingkungan, 11(3).
Zubaydah,  A.,  Sabilah,  A.  Z.,  Sari,  D.  P.,  &  Hidayah,  F.  N.  A.  (2024).  MENGURANGI  EMISI:
## MENDORONG  TRANSISI  KE  ENERGI  BERSIH  UNTUK  MENGATASI  POLUSI  UDARA.
Journal of Science Education, 4.





## LAMPIRAN

## Link Jira:
https://firyaljihan.atlassian.net/jira/software/projects/SCRUM/boards/1?filter=&groupBy=none&a
tlOrigin=eyJpIjoiMTU2ZGRiN2Y4MmQ4NGIyZGJjNTE2YjA4YzBhZmI3ZDYiLCJwIjoiaiJ9

## Link Draw.io:
https://drive.google.com/file/d/1f55ooUirTAMh-5bw5M8S8zcTawi-O3qQ/view?usp=drive_link



