<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_connectors', function (Blueprint $table) {
            $table->id('id_station_connectors');
            $table->foreignId('station_id')
                ->constrained('stations', 'id_stations')
                ->cascadeOnDelete();
            $table->string('connector_type');
            $table->decimal('power_kw', 8, 2);
            $table->string('charging_speed_category');
            $table->string('current_type')->default('DC');
            $table->unsignedInteger('available_connectors')->default(0);
            $table->unsignedInteger('total_connectors')->default(0);
            $table->timestamps();

            $table->index(['station_id', 'connector_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_connectors');
    }
};
