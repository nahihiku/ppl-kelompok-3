<?php

namespace Tests\Feature;

use App\Models\Station;
use App\Models\StationConnector;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacySchemaUpgradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_database_upgrade_preserves_account_and_connector_data(): void
    {
        $user = User::factory()->create(['nama' => 'Existing account']);
        $passwordHash = $user->getRawOriginal('password');
        $station = Station::create([
            'name' => 'Existing station', 'operator_type' => 'government', 'address' => 'Jakarta',
            'latitude' => -6.2, 'longitude' => 106.8, 'operating_hours' => '24 Jam', 'current_status' => 'normal',
        ]);
        $connector = $station->connectors()->create([
            'connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra',
            'current_type' => 'DC', 'available_connectors' => 1, 'total_connectors' => 2,
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id');
            $table->renameColumn('nama', 'name');
        });
        Schema::table('station_connectors', function (Blueprint $table) {
            $table->renameColumn('id_station_connector', 'id_station_connectors');
            $table->renameColumn('id_stations', 'station_id');
        });

        $upgrade = require database_path('migrations/2026_11_16_000001_align_legacy_identity_columns.php');
        $upgrade->up();
        $upgrade->up(); // Already upgraded databases must remain valid.

        $restoredUser = User::findOrFail($user->getKey());
        $restoredConnector = StationConnector::findOrFail($connector->getKey());
        $this->assertSame('Existing account', $restoredUser->name);
        $this->assertSame($passwordHash, $restoredUser->getRawOriginal('password'));
        $this->assertSame($station->getKey(), $restoredConnector->station->getKey());
        $this->assertSame(1, $restoredConnector->available_connectors);
        $this->assertSame(2, $restoredConnector->total_connectors);
        $this->assertCount(1, $station->fresh()->connectors);
        $this->assertEmpty(DB::select('PRAGMA foreign_key_check'));
    }
}
