<?php
session_start();
ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
function escape_string($var) {
	return mysql_real_escape_string($var);
}
$msg = '';
if (!empty($_POST['Hospital_Name']) !='' && !empty($_POST['Patient_Name']) !='' && !empty($_POST['IP_Registration']) !='') {
	//Hospital Details
	$Hospital_Name = escape_string($_POST['Hospital_Name']);
	$Hospital_ID = escape_string($_POST['Hospital_ID']);
	$hospital_type = escape_string($_POST['hospital_type']);
	$Doctor_Name = escape_string($_POST['Doctor_Name']);
	$Qualification = escape_string($_POST['Qualification']);
	$Registration_No = escape_string($_POST['Registration_No']);
	$cont_code = escape_string($_POST['cont_code']);
	$phone_no = escape_string($_POST['phone_no']);
	$area_code = escape_string($_POST['area_code']);
	// $date = escape_string($_POST['date']);
	$date = date("Y-m-d", strtotime($_POST['date']));
	$place = escape_string($_POST['place']);
	//Patient Details
	$Patient_Name = escape_string($_POST['Patient_Name']);
	$IP_Registration = escape_string($_POST['IP_Registration']);
	$gender = escape_string($_POST['gender']);
	$DateofBirth = date("Y-m-d", strtotime($_POST['DateofBirth']));
	$DateofAdmission =  date("Y-m-d", strtotime($_POST['DateofAdmission']));
	$DateofTime = escape_string($_POST['DateofTime']);
	$DateofDischarge = date("Y-m-d", strtotime($_POST['DateofDischarge']));
	$TimeofDischarge = escape_string($_POST['TimeofDischarge']);
	$type_admission = escape_string($_POST['type_admission']);
	$DateofDelivery = date("Y-m-d", strtotime($_POST['DateofDelivery']));
	$GravidaStatus = escape_string($_POST['GravidaStatus']);
	$status_time = escape_string($_POST['status_time']);
	$ClaimedAmount = escape_string($_POST['ClaimedAmount']);
	$q = mysql_query("SELECT max(Claim_ID) as LastClaimID FROM patient_details");
	$value = mysql_fetch_object($q);
	if($value->LastClaimID != 0){
    	$new_id = substr($value->LastClaimID,4);
    	$Claim_ID =  date("Y").$new_id+1;
     }else{
         $Claim_ID =  date("Y").'00000001';
     }
	$Patient_ID = 'PID-'.substr(str_shuffle("0123456789"), 0,5);
	$patient_query = "INSERT INTO patient_details (Patient_ID,Claim_ID,Patient_Name,Policy_No,Gender,Date_of_Birth,Date_of_Admission, Time_of_Admission,Date_of_Discharge,Time_of_Discharge,Type_of_Admission,Date_of_Delivery,Gravida_Status,Status_Time_of_Discharge,Total_Claimed_Amount,CreatedDate,CreatedBy) VALUES (
	'$Patient_ID', '$Claim_ID', '$Patient_Name', '$IP_Registration', '$gender', '$DateofBirth', '$DateofAdmission', '$DateofTime', '$DateofDischarge', '$TimeofDischarge', '$type_admission', '$DateofDelivery', '$GravidaStatus', '$status_time','$ClaimedAmount',now(),'')";
		if (mysql_query($patient_query))
		{
				$hospital_query = "INSERT INTO `hospital_details` (`Claim_ID`, `Hospital_Name`, `Hospital_ID`,`Doctor_Name`,`Qualification`,`Network_Type`,`Registration_No`, `Country_Code`, `Area_Code`, `Phone_No`,`Declaration_Date`,`Declaration_Place`,`CreatedDate`, `CreatedBy`) VALUES ('$Claim_ID', '$Hospital_Name', '$Hospital_ID','$Doctor_Name','$Qualification','$hospital_type','$Registration_No', '$cont_code', '$area_code', '$phone_no','$date','$place',now(),'')";
				mysql_query($hospital_query); 
                //diagnosis details
			    if( !empty($_POST["diagnosis"])){
					$diagnosis    =   $_POST["diagnosis"];
					$procedure    =   $_POST["procedure"];
					$query = 'INSERT INTO claim_diagnosis_procedure (`Claim_ID`, `ICD10CM`,`ICD10PCS`) VALUES ';
					$query_parts = array();
						for($x=0; $x<count($diagnosis); $x++){
						if($diagnosis[$x] !='' && $diagnosis[$x] !=''){
							$query_parts[] = "('$Claim_ID','" .$diagnosis[$x] . "', '" . $procedure[$x]."')";
						}else{}
						}
						$query .= implode(',',$query_parts);
						mysql_query($query);
                  }
					$auth = $_POST['auth'];
					$auth_number = $_POST['auth_number'];
					$auth_reason = $_POST['auth_reason'];
					$injury = $_POST['injury'];
					$cause = $_POST['cause'];
					$injury_reason = $_POST['injury_reason'];
					$medico = $_POST['medico'];
					$police = $_POST['police'];
					$fir_no = $_POST['fir_no'];
					$report_reason = $_POST['report_reason'];
					$patint_query = "INSERT INTO `patient_diagnosis_details` (`Claim_ID`, `Authorization_Obtained`, `Authorization_Number`, `Authorization_Reason`, `Hospitalization_Injury`, `Give_Cause`, `Test_Conducted`, `Medico_Legal`, `Reported_Police`, `Fir_No`, `Reported_Reason`, `CreatedDate`, `CreatedBy`) VALUES ('$Claim_ID','$auth', '$auth_number', '$auth_reason', '$injury','$cause','$injury_reason', '$medico', '$police', '$fir_no', '$report_reason',now(),'')";
		            mysql_query($patint_query);
		            // NON NETWORK HOSPITAL DETAILS
		            $AddressHospital = $_POST['AddressHospital'];
		            $city = $_POST['city'];
		            $state = $_POST['state'];
		            $PinCode = $_POST['PinCode'];
		            $contcode = $_POST['contcode'];
		            $areacode = $_POST['areacode'];
		            $phoneno = $_POST['phoneno'];
		            $RegistrationCode = $_POST['RegistrationCode'];
		            $HospitalPAN = $_POST['HospitalPAN'];
		            $PatientBeds = $_POST['PatientBeds'];
		            $icu = $_POST['icu'];
		            $OT = $_POST['OT'];
		            $others = $_POST['others'];
		            $non_hospital_query = "INSERT INTO `non_network_hospital` (`Claim_ID`, `Hospital_Address`, `City`, `State`, `Pin_Code`, `CountryCode`, `AreaCode`, `PhoneNo`, `Registration_No`, `Hospital_PAN`, `Beds`, `OT`, `ICU`, `Others`, `CreatedDate`, `CreatedBy`) VALUES ('$Claim_ID', '$AddressHospital', '$city', '$state', '$PinCode', '$contcode', ' $areacode', '$phoneno', '$RegistrationCode', '$HospitalPAN', '$PatientBeds', '$icu', '$OT', '$others',now(), '')";
		            mysql_query($non_hospital_query);
		    // CLAIM DOCUMENTS SUBMITTED
			$doc1 = $_POST['doc1'];
			$doc2 = $_POST['doc2'];
			$doc3 = $_POST['doc3'];
			$doc4 = $_POST['doc4'];
			$doc5 = $_POST['doc5'];
			$doc6 = $_POST['doc6'];
			$doc7 = $_POST['doc7'];
			$doc8 = $_POST['doc8'];
			$doc9 = $_POST['doc9'];
			$doc10 = $_POST['doc10'];
			$doc11 = $_POST['doc11'];
			$doc12 = $_POST['doc12'];
			$doc13 = $_POST['doc13'];
			$doc14 = $_POST['doc14'];
			$doc15 = $_POST['doc15'];
			$doc16 = $_POST['doc16'];
			$please_specify = $_POST['please_specify'];
			$doc_query = "INSERT INTO `patient_claim_documents` (`Claim_ID`, `duly_signed`, `Investigation_reports`, `original_authorization`, `ct_Investigation_reports`, `approval_letter`, `referance_slip`, `verified_by_hospital`, `ecg`, `discharge_summary`, `pharmacy_bills`, `police_fir`, `oparation_theatre_notes`, `hospital_main_bil`, `death_summary`, `break_up_bill`, `any_other`, `please_specify`, `CreatedDate`, `CreatedBy`) VALUES ('$Claim_ID', '$doc1', '$doc2', '$doc3', '$doc4', '$doc5', '$doc6', '$doc7', '$doc8','$doc9', '$doc10', '$doc11', '$doc12', '$doc13', '$doc14', '$doc15','$doc16','$please_specify',now(),'')";
			    if(mysql_query($doc_query)){
                 $msg = 'Patient Data Submited Successfully.';
			    }else{
			    	$msg = '';
			    }
		}
		else
		{
			$msg = "Error creating database: " . mysql_error();
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
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link type="text/css" href="css/bootstrap-timepicker.min.css" />
			<style>
			.list{float:left;list-style:none;margin-top:-3px;padding:0;width:190px;position: absolute; z-index: 99;}
			.list li{padding: 10px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
			.list li:hover{background:#ece3d2;cursor: pointer;}
			.error{color: red;font-size: 15px;font-weight: bold;}
			.dia_des{font-size: 15px;font-weight: normal;}
			.radio label, .checkbox label {font-weight: bold;}
			.area_code_errmsg{color: red;}
			.phone_no_errmsg{color: red;}
			.areacode_errmsg{color: red;}
			.phoneno_errmsg{color: red;}
			</style>
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
		<div id="plantitle">
			<?php $title = "CLAIM FORM - PART B"; ?>
			<span style="padding-left:0px;">
				<?php echo $title;?>  <?php if(isset($PatientList) &&  $PatientList !=''){echo '('.$PatientList.')';}?>  
			</span>
		</div>
	</div>
	<?php if($msg !=''){ ?>
		<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php echo $msg; ?>
		</div>
	<?php } ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="mainplanlistdiv" style="height: 680px;">
	<ul class="nav nav-tabs">
		<li class="active navbar_li">
			<a class="navbar_href" data-toggle="tab" href="#menu1">DETAILS OF HOSPITAL</a>
		</li>
		<li class="navbar_li">
			<a class="navbar_href" data-toggle="tab" href="#menu2">DETAILS OF PATIENT</a>
		</li>
		<li class="navbar_li">
			<a class="navbar_href" data-toggle="tab" href="#menu3">DETAILS OF DIAGNOSIS</a>
		</li>
		<li class="navbar_li">
			<a class="navbar_href" data-toggle="tab" href="#menu4">CLAIM DOCUMENTS</a>
		</li>
		<li class="navbar_li">
			<a class="navbar_href" data-toggle="tab" href="#menu5">DETAILS OF NON NETWORK HOSPITAL</a>
		</li>
	</ul>
	<div class="col-md-12 col-sm-12" style="height: 4px;top: -22px;">
	   <h2 style="color:#FE0000;text-align:center;font-size:16px; font-weight:bold;">
	   		( All colored fields are required )
	   </h2>
	</div>
	<div class="clearfix"></div>
<form data-toggle="validator" role="form" name="frm_assign_incurance" id="frm_assign_incurance" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
   <div class="tab-content">
	<div id="menu1" class="tab-pane fade in active"><br>
		<div id="pageheading" style="text-align: left;">DETAILS OF HOSPITAL</div>
		<div class="form-group col-sm-12">
			<label>Name of the Hospital</label>
			<input type="text" class="form-control" name="Hospital_Name" required>
		</div>
		<div class="form-group col-sm-6">
			<label>Hospital ID</label>
			<input type="text" class="form-control" name="Hospital_ID">
		</div>
      <div class="form-group col-sm-6">
	    <label>Type of Hospital</label>
			<div class="form-check">
			<input class="form-check-input" type="radio" name="hospital_type" value="1">
				<label class="form-check-label" for="gridCheck">
					Network
				</label>
				   &nbsp;&nbsp;
				<input class="form-check-input" type="radio" name="hospital_type" value="0">
				<label class="form-check-label" for="gridCheck">
				   Non Network 
				</label>
			</div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-sm-6">
	    <label>Name of the treating Doctor</label>
	    <input type="text" class="form-control" name="Doctor_Name">
    </div>
    <div class="form-group col-sm-6">
	    <label>Qualification</label>
	    <input type="text" class="form-control" name="Qualification">
    </div>
    <div class="form-group col-sm-6">
	   <label>Registration No. with state code</label>
	   <input type="text" class="form-control" name="Registration_No" id="Registration_No">
    </div>
    <div class="form-group col-sm-6">
	    <label>Mobile No.</label>
	     <div class="clearfix"></div>
	    <div class="col-sm-2" style="margin-left: -15px;">
	      <input type="text" maxlength="3" value="+91" class="form-control" name="cont_code" id="cont_code">
	    </div>
	    <div class="col-sm-3">
	      <input type="text" placeholder="Area Code" class="form-control" maxlength="3" name="area_code" id="area_code">&nbsp;<span class="area_code_errmsg">
	    </div>
	    <div class="col-sm-3">
	      <input type="text" placeholder="Phone No" maxlength="8" class="form-control" name="phone_no" id="phone_no">&nbsp;<span class="phone_no_errmsg">
	    </div>
    </div>
</div>

<div id="menu2" class="tab-pane fade">
    <div class="clearfix"><br></div>
    <div id="pageheading" style="text-align: left;">DETAILS OF PATIENT ADMITTED	</div>
     <div class="form-group col-sm-12">
	    <label>Name of the Patient</label>
	    <input type="text" class="form-control" name="Patient_Name" required>
    </div>
    <div class="form-group col-sm-6">
	    <label>IP Registration No.</label>
	    <input type="text" class="form-control" name="IP_Registration" required>
    </div>
    <div class="form-group col-sm-6">
	    <label>Gender</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="gender" value="M">
				<label class="form-check-label" for="gridCheck">
					Male
				</label>
				&nbsp;&nbsp;
				<input class="form-check-input" type="radio" name="gender" value="F">
				<label class="form-check-label" for="gridCheck">
				    Female
				</label>
				&nbsp;&nbsp;
				<input class="form-check-input" type="radio" name="gender" value="T">
				<label class="form-check-label" for="gridCheck">
				    Transgender
				</label>
			</div>
    </div>
    <div style="clear:both;"></div>
	<div class="form-group col-sm-4">
		<label>Date of Birth </label>
		<input type="text" class="form-control" name="DateofBirth" id="DateofBirth" required>
	</div>
    <div class="form-group col-sm-4">
	    <label>Date of Admission </label>
		<input type="text" class="form-control" name="DateofAdmission" id="DateofAdmission" required>
    </div>
    <div class="form-group col-sm-4">
	    <label>Time </label>
		<input id="DateofTime" name="DateofTime" type="text" class="form-control input-small">
    </div>
    <div style="clear:both;"></div>
    <div class="form-group col-sm-4">
	    <label>Date of Discharge </label>
		<input type="text" class="form-control" name="DateofDischarge" id="DateofDischarge" required>
    </div>
    <div class="form-group col-sm-4">
	    <label>Time</label>
		<input name="TimeofDischarge" id="TimeofDischarge" type="text" class="form-control input-small">
    </div>
    <div class="form-group col-sm-4">
	    <label>Type of Admission</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="type_admission" value="E">
				<label class="form-check-label" for="gridCheck">
					Emergency
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="type_admission" value="P">
				<label class="form-check-label" for="gridCheck">
				    Planned
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="type_admission" value="D">
				<label class="form-check-label" for="gridCheck">
				    Day Care
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="type_admission" value="M">
				<label class="form-check-label" for="gridCheck">
				    Maternity
				</label>
			</div>
    </div>
    <div style="clear:both;"></div>
    	<label style="margin-left: 15px;">If Maternity</label>
    <div style="clear:both;"></div>	
	<div class="col-sm-3 form-group">
		<label>Date of Delivery </label>
		<input type="text" class="form-control" name="DateofDelivery" id="DateofDelivery">
	</div>
	<div class="col-sm-2 form-group">
		<label>Gravida Status</label>
		<input type="text" class="form-control" name="GravidaStatus">
	</div>
    <div class="form-group col-sm-7">
	    <label>Status at time of Discharge :</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="status_time" value="H">
				<label class="form-check-label" for="gridCheck">
					Discharged at Home
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="status_time" value="AH">
				<label class="form-check-label" for="gridCheck">
				   Discharged to Another Hospital
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="status_time" value="DC">
				<label class="form-check-label" for="gridCheck">
				    Day Care
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="status_time" value="D">
				<label class="form-check-label" for="gridCheck">
				    Deceased
				</label>
			</div>
    </div>
    <div style="clear:both;"></div>
    <div class="form-group col-sm-4">
	   <label>Total Claimed Amount</label>
	   <input type="text" class="form-control" name="ClaimedAmount" required>
    </div>
   </div>
   <div id="menu3" class="tab-pane fade">
       <div style="clear:both;"><br></div>
    	<div id="pageheading" style="text-align: left;"> DETAILS OF AILMENT DIAGNOSED (PRIMARY)</div>
		<div class="col-sm-6">
			<label>Primary Diagnosis ( ICD 10 Codes ) </label>
			<input type="text" id="ICD1" name="diagnosis[]" class="form-control diagnosis">
			<p id="des_1" class="dia_des"></p>
		</div>
    	<div class="col-sm-6">
		    <label>Procedure ( ICD 10 PCS )</label>
			<input type="text" id="PCS1" name="procedure[]" class="form-control procedure">
			<p id="desp_1" class="dia_des"></p>
        </div>
        <div style="clear:both;"><br></div>
        <div class="col-sm-6">
			<label>Additional Diagnosis ( ICD 10 Codes ) </label>
			 <input type="text" id="ICD2" name="diagnosis[]" class="form-control diagnosis">
			 <p id="des_2" class="dia_des"></p>
		</div>
    	<div class="col-sm-6">
		    <label>Procedure ( ICD 10 PCS )</label>
			<input type="text" id="PCS2" name="procedure[]" class="form-control procedure">
			<p id="desp_2" class="dia_des"></p>
        </div>
        <div style="clear:both;"><br></div>
        <div class="col-sm-6">
			<label>Co-Morbidities ( ICD 10 Codes ) </label>
			 <input type="text" id="ICD3" name="diagnosis[]" class="form-control diagnosis">
			 <p id="des_3" class="dia_des"></p>
		</div>
    	<div class="col-sm-6">
		    <label>Procedure ( ICD 10 PCS )</label>
			<input type="text" id="PCS3" name="procedure[]" class="form-control procedure">
			<p id="desp_3" class="dia_des"></p>
        </div>
        <div style="clear:both;"><br></div>
        <div class="col-sm-6">
			<label>Co-Morbidities ( ICD 10 Codes ) </label>
			 <input type="text" id="ICD4" name="diagnosis[]" class="form-control diagnosis">
			 <p id="des_4" class="dia_des"></p>
		</div>
    	<div class="col-sm-6">
		    <label>Details of Procedure</label>
			<input type="text" id="PCS4" name="procedure[]" class="form-control procedure">
			<p id="desp_4" class="dia_des"></p>
        </div>
        <div style="clear:both;"><br></div>
		<div class="col-sm-3">
		<label>Pre Authorization Obtained</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="auth" value="1">
				<label class="form-check-label" for="gridCheck">Yes</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="auth" value="0">
				<label class="form-check-label" for="gridCheck">No</label>
			</div>
		</div>
	    <div class="col-sm-4">
		   <label>Pre Authorization Number :</label>
		   <input type="text" class="form-control" name="auth_number">
        </div>
        <div style="clear: both;"><br></div>
	    <div class="col-sm-12">
		    <label>If Authorization By Network Hospital not Obtained ,Give Reason </label>
		    <textarea class="form-control" name="auth_reason"></textarea>
	    </div>
        <div style="clear: both;"><br></div>
		<div class="col-sm-4">
		<label>Hospitalization due to injury</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="injury" value="1">
				<label class="form-check-label" for="gridCheck">
					Yes
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="injury" value="0">
				<label class="form-check-label" for="gridCheck">
				   No
				</label>
			</div>
		</div>
	    <div class="col-sm-8">
		    <label>If yes,give cause</label>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="cause" value="S">
					<label class="form-check-label" for="gridCheck">
						Self Inflicted
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="cause" value="R">
					<label class="form-check-label" for="gridCheck">
					   Road Traffic Accident
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="cause" value="SC">
					<label class="form-check-label" for="gridCheck">
					  Substance abuse / alcohol consumption
					</label>
				</div>
	     </div>
	     <div style="clear: both;"><br></div>
	     <div class="col-sm-4">
			<label>If Medico Legal</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="medico" value="1">
				<label class="form-check-label" for="gridCheck">Yes</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="medico" value="0">
				<label class="form-check-label" for="gridCheck">No</label>
			</div>
		 </div>
		  <div class="col-sm-8">
			    <label>If injurydue to Substance abuse / alcohol consumption, Test Conducted to establish this </label>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="injury_reason" value="1">
					<label class="form-check-label" for="gridCheck">Yes
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="injury_reason" value="0">
					<label class="form-check-label" for="gridCheck">No
					</label>
					<label class="form-check-label" for="gridCheck">
					&nbsp; &nbsp;(if yesy , attach reports)
					</label>
				</div>
		   </div> 
		<div style="clear: both;"><br></div>
	    <div class="col-sm-4">
		    <label> Reported to Police</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="police" value="1">
				<label class="form-check-label" for="gridCheck">Yes</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="police" value="0">
				<label class="form-check-label" for="gridCheck">No</label>
			</div>
	    </div>
    	<div class="col-sm-4">
		   <label>Fir No. </label>
		   <input type="text" class="form-control" name="fir_no">
        </div>
		<div class="col-sm-12">
			<label>If not Reported to  Police,give reason :</label>
			<textarea class="form-control" name="report_reason"></textarea>
		</div>
  </div>
 <div id="menu4" class="tab-pane fade">
      <br>
      <div id="pageheading" style="text-align: left;">CLAIM DOCUMENTS SUBMITTED - CHECKLIST</div>
	    	<div class="col-sm-4">
				<div class="checkbox">
				<label><input name="doc1" type="checkbox" value="1">Claim Form duly signed</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc2" type="checkbox" value="1">Investigation reports</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc3" type="checkbox" value="1">Original Pre-authorization request</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc4" type="checkbox" value="1">CT/ MRI/ USG/ HPE/ Investigation reports</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc5"  type="checkbox" value="1">Copy of the Pre-authorization approval letter</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc6" type="checkbox" value="1">Doctor's referance slip</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc7" type="checkbox" value="1">Copy of photo ID card of patient verified by hospital</label>
				</div>
	        </div>
	         <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc8" type="checkbox" value="1">ECG</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc9" type="checkbox" value="1">Hospital discharge summary</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc10" type="checkbox" value="1">Pharmacy bills</label>
				</div>
	        </div>
	         <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc11" type="checkbox" value="1">Oparation Theatre Notes</label>
				</div>
	        </div>
	         <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc12" type="checkbox" value="1">MLC report & Police FIR</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc13" type="checkbox" value="1">Hospital main bil</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc14" type="checkbox" value="1">Original death summary from hospital, where applicable</label>
				</div>
	        </div>
	        <div class="form-group col-sm-4">
				<div class="checkbox">
				<label><input name="doc15" type="checkbox" value="1">Hospital break-up bill</label>
				</div>
	        </div>
	          <div class="form-group col-sm-6">
				<div class="checkbox">
					<label><input name="doc16" type="checkbox" value="1">Any other, please specify</label>
				</div>
				<div class="col-sm-12">
					<input type="text" class="form-control" name="please_specify">
		        </div>
	          </div>
      <div style="clear: both;"><br></div>
</div>
	 <div id="menu5" class="tab-pane fade">
	 	<br>
      <div id="pageheading" style="text-align: left;">DETAILS IN CASE OF NON NETWORK HOSPITAL (ONLY FILL IN CASE OF NON NETWORK HOSPITAL)</div>
         <div class="col-sm-12">
		   <label>Address of the hospital</label>
		   <textarea class="form-control" name="AddressHospital"></textarea>
         </div>
         <div style="clear: both;"><br></div>
         <div class="col-sm-4">
		   <label>City</label>
		   <input type="text" class="form-control" name="city">
         </div>
         <div class="col-sm-4">
		   <label>State</label>
		   <input type="text" class="form-control" name="state">
         </div>
         <div class="col-sm-4">
		   <label>Pin Code</label>
		   <input type="text" class="form-control" name="PinCode">
         </div>
        <div style="clear: both;"><br></div>
        <div class="col-sm-6">
	    <label>Mobile No.</label>
	     <div class="clearfix"></div>
	      <div class="col-sm-2">
		      <input type="text" maxlength="3" value="+91" class="form-control" name="contcode" id="contcode" style="MARGIN-LEFT: -15PX;">
		    </div>
		    <div class="col-sm-3">
		      <input type="text" placeholder="Area Code" class="form-control" maxlength="3" name="areacode" id="areacode">&nbsp;<span class="areacode_errmsg">
		    </div>
		    <div class="col-sm-3">
		      <input type="text" placeholder="Phone No" maxlength="8" class="form-control" name="phoneno" id="phoneno">&nbsp;<span class="phoneno_errmsg">
		    </div>
         </div>
        <div style="clear: both;"><br></div>
		<div class="col-sm-4">
			<label>Registration No. with State Code</label>
			<input type="text" class="form-control" name="RegistrationCode">
		</div>
        <div class="col-sm-4">
		   <label>Hospital PAN</label>
		   <input type="text" class="form-control" name="HospitalPAN">
        </div>
        <div class="col-sm-4">
		   <label>Number of Patient Beds</label>
		   <input type="text" class="form-control" name="PatientBeds">
        </div>
        <div style="clear: both;"><br></div>
        <div class="col-sm-12">
          <label> Facilities available in the hospital</label>
        </div>
          <div class="form-group col-sm-4">
		        <label> OT</label>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="OT" value="1">
					<label class="form-check-label" for="gridCheck">
					  Yes
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="OT" value="0">
					<label class="form-check-label" for="gridCheck">
					   No
					</label>
				</div>
	     </div>
	     <div class="form-group col-sm-4">
			<label> ICU</label>
			   <div class="form-check">
					<input class="form-check-input" type="radio" name="icu" value="1">
					<label class="form-check-label" for="gridCheck">
						Yes
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="icu" value="0">
					<label class="form-check-label" for="gridCheck">
						No
					</label>
			  </div>
	     </div>
	      <div class="col-sm-12">
		   	<label>Others</label>
		   	 <input type="text" class="form-control" name="others">
          </div>
		<div style="clear: both;"><br></div>
			<div id="pageheading" style="text-align: left;">DECLARATION BY THE HOSPITAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Please read very carefully)</div>
				<p style="font-size: 17px;font-style: bold;">We hereby declare that the information furnished in this Claim Form is true & correct to the best of our knowledge and belief. If we have made any false or untrue statement, suppress or concealment of anu material fact, our right to claim under this claim shall be forfeited.</p>
				<div class="col-sm-6">
					<label for="inputPassword3" class="col-sm-2 col-form-label">Date:</label>
					<div class="col-sm-10">
					<input type="text" class="form-control" name="date" id="date" required>
					</div>
				</div>
				<div class="col-sm-6">
					<label for="inputPassword3" class="col-sm-2 col-form-label">Place:</label>
					<div class="col-sm-10">
					<input id="place" type="text" class="form-control" name="place" required>
					</div>
				</div>
       </div>
		<div id="ActionBar2" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px;">
			<button type="button" id="assigntouser" class="btns formbuttonsmall">SUBMIT </button>
			<button type="reset" id="cancelbutton" class="btns formbuttonsmall">RESET </button>
		</div>
		</div>
        </form>
	   </div>   
	 </section>
 </div>
</div>	
</div>
</div><!-- big_wrapper ends -->   
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script src="js/bootstrap3-typeahead.min.js"></script> 
<script>
$(document).ready(function () {
  $("#area_code").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        $(".area_code_errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
  $("#phone_no").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        $(".phone_no_errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
  $("#areacode").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        $(".areacode_errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
  $("#phoneno").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        $(".phoneno_errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

/**************************diagnosis********************/
	$('#ICD1').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value;
		},
		source: function (query, process) {
		return $.getJSON('ajax_diagnosis_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#des_1').html(item.desc);
        return item;
      }
    });

	$('#ICD2').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value;
		},
		source: function (query, process) {
		return $.getJSON('ajax_diagnosis_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#des_2').html(item.desc);
        return item;
      }
    });

	$('#ICD3').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value;
		},
		source: function (query, process) {
		return $.getJSON('ajax_diagnosis_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#des_3').html(item.desc);
        return item;
      }
    });

	$('#ICD4').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value;
		},
		source: function (query, process) {
		return $.getJSON('ajax_diagnosis_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#des_4').html(item.desc);
        return item;
      }
    });


	/**************************diagnosis********************/
	$('#PCS1').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value
		},
		source: function (query, process) {
		return $.getJSON('ajax_procedure_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#desp_1').html(item.desc);
        return item;
      }
    });

	$('#PCS2').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value
		},
		source: function (query, process) {
		return $.getJSON('ajax_procedure_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#desp_2').html(item.desc);
        return item;
      }
    });

	$('#PCS3').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value
		},
		source: function (query, process) {
		return $.getJSON('ajax_procedure_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#desp_3').html(item.desc);
        return item;
      }
    });

	$('#PCS4').typeahead({
		displayText: function(item) {
		return item.label
		},
		afterSelect: function(item) {
		this.$element[0].value = item.value
		},
		source: function (query, process) {
		return $.getJSON('ajax_procedure_code.php', { query: query }, function(data) {
		process(data)
		})
		},
		updater: function (item) {
		$('#desp_4').html(item.desc);
        return item;
      }
    });

	$( "#assigntouser" ).click(function() {
	   $( "#frm_assign_incurance" ).validate( {
				rules: {
					Hospital_Name: "required",
					Patient_Name: "required",
					IP_Registration:"required",
					DateofBirth:"required",
					DateofAdmission:"required",
					DateofDischarge:"required",
					ClaimedAmount:"required",
					date:"required",
					place:"required"
				},
				messages: {
					Hospital_Name: "Please Enter Hospital Name.",
					Patient_Name: "Please Enter Patient Name",
					IP_Registration: "Please Enter User Patient Policy ID.",
					DateofBirth: "Please Enter Date Of Birth.",
					DateofAdmission: "Please Enter Date Of Admission.",
					DateofDischarge: "Please Enter Date Of Discharge.",
					ClaimedAmount: "Please Enter Claimed Amount.",
					date: "Please Enter Declaration Date.",
					place: "Please Enter Declaration Place."
				},
				errorElement: "em",
				errorPlacement: function ( error, element ) {
					// Add the `help-block` class to the error element
					error.addClass( "help-block" );

					if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.parent( "label" ) );
					} else {
						error.insertAfter( element );
					}
				},
				highlight: function ( element, errorClass, validClass ) {
					// $( element ).parents( ".col-sm-6" ).addClass( "has-error" ).removeClass( "has-success" );
				},
				unhighlight: function (element, errorClass, validClass) {
					// $( element ).parents( ".col-sm-6" ).addClass( "has-success" ).removeClass( "has-error" );
				}
			} );
		$( "#frm_assign_incurance" ).submit();

		document.getElementById("frm_assign_incurance").reset();

	});

 	$('.nav-tabs a').click(function(){
    	$(this).tab('show');
    });
    $('#DateofTime').timepicker();
    $('#TimeofDischarge').timepicker();
    $(function() {
        $( "#DateofBirth" ).datepicker({
            dateFormat : 'mm/dd/yy',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });
    $(function() {
        $( "#DateofAdmission" ).datepicker({
            dateFormat : 'mm/dd/yy',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });
    $(function() {
        $( "#DateofDischarge" ).datepicker({
            dateFormat : 'mm/dd/yy',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });
    $(function() {
        $( "#DateofDelivery" ).datepicker({
            dateFormat : 'mm/dd/yy',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });
    $(function() {
        $( "#date" ).datepicker({
            dateFormat : 'mm/dd/yy',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
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
	$(document).ready(function() {
        var w = window.innerWidth;
        var h = window.innerHeight;
        var total = h - 150;
        var each = total/12;
        $('.navbar_li').height(each);
        $('.navbar_href').height(each/2);
        $('.navbar_href').css('padding-top', each/4.9);
        var currentpage = "claim_form1";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Patients");
        var windowheight = h;
        var available_height = h - 150;
        $('#mainplanlistdiv').height(available_height);
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