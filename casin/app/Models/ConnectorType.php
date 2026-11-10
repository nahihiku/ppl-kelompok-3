<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectorType extends Model
{
    use HasFactory;

    protected $table = 'connector_types';

    protected $primaryKey = 'id_connector_type';

    protected $fillable = [
        'name',
        'current_type',
    ];
}

