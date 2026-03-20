import math
import time
import network
import urequests 
from machine import I2C, Pin
from micropython import const
from utime import sleep_ms
from picozero import pico_temp_sensor, pico_led
import machine
import rp2
import sys


ssid = 'wifirpi'
password = '88E4VB1YQBI15TM4UCK9KP1LWQ'


class BH1750:
    """Class for the BH1750 digital Ambient Light Sensor"""
    MEASUREMENT_MODE_CONTINUOUSLY = const(1)
    MEASUREMENT_MODE_ONE_TIME = const(2)
    
    RESOLUTION_HIGH = const(0)
    RESOLUTION_HIGH_2 = const(1)
    RESOLUTION_LOW = const(2)
    
    MEASUREMENT_TIME_DEFAULT = const(69)
    MEASUREMENT_TIME_MIN = const(31)
    MEASUREMENT_TIME_MAX = const(254)

    def __init__(self, address, i2c):
        self._address = address
        self._i2c = i2c
        self._measurement_mode = BH1750.MEASUREMENT_MODE_ONE_TIME
        self._resolution = BH1750.RESOLUTION_HIGH
        self._measurement_time = BH1750.MEASUREMENT_TIME_DEFAULT
        
        self._write_measurement_time()
        self._write_measurement_mode()
        
    def configure(self, measurement_mode: int, resolution: int, measurement_time: int):
        if measurement_time not in range(BH1750.MEASUREMENT_TIME_MIN, BH1750.MEASUREMENT_TIME_MAX + 1):
            raise ValueError("measurement_time must be between {0} and {1}".format(BH1750.MEASUREMENT_TIME_MIN, BH1750.MEASUREMENT_TIME_MAX))
        
        self._measurement_mode = measurement_mode
        self._resolution = resolution
        self._measurement_time = measurement_time
        
        self._write_measurement_time()
        self._write_measurement_mode()
    
    def _write_measurement_time(self):
        buffer = bytearray(1)
        high_bit = 1 << 6 | self._measurement_time >> 5
        low_bit = 3 << 5 | (self._measurement_time << 3) >> 3
                
        buffer[0] = high_bit
        self._i2c.writeto(self._address, buffer)
        
        buffer[0] = low_bit
        self._i2c.writeto(self._address, buffer)
        
    def _write_measurement_mode(self):
        buffer = bytearray(1)
                
        buffer[0] = self._measurement_mode << 4 | self._resolution
        self._i2c.writeto(self._address, buffer)
        sleep_ms(24 if self._measurement_time == BH1750.RESOLUTION_LOW else 180)
        
    def reset(self):
        """Clear the illuminance data register."""
        self._i2c.writeto(self._address, bytearray(b'\x07'))
    
    def power_on(self):
        """Powers on the BH1750."""
        self._i2c.writeto(self._address, bytearray(b'\x01'))

    def power_off(self):
        """Powers off the BH1750."""
        self._i2c.writeto(self._address, bytearray(b'\x00'))

    @property
    def measurement(self) -> float:
        """Returns the latest measurement."""
        if self._measurement_mode == BH1750.MEASUREMENT_MODE_ONE_TIME:
            self._write_measurement_mode()
            
        buffer = bytearray(2)
        self._i2c.readfrom_into(self._address, buffer)
        lux = (buffer[0] << 8 | buffer[1]) / (1.2 * (BH1750.MEASUREMENT_TIME_DEFAULT / self._measurement_time))
        
        if self._resolution == BH1750.RESOLUTION_HIGH_2:
            return lux / 2
        else:
            return lux
    
    def measurements(self) -> float:
        while True:
            yield self.measurement
            
            if self._measurement_mode == BH1750.MEASUREMENT_MODE_CONTINUOUSLY:
                base_measurement_time = 16 if self._measurement_time == BH1750.RESOLUTION_LOW else 120
                sleep_ms(math.ceil(base_measurement_time * self._measurement_time / BH1750.MEASUREMENT_TIME_DEFAULT))
                


def connect():
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)
    wlan.connect(ssid, password)
    while not wlan.isconnected():
        if rp2.bootsel_button() == 1:
            sys.exit()
        print('Waiting for connection...')
        pico_led.on()
        time.sleep(0.5)
        pico_led.off()
        time.sleep(0.5)
    ip = wlan.ifconfig()[0]
    print(f'Connected on {ip}')
    #pico_led.on()
    return ip


ip = connect()


i2c = I2C(0, sda=Pin(8), scl=Pin(9), freq=400000)
devices = i2c.scan()

if not devices:
    print("No I2C devices found! Check wiring.")
    sys.exit()

sensor = BH1750(devices[0], i2c)
print("BH1750 ready. Sending data...\n")


external_led = Pin(15, Pin.OUT)

while True:
    if rp2.bootsel_button() == 1:
        pico_led.off()
        external_led.value(0) 
        print('ByBye')
        sys.exit()

    try:
        lux = sensor.measurement
        print(f"Ambient Light: {lux:.2f} lux")

       
        if lux < 50:
            external_led.value(1) 
        else:
            external_led.value(0)  

       
        ip = "193.48.125.209" 
        url = f"http://{ip}/TPcapteur/ProjetCapteurBH/api.php?valeur=" + str(round(lux, 2))
        
        response = urequests.get(url)
        print(f"Server response: {response.status_code} - {response.content.decode('utf-8')}")
        response.close()

    except Exception as e:
        print(f"Error: {e}")

    time.sleep(2)


    
"""
 if lux<=100.0:
            pico_led.on()
        else:
            pico_led.off()
     
"""
