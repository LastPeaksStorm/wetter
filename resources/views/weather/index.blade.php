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
            <button type="button" id="weatherButton" onclick="getWeather()">Get Weather</button>
        </form>
        <hr/>

        <h3>Weather Information: </h3>
        <div id="weatherContainer">
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
    <div id="queryList"><p>Waiting...</p></div>
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

    document.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        document.getElementById("weatherButton").click();
      }
    });

    // leider funktionierte diese relevantere Methode nicht fehlerlos
    // $(document).on('change', function() {
    //     getHistory();
    // });

    function getWeather() {
        var formData = $('#weatherForm').serialize();
        if (formData.split('plz=')[1]) {
            $.ajax({
                type: 'GET',
                url: '{{ url('/get-weather') }}',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    $('#weatherContainer p').html('');
                    if (response.serviceResponse.success) {
                        let forecast = response.serviceResponse.data;
                        console.log(forecast.name);
                        $('#cityName').html('<b>City: </b>' + forecast.name);
                        $('#temperature').html('<b>Avg. Temperature: </b>' + forecast.temperature + '°C');
                        $('#humidity').html('<b>Avg. Humidity: </b>' + forecast.humidity + '%');
                        $('#wind_speed').html('<b>Avg. Wind speed: </b>' + forecast.wind_speed + ' mph');
                        getHistory();
                    } else {
                        $("#weatherContainer").html('<p>' + response.serviceResponse.message + '</p>');
                    }
                },
                error: function (error) {
                    $("#weatherContainer").html('<p>Problem beim Schildern der Daten</p>');
                }
            });
        }
        else {
            $("#weatherContainer").html('<p>PLZ field cannot be empty!</p>');
        }
    }

    function getHistory() {
        $.ajax({
            type: 'GET',
            url: '{{ url('/get-history') }}',
            dataType: 'json',
            success: function (response) {
                if (response.serviceResponse.success) {
                    if(response.serviceResponse.data){
                        $("#queryList").html('');
                        $.each(response.serviceResponse.data, function (index, query) {
                            let date = new Date(query.created_at);
                            date = date.toLocaleString('de-DE', options);

                            $('#queryList').append('<span><b>' + query.id + '. PLZ: </b>' + query.plz + ' - <b>Time:</b> ' + date + '</span><button class="button-small" onclick="RepeatQuery(' + query.plz + ')">Repeat</button><br/>')
                        });
                    }
                    else {
                        $("#queryList").html('<p>Nothing to display yet.</p>');
                    }
                } else {
                    $("#queryList").html('<p>' + response.serviceResponse.message + '</p>');
                }
            },
            error: function (error) {
                $("#queryList").html('<p>Problem with rendering of the query history</p>');
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