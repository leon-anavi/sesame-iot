//Delay between opeing the 1st and the 2nd door (in seconds)
var settingsDelay = 30;

function setButtonsStatus(status) {
  $('#buttonEntrance').button(status);
  $('#buttonExit').button(status);
  $('#buttonOpenBarrier').button(status);
  $('#buttonOpenDoor').button(status);
}

function sendCommand(userCommand) {

  $.ajax({ url: "http://127.0.0.1/sesame/",
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

      setButtonsStatus('disable');
      setTimeout(function(){ setButtonsStatus('enable'); }, settingsDelay*1000);

      var error = JSON.parse(result);
      var code = (error.hasOwnProperty('errorCode')) ? error.errorCode : 0;
      var message = (error.hasOwnProperty('errorMessage')) ? error.errorMessage : '';
      console.log('Code: '+code+' Message: '+message);
    },
    //Error callback
    error: function(xhr){
      alert("Error: " + xhr.status + " " + xhr.statusText);
    }
  });
}

$(document).ready(function() {

  $('#buttonEntrance').bind('click', function(event) {
    sendCommand('еntrance');
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
