from machine import Pin
from time import sleep
led = Pin("LED", Pin.OUT)
print("Rasberry Pico - LED clignotante")
while True:
    led.toggle()
    sleep(0.5)