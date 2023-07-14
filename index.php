<?php

$api_key = "0de4799ff4eab8891369637e70f9baad";
$city = "London";

$url = "http://api.openweathermap.org/geo/1.0/direct?q=$city&limit=5&appid=$api_key";

$cache = null;
$last_request_time = null;

function get_weather_data() {
    global $cache;
    global $last_request_time;
    global $url;

    if ($cache && $last_request_time && time() - $last_request_time < 20 * 60) {
        return $cache;
    } else {
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        $cache = $data;
        $last_request_time = time();
        return $data;
    }
}

$data = get_weather_data();

$weather_data_file = fopen('weather_data.csv', 'w');

fputcsv($weather_data_file, ['time', 'precipitation']);

foreach ($data["hourly"] as $hour) {
    $time = date('Y-m-d H:i:s', $hour["dt"]);
    $precipitation = isset($hour["rain"]["1h"]) ? $hour["rain"]["1h"] : 0;
    fputcsv($weather_data_file, [$time, $precipitation]);
}

fclose($weather_data_file);

?>