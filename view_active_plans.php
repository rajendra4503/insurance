<?php
session_start();
ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;

$uri = basename($_SERVER['PHP_SELF']);
//$query = $_SERVER['QUERY_STRING'];
$current_page_name = $uri;
$_SESSION['page_back_from_customize_page'] = $current_page_name;

include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
$user_id = $_SESSION['view_active_plans_userid'];
//GET USER DETAILS
$get_details = "select t1.FirstName, t1.LastName, t2.EmailID, t2.MobileNo from USER_DETAILS as t1, USER_ACCESS as t2 where 
t1.UserID = t2.UserID and t1.UserID = '$user_id'";
$get_details_query = mysql_query($get_details);
$details_num_rows = mysql_num_rows($get_details_query);
if($details_num_rows > 0){
  while ($details = mysql_fetch_array($get_details_query)) {
    $firstname    = $details['FirstName'];
    $lastname     = $details['LastName'];
    $fullname     = $firstname." ".$lastname;
    $emailid      = $details['EmailID'];
    $mobileno     = $details['MobileNo'];
  }
}
$get_all_plans = mysql_query("select distinct PlanCode from USER_PLAN_HEADER where MerchantID='$logged_merchantid' and UserID = '$user_id' order by CreatedDate desc");
          $get_all_plans_count = mysql_num_rows($get_all_plans);
          if($get_all_plans_count > 0){
            while ($all_plans = mysql_fetch_array($get_all_plans)) {
              $plan_code = $all_plans['PlanCode'];
              if($get_all_plans_count == 1){
                header("Location:ajax_validation.php?type=edit_assigned_plan&userid=$user_id&plancode=$plan_code");
              }
              }
            }
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
   <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Plan Users</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>
    <style type="text/css">

        #mainplanlistdiv {
        overflow: scroll;
        overflow-x: hidden;
        overflow-y: auto;
        }
    </style>       
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
      <?php
      if($logged_usertype=='I')
      {
        $button="Add a Family Member";
      }
      else
      {
        $button="Add a New Plan User";
      }
      ?>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left"  id="plantitle"><a href="plan_users.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="left" id="finished_adding" style="float:left;margin-left:5px;">Back</button></a></div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" align="center"  id="plantitle"><span style="padding-left:0px;">Plans assigned to <?php echo $fullname;?></span></div>
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingrl0" align="right"  id="plantitle"><!-- <a href="new_user.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="right" id="finished_adding"><?php echo $button;?></button></a> --></div>
        <div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px; max-height: 584px;" id="mainplanlistdiv">      
              <?php
          $get_all_plans = mysql_query("select distinct PlanCode, PlanName, PlanCoverImagePath, UserStartOrUpdateDateTime, PlanEndDate from USER_PLAN_HEADER where MerchantID='$logged_merchantid' and UserID = '$user_id' order by CreatedDate desc");
          $get_all_plans_count = mysql_num_rows($get_all_plans);
          if($get_all_plans_count > 0){
            while ($all_plans = mysql_fetch_array($get_all_plans)) {
              $plan_code = $all_plans['PlanCode'];
              $plan_name = $all_plans['PlanName'];
              if(($all_plans['PlanCoverImagePath'] != "")&&($all_plans['PlanCoverImagePath'] != NULL)){
                  $plan_img  = "uploads/planheader/".$all_plans['PlanCoverImagePath'];
              }
              else {
                $plan_img  = "uploads/planheader/default.jpg";
              }
              $start_date = $all_plans['UserStartOrUpdateDateTime'];
              $end_date  =  $all_plans['PlanEndDate'];
              $get_user_count = mysql_query("select count(*) from USER_PLAN_MAPPING as t1, PLAN_HEADER as t2 where t1.PlanCode = t2.PlanCode and t1.PlanCode = '$plan_code' and t1.Status = 'A'");
              $get_user_count_rows = mysql_num_rows($get_user_count);
              if($get_user_count_rows > 0){
                while ($users = mysql_fetch_array($get_user_count)) {
                  $count = $users['count(*)'];
                  //echo $count;
                  ?>
             <div class="assignedplanbox" id="<?php echo $plan_code;?>">
                   <!-- <img src="<?php echo $plan_img;?>" class="planboximg">
                    <div class="blackoverlay"></div>-->
                    <div class="planboxname2">
                    <?php
                     echo $plan_name;
                     if(($start_date != "")&&($start_date != NULL)){
                       $start_date = date('jS M Y',strtotime($all_plans['UserStartOrUpdateDateTime']));
                        $end_date  =  date('jS M Y',strtotime($all_plans['PlanEndDate']));
                      echo "<span style ='font-size:0.6em;'> ( From $start_date to $end_date)</span>";
                     }
                    ?></div>
                    <!--<div class="planboxnumusers"><?php echo $count;?> active users</div>-->
                  </div>
                  
              <?php
                }
              }
            }
          } else {
             echo "<div style='color:#000;text-align:center;font-family:RalewayRegular;font-size:22px;'>No plans assigned for this user.</div>";
          }
        ?>
        </div>
		 	</section>
		  </div>
		</div>		
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/resample.js"></script>
  <script type="text/javascript" src="js/modernizr.js"></script>
  <script type="text/javascript" src="js/placeholders.min.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {
         var w = window.innerWidth;
        var h = window.innerHeight;
        var total = h - 200;
        var each = total/12;
        $('.navbar_li').height(each);
        $('.navbar_href').height(each/2);
        $('.navbar_href').css('padding-top', each/2.8);
        var currentpage = "planusers";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Plan Users");

        var windowheight = h;
        var available_height = h - 240;
        $('#mainplanlistdiv').height(available_height);
        $('.assignedplanbox').click(function(){
          var plancode = $(this).attr('id');
          //alert(plancode);
          var userid = '<?php echo $user_id?>';
          window.location.href = "ajax_validation.php?type=edit_assigned_plan&userid="+userid+"&plancode="+plancode;
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
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
</body>
</html>