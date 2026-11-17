<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Earlier checkouts already ran migrations whose source now uses new names.
        // Rename the persisted legacy columns without recreating or reseeding data.
        foreach ([
            'users' => ['id' => 'id_user', 'name' => 'nama'],
            'station_connectors' => [
                'id_station_connectors' => 'id_station_connector',
                'station_id' => 'id_stations',
            ],
        ] as $tableName => $columns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($columns as $legacy => $current) {
                if (Schema::hasColumn($tableName, $legacy) && ! Schema::hasColumn($tableName, $current)) {
                    Schema::table($tableName, fn (Blueprint $table) => $table->renameColumn($legacy, $current));
                }
            }
        }
    }

    public function down(): void
    {
        // Keep the canonical names: fresh databases also use these columns.
    }
};
