<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
$plan_to_customize = $_SESSION['current_assigned_plan_code'];
$assigned_to_user  = $_SESSION['current_assigned_user_id'];
$plan_to_customize_name = $_SESSION['current_assigned_plan_name'];
if((isset($_REQUEST['id'])) && (!empty($_REQUEST['id']))){
	$presc_edit_id = $_REQUEST['id'];
	//echo $presc_edit_id;exit;
} else {
	//header("Location:plan_medication.php");
	$presc_edit_id = "1";
}
//GET DOCTOR SHORT HAND
$shorthand_options = "";
$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND");
$shorthand_count = mysql_num_rows($get_shorthand);
if($shorthand_count > 0){
  while ($shorthand = mysql_fetch_array($get_shorthand)) {
    $shorthand_id  = $shorthand['ID'];
    $shorthandname = $shorthand['ShortHand'];
    $shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
  }
}
$get_max_rowno = mysql_query("select max(RowNo) from USER_INSTRUCTION_DETAILS where `PlanCode` = '$plan_to_customize' and UserID='$assigned_to_user' and `PrescriptionNo` = '$presc_edit_id'");
		$get_max_count = mysql_num_rows($get_max_rowno);
			if($get_max_count > 0){
				while ($maxcount = mysql_fetch_array($get_max_rowno)) {
					$max_rowno = $maxcount['max(RowNo)'];
				}
			} else {
				$max_rowno = 0;
		}
