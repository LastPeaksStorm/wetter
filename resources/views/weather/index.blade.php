<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

    <h1>Weather App</h1>

    <form id="weatherForm" method="post">
        @csrf
        <label for="city">Enter City Name:</label>
        <input type="text" id="plz" name="plz" required>
        <button type="button" onclick="getWeather()">Get Weather</button>
    </form>
    <hr></hr>

    <div id="weatherContainer">
        <p id="cityName"></p>
        <p id="temperature"></p>
        <p id="humidity"></p>
        <p id="wind_speed"></p>
    </div>

    <script>
        function getWeather() {
            var formData = $('#weatherForm').serialize();
                $.ajax({
                    type: 'POST',
                    url: '{{ url('/get-weather') }}',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        $('#cityName').html('City: ' + response.forecast.name);
                        $('#temperature').html('Avg. Temperature: ' + response.forecast.temperature + '°C');
                        $('#humidity').html('Avg. Humidity: ' + response.forecast.humidity + '%');
                        $('#wind_speed').html('Avg. Wind speed: ' + response.forecast.wind_speed + ' mph');
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
        }
    </script>

</body>
</html>
