<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\StationConnector;
use App\Models\StationReport;
use App\Models\StationReview;
use App\Models\User;
use App\Models\VehicleMaster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminDataController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    public static function formatStationRow(Station $st): array
    {
        $isImported = ($st->source ?? 'manual') !== 'manual';
        $connectorList = $st->connectors->pluck('connector_type')->unique()->implode(', ');
        $price = (int) ($st->price_per_kwh ?? 0);

        return [
            'id' => $st->id_stations,
            'name' => $st->name,
            'operator' => match ($st->operator_type) {
                'government' => 'PLN',
                'private' => 'Swasta',
                default => 'Komersial',
            },
            'operator_type' => $st->operator_type,
            'source' => (string) ($st->source ?? 'manual'),
            'address' => $st->address,
            'connectors' => $connectorList !== '' ? $connectorList : ($isImported ? 'Belum tercatat' : 'CCS2'),
            'tariff' => $price > 0
                ? 'Rp ' . number_format($price, 0, ',', '.')
                : ($isImported ? 'Belum diketahui' : 'GRATIS'),
            'price_per_kwh' => $price,
            'hours' => $st->operating_hours ?: 'Belum diketahui',
            'latitude' => (string) $st->latitude,
            'longitude' => (string) $st->longitude,
            'app_name' => $st->require_app_name ?: ($isImported ? 'Belum diketahui' : 'PLN Mobile'),
            'status' => match ($st->current_status) {
                'busy' => 'Antrean Padat',
                'technical_issue' => 'Kendala Teknis',
                default => 'Normal',
            },
            'current_status' => $st->current_status,
            'show_url' => route('stations.show', $st->id_stations),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function formatConnectorRow(StationConnector $c): array
    {
        return [
            'id' => $c->id_station_connector,
            'station_id' => $c->id_stations,
            'station_name' => $c->station?->name ?? '-',
            'connector_type' => $c->connector_type,
            'power_kw' => (float) $c->power_kw,
            'charging_speed_category' => $c->charging_speed_category,
            'current_type' => $c->current_type,
            'available_connectors' => (int) $c->available_connectors,
            'total_connectors' => (int) $c->total_connectors,
        ];
    }

    public function listStations(Request $request): JsonResponse
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 10)));
        $page = max(1, (int) $request->query('page', 1));
        $search = trim((string) $request->query('search', ''));
        $operatorType = trim((string) $request->query('operator_type', ''));
        $currentStatus = trim((string) $request->query('current_status', ''));

        $query = Station::with('connectors')->orderBy('id_stations');

        if ($operatorType !== '') {
            $query->where('operator_type', $operatorType);
        }

        if ($currentStatus !== '') {
            $query->where('current_status', $currentStatus);
        }

        if ($search !== '') {
            $like = '%' . mb_strtolower($search) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(address) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(operating_hours) LIKE ?', [$like])
                    ->orWhereHas('connectors', function ($connQuery) use ($like) {
                        $connQuery->whereRaw('LOWER(connector_type) LIKE ?', [$like]);
                    });
            });
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        $items = $paginator->getCollection()
            ->map(fn (Station $st): array => self::formatStationRow($st))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => max(1, $paginator->lastPage()),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function listStationOptions(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));
        $query = Station::query()->select(['id_stations', 'name']);

        if ($search !== '') {
            $like = '%' . mb_strtolower($search) . '%';
            $query->whereRaw('LOWER(name) LIKE ?', [$like])->orderBy('name');
        } else {
            $query->orderBy('id_stations');
        }

        $options = $query->take(60)->get()->map(fn (Station $s): array => [
            'id' => $s->id_stations,
            'name' => $s->name,
        ])->all();

        return response()->json([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function listConnectors(Request $request): JsonResponse
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 10)));
        $page = max(1, (int) $request->query('page', 1));
        $search = trim((string) $request->query('search', ''));
        $connectorType = trim((string) $request->query('connector_type', ''));

        $query = StationConnector::with('station')->orderBy('id_station_connector');

        if ($connectorType !== '') {
            $query->where('connector_type', $connectorType);
        }

        if ($search !== '') {
            $like = '%' . mb_strtolower($search) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->whereRaw('LOWER(connector_type) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(current_type) LIKE ?', [$like])
                    ->orWhereHas('station', function ($stQuery) use ($like) {
                        $stQuery->whereRaw('LOWER(name) LIKE ?', [$like]);
                    });
            });
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        $items = $paginator->getCollection()
            ->map(fn (StationConnector $c): array => self::formatConnectorRow($c))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => max(1, $paginator->lastPage()),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function storeStation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'operator_type' => ['required', Rule::in(['government', 'private', 'commercial'])],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'operating_hours' => ['required', 'string', 'max:100'],
            'price_per_kwh' => ['required', 'numeric', 'min:0'],
            'require_app_name' => ['nullable', 'string', 'max:100'],
            'current_status' => ['required', Rule::in(['normal', 'busy', 'technical_issue'])],
            'connector_type' => ['nullable', 'string', 'max:50'],
            'power_kw' => ['nullable', 'numeric', 'min:1'],
            'current_type' => ['nullable', Rule::in(['AC', 'DC'])],
            'available_connectors' => ['nullable', 'integer', 'min:0'],
            'total_connectors' => ['nullable', 'integer', 'min:1'],
        ]);

        $station = Station::create([
            'name' => $validated['name'],
            'operator_type' => $validated['operator_type'],
            'address' => $validated['address'],
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'operating_hours' => $validated['operating_hours'],
            'price_per_kwh' => (float) $validated['price_per_kwh'],
            'require_app_name' => $validated['require_app_name'] ?: 'PLN Mobile',
            'current_status' => $validated['current_status'],
        ]);

        $connectorType = $validated['connector_type'] ?? 'CCS2';
        $powerKw = (float) ($validated['power_kw'] ?? 50);
        $currentType = $validated['current_type'] ?? (str_contains(strtoupper($connectorType), 'AC') ? 'AC' : 'DC');
        $speedCategory = $powerKw >= 50 ? 'ultra' : ($powerKw >= 20 ? 'fast' : 'standard');
        $totalConnectors = (int) ($validated['total_connectors'] ?? 2);
        $availableConnectors = min((int) ($validated['available_connectors'] ?? $totalConnectors), $totalConnectors);

        $station->connectors()->create([
            'connector_type' => $connectorType,
            'power_kw' => $powerKw,
            'charging_speed_category' => $speedCategory,
            'current_type' => $currentType,
            'available_connectors' => $availableConnectors,
            'total_connectors' => $totalConnectors,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data stasiun berhasil ditambahkan ke basis data.',
            'data' => $station->load('connectors'),
        ], 201);
    }

    public function updateStation(Request $request, int $id): JsonResponse
    {
        $station = Station::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'operator_type' => ['required', Rule::in(['government', 'private', 'commercial'])],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'operating_hours' => ['required', 'string', 'max:100'],
            'price_per_kwh' => ['required', 'numeric', 'min:0'],
            'require_app_name' => ['nullable', 'string', 'max:100'],
            'current_status' => ['required', Rule::in(['normal', 'busy', 'technical_issue'])],
        ]);

        $station->update([
            'name' => $validated['name'],
            'operator_type' => $validated['operator_type'],
            'address' => $validated['address'],
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'operating_hours' => $validated['operating_hours'],
            'price_per_kwh' => (float) $validated['price_per_kwh'],
            'require_app_name' => $validated['require_app_name'] ?: 'PLN Mobile',
            'current_status' => $validated['current_status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data stasiun berhasil diperbarui.',
            'data' => $station->fresh('connectors'),
        ]);
    }

    public function destroyStation(int $id): JsonResponse
    {
        $station = Station::findOrFail($id);
        $station->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data stasiun berhasil dihapus dari basis data.',
        ]);
    }

    public function storeVehicle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(['Mobil Listrik', 'Motor Listrik'])],
            'battery_capacity_kwh' => ['required', 'numeric', 'min:0.1'],
            'default_connector_type' => ['required', 'string', 'max:50'],
        ]);

        $vehicle = VehicleMaster::create([
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'category' => $validated['category'],
            'battery_capacity_kwh' => (float) $validated['battery_capacity_kwh'],
            'default_connector_type' => $validated['default_connector_type'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kendaraan berhasil ditambahkan.',
            'data' => $vehicle,
        ], 201);
    }

    public function updateVehicle(Request $request, int $id): JsonResponse
    {
        $vehicle = VehicleMaster::findOrFail($id);

        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(['Mobil Listrik', 'Motor Listrik'])],
            'battery_capacity_kwh' => ['required', 'numeric', 'min:0.1'],
            'default_connector_type' => ['required', 'string', 'max:50'],
        ]);

        $vehicle->update([
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'category' => $validated['category'],
            'battery_capacity_kwh' => (float) $validated['battery_capacity_kwh'],
            'default_connector_type' => $validated['default_connector_type'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kendaraan berhasil diperbarui.',
            'data' => $vehicle->fresh(),
        ]);
    }

    public function destroyVehicle(int $id): JsonResponse
    {
        $vehicle = VehicleMaster::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kendaraan berhasil dihapus.',
        ]);
    }

    public function storeConnector(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'station_id' => ['required', 'exists:stations,id_stations'],
            'connector_type' => ['required', 'string', 'max:50'],
            'power_kw' => ['required', 'numeric', 'min:1'],
            'charging_speed_category' => ['required', Rule::in(['standard', 'fast', 'ultra'])],
            'current_type' => ['required', Rule::in(['AC', 'DC'])],
            'available_connectors' => ['required', 'integer', 'min:0'],
            'total_connectors' => ['required', 'integer', 'min:1'],
        ]);

        $total = (int) $validated['total_connectors'];
        $available = min((int) $validated['available_connectors'], $total);

        $connector = StationConnector::create([
            'id_stations' => (int) $validated['station_id'],
            'connector_type' => $validated['connector_type'],
            'power_kw' => (float) $validated['power_kw'],
            'charging_speed_category' => $validated['charging_speed_category'],
            'current_type' => $validated['current_type'],
            'available_connectors' => $available,
            'total_connectors' => $total,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data konektor stasiun berhasil ditambahkan.',
            'data' => $connector->load('station'),
        ], 201);
    }

    public function updateConnector(Request $request, int $id): JsonResponse
    {
        $connector = StationConnector::findOrFail($id);

        $validated = $request->validate([
            'station_id' => ['required', 'exists:stations,id_stations'],
            'connector_type' => ['required', 'string', 'max:50'],
            'power_kw' => ['required', 'numeric', 'min:1'],
            'charging_speed_category' => ['required', Rule::in(['standard', 'fast', 'ultra'])],
            'current_type' => ['required', Rule::in(['AC', 'DC'])],
            'available_connectors' => ['required', 'integer', 'min:0'],
            'total_connectors' => ['required', 'integer', 'min:1'],
        ]);

        $total = (int) $validated['total_connectors'];
        $available = min((int) $validated['available_connectors'], $total);

        $connector->update([
            'id_stations' => (int) $validated['station_id'],
            'connector_type' => $validated['connector_type'],
            'power_kw' => (float) $validated['power_kw'],
            'charging_speed_category' => $validated['charging_speed_category'],
            'current_type' => $validated['current_type'],
            'available_connectors' => $available,
            'total_connectors' => $total,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data konektor berhasil diperbarui.',
            'data' => $connector->fresh('station'),
        ]);
    }

    public function destroyConnector(int $id): JsonResponse
    {
        $connector = StationConnector::findOrFail($id);
        $connector->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data konektor berhasil dihapus.',
        ]);
    }

    public function updateUserRole(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        if ((int) $user->id_user === (int) Auth::id() && $validated['role'] !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menurunkan hak akses akun Anda sendiri.',
            ], 422);
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Peran pengguna berhasil diperbarui.',
            'data' => [
                'id' => $user->id_user,
                'name' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function destroyUser(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ((int) $user->id_user === (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun yang sedang aktif digunakan.',
            ], 422);
        }

        $user->tokens()->delete();
        if (Schema::hasTable('user_vehicle')) {
            DB::table('user_vehicle')->where('id_user', $user->id_user)->delete();
        }
        if (Schema::hasTable('station_reviews')) {
            DB::table('station_reviews')->where('id_user', $user->id_user)->delete();
        }
        if (Schema::hasTable('user_favorites')) {
            DB::table('user_favorites')->where('id_user', $user->id_user)->delete();
        }
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id_user)->delete();
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun pengguna berhasil dihapus.',
        ]);
    }

    public function updateReportStatus(Request $request, int $id): JsonResponse
    {
        $report = StationReport::findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['normal', 'antre', 'rusak'])],
        ]);

        $report->update([
            'status' => $validated['status'],
        ]);

        $stationStatus = match ($validated['status']) {
            'rusak' => 'technical_issue',
            'antre' => 'busy',
            default => 'normal',
        };

        Station::where('id_stations', $report->id_stations)->update([
            'current_status' => $stationStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan dan stasiun terkait berhasil diperbarui.',
            'data' => $report->fresh('station'),
        ]);
    }

    public function destroyReport(int $id): JsonResponse
    {
        $report = StationReport::findOrFail($id);
        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan kondisi berhasil dihapus.',
        ]);
    }

    public function destroyReview(int $id): JsonResponse
    {
        $review = StationReview::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan pengguna berhasil dihapus.',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $updateData = [
            'nama' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profil admin berhasil diperbarui.',
            'data' => [
                'name' => $user->nama,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }
}
