<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
//$_SESSION['current_assigned_plan_code']="";
//echo $plan_to_customize;exit;

if (isset($_REQUEST['id'])) {
  $id = $_REQUEST['id']; 
}
else {
  header("location:dashboard.php");
}
//Get User Details
$get_user_details = "select concat('+', TRIM(LEADING '0' FROM t1.MobileNo)) as MobileNo, t1.EmailID, t2.FirstName, t2.LastName, t2.AddressLine1, t2.AddressLine2 from USER_ACCESS as t1, USER_DETAILS as t2 where t1.UserID='$id' and t1.UserID = t2.UserID ";
//echo $get_user_details;exit;
$get_user_details_run = mysql_query($get_user_details);
$get_user_count = mysql_num_rows($get_user_details_run);
if($get_user_count > 0){
  while ($details = mysql_fetch_array($get_user_details_run)) {
    $mobileno     = formatPhoneNumber($details['MobileNo']);
    $emailid      = $details['EmailID'];
    $firstname    = $details['FirstName'];
    $lastname     = $details['LastName'];
    $addline1     = $details['AddressLine1'];
    $addline2     = $details['AddressLine2'];
  }
} else {
  ?>
  <script type="text/javascript">
    alert("Please Try Again.");
    window.location.href = "dashboard.php";
  </script>
  <?php
}
if(isset($_REQUEST['hidden_value'])){
  $userarray = array();
  $userarray = $_REQUEST['magicsuggest'];
  foreach ($userarray as $user) {
    if($user != ""){
      header("location:client_planlist.php?id=$user");
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
    <link rel="shortcut icon" href="images/planpipe_logo.png"/> 
    <style type="text/css">
      .ms-ctn .ms-sel-item {
        background: #004f35;
        color: #fff;
        border: 1px solid #004f35;
        height: 35px;
        line-height: 35px;
      }
      .ms-ctn .ms-trigger{
        display: none;
      }
    </style>    
    <script type="text/javascript">
      function keychk(event){
        if(event.keyCode==13){
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
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 paddingr0" align="left" style="font-family: RalewayRegular;font-size: 0.6em;">
                <div>&nbsp;<img src="images/cdphone.png" style="height:25px;width:auto;">&nbsp;<?php echo $mobileno;?></div>
                <div><img src="images/cdemail.png" style="height:12px;width:auto;">&nbsp;<?php echo $emailid;?></div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingr0" align="left" style="font-family: RalewayRegular;font-size: 0.6em;">
                <div><img src="images/cdaddress.png" style="height:20px;width:auto;">&nbsp;<?php echo $addline1;?></div>
                <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $addline2;?></div>
              </div>
          </div>
         <div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">      
          <?php
          $count = 0;
                //GET ALL PLANS ASSIGNED TO THIS USER
                $get_all_plans = "select PlanCode, PlanName, PlanDescription,PlanCoverImagePath from USER_PLAN_HEADER where UserID='$id' and MerchantID='$logged_merchantid'";
                //echo $get_all_plans;exit;
                $get_all_plans_run    = mysql_query($get_all_plans);
                $get_all_plans_count  = mysql_num_rows($get_all_plans_run);
                if($get_all_plans_count > 0){
                  while ($plan_row = mysql_fetch_array($get_all_plans_run)) {
                    $count++;
                    $plancode   = $plan_row['PlanCode'];
                    $planname   = $plan_row['PlanName'];
                    $plandesc   = $plan_row['PlanDescription'];
                    if(($plan_row['PlanCoverImagePath'] != "")&&($plan_row['PlanCoverImagePath'] != NULL)){
                      $planimg    = "uploads/planheader/".$plan_row['PlanCoverImagePath'];
                    } else {
                      $planimg    = "uploads/planheader/default.jpg";
                    }
                    ?>
                   <a href="client_dashboard.php?id=<?php echo $id;?>&pc=<?php echo $plancode;?>"><div class="assignedplanbox" id="<?php echo $plancode;?>">
                    <!-- <img src="<?php echo $planimg;?>" class="planboximg">
                    <div class="blackoverlay"></div> -->
                    <div class="planboxname2"><?php echo $planname;?></div>
                  </div></a>

                    <?php
                  }
                } else {
                 echo "<div align='center' style='font-size:1.5em;font-family:RalewayRegular;'>No Plan Assigned to this user.</div>";
                }
            ?>        
            </div>
          </div>
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
        var username = "<?php echo $firstname." ".$lastname." - Dashboard";?>";
        $('#plapiper_pagename').html(username);

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
        // alert(available_height);
        available_height = available_height-$('.graphs').height() + 40;
        //alert(available_height);
        //$('#pdfobject').height(available_height);
        
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
        $('#searchbutton').click(function(){
          if(!$('div.ms-sel-item').length){
            alert("Please select a user to continue");  
            return false;
          }
          $('#frm_search_user').submit();
          //window.location.href = "selectuser.php?query="+searchuser;
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