//echo $max_rowno;exit;
if((isset($_REQUEST['prescriptionName']))&&(!empty($_REQUEST['prescriptionName']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	$prescription_name 			= mysql_real_escape_string(trim($_REQUEST['prescriptionName']));
	$doctor_name 				= mysql_real_escape_string(trim($_REQUEST['doctorName']));
	$medicationcount    		= mysql_real_escape_string(trim($_REQUEST['medicationcount'])); //Total number of medicines present on screen
	$usedpresciptioncount 		= mysql_real_escape_string(trim($_REQUEST['usedpresciptioncount'])); //row ids of medicines present
	$update_query 				= "update USER_INSTRUCTION_HEADER set `PrescriptionName` = '$prescription_name', `DoctorsName` = '$doctor_name', `UpdatedBy` = '$logged_userid' where `PlanCode` = '$plan_to_customize' and `PrescriptionNo` = '$presc_edit_id' and UserID='$assigned_to_user'";
	$update_header_run  		= mysql_query($update_query);
	$existingpresciptioncount 	= mysql_real_escape_string(trim($_REQUEST['existingpresciptioncount'])); //row ids of medicines present at the time of editing
	$usedmedids 				= explode(",", $usedpresciptioncount);
	$existingids 				= explode(",", $existingpresciptioncount);
	foreach ($usedmedids as $ids) {
				if($ids != ""){
				$medicinename = mysql_real_escape_string(trim($_REQUEST["medicine$ids"]));
					if($medicinename != ""){
					$when 		  = mysql_real_escape_string(trim($_REQUEST["when$ids"]));
					$specifictime = NULL;
					if($when == '16'){
						$specifictime = mysql_real_escape_string(trim($_REQUEST["specifictime$ids"]));
						$specifictime = date('H:i:s',strtotime($specifictime));
					}
					$instruction  = mysql_real_escape_string(trim($_REQUEST["instruction$ids"])); 
					$linkentered  = mysql_real_escape_string(trim($_REQUEST["linkentered$ids"]));
					$frequency 	  = mysql_real_escape_string(trim($_REQUEST["frequency$ids"]));
					if($frequency == "Weekly"){
						$frequencystring 	  = mysql_real_escape_string(trim($_REQUEST["selectedweekdays$ids"]));
					} else if($frequency == "Monthly"){
						$frequencystring 	  = mysql_real_escape_string(trim($_REQUEST["selectedmonthdays$ids"]));
					} else {
						$frequencystring = NULL;
					}
					$frequencystring 	  = rtrim($frequencystring,",");
					if($frequency == "Once"){
						$howlong 		= NULL;
						$howlongtype 	= NULL;
					} else {
						$howlong 		= mysql_real_escape_string(trim($_REQUEST["count$ids"]));
						$howlongtype 	= mysql_real_escape_string(trim($_REQUEST["countType$ids"]));
					}
					$iscritical = "N";
					if(isset($_REQUEST["critical$ids"])){
						$iscritical = mysql_real_escape_string(trim($_REQUEST["critical$ids"]));
					}
					if($iscritical != "Y"){
						$iscritical = "N";
					}
					$responserequired = "N";
					if(isset($_REQUEST["response$ids"])){
						$responserequired = mysql_real_escape_string(trim($_REQUEST["response$ids"]));
					}
					
					if($responserequired != "Y"){
						$responserequired = "N";
					}
					$startflag 		= mysql_real_escape_string(trim($_REQUEST["radio$ids"]));
					if($startflag == "PS"){
						$numberofdaysafterplan 	= NULL;
						$specific_date 			= NULL;
					}else if($startflag == "ND"){
						$numberofdaysafterplan 	= mysql_real_escape_string(trim($_REQUEST["numofdays$ids"]));
						$specific_date 			= NULL;
					} else if($startflag == "SD"){
						$numberofdaysafterplan 	= NULL;
						$specific_date 			= mysql_real_escape_string(trim($_REQUEST["specificdate$ids"]));
						$specific_date			= date('Y-m-d',strtotime($specific_date));
					}
				if(in_array($ids, $existingids)){
					//UPDATE ROWS
					$currentrowid =  mysql_real_escape_string(trim($_REQUEST["currentrowid$ids"]));
					$update_query = "update `USER_INSTRUCTION_DETAILS` set `MedicineName` = '$medicinename', `When` = '$when', `Instruction` = '$instruction',`Link` = '$linkentered', `Frequency` = '$frequency', `FrequencyString` = '$frequencystring', `HowLong` = '$howlong', `HowLongType` = '$howlongtype', `IsCritical` = '$iscritical', `ResponseRequired` = '$responserequired',`SpecificTime` = '$specifictime', `StartFlag` = '$startflag', `NoOfDaysAfterPlanStarts` = '$numberofdaysafterplan', `SpecificDate` = '$specific_date', `UpdatedBy` = '$logged_userid' where `PlanCode` = '$plan_to_customize' and `PrescriptionNo` = '$presc_edit_id' and `RowNo` = '$currentrowid' and UserID='$assigned_to_user'";
					$update_query_run = mysql_query($update_query);

				} else {
					//INSERT NEW rows
					$max_rowno++;
					$insert_medicine_details = "insert into USER_INSTRUCTION_DETAILS (`UserID`, `PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`When`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`SpecificTime`,`CreatedDate`,`CreatedBy`) values ('$assigned_to_user','$plan_to_customize', '$presc_edit_id', '$max_rowno', '$medicinename','$when','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date','$specifictime', now(), '$logged_userid');";
			//echo $insert_medicine_details;exit;
			$insert_header_run  	= mysql_query($insert_medicine_details);
				}
			}
		}
	}
		foreach ($existingids as $delid) {
		if($delid != ""){
			if(!in_array($delid, $usedmedids)){
				$delete_medicine_details = mysql_query("delete from USER_INSTRUCTION_DETAILS where `PlanCode` = '$plan_to_customize' and `PrescriptionNo` = '$presc_edit_id' and `RowNo` = '$delid' and UserID='$assigned_to_user'");
			}
		}
	}
	$rand = mt_rand(1,999);
  $update_header = mysql_query("update USER_PLAN_HEADER set PlanStatus = '$rand' where PlanCode='$plan_to_customize' and UserID = '$assigned_to_user'");
	header("location:customize_instruction.php");
}

?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Customize Instruction</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/ndatepicker.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link href="css/bootstrap-timepicker.min.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">

        </script>
    </head>
    <body id="wrapper">
    <div class="col-sm-2 paddingrl0"  style="display:none;" id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		<div id="planpiper_wrapper" class="fullheight">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<?php include_once('top_header.php');?>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<div id="plantitle"><?php echo $plan_to_customize_name;?>
<button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH CUSTOMIZING</button></div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
					<!--<li role="presentation" class="navbartoptabs"><a href="customize_medication.php">MEDICATION</a></li>
					<li role="presentation" class="navbartoptabs"><a href="customize_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="customize_labtest.php">LAB TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_diet.php">DIET</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>
					<li role="presentation" class="active navbartoptabs"><a href="customize_instruction.php">INSTRUCTION</a></li>-->
				</ul>
			</div>
			    <div  style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Instruction List</h6>
		    	</div>
		    	<div id="listItems">
		    	<button type="button" id="addItemButton" class="btns" style="display:none;">Add A Instruction<img src="images/addItem.png"></button>
		    		<?php 
		    		$get_plan_prescriptions 		= "select PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate from USER_INSTRUCTION_HEADER where PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'";
		    		$get_plan_prescriptions_run 	= mysql_query($get_plan_prescriptions);
		    		$get_plan_prescriptions_count 	= mysql_num_rows($get_plan_prescriptions_run);
		    		if($get_plan_prescriptions_count > 0){
			    		while ($get_presc_row = mysql_fetch_array($get_plan_prescriptions_run)) {
			    			$prescription_no   = $get_presc_row['PrescriptionNo'];
			    			$prescription_name = $get_presc_row['PrescriptionName'];
			    			$prescription_doc  = $get_presc_row['DoctorsName'];
			    			$prescription_date = date('d-M-Y',strtotime($get_presc_row['CreatedDate'])); 
			    			?>
			    			<button type="button" class="btns editmedicationbuttons" id="<?php echo $prescription_no; ?>">
				    			<span><?php echo $prescription_name;?></span><br>
				    			<span>Created on <?php echo $prescription_date;?></span>
				    		</button>
			    			<?php
			    		}		    			
		    		}
		    		?>
		    	</div>
		    	<!--<div id="listTemplates">
		    		<ul>
		    			<li><a href="#1">Template 1</a></li>
		    			<li><a href="#2">Template 2</a></li>
		    			<li><a href="#3">Template 3</a></li>
		    		</ul>
		    	</div>-->
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent">
	    	<div id="dynamicPagePlusActionBar">
	    		<form name="frm_customize_prescription" id="frm_customize_prescription" method="post" action="">
		    	<?php 
		    		if(isset($presc_edit_id)){
		    			$get_medheader_to_edit = mysql_query("select PrescriptionName, DoctorsName from USER_INSTRUCTION_HEADER where PrescriptionNo = '$presc_edit_id' and PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'");
		    			//echo $get_medheader_to_edit;exit;
		    			$medheader_num = mysql_num_rows($get_medheader_to_edit);
		    			if($medheader_num > 0) {
		    				while ($headerrow = mysql_fetch_array($get_medheader_to_edit)) {
		    					$prescription_name 	= $headerrow['PrescriptionName'];
		    					$doctor_name 		= $headerrow['DoctorsName']; 
		    				}
		    			}
		    		}
		    	?>
		    	      <div class="prescriptionNameBar">
				        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="background-color:#BFBFBF;">
				          <span>Instruction Template Name:</span>
				        <input type="text" name="prescriptionName" id="prescriptionName" placeholder="Template Name.." maxlength="25" title="Enter a name for the instruction" value="<?php echo $prescription_name; ?>">
				        </div>
				        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="background-color:#BFBFBF;visibility:hidden;">
				          <span>Doctor's Name:</span>
				        <input type="text" name="doctorName" id="doctorName" placeholder="Enter Doctor Name.." maxlength="25" title="Enter the name of the doctor here.." value="<?php echo $doctor_name;?>">
				        </div>
				    </div>
				          <span style="float:left;width:3%;" class='hidden-xs hidden-sm'>
					        <table id="pslno">
					          <!--<tr><th>#</th></tr>
					          <tr><td>1</td></tr>
					          <tr><td>2</td></tr>
					          <tr><td>3</td></tr>-->
					        </table>
					      </span>
					      <div class="table-responsive" style="float:left;width:95%;padding-bottom:100px;">
					      <table id="pdata" class="table table-striped">
					      <tr id="pheader1">
					          <th style="max-width:250px;background-color:#C9C9C9;">Instruction Name</th>
					          <th style="background-color:#C9C9C9;">When</th>
					          <th style="background-color:#C9C9C9;">Instruction</th>
					          <th style="background-color:#C9C9C9;">Frequency</th>
					          <th style="background-color:#C9C9C9;">Duration</th>
					          <th style="background-color:#C9C9C9;">Critical?</th>
					          <th style="background-color:#C9C9C9;">Response?</th>
					          <th style="background-color:#C9C9C9;">Start</th>
					          <th style="background-color:#C9C9C9;"></th>
					        </tr>
				   <?php 
				   	if(isset($presc_edit_id)){
				   		$med_count = 0;
				   		$med_count_string = "";
				   		$get_meddetails_to_edit = mysql_query("select `RowNo`, `MedicineName`, `When`, `Instruction`,`Link`, `Frequency`, `FrequencyString`, `Howlong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `SpecificTime` from USER_INSTRUCTION_DETAILS where PrescriptionNo = '$presc_edit_id' and PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'");
				   		$meddetails_num = mysql_num_rows($get_meddetails_to_edit);
				   		if($meddetails_num > 0){
				   			while ($detailsrow = mysql_fetch_array($get_meddetails_to_edit)) {
				   				$med_count++;
				   				$med_count_string 			.= $med_count.",";
				   				$rowno 						= $detailsrow['RowNo'];
				   				$medicinename 				= $detailsrow['MedicineName'];
				   				$when 						= $detailsrow['When'];
				   				$instruction 				= $detailsrow['Instruction'];
				   				$link 		 				= $detailsrow['Link'];
				   				$frequency 					= $detailsrow['Frequency'];
				   				$frequencystring 			= $detailsrow['FrequencyString'];
				   				$howlong					= $detailsrow['Howlong'];
				   				if($howlong == "0"){
				   					$howlong = "";
				   				}
				   				$howlongtype 				= $detailsrow['HowLongType'];
				   				$iscritical 				= $detailsrow['IsCritical'];
				   				$responserequired 			= $detailsrow['ResponseRequired'];
				   				$startflag 					= $detailsrow['StartFlag'];
				   				$noofdaysafterplanstarts 	= $detailsrow['NoOfDaysAfterPlanStarts'];
				   				if($noofdaysafterplanstarts == "0"){
				   					$noofdaysafterplanstarts = "";
				   				}
				   				$specific_date				= $detailsrow['SpecificDate'];
				   				if(($specific_date == "0000-00-00")||($specific_date == "")){
				   					$specific_date = "";
				   				} else {
				   					$specific_date				= date('d-M-Y',strtotime($specific_date));
				   				}
				   				$specific_time				= date('h:i A',strtotime($detailsrow['SpecificTime']));
				   				//echo $specific_date;exit;
				   		?>
		<tr>
          <td><textarea rows="2" name="medicine<?php echo $med_count;?>" id="medicine<?php echo $med_count;?>" placeholder="Instruction Name.." maxlength="250" title="Enter Instruction Name here"><?php echo $medicinename;?></textarea></td>
          <td>
            <select name="when<?php echo $med_count;?>" id="when<?php echo $med_count;?>" title="Select the instruction frequency" class='whenshorthand'>
              <option value="0" style="display:none;">select</option>
              <?php
             	 $shorthand_selected = "";
				$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND");
				$shorthand_count = mysql_num_rows($get_shorthand);
				if($shorthand_count > 0){
				  while ($shorthand = mysql_fetch_array($get_shorthand)) {
				    $shorthand_id  = $shorthand['ID'];
				    $shorthandname = $shorthand['ShortHand'];
				    if($when == $shorthand_id){
				    $shorthand_selected .= "<option value='$shorthand_id' selected>$shorthandname</option>";
					  } else {
					$shorthand_selected .= "<option value='$shorthand_id'>$shorthandname</option>"; 	
					  }
				} 
			}
			echo $shorthand_selected;
				?>
            </select>
          </td>
          <td>
            <select name="instruction<?php echo $med_count;?>" id="instruction<?php echo $med_count;?>" title="Select the timing"  <?php if($when == "16"){echo "disabled";}?>>
              <option value="0" style="display:none;">select</option>
              <option value="Before Food" <?php if($instruction == "Before Food"){echo "selected";}?>>Before Food</option>
              <option value="With Food" <?php if($instruction == "With Food"){echo "selected";}?>>With Food</option>
              <option value="After Food" <?php if($instruction == "After Food"){echo "selected";}?>>After Food</option>
              <option value="NA" <?php if($instruction == "NA"){echo "selected";}?>>Not Applicable</option>
            </select>
          </td>
          <td>
            <select name="frequency<?php echo $med_count;?>" id="frequency<?php echo $med_count;?>" title="Select the frequency" class="medfrequency">
              <option value="0" style="display:none;">select</option>
              <option value="Once" <?php if($frequency == "Once"){echo "selected";}?>>Once</option>
              <option value="Daily"	<?php if($frequency == "Daily"){echo "selected";}?>>Daily</option>
              <option value="Weekly" <?php if($frequency == "Weekly"){echo "selected";}?>>Weekly</option>
              <option value="Monthly"<?php if($frequency == "Monthly"){echo "selected";}?>>Monthly</option>
            </select>
          </td>
          <td>
            <div style="border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;">
            <input type="text" maxlength="2" name="count<?php echo $med_count;?>" id="count<?php echo $med_count;?>" style="width:35px;height:40px;" class="forminputs2 roundedinputs countbox" title="Enter the duration" value="<?php echo $howlong;?>" <?php if($frequency == "Once"){echo "disabled";}?>>
            <select  id="countType<?php echo $med_count;?>" name="countType<?php echo $med_count;?>" style="width:80px;float:right;height:40px;line-height:35px;" title="Enter the duration" <?php if($frequency == "Once"){echo "disabled";}?>>
              <option value="0" style="display:none;">select</option>
              <option value="Days"<?php if($howlongtype == "Days"){echo "selected";}?>>Days</option>
              <option value="Weeks"<?php if($howlongtype == "Weeks"){echo "selected";}?>>Weeks</option>
              <option value="Months"<?php if($howlongtype == "Months"){echo "selected";}?>>Months</option>
            </select>
            </div>
            
          </td>
          <td><input type="checkbox" name="critical<?php echo $med_count;?>" id="critical<?php echo $med_count;?>" title="Check this if the medicine is cricical" value="Y"<?php if($iscritical == "Y"){echo "checked";}?> class='criticalcheck'></td>
          <td><input type="checkbox" name="response<?php echo $med_count;?>" id="response<?php echo $med_count;?>" title="Check this if a response is required" value="Y"<?php if($responserequired == "Y"){echo "checked";}?>></td>
          <td style="text-align:left;font-size : 0.7em;">
            <input type="radio" name="radio<?php echo $med_count;?>" value="PS" class="radio<?php echo $med_count;?> prescriptionradio"<?php if($startflag == "PS"){echo "checked";}?>> When the plan Starts/Updates<br>
            <input type="radio" name="radio<?php echo $med_count;?>" value="ND" class="radio<?php echo $med_count;?> prescriptionradio"<?php if($startflag == "ND"){echo "checked";}?>> No. Of days after plan started&nbsp;
            <input type="text" size="6px" name="numofdays<?php echo $med_count;?>" id="numofdays<?php echo $med_count;?>" class="numofdays" maxlength="2" value="<?php echo $noofdaysafterplanstarts;?>"><br>
            <input type="radio" name="radio<?php echo $med_count;?>" value="SD" class="radio<?php echo $med_count;?> prescriptionradio" <?php if($startflag == "SD"){echo "checked";}?>> On Specific Date:&nbsp;
            <input type="text" size="10px" name="specificdate<?php echo $med_count;?>" id="specificdate<?php echo $med_count;?>" class='specificdate' value="<?php echo $specific_date;?>">
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $med_count;?>" title="Delete this row"></td>
        </tr>
        <tr>
          <td colspan="8" style="text-align:left;" align="left">
          <input type="text" name="linkentered<?php echo $med_count;?>" id="linkentered<?php echo $med_count;?>" value="<?php echo $link;?>" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)">
          <?php if($frequency == "Weekly"){ ?>
          		 <input type="text" name="selectedweekdays<?php echo $med_count;?>" id="selectedweekdays<?php echo $med_count;?>" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency" value='<?php echo $frequencystring;?>'>
          	<?php } else { 	?>
          		 <input type="hidden" name="selectedweekdays<?php echo $med_count;?>" id="selectedweekdays<?php echo $med_count;?>" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
          		<?php } ?>
          		<?php if($frequency == "Monthly"){ ?>
          		  <input type="text" name="selectedmonthdays<?php echo $med_count;?>" id="selectedmonthdays<?php echo $med_count;?>" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency" value='<?php echo $frequencystring;?>'>
          	<?php } else { 	?>
          		  <input type="hidden" name="selectedmonthdays<?php echo $med_count;?>" id="selectedmonthdays<?php echo $med_count;?>" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency" value='<?php echo $frequencystring;?>'>
          		<?php } ?> 
          		 <input type="hidden" name="currentrowid<?php echo $med_count;?>" id="currentrowid<?php echo $med_count;?>" value="<?php echo $rowno;?>">
         
          <?php if($when == "16"){?>
           <span class="specifictimetext" id='specifictimetext<?php echo $med_count;?>'>Enter Specific Time :- <span class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="specifictime<?php echo $med_count;?>" id="specifictime<?php echo $med_count;?>" class="specifictime" value="<?php echo $specific_time;?>" class="forminputs2" style="width:150px;" readonly></span></span></span>
           <?php } else {
           	?>
           	 <span style="display:none;" class="specifictimetext" id='specifictimetext<?php echo $med_count;?>'>Enter Specific Time :- <span class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="specifictime<?php echo $med_count;?>" id="specifictime<?php echo $med_count;?>" class="specifictime" value="" class="forminputs2" style="width:150px;" readonly></span></span></span>
           	<?php
           	} ?> </td>
        </tr>
				   				<?php
				   			}
				   		}

				   	}
				   ?>
				       <input type="hidden" name="usedpresciptioncount" id="usedpresciptioncount" value="<?php echo $med_count_string;?>">
				       <input type="hidden" name="existingpresciptioncount" id="existingpresciptioncount" value="<?php echo $med_count_string;?>">
    				   <input type="hidden" name="medicationcount" id="medicationcount" value="<?php echo $med_count;?>">
    				  
		    	</form>
	    	</div>
    	</div>
    </div>
        <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addMedicine" class="btns formbuttons">ADD AN INSTRUCTION</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">UPDATE</button>
      </div>
		</div>
		<!--SHOW WEEK DAY PICKER MODAL WINDOW-->
            <div class="modal" id="weekdaypicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Select Weekdays</h5>
                  </div>
                  <div class="modal-body weekdayoptions" align="center" style="padding-top:0px;background-color:#fff;padding-bottom:50px;">
                	
               	  </div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="weeklyselectedid" id="weeklyselectedid" value="0">
               	  <button class="smallbutton" id="weeklydaysselected">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF SHOW WEEK DAY PICKER MODAL WINDOW-->
     <!--SHOW MONTH DAY PICKER MODAL WINDOW-->
            <div class="modal" id="monthdaypicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Select Days</h5>
                  </div>
                  <div class="modal-body monthdayoptions" align="center" style="padding-top:0px;background-color:#fff;padding-bottom:50px;">
                	
               	  </div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="monthlyselectedid" id="monthlyselectedid" value="0">
               	  <button class="smallbutton" id="monthlydaysselected">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF SHOW MONTH DAY PICKER MODAL WINDOW-->
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootstrap-timepicker.js"></script>
		<script type="text/javascript" src="js/modernizr.js"></script>
		<script type="text/javascript" src="js/placeholders.min.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#4').addClass('active');
			mainHeader = 115;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight);
		  	//$('#listBar').css({height: listBarHeight});
		  	$('#dynamicPagePlusActionBar').css({height: listBarHeight});
		  	
			var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	$('.maincontent').height(available_height);

			var medicationcount = 0;
			$('#plapiper_pagename').html("Medication");
			$(document).on('focus', '.specificdate', function () {
				$(this).datepicker({
			        dateFormat: "dd-M-yy",
			        minDate: 0,
			        changeMonth: true,
			        changeYear: true,
			     });
			});
			$(document).on('focus', '.specifictime', function () {
				$(this).timepicker("show");
			});
			//ADD MEDICINE
				$('#addMedicine').click(function(){
		        medicationcount = medicationcount + 1;
		        propercount     = propercount + 1;
		        $('.deleterow').show();
		        var first = "<tr><td><textarea rows='2' name='medicine"+propercount+"' placeholder='Enter Instruction..' id='medicine"+propercount+"' title='Enter Instruction Name here' maxlength='250'></textarea></td><td><select name='when"+propercount+"' id='when"+propercount+"' title='Select the instruction frequency' class='whenshorthand'><option value='0' style='display:none;'>select</option>"+shorthand_options+"</select></td><td><select name='instruction"+propercount+"' id='instruction"+propercount+"' title='Select the timing'><option value='0' style='display:none;'>select</option><option value='Before Food'>Before Food</option><option value='With Food'>With Food</option><option value='After Food'>After Food</option><option value='NA'>Not Applicable</option></select></td><td><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the frequency' class='medfrequency'><option value='0' style='display:none;'>select</option><option value='Once'>Once</option><option value='Daily'>Daily</option><option value='Weekly'>Weekly</option><option value='Monthly'>Monthly</option></select></td><td><div style='border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:40px;' class='forminputs2 roundedinputs countbox' title='Enter the duration'><select class='form-control' id='countType"+propercount+"' name='countType"+propercount+"'  style='width:80px;float:right;height:40px;line-height:35px;'><option value='0' style='display:none;'>select</option><option value='Days'>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option></select></div></td><td><input type='checkbox' name='critical"+propercount+"' id='critical"+propercount+"' class='criticalcheck' title='Check this if the medicine is critical' value='Y'></td><td><input type='checkbox' name='response"+propercount+"' id='response"+propercount+"' class='responsecheck' title='Check this if a response is required' value='Y'></td><td style='text-align:left;font-size : 0.7em;'><input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<br><input type='radio' name='radio"+propercount+"' class='radio"+propercount+" prescriptionradio' value='ND'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays pointernone' maxlength='2'><br><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio'> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate pointernone'></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td colspan='8' style='text-align:left;' aling='left'><input type='text' name='linkentered"+propercount+"' id='linkentered"+propercount+"' class='forminputs2' title='Enter Link here' placeholder='Enter Link Here (Optional)'><input type='hidden' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' value='' class='forminputs2 editselectedweekday' readonly  title='Click here to edit frequency'><input type='hidden' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' value='' class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'><span style='display:none;' class='specifictimetext' id='specifictimetext"+propercount+"'>Enter Specific Time :- <span class='input-append bootstrap-timepicker'><span class='add-on'> <input type='text' name='specifictime"+propercount+"' id='specifictime"+propercount+"' class='specifictime forminputs2' value='' style='width:150px;' readonly></span></span></span></td></tr>";
		        var slno  = "<tr><td>"+medicationcount+"</td></tr>";
		        $('#pslno > tbody').append(slno);
		        $('#pdata > tbody').append(first);
		        var current_usedprescriptioncount = $('#usedpresciptioncount').val();
		        var new_usedprescriptioncount = current_usedprescriptioncount+propercount+",";
		        $('#usedpresciptioncount').val(new_usedprescriptioncount);
		        $('#medicationcount').val(medicationcount);
		      });
			//DELETE ROW
			var medicationcount = <?php echo $med_count;?>;
			var propercount 	= <?php echo $med_count;?>;
			var shorthand_options = "<?php echo $shorthand_options;?>";
		  $(document).on('click', '.deleterow', function () {
		    var deleted_row_id = $(this).attr('id');  
		    //this.parentNode.remove();
		    var medicine_name = $('#medicine'+deleted_row_id).val();
		   if(medicine_name.replace(/\s+/g, '') == ""){
		        $('#pslno tr:last').remove();
		      $(this).closest('tr').next().remove();//To remove the next tr
		      $(this).closest('tr').remove();
		      medicationcount = medicationcount - 1;
		      if(medicationcount == 1){
		        $('.deleterow').hide();
		      }
		      var deleted_usedprescription = deleted_row_id+",";
		      var current_usedprescriptioncount = $('#usedpresciptioncount').val();
		      var new_usedprescriptioncount  = current_usedprescriptioncount.replace(deleted_usedprescription, "");
		      $('#usedpresciptioncount').val(new_usedprescriptioncount);
		      $('#medicationcount').val(medicationcount);
		   } else {
		    var deleteconfirm = confirm("This instruction will be deleted. Click OK to continue.");
		    if(deleteconfirm == true){
		      $('#pslno tr:last').remove();
		      $(this).closest('tr').next().remove();//To remove the next tr
		      $(this).closest('tr').remove();
		      medicationcount = medicationcount - 1;
		      if(medicationcount == 1){
		        $('.deleterow').hide();
		      }
		      var deleted_usedprescription = deleted_row_id+",";
		      var current_usedprescriptioncount = $('#usedpresciptioncount').val();
		      var new_usedprescriptioncount  = current_usedprescriptioncount.replace(deleted_usedprescription, "");
		      $('#usedpresciptioncount').val(new_usedprescriptioncount);
		      $('#medicationcount').val(medicationcount);
		    } else {

		    }
		   }

		  });
			//ON CHANGE OF FREQUENCY RESTRICT DURATION INPUTS
			$(document).on('change', '.medfrequency', function () {
			   var freqid = $(this).attr('id');
			   var id  = freqid.replace("frequency", "");
			   //bootbox.alert(id);
			   var value = this.value;
			   //bootbox.alert(value);
			   if(value == "Once"){
			   	$('#count'+id).prop('disabled', true);
			   	$('#count'+id).css('opacity', '0.2');
				$('#countType'+id).prop('disabled', true);
				$('#countType'+id).css('opacity', '0.2');
				$('#selectedmonthdays'+id).attr('type', 'hidden');
				$('#selectedweekdays'+id).attr('type', 'hidden');
			   }else{
			   	$('#count'+id).prop('disabled', false);
			   	$('#count'+id).css('opacity', '1');
				$('#countType'+id).css('opacity', '1');
				$('#countType'+id).prop('disabled', false);
				if(value == "Daily"){
					$('#countType'+id).empty().append("<option value='0' style='display:none;'>select</option><option value='Days' selected>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option>");
					$('#selectedmonthdays'+id).attr('type', 'hidden');
					$('#selectedweekdays'+id).attr('type', 'hidden');
				}
				 else if(value == "Weekly"){
					var weekdayoptions = "<div class='btn-group' data-toggle='buttons'><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sun' class='weekdaycheck'> Sun</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Mon' class='weekdaycheck'> Mon</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Tue' class='weekdaycheck'> Tue</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Wed' class='weekdaycheck'> Wed</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Thu' class='weekdaycheck'> Thu</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Fri' class='weekdaycheck'> Fri</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sat' class='weekdaycheck'> Sat</label></div>";
					$('#weeklyselectedid').val(id);
					$('.weekdayoptions').html(weekdayoptions);
					$('#monthdaypicker').modal('hide');
					$('#weekdaypicker').modal('show');
					$('#countType'+id).empty().append("<option value='0' style='display:none;'>select</option><option value='Weeks' selected>Weeks</option><option value='Months'>Months</option>");
					$('#selectedweekdays'+id).attr('type', 'hidden');
				}
				else if(value == "Monthly"){

					$('#countType'+id).empty().append("<option value='0' style='display:none;'>select</option><option value='Months' selected>Months</option>");
					var monthdayoptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
					for(i = 1; i <= 28; i++){
						i=(i<10) ? '0'+i : i;
						monthdayoptions = monthdayoptions + "<label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='monthdaycheck' class='monthdaycheck' value='"+i+"'> "+i+"</label>";
					}
					monthdayoptions = monthdayoptions + "<label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 29</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 30</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 31</label></div>";
				
					$('#monthlyselectedid').val(id);
					$('.monthdayoptions').html(monthdayoptions);
					$('#weekdaypicker').modal('hide');
					$('#monthdaypicker').modal('show');
					$('#selectedmonthdays'+id).attr('type', 'hidden');
			   }
			}
			});
			//ON CLICK OF DONE BUTTON AFTER SELECTING THE WEEKDAYS
			$(document).on('click', '#weeklydaysselected', function () {
				var getcurrentid = $('#weeklyselectedid').val();
				var chkId = "";
				$('.weekdaycheck:checked').each(function() {
				  chkId += $(this).val() + ",";
				});
				//bootbox.alert(chkId);
				if(chkId == ""){
					bootbox.alert("Please select atleast one week day to continue..");
					return false;
				}
				$('#selectedweekdays'+getcurrentid).val(chkId);
				$('#weekdaypicker').modal('hide');
				$('#selectedmonthdays'+getcurrentid).attr('type', 'hidden');
				$('#selectedweekdays'+getcurrentid).attr('type', 'text');
				//bootbox.alert(2);
				return false; // TO PREVENT PAGE REFRESH
			});
			//ON CLICK OF DONE BUTTON AFTER SELECTING THE MONTH DAYS
			$(document).on('click', '#monthlydaysselected', function () {
				var getcurrentid = $('#monthlyselectedid').val();
				var chkId2 = "";
				$('.monthdaycheck:checked').each(function() {
				  chkId2 += $(this).val() + ",";
				});
				//bootbox.alert(chkId);
				if(chkId2 == ""){
					bootbox.alert("Please select atleast one day to continue..");
					return false;
				}
				$('#selectedmonthdays'+getcurrentid).val(chkId2);
				$('#monthdaypicker').modal('hide');
				$('#selectedweekdays'+getcurrentid).attr('type', 'hidden');
				$('#selectedmonthdays'+getcurrentid).attr('type', 'text');
				return false; // TO PREVENT PAGE REFRESH
			});
			//ON CLICK SHOW WEEK DAY MODAL WINDOW TO EDIT SELECTION
			$(document).on('click','.editselectedweekday', function(){
			   var editweekdayid = $(this).attr('id');
			   var id  = editweekdayid.replace("selectedweekdays", "");
			   //bootbox.alert(id);
			   var selectedweekdays = $('#selectedweekdays'+id).val();
			  // bootbox.alert(selectedweekdays);
			  var weekdayresult = selectedweekdays.split(',');
				var selectedweekdaygroup = "<div class='btn-group' data-toggle='buttons'><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sun' class='weekdaycheck'> Sun</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Mon' class='weekdaycheck' > Mon</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Tue' class='weekdaycheck'> Tue</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Wed' class='weekdaycheck'> Wed</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Thu' class='weekdaycheck'> Thu</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Fri' class='weekdaycheck'> Fri</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sat' class='weekdaycheck'> Sat</label></div>";
					$('#weeklyselectedid').val(id);
					$('.weekdayoptions').html(selectedweekdaygroup);
					$('.weekdaycheck').each(function() {
                    if(jQuery.inArray($(this).val(),weekdayresult) != -1){
                    	$(this).prop('checked',true);
                    	$(this).parents('label').addClass('active'); 
                    }
                	});
					$('#monthdaypicker').modal('hide');
					$('#weekdaypicker').modal('show');
			});
			//ON CHANGE OF FREQUENCY RESTRICT DURATION INPUTS
			$(document).on('change', '.whenshorthand', function () {
			   var whenid = $(this).attr('id');
			   var id  = whenid.replace("when", "");
			   //bootbox.alert(id);
			   var value = this.value;
			   if(value == "16"){
			   		$('#instruction'+id).prop('disabled', true);
			   		$('#instruction'+id).css('opacity', '0.2');
			   		$('#specifictimetext'+id).show();
			   		$('#specifictime'+id).show();
			   } else {
			   		$('#instruction'+id).prop('disabled', false);
			   		$('#instruction'+id).css('opacity', '1');
			   		$('#specifictimetext'+id).hide();
			   		$('#specifictime'+id).hide();
			   }
			});
			//ON CLICK SHOW MONTH DAY MODAL WINDOW TO EDIT SELECTION
			$(document).on('click','.editselectedmonthday', function(){
				var editmonthdayid = $(this).attr('id');
				var id  = editmonthdayid.replace("selectedmonthdays", "");
			   //bootbox.alert(id);
			   var selectedmonthdays = $('#selectedmonthdays'+id).val();
			  // bootbox.alert(selectedmonthdays);
			  var monthdayresult = selectedmonthdays.split(',');
			  var monthdayoptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
					for(i = 1; i <= 28; i++){
						i=(i<10) ? '0'+i : i;
				monthdayoptions = monthdayoptions + "<label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='monthdaycheck' class='monthdaycheck' value='"+i+"'> "+i+"</label>";
					}
				monthdayoptions = monthdayoptions + "<label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 29</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 30</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 31</label></div>";				
					$('#monthlyselectedid').val(id);
					$('.monthdayoptions').html(monthdayoptions);
					$('.monthdaycheck').each(function() {
                    if(jQuery.inArray($(this).val(),monthdayresult) != -1){
                    	$(this).prop('checked',true);
                    	$(this).parents('label').addClass('active'); 
                    }
                	});
					$('#weekdaypicker').modal('hide');
					$('#monthdaypicker').modal('show');
			});
			//ON CHANGE OF 'START' RADIO, DISABLE AND ENABLE INPUT FIELDS 
			$(document).on('change', '.prescriptionradio', function () {
					var radioname = $(this).attr('name');
					//bootbox.alert(radioname);
					var id  = radioname.replace("radio", "");
					var radiovalue = $(this).val();
					//bootbox.alert(radiovalue);
					if(radiovalue == "PS"){
						$('#numofdays'+id).addClass("pointernone");
						$('#specificdate'+id).addClass("pointernone");
					} else if(radiovalue == "ND"){
						$('#numofdays'+id).removeClass("pointernone");
						$('#specificdate'+id).addClass("pointernone");
					} else if(radiovalue == "SD"){
						$('#numofdays'+id).addClass("pointernone");
						$('#specificdate'+id).removeClass("pointernone");
					}
				});
				//ON CLICK OF SAVE BUTTON
				$(document).on('click', '#saveAndEdit', function () {
				var current_prescription_name = $('#prescriptionName').val();
				if(current_prescription_name.replace(/\s+/g, '') == ""){
					bootbox.alert("Please enter a name for this instruction.");
					$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#prescriptionName').focus();
							});
					$('#prescriptionName').val("");
					return false;
				}
				var numberOfPrescription = 0;
				var current_usedprescriptioncount = $('#usedpresciptioncount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedprescriptioncount.split(',');
				medicationcount = $('#medicationcount').val(); //TOTAL NUMBER OF MEDICINE FIELDS PRESENT CURRENTLY ON THE PAGE
				for (i = 0; i < medicationcount; ++i) {
				 	var medicinename = $('#medicine'+result[i]).val();

				 	if(!medicinename == ""){
				 		if(medicinename.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Instruction name cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#medicine'+result[i]).focus();
							});
					 		$('#medicine'+result[i]).val("");
							return false;
				 		}
				 		numberOfPrescription = numberOfPrescription + 1;
				 		var wheninput = $('#when'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if(wheninput == 0){
				 			bootbox.alert("Please select the instruction frequency");
				 			$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#when'+result[i]).focus();
							});
				 			return false;
				 		}
				 		if(wheninput == '16'){
				 			var specifictime = $('#specifictime'+result[i]).val();
				 			if(specifictime.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please enter the specific time");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
							    	$('#specifictime'+result[i]).focus();
								});
						 		$('#specifictime'+result[i]).val("");
								return false;
				 			}
				 		}
				 		var instructioninput = $('#instruction'+result[i]).val();
				 		//bootbox.alert(instructioninput);
				 		if((instructioninput == 0)&&(wheninput != '16')){
				 			bootbox.alert("Please select the timing");
				 			$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#instruction'+result[i]).focus();
							});
				 			return false;
				 		}
				 		var frequencyinput = $('#frequency'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if(frequencyinput == 0){
				 			bootbox.alert("Please select the frequency");
				 			$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#frequency'+result[i]).focus();
							});
				 			return false;
				 		}
				 		if(frequencyinput!= "Once"){
					 		var countinput = $('#count'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if((countinput == "")||(countinput == 0)||(countinput == "0")){
					 			bootbox.alert("Please enter the duration");
					 			$('.bootbox').on('hidden.bs.modal', function() { 
							    	$('#count'+result[i]).focus();
								});
					 			return false;
					 		}
					 		if(!$.isNumeric(countinput)){
				 				bootbox.alert("Please enter a numeric value");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#count'+result[i]).focus();
								});
					 			return false;
				 			}
					 		var countTypeinput = $('#countType'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if(countTypeinput == 0){
					 			bootbox.alert("Please select the duration");
					 			$('.bootbox').on('hidden.bs.modal', function() { 
							    	$('#countType'+result[i]).focus();
								});
					 			return false;
					 		}				 			
				 		}
				 		if(frequencyinput == "Weekly"){
				 			var selectedweekdays = $('#selectedweekdays'+result[i]).val();
				 			if(selectedweekdays.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please select atleast one week day to continue..");
				 				var weekdayoptions = "<div class='btn-group' data-toggle='buttons'><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sun' class='weekdaycheck'> Sun</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Mon' class='weekdaycheck'> Mon</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Tue' class='weekdaycheck'> Tue</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Wed' class='weekdaycheck'> Wed</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Thu' class='weekdaycheck'> Thu</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Fri' class='weekdaycheck'> Fri</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sat' class='weekdaycheck'> Sat</label></div>";
								$('#weeklyselectedid').val(result[i]);
								$('.weekdayoptions').html(weekdayoptions);
								$('#monthdaypicker').modal('hide');
								$('#weekdaypicker').modal('show');
								return false;
				 			}
				 		}
				 		if(frequencyinput == "Monthly"){
				 			var selectedmonthdays = $('#selectedmonthdays'+result[i]).val();
				 			if(selectedmonthdays.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please select atleast one day to continue..");
								var monthdayoptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
								for(j = 1; j <= 28; j++){
									monthdayoptions = monthdayoptions + "<label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='monthdaycheck' class='monthdaycheck' value='"+j+"'> "+j+"</label>";
								}
								monthdayoptions = monthdayoptions + "<label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 29</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 30</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 31</label></div>";
								$('#monthlyselectedid').val(result[i]);
								$('.monthdayoptions').html(monthdayoptions);
								$('#weekdaypicker').modal('hide');
								$('#monthdaypicker').modal('show');
				 				return false;
				 			}
				 		}
				 		var selected = $(".radio"+result[i]+":checked").val();
				 		//bootbox.alert(selected);
				 		if(selected == "PS"){

				 		} else if(selected == "ND"){
				 			var numofdaysentered = $('#numofdays'+result[i]).val();
				 			if((numofdaysentered == "")||(numofdaysentered == 0)||(numofdaysentered == "0")){
				 				bootbox.alert("Please enter the number of days");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
							    	$('#numofdays'+result[i]).focus();
								});
					 			return false;
				 			}
				 			if(!$.isNumeric(numofdaysentered)){
				 				bootbox.alert("Please enter a numeric value");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#numofdays'+result[i]).focus();
								});
					 			return false;
				 			}

				 		} else if(selected == "SD"){
				 			var specificdateentered = $('#specificdate'+result[i]).val();
				 			if(specificdateentered == ""){
				 				bootbox.alert("Please select the specific date");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
							    	$('#specificdate'+result[i]).focus();
								});
					 			return false;
				 			}
				 		}
				 		var linkentered = $('#linkentered'+result[i]).val();
						if(linkentered.replace(/\s+/g, '') == ""){

				 		} else {
				 			if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(linkentered)) {
							} else {
							  bootbox.alert("Please enter a valid url");
								$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#linkentered'+result[i]).focus();
								});
					 			return false;
							}
				 		}

				 	}
				}
				if(numberOfPrescription == 0){
					bootbox.alert("Please enter atleast one instruction to continue.");
					$('.bootbox').on('hidden.bs.modal', function() { 
						$('#medicine1').focus();
					});
					return false;
				}
				$('#frm_customize_prescription').submit();
			});
				//EDIT MEDICATION BUTTON CLICKED - FROM SIDE PANEL
		$('.editmedicationbuttons').click(function(){
			var prescid = $(this).attr('id');
				//bootbox.alert(prescid);
			window.location.href = "customize_instruction.php?id="+prescid;
		});
		$(document).on('change', '.criticalcheck', function () {
		   if($(this).is(":checked")) {
		      //bootbox.alert(1);
		      var criticalid = $(this).attr('id');
		      var id  = criticalid.replace("critical", "");
		      $( "#response"+id ).prop( "checked", true );
		      //bootbox.alert(id);
		      return;
		   } else {
		   	var criticalid = $(this).attr('id');
		      var id  = criticalid.replace("critical", "");
		      $( "#response"+id ).prop( "checked", false );
		      //bootbox.alert(id);
		      return;
		   }

		});
					//FINISHED ADDING
			$('#finished_adding').click(function(){
				window.location.href = "customize_plan.php";
			});
		var sidebarflag = 0;
        $('#topbar-leftmenu').click(function(){
	      if(sidebarflag == 1){
              $('#sidebargrid').hide("slow","swing");
              $('#activitylist').show("slow","swing");
              $('.maincontent').addClass("col-lg-10");
              sidebarflag = 0;
          } else {
              $('#sidebargrid').show("slow","swing");
              $('#activitylist').hide("slow","swing");
              $('.maincontent').removeClass("col-lg-10");
              $('.maincontent').removeClass("col-md-9");
              $('.maincontent').removeClass("col-sm-9");
              sidebarflag = 1;
          }
        });
		});
		</script>
    </body>
    <?php
    include('include/unset_session.php');
	?>
</html>