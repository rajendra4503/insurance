<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;

include('include/configinc.php');
include('include/session.php');
include('include/functions.php');

//$_SESSION['plancode_for_current_plan'] = "IN0000000030";
$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
$userid_for_current_plan 	= $_SESSION['userid_for_current_plan'];
$planname_for_current_plan_text = "Click to edit Plan Details";


//GET PATIENT DATAS
$patient_data_flag = 0;
$get_patient_data  	= "select ID, UserID, MerchantID, Height, Weight, BloodPressure, Temperature, CreatedDate from VISIT_DATA where UserID='$userid_for_current_plan' and MerchantID = '$logged_merchantid'";
// /echo $get_patient_data;exit;
$patient_data_run 	= mysql_query($get_patient_data);
$patient_data_count 	= mysql_num_rows($patient_data_run);
if($patient_data_count > 0){
	$dataID 		= array();
	$Height 		= array();
	$Weight 		= array();
	$BloodPressure 	= array();
	$Temperature 	= array();
	$CreatedDate 	= array();
	while ($patient_data = mysql_fetch_array($patient_data_run)) {
		$dataID[$patient_data_flag] 			= $patient_data['ID'];
		$Height[$patient_data_flag] 			= $patient_data['Height'];
		$Weight[$patient_data_flag] 			= $patient_data['Weight'];
		$BloodPressure[$patient_data_flag] 		= $patient_data['BloodPressure'];
		$Temperature[$patient_data_flag] 		= $patient_data['Temperature'];
		$CreatedDate[$patient_data_flag] 		= $patient_data['CreatedDate'];
		$patient_data_flag++;
	}
}
//Get patient notes
$patient_notes_flag = 0;
$get_patient_notes  	= "select ID, UserID, MerchantID, Notes, CreatedDate from VISIT_NOTES where UserID='$userid_for_current_plan' and MerchantID = '$logged_merchantid'";
// /echo $get_patient_notes;exit;
$patient_notes_run 	= mysql_query($get_patient_notes);
$patient_notes_count 	= mysql_num_rows($patient_notes_run);
if($patient_notes_count > 0){
	$Notes 		= array();
	$CreatedDate2 	= array();
	while ($patient_notes = mysql_fetch_array($patient_notes_run)) {
		$Notes[$patient_notes_flag] 			= $patient_notes['Notes'];
		$CreatedDate2[$patient_notes_flag] 		= $patient_notes['CreatedDate'];
		$patient_notes_flag++;
	}
}
//Get review notes
$patient_review_flag = 0;
$get_patient_review    = "select ID, UserID, MerchantID, Notes, CreatedDate,TimeSpent from REVIEW_NOTES where UserID='$userid_for_current_plan' and MerchantID = '$logged_merchantid'";
// /echo $get_patient_review;exit;
$patient_review_run  = mysql_query($get_patient_review);
$patient_review_count  = mysql_num_rows($patient_review_run);
if($patient_review_count > 0){
  $Review    = array();
  $CreatedDate3   = array();
  while ($patient_review = mysql_fetch_array($patient_review_run)) {
    $Review[$patient_review_flag]       = $patient_review['Notes'];
    $CreatedDate3[$patient_review_flag]    = $patient_review['CreatedDate'];
    $patient_review_flag++;
  }
}
//Get Patient History
$UserHistory = "";
$get_patient_history  	= "select UserHistory from USER_DETAILS where UserID='$userid_for_current_plan'";
//echo $get_patient_history;exit;
$patient_history_run 	= mysql_query($get_patient_history);
$patient_history_count 	= mysql_num_rows($patient_history_run);
if($patient_history_count > 0){
	while ($patient_hist = mysql_fetch_array($patient_history_run)) {
		$UserHistory			= $patient_hist['UserHistory'];
	}
}

