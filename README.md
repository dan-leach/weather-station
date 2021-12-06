# weather-station
Panborough raspberry pi weather station

Based on the Raspberry Pi Oracle and the BC Robotics weather-stations

https://projects.raspberrypi.org/en/projects/build-your-own-weather-station
https://bc-robotics.com/tutorials/raspberry-pi-weather-station-part-1

Supports air T, ground T, pressure, humidity, wind speed, wind direction, rainfall

weather-station.py is started on power-up from the /etc/rc.local file. This means it is run as user root and is a background process, with STDOUT and STDERR appended to 'logfile'.

cd /home/pi/weather-station
nohup python3 -u weather_station.py >> logfile 2>&1 &

(Note that the -u parameter is essential to stop buffering).
In order to stop the backround process, open a terminal, use 'ps -u root', search for the line with 'python3' and note the PID. Then execute 'sudo kill PID' 


Errors (such as cable disconnection or sensor failure ) result in 'N/A' being sent to the server
Errors are logged in a local file call 'logfile'. I haven't found any way to push this to the internet.
There is a 'try' 'except' structure around the main loop which should catch any run-time errors, but not
timeouts.

 

