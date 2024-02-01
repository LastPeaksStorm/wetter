<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <nav class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/statistics') }}" class="disabled">Statistics</a>
    </nav>

    <div class="hero">
        <h2>Statistics: </h2>
        <hr/>
        <div id="statisticsList"><p>Waiting...</p></div>
    </div>

    <script>
        $(document).ready(function() {
            GetStatistics();
        });

        function GetStatistics() {
            $.ajax({
                type: 'GET',
                url: '{{ url('/get-statistics') }}',
                dataType: 'json',
                success: function(response){
                    if(response.serviceResponse.success) {
                        if(response.serviceResponse.data){
                            $("#statisticsList").html('');
                            $.each(response.serviceResponse.data, function (index, temperature) {
                                $("#statisticsList").append('<p><b>Region ' + index + ':</b> ' + temperature + '°C</p>');
                            });
                        }
                        else {
                            $("#statisticsList").html('<p>Nothing to display yet.</p>');
                        }
                    } else {
                        $("#statisticsList").html('<p>' + response.serviceResponse.message + '</p>');
                    }
                },
                error: function(error){
                    $("#statisticsList").html('<p>Problem beim Schildern der Daten</p>');
                }
            });
        }
    </script>
</html>
