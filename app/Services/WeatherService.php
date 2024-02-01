<?php

    namespace App\Services;
    use App\Models\City;
    use App\Models\QueryHistory;
    use Illuminate\Support\Facades\Http;
    use App\Http\Requests\StoreCityRequest;
    use App\Http\Requests\UpdateCityRequest;
    use Illuminate\Http\Request;
    use Carbon\Carbon;

    class WeatherService
    {
      public function hourHasPassed(Carbon $datetime) {
        $currentTime = Carbon::now();

        if ($datetime->diffInMinutes($currentTime) < 60) {
            return false;
        } else {
            return true;
        }
      }

      public function cityNameFromPostalCode($plz) {
          $response = Http::get("https://api.zippopotam.us/de/{$plz}");
          $cityInfo = json_decode($response->getBody(), true);
          $cityName = $cityInfo['places'][0]['place name'];
          
          return $cityName;
      }


      public function getWeatherForecast($plz) {
          $cityName = $this->cityNameFromPostalCode($plz);
          $apiKey = config('services.tomorrow_io.api_key');
          $response = Http::get("https://api.tomorrow.io/v4/weather/forecast?location=$cityName&apikey=$apiKey");
          $forecast = json_decode($response->getBody(), true);

          $filteredForecast = [
            'plz' => $plz,
            'name' => $forecast['location']['name'], 
            'temperature' => intval($forecast['timelines']['daily'][0]['values']['temperatureAvg']), 
            'humidity' => intval($forecast['timelines']['daily'][0]['values']['humidityAvg']), 
            'wind_speed' => intval($forecast['timelines']['daily'][0]['values']['windSpeedAvg']),
          ];

          return $filteredForecast;
      }


      public function FetchForecast($plz) {
          $city = City::where('plz', $plz)->latest()->first();

          if ($city && !$this->hourHasPassed(Carbon::parse($city->updated_at))) {
              $newForecast = [
                  'plz' => $plz, 
                  'name' => $city->name, 
                  'temperature' => $city->temperature, 
                  'humidity' => $city->humidity, 
                  'wind_speed' => $city->wind_speed,
              ];
          } 
          else {
              $newForecast = $this->getWeatherForecast($plz);

              if(!$city){
                City::create($newForecast);
              } else {
                  $city->plz = $newForecast['plz'];
                  $city->name = $newForecast['name'];
                  $city->temperature = $newForecast['temperature'];
                  $city->humidity = $newForecast['humidity'];
                  $city->wind_speed = $newForecast['wind_speed'];
      
                  $city->save();
              }
          }
          
          $this->ProtokolForecast($newForecast);
          return $newForecast;
      }

      public function GetAllQueries() {
        return QueryHistory::all();
      }

      public function ProtokolForecast($forecast) {
          QueryHistory::create($forecast);
      }
    }