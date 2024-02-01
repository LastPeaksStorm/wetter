<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    <nav class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/statistics') }}">Statistics</a>
    </nav>

    <div class="hero">
        <h1 class="appTitle">Weather App</h1>
        <hr/>

        <form id="weatherForm" method="post">
            @csrf
            <label for="city" id="formLabel">Enter City Name:</label>
            <input type="text" id="plz" name="plz" required>
            <button type="button" onclick="getWeather()">Get Weather</button>
        </form>
        <hr/>

        <div id="weatherContainer">
            <h3>Weather Information: </h3>
            <p id="cityName"></p>
            <p id="temperature"></p>
            <p id="humidity"></p>
            <p id="wind_speed"></p>
        </div>
    </div>


</body>
<div class="marginal">
    <hr/>
    <h3>Query History: </h3>
    <div id="queryList"></div>
</div>

<script>
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: "numeric",
        minute: "numeric",
        second: "numeric"
    };

    $(document).ready(function () {
        getHistory();
    });

    // leider funktionierte diese relevantere Methode nicht fehlerlos
    // $(document).on('change', function() {
    //     getHistory();
    // });

    function getWeather() {
        var formData = $('#weatherForm').serialize();
        $.ajax({
            type: 'GET',
            url: '{{ url('/get-weather') }}',
            data: formData,
            dataType: 'json',
            success: function (response) {
                $('#weatherContainer p').html('');
                if (response.serviceResponse.success) {
                    let forecast = response.serviceResponse.data;
                    $('#cityName').html('<b>City: </b>' + forecast.name);
                    $('#temperature').html('<b>Avg. Temperature: </b>' + forecast.temperature + '°C');
                    $('#humidity').html('<b>Avg. Humidity: </b>' + forecast.humidity + '%');
                    $('#wind_speed').html('<b>Avg. Wind speed: </b>' + forecast.wind_speed + ' mph');
                    getHistory();
                } else {
                    $("#weatherContainer").append('<p>' + response.serviceResponse.message + '</p>');
                }
            },
            error: function (error) {
                $("#weatherContainer").append('<p>Problem beim Schildern der Daten</p>');
            }
        });
    }

    function getHistory() {
        $.ajax({
            type: 'GET',
            url: '{{ url('/get-history') }}',
            dataType: 'json',
            success: function (response) {
                $('#queryList').html('');
                if (response.serviceResponse.success) {
                    $.each(response.serviceResponse.data, function (index, query) {
                        let date = new Date(query.created_at);
                        date = date.toLocaleString('de-DE', options);

                        $('#queryList').append('<span><b>' + query.id + '. PLZ: </b>' + query.plz + ' - <b>Time:</b> ' + date + '</span><button class="button-small" onclick="RepeatQuery(' + query.plz + ')">Repeat</button><br/>')
                    });
                } else {
                    $("#queryList").append('<p>' + response.serviceResponse.message + '</p>');
                }
            },
            error: function (error) {
                $("#queryList").append('<p>Problem beim Schildern der Daten</p>');
            }
        });
    }

    function RepeatQuery(plz) {
        plzString = plz.toString()
        plzString = '0'.repeat(5 - plzString.length) + plzString;

        document.getElementById('plz').value = plzString;
        getWeather();

        //
        getHistory();
    }
</script>

</html>