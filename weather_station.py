#!/usr/bin/python3
# 0.1 first operational draft
# 0.2 error trapping implemented ('N/A' for disconnected sensors)
from gpiozero import Button
import time
import math
import statistics
import bme280
import smbus2
from gpiozero import MCP3008
from time import strftime

import ds18b20_therm
import CPU_temp

import http.client
import os

# --------------------------------------------------------
# Wind direction

adc = MCP3008(channel = 0)

def calc_wind_direction(adc_value):
# The wind vane is in series with a 4.7kOhm resistor. 
#    See the Sparkfun datasheet for the vane resistances that correspond to each direction.
#    adc output is normalised to be between 0 and 1
#    Either or open or short circuit sets wind_deg to 360 which is an error code"""
    res = 4700.0 * (1 - adc_value) / adc_value
    wind_deg = 360.0

    if 27440.0 <= res < 37560.0:
        wind_deg = 0.0  
    elif 5235.0 <= res < 7385.0:
        wind_deg = 22.5
    elif 7385.0 <= res < 11160.0:
        wind_deg = 45.0
    elif 789.0 <= res < 945.0:
        wind_deg = 67.5
    elif 945 <= res < 1205:
        wind_deg = 90.0
    elif 344.0 <= res < 789.0:
        wind_deg = 112.5
    elif 1805.0 <= res < 2670:
        wind_deg = 135.0
    elif 1205.0 <= res < 1805.0:
        wind_deg = 157.5
    elif 3520.0 <= res < 5235.0:
        wind_deg = 180.0
    elif 2670.0 <= res < 3520.0:
        wind_deg = 202.5
    elif 15060.0 <= res < 18940.0:
        wind_deg = 225.0
    elif 11160.0 <= res < 15060.0:
        wind_deg = 247.5
    elif 92450.0 <= res < 180000.0:
        wind_deg = 270.0
    elif 37560.0 <= res < 53510.0:
        wind_deg = 292.5
    elif 53510.0 <= res < 92450.0:
        wind_deg = 315.0
    elif 18940.0 <= res < 27440.0:
        wind_deg = 337.5  
    elif res < 344.0:     # Short
        wind_deg = 360.0
        
    return wind_deg

def wind_direction(win_deg, wind_speed):
# returns the wind direction as a text string given the angle in degrees
# if there is no wind, set the direction to "-"
# 360 degrees is an error marker
    windDir = ['N', 'NNE', 'NE', 'ENE', 
               'E', 'ESE', 'SE', 'SSE', 
               'S', 'SSW', 'SW', 'WSW', 
               'W', 'WNW', 'NW', 'NNW', 'N/A']
    
    if wind_speed > 0.0 or win_deg == 360.0 :
        wind_dir = windDir[int(round(win_deg / 22.5))]
            
    else:
        wind_dir = "-"
 
    return wind_dir

def get_average(angles):
# Calculate the average value of a list of angles. Return 0 if empty list
# Return the wind direction in 22.5 degree intervals
    average = 0.0
    if len(angles) > 0 :
        if angles[0] == 360 :   # Error marker. Reset the list to just show the error
            angles = [360]
            average = 360.0
        else :       
            sin_sum = 0.0
            cos_sum = 0.0

            for angle in angles:
                r = math.radians(angle)
                sin_sum += math.sin(r)
                cos_sum += math.cos(r)

            flen = float(len(angles))
            s = sin_sum / flen
            c = cos_sum / flen
            if c == 0.0 : # prevent division by zero in math.atan
                if s > 0.0 :
                    average = 90.0
                else :
                    average = 270.0
            else :
                arc = math.degrees(math.atan(s / c))
                average = 0.0

                if s >= 0.0 and c >= 0.0: # 0 - 90
                    average = arc
                elif c < 0.0:    # 90 - 270
                    average = 180.0 + arc
                else : # s <= 0 and c >0  # 270 - 360
                    average = 360.0 + arc
                    
                average = round(average / 22.5) * 22.5
                if average == 360.0 :
                    average = 0.0

    return average

def save_wind_deg(store_wind_deg, wind_speed):
# reads wind direction ADC and appends the wind direction to the end of a list, but
# only if there is a wind blowing
    adc_norm = adc.value
    wind_deg = calc_wind_direction(adc_norm)
    if final_speed > 0.0 or wind_deg == 360 :
        store_wind_deg.append(wind_deg)

