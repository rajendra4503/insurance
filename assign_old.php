<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>";print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php'); 
if(isset($_REQUEST['plancodeselected']) && (!empty($_REQUEST['plancodeselected']))){

	//print_r($_REQUEST);exit;
	$selectedplancode = $_REQUEST['plancodeselected'];
	$userarray = array();
	$userarray = $_REQUEST['magicsuggest'];
	foreach ($userarray as $user) {
		$assign_to_user_query = mysql_query("insert into `USER_PLAN_MAPPING` (`UserID`, `PlanCode`, `DependencyID`, `Status`, `CreatedDate`, `CreatedBy`) VALUES ('$user', '$selectedplancode', '0', 'A', now(), '$logged_userid')");
		//echo $assign_to_user_query;exit;
		$assigned_to_user = mysql_affected_rows();
		$assign_to_merchant_query = mysql_query("insert into `USER_MERCHANT_MAPPING` (`MerchantID`, `UserID`, `RoleID`, `Status`, `CreatedDate`, `CreatedBy`) VALUES ('$logged_merchantid', '$user', '5', 'A', now(), '$logged_userid')");
		//echo $assign_to_user_query;exit;
		//$assigned_to_user = mysql_affected_rows();
		//echo $assigned_to_user;exit;
		if($assigned_to_user >= 1){
			?>
			<script type="text/javascript">
			alert("Succesfully Assigned");
			</script>
			<?php
		} else {
			?>
		<script type="text/javascript">
		alert("This plan is already assigned to this user.");
		</script>
		<?php
			}			
		}
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Assign To User</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/magicsuggest.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">
		function keychk(event)
		{
			//alert(123)
			if(event.keyCode==13)
			{
				$("#assigntouser").click();
			}
		}
	</script>
	</head>
	<body style="overflow:hidden;">
	<div id="planpiper_wrapper"class="fullheight">
	  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
	  	 <div class="col-sm-2 paddingrl0" style="height:100%;">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-8 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
		 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
		 	<div id="pageheading">Select a Plan from Plan List on the right & Assign it to a User</div>
			</div>
		 	<section>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<form name="frm_assign_plan" id="frm_assign_plan" method="post" enctype="multipart/form-data" action="assign_plan.php">
								<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:5%;margin-bottom:5%;">
									<div class="bigplanbox">
										<img src="" class="planboximg">
										<div class="blackoverlay" style="display:none;"></div>
										<div class="planboxname"></div>
										<div class="planboxcatg"></div>
										<div class="planboxdesc"></div>
										<div class="selectaplan">Select A Plan -></div>
									</div>
								</div>
									<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Select a User:
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<div id="magicsuggest" name="magicsuggest"></div>
										</div>
									</div>
									<!--<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:20px;">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Or
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<div class="addnewclient">Add a New Client</div>
										</div>
									</div>-->
									<input type="hidden" name="plancodeselected" id="plancodeselected" value="">
								</form>
								<!--  style="position: fixed;bottom: 0;background-color: #fff;text-align: center;left:0;"-->
								 <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:50px;">
							        <button type="button" id="assigntouser" class="btns formbuttonsmall">ASSIGN</button>
							        <button type="button" id="cancelbutton" class="btns formbuttonsmall">CANCEL</button>
							      </div>
								</div>
		 	</section>
		 </div>
		 <div class="col-sm-2 hidden-xs paddingrl0" id="planlistBar" style="height:100%;">
		 <div style="height:100%;overflow:scroll;overflow-x:hidden;">
		 	<div id="rightmenuheading">Plan List</div>
		 	<?php 
		 		$get_active_plans = "select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2 where MerchantID = '$logged_merchantid' and t1.CategoryID = t2.CategoryID and t1.PlanStatus = 'A' order by t1.CreatedDate desc";
		 		//echo $get_active_plans;exit;
		 		$get_active_plans_run = mysql_query($get_active_plans);
		 		$get_active_plans_count = mysql_num_rows($get_active_plans_run);
		 		if($get_active_plans_count > 0){
		 			while ($active_plans = mysql_fetch_array($get_active_plans_run)) {
		 				$plan_code 		= $active_plans['PlanCode'];
		 				$plan_name 		= $active_plans['PlanName'];
		 				$plan_desc 		= substr($active_plans['PlanDescription'], 0, 63);
		 				if(strlen($plan_desc) >= 63){
		 					$plan_desc = $plan_desc."...";
		 				}
		 				$plan_path 		= $active_plans['PlanCoverImagePath'];
		 				$plan_catg 		= $active_plans['CategoryName'];
		 				?>
		 				<div class="plandetails" id="<?php echo $plan_code;?>">
							<span class='planH' style="height:40px;"><?php echo $plan_name;?></span>
							<p style="height:85px;margin-top:5px;"><?php echo $plan_desc;?></p>
							<span class='planC'><?php echo $plan_catg;?></span>
						</div>
		 				<?php 
		 			}
		 		} else {
		 			echo "<div id='sidebartext'>No active plans. <a href='create_plan.php' style='color:#fff;border-bottom:1px dotted #fff;text-decoration:none;'>Add</a> a plan to assign.</div>";
		 		}
		 	?>
		 </div>
     		</div>
		</div>		
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/magicsuggest.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
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
			 var currentpage = "assign";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("Assign");
    		        $('#magicsuggest').magicSuggest({
			        allowDuplicates: false,
			        allowFreeEntries: false,
			        name: 'magicsuggest',
			        data: 'ajax_get_users.php',
			        placeholder : 'Search for users',
			        maxSelection : 1,
			        ajaxConfig: {
			            xhrFields: {
			            withCredentials: true,
			            }
			        }
			    });
			$('#assigntouser').click(function(){
				//alert(123);
				var selectedplancode = $('#plancodeselected').val();
				if(selectedplancode == ""){
					alert("Please select a plan to continue");  
	              	return false;
				}
				if(!$('div.ms-sel-item').length){
	              alert("Please select a user to continue");  
	              return false;
	            }
	            $('#frm_assign_plan').submit();
			});
			$('.plandetails').click(function(){
				var plancode = $(this).attr('id');
				$(".plandetails").css("color","#fff");
				$(".plandetails").css("background-color","#004f35");
				$(this).css("color","#004f35");
				$(this).css("background-color","#f2bd43");
				//alert(plancode);
				var dataString = "plancode="+plancode+"&type=get_plan_details";
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
                      $('.planboximg').attr("src",item.PlanCoverImagePath);
                      $('.blackoverlay').show();
                      $('.planboxname').html(item.PlanName);
                      $('.planboxcatg').html(item.CategoryName);
                      $('.planboxdesc').html(item.PlanDescription);
                      $('#plancodeselected').val(item.PlanCode);
                      $('.selectaplan').html('');
                    });
                  },
                  error: function(){
                      
                  }
                });
			});
			$('#cancelbutton').click(function(){
				window.location.href = "assign_plan.php";
			});

	});
	</script>
</body>
</html>