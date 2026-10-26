<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('station_reports', function (Blueprint $table) {
        $table->id('id_station_report');
        $table->unsignedBigInteger('id_stations');
        $table->string('connector_type', 50);
        $table->float('power_kw');
        $table->string('charging_speed_category', 50);
        $table->enum('status', ['normal', 'rusak', 'antre']);
        $table->text('notes')->nullable();
        $table->timestamp('reported_at')->useCurrent();
        $table->timestamps();

        // Foreign key ke tabel stations
        $table->foreign('id_stations')->references('id_stations')->on('stations')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_reports');
    }
};
