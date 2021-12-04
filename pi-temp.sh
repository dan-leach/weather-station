#!/bin/bash
# Script: pi-temp.sh
# Purpose: Display the ARM GPU and CPU temperatures
# Author: Vivek Gite <www.cyberciti.biz> under GPL v2.x+
#-------------------------------------------------------
cpu=$(</sys/class/thermal/thermal_zone0/temp)
echo "$(date) @ $(hostname)"
echo "------------------------------------------"
echo "GPU => $(vcgencmd measure_temp)"
echo "CPU => $((cpu/1000))'C"

