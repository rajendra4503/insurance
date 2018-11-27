<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');

$ua=getBrowser();
$browser_name = strtolower(str_replace(" ","",$ua['name']));
$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
$userid_for_current_plan 	= $_SESSION['userid_for_current_plan'];
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
// $det_medicineid 	= "";
// $get_medicines 		= mysql_query("select ID, MedicineName from MERCHANT_MEDICINE_LIST where MerchantID in ('0', '$logged_merchantid') order by MedicineName");
// $medicine_count 	= mysql_num_rows($get_medicines);
// $medicine_options 	= "";
// if($medicine_count > 0){
// 	while ($medicines = mysql_fetch_array($get_medicines)) {
// 		$medicine_id 		= $medicines['ID'];
// 		$medicine_name 		= $medicines['MedicineName'];
// 		if($det_medicineid == $medicine_id){
// 			$medicine_options 	.= "<option value='$medicine_id' selected>$medicine_name</option>";
// 		} else {
// 			$medicine_options 	.= "<option value='$medicine_id'>$medicine_name</option>";			
// 		}
// 	}
// }

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
$plancode_for_current_plan="";

if((isset($_REQUEST['prescriptionName']))&&(!empty($_REQUEST['prescriptionName']))){

   $plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
   
	$prescription_name 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['prescriptionName'])));
	$doctor_name 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['doctorName'])));
	$medicationcount    		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['medicationcount']))); //Total number of medicines present on screen
	$usedpresciptioncount 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['usedpresciptioncount']))); //row ids of medicines present
	$medicineids 				= explode(",", $usedpresciptioncount);
	$delete_presc_ 				= mysql_query("delete from USER_MEDICATION_HEADER where PlanCode = '$plancode_for_current_plan' and UserID='$userid_for_current_plan'");
	$delete_ordinary_user_med 	= mysql_query("delete from ACTIVITES_ORDINARY_USER where PlanCode = '$plancode_for_current_plan' and UserID='$userid_for_current_plan' and SectionID='1'");
	$delete_all_prescriptions 	= mysql_query("delete from USER_MEDICATION_DETAILS where PlanCode = '$plancode_for_current_plan' and UserID='$userid_for_current_plan'");
	$get_last_prescription_num 	= mysql_query("select max(PrescriptionNo) from USER_MEDICATION_HEADER where PlanCode = '$plancode_for_current_plan' and UserID='$userid_for_current_plan'");
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
	$insert_header_details 	= " insert into USER_MEDICATION_HEADER (UserID,Plancode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_presc_num', '$prescription_name', '$doctor_name', now(), '$logged_userid')";
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
				$insert_medicine_details = "insert into USER_MEDICATION_DETAILS (`UserID`,`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`MedicineCount`,`MedicineTypeID`,`When`,`SpecificTime`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$medcount','$medcount_type','$when','$stime','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
				//echo $insert_medicine_details;
				$insert_header_run  	= mysql_query($insert_medicine_details);
				$count++;
			}

		}*/
		$insert_medicine_details = "insert into USER_MEDICATION_DETAILS (`UserID`,`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`ThresholdLimit`,`MedicineCount`,`MedicineTypeID`,`When`,`SpecificTime`,`Instruction`,`Link`, `OriginalFileName`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$threshold','$medcount','$medcount_type','$when','$specifictime','$instruction','$fullpath','$filename','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
	//	$insert_medicine_details = "insert into USER_MEDICATION_DETAILS (`UserID`,`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`MedicineCount`,`MedicineTypeID`,`When`,`SpecificTime`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$medcount','$medcount_type','$when','$specifictime','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
				//echo $insert_medicine_details;
				$insert_header_run  	= mysql_query($insert_medicine_details);
				$count++;
		//exit;
} else {
	//$specifictime = date('H:i:s',strtotime($specifictime));
	$insert_medicine_details = "insert into USER_MEDICATION_DETAILS (`UserID`,`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`ThresholdLimit`,`MedicineCount`,`MedicineTypeID`,`When`,`Instruction`,`Link`, `OriginalFileName`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$threshold','$medcount','$medcount_type','$when','$instruction','$fullpath','$filename','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
//	$insert_medicine_details = "insert into USER_MEDICATION_DETAILS (`UserID`,`PlanCode`,`PrescriptionNo`,`RowNo`,`MedicineName`,`MedicineCount`,`MedicineTypeID`,`When`,`Instruction`,`Link`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`IsCritical`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_presc_num', '$count', '$medicinename','$medcount','$medcount_type','$when','$instruction','$linkentered','$frequency','$frequencystring','$howlong','$howlongtype','$iscritical','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid')";
			//echo $insert_medicine_details;exit;
			$insert_header_run  	= mysql_query($insert_medicine_details);

}
		$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       {

		       	/*GET USER MOBILE TYPE*/
				$get_mobile_type 	= mysql_fetch_object(mysql_query("select MobilePhoneType from USER_DETAILS where UserID='$user'"));
				$mobile_type 		= $get_mobile_type->MobilePhoneType;
				/*END OF GET USER MOBILE TYPE*/
				/*Ordinary Plan User Calculation*/
				//echo "Mobile Type ".$mobile_type;
				if($mobile_type=='O')
				{
					$section_id 	= 1;
					include('ordinary_med_inst.php');
				}
				/*End of Ordinary Plan User Calculation*/

		       //	header("Location:cust_med_new.php");
		       } else {
		       	//header("Location:cust_med_new.php");
		       }
		}
	}
		$count++;
	}
		
  		$update_header = mysql_query("update USER_PLAN_HEADER set PlanUpdatedDate = current_timestamp where PlanCode='$plancode_for_current_plan' and UserID = '$userid_for_current_plan'");
  		header("Location:cust_med_new.php");
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
    		<h6 style="font-family:Freestyle;font-size:33px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;">Prescription List</h6>
    		<div class="sidebarheadings">Master Prescriptions :</div>
    				    		<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_prescriptions 		= "select t1.PrescriptionNo,t1.PlanCode, t1.PrescriptionName, t1.DoctorsName, t1.CreatedDate from MEDICATION_HEADER as t1, PLAN_HEADER as t2 where t1.PlanCode = t2.PlanCode and t2.MerchantID = '$logged_merchantid' and t2.PlanStatus='A'";
		    		$get_plan_prescriptions_run 	= mysql_query($get_plan_prescriptions);
		    		$get_plan_prescriptions_count 	= mysql_num_rows($get_plan_prescriptions_run);
		    		if($get_plan_prescriptions_count > 0){
			    		while ($get_presc_row = mysql_fetch_array($get_plan_prescriptions_run)) {
			    			$prescription_no   = $get_presc_row['PrescriptionNo'];
			    			$prescription_name = $get_presc_row['PrescriptionName'];
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
		    		} else {
		    			echo "<div style='color:#fff;'>No Prescriptions to show</div>";
		    		}
			    		?>
		    		</div>
		    		<div class="sidebarheadings" style="margin-top: -13px;">Assigned Prescriptions :</div>
		    				    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_prescriptions 		= "select t1.UserID, t1.PrescriptionNo, t1.PlanCode, t1.PrescriptionName, t1.DoctorsName, t1.CreatedDate from USER_MEDICATION_HEADER as t1, USER_PLAN_HEADER as t2 where t1.PlanCode = t2.PlanCode and t2.MerchantID = '$logged_merchantid' and t1.UserID = t2.UserID";
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
		    		} else {
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
							<option>AM</option>
							<option>PM</option>
							</select>
							</div>
							</div>
						</div>

						<div class="form-group">&nbsp;</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="pwd">Interval time between doses :</label>
							<div class="col-sm-6">
							<select class="form-control selectpicker" id="intervel_time" name="intervel_time">
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
    <div class="printable" style="display:none;">
 		<?php echo $display_print;?>
    </div>
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
	<script type="text/javascript" src="js/modernizr.js"></script>
	<script type="text/javascript" src="js/placeholders.min.js"></script>
	<script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="js/jasny-bootstrap.min.js"></script>
		<script type="text/javascript">

		$(document).ready(function() {
			$('#6').addClass('active');
			mainHeader = 115;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight);
		  	//$('#listBar').css({height: listBarHeight});
		  	//$('#dynamicPagePlusActionBar').css({height: listBarHeight});
		  	var browser_name = '<?php echo $browser_name; ?>';
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
			$('#thisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#editthisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#add_patient_info').click(function(){
				$('#patientdetailsmodal').modal('show');
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
				window.location.href = "customize_plan.php";
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
				var patientheight 				= $('#patientheight').val();
				var patientweight 				= $('#patientweight').val();
				var patientpressure 			= $('#patientpressure').val();
				var patienttemp 				= $('#patienttemp').val();
				var userHistory     			= $('#userHistory').val();
				var visit_notes     			= $('#visit_notes').val();
        var review_notes           = $('#review_notes').val();
				var userid_for_current_plan 	= $('#userid_for_current_plan').val();
				var logged_merchantid 			= $('#logged_merchantid').val();
				var plancode_for_current_plan 	= $('#plancode_for_current_plan').val();
				var dataString = "type=insert_patient_details&patientheight="+patientheight+"&patientweight="+patientweight+"&patientpressure="+patientpressure+"&patienttemp="+patienttemp+"&userHistory="+userHistory+"&visit_notes="+visit_notes+"&review_notes="+review_notes+"&userid_for_current_plan="+userid_for_current_plan+"&logged_merchantid="+logged_merchantid+"&plancode_for_current_plan="+plancode_for_current_plan;
					//alert(dataString);
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
						url			: "prescriptionCustomizePage.php",
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

			$(document).on('click', '.removelinkbutton', function(e) {
			    var linkid 	= $(this).attr('id'); 
				$('#deletelink'+linkid).val("1");
			});
		    
			//ON CLICK OF DONE BUTTON AFTER ENTERING SPECIFIC TIME
			$(document).on('click', '#specifictimeselected', function () {

				var getcurrentid = $('#specifictimeselectedid').val();
				//bootbox.alert(getcurrentid);
				var specifictimes = "";
				var providercount = 0;
				var providervalue = [];
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

			    //bootbox.alert(specifictimes);

		    	$('#specifictime'+getcurrentid).val(specifictimes);
		    	$('#specifictime'+getcurrentid).attr('type', 'text');
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
				 	//alert(medicinename);
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

   $('#specific_time_inp').keyup(function(){
        if ($(this).val() > 12){
        bootbox.alert("Start time will be between 1 to 12");
        $(this).val('');
        }else{
          specific_data_find();
        }

    });

    $('#specific_time_min').keyup(function(){
        if ($(this).val() > 59){
        bootbox.alert("Minute will be between 0 to 59");
        $(this).val('');
        }else{
          specific_data_find();
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
        $('#push_time').html(text);
    }

	$('#intervel_time').change(function(){

      var val =  $('#intervel_time').val();

      var val_1 = $('#specific_time_inp').val();
         
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

     });

  $("#specific_time_inp").blur(function(){

    var val =  $('#intervel_time').val();

      var val_1 = $('#specific_time_inp').val();
         
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

 
  });

    $('#time_type').change(function(){

      var val =  $('#intervel_time').val();

      var val_1 = $('#specific_time_inp').val();

      if(val > 0 && val_1 > 0){
        specific_data_find(); 
      }else{
        bootbox.alert("Please select intervel time and enter start time.");
        return false;
      }


    });

	</script>
    </body>
    <?php
    include('include/unset_session.php');
	?>
</html>