$display_print = "<div id='printtable' style='border:1px solid #004F35;width:100%;background-color: #004F35;color:#fff;height:40px;text-align:center;line-height:40px;font-size:26px;    font-family:RalewayBold;'>PATIENT INFORMATION</div>";
          $get_profile_details1 = "select t1.FirstName, t1.LastName, t1.Gender, t1.DOB, t1.BloodGroup, t1.CountryCode, t1.StateID, t1.CityID, t2.MobileNo, t2.EmailID, t1.AddressLine1, t1.AddressLine2, t1.PinCode, t1.AreaCode, t1.Landline, t1.MobilePhoneType, t1.LanguageID,t1.SupportPersonName,t1.SupportPersonMobileNo from USER_DETAILS as t1, USER_ACCESS as t2 where t1.UserID = t2.UserID and t1.UserID = '$userid_for_current_plan'";
            //echo $get_profile_details1;exit;
            $get_profile_details = mysql_query($get_profile_details1);
            $get_profile_count = mysql_num_rows($get_profile_details);
            if($get_profile_count > 0){
              while ($details = mysql_fetch_array($get_profile_details)) {
                $det_firstname    = $details['FirstName'];
                $det_lastname     = $details['LastName'];
                $det_gender     = $details['Gender'];
                if(($details['DOB'] != "")&&($details['DOB'] != "0000-00-00")&&($details['DOB'] != NULL)){
                  $det_dobday     = date('d',strtotime($details['DOB']));
                  $det_dobmon     = date('M',strtotime($details['DOB']));
                  $det_dobyear    = date('Y',strtotime($details['DOB']));
                } else {
                  $det_dobday     = "";
                  $det_dobmon     = "";
                  $det_dobyear    = "";
                }
                $det_countrycode    = $details['CountryCode'];
                $det_bloodgroup     = $details['BloodGroup'];
                if($det_bloodgroup == ""){
                  $det_bloodgroup = "-";
                }
                $det_countrycall      = "+".ltrim($det_countrycode, '0');
                $det_stateid      = $details['StateID'];
                $det_cityid       = $details['CityID'];
                $det_mobileno       = substr($details['MobileNo'], 5);
                $det_emailid      = $details['EmailID'];
                $det_addressline1     = stripslashes($details['AddressLine1']);
                $det_addressline2     = stripslashes($details['AddressLine2']);
                $det_pincode      = $details['PinCode'];
                if($det_pincode == "0"){
                  $det_pincode = "";
                }
                $det_areacode       = $details['AreaCode'];
                if($det_areacode == "0"){
                  $det_areacode = "";
                }
                $det_landline       = $details['Landline'];
                $det_mobile_type    = $details['MobilePhoneType'];
                $det_language       = $details['LanguageID'];
                $det_support_pers_name  = stripslashes($details['SupportPersonName']);
                $det_support_pers_mobile= $details['SupportPersonMobileNo'];
              }
            }
              $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'><tr><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Name : <b>$det_firstname $det_lastname</b></td><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Gender : <b>$det_gender</b></td></tr><tr><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;background-color:#F1F1F1;'>DOB : <b>$det_dobday $det_dobmon $det_dobyear</b></td><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;background-color:#F1F1F1;'>Blood Group : <b>$det_bloodgroup</b></td></tr></table>";

  $get_profile_details2 = mysql_query("select t1.FirstName, t1.LastName, t1.CountryCode, t2.MobileNo, t2.EmailID from USER_DETAILS as t1, USER_ACCESS as t2, USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID and t3.MerchantID = '$logged_merchantid' and t3.RoleID='1' and t2.UserID = t3.UserID");
  //echo $get_profile_details;exit;
  $get_profile_count2 = mysql_num_rows($get_profile_details2);
  if($get_profile_count2 > 0){
    while ($details2 = mysql_fetch_array($get_profile_details2)) {
      //echo "<pre>";print_r($details);exit;
      $det2_firstname    = stripslashes($details2['FirstName']);
      $det2_lastname     = stripslashes($details2['LastName']);
      $det2_countrycode   = $details2['CountryCode'];
      $det2_countrycall   = "+".ltrim($det2_countrycode, '0');
      $det2_mobileno      = substr($details2['MobileNo'], 5);
      $det2_emailid       = $details2['EmailID'];
    }
  }

    $get_company_details = mysql_query("select CompanyName, CompanyRegistrationNo, CompanyEmailID, CompanyMobileNo, CompanyAreaCode1, 
              CompanyLandline1, CompanyWebsiteURL, CompanyCountryCode, CompanyStateID, CompanyCityID, 
              CompanyAddressLine1, CompanyAddressLine2, CompanyPinCode from MERCHANT_DETAILS 
              where MerchantID = '$logged_merchantid'");
  //echo $get_company_details;exit;
  $get_company_count = mysql_num_rows($get_company_details);
  if($get_company_count > 0){
    while ($cdetails = mysql_fetch_array($get_company_details)) {
      $cdet_companyname   = stripslashes($cdetails['CompanyName']);
      $cdet_regno       = stripslashes($cdetails['CompanyRegistrationNo']);
      $cdet_emailid     = $cdetails['CompanyEmailID'];
      $cdet_mobileno    = $cdetails['CompanyMobileNo'];
      $cdet_areacode    = $cdetails['CompanyAreaCode1'];
      $cdet_landline    = $cdetails['CompanyLandline1'];
      $cdet_weburl    = stripslashes($cdetails['CompanyWebsiteURL']);
      $cdet_countrycode   = $cdetails['CompanyCountryCode'];
      //$cdet_countrycode    = "+".ltrim($cdet_countrycode, '0');
      $cdet_stateid     = $cdetails['CompanyStateID'];
      $cdet_cityid    = $cdetails['CompanyCityID'];
      $cdet_addressline1  = stripslashes($cdetails['CompanyAddressLine1']);
      $cdet_addressline2  = stripslashes($cdetails['CompanyAddressLine2']);
      $cdet_pincode     = $cdetails['CompanyPinCode'];
    }
  }

  $display_print .= "<div id='printtable' style='border:1px solid #004F35;width:100%;background-color: #004F35;color:#fff;height:40px;text-align:center;line-height:40px;font-size:26px;font-family:RalewayBold;'>DOCTOR INFORMATION</div>";

  $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'><tr><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Name : <b>$det2_firstname $det2_lastname</b></td><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Mobile : <b>$det2_mobileno</b></td></tr><tr><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;background-color:#F1F1F1;'>Email : <b>$det2_emailid</b></td><td style='width:49%;padding-left:5px;border:1px solid #D3D3D3;height:35px;background-color:#F1F1F1;'>Organization : <b>$cdet_companyname</b></td></tr></table>";

$display_print .= "<div id='printtable' style='border:1px solid #004F35;width:100%;background-color: #004F35;color:#fff;height:40px;text-align:center;line-height:40px;font-size:26px;font-family:RalewayBold;'>PLAN INFORMATION</div>";

$get_plan_details = "select t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from USER_PLAN_HEADER as t1, CATEGORY_MASTER as t2 where t1.PlanCode = '$plancode_for_current_plan' and t1.CategoryID = t2.CategoryID and t1.UserID = '$userid_for_current_plan'";
//echo $get_plan_details;exit;
$get_plan_details_run = mysql_query($get_plan_details);
$get_plan_details_count = mysql_num_rows($get_plan_details_run);
	if($get_plan_details_count > 0){
		while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
			$plandet_name 		= $plan_details['PlanName'];
			$_SESSION['current_assigned_plan_name'] =  $plandet_name;
			$plandet_desc_full     = $plan_details['PlanDescription'];
			$plandet_desc 		= substr($plan_details['PlanDescription'], 0, 120);
			if(strlen($plandet_desc) >= 120){
				$plandet_desc = $plandet_desc."...";
			}
			if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
				$plandet_path       = "uploads/planheader/".$plan_details['PlanCoverImagePath'];
			} else {
				$plandet_path       = "uploads/planheader/default.jpg";
			}
			$plandet_catg 		= $plan_details['CategoryName'];
			$plandet_cid		= $plan_details['CategoryID'];

			$display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'><tr><td style='padding-left:5px;border:1px solid #D3D3D3;'><div style='min-height:35px;'>Plan Name : <b>$plandet_name</b></div></td></tr><tr><td style='padding-left:5px;border:1px solid #D3D3D3;background-color:#F1F1F1;'><div style='min-height:35px;'>$plandet_desc</di></td></tr></table>";

		}
	}
