# Panborough Weather Station

*A weather monitoring tool using a Raspberry Pi weather station. Includes an API (PHP/SQL) for data logging and retrieval and a web application client for viewing live, historical and trend data.*
> Based on the Raspberry Pi Oracle and the BC Robotics weather-stations:
> https://projects.raspberrypi.org/en/projects/build-your-own-weather-station
> https://bc-robotics.com/tutorials/raspberry-pi-weather-station-part-1

This repo is for the API and browser client. For the station code see: https://github.com/mike-uren/weather-station

## API

Requires a key for all requests that modify data including `INSERT`, `DELETE` and remotely clearing error logs.
 
#### Paths

> `GET` api/select/latest/

Returns most recently received weather data as JSON:

```json
{ 
    "latest": { //most recently received data from weather station
        "datetime": "string", //datetime of weather data in format: YYYY-MM-DD HH:MM:SS
        "wind_speed": "float",
        "gust_speed": "float",
        "wind_direction": "float",
        "ambient_temp": "float",
        "ground_temp": "float", 
        "humidity": "float",
        "pressure": "float"
    },
    "cum": { // data that requires summing of data points over a time period
        "rainfall": "float", //total rainfall over given period, currently set to 60 mins, to evenout granular nature of this data
    },
    "minMax": { // selects the minimum and maximum values for each variable since midnight
        "ambient_temp": {
            "min": "float",
            "max": "float"
        },
        "ground_temp": {
            "min": "float",
            "max": "float"
        },
        "pressure": {
            "min": "float",
            "max": "float"
        },
        "humidity": {
            "min": "float",
            "max": "float"
        },
        "gust_speed": { // gust speed does not return a minimum for the day
            "max": "float"
        }
    },
    "error": "string" // returns a string containing an error if any occur during scripts generating the variables above
}
```
> `GET` api/select/all/

Returns all received weather data as HTML document

> `GET` api/insert/?

Parameters:
- {key} string - must be the correct API key or will return "incorrect key"
- {version}, {comment}, {wind_speed}, {gust_speed}, {wind_direction}, {rainfall}, {ambient_temp}, {ground_temp}, {humidity}, {pressure}

Inserts variables into SQL database and return success message or error.

> `GET` api/error/check

Returns any errors as JSON:

```JSON
{ 
    "client": "string",
    "server": "string",
    "station": "string"
}
```
Error strings are zero length if no errors, base64 encoded if errors found. Server errors may contain content from multiple files

> `GET` api/error/new

Parameters:
- {source} string - the source of the error (client/station)
- {stack} string - the error stack to be logged

Records errors in error file and returns success message.

> `GET` api/error/clear

Parameters:
- {key} string - must be the correct API key or will return "incorrect key"
- {source} string - e.g. server/station/client

Clears the error file and retusn success message or error.

> `GET` api/delete/all

Parameters:
-{key} string - must be the correct API key or will return "incorrect key"

Deletes all weather data from database and returns success message.

## Client
Uses ES6 standards - not IE compatible.
Locally installed dependencies:
- Moment.js

CDN dependencies:
- Bootstrap 5
- Sweet alert 2

