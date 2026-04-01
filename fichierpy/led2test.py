from machine import Pin
import time
external_led = Pin(15, Pin.OUT)

print("Starting to blink!")

while True:
    external_led.value(1)
    print("LED ON")
    time.sleep(1)          
    
    external_led.value(0)   
    print("LED OFF")
    time.sleep(1)           


