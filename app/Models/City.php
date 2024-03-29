<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'plz',
        'name',
        'temperature',
        'humidity',
        'wind_speed',
        'updated_at'
    ];

    use HasFactory;
}
