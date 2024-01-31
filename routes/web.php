<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;

Route::get('/', function () {
    return view('weather.index');
});

Route::post('/fetch-weather', [CityController::class, 'fetchWeather']);

require __DIR__.'/auth.php';
