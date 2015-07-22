# sesame-iot
Simple UI and server to open doors with MQTT and ESP8266

#Installation

* Install web server and PHP
* Install MQTT broker, for example mosqitto
* Enable MQTT over websockets
* Create config file
```
sudo mkdir -p /etc/opt/sesame-iot/
sudo cp config/sesame-iot.sample.json /etc/opt/sesame-iot/sesame-iot.json
sudo chown 644  /etc/opt/sesame-iot/sesame-iot.json
```
* Open /etc/opt/sesame-iot/sesame-iot.json and edit settings
