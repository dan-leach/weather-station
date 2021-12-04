import os

def printCPUtemperature():
    # call a bash shell which which accesses the CPU and GPU temperatures
    os.system("/home/pi/weather-station/pi-temp.sh")

if __name__ == "__main__":
    printCPUtemperature()