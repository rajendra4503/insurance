<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
$ua=getBrowser();
$browser_name = strtolower(str_replace(" ","",$ua['name']));

//$_SESSION['plancode_for_current_plan'] = "IN0000000030";
$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
$planname_for_current_plan_text = "Click to edit Plan Details";
$planname_for_current_plan = "";
$plandesc_for_current_plan = "";
//GET DOCTOR SHORT HAND
$shorthand_options = "";
$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
$shorthand_count = mysql_num_rows($get_shorthand);
if($shorthand_count > 0){
  while ($shorthand = mysql_fetch_array($get_shorthand)) {
    $shorthand_id  = $shorthand['ID'];
    $shorthandname = $shorthand['ShortHand'];
    $shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
  }
}

//GET MEDICINES
$det_medicineid 	= "";
$get_medicines 		= mysql_query("select MedicineID, GenericName, BrandName, Quantity, CompanyName, MedicineType from MEDICINE_LIST order by GenericName");
$medicine_count 	= mysql_num_rows($get_medicines);
$medicine_options 	= "";
if($medicine_count > 0){
	while ($medicines = mysql_fetch_array($get_medicines)) {
		$medicine_id 		= $medicines['MedicineID'];
		$generic_name 		= $medicines['GenericName'];
		$brand_name 		= $medicines['BrandName'];
		$company_name  		= $medicines['CompanyName'];
		$quantity 			= $medicines['Quantity'];
		$medicine_type    	= $medicines['MedicineType'];
		$display_name     	= $generic_name." - ".$brand_name;
      if($medicine_type == 2){
         $display_name     .= " (".$quantity." )";
      }
      $display_name     .= " - ".$company_name;
		if($det_medicineid == $medicine_id){
			$medicine_options 	.= "<option value='$medicine_id' selected>$display_name</option>";
		} else {
			$medicine_options 	.= "<option value='$medicine_id'>$display_name</option>";			
		}
	}
}
//echo "<pre>";print_r($medicine_options);exit;

if(isset($_SESSION['plancode_for_current_plan'])){
	$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
	//Get Plan Details
		if($plancode_for_current_plan != ""){
		$get_plan_details = "select PlanCode, PlanName, PlanDescription from PLAN_HEADER where PlanCode = '$plancode_for_current_plan'";
	//echo $get_plan_details;exit;
	$get_plan_details_run = mysql_query($get_plan_details);
	$get_plan_details_count = mysql_num_rows($get_plan_details_run);
			 		if($get_plan_details_count > 0){
			 			while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
			 				$plancode_for_current_plan 			= $plan_details['PlanCode'];
			 				$planname_for_current_plan 			= $plan_details['PlanName'];
			 				$planname_for_current_plan_text     = $plan_details['PlanName'];
			 				$plandesc_for_current_plan 			= $plan_details['PlanDescription'];
			 				}
			 		} else {

			 		}
	}
}
$plan_to_customize="";

