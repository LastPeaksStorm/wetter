<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ url('/statistics') }}">Statistics</a></li>
        </ul>
    </nav>

    <h1>Statistics: </h1>
    <div id="statisticsList"></div>

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
                        console.log(response.serviceResponse.success);
                        $.each(response.serviceResponse.data, function (index, temperature) {
                            $("#statisticsList").append('<p><b>' + index + ':</b> ' + temperature + '°C</p>');
                        });
                    } else {
                        console.log(response.serviceResponse.success);
                        $("#statisticsList").append('<p>' + response.serviceResponse.message + '</p>');
                    }
                },
                error: function(error){
                    $("#statisticsList").append('<p>Problem beim Schildern der Daten</p>');
                }
            });
        }
    </script>
</html>
