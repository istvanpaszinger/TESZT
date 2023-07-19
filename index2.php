<?php

$api_key = "574b13d8f9c4c74b7cc7f92b9909c6d7";
$city = "London";
$cache_file = 'cache.txt';

$geo_url = "http://api.openweathermap.org/geo/1.0/direct?q=$city&limit=5&appid=$api_key";

function get_weather_data() {
    global $geo_url;
    global $api_key;
    global $cache_file;

    if (file_exists($cache_file) && time() - filemtime($cache_file) < 20 * 60) {
        return json_decode(file_get_contents($cache_file), true);
    } else {
        $geo_response = file_get_contents($geo_url);
        $geo_data = json_decode($geo_response, true);
        $lat = $geo_data[0]["lat"];
        $lon = $geo_data[0]["lon"];

        $weather_url = "https://api.openweathermap.org/data/3.0/onecall?lat=$lat&lon=$lon&exclude=minutely,daily&appid=$api_key";
        $weather_response = file_get_contents($weather_url);
        $weather_data = json_decode($weather_response, true);

        file_put_contents($cache_file, json_encode($weather_data));
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
