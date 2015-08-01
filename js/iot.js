//Delay between opeing the 1st and the 2nd door (in seconds)

var mqttHost = location.hostname;
var mqttPort = (!location.port) ? 80 : location.port;
var mqttPath = '/mosquitto';

var client = null;

function setButtonsStatus(status) {
  $('#buttonEntrance').button(status);
  $('#buttonExit').button(status);
  $('#buttonOpenBarrier').button(status);
  $('#buttonOpenDoor').button(status);
}

function sendCommand(userCommand) {

  $.ajax({ url: "http://"+location.hostname+"/sesame-iot/server/",
    type: "POST",
    data: { command: userCommand },
    beforeSend: function() {
      //Show loading message
      $.mobile.loading('show', {
          theme: "a",
          text: 'Loading...',
          textonly: false,
          textVisible: true
      });
    },
    complete: function() {
      //Hide loading message
      $.mobile.loading('hide');
    },
    //Success callback
    success: function(result) {

      listenMQTT();

      var error = JSON.parse(result);
      var code = (error.hasOwnProperty('errorCode')) ? error.errorCode : 0;
      var message = (error.hasOwnProperty('errorMessage')) ? error.errorMessage : '';
      console.log('Code: '+code+' Message: '+message);

      if (0 < code) {
        alert(message);
	return;
      }

      var settingsDelay = 0;
      if ('entrance' === userCommand) {
        settingsDelay = settingsDelayEntrance;
      }
      else if ('exit' === userCommand) {
        settingsDelay = settingsDelayExit;
      }

      if (0 < settingsDelay) {
	setButtonsStatus('disable');
        setTimeout(function(){ setButtonsStatus('enable'); }, settingsDelay*1000);
      }
    },
    //Error callback
    error: function(xhr){
      alert("Error: " + xhr.status + " " + xhr.statusText);
    }
  });
}

// called when the client connects
function onConnect() {
  // Once a connection has been made, make a subscription and send a message.
  console.log("onConnect");
  client.subscribe("/door");
  client.subscribe("/barrier");
}

// called when the client loses its connection
function onConnectionLost(responseObject) {
  if (responseObject.errorCode !== 0) {
    console.log("onConnectionLost:"+responseObject.errorMessage);
  }
}

function getCurrentTime() {
  return new Date().toString();
}

function changeIcon(linkId) {
  if ($(linkId).hasClass('ui-icon-delete')) {
    $(linkId).removeClass('ui-icon-delete');
    $(linkId).addClass('ui-icon-check');
  }
}

// called when a message arrives
function onMessageArrived(message) {
  try {
    var data = JSON.parse(message.payloadString);
    if ('ok' === data.status) {
      if ('/door' === message.destinationName) {
        $('#statusDoor').text('opened at: ' + getCurrentTime());
        changeIcon('#statusLinkDoor');
      }
      else if ('/barrier' === message.destinationName) {
        $('#statusBarrier').text('opened at: ' + getCurrentTime());
        changeIcon('#statusLinkBarrier');
      }
    }
  }
  catch(err) {
      console.log("MQTT message: "+message.payloadString);
  }
}

function listenMQTT() {
  // Create a client instance
  client = new Paho.MQTT.Client(mqttHost, Number(mqttPort), mqttPath, "clientWeb");

  // set callback handlers
  client.onConnectionLost = onConnectionLost;
  client.onMessageArrived = onMessageArrived;

  // connect the client
  client.connect({onSuccess:onConnect});
}

$(document).ready(function() {

  $('#buttonEntrance').bind('click', function(event) {
    sendCommand('entrance');
  });

  $('#buttonExit').bind('click', function(event) {
    sendCommand('exit');
  });

  $('#buttonOpenBarrier').bind('click', function(event) {
    sendCommand('barrier');
  });

  $('#buttonOpenDoor').bind('click', function(event) {
    sendCommand('door');
  });


});
