<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationReport extends Model
{
    use HasFactory;

    protected $table = 'station_reports';

    protected $primaryKey = 'id_station_report';

    protected $fillable = [
        'id_stations',
        'connector_type',
        'power_kw',
        'charging_speed_category',
        'status',
        'notes',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'power_kw' => 'float',
            'reported_at' => 'datetime',
        ];
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'id_stations', 'id_stations');
    }
}

