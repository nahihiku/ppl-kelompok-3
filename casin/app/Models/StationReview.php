<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationReview extends Model
{
    use HasFactory;

    protected $table = 'station_reviews';

    protected $primaryKey = 'id_station_review';

    protected $fillable = [
        'id_stations',
        'id_user',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'id_stations', 'id_stations');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

