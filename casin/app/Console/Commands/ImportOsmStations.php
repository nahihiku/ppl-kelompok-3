<?php

namespace App\Console\Commands;

use App\Services\StationImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Mengimpor stasiun pengisian kendaraan listrik di Indonesia dari OpenStreetMap
 * (tag amenity=charging_station) melalui Overpass API.
 *
 * Data OpenStreetMap berlisensi ODbL: (c) kontributor OpenStreetMap.
 */
class ImportOsmStations extends Command
{
    protected $signature = 'stations:import-osm
        {--file= : Baca dari file JSON Overpass lokal alih-alih mengunduh}
        {--include-unnamed : Ikut impor titik tanpa nama dan tanpa operator}
        {--dry-run : Tampilkan ringkasan tanpa menyimpan ke basis data}';

    protected $description = 'Impor data SPKLU Indonesia dari OpenStreetMap (Overpass API)';

    /** @var array<int, string> */
    private const ENDPOINTS = [
        'https://overpass-api.de/api/interpreter',
        'https://overpass.private.coffee/api/interpreter',
        'https://overpass.kumi.systems/api/interpreter',
    ];

    private const QUERY = '[out:json][timeout:170];'
        .'area["ISO3166-1"="ID"][admin_level=2]->.a;'
        .'(node["amenity"="charging_station"](area.a);way["amenity"="charging_station"](area.a););'
        .'out center tags;';

    /** Pemetaan tag socket OSM ke tipe konektor aplikasi. */
    private const SOCKETS = [
        'type2_combo' => ['CCS2', 'DC'],
        'chademo' => ['CHAdeMO', 'DC'],
        'type2' => ['AC Type 2', 'AC'],
        'type2_cable' => ['AC Type 2', 'AC'],
        'gb_dc' => ['GB/T', 'DC'],
        'gb_ac' => ['GB/T', 'AC'],
    ];

    /** Aplikasi resmi berdasarkan kata kunci operator/brand. */
    private const APPS = [
        'pln' => 'PLN Mobile',
        'shell' => 'Shell Recharge',
        'starvo' => 'Starvo App',
        'voltron' => 'Voltron App',
        'charge+' => 'Charge+ App',
        'chargeplus' => 'Charge+ App',
        'ion' => 'ION Mobility',
        'hyundai' => 'MyHyundai ID',
        'astra' => 'Astra Otoshop',
        'pertamina' => 'MyPertamina',
    ];

