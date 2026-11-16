<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $table = 'operators';

    protected $primaryKey = 'id_operators';

    protected $fillable = [
        'name',
        'logo_url',
        'official_app_name',
    ];
}

