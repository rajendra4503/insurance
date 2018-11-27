<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>";print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php'); 
//$_SESSION['plancode_for_current_plan'] = "444444444444";
//$_SESSION['current_created_planname'] = "Diabetic Diet Plan";
$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
//$current_created_planname = $_SESSION['current_created_planname'];

$check_plancode_availability = mysql_query("select userID, PlanCode from USER_PLAN_MAPPING where PlanCode = '$plancode_for_current_plan'");
$check_plancode_count        = mysql_num_rows($check_plancode_availability);
//echo $check_plancode_count;exit;
if($check_plancode_count > 0){
	?>
	<script type="text/javascript">
		//bootbox.alert("This plan is already assigned to a user.");
		//window.location.href = "plan_list.php";
	</script>
	<?php
}


$get_plan_details = "select t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2  where PlanCode = '$plancode_for_current_plan' and t1.CategoryID = t2.CategoryID and t1.PlanStatus = 'A'";
//echo $get_plan_details;exit;
$get_plan_details_run = mysql_query($get_plan_details);
$get_plan_details_count = mysql_num_rows($get_plan_details_run);
		 		if($get_plan_details_count > 0){
		 			while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
		 				$plandet_name 		= $plan_details['PlanName'];
		 				$plandet_desc 		= substr($plan_details['PlanDescription'], 0, 120);
		 				if(strlen($plandet_desc) >= 120){
		 					$plandet_desc = $plandet_desc."...";
		 				}
		 				if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
							$plandet_path 		= "uploads/planheader/".$plan_details['PlanCoverImagePath'];
		 				} else {
		 					$plandet_path 		= "uploads/planheader/default.jpg";
		 				}
		 				
		 				$plandet_catg 		= $plan_details['CategoryName'];
		 				$plandet_cid		= $plan_details['CategoryID'];
		 				}
		 		} else {
		 			?>
		 			<script type="text/javascript">
						alert("Please select a plan");
						window.location.href = "plan_list.php";
					</script>
					<?php
		 		}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Assign To Patient</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/magicsuggest.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">
		function keychk(event)
		{
			//bootbox.alert(123)
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
	  	 <div class="col-sm-2 paddingrl0" id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-8 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
		 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
		 	<div id="pageheading">Assign this plan to a Patient</div>
		 	</div>
		 	<section>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="mainplanlistdiv">
								<form name="frm_assign_plan" id="frm_assign_plan" method="post" enctype="multipart/form-data" action="assign_to_user.php">
								<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:5%;margin-bottom:5%;">
									<div class="bigplanbox">
										<img src="<?php echo $plandet_path;?>" class="planboximg">
										<div class="blackoverlay"></div>
										<div class="planboxname"><?php echo $plandet_name; ?></div>
										<div class="planboxcatg"><?php echo $plandet_catg; ?></div>
										<div class="planboxdesc"><?php echo $plandet_desc;?></div>
									</div>
								</div>
									<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Select a Patient:
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<div id="magicsuggest" name="magicsuggest"></div>
										</div>
									</div>
		<?php
								    if($logged_usertype!='I')
								    {
								    ?>
									<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv margintop10">
											Doctor Name:
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12 margintop10">
										
												<?php 
												$doctorNameValue = "";
												$get_users      = "select SNo, Name from MERCHANT_STAFF where MerchantID = '$logged_merchantid' and Status = 'A' limit 1";
													// /echo $get_users;exit;
													$get_users_qry	= mysql_query($get_users);
													$get_user_count	= mysql_num_rows($get_users_qry);
													if($get_user_count)
													{
														while($rows = mysql_fetch_array($get_users_qry))
														{
															$name = $rows['Name'];
															$doctorNameValue = $name;
														}
													} else {
														$doctorNameValue = "";
													}
												?>
												<input type="text"  name="doctorName" id="doctorName" class="forminputs2" value="<?php echo $doctorNameValue;?>" maxlength='25' style='height:35px;border:1px solid #004f35;'>
										</div>
									</div>
									<?php
									} else {
										?>
											<input type="hidden"  name="doctorName" id="doctorName" class="forminputs2" value="<?php echo $logged_firstname;?>" maxlength='25' style='height:35px;border:1px solid #004f35;'>
										<?php
									}
									?>
									<!--<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:20px;">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Or
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<div class="addnewclient">Add a New Client</div>
										</div>
									</div>-->
									<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:10px;">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Select the Disease :
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<select class="forminputs2" name="diseasename" id="diseasename" style='height:35px;border:1px solid #004f35;'>
											<option value="0" style="display:none;">Select</option>
											<?php 
												$disease_query = mysql_query("select ID, Disease from MASTER_DISEASES order by Disease");
												$disease_count = mysql_num_rows($disease_query);
												if($disease_count > 0){
													while ($disease_row = mysql_fetch_array($disease_query)) {
														$disease_id 	= $disease_row['ID'];
														$disease_name 	= $disease_row['Disease'];
														echo "<option value='$disease_id'>$disease_name</option>";
													}
												}
											?>
												
											</select>
										</div>
									</div>
									<!-- <div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:10px;">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Diagnosis :
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<textarea class="forminputstextarea" id="diagnosis" name="diagnosis" rows="3" maxlength='500'></textarea>
										</div>
									</div> -->
									<div align="left" class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:5px;">
										<div class="col-lg-4 col-sm-4 col-md-12 col-xs-12 bigtextdiv">
											Medical Record :
										</div>
										<div class="col-lg-8 col-sm-8 col-md-12 col-xs-12">
											<textarea class="forminputstextarea" id="patientsummary" name="patientsummary" rows="3" maxlength='500'></textarea>
										</div>
									</div>
									<input type="hidden" name="plancodeselected" id="plancodeselected" value="">
								</form>
								<!--  style="position: fixed;bottom: 0;background-color: #fff;text-align: center;left:0;"-->
								 <div id="ActionBar2" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px;">
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
		 				$plan_name 		= stripslashes($active_plans['PlanName']);
		 				$plan_desc 		= stripslashes(substr($active_plans['PlanDescription'], 0, 63));
		 				if(strlen($plan_desc) >= 63){
		 					$plan_desc = $plan_desc."...";
		 				}
		 				$plan_path 		= $active_plans['PlanCoverImagePath'];
		 				$plan_catg 		= $active_plans['CategoryName'];
		 				if($plancode_for_current_plan == $plan_code){
		 				?>
		 				<div class="plandetails" style="background-color:#f2bd43;color:#004F35;">
							<span class='planH' style="height:40px;"><?php echo $plan_name;?></span>
							<p style="height:85px;margin-top:5px;"><?php echo $plan_desc;?></p>
							<span class='planC'><?php echo $plan_catg;?></span>
						</div>
		 				<?php 

		 				} else {
		 				?>
		 				<div class="plandetails">
							<span class='planH' style="height:40px;"><?php echo $plan_name;?></span>
							<p style="height:85px;margin-top:5px;"><?php echo $plan_desc;?></p>
							<span class='planC'><?php echo $plan_catg;?></span>
						</div>
		 				<?php 
		 				}

		 			}
		 		}
		 		else {
		 			echo "<div id='sidebartext'>No active plans. <a href='create_plan.php' style='color:#fff;border-bottom:1px dotted #fff;text-decoration:none;'>Add</a> a plan to assign.</div>";
		 		}
		 	?>
		 </div>
     		</div>
		</div>		
	<!-- Modal window to show the assigned plan code -->
		<div class="modal" id="showassignedplancode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  		<div class="modal-dialog" >
	    		<div class="modal-content modal-content-transparent">
	      			<div class="modal-header" align="center">
	      			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			 			<h4 class="modal-title"><span id="changepasswordtext">Plan Assigned</span></h4>
	      			</div>
	      			<div class="modal-body">
	      			</div>
	      			<div align="center" style="margin-top:15px;margin-bottom:30px;">
	      			<span class="plannamemodal"></span> <br>
	      			<span class="planassigned">is successfully assigned to</span> <br>
	      			<span class="planassignedto"></span> <br>
	      			<span class="plancode">Plan Code - <span id='assignedplancode'></span></span>
	      			</div>
	      			<div class="appointmentdateexpired" style="display:none;">
	      				<u>Note</u> : One or More Appointments in this plan are scheduled on past dates. 
	      				You can click on Customize Plan to change the dates.
	      			</div>
	      			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom:50px;background-color:#fff;" align="center">
						<button id="okbutton" class="formbuttonsmall">Back to Plan List</button>
						<button id="okbutton2" class="formbuttonsmall">Customize Plan</button>
					</div>
	    		</div>
	  		</div>
		</div>
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/magicsuggest.js"></script>
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
		     var windowheight = h;
	        var available_height = h - 120;
	        $('#mainplanlistdiv').height(available_height);

			 var currentpage = "assign";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("Assign");
    		        $('#magicsuggest').magicSuggest({
			        allowDuplicates: false,
			        allowFreeEntries: false,
			        name: 'magicsuggest',
			        data: 'ajax_get_users.php',
			        placeholder : 'Search for patients',
			        maxSelection : 1,
			        ajaxConfig: {
			            xhrFields: {
			            withCredentials: true,
			            }
			        }
			    });
			$('#assigntouser').click(function(){
				//bootbox.alert(123);
				if(!$('div.ms-sel-item').length){
	              alert("Please select a patient to continue");  
	              return false;
	            }
	            var doctorName = $('#doctorName').val();
				doctorName       = doctorName.replace(/ /g,''); //To check if the variable contains only spaces
		        if(doctorName == ''){
		            $('#doctorName').val('');
		            $('#doctorName').focus();
		           	alert("Please enter the doctor name to continue");  
		            return false;
		        }
		        var diseasename = $('#diseasename').val();
		        if(diseasename == 0){
		            $('#diseasename').focus();
		           	alert("Please select the disease to continue");  
		            return false;
		        }
	            $('#frm_assign_plan').submit();
			});
			$('#cancelbutton').click(function(){
				window.location.href = "plan_list.php";
			});
			$('#okbutton').click(function(){
				window.location.href = "plan_list.php";
			});
			$('#okbutton2').click(function(){
				window.location.href = "customize_plan.php";
			});
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
	<?php
	if(isset($_REQUEST['magicsuggest']) && (!empty($_REQUEST['magicsuggest']))){
		//print_r($_REQUEST['magicsuggest']);exit;
		$selectedplancode = $plancode_for_current_plan;
		$_SESSION['current_assigned_plan_code'] = $selectedplancode;
		$_SESSION['plancode_for_current_plan'] = $selectedplancode;
		$userarray = array();
		$userarray = $_REQUEST['magicsuggest'];
		$doctorsname 	= $_REQUEST['doctorName'];
		$patientsummary = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['patientsummary'])));
		$disease_id     = $_REQUEST['diseasename'];	
		foreach ($userarray as $user) {
		$_SESSION['current_assigned_user_id'] = $user;
		$_SESSION['userid_for_current_plan'] = $user;
		$get_user_merchant_link = mysql_query("select MerchantID, UserID, RoleID from USER_MERCHANT_MAPPING where MerchantID='$logged_merchantid' and UserID='$user' and RoleID='5'");
		$link_count = mysql_num_rows($get_user_merchant_link);
		if($link_count > 0){

		} else {
		$user_merchant_query = mysql_query("insert into `USER_MERCHANT_MAPPING` (`MerchantID`,`UserID`, `RoleID`, `Status`, `CreatedDate`, `CreatedBy`) VALUES ('$logged_merchantid', '$user', '5', 'A', now(), '$logged_userid')");			
		}

		$assign_to_user_query = mysql_query("insert into `USER_PLAN_MAPPING` (`UserID`, `PlanCode`, `DependencyID`, `Status`, `CreatedDate`, `CreatedBy`) VALUES ('$user', '$selectedplancode', '0', 'A', now(), '$logged_userid')");
		//echo $assign_to_user_query;exit;
		$assigned_to_user = mysql_affected_rows();
		//echo $assigned_to_user;exit;
		if($assigned_to_user > 0){
			//MOVE ALL PLAN DETAILS TO USER TABLES
			/*Insert Default Phone Settings (Wake Up Time,Breakfast Time,Lunch Time etc) to USER_PHONE_SETTINGS*/
			$check_user_exists 		= "select UserID from USER_PHONE_SETTINGS where UserID='$user'";
			$check_user_exists_query= mysql_query($check_user_exists);
			$check_user_exists_count= mysql_num_rows($check_user_exists_query);
			if($check_user_exists_count==0)
			{
				$insert_default_settings = "insert into USER_PHONE_SETTINGS 
											(UserID, WakeUp, Morning, BeforeBreakfast, WithBreakfast, AfterBreakfast, MorningSnack, BeforeLunch, WithLunch, AfterLunch, Afternoon, EveningSnack, BeforeTea, WithTea, AfterTea, Evening, BeforeDinner, WithDinner, AfterDinner, BeforeSleeping, NormalVolume, CriticalVolume, NormalMusicFileName, CriticalMusicFileName, CreatedDate, CreatedBy)
											select '$user', WakeUp, Morning, BeforeBreakfast, WithBreakfast, AfterBreakfast, MorningSnack, BeforeLunch, WithLunch, AfterLunch, Afternoon, EveningSnack, BeforeTea, WithTea, AfterTea, Evening, BeforeDinner, WithDinner, AfterDinner, BeforeSleeping, NormalVolume, CriticalVolume, NormalMusicFileName, CriticalMusicFileName,now(),'$logged_userid' from USER_PHONE_SETTINGS where UserID='111'";
				$insert_default_query 	 = mysql_query($insert_default_settings);
			}
			/*End of Insert Default Phone Settings (Wake Up Time,Breakfast Time,Lunch Time etc) to USER_PHONE_SETTINGS*/
			//GET PLAN HEADER
			$get_plan_header = "select PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCurrencyCode, PlanCost, PlanCoverImagePath from PLAN_HEADER where PlanCode='$selectedplancode'";
			// /echo $get_plan_header;exit;
			$get_plan_header_run = mysql_query($get_plan_header);
			while ($plan_header = mysql_fetch_array($get_plan_header_run)) {
				$user_plan_code 		= $plan_header['PlanCode'];
				$user_merchant_id 		= $plan_header['MerchantID'];
				$user_category_id 		= $plan_header['CategoryID'];
				$user_plan_name 		= mysql_real_escape_string(trim(htmlspecialchars($plan_header['PlanName'])));
				$user_plan_desc 		= mysql_real_escape_string(trim(htmlspecialchars($plan_header['PlanDescription'])));
				$user_plan_status 		= "A"; //ACTIVE PLAN 
				$user_plan_currency		= $plan_header['PlanCurrencyCode'];
				$user_plan_cost			= $plan_header['PlanCost'];
				$user_plan_image 		= $plan_header['PlanCoverImagePath'];
				//INSERT TO USER_PLAN_HEADER
				$insert_to_user_plan_header = mysql_query("insert into USER_PLAN_HEADER (UserID, PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCurrencyCode, PlanCost, PlanCoverImagePath,PatientSummary,DiseaseID,PlanUpdatedDate) values ('$user', '$user_plan_code', '$user_merchant_id', '$user_category_id', '$user_plan_name', '$user_plan_desc', '$user_plan_status', '$user_plan_currency', '$user_plan_cost', '$user_plan_image','$patientsummary','$disease_id',now())");
			}
			//GET MEDICATION HEADER
			$get_medication_header = mysql_query("select PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy from MEDICATION_HEADER where PlanCode = '$selectedplancode'");
			$medication_count = mysql_num_rows($get_medication_header);
			if($medication_count > 0){
				while ($medication_header 	= mysql_fetch_array($get_medication_header)) {
					$user_mh_plan_code  	= $medication_header['PlanCode'];
					$user_mh_presc_no   	= $medication_header['PrescriptionNo'];
					$user_mh_presc_name 	= mysql_real_escape_string(trim(htmlspecialchars($medication_header['PrescriptionName'])));
					$user_mh_doc_name   	= mysql_real_escape_string(trim(htmlspecialchars($medication_header['DoctorsName'])));
					$user_mh_created_date  	= $medication_header['CreatedDate'];
					$user_mh_created_by  	= $medication_header['CreatedBy'];
					//INSERT INTO USER MEDICATION HEADER
			$insert_to_user_med_header = mysql_query("insert into USER_MEDICATION_HEADER (UserID, PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy) values ('$user', '$user_mh_plan_code', '$user_mh_presc_no', '$user_mh_presc_name', '$doctorsname', '$user_mh_created_date', '$user_mh_created_by')");		
				}
			} else {
				//NO MEDICATION ADDED FOR THIS PLAN
			}
			//GET MEDICATION DETAILS
			$get_medication_details = mysql_query("select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`, `When`, `SpecificTime`, `Instruction`, `Link`, `OriginalFileName`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `ThresholdLimit`, `SpecificDate`, `CreatedDate`, `CreatedBy` from MEDICATION_DETAILS where `PlanCode` = '$selectedplancode'");
			$med_details_count  = mysql_num_rows($get_medication_details);
			if($med_details_count > 0){
				while ($medication_details = mysql_fetch_array($get_medication_details)) {
					$user_md_plan_code  		= $medication_details['PlanCode'];
					$user_md_presc_no   		= $medication_details['PrescriptionNo'];
					$user_md_row_no		 		= $medication_details['RowNo'];
					$user_md_med_name   		= mysql_real_escape_string(trim(htmlspecialchars($medication_details['MedicineName'])));
					$user_md_med_count  		= mysql_real_escape_string(trim(htmlspecialchars($medication_details['MedicineCount'])));
					$user_md_type_id		 	= $medication_details['MedicineTypeID'];
					$user_md_when		 		= $medication_details['When'];
					$user_md_sptime		 		= $medication_details['SpecificTime'];
					$user_md_instruction		= $medication_details['Instruction'];
					$user_md_link				= $medication_details['Link'];
					$user_md_ofn				= $medication_details['OriginalFileName'];
					$user_md_frequency		 	= $medication_details['Frequency'];
					$user_md_freq_string		= $medication_details['FrequencyString'];
					$user_md_how_long		 	= $medication_details['HowLong'];
					$user_md_howlong_type		= $medication_details['HowLongType'];
					$user_md_iscritical		 	= $medication_details['IsCritical'];
					$user_md_respreqd		 	= $medication_details['ResponseRequired'];
					$user_md_startflag		 	= $medication_details['StartFlag'];
					$user_md_no_days		 	= $medication_details['NoOfDaysAfterPlanStarts'];
					$user_md_threshold		 	= $medication_details['ThresholdLimit'];
					$user_md_specific_date		= $medication_details['SpecificDate'];
					$user_md_created_date  		= $medication_details['CreatedDate'];
					$user_md_created_by  		= $medication_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_med_details = mysql_query("insert into USER_MEDICATION_DETAILS (`UserID`, `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`, `When`, `SpecificTime`, `Instruction`,`Link`, `OriginalFileName`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `ThresholdLimit`,  `SpecificDate`, `CreatedDate`,`CreatedBy`) values ('$user', '$user_md_plan_code', '$user_md_presc_no', '$user_md_row_no', '$user_md_med_name', '$user_md_med_count', '$user_md_type_id', '$user_md_when','$user_md_sptime', '$user_md_instruction','$user_md_link', '$user_md_ofn', '$user_md_frequency','$user_md_freq_string','$user_md_how_long','$user_md_howlong_type','$user_md_iscritical','$user_md_respreqd', '$user_md_startflag', '$user_md_no_days', '$user_md_threshold', '$user_md_specific_date','$user_md_created_date','$user_md_created_by')");
					/*Ordinary Plan User Calculation*/

					if($mobile_type=='O')
					{
						$section_id 	= 1;
						include('ordinary_med_inst.php');
					}
					/*End of Ordinary Plan User Calculation*/
				}
			} else {
				//NO MEDICATION ADDED FOR THIS PLAN
			}

			//GET INSTRUCTION HEADER
			$get_instruction_header = mysql_query("select PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy from INSTRUCTION_HEADER where PlanCode = '$selectedplancode'");
			$instruction_count = mysql_num_rows($get_instruction_header);
			if($instruction_count > 0){
				while ($instruction_header 	= mysql_fetch_array($get_instruction_header)) {
					$user_ih_plan_code  	= $instruction_header['PlanCode'];
					$user_ih_presc_no   	= $instruction_header['PrescriptionNo'];
					$user_ih_presc_name 	= mysql_real_escape_string(trim(htmlspecialchars($instruction_header['PrescriptionName'])));
					$user_ih_doc_name   	= mysql_real_escape_string(trim(htmlspecialchars($instruction_header['DoctorsName'])));
					$user_ih_created_date  	= $instruction_header['CreatedDate'];
					$user_ih_created_by  	= $instruction_header['CreatedBy'];
					//INSERT INTO USER INSTRUCTION HEADER
			$insert_to_user_med_header = mysql_query("insert into USER_INSTRUCTION_HEADER (UserID, PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy) values ('$user', '$user_ih_plan_code', '$user_ih_presc_no', '$user_ih_presc_name', '$doctorsname', '$user_ih_created_date', '$user_ih_created_by')");		
				}
			} else {
				//NO INSTRUCTION ADDED FOR THIS PLAN
			}
			//GET INSTRUCTION DETAILS
			$get_instruction_details = mysql_query("select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `When`, `SpecificTime`, `InstructionTypeID`, `Instruction`,`Link`, `OriginalFileName`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `ThresholdLimit`, `SpecificDate`, `CreatedDate`, `CreatedBy` from INSTRUCTION_DETAILS where `PlanCode` = '$selectedplancode'");
			$med_details_count  = mysql_num_rows($get_instruction_details);
			if($med_details_count > 0){
				while ($instruction_details = mysql_fetch_array($get_instruction_details)) {
					$user_id_plan_code  		= $instruction_details['PlanCode'];
					$user_id_presc_no   		= $instruction_details['PrescriptionNo'];
					$user_id_row_no		 		= $instruction_details['RowNo'];
					$user_id_med_name   		= mysql_real_escape_string(trim(htmlspecialchars($instruction_details['MedicineName'])));
					$user_id_when		 		= $instruction_details['When'];
					$user_id_sptime		 		= $instruction_details['SpecificTime'];
					$user_id_insttid			= $instruction_details['InstructionTypeID'];
					$user_id_instruction		= $instruction_details['Instruction'];
					$user_id_link				= $instruction_details['Link'];
					$user_id_ofn				= $instruction_details['OriginalFileName'];
					$user_id_frequency		 	= $instruction_details['Frequency'];
					$user_id_freq_string		= $instruction_details['FrequencyString'];
					$user_id_how_long		 	= $instruction_details['HowLong'];
					$user_id_howlong_type		= $instruction_details['HowLongType'];
					$user_id_iscritical		 	= $instruction_details['IsCritical'];
					$user_id_respreqd		 	= $instruction_details['ResponseRequired'];
					$user_id_startflag		 	= $instruction_details['StartFlag'];
					$user_id_no_days		 	= $instruction_details['NoOfDaysAfterPlanStarts'];
					$user_id_threshold		 	= $instruction_details['ThresholdLimit'];
					$user_id_specific_date		= $instruction_details['SpecificDate'];
					$user_id_created_date  		= $instruction_details['CreatedDate'];
					$user_id_created_by  		= $instruction_details['CreatedBy'];
					//INSERT INTO USER INSTRUCTION DETAILS
					$insert_to_user_med_details = mysql_query("insert into USER_INSTRUCTION_DETAILS (`UserID`, `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `When`, `SpecificTime`, `InstructionTypeID`, `Instruction`,`Link`, `OriginalFileName`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `ThresholdLimit`, `SpecificDate`, `CreatedDate`,`CreatedBy`) values ('$user', '$user_id_plan_code', '$user_id_presc_no', '$user_id_row_no', '$user_id_med_name', '$user_id_when','$user_id_sptime', '$user_id_insttid', '$user_id_instruction','$user_id_link', '$user_id_ofn','$user_id_frequency','$user_id_freq_string','$user_id_how_long','$user_id_howlong_type','$user_id_iscritical','$user_id_respreqd', '$user_id_startflag', '$user_id_no_days', '$user_id_threshold', '$user_id_specific_date','$user_id_created_date','$user_id_created_by')");
					/*Ordinary Plan User Calculation*/
					//echo "Mobile Type ".$mobile_type;
					if($mobile_type=='O')
					{
						$section_id 	= 6;
						include('ordinary_med_inst.php');
					}
					/*End of Ordinary Plan User Calculation*/
				}
			} else {
				//NO INSTRUCTION ADDED FOR THIS PLAN
			}

			//exit;

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
			$get_appointment_details = mysql_query("select `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `AppointmentDuration`, `AppointmentPlace`, `CreatedDate`, `CreatedBy` from APPOINTMENT_DETAILS where `PlanCode` = '$selectedplancode'");
			$appo_details_count  = mysql_num_rows($get_appointment_details);
			if($appo_details_count > 0){
				while ($appointment_details = mysql_fetch_array($get_appointment_details)) {
					$user_ad_plan_code  		= $appointment_details['PlanCode'];
					$user_ad_appo_date   		= $appointment_details['AppointmentDate'];
					$user_ad_appo_time		 	= $appointment_details['AppointmentTime'];
					$user_ad_appo_sname   		= mysql_real_escape_string(trim(htmlspecialchars($appointment_details['AppointmentShortName'])));
					$user_ad_docname		 	= mysql_real_escape_string(trim(htmlspecialchars($appointment_details['DoctorsName'])));
					$user_ad_requirements		= mysql_real_escape_string(trim(htmlspecialchars($appointment_details['AppointmentRequirements'])));
					$user_ad_appo_durn			= $appointment_details['AppointmentDuration'];
					$user_ad_appo_place			= $appointment_details['AppointmentPlace'];
					$user_ad_created_date		= $appointment_details['CreatedDate'];
					$user_ad_created_by			= $appointment_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_APPOINTMENT_DETAILS (`UserID`, `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentDuration`, `AppointmentPlace`,  `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `CreatedDate`, `CreatedBy`) values ('$user', '$user_ad_plan_code', '$user_ad_appo_date', '$user_ad_appo_time', '$user_ad_appo_durn', '$user_ad_appo_place', '$user_ad_appo_sname', '$user_ad_docname', '$user_ad_requirements','$user_ad_created_date','$user_ad_created_by')");

						/*Ordinary Plan User Calculation*/
						//echo "Mobile Type ".$mobile_type;
						if($mobile_type=='O')
						{
							$section_id 	= "2";
							include('ordinary_appointment.php');
						}
						/*End of Ordinary Plan User Calculation*/
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
			$get_selftest_details = mysql_query("select `PlanCode`, `SelfTestID`, `RowNo`,`MedicalTestID`, `TestName`, `DoctorsName`, `TestDescription`, `Instruction`,`Link`, `OriginalFileName`, `Frequency`,`FrequencyString`, `HowLong`, `HowLongType`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy` from SELF_TEST_DETAILS where `PlanCode` = '$selectedplancode'");
			$selftest_details_count  = mysql_num_rows($get_selftest_details);
			if($selftest_details_count > 0){
				while ($selftest_details = mysql_fetch_array($get_selftest_details)) {
					$user_sd_plan_code  			= $selftest_details['PlanCode'];
					$user_sd_selftest_id   			= $selftest_details['SelfTestID'];
					$user_sd_self_row		 		= $selftest_details['RowNo'];
					$user_sd_self_mtid		 		= $selftest_details['MedicalTestID'];
					$user_sd_self_name   			= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['TestName'])));
					$user_sd_docname		 		= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['DoctorsName'])));
					$user_sd_test_desc				= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['TestDescription'])));
					$user_sd_instruction			= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['Instruction'])));
					$user_sd_link					= $selftest_details['Link'];
					$user_sd_ofn					= $selftest_details['OriginalFileName'];
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
					$insert_to_user_appo_details = mysql_query("insert into USER_SELF_TEST_DETAILS (`UserID`, `PlanCode`, `SelfTestID`, `RowNo`, `MedicalTestID`, `TestName`, `DoctorsName`, `TestDescription`, `InstructionID`,`Link`, `OriginalFileName`, `Frequency`,`FrequencyString`,`HowLong`, `HowLongType`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy`) values ('$user', '$user_sd_plan_code', '$user_sd_selftest_id', '$user_sd_self_row','$user_sd_self_mtid', '$user_sd_self_name', '$user_sd_docname', '$user_sd_test_desc','$user_sd_instruction','$user_sd_link', '$user_sd_ofn','$user_sd_frequency','$user_sd_freq_string','$user_sd_howlong','$user_sd_howlong_type','$user_sd_resp_reqd','$user_sd_start_flag','$user_sd_no_of_days','$user_sd_specific_date','$user_sd_created_date','$user_sd_created_by')");

					/*Ordinary Plan User Calculation*/
					//echo "Mobile Type ".$mobile_type;
					if($mobile_type=='O')
					{
						$section_id 	= "3-1";
						include('ordinary_self_test.php');
					}
					/*End of Ordinary Plan User Calculation*/
				}
			} else {
				//NO SELF TEST ADDED FOR THIS PLAN
			}
			//GET LAB TEST HEADER
			$get_labtest_header = mysql_query("select PlanCode, LabTestID, CreatedDate, CreatedBy from LAB_TEST_HEADER1 where PlanCode = '$selectedplancode'");
			$labtest_count = mysql_num_rows($get_labtest_header);
			if($labtest_count > 0){
				while ($labtest_header 	= mysql_fetch_array($get_labtest_header)) {
					$user_ah_plan_code  	= $labtest_header['PlanCode'];
					$user_ah_labtest_id   	= $labtest_header['LabTestID'];
					$user_ah_created_date  	= $labtest_header['CreatedDate'];
					$user_ah_created_by  	= $labtest_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_labtest_header = mysql_query("insert into USER_LAB_TEST_HEADER1 (UserID, PlanCode, LabTestID, CreatedDate, CreatedBy) values ('$user', '$user_ah_plan_code', '$user_ah_labtest_id', '$user_ah_created_date','$user_ah_created_by')");
				}
			} else {
				//NO LAB TEST ADDED FOR THIS PLAN
			}
			//GET LAB TEST DETAILS
			$get_labtest_details = mysql_query("select `PlanCode`, `LabTestID`, `RowNo`, `TestName`, `DoctorsName`, `LabTestRequirements`, `CreatedDate`, `CreatedBy` from LAB_TEST_DETAILS1 where `PlanCode` = '$selectedplancode'");
			$labtest_details_count  = mysql_num_rows($get_labtest_details);
			if($labtest_details_count > 0){
				while ($labtest_details = mysql_fetch_array($get_labtest_details)) {
					$user_sd_plan_code  			= $labtest_details['PlanCode'];
					$user_sd_labtest_id   			= $labtest_details['LabTestID'];
					$user_sd_self_row		 		= $labtest_details['RowNo'];
					$user_sd_self_name   			= mysql_real_escape_string(trim(htmlspecialchars($labtest_details['TestName'])));
					$user_sd_docname		 		= mysql_real_escape_string(trim(htmlspecialchars($labtest_details['DoctorsName'])));
					$user_sd_test_req				= mysql_real_escape_string(trim(htmlspecialchars($labtest_details['LabTestRequirements'])));
					$user_sd_created_date			= $labtest_details['CreatedDate'];
					$user_sd_created_by				= $labtest_details['CreatedBy'];
					//INSERT INTO USER LAB TEST DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_LAB_TEST_DETAILS1 (`UserID`, `PlanCode`, `LabTestID`, `RowNo`, `TestName`, `DoctorsName`, `LabTestRequirements`, `CreatedDate`, `CreatedBy`) values ('$user', '$user_sd_plan_code', '$user_sd_labtest_id', '$user_sd_self_row', '$user_sd_self_name', '$user_sd_docname', '$user_sd_test_req','$user_sd_created_date','$user_sd_created_by')");
				}
			} else {
				//NO LAB TEST ADDED FOR THIS PLAN
			}
			//GET DIET HEADER
			$get_diet_header = mysql_query("select PlanCode, DietNo, DietPlanName, AdvisorName, DietDurationDays, CreatedDate, CreatedBy from DIET_HEADER where PlanCode = '$selectedplancode'");
			$diet_count = mysql_num_rows($get_diet_header);
			if($diet_count > 0){
				while ($diet_header 	= mysql_fetch_array($get_diet_header)) {
					$user_dh_plan_code  	= $diet_header['PlanCode'];
					$user_dh_dietno   		= $diet_header['DietNo'];
					$user_dh_diet_plan  	= mysql_real_escape_string(trim(htmlspecialchars($diet_header['DietPlanName'])));
					$user_dh_advisor  		= mysql_real_escape_string(trim(htmlspecialchars($diet_header['AdvisorName'])));
					$user_dh_duration  		= $diet_header['DietDurationDays'];
					$user_dh_created_date  	= $diet_header['CreatedDate'];
					$user_dh_created_by  	= $diet_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_diet_header = mysql_query("insert into USER_DIET_HEADER (UserID, PlanCode, DietNo, DietPlanName, AdvisorName, DietDurationDays, CreatedDate, CreatedBy) values ('$user', '$user_dh_plan_code', '$user_dh_dietno', '$user_dh_diet_plan','$user_dh_advisor','$user_dh_duration','$user_dh_created_date','$user_dh_created_by')");
				}
			} else {
				//NO DIET ADDED FOR THIS PLAN
			}
			//GET DIET DETAILS
			$get_diet_details = mysql_query("select PlanCode, DietNo, DayNo, SNo, InstructionID, MealDescription, SpecificTime, CreatedDate, CreatedBy from DIET_DETAILS where PlanCode = '$selectedplancode'");
			$diet_details_count  = mysql_num_rows($get_diet_details);
			if($diet_details_count > 0){
				while ($diet_details = mysql_fetch_array($get_diet_details)) {
					$user_dd_plan_code  			= $diet_details['PlanCode'];
					$user_dd_diet_no	   			= $diet_details['DietNo'];
					$user_dd_day_no 		 		= $diet_details['DayNo'];
					$user_dd_sno         			= $diet_details['SNo'];
					$user_dd_instruction	 		= $diet_details['InstructionID'];
					$user_dd_meal_desc				= mysql_real_escape_string(trim(htmlspecialchars($diet_details['MealDescription'])));
					$user_dd_spec_time				= $diet_details['SpecificTime'];
					$user_dd_created_date			= $diet_details['CreatedDate'];
					$user_dd_created_by				= $diet_details['CreatedBy'];
					//INSERT INTO USER LAB TEST DETAILS
					$insert_to_user_diet_details = mysql_query("insert into USER_DIET_DETAILS (UserID, PlanCode, DietNo, DayNo, SNo, InstructionID, MealDescription, SpecificTime, CreatedDate, CreatedBy) values ('$user', '$user_dd_plan_code', '$user_dd_diet_no', '$user_dd_day_no', '$user_dd_sno', '$user_dd_instruction', '$user_dd_meal_desc','$user_dd_spec_time','$user_dd_created_date','$user_dd_created_by')");
				}
			} else {
				//NO DIET ADDED FOR THIS PLAN
			}
			//GET GOAL DETAILS
			$get_goal_header = mysql_query("select  PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate, CreatedBy from GOAL_DETAILS where PlanCode = '$selectedplancode'");
			$goal_count = mysql_num_rows($get_goal_header);
			if($goal_count > 0){
				while ($goal_header 	= mysql_fetch_array($get_goal_header)) {
					$user_g_plan_code  	= $goal_header['PlanCode'];
					$user_g_goal_no   	= $goal_header['GoalNo'];
					$user_g_goal_desc  	= $goal_header['GoalDescription'];
					$user_g_display   	= $goal_header['DisplayedWith'];
					$user_g_cdate  		= $goal_header['CreatedDate'];
					$user_g_cby   		= $goal_header['CreatedBy'];

					//INSERT INTO USER GOAL DETAILS
			$insert_to_user_goaldetails = mysql_query("insert into USER_GOAL_DETAILS (UserID, PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate, CreatedBy) values ('$user', '$user_g_plan_code', '$user_g_goal_no', '$user_g_goal_desc', '$user_g_display', '$user_g_cdate', '$user_g_cby')");		
				}
			} else {
				//NO GOALS ADDED FOR THIS PLAN
			}
			$plan_to_customize 	= $selectedplancode;