    public function handle(StationImportService $importer): int
    {
        $elements = $this->loadElements();
        if ($elements === null) {
            return self::FAILURE;
        }

        $this->info('Ditemukan '.count($elements).' titik charging_station di OpenStreetMap.');

        $items = [];
        $skipped = 0;
        $dryRun = (bool) $this->option('dry-run');

        foreach ($elements as $element) {
            $mapped = $this->mapElement($element);
            if ($mapped === null) {
                $skipped++;

                continue;
            }

            [$station, $connectors] = $mapped;
            $items[] = ['station' => $station, 'connectors' => $connectors];
        }

        $stats = $importer->importBatch($items, $dryRun, true);

        $this->table(['Baru', 'Diperbarui', 'Duplikat (<100 m dari data lain)', 'Dilewati'], [[
            $stats['created'], $stats['updated'], $stats['duplicate'], $skipped,
        ]]);

        if ($dryRun) {
            $this->warn('Mode dry-run: tidak ada data yang disimpan.');
        }

        $this->line('Sumber: (c) kontributor OpenStreetMap, lisensi ODbL.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function loadElements(): ?array
    {
        if ($file = $this->option('file')) {
            if (! is_file($file)) {
                $this->error("File tidak ditemukan: {$file}");

                return null;
            }

            return json_decode((string) file_get_contents($file), true)['elements'] ?? [];
        }

        foreach (self::ENDPOINTS as $endpoint) {
            $this->line("Mengunduh dari {$endpoint} ...");
            try {
                $response = Http::asForm()
                    ->withUserAgent('CasIn.id/1.0 (proyek PPL)')
                    ->acceptJson()
                    ->timeout(200)
                    ->post($endpoint, ['data' => self::QUERY]);

                if ($response->successful()) {
                    return $response->json('elements') ?? [];
                }

                $this->warn("Gagal ({$response->status()}), mencoba server lain.");
            } catch (\Throwable $e) {
                $this->warn('Gagal: '.$e->getMessage());
            }
        }

        $this->error('Semua server Overpass gagal. Coba lagi nanti atau gunakan --file.');

        return null;
    }

    /**
     * @param  array<string, mixed>  $element
     * @return array{0: array<string, mixed>, 1: array<int, array<string, mixed>>}|null
     */
    private function mapElement(array $element): ?array
    {
        $tags = $element['tags'] ?? [];
        $lat = $element['lat'] ?? $element['center']['lat'] ?? null;
        $lng = $element['lon'] ?? $element['center']['lon'] ?? null;

        if ($lat === null || $lng === null) {
            return null;
        }

        // Lewati titik khusus sepeda/motor saja atau yang tidak untuk umum.
        if (($tags['motorcar'] ?? null) === 'no' || in_array($tags['access'] ?? null, ['private', 'no'], true)) {
            return null;
        }

        $operator = trim((string) ($tags['operator'] ?? $tags['brand'] ?? $tags['network'] ?? ''));
        $name = trim((string) ($tags['name'] ?? ''));
        $ref = trim((string) ($tags['ref'] ?? ''));

        if (in_array(strtolower($name), ['spklu', 'splu', 'charging station'], true) && $ref !== '') {
            $name = $ref;
        }

        if ($name === '' && $operator === '' && $ref === '' && ! $this->option('include-unnamed')) {
            return null;
        }

        $city = $tags['addr:city'] ?? $tags['addr:district'] ?? $tags['addr:province'] ?? null;
        if ($name === '') {
            $name = $ref !== '' ? $ref : trim('SPKLU '.($operator !== '' ? $operator : 'Umum').($city ? " {$city}" : ''));
        }

        $haystack = strtolower($operator.' '.$name);
        $isPln = str_contains($haystack, 'pln') || str_contains($haystack, 'perusahaan listrik negara');

        $station = [
            'name' => mb_substr($name, 0, 255),
            'operator_type' => $this->operatorType($operator, $name, $isPln),
            'address' => mb_substr($this->address($tags, (float) $lat, (float) $lng), 0, 255),
            'latitude' => (float) $lat,
            'longitude' => (float) $lng,
            'operating_hours' => $this->hours($tags['opening_hours'] ?? null),
            // Tarif resmi SPKLU PLN; operator lain dibiarkan kosong karena tidak tercatat di OSM.
            'price_per_kwh' => $isPln ? 2466 : null,
            'require_app_name' => $this->app($operator.' '.$name),
            'current_status' => 'normal',
            'source' => 'osm',
            'external_id' => $element['type'].'/'.$element['id'],
        ];

        return [$station, $this->connectors($tags)];
    }

    /**
     * @param  array<string, string>  $tags
     * @return array<int, array<string, mixed>>
     */
    private function connectors(array $tags): array
    {
        $grouped = [];

        foreach (self::SOCKETS as $key => [$type, $current]) {
            $rawCount = strtolower(trim((string) ($tags["socket:{$key}"] ?? '')));
            if ($rawCount === '' || $rawCount === 'no' || $rawCount === '0') {
                continue;
            }
            $count = $rawCount === 'yes' ? 1 : (int) $rawCount;
            if ($count <= 0) {
                continue;
            }

            $power = $this->parsePower($tags["socket:{$key}:output"] ?? null)
                ?? ($current === 'DC' ? 50.0 : 22.0);

            $groupKey = $type.'|'.$power.'|'.$current;
            if (! isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'connector_type' => $type,
                    'power_kw' => $power,
                    'charging_speed_category' => StationImportService::speedCategory($power),
                    'current_type' => $current,
                    'available_connectors' => 0,
                    'total_connectors' => 0,
                ];
            }
            $grouped[$groupKey]['available_connectors'] += $count;
            $grouped[$groupKey]['total_connectors'] += $count;
        }

        return array_values($grouped);
    }

    private function parsePower(?string $raw): ?float
    {
        if (! $raw || ! preg_match('/([\d.,]+)\s*(kw|w)?/i', $raw, $m)) {
            return null;
        }

        $value = (float) str_replace(',', '.', $m[1]);
        if (strtolower($m[2] ?? 'kw') === 'w') {
            $value /= 1000;
        }

        return $value > 0 ? round($value, 2) : null;
    }

    private function operatorType(string $operator, string $name, bool $isPln): string
    {
        if ($isPln || preg_match('/pertamina|pemprov|pemkot|pemkab|dinas|kementerian/i', $operator)) {
            return 'government';
        }

        if (preg_match('/mall|plaza|hotel|square|town|city|resort|apartemen/i', $operator.' '.$name)) {
            return 'commercial';
        }

        return 'private';
    }

    private function app(string $haystack): ?string
    {
        $haystack = strtolower($haystack);
        foreach (self::APPS as $keyword => $app) {
            if (preg_match('/\b'.preg_quote($keyword, '/').'/', $haystack)) {
                return $app;
            }
        }

        return null;
    }

    private function hours(?string $raw): string
    {
        if (! $raw) {
            return 'Belum diketahui';
        }

        return trim($raw) === '24/7' ? '24 Jam' : mb_substr($raw, 0, 100);
    }

    /**
     * @param  array<string, string>  $tags
     */
    private function address(array $tags, float $lat, float $lng): string
    {
        if (! empty($tags['addr:full'])) {
            return $tags['addr:full'];
        }

        $street = trim(($tags['addr:street'] ?? '').' '.($tags['addr:housenumber'] ?? ''));
        $parts = array_filter([
            $street,
            $tags['addr:subdistrict'] ?? null,
            $tags['addr:district'] ?? null,
            $tags['addr:city'] ?? null,
            $tags['addr:province'] ?? null,
        ]);

        return $parts !== []
            ? implode(', ', $parts)
            : sprintf('Koordinat %.6f, %.6f (alamat belum tercatat di OpenStreetMap)', $lat, $lng);
    }
}
