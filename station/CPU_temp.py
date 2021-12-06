import os
import subprocess

def printCPUtemperature():
    # call a bash shell which which accesses the CPU and GPU temperatures
    os.system("/home/pi/weather-station/pi-temp.sh")

def CPUtemp():
    # Read the Raspberry Pi CPU temperature
    result = subprocess.check_output(['cat', '/sys/class/thermal/thermal_zone0/temp'])
    # returns type byte so need to decode it. The temperature needs to be divided by 1000
    return round(float(result.decode('utf-8')) / 1000.0, 1)

def GPUtemp():
    # Read the Raspberry Pi GPU temperature
    result = subprocess.check_output(['vcgencmd', 'measure_temp'])
    txt = result.decode('utf-8')  # convert to str
    txt = txt[5:]  # strip off 'temp='
    txt = txt[:-3] # strip off 'C  
    return float(txt)

if __name__ == "__main__":
    printCPUtemperature()
    
    print(CPUtemp(), GPUtemp())
    