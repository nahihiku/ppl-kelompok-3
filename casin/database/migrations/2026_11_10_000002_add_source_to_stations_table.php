<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan penanda sumber data stasiun (manual, osm, pln) dan ID eksternal
     * agar proses impor dapat diulang tanpa menghasilkan data ganda.
     */
    public function up(): void
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->after('current_status');
            $table->string('external_id', 100)->nullable()->after('source');

            $table->index(['source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropIndex(['source', 'external_id']);
            $table->dropColumn(['source', 'external_id']);
        });
    }
};
