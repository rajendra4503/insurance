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
	if($value !=''){
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
			mysql_query($doc_query);          
		}
		else
		{
			echo "Error creating database: " . mysql_error();
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
			.errmsg{color: red;}
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
         <?php $title = "CLAIM FORM - PART B"; ?>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">

			<div id="plantitle"><span style="padding-left:165px;"><?php echo $title;?>  <?php if(isset($PatientList) &&  $PatientList !=''){echo '('.$PatientList.')';}?>  </span></div>
		</div>

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
	</ul>

<form name="frm_assign_incurance" id="frm_assign_incurance" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">

   <div class="tab-content">
	  <div id="menu1" class="tab-pane fade in active">
        <br>
	<div id="pageheading" style="text-align: left;">DETAILS OF HOSPITAL</div>
    <div class="form-group col-sm-12">
	    <label for="inputEmail3" class="col-sm-2 col-form-label">Name of the Hospital : </label>
	    <div class="col-sm-9">
	      <input type="text" class="form-control" name="Hospital_Name" required>
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-4 col-form-label">Hospital ID : </label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" name="Hospital_ID">
	    </div>
    </div>

      <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-4 col-form-label">Type of Hospital :</label>
	    <div class="col-sm-8">
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
    </div>

    <div class="form-group col-sm-12">
	    <label for="inputEmail3" class="col-sm-3 col-form-label">Name of the treating Doctor : </label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" name="Doctor_Name">
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-3 col-form-label">Qualification : </label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" name="Qualification">
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-6 col-form-label">Registration No. with state code : </label>
	    <div class="col-sm-6">
	      <input type="text" class="form-control" name="Registration_No" id="Registration_No">
	    </div>
    </div>

    <div class="form-group col-sm-12">
	    <label for="inputPassword3" class="col-sm-2 col-form-label">Mobile No. :</label>
	    
	    <div class="col-sm-1">
	      <input type="text" maxlength="3" value="+91" class="form-control" name="cont_code" id="cont_code">
	    </div>

	    <div class="col-sm-3">
	      <input type="text" placeholder="Area Code" class="form-control digit" maxlength="3" name="area_code" id="area_code">&nbsp;<span class="errmsg">
	    </div>

	    <div class="col-sm-3">
	      <input type="text" placeholder="Phone No" maxlength="8" class="form-control digit" name="phone_no" id="phone_no">&nbsp;<span class="errmsg">
	    </div>

    </div>
</div>

<div id="menu2" class="tab-pane fade">

    <div style="clear: both;"><br></div>
    <div id="pageheading" style="text-align: left;">DETAILS OF PATIENT ADMITTED	</div>

     <div class="form-group col-sm-12">
	    <label for="inputEmail3" class="col-sm-2 col-form-label">Name of the Patient :</label>
	    <div class="col-sm-9">
	      <input type="text" class="form-control" name="Patient_Name" required>
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-4 col-form-label">IP Registration No. :</label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" name="IP_Registration" required>
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-3 col-form-label">Gender :</label>
	    <div class="col-sm-8">
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
				<input class="form-check-input" type="radio" name="gender" value="T">
				<label class="form-check-label" for="gridCheck">
				    Transgender
				</label>
			</div>
   
	    </div>
    </div>
    <div style="clear:both;"></div>

    <div class="form-group col-sm-12">
	     <!--  <label for="inputPassword3" class="col-sm-1 col-form-label">Age :</label>
	      <div class="col-sm-3">
				<label for="inputPassword3" class="col-sm-4 col-form-label">Years :</label>
				 <div class="col-sm-8">
				<input class="form-control" type="text" name="Years" id="Years">
			</div>
		  </div>
		  <div class="col-sm-3">		
			<label for="inputPassword3" class="col-sm-4 col-form-label">Months:</label>
				 <div class="col-sm-8">
				<input class="form-control" type="text"  name="Months" id="Months">
			</div>
	     </div> -->
   		 <div class="col-sm-5">
		     <label for="inputPassword3" class="col-sm-4 col-form-label">Date of Birth :</label>
			 <div class="col-sm-8">
			<input type="text" class="form-control" name="DateofBirth" id="DateofBirth" required>
			</div>
         </div>
     </div>

      <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-4 col-form-label">Date of Admission :</label>
	    <div class="col-sm-8">
				<input type="text" class="form-control" name="DateofAdmission" id="DateofAdmission" required>
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-2 col-form-label">Time :</label>
	    <div class="col-sm-8">
				<input id="DateofTime" name="DateofTime" type="text" class="form-control input-small">
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-4 col-form-label">Date of Discharge :</label>
	    <div class="col-sm-8">
				<input type="text" class="form-control" name="DateofDischarge" id="DateofDischarge" required>
	    </div>
    </div>

    <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-2 col-form-label">Time</label>
	    <div class="col-sm-8">
				<input name="TimeofDischarge" id="TimeofDischarge" type="text" class="form-control input-small">
	    </div>

    </div>

    <div class="form-group col-sm-12">
	    <label for="inputPassword3" class="col-sm-2 col-form-label">Type of Admission :</label>
	    <div class="col-sm-8">
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
    </div>
   
    

    <div class="form-group col-sm-12">

	    <label class="col-sm-2 col-form-label">If Maternity :</label>

			<div class="col-sm-5">
				<label for="inputPassword3" class="col-sm-4">Date of Delivery :</label>
				<div class="col-sm-8">
				<input type="text" class="form-control" name="DateofDelivery" id="DateofDelivery">
				</div>
			</div>

			<div class="col-sm-5">
				<label for="inputPassword3" class="col-sm-4">Gravida Status :</label>
				<div class="col-sm-8">
				<input type="text" class="form-control" name="GravidaStatus">
				</div>
			</div>
    </div>

    <div class="form-group col-sm-12">
	    <label for="inputPassword3" class="col-sm-3 col-form-label">Status at time of Discharge :</label>
	    <div class="col-sm-8">
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
    </div>

     <div class="form-group col-sm-6">
	    <label for="inputPassword3" class="col-sm-4 col-form-label">Total Claimed Amount :</label>
	    <div class="col-sm-8">
				<input type="text" class="form-control" name="ClaimedAmount" required>
	    </div>
    </div>
   </div>

   <div id="menu3" class="tab-pane fade">

    <div style="clear:both;"><br></div>
    	<div id="pageheading" style="text-align: left;"> DETAILS OF AILMENT DIAGNOSED (PRIMARY)</div>

    	<div class="col-sm-12">
    		<label class="col-sm-3 col-form-label">Primary Diagnosis :</label>
    	</div>	

    	<div class="col-sm-12">
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 Codes</label>
		    <div class="col-sm-9">
				<input type="text" id="1" name="diagnosis[]" class="form-control diagnosis">
				<div id="search_ICD10Codes_1"></div>
		    </div>
        </div>
        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10Codes_description_1">
				<!-- <input type="text" class="form-control" name="primary_description" id="primary_description"> -->
		    </div>
        </div>
    </div>
        <div class="col-sm-12">
    			<label class="col-sm-2 col-form-label">Procedure 1 :</label>
    	</div>	
    <div class="col-sm-12">
    
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 PCS</label>
		    <div class="col-sm-9">
				<select class="form-control change_value" name="procedure[]" id="ICD10PCS_procedure_1">
				</select>
		    </div>
        </div>

        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10PCS_description_1">

				<!-- <input type="text" class="form-control" name="primary_procedure_description" id="primary_procedure_description"> -->
		    </div>
        </div>
    </div>


<div class="col-sm-12">
    		<label class="col-sm-3 col-form-label">Additional Diagnosis :</label>
    	</div>	
    	<div class="col-sm-12">
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 Codes</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control diagnosis" id="2" name="diagnosis[]">
				<div id="search_ICD10Codes_2"></div>
		    </div>
        </div>
        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10Codes_description_2">
				
		    </div>
        </div>
    </div>
        <div class="col-sm-12">
    			<label class="col-sm-2 col-form-label">Procedure 2 :</label>
    	</div>	
    <div class="col-sm-12">
    
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 PCS</label>
		    <div class="col-sm-9">
				<select class="form-control change_value" name="procedure[]" id="ICD10PCS_procedure_2">
				</select>
		    </div>
        </div>

        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10PCS_description_2">
				
		    </div>
        </div>
    </div>

        <div class="col-sm-12">
    		<label class="col-sm-3 col-form-label">Co-Morbidities :</label>
    		<br><br>
    	</div>	

    	<div class="col-sm-12">
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 Codes</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control diagnosis" id="3" name="diagnosis[]">
				<div id="search_ICD10Codes_3"></div>
		    </div>
        </div>
        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10Codes_description_3">
				
		    </div>
        </div>
    </div>
        <div class="col-sm-12">
    			<label class="col-sm-2 col-form-label">Procedure 3 :</label>
    	</div>	
    <div class="col-sm-12">
    
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 PCS</label>
		    <div class="col-sm-9">
				<select class="form-control change_value" name="procedure[]" id="ICD10PCS_procedure_3">
				</select>
		    </div>
        </div>

        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10PCS_description_3">
				
		    </div>
        </div>
    </div>

    <div class="col-sm-12">
    		<label class="col-sm-3 col-form-label">Co-Morbidities :</label>
    		<br><br>
    	</div>	

    	<div class="col-sm-12">
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">ICD 10 Codes</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control diagnosis" id="4" name="diagnosis[]">
				<div id="search_ICD10Codes_4"></div>
		    </div>
        </div>
        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9">
				<div class="col-sm-9" id="ICD10Codes_description_4">
				
		    </div>
		    </div>
        </div>
    </div>
        <div class="col-sm-12">
    			<label class="col-sm-2 col-form-label">Details of Procedure :</label>
    	</div>	
    <div class="col-sm-12">
    
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9">
				<select class="form-control change_value" name="procedure[]" id="ICD10PCS_procedure_4">
				</select>
		    </div>
        </div>

        <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Description :</label>
		    <div class="col-sm-9" id="ICD10PCS_description_4">
				
		    </div>
        </div>
    </div>

    	<div class="form-group col-sm-12">

    	<div class="form-group col-sm-6">
		    <label for="inputPassword3" class="col-sm-5 col-form-label">Pre Authorization Obtained :</label>
		    <div class="col-sm-7">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="auth" value="1">
					<label class="form-check-label" for="gridCheck">
						Yes
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="auth" value="0">
					<label class="form-check-label" for="gridCheck">
					   No
					</label>
				</div>
		    </div>
	    </div>

	    <div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-5 col-form-label">Pre Authorization Number :</label>
		    <div class="col-sm-7">
				<input type="text" class="form-control" name="auth_number">
		    </div>
        </div>

    </div>

    <div class="form-group col-sm-12">
	    <label for="inputEmail3" class="col-sm-12 col-form-label">If Authorization By Network Hospital not Obtained ,Give Reason : </label>
	    <div class="col-sm-12">
	        <textarea class="form-control" name="auth_reason"></textarea>
	    </div>
    </div>

    <div class="form-group col-sm-12">

    	<div class="form-group col-sm-4">

		    <label for="inputPassword3" class="col-sm-7 col-form-label">Hospitalization due to injury :</label>
		    <div class="col-sm-5">
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

	    </div>

	    <div class="form-group col-sm-8">
		    <label for="inputPassword3" class="col-sm-2 col-form-label">If yes,give cause :</label>
		    <div class="col-sm-10">
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
	    </div>
	 </div> 

	<div class="form-group col-sm-12">
		    <label for="inputPassword3" class="col-sm-7 col-form-label">If injurydue to Substance abuse / alcohol consumption, Test Conducted to establish this :</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="injury_reason" value="1">
				<label class="form-check-label" for="gridCheck">
					Yes
				</label>
				&nbsp;
				<input class="form-check-input" type="radio" name="injury_reason" value="0">
				<label class="form-check-label" for="gridCheck">
				   No
				</label>
				<label class="form-check-label" for="gridCheck">
				    &nbsp; &nbsp;(if yesy , attach reports)
				</label>
			</div>
	</div> 

	 <div class="form-group col-sm-12">

    	<div class="form-group col-sm-6">
		    <label for="inputPassword3" class="col-sm-4 col-form-label">If Medico Legal :</label>
		    <div class="col-sm-5">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="medico" value="1">
					<label class="form-check-label" for="gridCheck">
						Yes
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="medico" value="0">
					<label class="form-check-label" for="gridCheck">
					   No
					</label>
				</div>
		    </div>
	    </div>

	    <div class="form-group col-sm-6">
		    <label for="inputPassword3" class="col-sm-4 col-form-label"> Reported to Police :</label>
		    <div class="col-sm-5">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="police" value="1">
					<label class="form-check-label" for="gridCheck">
						Yes
					</label>
					&nbsp;
					<input class="form-check-input" type="radio" name="police" value="0">
					<label class="form-check-label" for="gridCheck">
					   No
					</label>
				</div>
		    </div>
	    </div>
    </div>


    	<div class="col-sm-12">
    
    	<div class="form-group col-sm-6">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Fir No. :</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control" name="fir_no">
		    </div>
        </div>

    </div>
       
    <div class="col-sm-12">
		   <label for="inputPassword3" class="col-sm-12">If not Reported to  Police,give reason :</label>
		    <div class="col-sm-12">
				<textarea class="form-control" name="report_reason"></textarea>
		    </div>
    </div>
</div>
 <div id="menu4" class="tab-pane fade">

    <div style="clear: both;"><br></div>

      <div id="pageheading" style="text-align: left;">CLAIM DOCUMENTS SUBMITTED - CHECKLIST</div>

        <div class="col-sm-12">
    
	    	<div class="form-group col-sm-4">
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
        </div>

     <div style="clear: both;"><br></div>

        <div id="pageheading" style="text-align: left;">DETAILS IN CASE OF NON NETWORK HOSPITAL (ONLY FILL IN CASE OF NON NETWORK HOSPITAL)</div>

         <div class="col-sm-12">
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Address of the hospital :</label>
		    <div class="col-sm-9">
		    	<textarea class="form-control" name="AddressHospital"></textarea>
		    </div>
         </div>

        <div class="col-sm-6">
           <br>
		   <label for="inputPassword3" class="col-sm-3 col-form-label">City :</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control" name="city">
		    </div>
        </div>

        <div class="col-sm-6">
           <br>
		   <label for="inputPassword3" class="col-sm-3 col-form-label">State :</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control" name="state">
		    </div>
        </div>

        <div class="col-sm-6">
           <br>
		   <label for="inputPassword3" class="col-sm-3 col-form-label">Pin Code :</label>
		    <div class="col-sm-9">
				<input type="text" class="form-control" name="PinCode">
		    </div>
        </div>

         <div style="clear: both;">&nbsp;</div>

        <div class="col-sm-12">

	    <label for="inputPassword3" class="col-sm-2 col-form-label">Mobile No. :</label>
	    
	    <div class="col-sm-1">
	      <input type="text" maxlength="3" value="+91" class="form-control" name="contcode" id="contcode">
	    </div>

	    <div class="col-sm-3">
	      <input type="text" placeholder="Area Code" class="form-control" maxlength="3" name="areacode" id="areacode">
	    </div>

	    <div class="col-sm-3">
	      <input type="text" placeholder="Phone No" maxlength="8" class="form-control" name="phoneno" id="phoneno">
	    </div>

    </div>

        <div class="col-sm-6">
           <br>
		   <label for="inputPassword3" class="col-sm-4 col-form-label">Registration No. with State Code :</label>
		    <div class="col-sm-8">
				<input type="text" class="form-control" name="RegistrationCode">
		    </div>
        </div>

        <div class="col-sm-6">
           <br>
		   <label for="inputPassword3" class="col-sm-4 col-form-label">Hospital PAN :</label>
		    <div class="col-sm-8">
				<input type="text" class="form-control" name="HospitalPAN">
		    </div>
        </div>

        <div class="col-sm-6">
           <br>
		   <label for="inputPassword3" class="col-sm-4 col-form-label">Number of Patient Beds :</label>
		    <div class="col-sm-8">
				<input type="text" class="form-control" name="PatientBeds">
		    </div>
        </div>

         <div class="col-sm-12">

           <label for="inputPassword3" class="col-sm-4 col-form-label"> Facilities available in the hospital :</label>

         <div class="col-sm-12">
	

          <div class="form-group col-sm-4">
		    <label for="inputPassword3" class="col-sm-2 col-form-label"> OT :</label>
		    <div class="col-sm-5">
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
	     </div>

	     <div class="form-group col-sm-4">
		    <label for="inputPassword3" class="col-sm-3 col-form-label"> ICU :</label>
		    <div class="col-sm-5">
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
	     </div>
	      <div class="col-sm-12">
		   <label for="inputPassword3" class="col-sm-2 col-form-label">Others :</label>
		    <div class="col-sm-10">
				<input type="text" class="form-control" name="others">
		    </div>
        </div>
	     </div>
	    </div>

			<div style="clear: both;"><br></div>

			<div id="pageheading" style="text-align: left;">DECLARATION BY THE HOSPITAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Please read very carefully)</div>

			<div class="col-sm-12">

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
       </div>

		<div id="ActionBar2" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px;">
			<button type="button" id="assigntouser" class="btns formbuttonsmall">SUBMIT </button>
			<button type="button" id="cancelbutton" class="btns formbuttonsmall">RESET </button>
		</div>

		</div>						
        </form>
	   </div>   
	 </section>
   </div>
