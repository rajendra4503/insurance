<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
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
	  	 <div class="col-sm-2 paddingrl0"  style="height:100%;" id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
     
      <!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
        <div id="plantitle">My Plans</div>
      </div>-->
		 	<section>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">My Plan Lists<a href="create_plan.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="right" id="finished_adding">Add New Plan</button></a></div></div>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="plantitlelight">
        <span>View : </span>
        <a href="plan_list.php" style="color:#000;text-decoration:none;"><span class="planfilters active">Active Plans</span></a>
        <a href="pendingplans.php" style="color:#000;text-decoration:none;"><span class="planfilters">Pending Plans</span></a>
        <a href="deactivatedplans.php" style="color:#000;text-decoration:none;"><span class="planfilters">Deactivated Plans</span></a>
        <a href="assignedplans.php" style="color:#000;text-decoration:none;"><span class="planfilters">Assigned Plans</span></a>
				</div>
              <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2 col-md-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">      
          <?php 
          //GET ACTIVE PLANS
          $get_active_plans = mysql_query("select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2 where MerchantID = '$logged_merchantid' and t1.CategoryID = t2.CategoryID and t1.PlanStatus = 'A' order by t1.CreatedDate desc");
          //echo $get_active_plans;exit;
          $active_plans_count = mysql_num_rows($get_active_plans);
          if($active_plans_count > 0){
            while ($plan_details = mysql_fetch_array($get_active_plans)) {
              $plan_code          = $plan_details['PlanCode'];
              $plan_name          = $plan_details['PlanName'];
              $plan_desc          = substr($plan_details['PlanDescription'], 0, 120);
              if(strlen($plan_desc)  >= 120){
                $plan_desc = $plan_desc."...";
              }
              
              $plan_coverimg    = "uploads/planheader/".$plan_details['PlanCoverImagePath'];
              $plan_catname     = $plan_details['CategoryName'];
              $plan_catid       = $plan_details['CategoryID'];
              ?>
             <div class="smallplanbox">
                    <img src="<?php echo $plan_coverimg;?>" class="planboximg">
                    <div class="blackoverlay"></div>
                    <div class="planboxname"><?php echo $plan_name;?></div>
                    <div class="planboxcatg"><?php echo $plan_catname;?></div>
                    <div class="planboxdesc"><?php echo $plan_desc;?></div>
                    <div class="onhoveroptions">
                      <div class="onhoveroption assignplanbutton" id="<?php echo $plan_code;?>">Assign</div>
                      <div class="onhoveroption editplanbutton" id="<?php echo $plan_code;?>">Edit</div>
                      <div class="onhoveroption deactivateplanbutton" id="<?php echo $plan_code;?>">Deactivate</div>
                    </div>
                  </div>
              <?php
            }
          } else {
            echo "<div style='color:#000;text-align:center;font-family:RalewayRegular;font-size:22px;'>You have no active plans.</div>";
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
        var currentpage = "plans";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Plans");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
        $('.smallplanbox').hover(function(){
          $('.onhoveroptions').hide();
          $('.onhoveroptions', $(this)).show(); 
        });
        //ASSIGN PLAN BUTTON CLICKED
        $('.assignplanbutton').click(function(){
          var plancode = $(this).attr('id');
          //alert(plancode);
          window.location.href = "ajax_validation.php?type=assign_to_user&plancode="+plancode;
        });
        //EDIT PLAN BUTTON CLICKED
        $('.editplanbutton').click(function(){
          var plancode = $(this).attr('id');
          //alert(plancode);
          window.location.href ='ajax_validation.php?type=edit_master_plan&plancode='+plancode;
        });
        //DEACTIVATE PLAN BUTTON CLICKED
        $('.deactivateplanbutton').click(function(){
          var plancode = $(this).attr('id');
          //alert(plancode);
          var deact = confirm("This plan will be deactivated. Click OK to continue");
          if(deact == true){
              var dataString = "type=deactivate_plan&plancode="+plancode;
                $.ajax({
              type    : 'POST', 
              url     : 'ajax_validation.php', 
              crossDomain : true,
              data    : dataString,
              dataType  : 'json', 
              async   : false,
              success : function (response)
                { 
                  alert("Plan successfully deactivated.");
                  window.location.href = "plan_list.php";
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
	});
	</script>
</body>
</html>