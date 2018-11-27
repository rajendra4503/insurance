<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
//$_SESSION['current_assigned_plan_code']="";
//echo $plan_to_customize;exit;
/*
if(isset($_REQUEST['query'])){
  $query = $_REQUEST['query'];
  //echo $query;exit;
  header("location:selectuser.php?query=$query");
}
*/
if(isset($_REQUEST['hidden_value'])){
  $userarray = array();
  $userarray = $_REQUEST['magicsuggest'];
  foreach ($userarray as $user) {
    if($user != ""){
      header("location:client_dashboard.php?id=$user");
    }
  }

}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Plan Piper - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/magicsuggest.css">
    <link rel="stylesheet" type="text/css" href="css/planpiper.css">
    <link rel="stylesheet" type="text/css" href="fonts/font.css">
    <style type="text/css">
      .ms-ctn .ms-sel-item {
        background: #004f35;
        color: #fff;
        border: 1px solid #004f35;
        height: 40px;
        line-height: 16px;
      }
      .ms-ctn .ms-trigger{
        display: none;
      }
    </style>
    <link rel="shortcut icon" href="images/planpipe_logo.png"/> 
    <script type="text/javascript">
    function keychk(event)
    {
    //alert(123)
      if(event.keyCode==13)
      {
        $("#searchbutton").click();
      }
    }
  </script>   
  <script>
  var loggeduser = '<?php echo $logged_firstname;?>';
  var message = "Hi "+loggeduser;
function notifyMe(message) {
  var msg = message;
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    alert("This browser does not support desktop notification");
  }

  // Let's check whether notification permissions have alredy been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification('Email received', {
  body: 'You have a total of 3 unread emails'
});
    notification.onclick = function() {
  alert("Clicked");
};
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        var notification = new Notification('Email received', {
  body: 'You have a total of 3 unread emails'
});
            notification.onclick = function() {
  alert("Clicked");
};
      }
    });
  }

  // At last, if the user has denied notifications, and you 
  // want to be respectful there is no need to bother them any more.
}
  </script>   
  </head>
  <body style="overflow:hidden;" onkeypress='keychk(event)'>
  <div id="planpiper_wrapper">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
       <div class="col-sm-2 paddingrl0" id="sidebargrid">
        <?php include("sidebar.php");?>
     </div>
     <div class="col-sm-10 paddingrl0" id="content_wrapper">
      <?php include_once('top_header.php');?>
        <section>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="plantitle" style="height:65px;padding-left:2px;">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left">
                <form name="frm_search_user" id="frm_search_user" method="POST">
                  <div id="magicsuggest" name="magicsuggest" style="width:100%;"></div>
                  <input type="hidden" name="hidden_value" id="hidden_value" value="1">
                </form>
              </div>
              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 paddingr0" style="background-color: #DFDFDF;height: 52px;margin-top: 1px;margin-left: -5px;">
                <span><img src="images/find.png" style="height:30px;cursor:pointer;margin-top:5px;" class="searchbutton" name="searchbutton" id="searchbutton"></span>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 paddingr0" align="left">
                
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingr0" align="left">
                
              </div>
          </div>
          <div><button onclick="notifyMe('Hi')">Notify me!</button></div>
        </section>
      </div>
    </div>    
  </div><!-- planpiper_wrapper ends -->
      
  <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
  <script type="text/javascript" src="js/magicsuggest.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/modernizr.js"></script>
  <script type="text/javascript" src="js/placeholders.min.js"></script>
  <script type="text/javascript" src="js/bootbox.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function() {
        var w = window.innerWidth;
        var h = window.innerHeight;
        var total = h - 200;
        var each = total/12;
        $('.navbar_li').height(each);
        $('.navbar_href').height(each/2);
        $('.navbar_href').css('padding-top', each/2.8);
        
        var currentpage = "dashboard";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Dashboard");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
        
        $('.smallplanbox').hover(function(){
          $('.onhoveroptions').hide();
          $('.onhoveroptions', $(this)).show(); 
        });
        $('#magicsuggest').magicSuggest({
            allowDuplicates: false,
            allowFreeEntries: false,
            name: 'magicsuggest',
            cls: 'custom',
            data: 'ajax_get_clients.php',
            placeholder : 'Search for a user',
            maxSelection : 1,
            ajaxConfig: {
                xhrFields: {
                withCredentials: true,
                }
            }
        });

        // To hide 'No Suggestions' text after selecting one from the dropdown
        var ms = $('#magicsuggest').magicSuggest({});
        $(ms).on('selectionchange', function(event, combo, selection){
          $('.ms-helper').hide();
        });
        
        var sidebarflag = 1;
        $('#topbar-leftmenu').click(function(){
          if(sidebarflag == 1){
              //$('#sidebargrid').hide(150);
              $('#sidebargrid').hide("slow","swing");
              //$('#content_wrapper').addClass("col-sm-12");
              $('#content_wrapper').removeClass("col-sm-10");
              sidebarflag = 0;
          } else {
              $('#sidebargrid').show("slow","swing");
              $('#content_wrapper').addClass("col-sm-10");
              //$('#content_wrapper').removeClass("col-sm-12");
              sidebarflag = 1;
          }
        });
        $('#searchbutton').click(function(){
          if(!$('div.ms-sel-item').length){
            alert("Please select a user to continue");  
            return false;
          }
          $('#frm_search_user').submit();
          //window.location.href = "selectuser.php?query="+searchuser;
        });
        $('.ms-sel-ctn input').focus();


  });
  </script>
</body>
<?php
  include('include/unset_session.php');
  ?>
</html>