<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;

$uri = basename($_SERVER['PHP_SELF']);
//$query = $_SERVER['QUERY_STRING'];
$current_page_name = $uri;
$_SESSION['page_back_from_customize_page'] = $current_page_name;

include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
$view_active_users_plancode = $_SESSION['view_active_users_plancode'];
//echo $view_active_users_plancode;exit;
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Plans</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
	</head>
	<body style="overflow:hidden;">
	<div id="planpiper_wrapper">
	  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
	  	 <div class="col-sm-2 paddingrl0"  id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
     
      <!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
        <div id="plantitle">My Plans</div>
      </div>-->
		 	<section>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left"  id="plantitle"><a href="assignedplans.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="left" id="finished_adding" style="float:left;margin-left:5px;">Back</button></a></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="center"  id="plantitle">My Plan Lists</div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="right"  id="plantitle"><?php if($logged_roleid !=4){?><a href="generate_plancode.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="right" id="finished_adding">Add New Plan</button></a><?php } ?></div>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="plantitlelight">
        <span>View : </span>
        <a href="plan_list.php" style="color:#000;text-decoration:none;"><span class="planfilters">Active Plans</span></a>
        <a href="pendingplans.php" style="color:#000;text-decoration:none;"><span class="planfilters">Pending Plans</span></a>
        <a href="deactivatedplans.php" style="color:#000;text-decoration:none;"><span class="planfilters">Deactivated Plans</span></a>
        <a href="assignedplans.php" style="color:#000;text-decoration:none;"><span class="planfilters active">Assigned Plans</span></a>
				</div>
              <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2 col-md-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">        
             <div class="table-responsive">
              <table class="table table-bordered">
              <tr class="tableheadings">
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <!--<th>Actions</th>-->
              </tr>
          <?php 
          //GET ASSIGNED PLANS
          $get_assigned_plans = mysql_query("select distinct t1.PlanCode,t3.UserID,t4.EmailID, concat('+', TRIM(LEADING '0' FROM t4.MobileNo)) as mobileno, t5.FirstName, t5.LastName, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2, USER_PLAN_MAPPING as t3, USER_ACCESS as t4, USER_DETAILS as t5 where t1.MerchantID = '$logged_merchantid' and t1.CategoryID = t2.CategoryID and t1.PlanCode = t3.PlanCode and t1.PlanCode = '$view_active_users_plancode' and t3.UserID=t4.UserID and t3.UserID = t5.UserID");
          //echo $get_assigned_plans;exit;
          $assigned_plans_count = mysql_num_rows($get_assigned_plans);
          $count = 0;
          if($assigned_plans_count > 0){
            while ($plan_details = mysql_fetch_array($get_assigned_plans)) {
               $count ++;
              $plan_code          = $plan_details['PlanCode'];
              $plan_name          = $plan_details['PlanName'];
              $plan_desc          = substr($plan_details['PlanDescription'], 0, 120);
              if(strlen($plan_desc)  >= 120){
                $plan_desc = $plan_desc."...";
              }
              if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
                  $plan_coverimg    = "uploads/planheader/".$plan_details['PlanCoverImagePath'];
              }else {
                  $plan_coverimg    = "uploads/planheader/default.jpg";
              }
              $plan_catname     = $plan_details['CategoryName'];
              $plan_catid       = $plan_details['CategoryID'];
              $plan_userid      = $plan_details['UserID'];
              $plan_email       = $plan_details['EmailID'];
              $plan_mobile      = $plan_details['mobileno'];
              $plan_fname       = $plan_details['FirstName'];
              $plan_lname       = $plan_details['LastName'];
              ?>
             <tr class="tablecontents activeuserslist" id="<?php echo $plan_userid;?>">
                <td><?php echo $count;?></td>
                <td><?php echo $plan_fname." ".$plan_lname;?></td>
                <td><?php echo $plan_email;?></td>
                <td><?php echo formatPhoneNumber($plan_mobile);?></td>
                <!--<td></td>-->
              </tr>
              <?php
            }
          } else {
            echo "<tr class='tablecontents'><td colspan='5'>No Active Users.</td></tr>";
          }

          //GET ALL PLANS UNDER THIS MERCHANT
         /* $get_all_plans = mysql_query("select distinct PlanCode, PlanName, PlanCoverImagePath from PLAN_HEADER where MerchantID='$logged_merchantid' and PlanStatus = 'A' order by CreatedDate desc");
          $get_all_plans_count = mysql_num_rows($get_all_plans);
          if($get_all_plans_count > 0){
            while ($all_plans = mysql_fetch_array($get_all_plans)) {
              $plan_code = $all_plans['PlanCode'];
              $plan_name = $all_plans['PlanName'];
              $plan_img  = "uploads/planheader/".$all_plans['PlanCoverImagePath'];

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
                    <div class="planboxname2"><?php echo $plan_name;?></div>
                    <div class="planboxnumusers"><?php echo $count;?> active users</div>
                  </div>
              <?php
                }
              }
            }
          } else {
             echo "<div style='color:#000;text-align:center;font-family:RalewayRegular;font-size:22px;'>You have no plans.</div>";
          }*/
        ?>
          </table>
            </div>
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
        var currentpage = "plans";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Plans");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);

        $('.activeuserslist').click(function(){
          var userid = $(this).attr('id');
          //alert(plancode);
          var plancode = '<?php echo $view_active_users_plancode?>';
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