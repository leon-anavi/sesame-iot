<!DOCTYPE html>
<html>
<head>
  <title>Sesame</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="jqm/jquery.mobile-1.4.5.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <script src="jqm/jquery-1.11.3.min.js"></script>
  <script src="jqm/jquery.mobile-1.4.5.min.js"></script>
  <script src="paho/mqttws31.js"></script>
  <script>
<?php
	$configFilepath = '/etc/opt/sesame-iot/sesame-iot.json';
	$configData = @file_get_contents($configFilepath);
	$config = json_decode($configData, true);
	
	echo "\tvar settingsDelayEntrance = {$config['delayEntrance']};\n";
	echo "\tvar settingsDelayExit = {$config['delayExit']};\n";
?>
  </script>
  <script src="js/iot.js"></script>
</head>
<body>

<div data-role="page" id="pagePin">
  <div data-role="header">
    <h1>Sesame</h1>
  </div>

  <div data-role="content">
    <label for="pin">PIN:</label>
    <input type="tel" maxlength="4" inputmode="numeric" name="pin" id="pin" value="" autofocus required />
    <input type="button" id="buttonLogin" name="buttonLogin" value="OK" />

  <div data-role="popup" id="pinAlert" data-position-to="window" data-transition="turn">
        <div data-role="header">
                <h1>Alert</h1>
        </div>
        <div role="main" class="ui-content">
                <h2 id="pinAlertMessage"></h2>
                <a href="#" data-rel="back" data-role="button" data-theme="b">OK</a>
        </div>
  </div>

  </div><!-- /content -->
</div>

<div data-role="page" id="pageSesame">
  <div data-role="header">
    <h1>Sesame</h1>
  </div>

  <div data-role="main" class="ui-content">
    <input type="button" id="buttonEntrance" value="Entrance" data-role="button" data-icon="arrow-r" />
    <input type="button" id="buttonExit" value="Exit" data-role="button" data-icon="arrow-l" />

    <div data-role="collapsible">
      <h3>Advanced</h3>

      <input type="button" id="buttonOpenBarrier" value="Open Barrier" data-role="button" data-icon="arrow-u" />
      <input type="button" id="buttonOpenDoor" value="Open Garage Door" data-role="button" data-icon="arrow-u" />

    </div>

    <ul data-role="listview" data-inset="true">
      <li data-role="list-divider">Status</li>
      <li data-icon="delete"><a id="statusLinkBarrier" href="#">Barrier <span id="statusBarrier">closed</span></a></li>
      <li data-icon="delete"><a id="statusLinkDoor"  href="#">Door <span id="statusDoor">closed</span></a></li>
    </ul>
  </div>

  <div data-role="popup" id="alert" data-position-to="window" data-transition="turn">
	<div data-role="header">
		<h1>Alert</h1>
	</div>
	<div role="main" class="ui-content">
		<h2 id="alertMessage"></h2>
		<a href="#" data-role="button" data-rel="back" data-theme="b">OK</a>
	</div>
  </div>

</div>

<div class="ui-loader-background"> </div>

</body>
</html>
