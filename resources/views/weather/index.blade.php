<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ url('/statistics') }}">Statistics</a></li>
        </ul>
    </nav>

    <h1>Weather App</h1>

    <form id="weatherForm" method="post">
        @csrf
        <label for="city">Enter City Name:</label>
        <input type="text" id="plz" name="plz" required>
        <button type="button" onclick="getWeather()">Get Weather</button>
    </form>
    <hr></hr>

    <div id="weatherContainer">
        <h3>Weather Information: </h3>
        <p id="cityName"></p>
        <p id="temperature"></p>
        <p id="humidity"></p>
        <p id="wind_speed"></p>
    </div>

    
</body>
<footer>
    <hr></hr>
        <h3>Query History: </h3>
        <div id="queryList"></div>
</footer>

    <script>
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric', 
            hour: "numeric", 
            minute: "numeric", 
            second: "numeric" 
        };

        $(document).ready(function() {
            getHistory();
        });

        // leider funktionierte diese relevantere Methode nicht fehlerlos
        // $(document).on('change', function() {
        //     getHistory();
        // });



        $(document).on('change', function() {
            
        });

        function getWeather() {
            var formData = $('#weatherForm').serialize();
                $.ajax({
                    type: 'GET',
                    url: '{{ url('/get-weather') }}',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        $('#cityName').html('City: ' + response.forecast.name);
                        $('#temperature').html('Avg. Temperature: ' + response.forecast.temperature + '°C');
                        $('#humidity').html('Avg. Humidity: ' + response.forecast.humidity + '%');
                        $('#wind_speed').html('Avg. Wind speed: ' + response.forecast.wind_speed + ' mph');

                        //
                        getHistory();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
        }

        function getHistory() {
            $.ajax({
                type: 'GET',
                url: '{{ url('/get-history') }}',
                dataType: 'json',
                success: function(response) {
                    $('#queryList').html('');
                    $.each(response.queries, function(index, query) {
                        let date = new Date(query.created_at);
                        date = date.toLocaleString('de-DE', options);

                        $('#queryList').append('<span><b>' + query.id + '. PLZ: </b>' + query.plz + ' - <b>Time:</b> ' + date + '</span><button onclick="RepeatQuery(' + query.plz + ')">Wiederholen</button><br/>')
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function RepeatQuery(plz) {
                document.getElementById('plz').value = plz;
                getWeather();

                //
                getHistory();
        }
    </script>

</html>
