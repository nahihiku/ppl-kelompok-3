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
        'source',
        'external_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'price_per_kwh' => 'float',
        ];
    }

    public static function isValidCoordinatePair(mixed $latitude, mixed $longitude): bool
    {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return false;
        }

        $lat = (float) $latitude;
        $lng = (float) $longitude;

        if (! is_finite($lat) || ! is_finite($lng)) {
            return false;
        }

        if (abs($lat) > 90 || abs($lng) > 180) {
            return false;
        }

        return ! ($lat === 0.0 && $lng === 0.0);
    }

    public function hasValidCoordinates(): bool
    {
        return self::isValidCoordinatePair($this->latitude, $this->longitude);
    }

    public static function haversineDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));

        return round($earthRadiusKm * $c, 2);
    }

    public function calculateDistance(float $userLatitude, float $userLongitude): float
    {
        if (! $this->hasValidCoordinates() || ! self::isValidCoordinatePair($userLatitude, $userLongitude)) {
            return 0.0;
        }

        return self::haversineDistanceKm($userLatitude, $userLongitude, (float) $this->latitude, (float) $this->longitude);
    }

    public function getDirectNavUrl(): ?string
    {
        if (! $this->hasValidCoordinates()) {
            return null;
        }

        return sprintf(
            'https://www.google.com/maps/dir/?api=1&destination=%s,%s',
            $this->latitude,
            $this->longitude
        );
    }

    public function getDirectNavUrlAttribute(): ?string
    {
        return $this->getDirectNavUrl();
    }

    public function connectors()
    {
        return $this->hasMany(StationConnector::class, 'station_id', 'id_stations');
    }

    public function reports()
    {
        return $this->hasMany(StationReport::class, 'id_stations', 'id_stations');
    }

    public function reviews()
    {
        return $this->hasMany(StationReview::class, 'id_stations', 'id_stations');
    }
}
