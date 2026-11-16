<?php

namespace Tests\Feature;

use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationDistanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_stations_sorted_by_distance_ascending(): void
    {
        $near = Station::create([
            'name' => 'SPKLU Stasiun Gambir',
            'operator_type' => 'government',
            'address' => 'Gambir, Jakarta',
            'latitude' => -6.1767,
            'longitude' => 106.8306,
            'operating_hours' => '24 Jam',
            'current_status' => 'normal',
        ]);
        $near->connectors()->create([
            'connector_type' => 'CCS2',
            'power_kw' => 50,
            'charging_speed_category' => 'ultra',
        ]);

        $far = Station::create([
            'name' => 'SPKLU PLN Bandung',
            'operator_type' => 'government',
            'address' => 'Bandung, Jawa Barat',
            'latitude' => -6.9147,
            'longitude' => 107.6098,
            'operating_hours' => '24 Jam',
            'current_status' => 'normal',
        ]);
        $far->connectors()->create([
            'connector_type' => 'CCS2',
            'power_kw' => 50,
            'charging_speed_category' => 'ultra',
        ]);

        $response = $this->getJson('/api/stations?lat=-6.2088&lng=106.8456');

        $response->assertOk();
        $stations = $response->json('data.stations');
        $this->assertCount(2, $stations);
        $this->assertSame('SPKLU Stasiun Gambir', $stations[0]['name']);
        $this->assertSame('SPKLU PLN Bandung', $stations[1]['name']);
        $this->assertLessThan($stations[1]['distance_km'], $stations[0]['distance_km']);
    }

    public function test_api_distance_fallback_to_monas_when_no_coordinates_sent(): void
    {
        $station = Station::create([
            'name' => 'SPKLU Thamrin',
            'operator_type' => 'government',
            'address' => 'Jakarta Pusat',
            'latitude' => -6.1880,
            'longitude' => 106.8230,
            'operating_hours' => '24 Jam',
            'current_status' => 'normal',
        ]);
        $station->connectors()->create([
            'connector_type' => 'CCS2',
            'power_kw' => 50,
            'charging_speed_category' => 'ultra',
        ]);

        $response = $this->getJson('/api/stations');

        $response->assertOk();
        $stations = $response->json('data.stations');
        $this->assertNotEmpty($stations);
        $this->assertNotNull($stations[0]['distance_km']);
        $this->assertSame('default', $response->json('data.center.source'));
    }
}