if((isset($_REQUEST['prescriptionName']))&&(!empty($_REQUEST['prescriptionName']))){
	//echo "<pre>";print_r($_POST);exit;
	$prescription_name 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['prescriptionName'])));
	$doctor_name 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['doctorName'])));
	$medicationcount    		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['medicationcount']))); //Total number of medicines present on screen
	$usedpresciptioncount 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['usedpresciptioncount']))); //row ids of medicines present
	$medicineids 				= explode(",", $usedpresciptioncount);
	$delete_presc_ 	= mysql_query("delete from MEDICATION_HEADER where PlanCode = '$plancode_for_current_plan'");
	$delete_all_prescriptions 	= mysql_query("delete from MEDICATION_DETAILS where PlanCode = '$plancode_for_current_plan'");
	$get_last_prescription_num 	= mysql_query("select max(PrescriptionNo) from MEDICATION_HEADER where PlanCode = '$plancode_for_current_plan'");
	$presc_count 				= mysql_num_rows($get_last_prescription_num);
	if($presc_count > 0){
		while($last_presc_num 		= mysql_fetch_array($get_last_prescription_num)){
			$last_prescription 		= (empty($last_presc_num['max(PrescriptionNo)'])) 		? 0 : $last_presc_num['max(PrescriptionNo)'];
		}
	} else {
			$last_prescription      = 0;
	}
	//echo $last_prescription;exit;
	//print_r($medicineids);exit;
	$current_presc_num 		= $last_prescription + 1;
	$insert_header_details 	= " insert into MEDICATION_HEADER (Plancode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy,AssignedDate,AssignedBy,UpdatedBy) values ('$plancode_for_current_plan', '$current_presc_num', '$prescription_name', '$doctor_name', now(), '$logged_userid',now(),'','')";
	//echo $insert_header_details;exit;
	$insert_header_run  	= mysql_query($insert_header_details);
    $count = 1;
	foreach ($medicineids as $ids) {
		if($ids != ""){
			$medicinename = ucfirst(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["medicine$ids"]))));
			if($medicinename != ""){
			$when 		  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["when$ids"])));
			$threshold    = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["threshold$ids"])));
			$specifictime = NULL;
			if($when != '16'){
				$instruction  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["instruction$ids"])));
			}else {
				$instruction  = "0";
			}
			//$linkentered  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["linkentered$ids"])));
			$frequency 	  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["frequency$ids"])));
			if($frequency == "Weekly"){
				$frequencystring 	  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selectedweekdays$ids"])));
			} else if($frequency == "Monthly"){
				$frequencystring 	  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selectedmonthdays$ids"])));
			} else {
				$frequencystring = NULL;
			}
			$frequencystring 	  = rtrim($frequencystring,",");
			if($frequency == "Once"){
				$howlong 		= NULL;
				$howlongtype 	= NULL;
			} else {
				$howlong 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["count$ids"])));
				$howlongtype 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["countType$ids"])));
			}
				$medcount 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["medcount$ids"])));
				$medcount_type 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["medcountType$ids"])));
			$iscritical = "N";
			if(isset($_REQUEST["critical$ids"])){
				$iscritical = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["critical$ids"])));
			}
			if($iscritical != "Y"){
				$iscritical = "N";
			}
			$responserequired = "N";
			if(isset($_REQUEST["response$ids"])){
				$responserequired = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["response$ids"])));
			}
			if($responserequired != "Y"){
				$responserequired = "N";
			}
			$startflag 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["radio$ids"])));
			if($startflag == "PS"){
				$numberofdaysafterplan 	= NULL;
				$specific_date 			= NULL;
			}else if($startflag == "ND"){
				$numberofdaysafterplan 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["numofdays$ids"])));
				$specific_date 			= NULL;
			} else if($startflag == "SD"){
				$numberofdaysafterplan 	= NULL;
				$specific_date 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["specificdate$ids"])));
				$specific_date			= date('Y-m-d',strtotime($specific_date));
			}
			// //Checking if the entered medicine already exists in the medicine database.
			// $medicine_exist_query = mysql_query("select MedicineName from MERCHANT_MEDICINE_LIST where MedicineName = '$medicinename' and MerchantID in ('0', '$logged_merchantid')");
			// $medicine_exists = mysql_num_rows($medicine_exist_query);
			// if($medicine_exists > 0){

			// } else {
			// 	//If no, add
			// 	$insert_new_medicine = mysql_query("insert into MERCHANT_MEDICINE_LIST(`MedicineName`, `MerchantID`, `CreatedDate`, `CreatedBy`) values('$medicinename', '$logged_merchantid',  now(), '$logged_userid')");
			// }

				//File Upload
				if($_FILES["uploadedfile$ids"]["error"]==0)
				{
					$uploadedfile        	= (empty($_FILES["uploadedfile$ids"]["name"])) ? ''    : $_FILES["uploadedfile$ids"]["name"];
					$path               	= "uploads/files/";
			        //echo $path;exit;
					if(!is_dir($path))
					{
					mkdir($path, 0777, true);
					}
					//$originalfilename = $uploadedfile;	
					if($uploadedfile)
					{
					$date          = time().mt_rand(1000,9999);
					$imgtype       = explode('.', $uploadedfile);
					$ext           = end($imgtype);
					$filename      = $imgtype[0];
					$fullfilename  = $date.".".$ext;
					$fullpath      = $path . $fullfilename;
					move_uploaded_file($_FILES["uploadedfile$ids"]["tmp_name"], $fullpath);
					}
				}
				else
				{
					$fullpath 	= $_REQUEST["previouslink$ids"];
					$filename 	= $_REQUEST["originalfilename$ids"];
				}
				//End of File Upload

			if($when == '16'){
				$specifictime = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["specifictime$ids"])));
				//$specifictime = date('H:i:s',strtotime($specifictime));
				/*$starray = array();
				$starray = explode(",",$specifictime);
				//print_r($starray);exit;
			foreach ($starray as $st) {
			if($st != ""){
				$stime = date('H:i:s',strtotime($st));
				$insert_medicine_details = "insert into MEDICATION_DETAILS (`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`MedicineCount`,`MedicineTypeID`,`When`,`SpecificTime`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$medcount','$medcount_type','$when','$stime','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
				//echo $insert_medicine_details;
				$insert_header_run  	= mysql_query($insert_medicine_details);
				$count++;
			}

		}*/
		$insert_medicine_details = "insert into MEDICATION_DETAILS (`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`ThresholdLimit`,`MedicineCount`,`MedicineTypeID`,`When`,`SpecificTime`,`Instruction`, `Link`, `OriginalFileName`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$threshold','$medcount','$medcount_type','$when','$specifictime','$instruction','$fullpath','$filename','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";


		
		//$insert_medicine_details = "insert into MEDICATION_DETAILS (`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`MedicineCount`,`MedicineTypeID`,`When`,`SpecificTime`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$medcount','$medcount_type','$when','$specifictime','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
				//echo $insert_medicine_details;
				$insert_header_run  	= mysql_query($insert_medicine_details);
				$count++;
		//exit;
} else {
	//$specifictime = date('H:i:s',strtotime($specifictime));

      //only for test
      if($numberofdaysafterplan ==''){
      	$numberofdaysafterplan = 0;
      }
      if($specific_date==''){
      	$specific_date = '0000-00-00';
      }
     
	$insert_medicine_details = "insert into MEDICATION_DETAILS (`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`ThresholdLimit`,`MedicineCount`,`MedicineTypeID`,`When`,`Instruction`,`Link`, `OriginalFileName`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`,`UpdatedBy`) values ('$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$threshold','$medcount','$medcount_type','$when','$instruction','$fullpath','$filename','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid','')";

	//$insert_medicine_details = "insert into MEDICATION_DETAILS (`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`MedicineCount`,`MedicineTypeID`,`When`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$medcount','$medcount_type','$when','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
			//echo $insert_medicine_details;exit;
			$insert_header_run  	= mysql_query($insert_medicine_details);

}
		$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       {
		       	header("Location:plan_med_new.php");
		       } else {
		       	header("Location:plan_med_new.php");
		       }
		}
	}
		$count++;
	}
}

	mysql_set_charset('utf8');
	if( !empty( $_REQUEST['lang']) && $_REQUEST['lang'] != '' ){
		$table = $_REQUEST['lang'];
		if(table_exists($table)){
			$query  = mysql_query("SELECT FieldNo ,$table  FROM $table WHERE ScreenNo='011'");
			while ( $result = mysql_fetch_assoc($query) ) {
				${$result['FieldNo']} = $result[$table];
			} 
		}
		$_SESSION['LANGUAGE'] = $_REQUEST['lang'];
	}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Plan Piper | Medication</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/jasny-bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/ndatepicker.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-timepicker.min.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>
        <script type="text/javascript">
		    var imageflag = 0;
		    var flag = 0;
		    var flag2 = 0;
		      function changeimage(id){
		        imageflag = "arrow"+id;
		        if(flag != 0){
		          if(imageflag == flag) {
		          document.getElementById(imageflag).src = "images/rightarrow.png";
		          flag2 = 1;
		        } else if(imageflag != flag){
		           document.getElementById(flag).src = "images/rightarrow.png";
		           document.getElementById(imageflag).src = "images/downarrow.png";
		            } 
		      } else {
		        document.getElementById(imageflag).src = "images/downarrow.png";
		      }
		      if(flag2 != 1){
		        flag = "arrow"+id;
		      } else {
		        flag = 0;
		      }
		      flag2 = 0;
		      }
        </script>
    </head>
    <body id="wrapper">
    <div class="col-sm-2 paddingrl0"  style="display:none;" id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		<div id="planpiper_wrapper" class="fullheight" class="col-sm-10 paddingrl0">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<?php include_once('top_header.php');?>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left"  id="plantitle"></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="center"  id="plantitle"><span id="thisplantitle" title="Click to edit the plan details">	<?php if(isset($ClicktoeditPlanDetails) &&  $ClicktoeditPlanDetails !=''){echo $ClicktoeditPlanDetails;} else{echo $planname_for_current_plan_text;}?></span><span title="Click to edit the plan details" id="editthisplantitle">&nbsp;&nbsp;<img src="images/editad.png" style="height:20px;cursor:pointer;"></span></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="right"  id="plantitle"><button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;

      	<?php if(isset($FINISHADDING) &&  $FINISHADDING !=''){echo $FINISHADDING;} else{echo 'FINISH ADDING';}?>
       </button></div>
