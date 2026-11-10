<?php

namespace App\Filament\Pages;

use App\Http\Controllers\Admin\AdminDataController;
use App\Models\Station;
use App\Models\StationConnector;
use App\Models\StationReport;
use App\Models\StationReview;
use App\Models\User;
use App\Models\VehicleMaster;
use Carbon\Carbon;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected Width | string | null $maxContentWidth = Width::Full;

    protected string $view = 'filament.pages.dashboard';

    public function getHeading(): string | Htmlable | null
    {
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $hasUsersTable = Schema::hasTable('users');
        $hasStationsTable = Schema::hasTable('stations');
        $hasConnectorsTable = Schema::hasTable('station_connectors');
        $hasVehiclesTable = Schema::hasTable('vehicle_masters');
        $hasReportsTable = Schema::hasTable('station_reports');
        $hasReviewsTable = Schema::hasTable('station_reviews');

        $userCount = $hasUsersTable ? User::query()->count() : 0;
        $stationCount = $hasStationsTable ? Station::query()->count() : 0;
        $connectorCount = $hasConnectorsTable ? StationConnector::query()->count() : 0;
        $vehicleCount = $hasVehiclesTable ? VehicleMaster::query()->count() : 0;
        $reportCount = $hasReportsTable ? StationReport::query()->count() : 0;
        $reviewCount = $hasReviewsTable ? StationReview::query()->count() : 0;
        $stationsUseRemotePagination = $stationCount > 100;
        $connectorsUseRemotePagination = $connectorCount > 100;

        $activeStationCount = $hasStationsTable
            ? Station::query()->where('current_status', '!=', 'technical_issue')->count()
            : 0;

        $hasRealStations = $stationCount > 0;

        $totalUsers = $userCount > 0 ? $userCount : 7;
        $totalStations = $hasRealStations ? $stationCount : 8;
        $totalVehicles = $vehicleCount > 0 ? $vehicleCount : 6;
        $totalReports = $hasRealStations ? $reportCount : 5;
        $totalReviews = $hasRealStations ? $reviewCount : 5;
        $activeStationsRatio = $hasRealStations
            ? "{$activeStationCount}/{$stationCount}"
            : '5/6';

        // Recent Stations (DB or empty-DB preview fallback)
        if ($hasRealStations) {
            $recentStations = Station::query()
                ->latest('id_stations')
                ->take(5)
                ->get()
                ->map(fn (Station $s) => [
                    'name' => $s->name,
                    'address' => $s->address,
                    'is_active' => $s->current_status !== 'technical_issue',
                ])
                ->all();
        } else {
            $recentStations = [
                [
                    'name' => 'CasIn Hub Sudirman',
                    'address' => 'Jl. Jend. Sudirman No. 52, Jakarta Pusat',
                    'is_active' => true,
                ],
                [
                    'name' => 'PLN SPKLU Thamrin',
                    'address' => 'Jl. M.H. Thamrin No. 9, Jakarta Pusat',
                    'is_active' => true,
                ],
                [
                    'name' => 'Charge+ Grand Indonesia',
                    'address' => 'Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                    'is_active' => true,
                ],
                [
                    'name' => 'Shell Recharge Kemang',
                    'address' => 'Jl. Kemang Raya No. 42, Jakarta Selatan',
                    'is_active' => false,
                ],
            ];
        }

        // Report Summary (DB or empty-DB preview fallback)
        if ($hasRealStations && $hasReportsTable) {
            $rusakCount = StationReport::query()->where('status', 'rusak')->count();
            $normalCount = StationReport::query()->where('status', 'normal')->count();
            $antreCount = StationReport::query()->where('status', 'antre')->count();
            $maxReport = max(1, $reportCount);

            $reportSummary = [
                [
                    'label' => 'Kendala / Rusak',
                    'count' => $rusakCount,
                    'percentage' => (int) round(($rusakCount / $maxReport) * 100),
                    'color' => '#ffb900',
                ],
                [
                    'label' => 'Terverifikasi / Normal',
                    'count' => $normalCount,
                    'percentage' => (int) round(($normalCount / $maxReport) * 100),
                    'color' => '#00d3f2',
                ],
                [
                    'label' => 'Antrean Padat',
                    'count' => $antreCount,
                    'percentage' => (int) round(($antreCount / $maxReport) * 100),
                    'color' => '#2b7fff',
                ],
            ];
        } else {
            $reportSummary = [
                [
                    'label' => 'Menunggu',
                    'count' => 2,
                    'percentage' => 55,
                    'color' => '#ffb900',
                ],
                [
                    'label' => 'Terverifikasi',
                    'count' => 2,
                    'percentage' => 76,
                    'color' => '#00d3f2',
                ],
                [
                    'label' => 'Ditindaklanjuti',
                    'count' => 1,
                    'percentage' => 42,
                    'color' => '#2b7fff',
                ],
            ];
        }

        // Recent Reviews (DB or empty-DB preview fallback)
        if ($hasRealStations && $hasReviewsTable) {
            $recentReviews = StationReview::with(['user', 'station'])
                ->latest('id_station_review')
                ->take(6)
                ->get()
                ->map(function (StationReview $rev) {
                    $userName = $rev->user?->name ?? 'Pengguna';
                    $stationName = $rev->station?->name ?? 'Stasiun SPKLU';
                    $parts = preg_split('/\s+/', trim($userName)) ?: ['U'];
                    $initials = strtoupper(
                        substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
                    );

                    return [
                        'initials' => $initials ?: 'U',
                        'title' => "{$userName} mengulas {$stationName}",
                        'date' => $rev->created_at ? $rev->created_at->translatedFormat('d M Y') : '-',
                        'rating' => (int) $rev->rating,
                    ];
                })
                ->all();
        } else {
            $recentReviews = [
                [
                    'initials' => 'NP',
                    'title' => 'Nadia Putri mengulas CasIn Hub Sudirman',
                    'date' => '20 Mei 2025',
                    'rating' => 5,
                ],
                [
                    'initials' => 'AR',
                    'title' => 'Arif Ramadhan mengulas PLN SPKLU Thamrin',
                    'date' => '19 Mei 2025',
                    'rating' => 4,
                ],
                [
                    'initials' => 'DP',
                    'title' => 'Dimas Prakoso mengulas Shell Recharge Kemang',
                    'date' => '19 Mei 2025',
                    'rating' => 2,
                ],
                [
                    'initials' => 'SM',
                    'title' => 'Salsa Maharani mengulas Charge+ Grand Indonesia',
                    'date' => '18 Mei 2025',
                    'rating' => 5,
                ],
            ];
        }

        // Station Rows (DB or empty-DB preview fallback)
        if ($hasRealStations) {
            $stationQuery = Station::with('connectors')->orderBy('id_stations');
            if ($stationsUseRemotePagination) {
                $stationQuery->take(10);
            }
            $stationRows = $stationQuery
                ->get()
                ->map(fn (Station $st): array => AdminDataController::formatStationRow($st))
                ->all();
        } else {
            $stationRows = [
                [
                    'id' => 1,
                    'name' => 'SPKLU PLN Embong Wungu',
                    'operator' => 'PLN',
                    'operator_type' => 'government',
                    'address' => 'Jl. Kenangan',
                    'connectors' => 'CCS2, CHAdeMO',
                    'tariff' => 'Rp 2.466',
                    'price_per_kwh' => 2466,
                    'hours' => '24 Jam',
                    'latitude' => '-7.265412',
                    'longitude' => '112.748190',
                    'app_name' => 'PLN Mobile',
                    'status' => 'Normal',
                    'current_status' => 'normal',
                ],
                [
                    'id' => 2,
                    'name' => 'PLN SPKLU Substation Central',
                    'operator' => 'PLN',
                    'operator_type' => 'government',
                    'address' => 'Jl. Letjen S. Parman No. 28',
                    'connectors' => 'CCS2',
                    'tariff' => 'Rp 2.000',
                    'price_per_kwh' => 2000,
                    'hours' => '24 Jam',
                    'latitude' => '-6.175392',
                    'longitude' => '106.790512',
                    'app_name' => 'PLN Mobile',
                    'status' => 'Normal',
                    'current_status' => 'normal',
                ],
            ];
        }

        // Vehicle Rows (DB or empty-DB preview fallback)
        if ($vehicleCount > 0) {
            $vehicleRows = VehicleMaster::query()
                ->orderBy('id_vehicle_masters')
                ->get()
                ->map(fn (VehicleMaster $v) => [
                    'id' => $v->id_vehicle_masters,
                    'brand' => $v->brand,
                    'model' => $v->model,
                    'category' => $v->category,
                    'battery' => str_replace('.', ',', rtrim(rtrim(number_format((float) $v->battery_capacity_kwh, 2, '.', ''), '0'), '.')) . ' kWh',
                    'battery_kwh' => (float) $v->battery_capacity_kwh,
                    'connector' => $v->default_connector_type,
                ])
                ->all();
        } else {
            $vehicleRows = [
                [
                    'id' => 1,
                    'brand' => 'Hyundai',
                    'model' => 'IONIQ 5',
                    'category' => 'Mobil Listrik',
                    'battery' => '72,6 kWh',
                    'battery_kwh' => 72.6,
                    'connector' => 'CCS2',
                ],
                [
                    'id' => 2,
                    'brand' => 'Wuling',
                    'model' => 'Air ev Long Range',
                    'category' => 'Mobil Listrik',
                    'battery' => '26,7 kWh',
                    'battery_kwh' => 26.7,
                    'connector' => 'AC Type 2',
                ],
            ];
        }

        // User Rows (always from real DB)
        $userRows = $hasUsersTable
            ? User::query()
                ->orderBy('id')
                ->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone ?? '-',
                    'role' => $u->role ?? 'user',
                    'joined_at' => $u->created_at ? $u->created_at->format('d M Y') : '-',
                ])
                ->all()
            : [];

        // Connector Rows (always from real DB)
        if ($hasConnectorsTable) {
            $connectorQuery = StationConnector::with('station')->orderBy('id_station_connectors');
            if ($connectorsUseRemotePagination) {
                $connectorQuery->take(10);
            }
            $connectorRows = $connectorQuery
                ->get()
                ->map(fn (StationConnector $c): array => AdminDataController::formatConnectorRow($c))
                ->all();
        } else {
            $connectorRows = [];
        }

        // Report Rows (always from real DB)
        $reportRows = $hasReportsTable
            ? StationReport::with('station')
                ->latest('id_station_report')
                ->get()
                ->map(fn (StationReport $r) => [
                    'id' => $r->id_station_report,
                    'station_id' => $r->id_stations,
                    'station_name' => $r->station?->name ?? '-',
                    'connector_type' => $r->connector_type,
                    'power_kw' => (float) $r->power_kw,
                    'charging_speed_category' => $r->charging_speed_category,
                    'status' => $r->status,
                    'notes' => $r->notes ?: '-',
                    'reported_at' => ($r->reported_at ?? $r->created_at)?->format('d M Y H:i') ?? '-',
                ])
                ->all()
            : [];

        // Review Rows (always from real DB)
        $reviewRows = $hasReviewsTable
            ? StationReview::with(['user', 'station'])
                ->latest('id_station_review')
                ->get()
                ->map(fn (StationReview $rev) => [
                    'id' => $rev->id_station_review,
                    'user_name' => $rev->user?->name ?? 'Pengguna',
                    'station_name' => $rev->station?->name ?? '-',
                    'rating' => (int) $rev->rating,
                    'comment' => $rev->comment ?: '-',
                    'created_at' => $rev->created_at ? $rev->created_at->format('d M Y') : '-',
                ])
                ->all()
            : [];

        // Station list for Connector select dropdown (batasi awal agar DOM tetap ringan pada skala ribuan titik)
        $stationOptions = $hasStationsTable
            ? Station::query()
                ->when($stationsUseRemotePagination, fn ($q) => $q->orderBy('id_stations')->take(50), fn ($q) => $q->orderBy('name'))
                ->get(['id_stations', 'name'])
                ->map(fn (Station $s) => [
                    'id' => $s->id_stations,
                    'name' => $s->name,
                ])->all()
            : [];

        // Last 7 months labels + real monthly user counts
        $monthNames = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        $growthMonths = [];
        $monthlyUserCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $growthMonths[] = $monthNames[$monthDate->month] ?? 'Bulan';
            if ($hasUsersTable && $hasRealStations) {
                $monthlyUserCounts[] = User::query()
                    ->whereYear('created_at', $monthDate->year)
                    ->whereMonth('created_at', $monthDate->month)
                    ->count();
            }
        }

        if ($hasRealStations && count($monthlyUserCounts) === 7) {
            $usersThisMonth = end($monthlyUserCounts) ?: 0;
            $growthBadge = "+{$usersThisMonth} bulan ini";
            $maxMonthlyUsers = max(1, max($monthlyUserCounts));
            $xCoords = [16, 124, 232, 340, 448, 556, 666];
            $points = [];
            foreach ($monthlyUserCounts as $idx => $count) {
                $x = $xCoords[$idx];
                $y = (int) round(148 - (($count / $maxMonthlyUsers) * 124));
                $points[] = [$x, $y];
            }
            $lineSegments = ["M {$points[0][0]} {$points[0][1]}"];
            for ($i = 0; $i < count($points) - 1; $i++) {
                [$x1, $y1] = $points[$i];
                [$x2, $y2] = $points[$i + 1];
                $cx = (int) round(($x1 + $x2) / 2);
                $lineSegments[] = "C {$cx} {$y1}, {$cx} {$y2}, {$x2} {$y2}";
            }
            $growthLinePath = implode(' ', $lineSegments);
            $growthAreaPath = "{$growthLinePath} L 666 168 L 16 168 Z";
        } else {
            $growthBadge = '+18,2%';
            $growthLinePath = 'M 16 142 C 65 138, 95 118, 126 118 C 165 118, 195 124, 236 105 C 275 88, 305 84, 346 84 C 385 84, 415 65, 456 64 C 495 63, 525 46, 566 48 C 605 50, 635 20, 666 22';
            $growthAreaPath = "{$growthLinePath} L 666 168 L 16 168 Z";
        }

        $currentUser = Auth::user();

        return [
            'stats' => [
                [
                    'label' => 'Total Pengguna',
                    'value' => (string) $totalUsers,
                    'trend' => 'DB Live',
                    'gradient' => 'linear-gradient(135deg, #155dfc 0%, #2b7fff 100%)',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Total Stasiun',
                    'value' => (string) $totalStations,
                    'trend' => 'DB Live',
                    'gradient' => 'linear-gradient(135deg, #00b8db 0%, #00d3f2 100%)',
                    'icon' => 'stations',
                ],
                [
                    'label' => 'Total Kendaraan',
                    'value' => (string) $totalVehicles,
                    'trend' => 'DB Live',
                    'gradient' => 'linear-gradient(135deg, #4f39f6 0%, #615fff 100%)',
                    'icon' => 'vehicles',
                ],
                [
                    'label' => 'Total Laporan',
                    'value' => (string) $totalReports,
                    'trend' => 'DB Live',
                    'gradient' => 'linear-gradient(135deg, #fe9a00 0%, #ff8904 100%)',
                    'icon' => 'reports',
                ],
                [
                    'label' => 'Total Ulasan',
                    'value' => (string) $totalReviews,
                    'trend' => 'DB Live',
                    'gradient' => 'linear-gradient(135deg, #7f22fe 0%, #e12afb 100%)',
                    'icon' => 'reviews',
                ],
                [
                    'label' => 'Stasiun Aktif',
                    'value' => $activeStationsRatio,
                    'trend' => 'DB Live',
                    'gradient' => 'linear-gradient(135deg, #009966 0%, #00d492 100%)',
                    'icon' => 'active',
                ],
            ],
            'growthMonths' => $growthMonths,
            'growthBadge' => $growthBadge,
            'growthLinePath' => $growthLinePath,
            'growthAreaPath' => $growthAreaPath,
            'reportSummary' => $reportSummary,
            'recentStations' => $recentStations,
            'recentReviews' => $recentReviews,
            'stationRows' => $stationRows,
            'stationsTotalCount' => $hasRealStations ? $stationCount : count($stationRows),
            'stationsUseRemotePagination' => $stationsUseRemotePagination,
            'vehicleRows' => $vehicleRows,
            'userRows' => $userRows,
            'connectorRows' => $connectorRows,
            'connectorsTotalCount' => $hasConnectorsTable ? $connectorCount : 0,
            'connectorsUseRemotePagination' => $connectorsUseRemotePagination,
            'reportRows' => $reportRows,
            'reviewRows' => $reviewRows,
            'stationOptions' => $stationOptions,
            'adminId' => $currentUser?->id,
            'adminName' => $currentUser?->name ?? 'Admin CasIn.id',
            'adminEmail' => $currentUser?->email ?? 'admin@casin.id',
            'adminPhone' => $currentUser?->phone ?? '',
        ];
    }
}
