<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
//$_SESSION['current_assigned_plan_code']="";
//echo $plan_to_customize;exit;

if(isset($_REQUEST['query'])){
  $query = $_REQUEST['query'];
  //echo $query;exit;
} else {
  header("location:dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Plan Piper - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/planpiper.css">
    <link rel="stylesheet" type="text/css" href="fonts/font.css">
    <link rel="shortcut icon" href="images/planpipe_logo.png"/>     
    <style type="text/css">
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
      padding: 5px;
    }
    </style> 
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
  </head>
  <body style="overflow:hidden;">
  <div id="planpiper_wrapper">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
       <div class="col-sm-2 paddingrl0" id="sidebargrid">
        <?php include("sidebar.php");?>
     </div>
     <div class="col-sm-10 paddingrl0" id="content_wrapper">
      <?php include_once('top_header.php');?>
        <section>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="plantitle" style="height:70px;padding-left:2px;">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left" style="background-color:#DFDFDF;border-radius:10px;">
                <input type="text" placeholder="Search for users.." id="searchuser" name="searchuser" class="dashboardsearch" maxlength="50" autofocus value="<?php echo $query;?>" onfocus="this.value = this.value;" onkeypress='keychk(event)'><span><img src="images/find.png" style="height:30px;padding-left:5px;cursor:pointer;" class="searchbutton" name="searchbutton" id="searchbutton"></span>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingr0" align="left">
                
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingr0" align="left">
                
              </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
          <table class="table table-responsive">
            <tr>
              <th style="width:60px;text-align:center;">#</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>Email</th>
              <th></th>
            </tr>
          
            <?php 
              $get_user_query = "select t1.UserID, concat('+', TRIM(LEADING '0' FROM t1.MobileNo)) as MobileNo, t1.EmailID, t2.FirstName, t2.LastName from USER_ACCESS as t1, USER_DETAILS as t2, USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID and t1.UserID = t3.UserID and t3.RoleID = '5' and t3.MerchantID = '$logged_merchantid' and (t2.FirstName like '%$query%' or t2.LastName like '%$query%' or t1.EmailID like '%$query%' or t1.MobileNo like '%$query%')";
              //echo $get_user_query;exit;
              $get_user_run = mysql_query($get_user_query);
              $get_user_count = mysql_num_rows($get_user_run);
              $count = 0;
              if($get_user_count > 0){
                while ($user_row  = mysql_fetch_array($get_user_run)) {
                  $count++;
                  $userid         = $user_row['UserID'];
                  $mobileno       = formatPhoneNumber($user_row['MobileNo']);
                  $emailid        = $user_row['EmailID'];
                  $firstname      = $user_row['FirstName'];
                  $lastname       = $user_row['LastName'];
                  echo "<tr><td style='text-align:center;'>$count</td><td>$firstname&nbsp;$lastname</td><td>$mobileno</td><td>$emailid</td><td><a href='client_dashboard.php?id=$userid'>View Dashboard</a></td></tr>";
                }
              } else {
                echo "<tr><td colspan='5' align='center'>No matches found.</td></tr>";
              }
            ?>
            </table>
          </div>
        </section>
      </div>
    </div>    
  </div><!-- planpiper_wrapper ends -->
      
  <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
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
          var searchuser = $('#searchuser').val();
          if(searchuser.replace(/\s+/g, '') == ""){
            alert("Please enter a keyword to search.");
            $('.bootbox').on('hidden.bs.modal', function() { 
              $('#searchuser').focus();     
            });
            $('#searchuser').val("");
            return false;
          }
          window.location.href = "selectuser.php?query="+searchuser;
        });
                var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
  });
  </script>
</body>
<?php
  include('include/unset_session.php');
  ?>
</html>