<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StationReport;
use App\Models\VehicleMaster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StationController extends Controller
{
    public const DEFAULT_LATITUDE = -6.2088;

    public const DEFAULT_LONGITUDE = 106.8456;

    public function home(Request $request): View
    {
        $stations = $this->filterStations($this->getStationsCatalog(), $request);
        $vehicles = $this->getVehiclesCatalog();

        return view('welcome', [
            'stations' => $stations,
            'vehicles' => $vehicles,
        ]);
    }

    public function index(Request $request): View
    {
        $stations = $this->filterStations($this->getStationsCatalog(), $request);
        $vehicles = $this->getVehiclesCatalog();

        return view('stations.index', [
            'stations' => $stations,
            'vehicles' => $vehicles,
        ]);
    }

    public function apiIndex(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $hasValidUserCoords = Station::isValidCoordinatePair($lat, $lng);

        $centerLat = $hasValidUserCoords ? (float) $lat : self::DEFAULT_LATITUDE;
        $centerLng = $hasValidUserCoords ? (float) $lng : self::DEFAULT_LONGITUDE;

        $stations = $this->filterStations($this->getStationsCatalog(), $request);

        return response()->json([
            'success' => true,
            'message' => 'Data sebaran stasiun pengisian daya berhasil dimuat.',
            'data' => [
                'center' => [
                    'latitude' => $centerLat,
                    'longitude' => $centerLng,
                    'source' => $hasValidUserCoords ? 'device' : 'default',
                ],
                'count' => $stations->count(),
                'stations' => $stations->values()->all(),
            ],
        ]);
    }

    public function show(string $id): View
    {
        try {
            if (Schema::hasTable('stations') && Station::query()->exists()) {
                $relations = ['connectors'];
                if (Schema::hasTable('station_reviews')) {
                    $relations[] = 'reviews.user';
                }

                $targetModel = Station::with($relations)->find($id)
                    ?? Station::with($relations)->orderBy('id_stations')->first();

                if ($targetModel) {
                    $station = $this->formatStationDetail($targetModel);
                    $targetLat = (float) $station['latitude'];
                    $targetLng = (float) $station['longitude'];

                    // Kirim hanya stasiun terdekat dalam format ringkas agar halaman detail tetap ringan dan cepat.
                    $nearbyStations = Station::query()
                        ->get(['id_stations', 'name', 'address', 'latitude', 'longitude', 'current_status'])
                        ->map(function (Station $s) use ($targetLat, $targetLng): array {
                            $lat = (float) $s->latitude;
                            $lng = (float) $s->longitude;
                            $dist = Station::isValidCoordinatePair($targetLat, $targetLng) && $s->hasValidCoordinates()
                                ? Station::haversineDistanceKm($targetLat, $targetLng, $lat, $lng)
                                : 99999.0;

                            return [
                                'id' => (string) $s->id_stations,
                                'name' => $s->name,
                                'address' => $s->address,
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'current_status' => $s->current_status,
                                '_dist' => $dist,
                            ];
                        })
                        ->sortBy('_dist')
                        ->take(30)
                        ->map(function (array $item): array {
                            unset($item['_dist']);

                            return $item;
                        })
                        ->values();

                    return view('stations.show', [
                        'station' => $station,
                        'allStations' => $nearbyStations,
                    ]);
                }
            }
        } catch (\Throwable) {
            // Fallback ke data pratinjau bawaan jika basis data belum dimigrasi
        }

        $defaults = $this->getDefaultStationsCatalog();
        $station = $defaults->firstWhere('id', (string) $id) ?? $defaults->first();

        return view('stations.show', [
            'station' => $station,
            'allStations' => $defaults,
        ]);
    }

    public function report(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:normal,technical_issue,busy'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (Schema::hasTable('stations')) {
            $station = Station::with('connectors')->find($id);
            if ($station) {
                $station->update([
                    'current_status' => $validated['status'],
                ]);

                if (Schema::hasTable('station_reports')) {
                    $primaryConnector = $station->connectors->first();
                    $reportStatus = match ($validated['status']) {
                        'technical_issue' => 'rusak',
                        'busy' => 'antre',
                        default => 'normal',
                    };

                    StationReport::create([
                        'id_stations' => $station->id_stations,
                        'connector_type' => $primaryConnector?->connector_type ?? 'CCS2',
                        'power_kw' => (float) ($primaryConnector?->power_kw ?? 50),
                        'charging_speed_category' => $primaryConnector?->charging_speed_category ?? 'ultra',
                        'status' => $reportStatus,
                        'notes' => $validated['notes'] ?? null,
                        'reported_at' => now(),
                    ]);
                }
            }
        }

        $statusLabel = match ($validated['status']) {
            'normal' => 'Tersedia (Normal)',
            'busy' => 'Antrean Padat',
            'technical_issue' => 'Kendala Teknis',
            default => 'Diperbarui',
        };

        return redirect()
            ->route('stations.show', $id)
            ->with('status', "Laporan kondisi stasiun ({$statusLabel}) berhasil dikirim. Terima kasih atas kontribusi Anda.");
    }

    private function filterStations(Collection $stations, Request $request): Collection
    {
        $search = trim((string) $request->query('q', ''));
        $connector = trim((string) $request->query('connector', ''));
        $operator = trim((string) $request->query('operator', ''));
        $status = trim((string) $request->query('status', ''));
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $searchLower = strtolower($search);
        $connectorLower = strtolower($connector);

        $filtered = $stations->filter(function (array $station) use ($searchLower, $connectorLower, $operator, $status) {
            if ($searchLower !== '') {
                $haystack = strtolower($station['name'].' '.$station['address'].' '.$station['operator_badge']);
                if (! str_contains($haystack, $searchLower)) {
                    return false;
                }
            }

            if ($connectorLower !== '' && ! $this->matchesConnectorFilter($station, $connectorLower)) {
                return false;
            }

            if ($operator !== '' && strcasecmp($station['operator_badge'], $operator) !== 0) {
                return false;
            }

            if ($status !== '' && strcasecmp($station['current_status'], $status) !== 0) {
                return false;
            }

            return true;
        })->values();

        if (Station::isValidCoordinatePair($lat, $lng)) {
            $userLat = (float) $lat;
            $userLng = (float) $lng;

            $filtered = $filtered->map(function (array $station) use ($userLat, $userLng): array {
                if (Station::isValidCoordinatePair($station['latitude'] ?? null, $station['longitude'] ?? null)) {
                    $station['distance_km'] = Station::haversineDistanceKm(
                        $userLat,
                        $userLng,
                        (float) $station['latitude'],
                        (float) $station['longitude']
                    );
                }

                return $station;
            })->sortBy('distance_km')->values();
        } elseif (! empty($filtered->first()['_from_db'])) {
            $filtered = $filtered->sortBy('distance_km')->values();
        }

        return $filtered->map(function (array $station): array {
            unset($station['_from_db']);

            return $station;
        });
    }

    /**
     * @param  array<string, mixed>  $station
     */
    private function matchesConnectorFilter(array $station, string $connectorLower): bool
    {
        if (strtolower((string) ($station['connector_type'] ?? '')) === $connectorLower) {
            return true;
        }

        $searchTokens = strtolower((string) ($station['connector_search'] ?? ''));
        if ($searchTokens !== '' && str_contains($searchTokens, $connectorLower)) {
            return true;
        }

        return collect($station['charging_boxes'] ?? [])->contains(function (array $box) use ($connectorLower) {
            return str_contains(strtolower($box['connector_spec'] ?? ''), $connectorLower);
        });
    }

    private function getVehiclesCatalog(): Collection
    {
        $defaults = collect([
            [
                'id' => 'ioniq-5',
                'category' => 'Mobil Listrik',
                'brand' => 'Hyundai',
                'model' => 'Ioniq 5',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 72.6,
            ],
            [
                'id' => 'air-ev',
                'category' => 'Mobil Listrik',
                'brand' => 'Wuling',
                'model' => 'Air EV Long Range',
                'default_connector_type' => 'AC Type 2',
                'battery_capacity_kwh' => 26.7,
            ],
            [
                'id' => 'byd-seal',
                'category' => 'Mobil Listrik',
                'brand' => 'BYD',
                'model' => 'Seal Performance',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 82.5,
            ],
            [
                'id' => 'kona-ev',
                'category' => 'Mobil Listrik',
                'brand' => 'Hyundai',
                'model' => 'Kona Electric',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 66.0,
            ],
            [
                'id' => 'chery-omoda-e5',
                'category' => 'Mobil Listrik',
                'brand' => 'Chery',
                'model' => 'Omoda E5',
                'default_connector_type' => 'CCS2',
                'battery_capacity_kwh' => 61.0,
            ],
            [
                'id' => 'alva-one',
                'category' => 'Motor Listrik',
                'brand' => 'Alva',
                'model' => 'Alva One',
                'default_connector_type' => 'AC Type 2',
                'battery_capacity_kwh' => 2.7,
            ],
        ]);

        try {
            if (Schema::hasTable('vehicle_masters') && VehicleMaster::count() > 0) {
                return VehicleMaster::all()->map(fn (VehicleMaster $v) => [
                    'id' => (string) $v->id_vehicle_masters,
                    'category' => $v->category,
                    'brand' => $v->brand,
                    'model' => $v->model,
                    'default_connector_type' => $v->default_connector_type,
                    'battery_capacity_kwh' => (float) $v->battery_capacity_kwh,
                ]);
            }
        } catch (\Throwable) {
            // Fallback ke katalog standar jika tabel belum siap
        }

        return $defaults;
    }

    private function getStationsCatalog(): Collection
    {
        try {
            if (Schema::hasTable('stations') && Station::query()->exists()) {
                return Station::with('connectors')
                    ->get()
                    ->map(fn (Station $station, int $idx): array => $this->formatStationListItem($station, $idx));
            }
        } catch (\Throwable) {
            // Gunakan data pratinjau bawaan jika koneksi/tabel belum dimigrasi
        }

        return $this->getDefaultStationsCatalog();
    }

    /**
     * Format ringkas stasiun untuk katalog, pencarian, peta, dan endpoint JSON publik.
     *
     * @return array<string, mixed>
     */
    private function formatStationListItem(Station $station, int $idx = 0): array
    {
        $statusLabel = match ($station->current_status) {
            'normal' => 'Tersedia',
            'busy' => 'Antrean Padat',
            'technical_issue' => 'Kendala Teknis',
            default => 'Tersedia',
        };

        $isImported = ($station->source ?? 'manual') !== 'manual';
        $price = $isImported
            ? (int) ($station->price_per_kwh ?? 0)
            : (int) ($station->price_per_kwh ?: 2000);

        $connectors = $station->connectors;
        // Prioritaskan konektor berdaya tertinggi sambil merangkum seluruh tipe soket unik di lokasi.
        $sortedConnectors = $connectors->sortByDesc(fn ($c) => (float) $c->power_kw)->values();
        $primaryConnector = $sortedConnectors->first();
        $connectorType = $primaryConnector?->connector_type ?? ($isImported ? 'Belum diketahui' : 'CCS2');
        $distinctConnectorTypes = $sortedConnectors
            ->pluck('connector_type')
            ->filter(fn ($type) => is_string($type) && trim($type) !== '')
            ->unique()
            ->values()
            ->all();
        $connectorSummary = $distinctConnectorTypes !== []
            ? implode(', ', $distinctConnectorTypes)
            : $connectorType;
        $power = (float) ($primaryConnector?->power_kw ?? ($isImported ? 0 : 50));
        $currentType = $primaryConnector?->current_type ?? ($isImported ? '-' : 'DC');
        $available = $connectors->isNotEmpty() ? (int) $connectors->sum('available_connectors') : 0;
        $total = $connectors->isNotEmpty() ? (int) $connectors->sum('total_connectors') : 0;

        $lat = (float) $station->latitude;
        $lng = (float) $station->longitude;
        $isValidCoords = Station::isValidCoordinatePair($lat, $lng);

        $distanceKm = $isValidCoords
            ? Station::haversineDistanceKm(self::DEFAULT_LATITUDE, self::DEFAULT_LONGITUDE, $lat, $lng)
            : round(1.2 + ($idx * 0.8), 1);

        $id = (string) $station->id_stations;

        return [
            'id' => $id,
            'name' => $station->name,
            'operator_badge' => match ($station->operator_type) {
                'government' => 'PLN',
                'private' => 'SWASTA',
                default => 'KOMERSIAL',
            },
            'operator_type' => $station->operator_type,
            'source' => (string) ($station->source ?? 'manual'),
            'address' => $station->address,
            'latitude' => $lat,
            'longitude' => $lng,
            'distance_km' => $distanceKm,
            'operating_hours' => $station->operating_hours,
            'price_per_kwh' => $price,
            'require_app_name' => $station->require_app_name ?? ($isImported ? 'Belum diketahui' : 'PLN Mobile'),
            'current_status' => $station->current_status,
            'status_label' => $statusLabel,
            'connector_type' => $connectorType,
            'connector_types' => $distinctConnectorTypes,
            'connector_summary' => $connectorSummary,
            'connector_search' => $this->buildConnectorSearchTokens($connectors, $connectorType),
            'power_kw' => $power,
            'current_type' => $currentType,
            'available_chargers' => $available,
            'total_chargers' => $total,
            'is_valid_coordinate' => $isValidCoords,
            'direct_nav_url' => $isValidCoords
                ? sprintf('https://www.google.com/maps/dir/?api=1&destination=%s,%s', $lat, $lng)
                : null,
            'detail_url' => url("/stasiun/{$id}"),
            '_from_db' => true,
        ];
    }

    /**
     * Format lengkap stasiun beserta kotak pengisian, ulasan, dan fasilitas untuk halaman detail.
     *
     * @return array<string, mixed>
     */
    private function formatStationDetail(Station $station): array
    {
        $base = $this->formatStationListItem($station);
        unset($base['_from_db']);

        $isImported = ($station->source ?? 'manual') !== 'manual';
        $price = (int) $base['price_per_kwh'];
        $priceLabel = $price > 0 ? "Rp {$price}/kWh" : 'Tarif belum diketahui';
        $connectors = $station->connectors;

        $facilities = match (true) {
            $isImported => [
                ['name' => 'Fasilitas belum terverifikasi', 'detail' => 'Data stasiun ini berasal dari sumber '.strtoupper((string) $station->source).'. Bantu lengkapi melalui fitur laporan.'],
            ],
            str_contains($station->name, 'Rest Area') => [
                ['name' => 'Fasilitas Rest Area 24 Jam', 'detail' => 'SPBU, minimarket, dan pujasera siap melayani sepanjang hari'],
                ['name' => 'Masjid & Toilet Bersih', 'detail' => 'Kapasitas luas dan terawat di area utama rest area'],
                ['name' => 'Petugas Keamanan & CCTV', 'detail' => 'Pengawasan keamanan 24 jam di sekitar titik dispenser'],
                ['name' => 'Bengkel & Layanan Darurat', 'detail' => 'Bantuan teknis dan derek jalan tol terdekat'],
            ],
            str_contains($station->name, 'Mall') || str_contains($station->name, 'Plaza') || $station->operator_type === 'commercial' => [
                ['name' => 'Akses Lobi Mal & Parkir Khusus EV', 'detail' => 'Slot parkir prioritas dekat lift dan eskalator utama'],
                ['name' => 'Kafe, Restoran & Food Court', 'detail' => 'Pilihan kuliner lengkap saat menunggu kendaraan terisi'],
                ['name' => 'Toilet Bersih & Ruang Nursery', 'detail' => 'Fasilitas keluarga terawat di setiap lantai mal'],
                ['name' => 'Keamanan & Petugas Parkir', 'detail' => 'Area parkir diawasi petugas dan kamera CCTV'],
            ],
            $station->operator_type === 'private' => [
                ['name' => 'EV Lounge & Wi-Fi Cepat Gratis', 'detail' => 'Ruang santai nyaman ber-AC untuk pengendara'],
                ['name' => 'Minimarket & Kafe Buka 24 Jam', 'detail' => 'Tersedia minuman kopi dan makanan ringan'],
                ['name' => 'Pembayaran Mandiri Digital', 'detail' => 'Dukungan penuh transaksi aplikasi, QRIS, dan kartu bank'],
                ['name' => 'Penerangan & CCTV 24 Jam', 'detail' => 'Pencahayaan terang dan aman di malam hari'],
            ],
            default => [
                ['name' => 'Kanopi Peneduh Khusus EV', 'detail' => 'Melindungi kendaraan dan pengendara dari panas maupun hujan'],
                ['name' => 'Ruang Tunggu Ber-AC', 'detail' => 'Tempat istirahat nyaman tepat di samping unit pengisian daya'],
                ['name' => 'Toilet & Mushola', 'detail' => 'Fasilitas bersih dan buka 24 jam di area gedung'],
                ['name' => 'Petugas Siaga 24 Jam', 'detail' => 'Petugas operasional siap membantu kendala teknis di lokasi'],
            ],
        };

        $dbReviews = $station->relationLoaded('reviews') ? $station->reviews : collect();
        $reviews = $dbReviews->isNotEmpty()
            ? $dbReviews->sortByDesc('id_station_review')->map(fn ($rev) => [
                'user' => $rev->user?->name ?? 'Pengendara EV',
                'rating' => (int) $rev->rating,
                'time' => $rev->created_at ? $rev->created_at->diffForHumans() : 'Baru saja',
                'comment' => $rev->comment ?? '',
            ])->values()->all()
            : match (true) {
                $isImported => [],
                str_contains($station->name, 'Rest Area') => [
                    [
                        'user' => 'Bambang Sudiro',
                        'rating' => 5,
                        'time' => '3 jam yang lalu',
                        'comment' => 'Sangat membantu saat perjalanan tol. Pengisian DC Ultra Fast bekerja optimal dan fasilitas rest area sangat lengkap.',
                    ],
                    [
                        'user' => 'Hendra Wijaya',
                        'rating' => 4,
                        'time' => 'Kemarin',
                        'comment' => 'Konektor langsung terkoneksi, antrean tertib dan kanopi peneduh sangat nyaman saat hujan deras.',
                    ],
                ],
                str_contains($station->name, 'Mall') || $station->operator_type === 'commercial' => [
                    [
                        'user' => 'Siti Rahmawati',
                        'rating' => 5,
                        'time' => '1 hari yang lalu',
                        'comment' => 'Lokasi parkir EV sangat mudah ditemukan di basement. Ditinggal belanja dan makan di mal, baterai mobil sudah penuh.',
                    ],
                    [
                        'user' => 'Kevin Sanjaya',
                        'rating' => 5,
                        'time' => '3 hari yang lalu',
                        'comment' => 'Dispenser bersih, colokan kokoh, dan kecepatan pengisian stabil dari 20% ke 80% dalam 40 menit.',
                    ],
                ],
                default => [
                    [
                        'user' => 'Rizky Pratama',
                        'rating' => 5,
                        'time' => '2 jam yang lalu',
                        'comment' => 'Area parkir luas, atap peneduh kokoh, dan konektor berfungsi lancar tanpa kendala.',
                    ],
                    [
                        'user' => 'Nadia S.',
                        'rating' => 4,
                        'time' => 'Kemarin',
                        'comment' => 'Lokasi strategis, penerangan malam terang benderang, serta petugas keamanan sigap membantu.',
                    ],
                ],
            };

        $avgRating = $dbReviews->isNotEmpty()
            ? (int) round($dbReviews->avg('rating'))
            : ($isImported ? 0 : 4);

        $chargingBoxes = $connectors->isNotEmpty()
            ? $connectors->map(fn ($item) => [
                'name' => in_array($item->connector_type, ['DC Connector', 'AC Connector'], true)
                    ? 'Dispenser '.$item->current_type.' '.rtrim(rtrim(number_format((float) $item->power_kw, 1, '.', ''), '0'), '.').' kW'
                    : $item->connector_type.' Charger',
                'connector_count' => $item->available_connectors.'/'.$item->total_connectors.' Konektor',
                'status_badge' => $item->available_connectors > 0 ? 'Tersedia' : 'Penuh',
                'status_tone' => $item->available_connectors > 0 ? 'available' : 'used',
                'connector_spec' => strtoupper($item->connector_type).' - '.$item->power_kw.'kW '.$item->current_type,
                'price_label' => $priceLabel,
                'estimation_label' => $item->charging_speed_category === 'ultra' ? 'Est. 45 Menit' : 'Est. 02 Jam 21 Menit',
            ])->values()->all()
            : ($isImported ? [
                [
                    'name' => 'Detail konektor belum tercatat',
                    'connector_count' => '- Konektor',
                    'status_badge' => 'Belum terverifikasi',
                    'status_tone' => 'used',
                    'connector_spec' => 'Jenis dan daya konektor belum diketahui',
                    'price_label' => $priceLabel,
                    'estimation_label' => '-',
                ],
            ] : [
                [
                    'name' => 'Mobi AC Stand',
                    'connector_count' => '1 Konektor',
                    'status_badge' => '1 Sedang digunakan',
                    'status_tone' => 'used',
                    'connector_spec' => 'AC TYPE 2 - 22kW',
                    'price_label' => "Rp {$price}/kWh",
                    'estimation_label' => 'Est. 02 Jam 21 Menit',
                ],
            ]);

        return array_merge($base, [
            'rating' => $avgRating,
            'charging_boxes' => $chargingBoxes,
            'reviews' => $reviews,
            'facilities' => $facilities,
        ]);
    }

    private function buildConnectorSearchTokens(Collection $connectors, string $fallbackType): string
    {
        if ($connectors->isEmpty()) {
            return strtolower($fallbackType);
        }

        $tokens = [];
        foreach ($connectors as $c) {
            $type = strtolower((string) $c->connector_type);
            $tokens[] = $type;
            $tokens[] = $type.' '.$c->power_kw.'kw '.strtolower((string) $c->current_type);
            // Kompatibilitas umum SPKLU publik Indonesia agar filter kendaraan (CCS2 / AC Type 2) tetap menemukan titik PLN/CPO yang hanya mencatat arus AC/DC.
            if ($type === 'dc connector') {
                $tokens[] = 'ccs2';
            } elseif ($type === 'ac connector') {
                $tokens[] = 'ac type 2';
            }
        }

        return implode(' | ', array_unique($tokens));
    }

    private function getDefaultStationsCatalog(): Collection
    {
        return collect([
            [
                'id' => '1',
                'name' => 'PLN SPKLU Substation Central',
                'operator_badge' => 'PLN',
                'operator_type' => 'government',
                'source' => 'manual',
                'address' => 'Jl. Letjen S. Parman No. 28, Jakarta Barat',
                'latitude' => -6.175392,
                'longitude' => 106.790512,
                'distance_km' => 1.2,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2000,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'status_label' => 'Tersedia',
                'connector_type' => 'CCS2',
                'connector_search' => 'ccs2 | ac type 2',
                'power_kw' => 50,
                'current_type' => 'DC',
                'available_chargers' => 3,
                'total_chargers' => 4,
                'rating' => 4,
                'charging_boxes' => [
                    [
                        'name' => 'Mobi AC Stand',
                        'connector_count' => '1 Konektor',
                        'status_badge' => '1 Sedang digunakan',
                        'status_tone' => 'used',
                        'connector_spec' => 'AC TYPE 2 - 22kW',
                        'price_label' => 'Rp 2000/kWh',
                        'estimation_label' => 'Est. 02 Jam 21 Menit',
                    ],
                    [
                        'name' => 'Delta DC Ultra Fast',
                        'connector_count' => '2 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'CCS2 - 50kW DC',
                        'price_label' => 'Rp 2000/kWh',
                        'estimation_label' => 'Est. 45 Menit',
                    ],
                    [
                        'name' => 'ABB Terra DC Stand',
                        'connector_count' => '1 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'CCS2 - 50kW DC',
                        'price_label' => 'Rp 2000/kWh',
                        'estimation_label' => 'Est. 45 Menit',
                    ],
                ],
                'reviews' => [
                    [
                        'user' => 'Rizky Pratama',
                        'rating' => 5,
                        'time' => '2 jam yang lalu',
                        'comment' => 'Area parkir luas, atap peneduh aman saat hujan, dan konektor CCS2 langsung berfungsi lancar lewat aplikasi PLN Mobile.',
                    ],
                    [
                        'user' => 'nadia S.',
                        'rating' => 4,
                        'time' => 'Kemarin',
                        'comment' => 'Lokasi strategis dekat pusat perbelanjaan, penerangan malam sangat terang dan ada petugas keamanan 24 jam.',
                    ],
                ],
                'facilities' => [
                    ['name' => 'Atap Peneduh Kanopi', 'detail' => 'Melindungi kendaraan saat pengisian daya'],
                    ['name' => 'Kafe & Minimarket', 'detail' => 'Tersedia ruang tunggu ber-AC 50 meter dari titik SPKLU'],
                    ['name' => 'Toilet & Mushola', 'detail' => 'Bersih dan buka 24 jam di area gedung utama'],
                    ['name' => 'Keamanan & CCTV 24 Jam', 'detail' => 'Area dipantau petugas keamanan dan kamera pengawas'],
                ],
            ],
            [
                'id' => '2',
                'name' => 'PLN SPKLU Gambir Utama',
                'operator_badge' => 'PLN',
                'operator_type' => 'government',
                'source' => 'manual',
                'address' => 'Jl. Medan Merdeka Timur No. 1, Jakarta Pusat',
                'latitude' => -6.176655,
                'longitude' => 106.830589,
                'distance_km' => 2.4,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'status_label' => 'Tersedia',
                'connector_type' => 'CCS2',
                'connector_search' => 'ccs2 | ac type 2',
                'power_kw' => 50,
                'current_type' => 'DC',
                'available_chargers' => 3,
                'total_chargers' => 4,
                'rating' => 5,
                'charging_boxes' => [
                    [
                        'name' => 'Starvo Fast Dispenser A',
                        'connector_count' => '2 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'CCS2 - 50kW DC',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 40 Menit',
                    ],
                    [
                        'name' => 'Mobi AC Stand B',
                        'connector_count' => '2 Konektor',
                        'status_badge' => '1 Sedang digunakan',
                        'status_tone' => 'used',
                        'connector_spec' => 'AC TYPE 2 - 22kW',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 02 Jam 10 Menit',
                    ],
                ],
                'reviews' => [
                    [
                        'user' => 'Dimas Wahyu',
                        'rating' => 5,
                        'time' => '3 jam yang lalu',
                        'comment' => 'Mesin sangat terawat, pengisian dari 20% ke 80% hanya butuh sekitar 35 menit.',
                    ],
                ],
                'facilities' => [
                    ['name' => 'Area Tunggu Pengendara', 'detail' => 'Kursi tunggu teduh tepat di samping dispenser'],
                    ['name' => 'Minimarket & ATM', 'detail' => 'Tersedia di area lobi stasiun'],
                    ['name' => 'Toilet Umum', 'detail' => 'Tersedia dan bersih'],
                ],
            ],
            [
                'id' => '3',
                'name' => 'CasIn Hub Sudirman',
                'operator_badge' => 'SWASTA',
                'operator_type' => 'private',
                'source' => 'manual',
                'address' => 'Jl. Jend. Sudirman No. 52, Jakarta Pusat',
                'latitude' => -6.224510,
                'longitude' => 106.809640,
                'distance_km' => 3.1,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'CasIn Partner App',
                'current_status' => 'normal',
                'status_label' => 'Tersedia',
                'connector_type' => 'CCS2',
                'connector_search' => 'ccs2 | ac type 2',
                'power_kw' => 100,
                'current_type' => 'DC',
                'available_chargers' => 4,
                'total_chargers' => 4,
                'rating' => 5,
                'charging_boxes' => [
                    [
                        'name' => 'HyperDC Sudirman A',
                        'connector_count' => '2 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'CCS2 - 100kW DC',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 25 Menit',
                    ],
                    [
                        'name' => 'AC Type 2 Stand B',
                        'connector_count' => '2 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'AC TYPE 2 - 22kW',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 02 Jam 00 Menit',
                    ],
                ],
                'reviews' => [
                    [
                        'user' => 'Nadia Putri',
                        'rating' => 5,
                        'time' => '1 hari yang lalu',
                        'comment' => 'Pengisian DC 100kW sangat cepat, lounge ruang tunggu nyaman.',
                    ],
                ],
                'facilities' => [
                    ['name' => 'Coffee Lounge & Wi-Fi', 'detail' => 'Tersedia di lobi utama gedung'],
                    ['name' => 'Atap Peneduh Kanopi', 'detail' => 'Area parkir basement terlindung penuh'],
                ],
            ],
            [
                'id' => '4',
                'name' => 'PLN SPKLU Thamrin',
                'operator_badge' => 'PLN',
                'operator_type' => 'government',
                'source' => 'manual',
                'address' => 'Jl. M.H. Thamrin No. 9, Jakarta Pusat',
                'latitude' => -6.189120,
                'longitude' => 106.823110,
                'distance_km' => 1.8,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'busy',
                'status_label' => 'Antrean Padat',
                'connector_type' => 'CCS2',
                'connector_search' => 'ccs2 | ac type 2',
                'power_kw' => 50,
                'current_type' => 'DC',
                'available_chargers' => 1,
                'total_chargers' => 3,
                'rating' => 4,
                'charging_boxes' => [
                    [
                        'name' => 'PLN Fast Charger 1',
                        'connector_count' => '2 Konektor',
                        'status_badge' => '2 Sedang digunakan',
                        'status_tone' => 'used',
                        'connector_spec' => 'CCS2 - 50kW DC',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 45 Menit',
                    ],
                    [
                        'name' => 'PLN AC Type 2',
                        'connector_count' => '1 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'AC TYPE 2 - 22kW',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 02 Jam 15 Menit',
                    ],
                ],
                'reviews' => [
                    [
                        'user' => 'Arif Ramadhan',
                        'rating' => 4,
                        'time' => 'Kemarin',
                        'comment' => 'Lokasi di pusat kota, jam pulang kantor cukup ramai.',
                    ],
                ],
                'facilities' => [
                    ['name' => 'Pos Keamanan 24 Jam', 'detail' => 'Area diawasi petugas keamanan'],
                    ['name' => 'Minimarket', 'detail' => 'Berjarak 30 meter dari titik pengisian'],
                ],
            ],
            [
                'id' => '5',
                'name' => 'Charge+ Grand Indonesia',
                'operator_badge' => 'SWASTA',
                'operator_type' => 'private',
                'source' => 'manual',
                'address' => 'Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                'latitude' => -6.195180,
                'longitude' => 106.820920,
                'distance_km' => 2.1,
                'operating_hours' => '10.00 - 22.00 WIB',
                'price_per_kwh' => 2500,
                'require_app_name' => 'Charge+ App',
                'current_status' => 'normal',
                'status_label' => 'Tersedia',
                'connector_type' => 'AC Type 2',
                'connector_search' => 'ac type 2',
                'power_kw' => 22,
                'current_type' => 'AC',
                'available_chargers' => 3,
                'total_chargers' => 4,
                'rating' => 5,
                'charging_boxes' => [
                    [
                        'name' => 'Charge+ AC Wallbox',
                        'connector_count' => '4 Konektor',
                        'status_badge' => 'Tersedia',
                        'status_tone' => 'available',
                        'connector_spec' => 'AC TYPE 2 - 22kW',
                        'price_label' => 'Rp 2500/kWh',
                        'estimation_label' => 'Est. 02 Jam 00 Menit',
                    ],
                ],
                'reviews' => [
                    [
                        'user' => 'Salsa Maharani',
                        'rating' => 5,
                        'time' => '2 hari yang lalu',
                        'comment' => 'Cocok sambil belanja di mal, parkir khusus EV terjaga rapi.',
                    ],
                ],
                'facilities' => [
                    ['name' => 'Pusat Perbelanjaan & Restoran', 'detail' => 'Akses langsung ke lobi mal'],
                    ['name' => 'Toilet & Mushola', 'detail' => 'Tersedia di setiap lantai parkir'],
                ],
            ],
            [
                'id' => '6',
                'name' => 'Shell Recharge Kemang',
                'operator_badge' => 'SWASTA',
                'operator_type' => 'private',
                'source' => 'manual',
                'address' => 'Jl. Kemang Raya No. 42, Jakarta Selatan',
                'latitude' => -6.260710,
                'longitude' => 106.813420,
                'distance_km' => 4.8,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'Shell Asia App',
                'current_status' => 'technical_issue',
                'status_label' => 'Kendala Teknis',
                'connector_type' => 'CCS2',
                'connector_search' => 'ccs2',
                'power_kw' => 60,
                'current_type' => 'DC',
                'available_chargers' => 0,
                'total_chargers' => 2,
                'rating' => 3,
                'charging_boxes' => [
                    [
                        'name' => 'Shell DC Fast Kemang',
                        'connector_count' => '2 Konektor',
                        'status_badge' => 'Perawatan Teknis',
                        'status_tone' => 'used',
                        'connector_spec' => 'CCS2 - 60kW DC',
                        'price_label' => 'Rp 2466/kWh',
                        'estimation_label' => 'Est. 40 Menit',
                    ],
                ],
                'reviews' => [
                    [
                        'user' => 'Dimas Prakoso',
                        'rating' => 3,
                        'time' => '1 hari yang lalu',
                        'comment' => 'Sedang dalam pemeliharaan teknisi sore ini.',
                    ],
                ],
                'facilities' => [
                    ['name' => 'Shell Select Minimarket & Cafe', 'detail' => 'Buka 24 jam'],
                    ['name' => 'Toilet Bersih', 'detail' => 'Tersedia di area SPBU'],
                ],
            ],
        ])->map(fn (array $item): array => $this->enrichStationNavigation($item));
    }

    /**
     * @param  array<string, mixed>  $station
     * @return array<string, mixed>
     */
    private function enrichStationNavigation(array $station): array
    {
        $isValid = Station::isValidCoordinatePair($station['latitude'] ?? null, $station['longitude'] ?? null);

        $station['is_valid_coordinate'] = $isValid;
        $station['direct_nav_url'] = $isValid
            ? sprintf('https://www.google.com/maps/dir/?api=1&destination=%s,%s', $station['latitude'], $station['longitude'])
            : null;
        $station['detail_url'] = route('stations.show', $station['id']);

        return $station;
    }
}
