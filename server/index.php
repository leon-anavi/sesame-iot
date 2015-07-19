<?php

require("phpMQTT.php");

$mqttTopicDoor1 = '/barrier';
$mqttTopicDoor2 = '/door';
//delay in seconds
$mqttDelayEntrance = 10;
$mqttDelayExit = 10;
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

function printResponse($code, $message) {
	echo json_encode(array('errorCode' => $code, 'errorMessage' => $message));
}

if (false === $mqtt->connect()) {
	printResponse(1, 'Unable to connect to MQTT broker.');
}
else {

	ob_end_clean();
	header("HTTP/1.1 200 OK");
	header("Connection: close\r\n");
	header("Content-Encoding: none\r\n");
	ignore_user_abort(true); // optional
	ob_start();
	printResponse(0, 'OK');
	header("Content-Length: ".ob_get_length());
	ob_end_flush();     // Strange behaviour, will not work
	flush();            // Unless both are called !
	ob_end_clean();

	//Run the sequence to enter the building if the command is еntrance
	//or if it has not been specified.
	if ( (false === isset($_REQUEST['command'])) || ('exit' !== $_REQUEST['command']) ) {
		openSesame($mqtt, $mqttTopicDoor1, $mqttTopicDoor2, $mqttDelayEntrance);
	}
	else if ('barrier' !== $_REQUEST['command']) {
		$mqtt->publish($mqttTopicDoor1,"open");
	}
	else if ('door' !== $_REQUEST['command']) {
		$mqtt->publish($mqttTopicDoor2,"open");
	}
	else {
		//Run the sequence to exit the building
		openSesame($mqtt, $mqttTopicDoor2, $mqttTopicDoor1, $mqttDelayExit);
	}
	$mqtt->close();
}
?>
