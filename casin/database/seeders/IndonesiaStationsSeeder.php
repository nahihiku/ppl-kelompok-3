<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class IndonesiaStationsSeeder extends Seeder
{
    /**
     * Memuat dataset terbuka terverifikasi (petaspklu.id / PT PLN Persero dan OpenStreetMap).
     */
    public function run(): void
    {
        $plnStations = database_path('data/petaspklu_stations.csv');
        $plnChargers = database_path('data/petaspklu_chargers.csv');
        if (is_file($plnStations)) {
            Artisan::call('stations:import-pln', [
                '--stations' => $plnStations,
                '--chargers' => is_file($plnChargers) ? $plnChargers : null,
            ]);
        }

        $osmFile = database_path('data/osm_charging_stations.json');
        if (is_file($osmFile)) {
            Artisan::call('stations:import-osm', [
                '--file' => $osmFile,
            ]);
        }
    }
}
