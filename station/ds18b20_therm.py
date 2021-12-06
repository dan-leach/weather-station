#!/usr/bin/python3
import os, glob, time

# add the lines below to /etc/modules (reboot to take effect)
# w1-gpio
# w1-therm

class DS18B20(object):
    def __init__(self):        
        try :
            self.device_file = glob.glob("/sys/bus/w1/devices/28*")[0] + "/w1_slave"
        except :
            return
        
    def read_temp_raw(self):
        try :
            f = open(self.device_file, "r")
            lines = f.readlines()
            f.close()
        except:
            lines = []
        return lines
        
    def crc_check(self, lines):
        if len(lines) > 0 :    # an empty list did occur for some reason
            return lines[0].strip()[-3:] == "YES"
        else :
            return False
        
    def read_temp(self):
        temp_c = -255
        attempts = 0
        
        lines = self.read_temp_raw()
        success = self.crc_check(lines)
        
        while not success and attempts < 3:
            time.sleep(.2)
            lines = self.read_temp_raw()            
            success = self.crc_check(lines)
            attempts += 1
        
        if success:
            temp_line = lines[1]
            equal_pos = temp_line.find("t=")            
            if equal_pos != -1:
                temp_string = temp_line[equal_pos+2:]
                temp_c = float(temp_string)/1000.0
                if temp_c > 65.0 :   # If the +3V power becomes disconnected
                    temp_c = -255    # it returns 85C. So flag as N/C
        return temp_c

if __name__ == "__main__":
    obj = DS18B20()
    print("Temp: %s C" % obj.read_temp())
