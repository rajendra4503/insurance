<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
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
	</head>
	<body style="overflow:hidden;">
	<div id="planpiper_wrapper">
	  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
	  	 <div class="col-sm-2 paddingrl0"  id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
		 	<section>
      <?php
      if($logged_usertype=='I')
      {
        $title = "Member List";
        $button="Add a Family Member";
      }
      else
      {
        $title = "Patient List";
        $button="Add a New Patient";
      }
      ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle"><span style="padding-left:165px;"><?php echo $title;?></span><a href="new_user.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="right" id="finished_adding"><?php echo $button;?></button></a></div></div>         
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">      
              <div class="table-responsive">
              <table class="table table-bordered">
              <tr class="tableheadings">
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Number Of Plans</th>
                <th>Review</th>
                <th>Edit</th>
                <th>Deact</th>
              </tr>
          <?php 
          //GET ADMINS
          $get_admins = mysql_query("select distinct t1.UserID,t2.FirstName, t2.Lastname, t1.EmailID, concat('+', TRIM(LEADING '0' FROM t1.MobileNo)) as mobileno from USER_ACCESS as t1, USER_DETAILS as t2, USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID and t2.UserID = t3.UserID and t3.RoleID = '5' and t3.Status = 'A' and t1.UserStatus = 'A' and t3.MerchantID = '$logged_merchantid' order by t2.FirstName");
          //echo $get_admins;exit;

          $admin_count = mysql_num_rows($get_admins);
          $count = 0;
          if($admin_count > 0){
            while ($admins = mysql_fetch_array($get_admins)) {
              $count ++;
              $user_id         = $admins['UserID'];
              $first_name      = stripslashes($admins['FirstName']);
              $last_name       = stripslashes($admins['Lastname']);
              $emailid         = $admins['EmailID'];
              $mobileno        = $admins['mobileno'];
              $get_plan_count = mysql_query("select count(*) from USER_PLAN_HEADER where UserID='$user_id' and MerchantID='$logged_merchantid' and PlanStatus = 'A'");
              $plan_row_count = mysql_num_rows($get_plan_count);
              if($plan_row_count > 0){
              	while ($plan_count = mysql_fetch_array($get_plan_count)) {
              		$user_plan_count = $plan_count['count(*)'];
              	}
              } else {
              	$user_plan_count = 0;
              }
              ?>
               <tr class="tablecontents" id="<?php echo $user_id; ?>" style="cursor:pointer;">
                <td id="<?php echo $user_id; ?>" class='viewactiveplans' title='Click to view active plans'><?php echo $count;?></td>
                <td style="text-align:left;padding-left:5px;" id="<?php echo $user_id; ?>" class='viewactiveplans' title='Click to view active plans'><?php echo $first_name." ".$last_name;?></td>
                <td style="text-align:left;padding-left:5px;" id="<?php echo $user_id; ?>" class='viewactiveplans' title='Click to view active plans'><?php echo $emailid;?></td>
                <td id="<?php echo $user_id; ?>" class='viewactiveplans' title='Click to view active plans'><?php echo formatPhoneNumber($mobileno);?></td>
                <td id="<?php echo $user_id; ?>" class='viewactiveplans' title='Click to view active plans'><u><?php echo $user_plan_count;?></u></td>
                <td id="<?php echo $user_id; ?>" title='Review User' class='reviewuser'><span class="glyphicon glyphicon-folder-open" aria-hidden="true" style='color:#CE8C11;'></span></td>
                <td id="<?php echo $user_id; ?>" title="Edit this user" class='editthisuser'><img src="images/edit3.png" style="height:20px;width:auto;"></td>
                <td id="<?php echo $user_id; ?>" title="Deactivate this user" class='deactivatethisuser'><img src="images/delete.png" style="height:20px;width:auto;"></td>
              </tr>
              <?php
            }
          } else {
             echo "<tr class='tablecontents'><td colspan='8'>No Patients</td></tr>";
          }
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
        var currentpage = "planusers";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Patients");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
        $('.viewactiveplans').click(function(){
        	var userid = $(this).attr('id');
          //bootbox.alert(plancode);
          window.location.href = "ajax_validation.php?type=view_active_plans&userid="+userid;
        });
        $('.editthisuser').click(function(){
          var userid = $(this).attr('id');
          //bootbox.alert(userid);
           window.location.href = "ajax_validation.php?type=edit_plan_user&userid="+userid;
         });
        $('.reviewuser').click(function(){
           var userid = $(this).attr('id');
           window.location.href = "ajax_validation.php?type=review_user&userid="+userid;
        });
        $('.deactivatethisuser').click(function(){
          var userid = $(this).attr('id');
          //bootbox.alert(userid);
          var merchantid = '<?php echo $logged_merchantid;?>';
          //bootbox.alert(merchantid);
          var deact = confirm("This patient will be deactivated. Click OK to continue");
          if(deact == true){
          var dataString = "type=deactivate_plan_user&userid="+userid+"&merchantid="+merchantid;
          $.ajax({
              type    : 'POST', 
              url     : 'ajax_validation.php', 
              crossDomain : true,
              data    : dataString,
              dataType  : 'json', 
              async   : false,
              success : function (response)
                { 
                  alert("Patient successfully deactivated.");
                  window.location.href = "plan_users.php";
                },
              error: function(error)
              {
                
              }
            }); 
          } else{
            
          }
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