</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
				</ul>
			</div>
			    <div  style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar" align="center">
    		<h6 style="font-family:Freestyle;font-size:30px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;"><?php if(isset($PrescriptionList) &&  $PrescriptionList !=''){echo $PrescriptionList;} else{echo 'Prescription List';}?></h6>
    		<div class="sidebarheadings"><?php if(isset($MasterPrescriptions) &&  $MasterPrescriptions !=''){echo $MasterPrescriptions;} else{echo 'Master Prescriptions';}?>:</div>
    				    		<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_prescriptions 		= "select t1.PrescriptionNo,t1.PlanCode, t1.PrescriptionName, 
			    		t1.DoctorsName, t1.CreatedDate from MEDICATION_HEADER as t1,PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t2.MerchantID='$logged_merchantid' and t2.PlanStatus='A'";
			    		//echo $get_plan_prescriptions;exit;
		    		$get_plan_prescriptions_run 	= mysql_query($get_plan_prescriptions);
		    		$get_plan_prescriptions_count 	= mysql_num_rows($get_plan_prescriptions_run);
		    		if($get_plan_prescriptions_count > 0){
			    		while ($get_presc_row = mysql_fetch_array($get_plan_prescriptions_run)) {
			    			$prescription_no   	= $get_presc_row['PrescriptionNo'];
			    			$prescription_name 	= $get_presc_row['PrescriptionName'];
			    			$fortitle			= $get_presc_row['PrescriptionName'];
			    			$length 			= strlen($prescription_name);
			    			if($length > 12){
			    				$prescription_name 	= substr($prescription_name,0,12);
			    				$prescription_name 	= $prescription_name."...";
			    			}
			    			$prescription_doc  = $get_presc_row['DoctorsName'];
			    			$prescription_code = $get_presc_row['PlanCode'];
			    			$prescription_date = date('d-M-Y',strtotime($get_presc_row['CreatedDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $prescription_code.$prescription_no; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $prescription_code.$prescription_no; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $prescription_code.$prescription_no; ?>" onclick='changeimage("<?php echo $prescription_code.$prescription_no; ?>");'></a>
					                   <span title='<?php echo $fortitle;?>'><?php echo $prescription_name;?></span>
					                  <img src="images/addtoright.png" class="addmasterplanprescriptions" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $prescription_code."~~".$prescription_no; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $prescription_code.$prescription_no; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $prescription_code.$prescription_no; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php
					                		$med_details = mysql_query("select MedicineName from MEDICATION_DETAILS where PlanCode = '$prescription_code' and PrescriptionNo='$prescription_no'");
					                		$med_details_count = mysql_num_rows($med_details);
					                		if($med_details_count > 0){
						                		while ($med_row = mysql_fetch_array($med_details)) {
						                			$medicine_name = $med_row['MedicineName'];
						                			echo $medicine_name;echo "<br>";
						                		}
					                		} else {
					                			echo "No medicines found";
					                		}

					                	?>

					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		}else {
		    			echo "<div style='color:#fff;'>No Prescriptions to show</div>";
		    		}
			    		?>
		    		</div>
		    		<div class="sidebarheadings" style="margin-top: -13px;"><?php if(isset($AssignedPrescriptions) &&  $AssignedPrescriptions !=''){echo $AssignedPrescriptions;} else{echo 'Assigned Prescriptions';}?> :</div>
		    				    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_prescriptions 		= "select t1.UserID,t1.PrescriptionNo,t1.PlanCode,
			    		t1.PrescriptionName, t1.DoctorsName, t1.CreatedDate from USER_MEDICATION_HEADER as t1,USER_PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.MerchantID='$logged_merchantid'";
		    		$get_plan_prescriptions_run 	= mysql_query($get_plan_prescriptions);
		    		$get_plan_prescriptions_count 	= mysql_num_rows($get_plan_prescriptions_run);
		    		if($get_plan_prescriptions_count > 0){
			    		while ($get_presc_row = mysql_fetch_array($get_plan_prescriptions_run)) {
			    			$prescription_no   	= $get_presc_row['PrescriptionNo'];
			    			$prescription_name 	= $get_presc_row['PrescriptionName'];
			    			$fortitle			= $get_presc_row['PrescriptionName'];
			    			$length 			= strlen($prescription_name);
			    			if($length > 12){
			    				$prescription_name 	= substr($prescription_name,0,12);
			    				$prescription_name 	= $prescription_name."...";
			    			}
			    			$prescription_doc  	= $get_presc_row['DoctorsName'];
			    			$prescription_code  = $get_presc_row['PlanCode'];
			    			$prescription_user  = $get_presc_row['UserID'];
			    			$prescription_date 	= date('d-M-Y',strtotime($get_presc_row['CreatedDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $prescription_user.$prescription_code.$prescription_no; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $prescription_user.$prescription_code.$prescription_no; ?>" aria-expanded="true" aria-controls="collapse<?php echo $prescription_user.$prescription_code.$prescription_no; ?>"><img src="images/rightarrow.png" style="height:12px;width:auto;cursor:pointer;" align="left"></a>
					                   <span title='<?php echo $fortitle;?>'><?php echo $prescription_name;?></span>
					                  <img src="images/addtoright.png" class="addassignedplanprescriptions" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $prescription_user."~~".$prescription_code."~~".$prescription_no; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $prescription_user.$prescription_code.$prescription_no; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $prescription_user.$prescription_code.$prescription_no; ?>">
					                <div class="panel-body" style="text-align:center;">
					                	<?php
					                		$med_details = mysql_query("select MedicineName from USER_MEDICATION_DETAILS where PlanCode = '$prescription_code' and PrescriptionNo='$prescription_no' and UserID='$prescription_user'");
					                		$med_details_count = mysql_num_rows($med_details);
					                		if($med_details_count > 0){
						                		while ($med_row = mysql_fetch_array($med_details)) {
						                			$medicine_name = $med_row['MedicineName'];
						                			echo $medicine_name;echo "<br>";
						                		}
					                		} else {
					                			echo "No medicines found";
					                		}

					                	?>

					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		}else {
		    			echo "<div style='color:#fff;'>No Prescriptions to show</div>";
		    		}
			    		?>
		    		</div>
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent">
	    	<div id="dynamicPagePlusActionBar">
 			    <label>
	    			You must first add a Prescription to include all the Medicines. <span id='getmedications'>Click here</span> to start adding or Select A Template to get started.<br>
	    		</label>
			</div>
    	</div>
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
         <!--SHOW MULTIPLE SPECIFIC TIME MODAL WINDOW-->
            <div class="modal" id="specifictimepicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Enter Specific Times</h5>
                  </div>
                  <div class="modal-body multiplespecifictimes" align="center" style="padding-top:0px;background-color:#fff;">

                  		<div class="form-group">
							<label class="control-label col-sm-4" for="email">Enter start time :</label>
							<div class="col-sm-8" style="margin-left: -15px;">
							<div class="col-sm-4">
							<input class="form-control" type="text" placeholder="hh" id="specific_time_inp" name="specific_time" maxlength='2' onkeypress="return numbersonly(this, event)" pattern='^[0-9]$'>
							</div>
							<div class="col-sm-4">
							<input class="form-control" value="00" placeholder="mm" type="text" id="specific_time_min" name="specific_time_min" onkeypress="return numbersonly(this, event)" maxlength='2' pattern='^[0-9]$'>
							</div>
							<div class="col-sm-4">
							<select class="form-control selectpicker" id="time_type" name="time_type">
							<option value="AM">AM</option>
							<option value="PM">PM</option>
							</select>
							</div>
							</div>
						</div>
                       <div class="form-group">&nbsp;</div>

						<div class="form-group">
							<label class="control-label col-sm-4">Specific Interval time :</label>
							<div class="col-sm-1">
								<input type="checkbox" id="specific_interval">
							</div>	
						</div>
						 <div class="form-group">&nbsp;</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="pwd"></label>
							<div class="col-sm-6">

							<select class="form-control selectpicker" id="intervel_time" name="intervel_time" style="display: none;">
							<option value="0">Select interval time</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>6</option>
							<option>12</option>
							</select>

							</div>
						</div>

						<div class="col-sm-12" id="push_time" style="margin-top:20px;"> </div>

                	<!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                		<span>1. <span class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="specifictimea" id="specifictimea" class="specifictime forminputs4" value="" readonly></span></span><img src="images/delete.png"  class="clearspecifictime" id="a" title="Clear Specific Time"></span>
                	</div>

                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                		<span>2. <span class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="specifictimeb" id="specifictimeb" class="specifictime forminputs4" value="" readonly></span></span><img src="images/delete.png"  class="clearspecifictime" id="b" title="Clear Specific Time"></span>
                	</div>

                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                		<span>3. </span><span class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="specifictimec" id="specifictimec" class="specifictime forminputs4" value="" readonly></span><img src="images/delete.png" class="clearspecifictime" id="c" title="Clear Specific Time"></span>
                	</div>

                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                		<span>4. </span><span class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="specifictimed" id="specifictimed" class="specifictime forminputs4" value="" readonly></span><img src="images/delete.png" class="clearspecifictime" id="d" title="Clear Specific Time"></span>
                	</div> -->

               	  </div>
               	  <div class="margin20" align="center">
               	  <input type="hidden" name="specifictimeselectedid" id="specifictimeselectedid" value="0">
               	  <button class="smallbutton margin20" id="specifictimeselected">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF SHOW MULTIPLE SPECIFIC TIME MODAL WINDOW-->
         <!--SHOW PLAN DETAILS MODAL WINDOW-->
            <div class="modal" id="plandetailsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings"><?php if(isset($EnterPlanDetails) &&  $EnterPlanDetails !=''){echo $EnterPlanDetails;} else{echo 'Enter Plan Details';}?></h5>
                  </div>
                  	<div align="left" style="padding-left:5px;font-family:RalewayRegular;color:#000;">
						<input type="text" placeholder="<?php if(isset($EnterthePlanTitlehere) &&  $EnterthePlanTitlehere !=''){echo $EnterthePlanTitlehere;} else{echo 'Enter the Plan Title here';}?>" name="plan_name" id="plan_name" class="firstlettercaps" title="Plan Title" onkeypress='keychk(event)' maxlength="50" style="width:100%;" value="<?php echo $planname_for_current_plan;?>">
                        <textarea placeholder="<?php if(isset($Typetheplandescriptionhere) &&  $Typetheplandescriptionhere !=''){echo $Typetheplandescriptionhere;} else{echo 'Type the plan description here
';}?>" id="plan_desc" name="plan_desc" title="Plan Description" rows="4" style="resize:none;border-bottom:1px solid #004f35;"  maxlength="499"><?php echo $plandesc_for_current_plan;?></textarea>
                        <!--ADDED-->
                        <div id="textarea_feedback" style="color:#004F35;font-family:Raleway;padding-bottom:10px;text-align:right"></div>
                        <!---->
                        <!--<span>Upload a Cover Image (Optional):  <input id="plan_cover_image" name="plan_cover_image" type="file" accept='image/*' style="display:inline;"></span>-->
					</div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="plancode_for_current_plan" id="plancode_for_current_plan" value="<?php echo $plancode_for_current_plan;?>">
               	  <button class="smallbutton" id="plandetailsentered"><?php if(isset($Done) &&  $Done !=''){echo $Done;} else{echo 'Done';}?></button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF PLAN DETAILS MODAL WINDOW-->
</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootstrap-timepicker.js"></script>
		<script type="text/javascript" src="js/modernizr.js"></script>
		<script type="text/javascript" src="js/placeholders.min.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript" src="js/jasny-bootstrap.min.js"></script>
		<script src="js/datalist/modernizr.min.js" type="text/javascript"></script>
		<script>
		yepnope({
		  test : (!Modernizr.input.list),
		  yep : [
		      'js/datalist/jquery.relevant-dropdown.js',
		      'js/datalist/load-fallbacks.js'
		  ]
		});
		</script>

		<script type="text/javascript">

			$('#specific_interval').click(function(){
            if($(this).is(":checked")){
                $('#intervel_time').show(); 
            }
            else if($(this).is(":not(:checked)")){
                 $('#intervel_time').hide();  
            }
        });


		$(document).ready(function() {
			$('#6').addClass('active');
			mainHeader = 115;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight);
		  	//$('#listBar').css({height: listBarHeight});
		  	//$('#dynamicPagePlusActionBar').css({height: listBarHeight});

		  	var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	$('.maincontent').height(available_height);
        	var sidelistheight = $('#listBar').height();
        	var availableheight = sidelistheight - 280;
        	availableheight = availableheight/2;
        	//alert(availableheight);
        	$('.masterplanactivities').height(availableheight);
        	$('.assignedplanactivities').height(availableheight);
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
			var browser_name = '<?php echo $browser_name; ?>';
			$('#thisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#editthisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			var text_max = 499;
			$('#textarea_feedback').html(text_max + ' characters remaining');

			$('#plan_desc').keyup(function() {
				var text_length = $('#plan_desc').val().length;
				var text_remaining = text_max - text_length;

				$('#textarea_feedback').html(text_remaining + ' characters remaining');
			});
			$(document).on('focus', '.specifictime', function () {
				$(this).timepicker("show");
			});
			//FINISHED ADDING
			$('#finished_adding').click(function(){
				
				var plan_name = $('#plan_name').val();
			    	if(plan_name.replace(/\s+/g, '') == ""){
						bootbox.alert("Please enter a title for this plan.");
						$('.bootbox').on('hidden.bs.modal', function() {
						    $('#plan_name').focus();
						});
						$('#plandetailsmodal').modal('show');
						$('#plan_name').val("");
						return false;
					}
				//$( "#saveAndEdit" ).trigger( "click" );
				window.location.href = "finishedadding_new.php";
			});

			setTimeout(function() {
				$("#getmedications").trigger('click');        
		        var plan_name = $('#plan_name').val();
		        if(plan_name.replace(/\s+/g, '') == ""){
		        	$('#plandetailsmodal').modal('show');
		        }
		    },1);
		    
		    $('#plandetailsentered').click(function(){
		    	var plan_name = $('#plan_name').val();
		    	if(plan_name.replace(/\s+/g, '') == ""){
					bootbox.alert("Please enter a title for this plan.");
					$('.bootbox').on('hidden.bs.modal', function() {
					    $('#plan_name').focus();
					});
					$('#plan_name').val("");
					return false;
				}
				var plan_code = $('#plancode_for_current_plan').val();
				var plan_desc = $('#plan_desc').val();

					var dataString = "type=insert_plan_header&title="+plan_name+"&desc="+plan_desc+"&code="+plan_code+"&mer="+<?php echo $logged_merchantid;?>+"&user="+<?php echo $logged_userid;?>;
					//bootbox.alert(dataString);
					$('#thisplantitle').html(plan_name);
		        	$.ajax({
						type		: 'POST',
						url			: 'ajax_validation.php',
						crossDomain	: true,
						data		: dataString,
						dataType	: 'json',
						async		: false,
						success	: function (response)
							{
								if(response.success == true){
									$('#plandetailsmodal').modal('hide');
								} else {
									$('#plandetailsmodal').modal('hide');
								}
							},
						error: function(error)
						{

						}
					});
		    });
			$('.clearspecifictime').click(function(){
				var id = $(this).attr('id');
				$('#specifictime'+id).val("").timepicker('clear');
			});
			//GETTING MEDICATION FORM
			$('#addItemButton, #getmedications').click(function(){
				if (medicationcount > 0){
					var discard = confirm("The current prescription will be discarded. Click OK to continue.");
					if(discard == true){
						medicationcount = 0;
					} else {

					}
				} else {
					medicationcount = 0;
				}
				if(medicationcount == 0){
					$.ajax({
						type        : "GET",
						url			: "prescriptionDefaultPage4.php",
						dataType	: "html",
						success	: function (response)
						{
							$('#dynamicPagePlusActionBar').html(response);
							medicationcount = 3;
						 },
						 error: function(error)
						 {
						 	//bootbox.alert(error);
						 }
					});
				}

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

			   		$('#specifictimeselectedid').val(id);
			   		document.getElementById("intervel_time").selectedIndex = "0";
			   		$('#specific_time_inp').val('');
			   		$('#specific_time_min').val('00');
			   		$('#push_time').html('');
			   		
			   		
			   		//$('#specifictimetext'+id).show();
			   		//$('#specifictime'+id).show();
			   		/*$('#specifictimea').val("").timepicker('clear');
			   		$('#specifictimeb').val("").timepicker('clear');
			   		$('#specifictimec').val("").timepicker('clear');
			   		$('#specifictimed').val("").timepicker('clear');*/
			   		$('#specifictimepicker').modal('show');
			   } else {
			   		$('#instruction'+id).prop('disabled', false);
			   		$('#instruction'+id).css('opacity', '1');
			   		$('#specifictimetext'+id).hide();
			   		$('#specifictimepicker').modal('hide');
			   		$('#specifictime'+id).attr('type', 'hidden');
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
$('.panel-heading a').on('click',function(e){
	var id = $(this).attr('href');
	if($(id).hasClass('in')){
		//alert(1);
		//$(id).removeClass('in');
		//$('.panel-collapse').removeClass('in');		
	} else {
		//alert(2);
		$(id).addClass('in');
	    $('.panel-collapse').removeClass('in');		
	}

});
			//ON CLICK OF DONE BUTTON AFTER ENTERING SPECIFIC TIME
			$(document).on('click', '#specifictimeselected', function () {

				var getcurrentid = $('#specifictimeselectedid').val();

				var specifictimes = "";

				var providercount = 0;

				var providervalue = [];

				var specific_time_inp =  $('#specific_time_inp').val();

				var intervel_time =  $('#intervel_time').val();

				var min_time =  $('#specific_time_min').val();

				var time_type =  $('#time_type').val();

                var SingleSpecificTime = specific_time_inp+':'+min_time+' '+time_type;

                if($('#specific_interval').prop("checked")){
					if (intervel_time == 0) {
						bootbox.alert("Please select intervel time.");
						return false;
					}
			    }

				var specifictime = $('#specifictime'+getcurrentid).val();
				if (specific_time_inp == '') {
	   
	    			 if(specifictime != ''){
	    			 }else{
	    			 	bootbox.alert("Please enter start time.");
					    return false;
	    			 }
				}else{
					if($('#specific_interval').prop("checked")){	 
	                }else{
	                   providervalue.push(SingleSpecificTime);	
	                }	 
				}

				$('.specific_time').each(function(){
				    if(this.checked == true){
				        providervalue.push($(this).val());
				        providercount++;
				    }
				});

				specifictimes = providervalue;

				if(providervalue == ""){
					bootbox.alert("Please enter atleast one specific to continue..");
					return false;
				}

		    	$('#specifictime'+getcurrentid).val(specifictimes);
		    	$('#specifictime'+getcurrentid).attr('type', 'text');
		    	$('#specific_interval').attr('checked', false);
		    	$('#intervel_time').hide();
		    	$('#specifictimepicker').modal('hide');
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
			});
			//ON CLICK SHOW MULTIPLE SPECIFIC TIME MODAL WINDOW TO EDIT SELECTION
			$(document).on('click','.editedspecifictimes', function(){
			   var editst = $(this).attr('id');
			   var id  = editst.replace("specifictime", "");
			   //bootbox.alert(id);
			  var specific_time_inp =$('#specific_time_inp').val('');
			  document.getElementById("intervel_time").selectedIndex = "0";
			  $('#specific_time_min').val('00');
              $('#specific_time_inp').val('');
			    $('#push_time').html('');
                var text = '';
			    var specifictime = $('#specifictime'+id).val();
			  // bootbox.alert(selectedweekdays);
			    var sptimes = specifictime.split(',');
	              for(var x=0; x<sptimes.length; x++){
	                text += '<label class="checkbox-inline"><input type="checkbox" value="'+sptimes[x]+'" name="check" checked class="css-checkbox specific_time"/>' +sptimes[x]+'</label>';
	               }
	              $('#push_time').html(text);
	              
			  /*$('#specifictimea').val(sptimes[0]);
			  $('#specifictimeb').val(sptimes[1]);
			  $('#specifictimec').val(sptimes[2]);
			  $('#specifictimed').val(sptimes[3]);*/


			  $('#specifictimeselectedid').val(id);
			  $('#specifictimepicker').modal('show');
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
					var plan_name = $('#plan_name').val();
			    	if(plan_name.replace(/\s+/g, '') == ""){
						bootbox.alert("Please enter a title for this plan.");
						$('.bootbox').on('hidden.bs.modal', function() {
						    $('#plan_name').focus();
						});
						$('#plandetailsmodal').modal('show');
						$('#plan_name').val("");
						return false;
					}
					
				var current_prescription_name = $('#prescriptionName').val();
				if(current_prescription_name.replace(/\s+/g, '') == ""){
					bootbox.alert("Please enter a name for this prescription.");
					$('.bootbox').on('hidden.bs.modal', function() {
					    $('#prescriptionName').focus();
					});
					$('#prescriptionName').val("");
					return false;
				}
				/*var current_doctor_name = $('#doctorName').val();
				if(current_doctor_name.replace(/\s+/g, '') == ""){
					bootbox.alert("Please enter the doctors name");
					$('#doctorName').val("");
					$('#doctorName').focus();
					return false;
				}*/
				var numberOfPrescription = 0;
				var current_usedprescriptioncount = $('#usedpresciptioncount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedprescriptioncount.split(',');
				medicationcount = $('#medicationcount').val(); //TOTAL NUMBER OF MEDICINE FIELDS PRESENT CURRENTLY ON THE PAGE
				for (i = 0; i < medicationcount; ++i) {
				 	var medicinename = $('#medicine'+result[i]).val();

				 	if(!medicinename == ""){
				 		if(medicinename.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Medicine name cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() {
							    $('#medicine'+result[i]).focus();
							});
					 		$('#medicine'+result[i]).val("");
							return false;
				 		}
				 		numberOfPrescription = numberOfPrescription + 1;

				 			var medcount = $('#medcount'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if((medcount == "")||(medcount == 0)||(medcount == "0")){
					 			bootbox.alert("Please enter the medicine count");
					 			$('.bootbox').on('hidden.bs.modal', function() {
					 				$('#medcount'+result[i]).focus();
					 			});
					 			return false;
					 		}
					 		if(!$.isNumeric(medcount)){
				 				bootbox.alert("Please enter a numeric value");
				 				$('.bootbox').on('hidden.bs.modal', function() {
					 				$('#medcount'+result[i]).focus();
					 			});
					 			return false;
				 			}
					 		var medcountType = $('#medcountType'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if(medcountType == 0){
					 			bootbox.alert("Please select the medicine Type");
					 			$('.bootbox').on('hidden.bs.modal', function() {
					 				$('#medcountType'+result[i]).focus();
					 			});
					 			return false;
					 		}

				 		var wheninput = $('#when'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if(wheninput == 0){
				 			bootbox.alert("Please select the medicine dosage");
				 			$('.bootbox').on('hidden.bs.modal', function() {
				 				$('#when'+result[i]).focus();
				 			});
				 			return false;
				 		}
				 		if(wheninput == '16'){
				 			var specifictime = $('#specifictime'+result[i]).val();
				 			if(specifictime.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please enter the specific time");
						 		//$('#specifictime'+result[i]).val("");
								//$('#specifictime'+result[i]).focus();
								$('#specifictimeselectedid').val(+result[i]);
			  					$('#specifictimepicker').modal('show');
								return false;
				 			}
				 		}
				 		var instructioninput = $('#instruction'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if((instructioninput == 0)&&(wheninput != '16')){
				 			bootbox.alert("Please select the medicine instruction");
				 			$('.bootbox').on('hidden.bs.modal', function() {
							    $('#instruction'+result[i]).focus();
							});
				 			return false;
				 		}
				 		var frequencyinput = $('#frequency'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if(frequencyinput == 0){
				 			bootbox.alert("Please select the medicine frequency");
				 			$('.bootbox').on('hidden.bs.modal', function() {
				 				$('#frequency'+result[i]).focus();
				 			});
				 			return false;
				 		}
				 		if(frequencyinput!= "Once"){
					 		var countinput = $('#count'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if((countinput == "")||(countinput == 0)||(countinput == "0")){
					 			bootbox.alert("Please enter the medicine duration");
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
					 			bootbox.alert("Please select the medicine duration");
					 			$('.bootbox').on('hidden.bs.modal', function() {
					 				$('#countType'+result[i]).focus();
					 			});
					 			return false;
					 		}
				 		}
				 		if(frequencyinput == "Weekly"){
				 			$('#selectedmonthdays'+result[i]).val("");
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
				 			//alert(1);
				 			$('#selectedweekdays'+result[i]).val("");
				 			var selectedmonthdays = $('#selectedmonthdays'+result[i]).val();
				 			//alert(selectedmonthdays);
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
				 	// 	var linkentered = $('#linkentered'+result[i]).val();
						// if(linkentered.replace(/\s+/g, '') == ""){

				 	// 	} else {
				 	// 		if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(linkentered)) {
						// 	} else {
						// 	  bootbox.alert("Please enter a valid url");
						// 	  $('.bootbox').on('hidden.bs.modal', function() {
					 // 			$('#linkentered'+result[i]).focus();
					 // 			});
					 // 			return false;
						// 	}
				 	// 	}
				 	}
				}
				if(numberOfPrescription == 0){
					bootbox.alert("Please enter atleast one medicine to continue.");
					$('.bootbox').on('hidden.bs.modal', function() {
							    $('#medicine1').focus();
							});
					return false;
				}
				$('#frm_plan_prescription').submit();
			});
	//EDIT MEDICATION BUTTON CLICKED - FROM SIDE PANEL
		$('.editmedicationbuttons').click(function(){
			var prescid = $(this).attr('id');
				//bootbox.alert(prescid);
			window.location.href = "edit_plan_medication.php?id="+prescid;
		});
		$('.addassignedplanprescriptions').click(function(){
				var prescid 	= $(this).attr('id'); 
			   $(".forminputs2").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			    var deleted_row_id  = id.replace("medicine", "");
			    if (id.indexOf("medicine") >= 0){
			    	//alert(id);//Checking if substring medicine is present in id
			    	var medicine_name = $(this).val();
			    	//alert(medicine_name);
			    	if(medicine_name.replace(/\s+/g, '') == ""){
			    	//	alert(1);
			        $('#pslno tr:last').remove();
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').remove();
				      medicationcount = $('#medicationcount').val();
				      medicationcount = medicationcount - 1;
				      if(medicationcount == 1){
				        $('.deleterow').hide();
				      }
				      var deleted_usedprescription = deleted_row_id+",";
				      var current_usedprescriptioncount = $('#usedpresciptioncount').val();
				      var new_usedprescriptioncount  = current_usedprescriptioncount.replace(deleted_usedprescription, "");
				      $('#usedpresciptioncount').val(new_usedprescriptioncount);
				      $('#medicationcount').val(medicationcount);
			   }
			    }
			});
			var assigned 	= prescid.split("~~");
			var userid 		= assigned[0];
			var plancode 	= assigned[1];
			var prescno 	= assigned[2];
			var merchantid  = '<?php echo $logged_merchantid;?>';
			//alert(prescno);
//alert(browser_name);
			var dataString = "plancode="+plancode+"&type=get_assigned_prescriptions&prescno="+prescno+"&userid="+userid+"&merchantid="+merchantid;
				$.ajax({
                  type    :"GET",
                  url     :"ajax_validation.php",
                  data    :dataString,
                  dataType  :"jsonp",
                  jsonp   :"jsoncallback",
                  async   :false,
                  crossDomain :true,
                  success   : function(data,status){
                    //alert(1);
                    $.each(data, function(i,item){
                      medicationcount = $('#medicationcount').val();
				        propercount     = $('#propercount').val();
				        medicationcount = parseInt(medicationcount) + 1;
				        propercount     = parseInt(propercount) + 1;
				        $('.deleterow').show();
				        var mednamedisplay = "";
						if(browser_name == "applesafari"){
						mednamedisplay = "<select style='height:35px;width:100%;background-color:#2B6D57;' name='medicine"+propercount+"' id='medicine"+propercount+"'  class='forminputs2'><option value='' style='display:none;'>Select a Medicine</option>"+item.MedicineNameOptions+"</select>";
						}else {
						mednamedisplay = "<input type='text' list='medicine_list'  id='medicine"+propercount+"' name='medicine"+propercount+"' placeholder='Type Medicine Name Here' class='forminputs2' value='"+item.MedicineName+"'><datalist id='medicine_list'>"+item.MedicineNameOptions+"</datalist>";
						}
				        //<td class='paddingrl5' align='right'>Threshold:</td><td class='paddingrl5' align='center'><input type='text' maxlength='2' name='threshold"+propercount+"' id='threshold"+propercount+"' style='width:40px;height:35px;' class='forminputs2 roundedinputs countbox' title='Enter the threshold' value='"+item.ThresholdLimit+"'></td>
				        var first = "<tr style='border-top:4px solid #004f35;'><td class='paddingrl5' align='center' colspan='6'>"+mednamedisplay+"</td><td class='paddingrl5' align='center'><div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='medcount"+propercount+"' id='medcount"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the duration' value='"+item.MedicineCount+"'><select class=' lightcolorselect' id='medcountType"+propercount+"' name='medcountType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Select Medicine Type'><option value='0' style='display:none;'>select</option>"+item.MedicineTypeOptions+"</select></div></td><td style='width:300px;' class='paddingrl5'><input type='radio' name='radio"+propercount+"' value='PS' "+item.PlanStartRadio+" class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<br></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td class='paddingrl5' align='left'>When:</td><td class='paddingrl5' align='center'><select name='when"+propercount+"' id='when"+propercount+"' title='Select the medicine dosage' class='whenshorthand'><option value='0' style='display:none;'>select</option>"+item.ShortHandOptions+"</select></td><td class='paddingrl5' align='center'><select name='instruction"+propercount+"' id='instruction"+propercount+"' title='Select the medicine instruction'><option value='0' style='display:none;'>select</option>"+item.InstructionOptions+"</select></td><td class='paddingrl5' align='center'>Frequency :</td><td class='paddingrl5' align='center'><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the medicine frequency' class='medfrequency'><option value='0' style='display:none;'>select</option>"+item.FrequencyOptions+"</select></td><td class='paddingrl5' align='center'>Duration :</td><td class='paddingrl5' align='center'> <div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' "+item.CountSelect1+" class='forminputs2 roundedinputs countbox' title='Enter the duration' value='"+item.HowLong+"'><select class='' id='countType"+propercount+"' name='countType"+propercount+"' "+item.CountSelect2+" title='Enter the duration'><option value='0' style='display:none;'>select</option>"+item.HowLongTypeOptions+"</select></div></td><td><input type='radio' name='radio"+propercount+"' value='ND' "+item.NumOfDaysRadio+" class='radio"+propercount+" prescriptionradio'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays roundedinputs "+item.NDClass+"' maxlength='2'  value='"+item.NoOfDaysAfterPlanStarts+"'></td></tr><tr><td>Critical :<input type='checkbox' name='critical"+propercount+"' id='critical"+propercount+"' class='criticalcheck' title='Check this if the medicine is critical' value='Y' "+item.CriticalSelect+" style='margin-left:5px;'></td><td align='center'>Response :<input type='checkbox' name='response"+propercount+"' id='response"+propercount+"' class='responsecheck' title='Check this if a response is required' value='Y' style='margin-left:5px;' "+item.ResponseSelect+"></td><td>Threshold :<input type='text' maxlength='2' name='threshold"+propercount+"' id='threshold"+propercount+"' "+item.ThresholdInput+"class='forminputs2 roundedinputs countbox' title='Enter the threshold'></td><td colspan='4' style='white-space:nowrap;'><div class='fileinput fileinput-new input-group' data-provides='fileinput' style='width:100%;'><div class='form-control' data-trigger='fileinput'><i class='glyphicon glyphicon-file fileinput-exists'></i><span class='fileinput-filename'>"+item.OriginalFileName+"</span></div><span class='input-group-addon btn btn-default btn-file'><span class='fileinput-new'>Click To Upload A Document</span><span class='fileinput-exists'>Change</span><input type='file'  name='uploadedfile"+propercount+"' id='uploadedfile"+propercount+"'></span><a href='#' class='input-group-addon btn btn-default fileinput-exists removelinkbutton' id='"+propercount+"' data-dismiss='fileinput'>Remove</a></div><input type='hidden' name='previouslink"+propercount+"' id='previouslink"+propercount+"' value='"+item.Link+"'><input type='hidden' name='originalfilename"+propercount+"' id='originalfilename"+propercount+"' value='"+item.OriginalFileName+"'><input type='hidden' name='deletelink"+propercount+"' id='deletelink"+propercount+"'  value='0'></td><td><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio' "+item.SpecificDateRadio+"> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate "+item.SDClass+"'  value='"+item.SpecificDate+"'></td></tr><tr><td colspan='8'><input "+item.SpecificTimeType+" name='specifictime"+propercount+"' id='specifictime"+propercount+"' class='editedspecifictimes forminputs2' readonly title='Click here to edit specific times'  value='"+item.SpecificTime+"'></td></tr><tr><td colspan='8'> <input "+item.WeeklyType+" value='"+item.FrequencyString+"' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' value='' class='forminputs2 editselectedweekday' readonly title='Click here to edit frequency'><input "+item.MonthlyType+" value='"+item.FrequencyString+"' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' value='' class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'><div class='bottomshadow' style='width:100%;color:#fff;pointer-events:none;height:12px;'><span style='display:none;'>1</span></div></td></tr>";
				       // alert(propercount);
				        //alert('#when'+propercount);
				        //$('#when'+propercount).val(item.When);
				       // $('#when6 option[value="2"]').attr('selected', 'selected');
				        var slno  = "<tr><td>"+medicationcount+"</td></tr>";
				        $('#pslno > tbody').append(slno);
				        $('#pdata > tbody').append(first);
				        var current_usedprescriptioncount = $('#usedpresciptioncount').val();
				        var new_usedprescriptioncount = current_usedprescriptioncount+propercount+",";
				        $('#usedpresciptioncount').val(new_usedprescriptioncount);
				        $('#medicationcount').val(medicationcount);
				        $('#propercount').val(propercount);
				        $('#medicine'+propercount).focus();
                    });
                  },
                  error: function(){

                  }
                });

		});
		$(document).on('click', '.removelinkbutton', function(e) {
		        var linkid 	= $(this).attr('id'); 
				$('#deletelink'+linkid).val("1");
		    });
		$('.addmasterplanprescriptions').click(function(){
			   var prescid 	= $(this).attr('id'); 
			   $(".forminputs2").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			    var deleted_row_id  = id.replace("medicine", "");
			    if (id.indexOf("medicine") >= 0){
			    	//alert(id);//Checking if substring medicine is present in id
			    	var medicine_name = $(this).val();
			    	if(medicine_name.replace(/\s+/g, '') == ""){
			        $('#pslno tr:last').remove();
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').remove();
				      medicationcount = $('#medicationcount').val();
				      medicationcount = medicationcount - 1;
				      if(medicationcount == 1){
				        $('.deleterow').hide();
				      }
				      var deleted_usedprescription = deleted_row_id+",";
				      var current_usedprescriptioncount = $('#usedpresciptioncount').val();
				      var new_usedprescriptioncount  = current_usedprescriptioncount.replace(deleted_usedprescription, "");
				      $('#usedpresciptioncount').val(new_usedprescriptioncount);
				      $('#medicationcount').val(medicationcount);
			   }
			    }
			});

			
			//alert(prescid);
			var master 		= prescid.split("~~");
			var plancode 	= master[0];
			var prescno 	= master[1];
			var merchantid  = '<?php echo $logged_merchantid;?>';
			var dataString = "plancode="+plancode+"&type=get_master_prescriptions&prescno="+prescno+"&merchantid="+merchantid;
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
                    //alert(1);
                    $.each(data, function(i,item){
                      medicationcount = $('#medicationcount').val();
				        propercount     = $('#propercount').val();
				        medicationcount = parseInt(medicationcount) + 1;
				        propercount     = parseInt(propercount) + 1;
				        $('.deleterow').show();
				        //<td class='paddingrl5' align='right'>Threshold:</td><td class='paddingrl5' align='center'><input type='text' maxlength='2' name='threshold"+propercount+"' id='threshold"+propercount+"' style='width:40px;height:35px;' class='forminputs2 roundedinputs countbox' title='Enter the threshold' value='"+item.ThresholdLimit+"'></td>
				        var mednamedisplay = "";
						if(browser_name == "applesafari"){
						mednamedisplay = "<select style='height:35px;width:100%;background-color:#2B6D57;' name='medicine"+propercount+"' id='medicine"+propercount+"'  class='forminputs2'><option value='' style='display:none;'>Select a Medicine</option>"+item.MedicineNameOptions+"</select>";
						}else {
						mednamedisplay = "<input type='text' list='medicine_list'  id='medicine"+propercount+"' name='medicine"+propercount+"' placeholder='Type Medicine Name Here' class='forminputs2' value='"+item.MedicineName+"'><datalist id='medicine_list'>"+item.MedicineNameOptions+"</datalist>";
						}
				        var first = "<tr style='border-top:4px solid #004f35;'><td class='paddingrl5' align='center' colspan='6'>"+mednamedisplay+"</td><td class='paddingrl5' align='center'><div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='medcount"+propercount+"' id='medcount"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the duration' value='"+item.MedicineCount+"'><select class=' lightcolorselect' id='medcountType"+propercount+"' name='medcountType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Select Medicine Type'><option value='0' style='display:none;'>select</option>"+item.MedicineTypeOptions+"</select></div></td><td style='width:300px;' class='paddingrl5'><input type='radio' name='radio"+propercount+"' value='PS' "+item.PlanStartRadio+" class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<br></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td class='paddingrl5' align='left'>When:</td><td class='paddingrl5' align='center'><select name='when"+propercount+"' id='when"+propercount+"' title='Select the medicine dosage' class='whenshorthand'><option value='0' style='display:none;'>select</option>"+item.ShortHandOptions+"</select></td><td class='paddingrl5' align='center'><select name='instruction"+propercount+"' id='instruction"+propercount+"' title='Select the medicine instruction'><option value='0' style='display:none;'>select</option>"+item.InstructionOptions+"</select></td><td class='paddingrl5' align='center'>Frequency :</td><td class='paddingrl5' align='center'><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the medicine frequency' class='medfrequency'><option value='0' style='display:none;'>select</option>"+item.FrequencyOptions+"</select></td><td class='paddingrl5' align='center'>Duration :</td><td class='paddingrl5' align='center'> <div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' "+item.CountSelect1+" class='forminputs2 roundedinputs countbox' title='Enter the duration' value='"+item.HowLong+"'><select class='' id='countType"+propercount+"' name='countType"+propercount+"' "+item.CountSelect2+" title='Enter the duration'><option value='0' style='display:none;'>select</option>"+item.HowLongTypeOptions+"</select></div></td><td><input type='radio' name='radio"+propercount+"' value='ND' "+item.NumOfDaysRadio+" class='radio"+propercount+" prescriptionradio'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays roundedinputs "+item.NDClass+"' maxlength='2'  value='"+item.NoOfDaysAfterPlanStarts+"'></td></tr><tr><td>Critical :<input type='checkbox' name='critical"+propercount+"' id='critical"+propercount+"' class='criticalcheck' title='Check this if the medicine is critical' value='Y' "+item.CriticalSelect+" style='margin-left:5px;'></td><td align='center'>Response :<input type='checkbox' name='response"+propercount+"' id='response"+propercount+"' class='responsecheck' title='Check this if a response is required' value='Y' style='margin-left:5px;' "+item.ResponseSelect+"></td><td>Threshold :<input type='text' maxlength='2' name='threshold"+propercount+"' id='threshold"+propercount+"' "+item.ThresholdInput+"class='forminputs2 roundedinputs countbox' title='Enter the threshold'></td><td colspan='4' style='white-space:nowrap;'><div class='fileinput fileinput-new input-group' data-provides='fileinput' style='width:100%;'><div class='form-control' data-trigger='fileinput'><i class='glyphicon glyphicon-file fileinput-exists'></i><span class='fileinput-filename'>"+item.OriginalFileName+"</span></div><span class='input-group-addon btn btn-default btn-file'><span class='fileinput-new'>Click To Upload A Document</span><span class='fileinput-exists'>Change</span><input type='file'  name='uploadedfile"+propercount+"' id='uploadedfile"+propercount+"'></span><a href='#' class='input-group-addon btn btn-default fileinput-exists removelinkbutton' id='"+propercount+"' data-dismiss='fileinput'>Remove</a></div><input type='hidden' name='previouslink"+propercount+"' id='previouslink"+propercount+"' value='"+item.Link+"'><input type='hidden' name='originalfilename"+propercount+"' id='originalfilename"+propercount+"' value='"+item.OriginalFileName+"'><input type='hidden' name='deletelink"+propercount+"' id='deletelink"+propercount+"'  value='0'></td><td><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio' "+item.SpecificDateRadio+"> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate "+item.SDClass+"'  value='"+item.SpecificDate+"'></td></tr><tr><td colspan='8'><input "+item.SpecificTimeType+" name='specifictime"+propercount+"' id='specifictime"+propercount+"' class='editedspecifictimes forminputs2' readonly title='Click here to edit specific times'  value='"+item.SpecificTime+"'></td></tr><tr><td colspan='8'> <input "+item.WeeklyType+" value='"+item.FrequencyString+"' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' value='' class='forminputs2 editselectedweekday' readonly title='Click here to edit frequency'><input "+item.MonthlyType+" value='"+item.FrequencyString+"' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' value='' class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'><div class='bottomshadow' style='width:100%;color:#fff;pointer-events:none;height:12px;'><span style='display:none;'>1</span></div></td></tr>";
				       // alert(propercount);
				        //alert('#when'+propercount);
				        //$('#when'+propercount).val(item.When);
				       // $('#when6 option[value="2"]').attr('selected', 'selected');
				        var slno  = "<tr><td>"+medicationcount+"</td></tr>";
				        $('#pslno > tbody').append(slno);
				        $('#pdata > tbody').append(first);
				        var current_usedprescriptioncount = $('#usedpresciptioncount').val();
				        var new_usedprescriptioncount = current_usedprescriptioncount+propercount+",";
				        $('#usedpresciptioncount').val(new_usedprescriptioncount);
				        $('#medicationcount').val(medicationcount);
				        $('#propercount').val(propercount);
				        $('#medicine'+propercount).focus();
                    });
                  },
                  error: function(){

                  }
                });
		});
		$(document).on('change', '.criticalcheck', function () {
		   if($(this).is(":checked")) {
		      //bootbox.alert(1);
		      var criticalid 	= $(this).attr('id');
		      var id  			= criticalid.replace("critical", "");
		      $( "#response"+id ).prop( "checked", true );
		      //bootbox.alert(id);
		      $('#threshold'+id).prop('disabled', false);
				$('#threshold'+id).css('opacity', '1');
				$('#threshold'+id).focus();
		      return;
		   } else {
		   	var criticalid = $(this).attr('id');
		      var id  = criticalid.replace("critical", "");
		      $( "#response"+id ).prop( "checked", false );
		      //bootbox.alert(id);
		      $('#threshold'+id).prop('disabled', true);
				$('#threshold'+id).css('opacity', '0.2');
		      return;
		   }

		});
				$(document).on('change', '.responsecheck', function () {
		   if($(this).is(":checked")) {
		   	var responseid 	= $(this).attr('id');
		      var id  			= responseid.replace("response", "");
		      $('#threshold'+id).prop('disabled', false);
				$('#threshold'+id).css('opacity', '1');
				$('#threshold'+id).focus();
		      return;
		   } else {
		   	var responseid 	= $(this).attr('id');
		      var id  			= responseid.replace("response", "");
		      $('#threshold'+id).prop('disabled', true);
				$('#threshold'+id).css('opacity', '0.2');
		      return;
		   }

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
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
		});
function numbersonly(myfield, e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
	key = e.which;
	else
	return true;

	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;

	// numbers
	else if ((("0123456789").indexOf(keychar) > -1))
	return true;

	// only one decimal point
	else if ((keychar == "."))
	{
	if (myfield.value.indexOf(keychar) > -1)
	return false;
	}
	else
	return false;
}

   $('#specific_time_inp').keyup(function(e){

        if ($(this).val() > 12 && e.keyCode != 46 && e.keyCode != 8){
        	e.preventDefault();     
            $(this).val(12);
	        bootbox.alert("Start time will be between 1 to 12");
	        return false;
          }else{
         	if($('#intervel_time').val() != 0 && $('#specific_time_inp').val() <= 12){
             	specific_data_find();
             }
         }
     
    });

    $('#specific_time_min').keyup(function(e){
        if ($(this).val() > 59 && e.keyCode != 46 && e.keyCode != 8){
        	e.preventDefault();
            bootbox.alert("Minute will be between 0 to 59");
            $(this).val('00');
        }else{
        	if($('#intervel_time').val() != 0 && $('#specific_time_inp').val() != ''){
            	specific_data_find();
            }
        }
    });

    function specific_data_find(){

        var specific_time_inp = parseInt($('#specific_time_inp').val());

        var specific_time_inp_2 = parseInt($('#specific_time_inp').val());

        if($('#specific_time_inp').val() == ''){
            bootbox.alert("Please enter start time.");
            return false;
        }

        var specific_min = $('#specific_time_min').val();
        var intervel_time = parseInt($('#intervel_time').val());
        var time_type = $('#time_type').val();
        var time_type_2 = $('#time_type').val();
        var mid_time;
        var text = '';
        var flag;
        var length = 24/intervel_time;
        for (i = 1; i < length ; i++) {

            if(i==1){

                if(specific_time_inp == 12 && time_type =='AM'){
                        time_type = ' AM';
                        specific_time_inp = 00;
                        flag = 0;
                        text += '<label class="checkbox-inline"><input type="checkbox" value="'+specific_time_inp+':'+specific_min+' '+time_type+'" name="check" class="css-checkbox specific_time"/>' +specific_time_inp+':'+specific_min+' '+time_type+'</label>';
                } else{

                     if(specific_time_inp == 12 && time_type =='PM'){
                        flag = 1;
                        text += '<label class="checkbox-inline"><input type="checkbox" value="'+specific_time_inp+':'+specific_min+' '+time_type+'" name="check" class="css-checkbox specific_time"/>' +specific_time_inp+':'+specific_min+' '+time_type+'</label>';
                     }else{ 

            text += '<label class="checkbox-inline"><input type="checkbox" value="'+specific_time_inp+':'+specific_min+' '+time_type+'" name="check" class="css-checkbox specific_time"/>' +specific_time_inp+':'+specific_min+' '+time_type+'</label>';
                }

            }

           }

            specific_time_inp = specific_time_inp + intervel_time;
            if(specific_time_inp > 12){

                 if(specific_time_inp > 24){
                    mid_time = specific_time_inp - 24;
                    if(time_type_2 == 'PM'){

                        if (specific_time_inp_2 == 12) {
                           time_type = ' AM';    
                        }else{
                         time_type = ' PM';
                      } 
                    }else{
                        time_type = ' AM';
                    }
                }
                else{ 
                        mid_time = specific_time_inp - 12;
                        if(mid_time == 12 && flag == 1){
                            time_type = ' AM';
                        }else{
                            if(time_type_2 =="PM"){
                                  if (specific_time_inp_2 == 12){
                                    time_type = ' PM';  
                                  }else{
                               time_type = ' AM';
                               } 
                            }else{
                                time_type = ' PM';
                           }
                        } 
                } 
            }
            else{
                    if(specific_time_inp == 12 && flag == 0){
                        time_type = ' PM';
                        mid_time = specific_time_inp; 
                    }
                    else{
                        if(time_type_2 =="PM"){
                             if (specific_time_inp_2 == 12){
                                  time_type = ' AM'; 
                             }else{
                          time_type = ' PM';
                          }   
                        } else{
                        time_type = ' AM';
                        }
                        mid_time = specific_time_inp;
                    }
                }

                text += '<label class="checkbox-inline"><input type="checkbox" value="'+mid_time+':'+specific_min+' '+time_type+'" name="check" class="css-checkbox specific_time"/>' +mid_time+':'+specific_min+' '+time_type+'</label>';
            }

			var getcurrentid = $('#specifictimeselectedid').val();
			var specifictime = $('#specifictime'+getcurrentid).val();
			if(specifictime != ''){
				var sptimes = specifictime.split(',');
				for(var x=0; x<sptimes.length; x++){
				text += '<label class="checkbox-inline"><input type="checkbox" value="'+sptimes[x]+'" name="check" checked class="css-checkbox specific_time"/>' +sptimes[x]+'</label>';
				}
		    }

        $('#push_time').html(text);
    }

     $('#intervel_time').change(function(){

     	var val =  $('#intervel_time').val();
     	var val_1 = $('#specific_time_inp').val();

     	if($('#specific_interval').prop("checked")){
			if(val_1 > 0){
			if(val > 0){
				specific_data_find();
			}else{
				bootbox.alert("Please select intervel time.");
				return false;
			}
			}else{
				bootbox.alert("Please enter start time.");
				return false;	
			}
		}

     });

	// $("#specific_time_inp").keyup(function(){

	// 		var val =  $('#intervel_time').val();
	// 		var val_1 = $('#specific_time_inp').val();
	// 			if(val_1 > 0){
	// 			if(val > 0){
	// 				specific_data_find();
	// 			}else{
	// 				bootbox.alert("Please select intervel time.");
	// 				return false;
	// 			}
	// 			}else{
	// 				bootbox.alert("Please enter start time.");
	// 				return false;
	// 			}

	// });

		$('#time_type').change(function(){
			var val =  $('#intervel_time').val();
			var val_1 = $('#specific_time_inp').val();
			if(val != 0 && val_1 > 0 && val_1 <= 12){
				specific_data_find(); 
			}else{
				// bootbox.alert("Please select intervel time and enter start time.");
				// return false;
			}
		});


	$(document).ready(function(){
      var url = "<?php curPageName();?>";
      var value = "";
      $("select.language").change(function(){
        var selectedCountry = $(".language option:selected").val();
        if(selectedCountry != 'Chosse your language'){
        window.location.href = url+'?lang='+selectedCountry;
      }
      });
    });


   </script>
</body>
    <?php
    include('include/unset_session.php');
	?>
</html>
