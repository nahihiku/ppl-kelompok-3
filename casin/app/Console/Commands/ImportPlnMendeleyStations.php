<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Services\StationImportService;
use Illuminate\Console\Command;

/**
 * Mengimpor data SPKLU Indonesia dari dataset publik petaspklu.id / PT PLN (Persero)
 * yang dikompilasi pada Mendeley Data v2.0 (DOI: 10.17632/z2bvd5r574.2).
 *
 * Lisensi dataset: CC BY-NC 3.0 (khusus penggunaan non-komersial dengan atribusi).
 */
class ImportPlnMendeleyStations extends Command
{
    protected $signature = 'stations:import-pln
        {--stations= : Path file CSV stasiun (bawaan database/data/petaspklu_stations.csv)}
        {--chargers= : Path file CSV charger (bawaan database/data/petaspklu_chargers.csv)}
        {--esdm= : Path file CSV konektor ESDM/PLN Mobile (bawaan database/data/pln_esdm_connectors.csv)}
        {--dry-run : Validasi dan tampilkan ringkasan tanpa menyimpan}';

    protected $description = 'Impor data SPKLU seluruh Indonesia dari dataset petaspklu.id / PT PLN (Persero)';

    /** Pemetaan aplikasi resmi operator yang tercatat pada operator_brand. */
    private const BRAND_APPS = [
        'voltron' => 'Voltron App',
        'starvo' => 'Starvo App',
        'terra charge' => 'Terra Charge',
        'hyundai' => 'MyHyundai ID',
        'shell' => 'Shell Recharge',
        'charge+' => 'Charge+ App',
        'utomo' => 'Charge+ App',
        'astra' => 'Astra Otoshop',
        'ion' => 'ION Mobility',
        'bluecharge' => 'BlueCharge',
        'dayagreen' => 'DayaGreen',
    ];

    public function handle(StationImportService $importer): int
    {
        $stationsPath = (string) ($this->option('stations') ?: database_path('data/petaspklu_stations.csv'));
        $chargersPath = (string) ($this->option('chargers') ?: database_path('data/petaspklu_chargers.csv'));
        $esdmPath = (string) ($this->option('esdm') ?: database_path('data/pln_esdm_connectors.csv'));

        if (! is_file($stationsPath)) {
            $this->error("File stasiun tidak ditemukan: {$stationsPath}");

            return self::FAILURE;
        }

        $esdmByBox = is_file($esdmPath) ? $this->loadEsdmMappings($esdmPath) : [];
        $chargersByStation = is_file($chargersPath) ? $this->loadChargers($chargersPath, $esdmByBox) : [];
        $items = $this->loadStations($stationsPath, $chargersByStation);

        $dryRun = (bool) $this->option('dry-run');
        $stats = $importer->importBatch($items, $dryRun);

        $this->table(['Baru', 'Diperbarui', 'Duplikat (<100 m dari data lain)', 'Total diproses'], [[
            $stats['created'], $stats['updated'], $stats['duplicate'], count($items),
        ]]);

        if ($dryRun) {
            $this->warn('Mode dry-run: tidak ada data yang disimpan.');
        }

        $this->line('Sumber: petaspklu.id / PT PLN (Persero) via Mendeley Data v2.0 (DOI: 10.17632/z2bvd5r574.2, CC BY-NC 3.0) + pemetaan soket PLN Mobile / Kementerian ESDM.');

        return self::SUCCESS;
    }

