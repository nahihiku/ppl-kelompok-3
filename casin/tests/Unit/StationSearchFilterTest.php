<?php

namespace Tests\Unit;

use App\Http\Controllers\StationController;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class StationSearchFilterTest extends TestCase
{
    private function search(array $params): Collection
    {
        $stations = collect([
            ['id' => '1', 'name' => 'Bandung Campuran', 'address' => 'Bandung', 'operator_badge' => 'PLN',
                'current_status' => 'normal', 'connector_type' => 'CCS2', 'connector_search' => 'ccs2 | ac type 2',
                'latitude' => -6.9, 'longitude' => 107.6, 'distance_km' => 0],
            ['id' => '2', 'name' => 'Bandung AC', 'address' => 'Bandung', 'operator_badge' => 'PLN',
                'current_status' => 'normal', 'connector_type' => 'AC Type 2', 'connector_search' => 'ac type 2',
                'latitude' => -6.91, 'longitude' => 107.61, 'distance_km' => 0],
            ['id' => '3', 'name' => 'Jakarta DC', 'address' => 'Jakarta', 'operator_badge' => 'SWASTA',
                'current_status' => 'busy', 'connector_type' => 'CCS2', 'connector_search' => 'ccs2',
                'latitude' => -6.2, 'longitude' => 106.8, 'distance_km' => 0],
        ]);

        return (new ReflectionMethod(StationController::class, 'filterStations'))
            ->invoke(new StationController, $stations, Request::create('/api/stations', 'GET', $params));
    }

    public function test_vehicle_compatibility_is_combined_with_manual_connector_and_other_filters(): void
    {
        $results = $this->search([
            'q' => 'bandung', 'vehicle_connector' => 'CCS2', 'connector' => 'AC Type 2',
            'operator' => 'PLN', 'status' => 'normal',
        ]);

        $this->assertSame(['1'], $results->pluck('id')->all());
        $this->assertCount(0, $this->search(['vehicle_connector' => 'CHAdeMO']));
        $this->assertCount(3, $this->search([]));
    }

    public function test_new_location_changes_order_and_distances(): void
    {
        $bandung = $this->search(['lat' => -6.9, 'lng' => 107.6]);
        $jakarta = $this->search(['lat' => -6.2, 'lng' => 106.8]);

        $this->assertSame('1', $bandung->first()['id']);
        $this->assertSame('3', $jakarta->first()['id']);
        $this->assertEquals(0, $bandung->first()['distance_km']);
        $this->assertGreaterThan(100, $jakarta->last()['distance_km']);
    }
}