</div>		
</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-timepicker.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
	<script>


$(document).ready(function () {
  $(".digit").keypress(function (e) {
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        $(".errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

	/**************************primary diagnosis**********************************/
	$(document).ready(function(){

		$(".diagnosis").keyup(function(){

			var ID = $(this).attr("id");

            $('#ICD10Codes_description_'+ID).html('');

            $('#ICD10PCS_procedure_'+ID).html('');

            $('#ICD10PCS_description_'+ID).html('');

			$.ajax({
			type: "POST",
			url: "search.php",
			data:'type=primary&selectId='+ID+'&keyword='+$(this).val(),
			beforeSend: function(){
				$("#"+ID).css("background","#FFF url(images/LoaderIcon.gif) no-repeat 165px");
			},
			success: function(data){
				$("#search_ICD10Codes_"+ID).show();
				$("#search_ICD10Codes_"+ID).html(data);
				$("#"+ID).css("background","#FFF");
			}
			});
		});
	});

	function selectValue(val,id) {

		$("#"+id).val(val);

		$("#search_ICD10Codes_"+id).hide();

		$.ajax({
			type: "POST",
			url: "search.php",
			data:'type=primary&selectId='+id+'&description='+val,
			success: function(data){
				$("#ICD10Codes_description_"+id).html(data);
			 }
		});

		$.ajax({
			type: "POST",
			url: "search.php",
			data:'type=primary&selectId='+id+'&procedure='+val,
			success: function(data){
				$("#ICD10PCS_procedure_"+id).html(data);
			 }
		});
	}

	$('.change_value').on('change', function() {
	     var id = $(this).attr("id");
	     var val = id.split("_");
         var number = val[2];
	  	 $.ajax({
			type: "POST",
			url: "search.php",
			data:'type=primary&selectId='+number+'&procedure_description='+this.value,
			success: function(data){
				$("#ICD10PCS_description_"+number).html(data);
			 }
		});
	});

</script>

    <script type="text/javascript">

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
					$( element ).parents( ".col-sm-6" ).addClass( "has-error" ).removeClass( "has-success" );
				},
				unhighlight: function (element, errorClass, validClass) {
					$( element ).parents( ".col-sm-6" ).addClass( "has-success" ).removeClass( "has-error" );

				}
			} );

		$( "#frm_assign_incurance" ).submit();

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
        var available_height = h - 210;
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