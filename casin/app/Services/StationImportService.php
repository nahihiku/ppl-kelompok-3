<?php

namespace App\Services;

use App\Models\Station;
use Illuminate\Support\Facades\DB;

/**
 * Logika bersama untuk mengimpor data stasiun dari sumber eksternal
 * (OpenStreetMap, dataset petaspklu.id / PLN, file CSV, dan sebagainya).
 */
class StationImportService
{
    /** Jarak (meter) di mana dua titik dianggap stasiun yang sama. */
    public const DUPLICATE_RADIUS_METERS = 100;

    /** @var array<int, array{id:int, lat:float, lng:float, source:string, external_id:?string}>|null */
    private ?array $existing = null;

    /** @var array<string, array{id:int, lat:float, lng:float, source:string, external_id:?string}> */
    private array $byExternalId = [];

    /** @var array<string, array<int, array{id:int, lat:float, lng:float, source:string, external_id:?string}>> */
    private array $grid = [];

    /**
     * Simpan atau perbarui satu stasiun beserta konektornya.
     *
     * @param  array<string, mixed>  $station
     * @param  array<int, array<string, mixed>>  $connectors
     * @return string 'created' | 'updated' | 'duplicate'
     */
    public function upsert(array $station, array $connectors, bool $dryRun = false): string
    {
        $stats = $this->importBatch([['station' => $station, 'connectors' => $connectors]], $dryRun);

        if ($stats['created'] > 0) {
            return 'created';
        }
        if ($stats['updated'] > 0) {
            return 'updated';
        }

        return 'duplicate';
    }