    /**
     * Memuat pemetaan tipe soket fisik resmi (CHAdeMO, CCS2, AC Type 2) dari registry PLN Mobile / Kementerian ESDM.
     *
     * @return array<string, array<int, array<int, array{connector_type: string, power_kw: float, current_type: string, port_count: int}>>>
     */
    private function loadEsdmMappings(string $path): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return [];
        }

        $header = array_map(fn ($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h)), $header);
        $idx = array_flip($header);

        $mappings = [];
        while (($row = fgetcsv($handle)) !== false) {
            $stationId = trim((string) ($row[$idx['station_id'] ?? -1] ?? ''));
            $boxIdxRaw = $row[$idx['box_index'] ?? -1] ?? null;
            $type = trim((string) ($row[$idx['connector_type'] ?? -1] ?? ''));
            $power = round((float) ($row[$idx['power_kw'] ?? -1] ?? 0), 2);
            $current = strtoupper(trim((string) ($row[$idx['current_type'] ?? -1] ?? '')));
            $portCount = max(1, (int) ($row[$idx['port_count'] ?? -1] ?? 1));

            if ($stationId === '' || $boxIdxRaw === null || $boxIdxRaw === '' || $type === '' || $power <= 0) {
                continue;
            }

            $boxIdx = (int) $boxIdxRaw;
            $mappings[$stationId][$boxIdx][] = [
                'connector_type' => $type,
                'power_kw' => $power,
                'current_type' => in_array($current, ['AC', 'DC'], true) ? $current : 'DC',
                'port_count' => $portCount,
            ];
        }
        fclose($handle);

        return $mappings;
    }

    /**
     * @param  array<string, array<int, array<int, array{connector_type: string, power_kw: float, current_type: string, port_count: int}>>>  $esdmByBox
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function loadChargers(string $path, array $esdmByBox = []): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return [];
        }

        $header = array_map(fn ($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h)), $header);
        $idx = array_flip($header);

        $grouped = [];
        $boxIndexByStation = [];
        while (($row = fgetcsv($handle)) !== false) {
            $stationId = trim((string) ($row[$idx['station_id'] ?? -1] ?? ''));
            if ($stationId === '') {
                continue;
            }

            $boxIdx = $boxIndexByStation[$stationId] ?? 0;
            $boxIndexByStation[$stationId] = $boxIdx + 1;

            $power = (float) ($row[$idx['box_power_kW'] ?? -1] ?? 0);
            if ($power <= 0) {
                continue;
            }

            $power = round($power, 2);
            $rawCurrent = strtoupper(trim((string) ($row[$idx['inferred_current'] ?? -1] ?? '')));
            $tier = strtoupper(trim((string) ($row[$idx['box_power_tier'] ?? -1] ?? '')));
            $current = in_array($rawCurrent, ['AC', 'DC'], true)
                ? $rawCurrent
                : (str_starts_with($tier, 'DC') ? 'DC' : 'AC');

            // Dataset Mendeley kehilangan pemisah desimal pada wallbox AC 7,4 kW sehingga tercatat 74.0 kW.
            if ($current === 'AC' && abs($power - 74.0) < 0.01) {
                $power = 7.4;
            }

            $esdmPorts = $esdmByBox[$stationId][$boxIdx] ?? null;
            if ($esdmPorts !== null) {
                foreach ($esdmPorts as $ep) {
                    $cType = $ep['connector_type'];
                    $pKw = $ep['power_kw'];
                    $cCur = $ep['current_type'];
                    $cnt = $ep['port_count'];
                    $groupKey = $cType.'|'.$pKw.'|'.$cCur;
                    if (! isset($grouped[$stationId][$groupKey])) {
                        $grouped[$stationId][$groupKey] = [
                            'connector_type' => $cType,
                            'power_kw' => $pKw,
                            'charging_speed_category' => StationImportService::speedCategory($pKw),
                            'current_type' => $cCur,
                            'available_connectors' => 0,
                            'total_connectors' => 0,
                        ];
                    }
                    $grouped[$stationId][$groupKey]['available_connectors'] += $cnt;
                    $grouped[$stationId][$groupKey]['total_connectors'] += $cnt;
                }

                continue;
            }

            $boxName = (string) ($row[$idx['chargerbox_name'] ?? -1] ?? '');
            $connectorType = $this->resolveConnectorType($boxName, $current);
            $connectorsCount = max(1, (int) ($row[$idx['n_connectors'] ?? -1] ?? 1));

            $groupKey = $connectorType.'|'.$power.'|'.$current;
            if (! isset($grouped[$stationId][$groupKey])) {
                $grouped[$stationId][$groupKey] = [
                    'connector_type' => $connectorType,
                    'power_kw' => $power,
                    'charging_speed_category' => StationImportService::speedCategory($power),
                    'current_type' => $current,
                    'available_connectors' => 0,
                    'total_connectors' => 0,
                ];
            }

            $grouped[$stationId][$groupKey]['available_connectors'] += $connectorsCount;
            $grouped[$stationId][$groupKey]['total_connectors'] += $connectorsCount;
        }
        fclose($handle);

        $result = [];
        foreach ($grouped as $stationId => $boxes) {
            $result[$stationId] = array_values($boxes);
        }

        return $result;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $chargersByStation
     * @return array<int, array{station: array<string, mixed>, connectors: array<int, array<string, mixed>>}>
     */
    private function loadStations(string $path, array $chargersByStation): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return [];
        }

        $header = array_map(fn ($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h)), $header);
        $idx = array_flip($header);

        $items = [];
        while (($row = fgetcsv($handle)) !== false) {
            $stationId = trim((string) ($row[$idx['station_id'] ?? -1] ?? ''));
            $name = trim((string) ($row[$idx['location_name'] ?? -1] ?? ''));
            $lat = $row[$idx['latitude'] ?? -1] ?? null;
            $lng = $row[$idx['longitude'] ?? -1] ?? null;

            if ($stationId === '' || $name === '' || ! Station::isValidCoordinatePair($lat, $lng)) {
                continue;
            }

            $providerType = trim((string) ($row[$idx['provider_type'] ?? -1] ?? ''));
            $operatorBrand = trim((string) ($row[$idx['operator_brand'] ?? -1] ?? ''));
            $locationGroup = trim((string) ($row[$idx['location_group'] ?? -1] ?? ''));
            $rawAddress = trim((string) ($row[$idx['address'] ?? -1] ?? ''));
            $regency = trim((string) ($row[$idx['regency_city_clean'] ?? -1] ?? ''));
            $province = trim((string) ($row[$idx['province_name'] ?? -1] ?? ''));
            $statusActive = trim((string) ($row[$idx['status_active'] ?? -1] ?? '1'));

            $isPlnNetwork = $providerType === 'PLN / SPKLU network';
            $connectors = $chargersByStation[$stationId] ?? [];

            if ($statusActive === '0') {
                foreach ($connectors as &$c) {
                    $c['available_connectors'] = 0;
                }
                unset($c);
            }

            $items[] = [
                'station' => [
                    'name' => mb_substr($name, 0, 255),
                    'operator_type' => $this->resolveOperatorType($providerType, $locationGroup),
                    'address' => mb_substr($this->formatAddress($rawAddress, $regency, $province), 0, 255),
                    'latitude' => (float) $lat,
                    'longitude' => (float) $lng,
                    'operating_hours' => 'Belum diketahui',
                    // Tarif resmi SPKLU PLN sesuai Permen ESDM; operator swasta/diler dibiarkan null bila tidak tercantum.
                    'price_per_kwh' => $isPlnNetwork ? 2466 : null,
                    'require_app_name' => $this->resolveAppName($isPlnNetwork, $operatorBrand, $name),
                    'current_status' => $statusActive === '0' ? 'technical_issue' : 'normal',
                    'source' => 'pln',
                    'external_id' => 'petaspklu:'.$stationId,
                ],
                'connectors' => $connectors,
            ];
        }
        fclose($handle);

        return $items;
    }

    private function resolveConnectorType(string $boxName, string $current): string
    {
        $lower = strtolower($boxName);

        return match (true) {
            str_contains($lower, 'chademo') => 'CHAdeMO',
            str_contains($lower, 'ccs') || str_contains($lower, 'combo') => 'CCS2',
            str_contains($lower, 'gb/t') || str_contains($lower, 'gbt') => 'GB/T',
            str_contains($lower, 'type 2') || str_contains($lower, 'type2') => 'AC Type 2',
            $current === 'DC' => 'DC Connector',
            default => 'AC Connector',
        };
    }

    private function resolveOperatorType(string $providerType, string $locationGroup): string
    {
        if ($providerType === 'PLN / SPKLU network') {
            return 'government';
        }

        if (in_array($locationGroup, ['Retail & Commercial', 'Hospitality'], true)) {
            return 'commercial';
        }

        return 'private';
    }

    private function resolveAppName(bool $isPlnNetwork, string $operatorBrand, string $stationName): ?string
    {
        if ($isPlnNetwork) {
            return 'PLN Mobile';
        }

        $haystack = strtolower($operatorBrand.' '.$stationName);
        foreach (self::BRAND_APPS as $keyword => $app) {
            if (str_contains($haystack, $keyword)) {
                return $app;
            }
        }

        return null;
    }

    private function formatAddress(string $rawAddress, string $regency, string $province): string
    {
        $regionSuffix = implode(', ', array_filter([$regency, $province]));

        if ($rawAddress === '') {
            return $regionSuffix !== '' ? $regionSuffix : 'Indonesia';
        }

        $lowerAddr = strtolower($rawAddress);
        $cleanRegency = trim((string) preg_replace('/^(kota\s+administrasi|kota|kabupaten)\s+/i', '', $regency));

        $partsToAppend = [];
        if ($cleanRegency !== '' && ! str_contains($lowerAddr, strtolower($cleanRegency))) {
            $partsToAppend[] = $regency;
        }
        if ($province !== '' && ! str_contains($lowerAddr, strtolower($province))) {
            $partsToAppend[] = $province;
        }

        return $partsToAppend !== []
            ? $rawAddress.', '.implode(', ', $partsToAppend)
            : $rawAddress;
    }
}
