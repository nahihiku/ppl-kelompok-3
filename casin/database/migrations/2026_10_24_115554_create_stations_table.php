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
        Schema::create('stations', function (Blueprint $table) {
            $table->id('id_stations');
            $table->string('name');
            $table->string('operator_type'); // government (PLN/BUMN) | private | commercial
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('operating_hours');
            $table->decimal('price_per_kwh', 8, 2)->nullable();
            $table->string('require_app_name')->nullable();
            $table->string('current_status')->default('normal'); // normal | technical_issue | busy
            $table->timestamps();

            $table->index(['operator_type', 'current_status']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
