import requests
import json
import csv
from datetime import datetime, timedelta

api_key = "0de4799ff4eab8891369637e70f9baad"
city = "London"

url = f"https://api.openweathermap.org/data/2.5/onecall?q={city}&limit=5&exclude=minutely,dailys&appid={api_key}"

cache = None
last_request_time = None

def get_weather_data():
    global cache
    global last_request_time

    if cache and last_request_time and datetime.now() - last_request_time < timedelta(minutes=20):
        return cache
    else:
        response = requests.get(url)
        data = json.loads(response.text)
        cache = data
        last_request_time = datetime.now()
        return data

data = get_weather_data()

with open('weather_data.csv', mode='w') as weather_data_file:
    fieldnames = ['time', 'precipitation']
    writer = csv.DictWriter(weather_data_file, fieldnames=fieldnames)

    writer.writeheader()

    for hour in data["hourly"]:
        time = datetime.fromtimestamp(hour["dt"]).strftime('%Y-%m-%d %H:%M:%S')
        precipitation = hour.get("rain", {}).get("1h", 0)
        writer.writerow({'time': time, 'precipitation': precipitation})
		
		