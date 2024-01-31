<?php

namespace App\Http\Controllers;
use App\Models\City;
use App\Models\SetCityDTO;
use App\Models\QueryHistory;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CityController extends Controller
{
    public function GetWeather(Request $request)
    {
        $plz = (int)$request->input('plz');
        
        $forecast = get_object_vars($this->ProcessForecast($plz));
        
        return compact('forecast');
    }


    public function hourHasPassed(Carbon $datetime) {
        $currentTime = Carbon::now();

        if ($datetime->diffInMinutes($currentTime) < 60) {
            return false;
        } else {
            return true;
        }
     }

     public function cityNameFromPostalCode(int $plz) {
        $response = Http::get("https://api.zippopotam.us/de/{$plz}");

            $forecast = json_decode($response->getBody(), true);

            $cityName = $forecast['places'][0]['place name'];

            return $cityName;
     }


     public function getWeatherForecast($plz) {
        $cityName = $this->cityNameFromPostalCode($plz);

            $apiKey = config('services.tomorrow_io.api_key');

            $response = Http::get("https://api.tomorrow.io/v4/weather/forecast?location=$cityName&apikey=$apiKey");

            $forecast = json_decode($response->getBody(), true);

            return $forecast;
     }


     public function ProcessForecast($plz) {
        $city = City::where('plz', $plz)->latest()->first();

        if ($city && !$this->hourHasPassed(Carbon::parse($city->updated_at))) {
            $newForecast = new SetCityDTO(
                $plz, 
                $city->name, 
                $city->temperature, 
                $city->humidity, 
                $city->wind_speed,
            );
        } else {
            $forecast = $this->getWeatherForecast($plz);

            $newForecast = new SetCityDTO(
                $plz, 
                $forecast['location']['name'], 
                $forecast['timelines']['daily'][0]['values']['temperatureAvg'], 
                $forecast['timelines']['daily'][0]['values']['humidityAvg'], 
                $forecast['timelines']['daily'][0]['values']['windSpeedAvg'],
            );
    
            if(!$city){
                City::create(get_object_vars($newForecast));
            } else {
                $city->plz = $newForecast->plz;
                $city->name = $newForecast->name;
                $city->temperature = $newForecast->temperature;
                $city->humidity = $newForecast->humidity;
                $city->wind_speed = $newForecast->wind_speed;
    
                $city->save();
            }
        }

        $this->ProtokolForecast(get_object_vars($newForecast));
        return $newForecast;
     }


     public function ProtokolForecast($forecast) {
        QueryHistory::create($forecast);
     }
}
