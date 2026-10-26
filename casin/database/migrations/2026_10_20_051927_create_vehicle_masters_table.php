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
        Schema::create('vehicle_masters', function (Blueprint $table) {
            $table->id('id_vehicle_masters');
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->string('connector_type', 50);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vehicle');
    }
};
