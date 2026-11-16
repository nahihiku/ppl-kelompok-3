<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_connectors', function (Blueprint $table) {
            $table->id('id_station_connector');
            $table->foreignId('id_stations')->constrained('stations', 'id_stations')->onDelete('cascade');
            $table->foreignId('id_connector')->constrained('connectors', 'id_connector')->onDelete('cascade');
            $table->decimal('power_kw', 8, 2);
            $table->string('charging_speed_category');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_connectors');
    }
};
