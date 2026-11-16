<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable()->after('email');
                }
                if (! Schema::hasColumn('users', 'remember_token')) {
                    $table->rememberToken()->after('password');
                }
            });
        }

        if (Schema::hasTable('station_connectors')) {
            Schema::table('station_connectors', function (Blueprint $table) {
                if (! Schema::hasColumn('station_connectors', 'current_type')) {
                    $table->string('current_type')->default('DC')->after('charging_speed_category');
                }
                if (! Schema::hasColumn('station_connectors', 'available_connectors')) {
                    $table->unsignedInteger('available_connectors')->default(0)->after('current_type');
                }
                if (! Schema::hasColumn('station_connectors', 'total_connectors')) {
                    $table->unsignedInteger('total_connectors')->default(0)->after('available_connectors');
                }
            });

            if (
                Schema::hasColumn('station_connectors', 'id_stations')
                && Schema::hasColumn('station_connectors', 'connector_type')
                && ! Schema::hasIndex('station_connectors', ['id_stations', 'connector_type'])
            ) {
                Schema::table('station_connectors', function (Blueprint $table) {
                    $table->index(['id_stations', 'connector_type'], 'station_connectors_station_type_idx');
                });
            }
        }

        if (Schema::hasTable('station_reports')) {
            Schema::table('station_reports', function (Blueprint $table) {
                if (! Schema::hasColumn('station_reports', 'status')) {
                    $table->string('status', 30)->default('normal')->after('charging_speed_category');
                }
                if (! Schema::hasColumn('station_reports', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
                if (! Schema::hasColumn('station_reports', 'reported_at')) {
                    $table->timestamp('reported_at')->nullable()->after('notes');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('station_reports')) {
            Schema::table('station_reports', function (Blueprint $table) {
                foreach (['reported_at', 'notes', 'status'] as $column) {
                    if (Schema::hasColumn('station_reports', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('station_connectors')) {
            if (Schema::hasIndex('station_connectors', 'station_connectors_station_type_idx')) {
                Schema::table('station_connectors', function (Blueprint $table) {
                    $table->dropIndex('station_connectors_station_type_idx');
                });
            }

            Schema::table('station_connectors', function (Blueprint $table) {
                foreach (['total_connectors', 'available_connectors', 'current_type'] as $column) {
                    if (Schema::hasColumn('station_connectors', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                foreach (['remember_token', 'email_verified_at'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