    /**
     * Impor banyak stasiun sekaligus secara atomik dalam satu transaksi database.
     *
     * @param  array<int, array{station: array<string, mixed>, connectors: array<int, array<string, mixed>>}>  $items
     * @return array{created: int, updated: int, duplicate: int}
     */
    public function importBatch(array $items, bool $dryRun = false, bool $enrichNearbyGenericConnectors = false): array
    {
        $this->loadExisting();

        $stats = ['created' => 0, 'updated' => 0, 'duplicate' => 0];
        $toInsertStations = [];
        $toUpdateItems = [];
        $toEnrichNearby = [];

        foreach ($items as $item) {
            $station = $item['station'];
            $connectors = $item['connectors'];
            $source = (string) $station['source'];
            $externalId = isset($station['external_id']) && $station['external_id'] !== ''
                ? (string) $station['external_id']
                : null;
            $lat = (float) $station['latitude'];
            $lng = (float) $station['longitude'];

            $sameRecord = $externalId !== null
                ? ($this->byExternalId[$source.'|'.$externalId] ?? null)
                : null;

            if (! $sameRecord && ($nearby = $this->findNearby($lat, $lng)) !== null) {
                $stats['duplicate']++;
                if (
                    ! $dryRun
                    && $enrichNearbyGenericConnectors
                    && $nearby['id'] > 0
                    && $nearby['source'] !== 'manual'
                    && $connectors !== []
                ) {
                    $toEnrichNearby[] = [
                        'id' => $nearby['id'],
                        'distance_m' => Station::haversineDistanceKm($lat, $lng, $nearby['lat'], $nearby['lng']) * 1000,
                        'incoming_name' => (string) ($station['name'] ?? ''),
                        'connectors' => $connectors,
                    ];
                }

                continue;
            }

            if ($sameRecord) {
                $stats['updated']++;
                if (! $dryRun) {
                    $toUpdateItems[] = [
                        'id' => $sameRecord['id'],
                        'station' => $station,
                        'connectors' => $connectors,
                    ];
                }

                continue;
            }

            $stats['created']++;
            $tempKey = $externalId ?? ('__batch_'.count($toInsertStations));

            // Daftarkan koordinat ke indeks sementara agar duplikasi di batch yang sama langsung terdeteksi.
            $entry = [
                'id' => -1 - count($toInsertStations),
                'lat' => $lat,
                'lng' => $lng,
                'source' => $source,
                'external_id' => $externalId,
            ];
            $this->indexEntry($entry);

            if (! $dryRun) {
                $toInsertStations[] = [
                    'station' => $station,
                    'connectors' => $connectors,
                    'source' => $source,
                    'external_id' => $externalId ?? $tempKey,
                    'generated_external' => $externalId === null,
                ];
            }
        }

        if ($dryRun || ($toInsertStations === [] && $toUpdateItems === [] && $toEnrichNearby === [])) {
            return $stats;
        }

        DB::transaction(function () use ($toInsertStations, $toUpdateItems, $toEnrichNearby) {
            $now = now()->toDateTimeString();

            foreach ($toUpdateItems as $item) {
                $model = Station::find($item['id']);
                if (! $model) {
                    continue;
                }
                $model->update($item['station']);
                $model->connectors()->delete();
                if ($item['connectors'] !== []) {
                    $model->connectors()->createMany($item['connectors']);
                }
                $this->indexEntry($this->toEntry($model));
            }

            foreach ($toEnrichNearby as $enrich) {
                $model = Station::with('connectors')->find($enrich['id']);
                if (! $model || $model->connectors->isEmpty()) {
                    continue;
                }
                if (! $this->shouldEnrichNearbyStation((string) $model->name, $enrich['incoming_name'], (float) $enrich['distance_m'])) {
                    continue;
                }

                $explicitByCurrent = [];
                foreach ($enrich['connectors'] as $c) {
                    $cType = (string) ($c['connector_type'] ?? '');
                    $cur = strtoupper((string) ($c['current_type'] ?? ''));
                    if ($cType === '' || in_array($cType, ['DC Connector', 'AC Connector'], true) || ! in_array($cur, ['AC', 'DC'], true)) {
                        continue;
                    }
                    $explicitByCurrent[$cur][] = $cType;
                }

                foreach ($model->connectors as $conn) {
                    if (! in_array($conn->connector_type, ['DC Connector', 'AC Connector'], true)) {
                        continue;
                    }
                    $cur = strtoupper((string) $conn->current_type);
                    $candidates = array_values(array_unique($explicitByCurrent[$cur] ?? []));
                    if ($candidates === []) {
                        continue;
                    }
                    // Hindari duplikasi baris jika stasiun sudah punya tipe yang sama pada daya yang sama.
                    $existingExact = $model->connectors->first(
                        fn ($other) => $other->id_station_connectors !== $conn->id_station_connectors
                            && $other->connector_type === $candidates[0]
                            && abs((float) $other->power_kw - (float) $conn->power_kw) < 0.01
                            && strtoupper((string) $other->current_type) === $cur
                    );
                    if ($existingExact) {
                        $existingExact->update([
                            'available_connectors' => (int) $existingExact->available_connectors + (int) $conn->available_connectors,
                            'total_connectors' => (int) $existingExact->total_connectors + (int) $conn->total_connectors,
                        ]);
                        $conn->delete();
                    } else {
                        $conn->update(['connector_type' => $candidates[0]]);
                    }
                }
            }

            if ($toInsertStations !== []) {
                $stationRows = [];
                foreach ($toInsertStations as $item) {
                    $s = $item['station'];
                    $stationRows[] = [
                        'name' => $s['name'],
                        'operator_type' => $s['operator_type'],
                        'address' => $s['address'],
                        'latitude' => (float) $s['latitude'],
                        'longitude' => (float) $s['longitude'],
                        'operating_hours' => $s['operating_hours'],
                        'price_per_kwh' => $s['price_per_kwh'] ?? null,
                        'require_app_name' => $s['require_app_name'] ?? null,
                        'current_status' => $s['current_status'] ?? 'normal',
                        'source' => $s['source'],
                        'external_id' => $item['external_id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Chunk 50 baris per insert agar selalu aman terhadap batas parameter SQLite.
                foreach (array_chunk($stationRows, 50) as $chunk) {
                    DB::table('stations')->insert($chunk);
                }

                $sources = array_values(array_unique(array_column($toInsertStations, 'source')));
                $insertedIdMap = [];
                DB::table('stations')
                    ->whereIn('source', $sources)
                    ->whereNotNull('external_id')
                    ->orderBy('id_stations')
                    ->get(['id_stations', 'source', 'external_id', 'latitude', 'longitude'])
                    ->each(function ($row) use (&$insertedIdMap) {
                        $insertedIdMap[$row->source.'|'.$row->external_id] = $row;
                    });

                $connectorRows = [];
                $generatedIdsToNull = [];

                foreach ($toInsertStations as $item) {
                    $lookupKey = $item['source'].'|'.$item['external_id'];
                    $inserted = $insertedIdMap[$lookupKey] ?? null;
                    if (! $inserted) {
                        continue;
                    }

                    $stationId = (int) $inserted->id_stations;
                    if ($item['generated_external']) {
                        $generatedIdsToNull[] = $stationId;
                    }

                    $this->indexEntry([
                        'id' => $stationId,
                        'lat' => (float) $inserted->latitude,
                        'lng' => (float) $inserted->longitude,
                        'source' => (string) $inserted->source,
                        'external_id' => $item['generated_external'] ? null : (string) $inserted->external_id,
                    ]);

                    foreach ($item['connectors'] as $c) {
                        $connectorRows[] = [
                            'station_id' => $stationId,
                            'connector_type' => $c['connector_type'],
                            'power_kw' => (float) $c['power_kw'],
                            'charging_speed_category' => $c['charging_speed_category'],
                            'current_type' => $c['current_type'],
                            'available_connectors' => (int) $c['available_connectors'],
                            'total_connectors' => (int) $c['total_connectors'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($generatedIdsToNull !== []) {
                    foreach (array_chunk($generatedIdsToNull, 200) as $idChunk) {
                        DB::table('stations')->whereIn('id_stations', $idChunk)->update(['external_id' => null]);
                    }
                }

                if ($connectorRows !== []) {
                    foreach (array_chunk($connectorRows, 100) as $chunk) {
                        DB::table('station_connectors')->insert($chunk);
                    }
                }
            }
        });

        return $stats;
    }

    public static function speedCategory(float $powerKw): string
    {
        return $powerKw >= 50 ? 'ultra' : ($powerKw >= 20 ? 'fast' : 'standard');
    }

    public static function normalizeConnectorType(string $raw): ?string
    {
        $value = strtolower(trim($raw));

        return match (true) {
            $value === '' => null,
            str_contains($value, 'chademo') => 'CHAdeMO',
            str_contains($value, 'ccs') || str_contains($value, 'combo') => 'CCS2',
            str_contains($value, 'gb/t') || str_contains($value, 'gbt') => 'GB/T',
            str_contains($value, 'type 2') || str_contains($value, 'type2') || $value === 'ac' => 'AC Type 2',
            default => trim($raw),
        };
    }

    private function findNearby(float $lat, float $lng): ?array
    {
        $baseLatBucket = (int) floor($lat * 100);
        $baseLngBucket = (int) floor($lng * 100);

        for ($dLat = -1; $dLat <= 1; $dLat++) {
            for ($dLng = -1; $dLng <= 1; $dLng++) {
                $bucketKey = ($baseLatBucket + $dLat).':'.($baseLngBucket + $dLng);
                if (! isset($this->grid[$bucketKey])) {
                    continue;
                }

                foreach ($this->grid[$bucketKey] as $candidate) {
                    if (Station::haversineDistanceKm($lat, $lng, $candidate['lat'], $candidate['lng']) * 1000 <= self::DUPLICATE_RADIUS_METERS) {
                        return $candidate;
                    }
                }
            }
        }

        return null;
    }

    private function shouldEnrichNearbyStation(string $existingName, string $incomingName, float $distanceMeters): bool
    {
        $a = strtolower(trim($existingName));
        $b = strtolower(trim($incomingName));

        foreach (['hyundai', 'wuling', 'voltron', 'starvo', 'shell', 'charge+'] as $brand) {
            if (str_contains($b, $brand) && ! str_contains($a, $brand) && $distanceMeters > 20.0) {
                return false;
            }
        }

        if ($distanceMeters <= 25.0) {
            return true;
        }

        if (preg_match('/^(spklu|spklu\s+pln|perusahaan listrik negara|spklu\s+umum|\?)$/i', trim($b))) {
            return true;
        }

        $stop = ['spklu', 'pln', 'ulp', 'up3', 'uid', 'uiw', 'pt', 'tbk', 'jl', 'kota', 'kab', 'indonesia', 'charging', 'station', 'ev', 'umum', 'center'];
        $tokensA = array_diff(preg_split('/[^a-z0-9]+/', $a, -1, PREG_SPLIT_NO_EMPTY) ?: [], $stop);
        $tokensB = array_diff(preg_split('/[^a-z0-9]+/', $b, -1, PREG_SPLIT_NO_EMPTY) ?: [], $stop);
        $shared = array_intersect($tokensA, $tokensB);

        return count(array_filter($shared, fn (string $t): bool => strlen($t) >= 3 || is_numeric($t))) >= 1;
    }

    private function loadExisting(): void
    {
        if ($this->existing !== null) {
            return;
        }

        $this->existing = [];
        $this->byExternalId = [];
        $this->grid = [];

        Station::query()
            ->get(['id_stations', 'latitude', 'longitude', 'source', 'external_id'])
            ->each(function (Station $s) {
                $entry = $this->toEntry($s);
                $this->existing[$entry['id']] = $entry;
                $this->indexEntry($entry);
            });
    }

    /**
     * @param  array{id:int, lat:float, lng:float, source:string, external_id:?string}  $entry
     */
    private function indexEntry(array $entry): void
    {
        if ($entry['id'] > 0) {
            $this->existing[$entry['id']] = $entry;
        }

        if ($entry['external_id'] !== null && $entry['external_id'] !== '') {
            $this->byExternalId[$entry['source'].'|'.$entry['external_id']] = $entry;
        }

        $bucketKey = ((int) floor($entry['lat'] * 100)).':'.((int) floor($entry['lng'] * 100));
        $this->grid[$bucketKey][$entry['id']] = $entry;
    }

    private function toEntry(Station $s): array
    {
        return [
            'id' => (int) $s->id_stations,
            'lat' => (float) $s->latitude,
            'lng' => (float) $s->longitude,
            'source' => (string) ($s->source ?? 'manual'),
            'external_id' => $s->external_id,
        ];
    }
}