$get_medication = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.SpecificTime,t2.RowNo,t3.ShortHand,t2.Instruction,
t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString
from USER_MEDICATION_HEADER as t1,USER_MEDICATION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3
where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode_for_current_plan' 
and t2.When=t3.ID and t1.UserID = t2.UserID and t1.UserID = '$userid_for_current_plan'";
//echo $get_medication;exit;
$get_medication_qry     = mysql_query($get_medication);
$get_medication_count   = mysql_num_rows($get_medication_qry);
if($get_medication_count)
{
	$display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>MEDICATION</div>";
	$display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
	while($medication_rows=mysql_fetch_array($get_medication_qry))
	{
		$medprescno        		= (empty($medication_rows['PrescriptionNo']))           ? '-' : $medication_rows['PrescriptionNo'];
		$medprescname       	= (empty($medication_rows['PrescriptionName']))         ? '-' : $medication_rows['PrescriptionName'];
		$meddocname            	= (empty($medication_rows['DoctorsName']))              ? '-' : $medication_rows['DoctorsName'];
		$medname           		= (empty($medication_rows['MedicineName']))             ? '-' : $medication_rows['MedicineName'];
		$medshort               = (empty($medication_rows['ShortHand']))                ? '-' : $medication_rows['ShortHand'];
		$medrowno               = (empty($medication_rows['RowNo']))                    ? '-' : $medication_rows['RowNo'];
		$medinstr            	= (empty($medication_rows['Instruction']))              ? '-' : $medication_rows['Instruction'];
		$medfreq              	= (empty($medication_rows['Frequency']))                ? '-' : $medication_rows['Frequency'];
		$medfreqstring        	= (empty($medication_rows['FrequencyString']))          ? '-' : $medication_rows['FrequencyString'];
		$medhowlong             = (empty($medication_rows['HowLong']))                  ? '-' : $medication_rows['HowLong'];
		$medhowlongtype         = (empty($medication_rows['HowLongType']))              ? '-' : $medication_rows['HowLongType'];
		$mediscritical          = (empty($medication_rows['IsCritical']))               ? '-' : $medication_rows['IsCritical'];
		$medresponsereq       	= (empty($medication_rows['ResponseRequired']))         ? '-' : $medication_rows['ResponseRequired'];
		$medstartflag           = (empty($medication_rows['StartFlag']))                ? '-' : $medication_rows['StartFlag'];
		$mednumdays				= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '-' : $medication_rows['NoOfDaysAfterPlanStarts'];
		$medspecdate          	= (empty($medication_rows['SpecificDate']))             ? '-' : $medication_rows['SpecificDate'];
		$medspectime           	= (empty($medication_rows['SpecificTime']))             ? '-' : $medication_rows['SpecificTime'];

		$display_print .= "<tr><td style='width:25%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medname</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medshort</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medinstr</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medfreq</td></tr>";
	}
	$display_print .= "</table>";
}
//GET APPOINTMENT DETAILS
$get_appointment= "select distinct t2.AppointmentDate,t2.AppointmentTime,t2.AppointmentShortName,t2.DoctorsName,t2.AppointmentRequirements
from USER_APPOINTMENT_HEADER as t1,USER_APPOINTMENT_DETAILS as t2
where t1.PlanCode=t2.PlanCode and t1.PlanCode='$plancode_for_current_plan' and t1.UserID = t2.UserID and t1.UserID = '$userid_for_current_plan'";
//echo $get_appointment.'<br>';
$get_appointment_qry    = mysql_query($get_appointment);
$get_appointment_count  = mysql_num_rows($get_appointment_qry);
if($get_appointment_count)
{
	$display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>APPOINTMENTS</div>";
	$display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
	while($appointment_rows = mysql_fetch_array($get_appointment_qry))
	{
		$appodate       = (empty($appointment_rows['AppointmentDate']))         ? '' : date('d-M-Y',strtotime($appointment_rows['AppointmentDate']));
		$appotime       = (empty($appointment_rows['AppointmentTime']))         ? '' : date('h:i A',strtotime($appointment_rows['AppointmentTime']));
		$apponame   	= (empty($appointment_rows['AppointmentShortName']))    ? '' : $appointment_rows['AppointmentShortName'];
		$appodoc        = (empty($appointment_rows['DoctorsName']))             ? '' : $appointment_rows['DoctorsName'];
		$apporeq 		= (empty($appointment_rows['AppointmentRequirements'])) ? '' : $appointment_rows['AppointmentRequirements']; 
		$display_print .= "<tr><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$apponame</td><td style='width:17%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$appodate</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$appotime</td><td style='width:40%;padding-left:5px;border:1px solid #D3D3D3;'>$apporeq</td></tr>";
	}
	$display_print .= "</table>";
}
//END OF APPOINTMENT DETAILS
//SELF TEST DETAILS
$get_self_test = "select distinct t1.SelfTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.TestDescription,t2.InstructionID,
t2.Frequency,t2.HowLong,t2.HowLongType,t2.ResponseRequired,t2.StartFlag,t2.NoOfDaysAfterPlanStarts,
t2.SpecificDate,t2.FrequencyString
from USER_SELF_TEST_HEADER as t1,USER_SELF_TEST_DETAILS as t2
where t1.PlanCode=t2.PlanCode and t1.SelfTestID=t2.SelfTestID and t1.PlanCode='$plancode_for_current_plan' and t1.UserID = t2.UserID and t1.UserID = '$userid_for_current_plan'";
//echo $get_self_test;exit;
$get_self_test_qry  = mysql_query($get_self_test);
$get_self_test_count= mysql_num_rows($get_self_test_qry);
if($get_self_test_count)
{
	$display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>SELF TEST</div>";
	$display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
	while($self_test_rows=mysql_fetch_array($get_self_test_qry))
	{
		$selftestid           = (empty($self_test_rows['SelfTestID']))                ? '' : $self_test_rows['SelfTestID'];
		$selfttestno          = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
		$selftestname         = (empty($self_test_rows['TestName']))                  ? '' : $self_test_rows['TestName'];
		$selftestdocname      = (empty($self_test_rows['DoctorsName']))               ? '' : $self_test_rows['DoctorsName']; 
		$selftestdesc         = (empty($self_test_rows['TestDescription']))           ? '' : $self_test_rows['TestDescription'];
		$selftestinst         = (empty($self_test_rows['InstructionID']))             ? '' : $self_test_rows['InstructionID']; 
		$selftestfreq         = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
		$selftestfreqstring   = (empty($self_test_rows['FrequencyString']))           ? '' : $self_test_rows['FrequencyString'];
		$selftesthowlong      = (empty($self_test_rows['HowLong']))                   ? '' : $self_test_rows['HowLong'];
		$selftesthowlongtype  = (empty($self_test_rows['HowLongType']))               ? '' : $self_test_rows['HowLongType']; 
		$selfttestresp        = (empty($self_test_rows['ResponseRequired']))          ? '' : $self_test_rows['ResponseRequired'];
		$selfteststart        = (empty($self_test_rows['StartFlag']))                 ? '' : $self_test_rows['StartFlag']; 
		$selftestnumdays      = (empty($self_test_rows['NoOfDaysAfterPlanStarts']))   ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
		$selftestspecdate     = (empty($self_test_rows['SpecificDate']))              ? '' : $self_test_rows['SpecificDate'];

		$display_print .= "<tr><td style='width:79%;padding-left:5px;border:1px solid #D3D3D3;'>$selftestname</td><td style='width:19%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$selftestfreq</td></tr>";
	}   
	$display_print .= "</table>";
} 
//END OF SELF TEST DETAILS
//GET LAB TEST DETAILS
$get_lab_test = "select distinct t1.LabTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.LabTestRequirements,t2.LabTestDate,t2.LabTestTime from USER_LAB_TEST_HEADER1 as t1,USER_LAB_TEST_DETAILS1 as t2
where t1.PlanCode=t2.PlanCode and t1.LabTestID=t2.LabTestID and t1.PlanCode='$plancode_for_current_plan' and t1.UserID = t2.UserID and t1.UserID = '$userid_for_current_plan' and t2.LabTestDate is null";
$get_lab_test_qry  = mysql_query($get_lab_test);
$get_lab_test_count= mysql_num_rows($get_lab_test_qry);
if($get_lab_test_count)
{
	$display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>LAB TESTS</div>";                 
	$display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
	while($lab_test_rows=mysql_fetch_array($get_lab_test_qry))
	{
		$labtestid          = (empty($lab_test_rows['LabTestID']))                  ? '' : $lab_test_rows['LabTestID'];
		$labtestrowno       = (empty($lab_test_rows['RowNo']))                      ? '' : $lab_test_rows['RowNo']; 
		$labtestname        = (empty($lab_test_rows['TestName']))                   ? '' : $lab_test_rows['TestName'];
		$labtestdocname     = (empty($lab_test_rows['DoctorsName']))                ? '' : $lab_test_rows['DoctorsName']; 
		$labtestreq         = (empty($lab_test_rows['LabTestRequirements']))        ? '' : $lab_test_rows['LabTestRequirements'];
		$labtestdate        = (empty($lab_test_rows['LabTestDate']))                ? '' : $lab_test_rows['LabTestDate']; 
		$labtesttime        = (empty($lab_test_rows['LabTestTime']))                ? '' : $lab_test_rows['LabTestTime'];
		$display_print .= "<tr><td style='width:32%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$labtestname</td><td style='width:32%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$labtestdocname</td><td style='width:32%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$labtestreq</td></tr>";
	}   
	$display_print .= "</table>";
}   
//END OF LAB TEST DETAILS
    //GET INSTRUCTION DETAILS
$get_instruction = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.SpecificTime,t2.RowNo,t3.ShortHand,t2.Instruction,
                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString
                    from USER_INSTRUCTION_HEADER as t1,USER_INSTRUCTION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3
                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode_for_current_plan' 
                    and t2.When=t3.ID and t1.UserID = t2.UserID and t1.UserID = '$userid_for_current_plan'";