$assigned_to_user  	= $user;
$plan_name 		 	= $user_plan_name; 

if(isset($plan_to_customize))
{
//GET USER TO SEND NOTIFICATION
$get_user    =   "select UserID,OSType,DeviceID from USER_ACCESS where UserID='$assigned_to_user'";
//echo $get_user;exit;
$get_user_qry  = mysql_query($get_user);
$get_user_count= mysql_num_rows($get_user_qry);
	if($get_user_count)
	{//echo 123;exit;
		while($user_rows = mysql_fetch_array($get_user_qry))
		{
		$user_id  		= $user_rows['UserID'];
		$user_os_type  	= $user_rows['OSType'];
		$user_device_id	= $user_rows['DeviceID'];
		//echo $user_id;exit;
			if($user_id)
			{
				//Push notification for Android and IOS
				if($user_os_type=='A' && $user_device_id!='')
				{
				$regId          = $user_device_id;
				$res['message'] = "A new plan has been assigned to you - $plan_name.";
				$res['userid']  = $assigned_to_user;
				$res['flag']  	= "plan_update";
				$message        = json_encode($res); 
				include("gcm_server_php/send_message.php");
				}
				else if($user_os_type=='I' && $user_device_id!='')
				{
				$deviceToken= $user_device_id;
				//echo "<br>";
				$userid  	= $assigned_to_user;
				//echo "<br>";exit;
				$flag 		= "plan_update";
				$report_id   	= "";
				$message  = "A new plan has been assigned to you - $plan_name.";

				//echo "Token: ".$deviceToken."<br>"."UserID: ".$userid."<br>"."Flag: ".$flag."<br>"."Message: ".$message;exit;

				include("apple/local/push.php");
				//include("apple/production/push.php");
				}
			}
		}
	}
}
//END OF SEND NOTIFICATION
			//Check For Older Appointment Dates
			$old_appointment_flag = 0;
			$get_older_appointments = mysql_query("select * FROM USER_APPOINTMENT_DETAILS where UserID = '$user' and PlanCode = '$selectedplancode' and AppointmentDate < now()");
			$older_appointments_count = mysql_num_rows($get_older_appointments);
			if($older_appointments_count > 0){
				$old_appointment_flag = 1;
			}
				?>
				<script type="text/javascript">
				//bootbox.alert("Succesfully Assigned");
				var plancode = '<?php echo $selectedplancode;?>';
				var old_appointment_flag = '<?php echo $old_appointment_flag;?>';
				var dataString = "plancode="+plancode+"&type=get_plan_details";
				//bootbox.alert(dataString);
				$.ajax({
                  type    :"GET",
                  url     :"ajax_validation.php",
                  data    :dataString,
                  dataType  :"jsonp",
                  jsonp   :"jsoncallback",
                  async   :false,
                  crossDomain :true,
                  success   : function(data,status){
                    //bootbox.alert(status);
                    $.each(data, function(i,item){ 
                      $('.plannamemodal').html(item.PlanName);
                      $('#assignedplancode').html(item.PlanCode);
                      if(old_appointment_flag == "1"){
                      	$('.appointmentdateexpired').show();
                      } else {
                      	$('.appointmentdateexpired').hide();
                      }
                    });
                  },
                  error: function(){
                      
                  }
                });
                var userid = '<?php echo $user;?>';
                var dataString = "userid="+userid+"&type=get_user_details";
				//bootbox.alert(dataString);
				$.ajax({
                  type    :"GET",
                  url     :"ajax_validation.php",
                  data    :dataString,
                  dataType  :"jsonp",
                  jsonp   :"jsoncallback",
                  async   :false,
                  crossDomain :true,
                  success   : function(data,status){
                    //bootbox.alert(status);
                    $.each(data, function(i,item){ 
                      $('.planassignedto').html(item.FirstName+" "+item.LastName);
                    });
                  },
                  error: function(){
                      
                  }
                });
				$('#showassignedplancode').modal('show');
				</script>
				<?php
		} else {
			?>
		<script type="text/javascript">
		alert("This plan is already assigned to this patient.");
		</script>
		<?php
			}			
		}
	}
	 ?>
</body>
</html>