       var dataString = "type=get_notifications&merchant="+merchant;
        //alert(dataString);
        $.ajax({
            type    :"GET",
            url     :"ajax_validation.php",
            data    :dataString,
            dataType  :"jsonp",
            jsonp   :"jsoncallback",
            async   :false,
            crossDomain :true,
            success   : function(data,status){
              //alert(status);
             $.each(data, function(i,item){
                var id = item.ID;
                var message = item.Message;
                var userid = item.UserID;
                notifyMe(id,message,userid);

              });
            },
            error: function(){

            }
         });
        setInterval(function() {
       var dataString = "type=get_notifications&merchant="+merchant;
        //alert(dataString);
        $.ajax({
            type    :"GET",
            url     :"ajax_validation.php",
            data    :dataString,
            dataType  :"jsonp",
            jsonp   :"jsoncallback",
            async   :false,
            crossDomain :true,
            success   : function(data,status){
              //alert(status);
             $.each(data, function(i,item){
                var id = item.ID;
                var message = item.Message;
                var userid = item.UserID;
                notifyMe(id,message,userid);

              });
            },
            error: function(){

            }
         });

    }, 1000 * 60 * 5);
function notifyMe(id, message, userid) {
  var message = message;
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    alert("This browser does not support desktop notification");
  }

  // Let's check whether notification permissions have alredy been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var randnum = Date.now();
    var notification = new Notification('Planpiper Notification', {
      body: message,
      icon: "images/planpipe_logo.png",
      tag : userid
    });
    notification.onclick = function() {
      window.location.href = "client_dashboard.php?id="+userid;
    };
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        var notification = new Notification('Planpiper Notification', {
           body: message,
          icon: "images/planpipe_logo.png"
        });
        notification.onclick = function() {
          window.location.href = "client_dashboard.php?id="+userid;
        };
      }
    });
  }

  // At last, if the user has denied notifications, and you 
  // want to be respectful there is no need to bother them any more.
}