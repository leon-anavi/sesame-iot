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

});