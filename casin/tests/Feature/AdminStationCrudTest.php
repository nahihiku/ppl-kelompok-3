<?php

namespace Tests\Feature;

use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_new_station_record_and_see_it_in_catalog(): void
    {
        $admin = User::factory()->admin()->create();

        $createResponse = $this->actingAs($admin)->postJson('/admin/api/stations', [
            'name' => 'SPKLU Alifia Test Baru',
            'operator_type' => 'government',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'latitude' => -6.1750,
            'longitude' => 106.8280,
            'operating_hours' => '24 Jam',
            'price_per_kwh' => 2466,
            'require_app_name' => 'PLN Mobile',
            'current_status' => 'normal',
            'connector_type' => 'CCS2',
            'power_kw' => 60,
        ]);

        $createResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('stations', [
            'name' => 'SPKLU Alifia Test Baru',
            'operator_type' => 'government',
        ]);

        // Verifikasi kemunculan di sisi user
        $catalogResponse = $this->getJson('/api/stations');
        $catalogResponse->assertOk();
        $stations = $catalogResponse->json('data.stations');
        $found = collect($stations)->firstWhere('name', 'SPKLU Alifia Test Baru');
        $this->assertNotNull($found);
    }

    public function test_admin_can_update_and_delete_station_record(): void
    {
        $admin = User::factory()->admin()->create();

        $station = Station::create([
            'name' => 'SPKLU Sebelum Diedit',
            'operator_type' => 'government',
            'address' => 'Jl. Awal No. 1',
            'latitude' => -6.2000,
            'longitude' => 106.8000,
            'operating_hours' => '24 Jam',
            'current_status' => 'normal',
        ]);

        // Test Update
        $updateResponse = $this->actingAs($admin)->putJson("/admin/api/stations/{$station->id_stations}", [
            'name' => 'SPKLU Sesudah Diedit',
            'operator_type' => 'private',
            'address' => 'Jl. Baru No. 99',
            'latitude' => -6.2100,
            'longitude' => 106.8100,
            'operating_hours' => '08:00 - 22:00',
            'price_per_kwh' => 3000,
            'require_app_name' => 'CasIn App',
            'current_status' => 'busy',
        ]);

        $updateResponse->assertOk();
        $this->assertDatabaseHas('stations', [
            'id_stations' => $station->id_stations,
            'name' => 'SPKLU Sesudah Diedit',
            'operator_type' => 'private',
            'current_status' => 'busy',
        ]);

        // Test Delete
        $deleteResponse = $this->actingAs($admin)->deleteJson("/admin/api/stations/{$station->id_stations}");
        $deleteResponse->assertOk();

        $this->assertDatabaseMissing('stations', [
            'id_stations' => $station->id_stations,
        ]);
    }
}
