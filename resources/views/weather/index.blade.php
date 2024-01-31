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
        <input type="text" id="city" name="city" required>
        <button type="button" onclick="fetchWeather()">Get Weather</button>
    </form>

    <div id="weatherContainer"></div>

    <script>
        function fetchWeather() {
            var formData = $('#weatherForm').serialize();

            $.ajax({
                type: 'POST',
                url: '{{ url('/fetch-weather') }}',
                data: formData,
                success: function(data) {
                    $('#weatherContainer').html(data['apiData']);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>

</body>
</html>
