<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_vehicle')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $tableSql = DB::table('sqlite_master')
                ->where('type', 'table')
                ->where('name', 'user_vehicle')
                ->value('sql');

            if (! is_string($tableSql) || ! preg_match('/references\s+"?vehicle_master"?\s*\(/i', $tableSql)) {
                return;
            }

            $existingRows = DB::table('user_vehicle')->get()->map(fn ($row) => (array) $row)->all();

            Schema::disableForeignKeyConstraints();
            Schema::drop('user_vehicle');
            Schema::create('user_vehicle', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('vehicle_id')->constrained('vehicle_masters', 'id_vehicle_masters')->onDelete('cascade');
                $table->string('license_plate')->nullable();
                $table->string('color')->nullable();
                $table->timestamps();
            });

            if (! empty($existingRows)) {
                $validUserIds = DB::table('users')->pluck('id')->all();
                $validVehicleIds = DB::table('vehicle_masters')->pluck('id_vehicle_masters')->all();
                $filtered = array_values(array_filter(
                    $existingRows,
                    fn (array $row) => in_array($row['user_id'] ?? null, $validUserIds, true)
                        && in_array($row['vehicle_id'] ?? null, $validVehicleIds, true)
                ));
                if (! empty($filtered)) {
                    DB::table('user_vehicle')->insert($filtered);
                }
            }

            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        // Tidak perlu mengembalikan foreign key ke nama tabel yang keliru.
    }
};

