<?php

require("phpMQTT.php");

$mqttTopicDoor1 = '/door1';
$mqttTopicDoor2 = '/door2';
$mqttDelay = 1;
$mqtt = new phpMQTT("iot.example.com", 1883, "sesame");

/*
 * Open doors
 *
 * @param $mqtt MQTT client
 * @param $topic1 topic related to the first door on the route
 * @param $topic2 topic related to the second door on the route
 * @param delay seconds between the opening of the 1st and the 2nd door
 *
 * @return nothing
 * @throws nothing
 */
function openSesame($mqtt, $topic1, $topic2, $delay) {
	$mqtt->publish($topic1,"open");
	sleep($delay);
	$mqtt->publish($topic2,"open");
}

$errorCode = 0;
$errorMessage = 'OK';
if (false === $mqtt->connect()) {
	$errorCode = 1;
	$errorMessage = 'Unable to connect to MQTT broker.';
}
else {
	//Run the sequence to enter the building if the command is еntrance
	//or if it has not been specified.
	if ( (false === isset($_REQUEST['command'])) || ('exit' !== $_REQUEST['command']) ) {
		openSesame($mqtt, $mqttTopicDoor1, $mqttTopicDoor2, $mqttDelay);
	}
	else {
		//Run the sequence to exit the building
		openSesame($mqtt, $mqttTopicDoor2, $mqttTopicDoor1, $mqttDelay);
	}
	$mqtt->close();
}
echo json_encode(array('errorCode' => $errorCode, 'errorMessage' => $errorMessage));
?>
