<?php
$configFilepath = '/etc/opt/sesame-iot/sesame-iot.json';

require("phpMQTT.php");

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
	if (false !== $mqtt->connect()) {
		$mqtt->publish($topic1,"open");
		$mqtt->close();
	}
	sleep($delay);
	if (false !== $mqtt->connect()) {
		$mqtt->publish($topic2,"open");
		$mqtt->close();
	}
}

function printResponse($code, $message) {
	echo json_encode(array('errorCode' => $code, 'errorMessage' => $message));
}

$httpProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';

$isConnected = false;

//read configuration file
try {
	if (false === file_exists($configFilepath)) {
		throw new Exception("Unable to read configurations. Please ensure that $configFilepath exists.");
	}
	$configData = @file_get_contents($configFilepath);
	if (false === $configData) {
		throw new Exception("Unable to read configurations. Please ensure that the user have rights to read $configFilepath.");
	}
	$config = json_decode($configData, true);
	if (false === $config) {
		throw new Exception('Unable to parse configations. Please make sure that the JSON format is valid.');
	}

	if (false === isset($_REQUEST['command'])) {
		throw new Exception('Command not specified');
	}

	$mqtt = new phpMQTT($config['host'], $config['port'], $config['clientId']);

	if (false === @$mqtt->connect()) {
		printResponse(1, 'Unable to connect to MQTT broker.');
	}
	else {
		$isConnected = true;

		ob_end_clean();
		header("{$httpProtocol} 200 OK");
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
		if ('entrance' === $_REQUEST['command']) {
			openSesame($mqtt, $config['topicDoor1'], $config['topicDoor2'], $config['delayEntrance']);
		}
		else if ('barrier' === $_REQUEST['command']) {
			$mqtt->publish($config['topicDoor1'],"open");
		}
		else if ('door' === $_REQUEST['command']) {
			$mqtt->publish($config['topicDoor2'],"open");
		}
		else if ('exit' === $_REQUEST['command']) {
			//Run the sequence to exit the building
			openSesame($mqtt, $config['topicDoor2'], $config['topicDoor1'], $config['delayExit']);
		}
		else {
			throw new Exception('Command not found.');
		}
	}
}
catch (Exception $ex) {
	header("{$httpProtocol} 500 Internal Server Error", true, 500);
	echo $ex->getMessage()."\n";
	exit;
}
finally {
	if ($isConnected) {
		$mqtt->close();
	}
}
?>
