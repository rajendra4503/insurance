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
		//echo $assigned_to_user;exit;
		if($assigned_to_user >= 1){
			//MOVE ALL PLAN DETAILS TO USER TABLES
			//GET PLAN HEADER
			$get_plan_header = "select PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCurrencyCode, PlanCost, PlanCoverImagePath from PLAN_HEADER where PlanCode='$selectedplancode'";
			// /echo $get_plan_header;exit;
			$get_plan_header_run = mysql_query($get_plan_header);
			while ($plan_header = mysql_fetch_array($get_plan_header_run)) {
				$user_plan_code 		= $plan_header['PlanCode'];
				$user_merchant_id 		= $plan_header['MerchantID'];
				$user_category_id 		= $plan_header['CategoryID'];
				$user_plan_name 		= $plan_header['PlanName'];
				$user_plan_desc 		= $plan_header['PlanDescription'];
				$user_plan_status 		= $plan_header['PlanStatus'];
				$user_plan_currency		= $plan_header['PlanCurrencyCode'];
				$user_plan_cost			= $plan_header['PlanCost'];
				$user_plan_image 		= $plan_header['PlanCoverImagePath'];
				//INSERT TO USER_PLAN_HEADER
				$insert_to_user_plan_header = mysql_query("insert into USER_PLAN_HEADER (UserID, PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCurrencyCode, PlanCost, PlanCoverImagePath) values ('$user', '$user_plan_code', '$user_merchant_id', '$user_category_id', '$user_plan_name', '$user_plan_desc', '$user_plan_status', '$user_plan_currency', '$user_plan_cost', '$user_plan_image')");
			}
			//GET MEDICATION HEADER
			$get_medication_header = mysql_query("select PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy from MEDICATION_HEADER where PlanCode = '$selectedplancode'");
			$medication_count = mysql_num_rows($get_medication_header);
			if($medication_count > 0){
				while ($medication_header 	= mysql_fetch_array($get_medication_header)) {
					$user_mh_plan_code  	= $medication_header['PlanCode'];
					$user_mh_presc_no   	= $medication_header['PrescriptionNo'];
					$user_mh_presc_name 	= $medication_header['PrescriptionName'];
					$user_mh_doc_name   	= $medication_header['DoctorsName'];
					$user_mh_created_date  	= $medication_header['CreatedDate'];
					$user_mh_created_by  	= $medication_header['CreatedBy'];
					//INSERT INTO USER MEDICATION HEADER
			$insert_to_user_med_header = mysql_query("insert into USER_MEDICATION_HEADER (UserID, PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy) values ('$user', '$user_mh_plan_code', '$user_mh_presc_no', '$user_mh_presc_name', '$user_mh_doc_name', '$user_mh_created_date', '$user_mh_created_by')");		
				}
			} else {
				//NO MEDICATION ADDED FOR THIS PLAN
			}
			//GET MEDICATION DETAILS
			$get_medication_details = mysql_query("select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `When`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy` from MEDICATION_DETAILS where `PlanCode` = '$selectedplancode'");
			$med_details_count  = mysql_num_rows($get_medication_details);
			if($med_details_count > 0){
				while ($medication_details = mysql_fetch_array($get_medication_details)) {
					$user_md_plan_code  		= $medication_details['PlanCode'];
					$user_md_presc_no   		= $medication_details['PrescriptionNo'];
					$user_md_row_no		 		= $medication_details['RowNo'];
					$user_md_med_name   		= $medication_details['MedicineName'];
					$user_md_when		 		= $medication_details['When'];
					$user_md_instruction		= $medication_details['Instruction'];
					$user_md_frequency		 	= $medication_details['Frequency'];
					$user_md_freq_string		= $medication_details['FrequencyString'];
					$user_md_how_long		 	= $medication_details['HowLong'];
					$user_md_howlong_type		= $medication_details['HowLongType'];
					$user_md_iscritical		 	= $medication_details['IsCritical'];
					$user_md_respreqd		 	= $medication_details['ResponseRequired'];
					$user_md_startflag		 	= $medication_details['StartFlag'];
					$user_md_no_days		 	= $medication_details['NoOfDaysAfterPlanStarts'];
					$user_md_specific_date		= $medication_details['SpecificDate'];
					$user_md_created_date  		= $medication_details['CreatedDate'];
					$user_md_created_by  		= $medication_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_med_details = mysql_query("insert into USER_MEDICATION_DETAILS (`UserID`, `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `When`, 'Instruction', 'Frequency', 'FrequencyString', 'HowLong', 'HowLongType', 'IsCritical', 'ResponseRequired', 'StartFlag', 'NoOfDaysAfterPlanStarts', 'SpecificDate', 'CreatedDate','CreatedBy') values ('$user', '$user_md_plan_code', '$user_mh_presc_no', '$user_md_row_no', '$user_md_med_name', '$user_md_when', '$user_md_instruction','$user_md_frequency','$user_md_freq_string','$user_md_how_long','$user_md_howlong_type','$user_md_iscritical','$user_md_respreqd', '$user_md_startflag', '$user_md_no_days','$user_md_specific_date','$user_md_created_date','$user_md_created_by')");
				}
			} else {
				//NO MEDICATION ADDED FOR THIS PLAN
			}
			//GET APPOINTMENT HEADER
			$get_appointment_header = mysql_query("select PlanCode, AppointmentDate, CreatedDate, CreatedBy from APPOINTMENT_HEADER where PlanCode = '$selectedplancode'");
			$appointment_count = mysql_num_rows($get_appointment_header);
			if($appointment_count > 0){
				while ($appointment_header 	= mysql_fetch_array($get_appointment_header)) {
					$user_ah_plan_code  	= $appointment_header['PlanCode'];
					$user_ah_app_date   	= $appointment_header['AppointmentDate'];
					$user_ah_created_date  	= $appointment_header['CreatedDate'];
					$user_ah_created_by  	= $appointment_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_appo_header = mysql_query("insert into USER_APPOINTMENT_HEADER (UserID, PlanCode, AppointmentDate, CreatedDate, CreatedBy) values ('$user', '$user_ah_plan_code', '$user_ah_app_date', '$user_ah_created_date','$user_ah_created_by')");
				}
			} else {
				//NO APPOINTMENTS ADDED FOR THIS PLAN
			}
			//GET APPOINTMENT DETAILS
			$get_appointment_details = mysql_query("select `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `CreatedDate`, `CreatedBy` from APPOINTMENT_DETAILS where `PlanCode` = '$selectedplancode'");
			$appo_details_count  = mysql_num_rows($get_appointment_details);
			if($appo_details_count > 0){
				while ($appointment_details = mysql_fetch_array($get_appointment_details)) {
					$user_ad_plan_code  		= $appointment_details['PlanCode'];
					$user_ad_appo_date   		= $appointment_details['AppointmentDate'];
					$user_ad_appo_time		 	= $appointment_details['AppointmentTime'];
					$user_ad_appo_sname   		= $appointment_details['AppointmentShortName'];
					$user_ad_docname		 	= $appointment_details['DoctorsName'];
					$user_ad_requirements		= $appointment_details['AppointmentRequirements'];
					$user_ad_created_date		= $appointment_details['CreatedDate'];
					$user_ad_created_by			= $appointment_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_APPOINTMENT_DETAILS (`UserID`, `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, 'AppointmentRequirements', 'CreatedDate', 'CreatedBy') values ('$user', '$user_ad_plan_code', '$user_ad_appo_date', '$user_ad_appo_time', '$user_ad_appo_sname', '$user_ad_docname', '$user_ad_requirements','$user_ad_created_date','$user_ad_created_by')");
				}
			} else {
				//NO APPOINTMENTS ADDED FOR THIS PLAN
			}
			//GET SELF TEST HEADER
			$get_selftest_header = mysql_query("select PlanCode, SelfTestID, CreatedDate, CreatedBy from SELF_TEST_HEADER where PlanCode = '$selectedplancode'");
			$selftest_count = mysql_num_rows($get_selftest_header);
			if($selftest_count > 0){
				while ($selftest_header 	= mysql_fetch_array($get_selftest_header)) {
					$user_ah_plan_code  	= $selftest_header['PlanCode'];
					$user_ah_selftest_id   	= $selftest_header['SelfTestID'];
					$user_ah_created_date  	= $selftest_header['CreatedDate'];
					$user_ah_created_by  	= $selftest_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_selftest_header = mysql_query("insert into USER_SELF_TEST_HEADER (UserID, PlanCode, SelfTestID, CreatedDate, CreatedBy) values ('$user', '$user_ah_plan_code', '$user_ah_selftest_id', '$user_ah_created_date','$user_ah_created_by')");
				}
			} else {
				//NO SELF TEST ADDED FOR THIS PLAN
			}
			//GET SELF TEST DETAILS
			$get_selftest_details = mysql_query("select `PlanCode`, `SelfTestID`, `RowNo`, `TestName`, `DoctorsName`, `TestDescription`, `Instruction`, 'Frequency',`FrequencyString`, `HowLong`, `HowLongType`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy` from SELF_TEST_DETAILS where `PlanCode` = '$selectedplancode'");
			$selftest_details_count  = mysql_num_rows($get_selftest_details);
			if($selftest_details_count > 0){
				while ($selftest_details = mysql_fetch_array($get_selftest_details)) {
					$user_sd_plan_code  			= $selftest_details['PlanCode'];
					$user_sd_selftest_id   			= $selftest_details['SelfTestID'];
					$user_sd_self_row		 		= $selftest_details['RowNo'];
					$user_sd_self_name   			= $selftest_details['TestName'];
					$user_sd_docname		 		= $selftest_details['DoctorsName'];
					$user_sd_test_desc				= $selftest_details['TestDescription'];
					$user_sd_instruction			= $selftest_details['Instruction'];
					$user_sd_frequency				= $selftest_details['Frequency'];
					$user_sd_freq_string			= $selftest_details['FrequencyString'];
					$user_sd_howlong				= $selftest_details['HowLong'];
					$user_sd_howlong_type			= $selftest_details['HowLongType'];
					$user_sd_resp_reqd				= $selftest_details['ResponseRequired'];
					$user_sd_start_flag				= $selftest_details['StartFlag'];
					$user_sd_no_of_days				= $selftest_details['NoOfDaysAfterPlanStarts'];
					$user_sd_specific_date			= $selftest_details['SpecificDate'];
					$user_sd_created_date			= $selftest_details['CreatedDate'];
					$user_sd_created_by				= $selftest_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_SELF_TEST_DETAILS (`UserID`, `PlanCode`, `SelfTestID`, `RowNo`, `TestName`, `DoctorsName`, 'TestDescription', 'Instruction', 'Frequency','FrequencyString','HowLong', 'HowLongType', 'ResponseRequired', 'StartFlag', 'NoOfDaysAfterPlanStarts', 'SpecificDate', 'CreatedDate', 'CreatedBy') values ('$user', '$user_sd_plan_code', '$user_sd_selftest_id', '$user_sd_self_row', '$user_sd_self_name', '$user_sd_docname', '$user_sd_test_desc','$user_sd_instruction','$user_sd_frequency','$user_sd_freq_string','$user_sd_howlong','$user_sd_howlong_type','$user_sd_resp_reqd','$user_sd_start_flag','$user_sd_no_of_days','$user_sd_specific_date','$user_sd_created_date','$user_sd_created_by')");
				}
			} else {
				//NO SELF TEST ADDED FOR THIS PLAN
			}
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
		 		$get_active_plans = "select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2 where MerchantID = '$logged_merchantid' and t1.CategoryID = t2.CategoryID and t1.PlanStatus != 'P' order by t1.CreatedDate desc";
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
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
</body>
</html>