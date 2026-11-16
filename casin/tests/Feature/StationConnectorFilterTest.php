<?php

namespace Tests\Feature;

use Tests\TestCase;

class StationConnectorFilterTest extends TestCase
{
    public function test_filters_stations_matching_active_vehicle_connector(): void
    {
        // Pengujian filter stasiun berdasarkan konektor CCS2 pada rute /stasiun
        $responseCcs = $this->get('/stasiun?connector=CCS2');
        $responseCcs->assertStatus(200);
        $responseCcs->assertViewIs('stations.index');

        $stationsCcs = $responseCcs->viewData('stations');
        $this->assertNotEmpty($stationsCcs);

        foreach ($stationsCcs as $station) {
            $hasCcs2 = str_contains(json_encode($station), 'CCS2');
            $this->assertTrue($hasCcs2, "Stasiun tidak mengandung konektor CCS2");
        }

        // Pengujian umum rute stasiun merespons dengan sukses
        $responseAll = $this->get('/stasiun');
        $responseAll->assertStatus(200);
        $responseAll->assertViewIs('stations.index');
    }
}
