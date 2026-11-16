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
        $table->string('category');
        $table->string('brand');
        $table->string('model');
        $table->foreignId('id_connector')->constrained('connectors', 'id_connector')->onDelete('cascade');
        $table->decimal('battery_capacity_kwh', 8, 2);
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
