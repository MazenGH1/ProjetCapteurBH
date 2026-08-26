# 💡 IoT Light Sensor Dashboard (Raspberry Pi Pico W & BH1750)

## 📖 Overview
This project is an end-to-end IoT (Internet of Things) solution that measures ambient light levels, sends the data over Wi-Fi to a local server, and visualizes it in real-time on a web dashboard. 

## 🚀 Features
* **Real-Time Data:** Reads light levels (Lux) using the BH1750 I2C sensor.
* **Wi-Fi Connectivity:** The Raspberry Pi Pico W sends data directly to a local Apache server via HTTP GET requests.
* **Data Storage:** Archives all readings securely in a MySQL database.
* **Interactive Dashboard:** Visualizes the last 20 readings using **Plotly.js**, calculating the max, min, and average values automatically.
* **Hardware Sync:** An external LED triggers when the light drops below 50 Lux, and its status is mirrored on the web dashboard.

## 🛠️ Tech Stack
* **Hardware:** Raspberry Pi Pico W, BH1750 Light Sensor, LED, Breadboard
* **Microcontroller:** MicroPython (`machine.I2C`, `network`, `urequests`)
* **Backend:** PHP (PDO), Apache Web Server (XAMPP)
* **Database:** MySQL
* **Frontend:** HTML, CSS, JavaScript (Plotly.js)