//echo $get_instruction;exit;
$get_instruction_qry     = mysql_query($get_instruction);
$get_instruction_count   = mysql_num_rows($get_instruction_qry);
if($get_instruction_count)
{
  $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>INSTRUCTIONS</div>";
                      $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
    while($instruction_rows=mysql_fetch_array($get_instruction_qry))
    {
        $medprescno             = (empty($instruction_rows['PrescriptionNo']))           ? '-' : $instruction_rows['PrescriptionNo'];
        $medprescname           = (empty($instruction_rows['PrescriptionName']))         ? '-' : $instruction_rows['PrescriptionName'];
        $meddocname             = (empty($instruction_rows['DoctorsName']))              ? '-' : $instruction_rows['DoctorsName'];
        $medname                = (empty($instruction_rows['MedicineName']))             ? '-' : $instruction_rows['MedicineName'];
        $medshort               = (empty($instruction_rows['ShortHand']))                ? '-' : $instruction_rows['ShortHand'];
        $medrowno               = (empty($instruction_rows['RowNo']))                    ? '-' : $instruction_rows['RowNo'];
        $medinstr               = (empty($instruction_rows['Instruction']))              ? '-' : $instruction_rows['Instruction'];
        $medfreq                = (empty($instruction_rows['Frequency']))                ? '-' : $instruction_rows['Frequency'];
        $medfreqstring          = (empty($instruction_rows['FrequencyString']))          ? '-' : $instruction_rows['FrequencyString'];
        $medhowlong             = (empty($instruction_rows['HowLong']))                  ? '-' : $instruction_rows['HowLong'];
        $medhowlongtype         = (empty($instruction_rows['HowLongType']))              ? '-' : $instruction_rows['HowLongType'];
        $mediscritical          = (empty($instruction_rows['IsCritical']))               ? '-' : $instruction_rows['IsCritical'];
        $medresponsereq         = (empty($instruction_rows['ResponseRequired']))         ? '-' : $instruction_rows['ResponseRequired'];
        $medstartflag           = (empty($instruction_rows['StartFlag']))                ? '-' : $instruction_rows['StartFlag'];
        $mednumdays             = (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '-' : $instruction_rows['NoOfDaysAfterPlanStarts'];
        $medspecdate            = (empty($instruction_rows['SpecificDate']))             ? '-' : $instruction_rows['SpecificDate'];
        $medspectime            = (empty($instruction_rows['SpecificTime']))             ? '-' : $instruction_rows['SpecificTime'];

        $display_print .= "<tr><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medname</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medshort</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medinstr</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;'>$medfreq</td></tr>";
                            }
    $display_print .= "</table>";
}
//END OF GET INSTRUCTION DETAILS
//GET GOAL DETAILS
$get_goals = "select distinct UserID, PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where UserID = '$userid_for_current_plan' and PlanCode = '$plancode_for_current_plan'";
//echo $get_goals;exit;
$get_goals_qry     = mysql_query($get_goals);
$get_goals_count   = mysql_num_rows($get_goals_qry);
$tabnames = array("1" => "Medication", "2" => "Appointment", "3-2" => "Lab Test", "8" => "Instruction", "3-1" => "Self Test");
if($get_goals_count)
{
  $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>GOALS</div>";
  $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
    while($goal_rows=mysql_fetch_array($get_goals_qry))
    {
      $goal2tabstext = "";
        $tabnames = array("1" => "Medication", "2" => "Appointment", "3-2" => "Lab Test", "8" => "Instruction", "3-1" => "Self Test");
        $goaldesc             = (empty($goal_rows['GoalDescription']))           ? '-' : $goal_rows['GoalDescription'];
        $goasdisp             = (empty($goal_rows['DisplayedWith']))           ? '-' : $goal_rows['DisplayedWith'];
        $tabexplode     = explode(",", $goasdisp);
          foreach ($tabexplode as $tab) {
            if($tab != ""){
              $goal2tabstext .= $tabnames[$tab].", ";
            }
          }
          $display_print .= "<tr><td style='width:99%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$goaldesc</td></tr>";

    }
    $display_print .= "</table>";
}
                  if($patient_review_flag > 0){
            $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>REVIEW NOTES</div>";
            $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
          $display_print .= "<tr><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>#</td><td style='width:25%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Date</td><td style='width:59%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Notes</td></tr>";

                        for($r=0;$r<$patient_review_flag;$r++){
                          $CreatedDate33 = date('d-M-Y',strtotime($CreatedDate3[$r]));
                          $pvrc = $r+1;
                          $display_print .= "<tr><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$pvrc</td><td style='width:25%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$CreatedDate33</td><td style='width:59%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$Review[$r]</td></tr>";                        }
                          $display_print .= "</table>";
                  }
        if($patient_data_flag > 0){
          $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>VISIT DETAILS</div>";
          $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
          
          $display_print .= "<tr><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>#</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Date</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Height</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Weight</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Blood Pressure</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Temperature</td></tr>";

        for($i=0;$i<$patient_data_flag;$i++){
          $pvdc = $i+1;
          $CreatedDate11 = date('d-M-Y',strtotime($CreatedDate[$i]));
          $display_print .= "<tr><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$pvdc</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$CreatedDate11</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$Height[$i]</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$Weight[$i]</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$BloodPressure[$i]</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$Temperature[$i]</td></tr>";
        }
        $display_print .= "</table>";
        }
        if($UserHistory != ""){
          $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>PATIENT/FAMILY HISTORY</div>";
          $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
          $display_print .= "<tr><td style='width:99%;padding-left:5px;border:1px solid #D3D3D3;'>$UserHistory</td></tr>";
          $display_print .= "</table>";
        }
          if($patient_notes_flag > 0){
            $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>PATIENT NOTES</div>";
            $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
          $display_print .= "<tr><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>#</td><td style='width:25%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Date</td><td style='width:59%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>Notes</td></tr>";

                        for($i=0;$i<$patient_notes_flag;$i++){
                          $CreatedDate22 = date('d-M-Y',strtotime($CreatedDate2[$i]));
                          $pvnc = $i+1;
                          $display_print .= "<tr><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$pvnc</td><td style='width:25%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$CreatedDate22</td><td style='width:59%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$Notes[$i]</td></tr>";                        }
                          $display_print .= "</table>";
                  }

if(isset($_SESSION['plancode_for_current_plan'])){
	$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
	//Get Plan Details
		if($plancode_for_current_plan != ""){
		$get_plan_details = "select PlanCode, PlanName, PlanDescription from USER_PLAN_HEADER where PlanCode = '$plancode_for_current_plan' and UserID='$userid_for_current_plan'";
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
if(((isset($_REQUEST['goalDescription1']))&&(!empty($_REQUEST['goalDescription1'])))||((isset($_REQUEST['goalDescription2']))&&(!empty($_REQUEST['goalDescription2'])))){
	//echo "<pre>";print_r($_POST);exit;
	$goalDescription1 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goalDescription1'])));
	$goal1tabs 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goal1tabs'])));
	$goalDescription2 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goalDescription2'])));
	$goal2tabs 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goal2tabs'])));
	$delete_existing = mysql_query("delete from USER_GOAL_DETAILS where PlanCode='$plancode_for_current_plan' and UserID = '$userid_for_current_plan'");
	if($goalDescription1 != ""){
		$insert_goal_details = mysql_query("insert into USER_GOAL_DETAILS (`UserID`, `PlanCode`, `GoalNo`, `GoalDescription`, `DisplayedWith`, `CreatedDate`, `CreatedBy`) values ('$userid_for_current_plan', '$plancode_for_current_plan', '1', '$goalDescription1', '$goal1tabs', now(), '$logged_userid')");
		//echo "insert into GOAL_DETAILS (`PlanCode`, `GoalNo`, `GoalDescription`, `DisplayedWith`, `CreatedDate`, `CreatedBy`) values ('$plancode_for_current_plan', '1', '$goalDescription1', '$goal1tabs', now(), '$logged_userid')";exit;
	}
	if($goalDescription2 != ""){
		$insert_goal_details = mysql_query("insert into USER_GOAL_DETAILS (`UserID`, `PlanCode`, `GoalNo`, `GoalDescription`, `DisplayedWith`, `CreatedDate`, `CreatedBy`) values ('$userid_for_current_plan', '$plancode_for_current_plan', '2', '$goalDescription2', '$goal2tabs', now(), '$logged_userid')");
	}
			$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       {
		       	$update_header = mysql_query("update USER_PLAN_HEADER set PlanUpdatedDate = current_timestamp where PlanCode='$plancode_for_current_plan' and UserID = '$userid_for_current_plan'");
		       	header("Location:cust_goals.php");
		       } else {
		       	header("Location:cust_goals.php");
		       }

}
//Get Goals
$get_goal = "select UserID, PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where PlanCode='$plancode_for_current_plan' and UserID = '$userid_for_current_plan'";
  //echo $get_presc;exit;
  $get_goal_run = mysql_query($get_goal);
  $get_goal_count = mysql_num_rows($get_goal_run);
  $goalDescription1 = "";
  $goal1tabs 		= "";
  $goalDescription2 = "";
  $goal2tabs 		= "";
  $goal1tabstext 	= "";
  $goal2tabstext 	= "";
  $tabnames = array("1" => "Medication", "2" => "Appointment", "3-2" => "Lab Test", "8" => "Instruction", "3-1" => "Self Test");
  if($get_goal_count >0){
	  while ($goals = mysql_fetch_array($get_goal_run)) {
	  	$GoalNo = $goals['GoalNo'];
	  	if($get_goal_count > 1){
		  	if($GoalNo == "1"){
			  $goalDescription1 = $goals['GoalDescription'];
			  $goal1tabstext 	= "Displayed With : ";
			  $goal1tabs 		= $goals['DisplayedWith'];	
			  $tabexplode 		= explode(",", $goal1tabs);
			  foreach ($tabexplode as $tab) {
			  	if($tab != ""){
			  		$goal1tabstext .= $tabnames[$tab].", ";
			  	}
			  }
		  	}
		  	if($GoalNo == "2"){
		  	  $goal2tabstext 	= "Displayed With : ";
			  $goalDescription2 = $goals['GoalDescription'];
			  $goal2tabs 		= $goals['DisplayedWith'];		
			  $tabexplode 		= explode(",", $goal2tabs);
			  foreach ($tabexplode as $tab) {
			  	if($tab != ""){
			  		$goal2tabstext .= $tabnames[$tab].", ";
			  	}
			  }
		  	}	  		
	  	} else {
	  		  $goal1tabstext 	= "Displayed With : ";
		  	  $goalDescription1 = $goals['GoalDescription'];
			  $goal1tabs 		= $goals['DisplayedWith'];	
			  $tabexplode 		= explode(",", $goal1tabs);
			  foreach ($tabexplode as $tab) {
			  	if($tab != ""){
			  		$goal1tabstext .= $tabnames[$tab].", ";
			  	}
			  }	
	  	}
	  }   	
  }

?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Plan Piper | Goals</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/jasny-bootstrap.min.css">
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
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left"  id="plantitle"><button type="button" class="btns" id="add_patient_info" style="float:left;">PATIENT INFO</button></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="center"  id="plantitle"><span id="thisplantitle" title="Click to edit the plan details"><?php echo $planname_for_current_plan_text;?></span><span title="Click to edit the plan details" id="editthisplantitle">&nbsp;&nbsp;<img src="images/editad.png" style="height:20px;cursor:pointer;"></span></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="right"  id="plantitle"><button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH CUSTOMIZING</button></div>
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
    		<h6 style="font-family:Freestyle;font-size:33px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;">Goals List</h6>
    		<div class="sidebarheadings">Master Goals :</div>
    				    		<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_goals 		= "select t1.GoalDescription, t1.PlanCode, t1.GoalNo, t1.CreatedDate from GOAL_DETAILS as t1,PLAN_HEADER as t2 where t1.PlanCode=t2.PlanCode and t2.MerchantID='$logged_merchantid' and t2.PlanStatus='A'";
			    		//echo $get_plan_goals;exit;
		    		$get_plan_goals_run 	= mysql_query($get_plan_goals);
		    		$get_plan_goals_count 	= mysql_num_rows($get_plan_goals_run);
		    		if($get_plan_goals_count > 0){
			    		while ($get_goals_row = mysql_fetch_array($get_plan_goals_run)) {
			    			$goaldescription   	= $get_goals_row['GoalDescription'];
			    			$goaldescriptionfull   	= $get_goals_row['GoalDescription'];
			    			$length 			= strlen($goaldescription);
			    			if($length > 12){
			    				$goaldescription 	= substr($goaldescription,0,12);
			    				$goaldescription 	= $goaldescription."...";
			    			}
			    			$goal_no   = $get_goals_row['GoalNo'];
			    			$goal_code = $get_goals_row['PlanCode'];
			    			$goal_date = date('d-M-Y',strtotime($get_goals_row['CreatedDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $goal_code.$goal_no; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $goal_code.$goal_no; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $goal_code.$goal_no; ?>" onclick='changeimage("<?php echo $goal_code.$goal_no; ?>");'></a>
					                   <span title='<?php echo $goaldescriptionfull;?>'><?php echo $goaldescription;?></span>
					                  <img src="images/addtoright.png" class="addmasterplangoals" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $goal_code."~~".$goal_no; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $goal_code.$goal_no; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $goal_code.$goal_no; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php echo $goaldescriptionfull;?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		}else {
		    			echo "<div style='color:#fff;'>No Goals to show</div>";
		    		}
			    		?>
		    		</div>
		    		<div class="sidebarheadings" style="margin-top: -13px;">Assigned Goals :</div>
		    				    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_goals 		= "select t1.UserID, t1.GoalDescription, t1.PlanCode, t1.GoalNo, t1.CreatedDate from USER_GOAL_DETAILS as t1,USER_PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.MerchantID='$logged_merchantid'";
		    		$get_plan_goals_run 	= mysql_query($get_plan_goals);
		    		$get_plan_goals_count 	= mysql_num_rows($get_plan_goals_run);
		    		if($get_plan_goals_count > 0){
			    		while ($get_goals_row = mysql_fetch_array($get_plan_goals_run)) {
			    			$goaldescription 	= $get_goals_row['GoalDescription'];
			    			$goaldescriptionfull = $get_goals_row['GoalDescription'];
			    			$fortitle			= $get_goals_row['GoalDescription'];
			    			$length 			= strlen($goaldescription);
			    			if($length > 12){
			    				$goaldescription 	= substr($goaldescription,0,12);
			    				$goaldescription 	= $goaldescription."...";
			    			}
			    			$goal_no   = $get_goals_row['GoalNo'];
			    			$goal_user   = $get_goals_row['UserID'];
			    			$goal_code = $get_goals_row['PlanCode'];
			    			$goal_date = date('d-M-Y',strtotime($get_goals_row['CreatedDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $goal_user.$goal_code.$goal_no; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $goal_user.$goal_code.$goal_no; ?>" aria-expanded="true" aria-controls="collapse<?php echo $goal_user.$goal_code.$goal_no; ?>"><img src="images/rightarrow.png" style="height:12px;width:auto;cursor:pointer;" align="left"></a>
					                   <span title='<?php echo $goaldescriptionfull;?>'><?php echo $goaldescription;?></span>
					                  <img src="images/addtoright.png" class="addassignedplangoals" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $goal_user."~~".$goal_code."~~".$goal_no; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $goal_user.$goal_code.$goal_no; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $goal_user.$goal_code.$goal_no; ?>">
					                <div class="panel-body" style="text-align:center;">
					                	<?php
					                		echo $goaldescriptionfull;
					                	?>

					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		}else {
		    			echo "<div style='color:#fff;'>No Goals to show</div>";
		    		}
			    		?>
		    		</div>
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent" style="padding-bottom:100px;">
	    	<div>
	    	<!-- <div class="topnotetext well">
	    		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	    	</div> -->
	    	<form name="frm_plan_goals" id="frm_plan_goals" method="POST">
	    		<table id="pdata" class="table" style="border:transparent;">
	    			<tr>
	    				<td>#</td>
	    				<td>Goal</td>
	    				<td align="center">Displayed With</td>
	    			</tr>
	    			<tr>
	    				<td>1</td>
	    				<td><input type="text" name="goalDescription1" id="goalDescription1" placeholder="Enter the Goal.." class="forminputs2" maxlength="50" title="Enter a goal for this plan" value="<?php echo $goalDescription1;?>"></td>
	    				<td align="center"><div class="btns goalbutton tabpickerbutton" id="1">Click to Select</div></td>
	    			</tr>
	    			<tr>
	    				<td colspan="3" align="center"><input type="hidden" name="goal1tabs" id="goal1tabs" value="<?php echo $goal1tabs;?>"><input type="text" name="goal1tabstext" id="goal1tabstext" value="<?php echo $goal1tabstext;?>" readonly></td>
	    			</tr>
	    			<tr>
	    				<td>2</td>
	    				<td><input type="text" name="goalDescription2" id="goalDescription2" placeholder="Enter the Goal.." class="forminputs2" maxlength="50" title="Enter a goal for this plan" value="<?php echo $goalDescription2;?>"></td>
	    				<td align="center"><div class="btns goalbutton tabpickerbutton" id="2">Click to Select</div></td>
	    			</tr>
	    			<tr>
	    				<td colspan="3" align="center"><input type="hidden" name="goal2tabs" id="goal2tabs" value="<?php echo $goal2tabs;?>"><input type="text" name="goal2tabstext" id="goal2tabstext" value="<?php echo $goal2tabstext;?>" readonly></td>
	    			</tr>
	    			<tr>
	    				<td colspan="3" align="center"><div type="button" id="saveAndEdit" class="btns goalbutton formbuttons" style="margin-top:20px;height:40px;line-height:40px;padding:0px;">SAVE</div></td>
	    			</tr>
	    		</table>	    		
	    	</form>

	    	</div>
    	</div>
    </div>
	</div>	
    <div class="printable" style="display:none;">
		<?php echo $display_print;?>
    </div>
     <!--SHOW MONTH DAY PICKER MODAL WINDOW-->
            <div class="modal" id="goaltabpicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Select</h5>
                  </div>
                  <div class="modal-body taboptions" align="center" style="padding-top:0px;background-color:#fff;padding-bottom:50px;">

               	  </div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="tabselectedid" id="tabselectedid" value="0">
               	  <button class="smallbutton" id="tabsselected">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF SHOW MONTH DAY PICKER MODAL WINDOW-->
         <!--SHOW PLAN DETAILS MODAL WINDOW-->
            <div class="modal" id="plandetailsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Enter Plan Details</h5>
                  </div>
                  	<div align="left" style="padding-left:5px;font-family:RalewayRegular;color:#000;">
						<input type="text" placeholder="Enter the Plan Title here" name="plan_name" id="plan_name" class="firstlettercaps" title="Plan Title" onkeypress='keychk(event)' maxlength="50" style="width:100%;" value="<?php echo $planname_for_current_plan;?>">
                        <textarea placeholder="Type the plan description here" id="plan_desc" name="plan_desc" title="Plan Description" rows="4" style="resize:none;border-bottom:1px solid #004f35;"  maxlength="499"><?php echo $plandesc_for_current_plan;?></textarea>
                        <!--ADDED-->
                        <div id="textarea_feedback" style="color:#004F35;font-family:Raleway;padding-bottom:10px;text-align:right"></div>
                        <!---->
                        <!--<span>Upload a Cover Image (Optional):  <input id="plan_cover_image" name="plan_cover_image" type="file" accept='image/*' style="display:inline;"></span>-->
					</div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="plancode_for_current_plan" id="plancode_for_current_plan" value="<?php echo $plancode_for_current_plan;?>">
               	  <input type="hidden" name="userid_for_current_plan" id="userid_for_current_plan" value="<?php echo $userid_for_current_plan;?>">
               	  <button class="smallbutton" id="plandetailsentered">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF PLAN DETAILS MODAL WINDOW-->
                 <!--SHOW ADD PATIENT DETAILS MODAL WINDOW-->
                        <div class="modal bs-example-modal-lg" id="patientdetailsmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
              <div class="modal-dialog modal-lg" style="width:900px;">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Review Notes/Visit Details</h5>
                  </div>
                  	<div align="left" style="padding-left:5px;font-family:RalewayRegular;color:#000;">
                  		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour"><div class="panel-heading patientdetailpanel" role="tab" id="headingFour">
                  <h4 class="panel-title">Review Notes</h4>
                </div></a>
                <div id="collapseFour" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFour">
                  <div class="panel-body">
                  <?php 
                  if($patient_review_flag > 0){
                    ?>
                    <table class="table table-bordered table-striped">
                      <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:100px;">Date</th>
                        <th>Notes</th>
                      </tr>
                      <?php 
                        for($r=0;$r<$patient_review_flag;$r++){
                          $CreatedDate33 = date('d-M-Y',strtotime($CreatedDate3[$r]));
                          $pvrc = $r+1;
                          echo "<tr><td align='center'>$pvrc</td><td align='center'>$CreatedDate33</td><td>$Review[$r]</td></tr>";
                        }
                      ?>
                    </table>
                    <?php
                  }
                  ?>
                    <div>
                    As on date : <?php echo date("d-M-Y");?>
                    <textarea class="form-control" placeholder="Enter Review here.." rows="5" maxlength="1000" id="review_notes" name="review_notes"></textarea>
                  </div>
                  </div>
                </div>
              </div>
						  <div class="panel panel-default">
						    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><div class="panel-heading patientdetailpanel" role="tab" id="headingOne">
						      <h4 class="panel-title"> Visit Data</h4>
						    </div></a>
						    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
						      <div class="panel-body">
						      <?php 
						      if($patient_data_flag > 0){
						      	?>
						      	<table class="table table-bordered table-striped">
						      		<tr>
						      			<th>#</th>
						      			<th>Date</th>
						      			<th>Height</th>
						      			<th>Weight</th>
						      			<th>Blood Pressure</th>
						      			<th>Temperature</th>
						      		</tr>
						      		<?php 

						      			for($i=0;$i<$patient_data_flag;$i++){
						      				$pvdc = $i+1;
						      				$CreatedDate11 = date('d-M-Y',strtotime($CreatedDate[$i]));
						      				echo "<tr><td>$pvdc</td><td>$CreatedDate11</td><td>$Height[$i]</td><td>$Weight[$i]</td><td>$BloodPressure[$i]</td><td>$Temperature[$i]</td></tr>";
						      			}
						      		?>
						      	</table>
						      	<?php
						      }
						      ?>
						        <div class="form-group">
								    <label for="inputEmail3" class="col-sm-2 control-label">Height</label>
								    <div class="col-sm-10" style="margin-bottom: 5px;">
								    <div class="input-group">
								      <input type="text" class="form-control" id="patientheight" placeholder="Height" maxlength="10" name="patientheight">
								      <div class="input-group-addon" style="width:100px;">Cm</div>
								     </div>
								    </div>
								  </div>
								  <div class="form-group" style="margin-top:10px;">
								    <label for="inputEmail3" class="col-sm-2 control-label">Weight</label>
								    <div class="col-sm-10" style="margin-bottom: 5px;">
								    <div class="input-group">
								      <input type="text" class="form-control" id="patientweight" placeholder="Weight" name="patientweight" maxlength="10">
								      <div class="input-group-addon" style="width:100px;">Kg</div>
								     </div>
								    </div>
								  </div>
								  <div class="form-group" style="margin-top:10px;">
								    <label for="inputEmail3" class="col-sm-2 control-label">Blood Pressure</label>
								    <div class="col-sm-10" style="margin-bottom: 5px;">
								    <div class="input-group">
								      <input type="text" class="form-control" id="patientpressure"  name="patientpressure" placeholder="Blood Pressure" maxlength="20">
								      <div class="input-group-addon"  style="width:100px;">mmHg</div>
								     </div>
								    </div>
								  </div>
								  <div class="form-group" style="margin-top:10px;">
								    <label for="inputEmail3" class="col-sm-2 control-label">Temperature</label>
								    <div class="col-sm-10" style="margin-bottom: 5px;">
								    <div class="input-group">
								      <input type="text" class="form-control" id="patienttemp" name="patienttemp" placeholder="Temperature" maxlength="10">
								      <div class="input-group-addon" style="width:100px;">F</div>
								     </div>
								    </div>
								  </div>
						      </div>
						    </div>
						  </div>
						  <div class="panel panel-default">
						    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><div class="panel-heading patientdetailpanel" role="tab" id="headingTwo">
						      <h4 class="panel-title">Patient/Family History</h4>
						    </div></a>
						    <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
						      <div class="panel-body">
						      <div>
						        <textarea class="form-control" placeholder="Enter here.." rows="5" maxlength="1000" name="userHistory" id="userHistory"><?php echo $UserHistory;?></textarea>
						      </div>
						      </div>
						    </div>
						  </div>
						  <div class="panel panel-default">
						    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree"><div class="panel-heading patientdetailpanel" role="tab" id="headingThree">
						      <h4 class="panel-title">Visit Notes</h4>
						    </div></a>
						    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
						      <div class="panel-body">
						      <?php 
						      if($patient_notes_flag > 0){
						      	?>
						      	<table class="table table-bordered table-striped">
						      		<tr>
						      			<th style="width:50px;">#</th>
						      			<th style="width:100px;">Date</th>
						      			<th>Notes</th>
						      		</tr>
						      		<?php 
						      			for($i=0;$i<$patient_notes_flag;$i++){
						      				$CreatedDate22 = date('d-M-Y',strtotime($CreatedDate2[$i]));
						      				$pvnc = $i+1;
						      				echo "<tr><td align='center'>$pvnc</td><td align='center'>$CreatedDate22</td><td>$Notes[$i]</td></tr>";
						      			}
						      		?>
						      	</table>
						      	<?php
						      }
						      ?>
						        <div>
						        As on date : <?php echo date("d-M-Y");?>
						        <textarea class="form-control" placeholder="Enter Notes here.." rows="5" maxlength="1000" id="visit_notes" name="visit_notes"></textarea>
						      </div>
						      </div>
						    </div>
						  </div>
						</div>
                  	</div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="plancode_for_current_plan" id="plancode_for_current_plan" value="<?php echo $plancode_for_current_plan;?>">
               	  <input type="hidden" name="userid_for_current_plan" id="userid_for_current_plan" value="<?php echo $userid_for_current_plan;?>">
               	  <input type="hidden" name="logged_merchantid" id="logged_merchantid" value="<?php echo $logged_merchantid;?>">
               	  <input type="hidden" name="current_date" id="current_date" value="<?php echo date('d-M-Y h:i:s');?>">
               	  <button class="smallbutton" id="patientdetailsentered">Done</button>
               	  <button class="smallbutton" id="print_button">Print</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF ADD PATIENT DETAILS MODAL WINDOW-->
</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootstrap-timepicker.js"></script>
		<script type="text/javascript" src="js/jquery.print.js"></script>
		<script type="text/javascript" src="js/placeholders.min.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript" src="js/jasny-bootstrap.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#11').addClass('active');
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
        	//$('.maincontent').height(available_height);
        	var sidelistheight = $('#listBar').height();
        	var availableheight = sidelistheight - 280;
        	availableheight = availableheight/2;
        	//alert(availableheight);
        	$('.masterplanactivities').height(availableheight);
        	$('.assignedplanactivities').height(availableheight);
			var medicationcount = 0;
			$('#plapiper_pagename').html("Goals");

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
			$('#add_patient_info').click(function(){
				$('#patientdetailsmodal').modal('show');
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

				window.location.href = "customize_plan.php";
			});
			var alltabs = ["1","2","3-2","8","3-1"];
			var tabtexts = [];
			tabtexts["1"] = "Medication";
			tabtexts["2"] = "Appointment";
			tabtexts["3-2"] = "Lab Test";
			tabtexts["8"] = "Instruction";
			tabtexts["3-1"] = "Self Test";
			var usedtabs = [];
			var goal1tabs = $('#goal1tabs').val().split(',');
			var goal2tabs = $('#goal2tabs').val().split(',');
			var diff 		= $(alltabs).not(goal1tabs).get(); //In alltabs , not in goal1tabs
			var availabletabs = $(diff).not(goal2tabs).get();//In diff , not in goal2tabs
			//var availabletabs = ["1","2","3-2","8","3-1"];
			$('.tabpickerbutton').click(function(){
				var tabid  = $(this).attr('id');
				var selectedtabs = [];
				if(tabid == "1"){
					var i;
					for (i = 0; i < goal1tabs.length; ++i) {
					    selectedtabs.push(goal1tabs[i]);
					}
				}else {
					var i;
					for (i = 0; i < goal2tabs.length; ++i) {
					    selectedtabs.push(goal2tabs[i]);
					}
				}
				var taboptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
				if(($.inArray("1", availabletabs) !== -1)||($.inArray("1", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='1'> Medication</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='1'> Medication</label>";
				}
				if(($.inArray("2", availabletabs) !== -1)||($.inArray("2", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='2'> Appointment</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='2'> Appointment</label>";
				}
				if(($.inArray("3-2", availabletabs) !== -1)||($.inArray("3-2", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='3-2'> Lab Test</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='3-2'> Lab Test</label>";
				}
				if(($.inArray("8", availabletabs) !== -1)||($.inArray("8", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='8'> Instruction</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='8'> Instruction</label>";
				}
				if(($.inArray("3-1", availabletabs) !== -1)||($.inArray("3-1", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='3-1'> Self Test</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='3-1'> Self Test</label>";
				}
				taboptions = taboptions + "</div>";
				$('#tabselectedid').val(tabid);
				$('.taboptions').html(taboptions);
				if(tabid == "1"){
					$('.tabcheck').each(function() {
						if(jQuery.inArray($(this).val(),goal1tabs) != -1){
	                    	$(this).prop('checked',true);
	                    	$(this).parents('label').addClass('active');
	                    }
                	});
				} else {
					$('.tabcheck').each(function() {
						if(jQuery.inArray($(this).val(),goal2tabs) != -1){
	                    	$(this).prop('checked',true);
	                    	$(this).parents('label').addClass('active');
	                    }
                	});
				}
				
				$('#goaltabpicker').modal('show');
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
				var userid = $('#userid_for_current_plan').val();
				var plan_desc = $('#plan_desc').val();

					var dataString = "type=insert_user_plan_header&title="+plan_name+"&desc="+plan_desc+"&code="+plan_code+"&mer="+<?php echo $logged_merchantid;?>+"&user="+<?php echo $logged_userid;?>+"&userid="+userid;
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
			$('#print_button').click(function(){
		        $( ".printable" ).print();
	           return( false );
	        });
      $('#patientdetailsentered').click(function(){
        var patientheight         = $('#patientheight').val();
        var patientweight         = $('#patientweight').val();
        var patientpressure       = $('#patientpressure').val();
        var patienttemp         = $('#patienttemp').val();
        var userHistory           = $('#userHistory').val();
        var visit_notes           = $('#visit_notes').val();
        var review_notes           = $('#review_notes').val();
        var userid_for_current_plan   = $('#userid_for_current_plan').val();
        var logged_merchantid       = $('#logged_merchantid').val();
        var plancode_for_current_plan   = $('#plancode_for_current_plan').val();
        var dataString = "type=insert_patient_details&patientheight="+patientheight+"&patientweight="+patientweight+"&patientpressure="+patientpressure+"&patienttemp="+patienttemp+"&userHistory="+userHistory+"&visit_notes="+visit_notes+"&review_notes="+review_notes+"&userid_for_current_plan="+userid_for_current_plan+"&logged_merchantid="+logged_merchantid+"&plancode_for_current_plan="+plancode_for_current_plan;
          //alert(dataString);
              $.ajax({
            type    : 'POST',
            url     : 'ajax_validation.php',
            crossDomain : true,
            data    : dataString,
            dataType  : 'json',
            async   : false,
            success : function (response)
              {
                if(response.success == true){
                  $('#patientdetailsmodal').modal('hide');
                } else {
                  $('#patientdetailsmodal').modal('hide');
                }
              },
            error: function(error)
            {

            }
          });
        
      });
			$('.panel-heading a').on('click',function(e){
				var id = $(this).attr('href');
				if($(id).hasClass('in')){	
				} else {
					$(id).addClass('in');
				    $('.panel-collapse').removeClass('in');		
				}
			});
			//ON CLICK OF DONE BUTTON AFTER SELECTING THE MONTH DAYS
			$(document).on('click', '#tabsselected', function () {
				//alert(1);
				var goal1tabstext = "";
				var goal2tabstext = "";
				var getcurrentid = $('#tabselectedid').val();
				if(getcurrentid == "1"){
					var i;
					for (i = 0; i < goal1tabs.length; ++i) {
					    availabletabs.push(goal1tabs[i]);
					}
					goal1tabs = [];
				}else {
					var i;
					for (i = 0; i < goal2tabs.length; ++i) {
					    availabletabs.push(goal2tabs[i]);
					}
					goal2tabs = [];
				}
				//alert(getcurrentid);
				var chkId2 = "";
				goal1tabstext = "Displayed With : ";
				goal2tabstext = "Displayed With : ";
				$('.tabcheck:checked').each(function() {
					if(getcurrentid == "1"){
						goal1tabs.push($(this).val());
						var index = availabletabs.indexOf($(this).val());
						availabletabs.splice(index, 1);
						goal1tabstext = goal1tabstext + tabtexts[$(this).val()] + ", ";
					}
					if(getcurrentid == "2"){
						goal2tabs.push($(this).val());
						var index = availabletabs.indexOf($(this).val());
						availabletabs.splice(index, 1);
						goal2tabstext = goal2tabstext + tabtexts[$(this).val()] + ", ";
					}

				  chkId2 += $(this).val() + ",";
				});
				if(chkId2 == ""){
					bootbox.alert("Please select atleast one option to continue..");
					return false;
				}
				//alert(chkId2);
				if(getcurrentid == "1"){
					$('#goal1tabs').val(chkId2);
					$('#goal1tabstext').val(goal1tabstext);
				}
				if(getcurrentid == "2"){
					$('#goal2tabs').val(chkId2);
					$('#goal2tabstext').val(goal2tabstext);
				}
				$('#goaltabpicker').modal('hide');
			});
				//ON CLICK OF SAVE BUTTON
				$(document).on('click', '#saveAndEdit', function () {
					var goalDescription1 = $('#goalDescription1').val();
					var goalDescription2 = $('#goalDescription2').val();
			    	if((goalDescription1.replace(/\s+/g, '') == "") && (goalDescription2.replace(/\s+/g, '') == "")){
			    		bootbox.alert("Please enter atleast one goal to continue.");
						$('.bootbox').on('hidden.bs.modal', function() {
								    $('#goalDescription1').focus();
								});
						return false;
					}
					
				if(goalDescription1.replace(/\s+/g, '') != ""){
					var goal1tabs = $('#goal1tabs').val();
					if(goal1tabs.replace(/\s+/g, '') == ""){
						bootbox.alert("Please select where the goal should be displayed.");
						$('.bootbox').on('hidden.bs.modal', function() {
								    $("#1.tabpickerbutton").click();
								});
						return false;
					}
				}
				if(goalDescription2.replace(/\s+/g, '') != ""){
					var goal2tabs = $('#goal2tabs').val();
					if(goal2tabs.replace(/\s+/g, '') == ""){
						bootbox.alert("Please select where the goal should be displayed.");
						$('.bootbox').on('hidden.bs.modal', function() {
								    $("#2.tabpickerbutton").click();
								});
						return false;
					}
				}

				$('#frm_plan_goals').submit();
			});
		$('.addassignedplangoals').click(function(){
			var goalid 	= $(this).attr('id'); 
			var assigned 	= goalid.split("~~");
			var userid 		= assigned[0];
			var plancode 	= assigned[1];
			var goalno 		= assigned[2];
			var merchantid  = '<?php echo $logged_merchantid;?>';
			var goalDescription1 = $('#goalDescription1').val();
			var goalDescription2 = $('#goalDescription2').val();
	    	if((goalDescription1.replace(/\s+/g, '') == "") || (goalDescription2.replace(/\s+/g, '') == "")){
				var dataString = "plancode="+plancode+"&type=get_assigned_goals&goalno="+goalno+"&userid="+userid+"&merchantid="+merchantid;
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
	                    	if(goalDescription1.replace(/\s+/g, '') == ""){
	                    		$('#goalDescription1').val(item.GoalDescription);
	                    		// $('#goal1tabs').val(item.DisplayedWith);
	                    	} else {
	                    		$('#goalDescription2').val(item.GoalDescription);
	                    		// $('#goal2tabs').val(item.DisplayedWith);
	                    	}
	                    });
	                  },
	                  error: function(){

	                  }
	                });
	    	 }else {
				bootbox.alert("A Plan can have only two goals.");
				return false;
			}


		});
		$('.addmasterplangoals').click(function(){
			var goalid 	= $(this).attr('id'); 
			var master 		= goalid.split("~~");
			var plancode 	= master[0];
			var goalno  	= master[1];
			var goalDescription1 = $('#goalDescription1').val();
			var goalDescription2 = $('#goalDescription2').val();
	    	if((goalDescription1.replace(/\s+/g, '') == "") || (goalDescription2.replace(/\s+/g, '') == "")){
				var merchantid  = '<?php echo $logged_merchantid;?>';
				var dataString = "plancode="+plancode+"&type=get_master_goals&goalno="+goalno+"&merchantid="+merchantid;
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
	                    	if(goalDescription1.replace(/\s+/g, '') == ""){
	                    		$('#goalDescription1').val(item.GoalDescription);
	                    		// $('#goal1tabs').val(item.DisplayedWith);
	                    	} else {
	                    		$('#goalDescription2').val(item.GoalDescription);
	                    		// $('#goal2tabs').val(item.DisplayedWith);
	                    	}
	                    });
	                  },
	                  error: function(){

	                  }
	                });
			} else {
				bootbox.alert("A Plan can have only two goals.");
				return false;
			}


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
		</script>
    </body>
    <?php
    include('include/unset_session.php');
	?>
</html>