#---------------------------------------------------------
# Wind speed
# Each anemometer rotation closes a microswitch which is sensed by GPIO input 5.
# Each time the button is closed it calls an ISR (spin) that increments 'wind_count'

def spin():
    global wind_count
    wind_count = wind_count + 1
"""    print("spin " + str(wind_count))  """

def calculate_speed(time_sec):
    """ calculate the speed in km/hour from the counts and the wind_interval 
     DS_15901 Sparkfun anemometer - wind speed is 2.4km/hr * counts/sec
     Resets the wind counter """
    global wind_count
    km_per_hour = 2.4 * wind_count / time_sec
    wind_count = 0
    return km_per_hour

def save_wind_speed(store_speeds, wind_interval):
# finds the wind speed, appends it to a list, resets the wind counter, and returns the speed
    final_speed = calculate_speed(wind_interval)
    store_speeds.append(final_speed)
    return final_speed

wind_speed_sensor = Button(5)
wind_speed_sensor.when_pressed = spin

#-------------------------------------------------
# Rainfall
# The sensor consists of a bucket which tips after 0.2794mm of rainfall. This sensed by GPIO 6.
# The bucket_tipped ISR increments a counter each time the bucket tips.

def bucket_tipped():
    global rain_count
    rain_count = rain_count + 1

def reset_rainfall():
    global rain_count
    rain_count = 0
    
def read_rainfall():
    """ calculate the rainfall in mm from the counts  
     DS_15901 Sparkfun anemometer - rainfall is 0.2794 * counts in mm """
    global rain_count
    rainfall = 0.2794 * rain_count 
    reset_rainfall()

    return str(rainfall)


rain_sensor = Button(6)
rain_sensor.when_pressed = bucket_tipped

#------------------------------------------------------------
# Read the BME280 and return air temp, pressure and humidity
# The BME280 board requires +3V, ground and outputs SDI to the GPIO SDA pin, and SCK to the
# GPIO SCL pin

port = 1
address = 0x77 # Adafruit BME280 address
try :  # Check for disconnected or broken sensor
    bus = smbus2.SMBus(port)
    bme280.load_calibration_params(bus,address)
except :
    pass
   

def read_all(weather_data):
    "  read data from the bme280 "

    try : # check for broken/disconnected sensor
        bme280_data = bme280.sample(bus,address)
        weather_data['humidity'] = str(round(bme280_data.humidity))
        weather_data['pressure'] = str(round(bme280_data.pressure))
        weather_data['ambient_temp'] = str(round(bme280_data.temperature,1))
    except :
        weather_data['humidity'] = 'N/A'
        weather_data['pressure'] = 'N/A'
        weather_data['ambient_temp'] = 'N/A'   


#---------------------------------------------------------
# Ground temperature probe ds18b20. 

def read_ground_temperature(weather_data):
# read the ground temperature and store it in 'weather_data'
    ground_t = round(ground_temp.read_temp(),1)
    if ground_t < -250 :   # read error marker 
        weather_data['ground_temp'] = 'N/A'
    else :
        weather_data['ground_temp'] = str(round(ground_temp.read_temp(),1))

# Initialize the sensor

ground_temp = ds18b20_therm.DS18B20()

#-------------------------------------------------
# Web storage

def upload(weather_data):

    params = ""
    for k, v in weather_data.items():
#        print(k,v)
        params += str(k) + "=" + str(v) + "&"
    # add a key 
    params += 'key=SvddAkZ4yG'

    #Configure connection address
    conn = http.client.HTTPConnection("dev.danleach.uk:80")
  
    #Try to connect to Dan's website and send Data
    try:
        conn.request("GET", "/weather/api/insert/?" + params)
        response = conn.getresponse()
        if response.status != 200 :  # 200 is a successful upload
            print(fullDateTime() + 'Upload response ', response.status, response.reason)
        data = response.read()
        conn.close()
      
    #Catch the exception if the connection fails
    except:
        print(fullDateTime() + "Upload connection failed")

#------------------------------------------------------------
# local data storage

def save_data(weather_data):
    # append the weather data to the local CSV file
     
    filename = 'weather_data.csv'
# creates the file if it is absent
    with open(filename,'a') as f:
        filelen = os.stat(filename).st_size
# Add a header line if the file is empty
        if filelen == 0 :
            params = 'Time(s),'
