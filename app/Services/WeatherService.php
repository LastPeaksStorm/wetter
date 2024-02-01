<?php

    namespace App\Services;
    use App\Models\City;
    use App\Models\QueryHistory;
    use Illuminate\Support\Facades\Http;
    use App\Http\Requests\StoreCityRequest;
    use App\Http\Requests\UpdateCityRequest;
    use Illuminate\Http\Request;
    use Carbon\Carbon;
    use DateTime;

    class WeatherService
    {
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
      $queries = QueryHistory::all();
      return $queries;
    }

    public function ProtokolForecast($forecast) {
        QueryHistory::create($forecast);
    }

    public function GetRegionsTemperature() {
      $cities = City::all();

      $regionsInfo = [];
      foreach ($cities as $city) {
        $region = ($city->plz)[0];
        $regionsInfo[$region] = 0;
      }

      foreach ($regionsInfo as $key => &$value) {
        $sumTemper = 0;
        $count = 0;
        foreach($cities as $city){
          if(($city->plz)[0] == $key){
            $sumTemper += $city->temperature;
            $count++;
          }
        }

        $value = round($sumTemper / $count);
      }

      return $regionsInfo;
    }

      public function hourHasPassed(Carbon $datetime) {
        $currentTime = Carbon::now();

        if ($datetime->diffInMinutes($currentTime) < 60) {
            return false;
        } else {
            return true;
        }
      }


      public function cityNameFromPostalCode($plz) {
        // nicht so umfangreiche, aber kostenfreie API, um Stadtname durch PLZ zu bekommen.
        $response = Http::get("https://api.zippopotam.us/de/{$plz}");
        $cityInfo = json_decode($response->getBody(), true);

        if(!count($cityInfo)){
          throw new \Exception("Unsere API konnte Ihre Postleitzahl nicht finden. Bitte geben Sie eine Andere ein.");
        }
          $cityName = $cityInfo['places'][0]['place name'];
          return $cityName;
      }


      public function getWeatherForecast($plz) {
        $cityName = $this->cityNameFromPostalCode($plz);

        $apiKey = config('services.tomorrow_io.api_key');
        // Ihr API, um ducrh Stadtnamen Wetterinfo zu bekommen
        $response = Http::get("https://api.tomorrow.io/v4/weather/forecast?location=$cityName&apikey=$apiKey");
        $forecast = json_decode($response->getBody(), true);

          if(!count($forecast)) {
            throw new \Exception("Unsere API konnte Wetterinfos für Ihre Stadt nicht finden. Bitte geben Sie eine Andere ein.");
          }

        $filteredForecast = [
          'plz' => $plz,
          'name' => $forecast['location']['name'], 
          'temperature' => (int)$forecast['timelines']['daily'][0]['values']['temperatureAvg'], 
          'humidity' => (int)$forecast['timelines']['daily'][0]['values']['humidityAvg'], 
          'wind_speed' => (int)$forecast['timelines']['daily'][0]['values']['windSpeedAvg'],
        ];

        return $filteredForecast;
      }
    }