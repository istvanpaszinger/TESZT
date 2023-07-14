import requests
import json
import csv
from datetime import datetime, timedelta

api_key = "574b13d8f9c4c74b7cc7f92b9909c6d7"
city = "London"

geo_url = f'http://api.openweathermap.org/geo/1.0/direct?q={city}&limit=5&appid={api_key}'
cache = None
last_request_time = None

def get_weather_data():
    global cache
    global last_request_time

    if cache and last_request_time and datetime.now() - last_request_time < timedelta(minutes=20):
        return cache
    else:
        geo_response = requests.get(geo_url)
        geo_data = json.loads(geo_response.text)
        
        lat = geo_data[0]["lat"]
        lon = geo_data[0]["lon"]

        weather_url = f'https://api.openweathermap.org/data/3.0/onecall?lat={lat}&lon={lon}&exclude=minutely,daily&appid={api_key}'
        weather_response = requests.get(weather_url)
        weather_data = json.loads(weather_response.text)

        cache = weather_data
        last_request_time = datetime.now()
        return weather_data

data = get_weather_data()

with open('weather_data_py.csv', mode='w') as weather_data_file:
    fieldnames = ['time', 'precipitation']
    writer = csv.DictWriter(weather_data_file, fieldnames=fieldnames)

    writer.writeheader()

    for hour in data["hourly"]:
        time = datetime.fromtimestamp(hour["dt"]).strftime('%Y-%m-%d %H:%M:%S')
        precipitation = hour.get("rain", {}).get("1h", 0)
        writer.writerow({'time': time, 'precipitation': precipitation})
        print({'time': time, 'precipitation': precipitation})