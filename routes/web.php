<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;

Route::get('/', function () {
    return view('weather.index');
});

Route::get('/statistics', function () {
    return view('weather.statistics');
});

Route::get('/get-weather', [CityController::class, 'GetWeather']);
Route::get('/get-history', [CityController::class, 'GetHistory']);

require __DIR__.'/auth.php';
