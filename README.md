# 💡 Tableau de Bord pour Capteur de Lumière IoT (Raspberry Pi Pico W & BH1750)

## 📖 Présentation
Ce projet est une solution IoT (Internet des Objets) complète qui mesure la luminosité ambiante, envoie les données via Wi-Fi à un serveur local, et les affiche en temps réel sur une interface web.

## 🚀 Fonctionnalités
* **Données en temps réel :** Lecture de la luminosité (en Lux) grâce au capteur I2C BH1750.
* **Connectivité Wi-Fi :** Le Raspberry Pi Pico W envoie les données directement à un serveur web local Apache via des requêtes HTTP GET.
* **Stockage des données :** Archivage sécurisé de toutes les mesures dans une base de données MySQL.
* **Interface interactive :** Affichage des 20 dernières mesures avec **Plotly.js**, et calcul automatique des valeurs max, min et de la moyenne.
* **Synchronisation Matérielle :** Une LED externe s'allume physiquement lorsque la lumière passe sous les 50 Lux, et son état est reflété sur l'interface web.

## 🛠️ Technologies Utilisées
* **Matériel :** Raspberry Pi Pico W, Capteur de lumière BH1750, LED, Breadboard
* **Microcontrôleur :** MicroPython (`machine.I2C`, `network`, `urequests`)
* **Backend :** PHP (PDO), Serveur Web Apache (XAMPP)
* **Base de données :** MySQL
* **Frontend :** HTML, CSS, JavaScript (Plotly.js)

