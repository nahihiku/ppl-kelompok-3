<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\VehicleMaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StationController extends Controller
{
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

    public function show(string $id): View
    {
        $stations = $this->getStationsCatalog();
        $station = $stations->firstWhere('id', (string) $id) ?? $stations->first();

        return view('stations.show', [
            'station' => $station,
            'allStations' => $stations,
        ]);
    }

    public function report(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:normal,technical_issue,busy'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (Schema::hasTable('stations')) {
            Station::where('id_stations', $id)->update([
                'current_status' => $validated['status'],
            ]);
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

        return $stations->filter(function (array $station) use ($search, $connector, $operator, $status) {
            if ($search !== '') {
                $haystack = strtolower($station['name'].' '.$station['address'].' '.$station['operator_badge']);
                if (! str_contains($haystack, strtolower($search))) {
                    return false;
                }
            }

            if ($connector !== '' && strcasecmp($station['connector_type'], $connector) !== 0) {
                $boxMatch = collect($station['charging_boxes'] ?? [])->contains(function (array $box) use ($connector) {
                    return str_contains(strtolower($box['connector_spec']), strtolower($connector));
                });
                if (! $boxMatch) {
                    return false;
                }
            }

            if ($operator !== '' && strcasecmp($station['operator_badge'], $operator) !== 0) {
                return false;
            }

            if ($status !== '' && strcasecmp($station['current_status'], $status) !== 0) {
                return false;
            }

            return true;
        })->values();
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
        $defaults = collect([
            [
                'id' => '1',
                'name' => 'PLN SPKLU Substation Central',
                'operator_badge' => 'PLN',
                'operator_type' => 'government',
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
        ]);

        try {
            if (Schema::hasTable('stations') && Station::count() > 0) {
                return Station::all()->map(function (Station $station, int $idx) {
                    $statusLabel = match ($station->current_status) {
                        'normal' => 'Tersedia',
                        'busy' => 'Antrean Padat',
                        'technical_issue' => 'Kendala Teknis',
                        default => 'Tersedia',
                    };
                    $price = (int) ($station->price_per_kwh ?: 2000);

                    return [
                        'id' => (string) $station->id_stations,
                        'name' => $station->name,
                        'operator_badge' => $station->operator_type === 'government' ? 'PLN' : strtoupper($station->operator_type),
                        'operator_type' => $station->operator_type,
                        'address' => $station->address,
                        'latitude' => (float) $station->latitude,
                        'longitude' => (float) $station->longitude,
                        'distance_km' => round(1.2 + ($idx * 0.8), 1),
                        'operating_hours' => $station->operating_hours,
                        'price_per_kwh' => $price,
                        'require_app_name' => $station->require_app_name ?? 'PLN Mobile',
                        'current_status' => $station->current_status,
                        'status_label' => $statusLabel,
                        'connector_type' => 'CCS2',
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
                                'price_label' => "Rp {$price}/kWh",
                                'estimation_label' => 'Est. 02 Jam 21 Menit',
                            ],
                            [
                                'name' => 'Delta DC Ultra Fast',
                                'connector_count' => '2 Konektor',
                                'status_badge' => 'Tersedia',
                                'status_tone' => 'available',
                                'connector_spec' => 'CCS2 - 50kW DC',
                                'price_label' => "Rp {$price}/kWh",
                                'estimation_label' => 'Est. 45 Menit',
                            ],
                        ],
                        'reviews' => [
                            [
                                'user' => 'Rizky Pratama',
                                'rating' => 5,
                                'time' => '2 jam yang lalu',
                                'comment' => 'Area parkir luas, konektor berfungsi lancar.',
                            ],
                        ],
                        'facilities' => [
                            ['name' => 'Atap Peneduh Kanopi', 'detail' => 'Melindungi kendaraan saat pengisian daya'],
                            ['name' => 'Toilet & Mushola', 'detail' => 'Bersih dan buka 24 jam'],
                        ],
                    ];
                });
            }
        } catch (\Throwable) {
            // Gunakan data pratinjau bawaan jika koneksi/tabel belum dimigrasi
        }

        return $defaults;
    }
}
