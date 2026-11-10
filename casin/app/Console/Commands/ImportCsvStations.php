<?php

namespace App\Console\Commands;

use App\Services\StationImportService;
use Illuminate\Console\Command;

/**
 * Mengimpor data stasiun dari file CSV (misalnya ekspor resmi PLN).
 *
 * Satu baris = satu jenis konektor. Stasiun dengan beberapa jenis konektor
 * ditulis dalam beberapa baris dengan nama dan koordinat yang sama.
 * Contoh format: database/data/template_import_spklu.csv
 */
class ImportCsvStations extends Command
{
    protected $signature = 'stations:import-csv
        {file : Path file CSV}
        {--source=pln : Label sumber data yang disimpan di kolom source}
        {--delimiter=, : Pemisah kolom (gunakan ; untuk CSV dari Excel berbahasa Indonesia)}
        {--dry-run : Validasi dan tampilkan ringkasan tanpa menyimpan}';

    protected $description = 'Impor data SPKLU dari file CSV (contoh: data resmi PLN)';

    /** @var array<int, string> */
    private const REQUIRED = ['name', 'address', 'latitude', 'longitude'];

    public function handle(StationImportService $importer): int
    {
        $path = (string) $this->argument('file');
        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');
        $delimiter = (string) $this->option('delimiter');
        $header = fgetcsv($handle, 0, $delimiter);
        if (! $header) {
            $this->error('File CSV kosong.');

            return self::FAILURE;
        }

        $header = array_map(fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h))), $header);
        $missing = array_diff(self::REQUIRED, $header);
        if ($missing !== []) {
            $this->error('Kolom wajib tidak ada: '.implode(', ', $missing));

            return self::FAILURE;
        }

        $groups = [];
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line++;
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $data = array_combine($header, array_pad(array_slice($row, 0, count($header)), count($header), ''));
            $data = array_map(fn ($v) => trim((string) $v), $data);

            $lat = str_replace(',', '.', $data['latitude']);
            $lng = str_replace(',', '.', $data['longitude']);
            if ($data['name'] === '' || ! is_numeric($lat) || ! is_numeric($lng) || abs((float) $lat) > 90 || abs((float) $lng) > 180) {
                $errors[] = "Baris {$line}: nama atau koordinat tidak valid.";

                continue;
            }

            $key = ($data['external_id'] ?? '') !== ''
                ? $data['external_id']
                : md5(strtolower($data['name']).'|'.round((float) $lat, 5).'|'.round((float) $lng, 5));

            $groups[$key] ??= ['station' => $this->stationFrom($data, (float) $lat, (float) $lng, $key), 'connectors' => []];

            if ($connector = $this->connectorFrom($data)) {
                $groups[$key]['connectors'][] = $connector;
            }
        }
        fclose($handle);

        $dryRun = (bool) $this->option('dry-run');
        $stats = $importer->importBatch(array_values($groups), $dryRun);

        foreach ($errors as $error) {
            $this->warn($error);
        }

        $this->table(['Baru', 'Diperbarui', 'Duplikat (<100 m dari data lain)', 'Baris error'], [[
            $stats['created'], $stats['updated'], $stats['duplicate'], count($errors),
        ]]);

        if ($dryRun) {
            $this->warn('Mode dry-run: tidak ada data yang disimpan.');
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, string>  $data
     * @return array<string, mixed>
     */
    private function stationFrom(array $data, float $lat, float $lng, string $key): array
    {
        $operatorType = $data['operator_type'] ?? '';
        $source = (string) $this->option('source');

        return [
            'name' => mb_substr($data['name'], 0, 255),
            'operator_type' => in_array($operatorType, ['government', 'private', 'commercial'], true)
                ? $operatorType
                : ($source === 'pln' ? 'government' : 'private'),
            'address' => mb_substr($data['address'], 0, 255),
            'latitude' => $lat,
            'longitude' => $lng,
            'operating_hours' => ($data['operating_hours'] ?? '') !== '' ? $data['operating_hours'] : 'Belum diketahui',
            'price_per_kwh' => is_numeric($data['price_per_kwh'] ?? null) ? (float) $data['price_per_kwh'] : null,
            'require_app_name' => ($data['require_app_name'] ?? '') !== ''
                ? $data['require_app_name']
                : ($source === 'pln' ? 'PLN Mobile' : null),
            'current_status' => 'normal',
            'source' => $source,
            'external_id' => $key,
        ];
    }

    /**
     * @param  array<string, string>  $data
     * @return array<string, mixed>|null
     */
    private function connectorFrom(array $data): ?array
    {
        $type = StationImportService::normalizeConnectorType($data['connector_type'] ?? '');
        $power = (float) str_replace(',', '.', $data['power_kw'] ?? '');
        if ($type === null || $power <= 0) {
            return null;
        }

        $current = strtoupper($data['current_type'] ?? '');
        if (! in_array($current, ['AC', 'DC'], true)) {
            $current = $type === 'AC Type 2' ? 'AC' : 'DC';
        }

        $total = max(1, (int) ($data['total_connectors'] ?? 1));

        return [
            'connector_type' => $type,
            'power_kw' => $power,
            'charging_speed_category' => StationImportService::speedCategory($power),
            'current_type' => $current,
            'available_connectors' => $total,
            'total_connectors' => $total,
        ];
    }
}
