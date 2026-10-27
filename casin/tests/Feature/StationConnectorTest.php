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
}
