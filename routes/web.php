<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;

Route::get('/', function () {
    return view('weather.index');
});

Route::post('/get-weather', [CityController::class, 'GetWeather']);

require __DIR__.'/auth.php';
