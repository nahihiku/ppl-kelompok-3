<?php

namespace Tests\Feature;

use App\Models\Station;
use App\Models\StationConnector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationConnectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_station_connectors_are_stored_and_related_to_station(): void
    {
        $station = Station::create([
            'name' => 'PLN Connector Test',
            'operator_type' => 'government',
            'address' => 'Jakarta',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'operating_hours' => '24 Jam',
            'current_status' => 'normal',
        ]);

        $connector = $station->connectors()->create([
            'connector_type' => 'CCS2',
            'power_kw' => 50,
            'charging_speed_category' => 'ultra',
            'current_type' => 'DC',
            'available_connectors' => 2,
            'total_connectors' => 3,
        ]);

        $this->assertInstanceOf(StationConnector::class, $connector);
        $this->assertSame('CCS2', $station->fresh()->connectors->first()->connector_type);
        $this->assertSame($station->id_stations, $connector->station->id_stations);

        $this->withoutVite();
        $this->get(route('stations.index'))
            ->assertOk()
            ->assertSee('CCS2')
            ->assertSee('2 dari 3');
    }

    public function test_station_distance_and_direct_navigation_url_methods_work_per_proposal(): void
    {
        $validStation = new Station([
            'name' => 'SPKLU Gambir',
            'latitude' => -6.1767,
            'longitude' => 106.8306,
        ]);

        $this->assertTrue($validStation->hasValidCoordinates());
        $distanceKm = $validStation->calculateDistance(-6.2088, 106.8456);
        $this->assertGreaterThan(3.0, $distanceKm);
        $this->assertLessThan(5.0, $distanceKm);
        $this->assertSame(
            'https://www.google.com/maps/dir/?api=1&destination=-6.1767,106.8306',
            $validStation->getDirectNavUrl()
        );

        $invalidStation = new Station([
            'name' => 'SPKLU Tanpa Koordinat',
            'latitude' => 0.0,
            'longitude' => 0.0,
        ]);

        $this->assertFalse($invalidStation->hasValidCoordinates());
        $this->assertNull($invalidStation->getDirectNavUrl());
    }

    public function test_stations_api_endpoint_returns_filtered_and_distance_sorted_stations(): void
    {
        $response = $this->getJson(route('api.stations.index', [
            'lat' => -6.1767,
            'lng' => 106.8306,
            'connector' => 'CCS2',
        ]));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'center' => ['latitude', 'longitude', 'source'],
                    'count',
                    'stations' => [
                        '*' => [
                            'id',
                            'name',
                            'latitude',
                            'longitude',
                            'is_valid_coordinate',
                            'direct_nav_url',
                            'detail_url',
                            'distance_km',
                        ],
                    ],
                ],
            ]);

        $stations = $response->json('data.stations');
        $this->assertNotEmpty($stations);
        $this->assertSame('PLN SPKLU Gambir Utama', $stations[0]['name']);
    }

    public function test_database_seeder_populates_real_nationwide_stations_without_synthetic_records(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $stations = Station::with('connectors')->get();
        $this->assertGreaterThanOrEqual(2000, $stations->count());

        $bySource = $stations->groupBy('source')->map->count()->all();
        $this->assertSame(35, $bySource['manual'] ?? 0);
        $this->assertGreaterThan(2000, $bySource['pln'] ?? 0);
        $this->assertGreaterThan(0, $bySource['osm'] ?? 0);

        foreach ($stations as $station) {
            $this->assertTrue($station->hasValidCoordinates(), "Koordinat tidak valid pada stasiun: {$station->name}");
            $lat = (float) $station->latitude;
            $lng = (float) $station->longitude;
            $this->assertGreaterThanOrEqual(-11.5, $lat, "Latitude di luar batas Indonesia pada: {$station->name}");
            $this->assertLessThanOrEqual(6.5, $lat, "Latitude di luar batas Indonesia pada: {$station->name}");
            $this->assertGreaterThanOrEqual(94.5, $lng, "Longitude di luar batas Indonesia pada: {$station->name}");
            $this->assertLessThanOrEqual(141.5, $lng, "Longitude di luar batas Indonesia pada: {$station->name}");
        }

        $names = $stations->pluck('name')->all();
        $this->assertContains('PLN SPKLU Substation Central', $names);
        $this->assertNotContains('PLN SPKLU UID Aceh Banda Aceh', $names);
        $this->assertNotContains('PLN SPKLU UIW Papua dan Papua Barat Jayapura', $names);

        $addressesUpper = $stations->pluck('address')->map(fn (string $addr): string => strtoupper($addr))->implode("\n");
        foreach ([
            'ACEH',
            'SUMATERA UTARA',
            'RIAU',
            'DKI JAKARTA',
            'JAWA BARAT',
            'JAWA TIMUR',
            'BALI',
            'NUSA TENGGARA BARAT',
            'NUSA TENGGARA TIMUR',
            'KALIMANTAN BARAT',
            'KALIMANTAN TIMUR',
            'SULAWESI SELATAN',
            'SULAWESI UTARA',
            'MALUKU',
            'PAPUA',
        ] as $region) {
            $this->assertStringContainsString($region, $addressesUpper, "Wilayah {$region} tidak ditemukan pada dataset stasiun hasil seeder.");
        }
    }

    public function test_pln_and_osm_import_commands_support_dry_run_and_are_idempotent(): void
    {
        $this->artisan('stations:import-pln', ['--dry-run' => true])->assertSuccessful();
        $this->assertSame(0, Station::count());

        $this->artisan('stations:import-pln')->assertSuccessful();
        $this->artisan('stations:import-osm', [
            '--file' => base_path('database/data/osm_charging_stations.json'),
        ])->assertSuccessful();

        $firstStationCount = Station::count();
        $firstConnectorCount = StationConnector::count();
        $this->assertGreaterThan(2000, $firstStationCount);
        $this->assertGreaterThan(2500, $firstConnectorCount);

        $this->artisan('stations:import-pln')->assertSuccessful();
        $this->artisan('stations:import-osm', [
            '--file' => base_path('database/data/osm_charging_stations.json'),
        ])->assertSuccessful();

        $this->assertSame($firstStationCount, Station::count());
        $this->assertSame($firstConnectorCount, StationConnector::count());
    }

    public function test_csv_import_command_imports_stations_and_respects_deduplication(): void
    {
        $tmpCsv = tempnam(sys_get_temp_dir(), 'casin_csv_');
        $csvContent = implode("\n", [
            'name,address,latitude,longitude,operator_type,operating_hours,price_per_kwh,require_app_name,connector_type,power_kw,current_type,total_connectors,external_id',
            'SPKLU Uji CSV Pertama,"Jl. Uji Coba No. 1, Bandung",-6.917464,107.619123,government,24 Jam,2466,PLN Mobile,CCS2,50,DC,2,csv-test-1',
            'SPKLU Uji CSV Pertama,"Jl. Uji Coba No. 1, Bandung",-6.917464,107.619123,government,24 Jam,2466,PLN Mobile,AC Type 2,22,AC,1,csv-test-1',
            'SPKLU Duplikat Dekat,"Jl. Uji Coba No. 2, Bandung",-6.917500,107.619150,private,24 Jam,2500,Voltron,CCS2,60,DC,2,csv-test-dup',
        ]);
        file_put_contents($tmpCsv, $csvContent);

        try {
            $this->artisan('stations:import-csv', ['file' => $tmpCsv])->assertSuccessful();

            $this->assertSame(1, Station::count());
            $station = Station::with('connectors')->firstOrFail();
            $this->assertSame('SPKLU Uji CSV Pertama', $station->name);
            $this->assertSame(2, $station->connectors->count());
        } finally {
            @unlink($tmpCsv);
        }
    }

    public function test_stations_detail_and_api_filtering_scale_cleanly_with_seeded_database(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->withoutVite();
        $detailResponse = $this->get(route('stations.show', '1'));
        $detailResponse->assertOk()
            ->assertSee('PLN SPKLU Substation Central');

        $html = $detailResponse->getContent();
        $this->assertIsString($html);
        $this->assertLessThan(200000, strlen($html), 'Payload HTML halaman detail terlalu besar untuk 2.400+ stasiun.');

        $apiResponse = $this->getJson(route('api.stations.index', [
            'q' => 'Papua',
            'connector' => 'CCS2',
        ]));

        $apiResponse->assertOk()
            ->assertJsonPath('success', true);
        $this->assertGreaterThanOrEqual(1, $apiResponse->json('data.count'));
    }

    public function test_pln_and_osm_enrichment_maps_real_plug_types_including_chademo_and_ccs2(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $embongWungu = Station::with('connectors')
            ->where('name', 'SPKLU PLN ULP Embong Wungu')
            ->firstOrFail();

        $embongTypes = $embongWungu->connectors->pluck('connector_type')->all();
        $this->assertContains('CHAdeMO', $embongTypes);
        $this->assertContains('CCS2', $embongTypes);
        $this->assertNotContains('DC Connector', $embongTypes);

        $wtcSurabaya = Station::with('connectors')
            ->where('name', 'SPKLU PLN WTC Surabaya Mall')
            ->firstOrFail();
        $wtcTypes = $wtcSurabaya->connectors->pluck('connector_type')->all();
        $this->assertContains('CHAdeMO', $wtcTypes);
        $this->assertContains('CCS2', $wtcTypes);
        $this->assertContains('AC Type 2', $wtcTypes);

        $veronaHills = Station::with('connectors')
            ->where('name', 'SPKLU PLN VERONA HILLS')
            ->firstOrFail();
        $this->assertContains('AC Type 2', $veronaHills->connectors->pluck('connector_type')->all());

        // Pastikan koreksi tipografi Mendeley 74.0 kW AC -> 7.4 kW aktif dan nol data AC > 43 kW tersisa.
        $this->assertSame(0, StationConnector::where('current_type', 'AC')->where('power_kw', '>', 43)->count());

        $this->withoutVite();
        $catalogResponse = $this->get(route('stations.index', ['q' => 'Embong Wungu']));
        $catalogResponse->assertOk()
            ->assertSee('SPKLU PLN ULP Embong Wungu')
            ->assertSee('CHAdeMO')
            ->assertSee('CCS2');

        $detailResponse = $this->get(route('stations.show', (string) $embongWungu->id_stations));
        $detailResponse->assertOk()
            ->assertSee('CHAdeMO')
            ->assertSee('CCS2');

        $chademoApi = $this->getJson(route('api.stations.index', ['connector' => 'CHAdeMO']));
        $chademoApi->assertOk()
            ->assertJsonPath('success', true);
        $this->assertGreaterThanOrEqual(100, $chademoApi->json('data.count'));
        $this->assertContains(
            'SPKLU PLN ULP Embong Wungu',
            array_column($chademoApi->json('data.stations'), 'name')
        );
    }
}
