<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetCityDTO extends Model
{
    public readonly int $plz;
    public readonly string $name;
    public readonly string $temperature;
    public readonly string $humidity;
    public readonly string $wind_speed;
    
    public function __construct(int $plz, string $name, string $temperature, string $humidity, string $wind_speed) {
        $this->plz = $plz;
        $this->name = $name;
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->wind_speed = $wind_speed;
    }

    use HasFactory;
}