# save all the keys in csv format 
            for k, v in weather_data.items():
                params += str(k) + ","
            # remove the last , and add a linefeed
            params = params[:-1] + '\n'
            f.write(params)
            
# Now write the weather data to the .csv file
# epoch time is the number of seconds since 1st Jan 1970 ie unix time
        epoch_time = round(time.time())

        params = str(epoch_time) + "," 
# save all the dictionary values
        for k, v in weather_data.items():
            params += str(v) + ","
        # remove the last ',' and add a linefeed
        params = params[:-1] + '\n'

        f.write(params)
#        print(params)

#------------------------------------------------------------
# 

def target_time(interval) :
# returns the next time which is divisible by the interval

    return int(time.time() / interval) * interval + interval

def fullDateTime():
# returns a str containing the current date and time
# For a list of the strftime codes see: https://strftime.org/    
    return strftime("%d/%m/%y  %I:%M%p")

def print_data(weather_data):
# Mainly for debugging - prints out the data written to file and uploaded

    print(fullDateTime() + ' ' + 
        weather_data['wind_speed'] + 'km/hr, ' +
        weather_data['gust_speed'] + 'km/hr, ' +
        weather_data['wind_direction'] + ', ' +
        weather_data['rainfall'] + 'mm/min, ' +
        weather_data['ambient_temp'] + 'C, ' +
        weather_data['ground_temp'] + 'C, ' +
        weather_data['humidity'] + '%, ' +
        weather_data['pressure'] + 'mbar')              

#------------------------------------------------------------

# variables
weather_data = {
    'version' : "0.1",
    'comment' : "Basic_draft",   # No spaces in the comment
    'wind_speed' : "0",          # km/hour
    'gust_speed' : "0",          # km/hour
    'wind_direction' : "-",      # string eg NNW
    'rainfall' : "0",            # mm
    'ambient_temp' : "-273",     # C
    'ground_temp' : "-273",      # C
    'humidity' : "0",            # %
    'pressure' : "0"             # mbar
    }

wind_count = 0
rain_count = 0
savedata_interval = 60  # average data for 1 minute
wind_interval = 2.5       # integrate the wind for 2.5 secs per measurement

avg_wind_deg = 0.0
store_speeds = []
store_wind_deg = []

debug_mode = False
print('Weather Station Program started at ' + fullDateTime())


while True:

# error handling
    try:
        # empty the lists that will used to calculate the average and max values
        store_wind_deg = []
        store_speeds = []

        stop_time = target_time(savedata_interval)

        while time.time() < stop_time :

            time.sleep(target_time(wind_interval) - time.time())

    # read and save the wind speed in a list. Resets the wind counter
            final_speed = save_wind_speed(store_speeds, wind_interval)
         
    # read and save the wind direction in a list, if the wind is blowing
            save_wind_deg(store_wind_deg, final_speed)

    # reads the ambient temperature, humidity and pressure
            read_all(weather_data)

    # reads the ground temperature
            read_ground_temperature(weather_data)

    # debug code
 #           if debug_mode :
 #               wind_deg = '-'
 #               if len(store_wind_deg) > 0 :
 #                   wind_deg = store_wind_deg[len(store_wind_deg) - 1] # latest value
 #               print("Speed", final_speed, "Direction ", wind_direction(wind_deg, final_speed))
 #               print(weather_data['ambient_temp'], weather_data['pressure'], weather_data['humidity'])

    # find the mean and max speeds in the stored list
        gust_speed = round(max(store_speeds),1)
        weather_data['gust_speed'] = str(gust_speed)
        weather_data['wind_speed'] = str(round(statistics.mean(store_speeds),1))

    # find the average wind direction
        avg_wind_deg = get_average(store_wind_deg)
    #    print('avg_wind_deg: ', avg_wind_deg)
        weather_data['wind_direction'] = str(wind_direction(avg_wind_deg,gust_speed))

    # reads the rainfall and resets the rainfall counter
        weather_data['rainfall'] = read_rainfall()

    # upload the data to the web
        upload(weather_data)
    # log the data locally
        save_data(weather_data)
        
        if debug_mode : 
            CPU_temp.printCPUtemperature()
            print_data(weather_data)
            
            
    except:
        print('Error occurred at ' + fullDateTime() + ' ... \n')
        print_data(weather_data)
        CPU_temp.printCPUtemperature()

    
