<?php

namespace App\Http\Controllers;
use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function fetchWeather(Request $request)
    {
        $plz = $request->input('city');
        

        $response = Http::get("https://api.zippopotam.us/de/{$plz}");

        $data = json_decode($response->getBody(), true);

        $city = $data['places'][0]['place name'];

        // $apiKey = config('services.tomorrow_io.api_key');
        $apiKey = 'l1LnN9sX1FnoO9c7h1YSx0rTzHSByepc';

        $response = Http::get("https://api.tomorrow.io/v4/weather/forecast?location=$city&apikey=$apiKey");

        $data = json_decode($response->getBody(), true);

        $apiData = [];

        $apiData = [
                'plz' => $plz,
                'name' => $data['location']['name'],
                'temperature' => $data['timelines']['daily'][0]['values']['temperatureAvg'],
                'humidity' => $data['timelines']['daily'][0]['values']['humidityAvg'],
                'wind_speed' => $data['timelines']['daily'][0]['values']['windSpeedAvg'],
            ];

        // City::create($apiData);

        return compact('apiData');
    }

    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        //
    }
}
