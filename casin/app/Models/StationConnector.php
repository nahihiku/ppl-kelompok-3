<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationConnector extends Model
{
    use HasFactory;

    protected $table = 'station_connectors';

    protected $primaryKey = 'id_station_connector';

    protected $fillable = [
        'id_stations',
        'connector_type',
        'power_kw',
        'charging_speed_category',
        'current_type',
        'available_connectors',
        'total_connectors',
    ];

    protected function casts(): array
    {
        return [
            'power_kw' => 'float',
            'available_connectors' => 'integer',
            'total_connectors' => 'integer',
        ];
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'id_stations', 'id_stations');
    }
}
