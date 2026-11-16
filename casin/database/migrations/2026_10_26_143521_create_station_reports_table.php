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
        $table->foreignId('id_stations')->constrained('stations', 'id_stations')->onDelete('cascade');
        $table->foreignId('id_connector')->constrained('connectors', 'id_connector')->onDelete('cascade');
        $table->string('report_category');
        $table->text('description');
        $table->string('photo_evidence')->nullable();
        $table->enum('status', ['pending', 'reviewed', 'resolved', 'rejected'])->default('pending');
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
