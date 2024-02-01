<?php

    namespace App\Http\Controllers;
    use App\Http\Requests\StoreCityRequest;
    use App\Http\Requests\UpdateCityRequest;
    use Illuminate\Http\Request;
    use App\Services\WeatherService;

    class CityController extends Controller
    {
        private $weatherService;

        public function __construct(WeatherService $weatherService) {
            $this->weatherService = $weatherService;
        }

        public function GetWeather(Request $request)
        {
            $plz = (int)$request->input('plz');
            
            $forecast = $this->weatherService->FetchForecast($plz);
            
            return compact('forecast');
        } 
    }
