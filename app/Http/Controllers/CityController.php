<?php

    namespace App\Http\Controllers;
    use App\Http\Requests\StoreCityRequest;
    use App\Http\Requests\UpdateCityRequest;
    use Illuminate\Http\Request;
    use App\Services\WeatherService;
    use App\ServiceResponse;

    class CityController extends Controller
    {
        private $weatherService;

        public function __construct(WeatherService $weatherService) {
            $this->weatherService = $weatherService;
        }

        public function GetWeather(Request $request)
        {
            $serviceResponse = new ServiceResponse();
            $plz = $request->input('plz');
            
            try {
                $forecast = $this->weatherService->FetchForecast($plz);
                $serviceResponse->data = $forecast;
            } catch (\Throwable $ex) {
                $serviceResponse->success = false;
                $serviceResponse->message = $ex->getMessage();
            } finally {
                return compact('serviceResponse');
            }
        } 

        public function GetHistory(Request $request)
        {
            $serviceResponse = new ServiceResponse();

            try{
                $queries = $this->weatherService->GetAllQueries();
                $serviceResponse->data = $queries;
            }
            catch (\Throwable $ex) {
                $serviceResponse->success = false;
                $serviceResponse->message = $ex->getMessage();
            }
            finally {
                return compact('serviceResponse');
            }
        }

        public function GetStatistics(Request $request)
        {
            $serviceResponse = new ServiceResponse();

            try {
                $statistics = $this->weatherService->GetRegionsTemperature();
                $serviceResponse->data = $statistics;
            }
            catch (\Throwable $ex) {
                $serviceResponse->success = false;
                $serviceResponse->message = $ex->getMessage();
            }
            finally {
                return compact('serviceResponse');
            }
        }
    }
