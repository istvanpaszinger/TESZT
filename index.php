<?php

$api_key = "574b13d8f9c4c74b7cc7f92b9909c6d7";
$city = "London";

$geo_url = "http://api.openweathermap.org/geo/1.0/direct?q=$city&limit=5&appid=$api_key";
$cache = null;
$last_request_time = null;

function get_weather_data() {
    global $cache;
    global $last_request_time;
    global $geo_url;
    global $api_key;

    if ($cache && $last_request_time && time() - $last_request_time < 20 * 60) {
        return $cache;
    } else {
        $geo_response = file_get_contents($geo_url);
        $geo_data = json_decode($geo_response, true);
        $lat = $geo_data[0]["lat"];
        $lon = $geo_data[0]["lon"];

        $weather_url = "https://api.openweathermap.org/data/3.0/onecall?lat=$lat&lon=$lon&exclude=minutely,daily&appid=$api_key";
        $weather_response = file_get_contents($weather_url);
        $weather_data = json_decode($weather_response, true);

        $cache = $weather_data;
        $last_request_time = time();
        return $weather_data;
    }
}

$data = get_weather_data();
//echo '<pre>'.print_r($data, true);
$weather_data_file = fopen('weather_data.csv', 'w');
fputcsv($weather_data_file, ['time', 'precipitation']);

foreach($data["hourly"] as $datas){
$time = date('Y-m-d H:i:s', $datas["dt"]);
$precipitation = isset($datas["rain"]["1h"]) ? $datas["rain"]["1h"] : 0;
fputcsv($weather_data_file, [$time, $precipitation]);
}
fclose($weather_data_file);

?>