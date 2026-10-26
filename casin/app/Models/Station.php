<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $table = 'stations';

    protected $primaryKey = 'id_stations';

    protected $fillable = [
        'name',
        'operator_type',
        'address',
        'latitude',
        'longitude',
        'operating_hours',
        'price_per_kwh',
        'require_app_name',
        'current_status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'price_per_kwh' => 'float',
        ];
    }

    public function getDirectNavUrlAttribute(): string
    {
        return sprintf(
            'https://www.google.com/maps/dir/?api=1&destination=%s,%s',
            $this->latitude,
            $this->longitude
        );
    }
}
