<?php

namespace Database\Seeders;

use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $stations = [
            [
                'name' => 'PLN SPKLU Substation Central',
                'operator_type' => 'government',
                'address' => 'Jl. Letjen S. Parman No. 28, Jakarta Barat',
                'latitude' => -6.175392,
                'longitude' => 106.790512,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2000,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 2, 'total_connectors' => 3],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 1],
                ],
            ],
            [
                'name' => 'PLN SPKLU Gambir Utama',
                'operator_type' => 'government',
                'address' => 'Jl. Medan Merdeka Timur No. 1, Jakarta Pusat',
                'latitude' => -6.176655,
                'longitude' => 106.830589,
                'operating_hours' => '24 Jam',
                'price_per_kwh' => 2466,
                'require_app_name' => 'PLN Mobile',
                'current_status' => 'normal',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 50, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 3, 'total_connectors' => 4],
                ],
            ],
            [
                'name' => 'ION Mobility Sudirman',
                'operator_type' => 'private',
                'address' => 'Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan',
                'latitude' => -6.2254,
                'longitude' => 106.8067,
                'operating_hours' => '06.00 - 22.00',
                'price_per_kwh' => 2500,
                'require_app_name' => 'ION Mobility',
                'current_status' => 'busy',
                'connectors' => [
                    ['connector_type' => 'CCS2', 'power_kw' => 60, 'charging_speed_category' => 'ultra', 'current_type' => 'DC', 'available_connectors' => 0, 'total_connectors' => 2],
                    ['connector_type' => 'AC Type 2', 'power_kw' => 22, 'charging_speed_category' => 'fast', 'current_type' => 'AC', 'available_connectors' => 1, 'total_connectors' => 2],
                ],
            ],
        ];

        foreach ($stations as $stationData) {
            $connectors = $stationData['connectors'];
            unset($stationData['connectors']);
            $station = Station::updateOrCreate(['name' => $stationData['name']], $stationData);
            $station->connectors()->delete();
            $station->connectors()->createMany($connectors);
        }
    }
}
