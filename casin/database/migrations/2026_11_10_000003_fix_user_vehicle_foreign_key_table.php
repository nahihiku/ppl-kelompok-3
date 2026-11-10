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

        $hasAlignedColumns = Schema::hasColumn('user_vehicle', 'id_user')
            && Schema::hasColumn('user_vehicle', 'id_vehicle_masters');

        if (DB::getDriverName() === 'sqlite') {
            $tableSql = DB::table('sqlite_master')
                ->where('type', 'table')
                ->where('name', 'user_vehicle')
                ->value('sql');

            $hasBrokenVehicleFk = is_string($tableSql) && preg_match('/references\s+"?vehicle_master"?\s*\(/i', $tableSql);
            if ($hasAlignedColumns && ! $hasBrokenVehicleFk) {
                return;
            }

            $existingRows = DB::table('user_vehicle')->get()->map(fn ($row) => (array) $row)->all();

            Schema::disableForeignKeyConstraints();
            Schema::drop('user_vehicle');
            Schema::create('user_vehicle', function (Blueprint $table) {
                $table->id('id_user_vehicle');
                $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
                $table->foreignId('id_vehicle_masters')->constrained('vehicle_masters', 'id_vehicle_masters')->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            if (! empty($existingRows)) {
                $userPk = Schema::hasColumn('users', 'id_user') ? 'id_user' : 'id';
                $validUserIds = DB::table('users')->pluck($userPk)->all();
                $validVehicleIds = DB::table('vehicle_masters')->pluck('id_vehicle_masters')->all();
                $mapped = [];
                foreach ($existingRows as $row) {
                    $uId = $row['id_user'] ?? $row['user_id'] ?? null;
                    $vId = $row['id_vehicle_masters'] ?? $row['vehicle_id'] ?? null;
                    if (in_array($uId, $validUserIds, true) && in_array($vId, $validVehicleIds, true)) {
                        $mapped[] = [
                            'id_user' => $uId,
                            'id_vehicle_masters' => $vId,
                            'is_active' => $row['is_active'] ?? true,
                            'created_at' => $row['created_at'] ?? now(),
                            'updated_at' => $row['updated_at'] ?? now(),
                        ];
                    }
                }
                if (! empty($mapped)) {
                    DB::table('user_vehicle')->insert($mapped);
                }
            }

            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        // Tidak perlu mengembalikan foreign key ke nama tabel/kolom yang keliru.
    }
};
