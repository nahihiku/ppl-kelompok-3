<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMaster extends Model
{
    use HasFactory;

    protected $table = 'vehicle_masters';

    protected $primaryKey = 'id_vehicle_masters';

    protected $fillable = [
        'category',
        'brand',
        'model',
        'default_connector_type',
        'battery_capacity_kwh',
    ];
}
