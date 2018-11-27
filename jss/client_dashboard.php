<?php
//echo "2013-01-01-23-07";
//echo $respdatadate   = date('Y-m-d-H-i',strtotime('2015-06-02 18:30:00'));exit;
//echo round(microtime(true) * 1000);exit;
date_default_timezone_set('Asia/Kolkata');
$current_date       = date('Y-m-d');
session_start();
$uri = basename($_SERVER['PHP_SELF']);
$query = $_SERVER['QUERY_STRING'];
//echo $query;exit;
$current_page_name = $uri."?".$query;
$_SESSION['page_back_from_customize_page'] = $current_page_name;
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
//$_SESSION['current_assigned_plan_code']="";
//echo $plan_to_customize;exit;
$id = "";
if (isset($_REQUEST['id'])) {
  $id = $_REQUEST['id']; 
}
else {
  header("location:dashboard.php");
}
$pc = "";
if (isset($_REQUEST['pc'])) {
  $pc = $_REQUEST['pc']; 
}

$count = 0;
$plancodearray = array();
//GET ALL PLANS ASSIGNED TO THIS USER
$get_all_plans = "select PlanCode, PlanName, PlanDescription,PlanCoverImagePath from USER_PLAN_HEADER where UserID='$id' and MerchantID='$logged_merchantid'";
//Removed PlanStatus = 'A' from the query
//echo $get_all_plans;exit;
$get_all_plans_run    = mysql_query($get_all_plans);
$get_all_plans_count  = mysql_num_rows($get_all_plans_run);
if($get_all_plans_count > 0){
  while ($plan_row = mysql_fetch_array($get_all_plans_run)) {
    $count++;
    $plancode   = $plan_row['PlanCode'];
    $planname   = $plan_row['PlanName'];
    $plandesc   = $plan_row['PlanDescription'];
    $planimg    = $plan_row['PlanCoverImagePath'];
    array_push($plancodearray, $plancode);
  }

} else {
?>
  <script type="text/javascript">
    alert("No Plans Assigned To This Patient Yet.");
    window.location.href = "dashboard.php";
  </script>
  <?php
  exit;
}
  if($pc == ""){
      $pc = $plancodearray[0];
    }
//GET PATIENT DATAS
$patient_data_flag = 0;
$get_patient_data   = "select ID, UserID, MerchantID, Height, Weight, BloodPressure, Temperature, CreatedDate from VISIT_DATA where UserID='$id' and MerchantID = '$logged_merchantid'";
// /echo $get_patient_data;exit;
$patient_data_run   = mysql_query($get_patient_data);
$patient_data_count   = mysql_num_rows($patient_data_run);
if($patient_data_count > 0){
  $dataID     = array();
  $Height     = array();
  $Weight     = array();
  $BloodPressure  = array();
  $Temperature  = array();
  $CreatedDate  = array();
  while ($patient_data = mysql_fetch_array($patient_data_run)) {
    $dataID[$patient_data_flag]       = $patient_data['ID'];
    $Height[$patient_data_flag]       = $patient_data['Height'];
    $Weight[$patient_data_flag]       = $patient_data['Weight'];
    $BloodPressure[$patient_data_flag]    = $patient_data['BloodPressure'];
    $Temperature[$patient_data_flag]    = $patient_data['Temperature'];
    $CreatedDate[$patient_data_flag]    = $patient_data['CreatedDate'];
    $patient_data_flag++;
  }
}
//Get patient notes
$patient_notes_flag = 0;
$get_patient_notes    = "select ID, UserID, MerchantID, Notes, CreatedDate from VISIT_NOTES where UserID='$id' and MerchantID = '$logged_merchantid'";
// /echo $get_patient_notes;exit;
$patient_notes_run  = mysql_query($get_patient_notes);
$patient_notes_count  = mysql_num_rows($patient_notes_run);
if($patient_notes_count > 0){
  $Notes    = array();
  $CreatedDate2   = array();
  while ($patient_notes = mysql_fetch_array($patient_notes_run)) {
    $Notes[$patient_notes_flag]       = $patient_notes['Notes'];
    $CreatedDate2[$patient_notes_flag]    = $patient_notes['CreatedDate'];
    $patient_notes_flag++;
  }
}
//Get review notes
$patient_review_flag = 0;
$get_patient_review    = "select ID, UserID, MerchantID, Notes, CreatedDate,TimeSpent from REVIEW_NOTES where UserID='$id' and MerchantID = '$logged_merchantid'";
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
$get_patient_history    = "select UserHistory from USER_DETAILS where UserID='$id'";
//echo $get_patient_history;exit;
$patient_history_run  = mysql_query($get_patient_history);
$patient_history_count  = mysql_num_rows($patient_history_run);
if($patient_history_count > 0){
  while ($patient_hist = mysql_fetch_array($patient_history_run)) {
    $UserHistory      = $patient_hist['UserHistory'];
  }
}

$display_print = "<div id='printtable' style='border:1px solid #004F35;width:100%;background-color: #004F35;color:#fff;height:40px;text-align:center;line-height:40px;font-size:26px;    font-family:RalewayBold;'>PATIENT INFORMATION</div>";
          $get_profile_details1 = "select t1.FirstName, t1.LastName, t1.Gender, t1.DOB, t1.BloodGroup, t1.CountryCode, t1.StateID, t1.CityID, t2.MobileNo, t2.EmailID, t1.AddressLine1, t1.AddressLine2, t1.PinCode, t1.AreaCode, t1.Landline, t1.MobilePhoneType, t1.LanguageID,t1.SupportPersonName,t1.SupportPersonMobileNo from USER_DETAILS as t1, USER_ACCESS as t2 where t1.UserID = t2.UserID and t1.UserID = '$id'";
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

$get_plan_details = "select t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from USER_PLAN_HEADER as t1, CATEGORY_MASTER as t2 where t1.PlanCode = '$pc' and t1.CategoryID = t2.CategoryID and t1.UserID = '$id'";
//echo $get_plan_details;exit;
$get_plan_details_run = mysql_query($get_plan_details);
$get_plan_details_count = mysql_num_rows($get_plan_details_run);
  if($get_plan_details_count > 0){
    while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
      $plandet_name     = $plan_details['PlanName'];
      $_SESSION['current_assigned_plan_name'] =  $plandet_name;
      $plandet_desc_full     = $plan_details['PlanDescription'];
      $plandet_desc     = substr($plan_details['PlanDescription'], 0, 120);
      if(strlen($plandet_desc) >= 120){
        $plandet_desc = $plandet_desc."...";
      }
      if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
        $plandet_path       = "uploads/planheader/".$plan_details['PlanCoverImagePath'];
      } else {
        $plandet_path       = "uploads/planheader/default.jpg";
      }
      $plandet_catg     = $plan_details['CategoryName'];
      $plandet_cid    = $plan_details['CategoryID'];

      $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'><tr><td style='padding-left:5px;border:1px solid #D3D3D3;'><div style='min-height:35px;'>Plan Name : <b>$plandet_name</b></div></td></tr><tr><td style='padding-left:5px;border:1px solid #D3D3D3;background-color:#F1F1F1;'><div style='min-height:35px;'>$plandet_desc</di></td></tr></table>";

    }
  }
$get_medication = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.SpecificTime,t2.RowNo,t3.ShortHand,t2.Instruction,
t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString
from USER_MEDICATION_HEADER as t1,USER_MEDICATION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3
where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$pc' 
and t2.When=t3.ID and t1.UserID = t2.UserID and t1.UserID = '$id'";
//echo $get_medication;exit;
$get_medication_qry     = mysql_query($get_medication);
$get_medication_count   = mysql_num_rows($get_medication_qry);
if($get_medication_count)
{
  $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>MEDICATION</div>";
  $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
  while($medication_rows=mysql_fetch_array($get_medication_qry))
  {
    $medprescno           = (empty($medication_rows['PrescriptionNo']))           ? '-' : $medication_rows['PrescriptionNo'];
    $medprescname         = (empty($medication_rows['PrescriptionName']))         ? '-' : $medication_rows['PrescriptionName'];
    $meddocname             = (empty($medication_rows['DoctorsName']))              ? '-' : $medication_rows['DoctorsName'];
    $medname              = (empty($medication_rows['MedicineName']))             ? '-' : $medication_rows['MedicineName'];
    $medshort               = (empty($medication_rows['ShortHand']))                ? '-' : $medication_rows['ShortHand'];
    $medrowno               = (empty($medication_rows['RowNo']))                    ? '-' : $medication_rows['RowNo'];
    $medinstr             = (empty($medication_rows['Instruction']))              ? '-' : $medication_rows['Instruction'];
    $medfreq                = (empty($medication_rows['Frequency']))                ? '-' : $medication_rows['Frequency'];
    $medfreqstring          = (empty($medication_rows['FrequencyString']))          ? '-' : $medication_rows['FrequencyString'];
    $medhowlong             = (empty($medication_rows['HowLong']))                  ? '-' : $medication_rows['HowLong'];
    $medhowlongtype         = (empty($medication_rows['HowLongType']))              ? '-' : $medication_rows['HowLongType'];
    $mediscritical          = (empty($medication_rows['IsCritical']))               ? '-' : $medication_rows['IsCritical'];
    $medresponsereq         = (empty($medication_rows['ResponseRequired']))         ? '-' : $medication_rows['ResponseRequired'];
    $medstartflag           = (empty($medication_rows['StartFlag']))                ? '-' : $medication_rows['StartFlag'];
    $mednumdays       = (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '-' : $medication_rows['NoOfDaysAfterPlanStarts'];
    $medspecdate            = (empty($medication_rows['SpecificDate']))             ? '-' : $medication_rows['SpecificDate'];
    $medspectime            = (empty($medication_rows['SpecificTime']))             ? '-' : $medication_rows['SpecificTime'];

    $display_print .= "<tr><td style='width:25%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medname</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medshort</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medinstr</td><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$medfreq</td></tr>";
  }
  $display_print .= "</table>";
}
//GET APPOINTMENT DETAILS
$get_appointment= "select distinct t2.AppointmentDate,t2.AppointmentTime,t2.AppointmentShortName,t2.DoctorsName,t2.AppointmentRequirements
from USER_APPOINTMENT_HEADER as t1,USER_APPOINTMENT_DETAILS as t2
where t1.PlanCode=t2.PlanCode and t1.PlanCode='$pc' and t1.UserID = t2.UserID and t1.UserID = '$id'";
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
    $apponame     = (empty($appointment_rows['AppointmentShortName']))    ? '' : $appointment_rows['AppointmentShortName'];
    $appodoc        = (empty($appointment_rows['DoctorsName']))             ? '' : $appointment_rows['DoctorsName'];
    $apporeq    = (empty($appointment_rows['AppointmentRequirements'])) ? '' : $appointment_rows['AppointmentRequirements']; 
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
where t1.PlanCode=t2.PlanCode and t1.SelfTestID=t2.SelfTestID and t1.PlanCode='$pc' and t1.UserID = t2.UserID and t1.UserID = '$id'";
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
where t1.PlanCode=t2.PlanCode and t1.LabTestID=t2.LabTestID and t1.PlanCode='$pc' and t1.UserID = t2.UserID and t1.UserID = '$id' and t2.LabTestDate is null";
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
                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$pc' 
                    and t2.When=t3.ID and t1.UserID = t2.UserID and t1.UserID = '$id'";
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
$get_goals = "select distinct UserID, PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where UserID = '$id' and PlanCode = '$pc'";
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
$realgraphcount = 0;
//Get User Details
$get_user_details = "select concat('+', TRIM(LEADING '0' FROM t1.MobileNo)) as MobileNo, t1.EmailID, t1.PlanpiperEmailID, t2.FirstName, t2.LastName, t2.AddressLine1, t2.AddressLine2, t2.Gender, t2.DOB, t2.StateID, t2.CityID, t3.PatientSummary, t3.DiseaseID, t2.CountryCode from USER_ACCESS as t1, USER_DETAILS as t2,  USER_PLAN_HEADER as t3 where t1.UserID='$id' and t1.UserID = t2.UserID and t1.UserID = t3.UserID and t3.PlanCode = '$pc'";
//echo $get_user_details;exit;
$get_user_details_run = mysql_query($get_user_details);
$get_user_count = mysql_num_rows($get_user_details_run);
if($get_user_count > 0){
  while ($details = mysql_fetch_array($get_user_details_run)) {
    $mobileno     = formatPhoneNumber($details['MobileNo']);
    $emailid      = $details['EmailID'];
    $planpiper_email = $details['PlanpiperEmailID'];
    $firstname    = stripslashes($details['FirstName']);
    $lastname     = stripslashes($details['LastName']);
    $address      = "";
    $addline1     = stripslashes($details['AddressLine1']);
    if($addline1){
      $address = $addline1;
    }
    $addline2     = stripslashes($details['AddressLine2']);
    if($addline2){
      $address .= ", ".$addline2."<br>";
    }
    $cityid       = $details['CityID'];
    $stateid      = $details['StateID'];
    $countrycode  = $details['CountryCode'];
    $cityname     = "";
    $statename    = "";
    if(($stateid!="0")&&($stateid!=NULL)&&($stateid!="")){
        $state_query = mysql_query("select StateName from STATE_DETAILS where StateID='$stateid' and CountryCode = '$countrycode'");
        $statename = mysql_result($state_query, 0);
        $city_query = mysql_query("select CityName from CITY_DETAILS where StateID='$stateid' and CityID = '$cityid'");
        $cityname = mysql_result($city_query, 0);
    }
    $disease_name = "";
    $disease_id   = $details['DiseaseID'];
    if(($disease_id!="0")&&($disease_id!=NULL)&&($disease_id!="")){
      $disease_query = mysql_query("select Disease from MASTER_DISEASES where ID='$disease_id'");
        $disease_name = mysql_result($disease_query, 0);
    }
    if($cityname){
      $address .= $cityname;
    }
    if($statename){
      $address .= ", ".$statename;
    }
    //echo $cityname;exit;
    $gender       = $details['Gender'];
    if($gender){
      $gender     = $gender;
    } else {
      $gender = "";
    }
    if($gender == "M"){
      $gender = "MALE";
    } else if($gender == "F"){
      $gender = "FEMALE";
    } else {
      $gender = "";
    }
    $dob          = $details['DOB'];
    $age          = "";
    if(($dob != "0000-00-00")&&($dob != NULL)&&($dob != "")&&($gender)){
        $gender = $gender."-";
    }
    if(($dob != "0000-00-00")&&($dob != NULL)&&($dob != "")){
    $dob          = date('Y',strtotime($details['DOB']));
      $current_date   = date('Y');
      $age  = $current_date - $dob;
      $age  = $age." YEARS";
    }
    $patientsummary = stripslashes($details['PatientSummary']);
    if($patientsummary == ""){
      $patientsummary = "-";
    }
//   echo $age;exit;
  }
} else {
  ?>
  <script type="text/javascript">
    alert("Please Try Again.");
    window.location.href = "dashboard.php";
  </script>
  <?php
}

  $userid               = $id;
  $plancode             = $pc;
  $medvalues            = "";
  $medicines            = array();
  $medgraph_values      = array();
  $medgraph_value       = array();
  $count              = 0;
  $medicineselect     = "";
  $get_all_medicines  = mysql_query("select MedicineName from USER_MEDICATION_DETAILS where UserID = '$userid' and PlanCode = '$plancode'");
  $get_num_of_medicines = mysql_num_rows($get_all_medicines);
  if($get_num_of_medicines > 0){
    while ($namerow = mysql_fetch_array($get_all_medicines)) {
      $medicinename   = stripslashes($namerow['MedicineName']);
   
      
      if(!in_array($medicinename, $medicines)){
        array_push($medicines, $medicinename);
        $get_medication_values_query = "select t1.DateTime, t1.ResponseRequiredStatus, t2.MedicineName, t1.PrescriptionNo, t1.RowNo from USER_MEDICATION_DATA_FROM_CLIENT as t1, USER_MEDICATION_DETAILS as t2 where t1.UserID = t2.UserID and t1.PlanCode = t2.PlanCode and t1.PrescriptionNo = t2.PrescriptionNo and t1.RowNo = t2.RowNo and t1.UserID = '$userid' and t1.PlanCode = '$plancode' and t2.MedicineName = '$medicinename' and t1.PlanCode REGEXP '^[A-Za-z]'";
        //echo $get_medication_values_query;exit;
          $get_medication_values_run  = mysql_query($get_medication_values_query);
          $get_medication_values_count = mysql_num_rows($get_medication_values_run);
          $yes_count = 0;
          $no_count  = 0; 
          
            if($get_medication_values_count > 0){
              $medvalues  = "['NOT TAKEN', 100],['TAKEN', 0]";
            while ($row = mysql_fetch_array($get_medication_values_run)) {
              $responsevalue    = $row['ResponseRequiredStatus'];
              $prescno          = $row['PrescriptionNo'];
              $rowno            = $row['RowNo'];
              if($responsevalue == "Y"){
                $yes_count++;
              } else {
                $no_count++;
              }
            }
            $uniquevalue        = "med".$plancode.$prescno.$rowno;
            $medvalue           = $no_count."~~".$yes_count;
            $medvalues  = "['NOT TAKEN',$no_count],['TAKEN',$yes_count]";
            //echo $medvalues;
            $medgraph_values[$uniquevalue]  = $medvalues;
            $medgraph_value[$uniquevalue]   = $medvalue;
            $medicineselect .= "<option value='$uniquevalue'>$medicinename</option>";
            $count++;
            $realgraphcount++;
           }
        }
    }
     
  } else {

  }
//echo $medvalues;
  $instvalues             = "";
  $instructions           = array();
  $instgraph_values       = array();
  $instgraph_value        = array();
  $count              = 0;
  $instructionselect     = "";
  $get_all_instructions  = mysql_query("select MedicineName from USER_INSTRUCTION_DETAILS where UserID = '$userid' and PlanCode = '$plancode'");
  $get_num_of_instructions = mysql_num_rows($get_all_instructions);
  if($get_num_of_instructions > 0){
    while ($namerow = mysql_fetch_array($get_all_instructions)) {
      $instructionname   = $namerow['MedicineName'];
      
      
      if(!in_array($instructionname, $instructions)){
        array_push($instructions, $instructionname);
        $get_instruction_values_query = "select t1.DateTime, t1.ResponseRequiredStatus, t2.MedicineName, t1.PrescriptionNo, t1.RowNo from USER_INSTRUCTION_DATA_FROM_CLIENT as t1, USER_INSTRUCTION_DETAILS as t2 where t1.UserID = t2.UserID and t1.PlanCode = t2.PlanCode and t1.PrescriptionNo = t2.PrescriptionNo and t1.RowNo = t2.RowNo and t1.UserID = '$userid' and t1.PlanCode = '$plancode' and t2.MedicineName = '$instructionname' and t1.PlanCode REGEXP '^[A-Za-z]'";
        //echo $get_instruction_values_query;exit;
          $get_instruction_values_run  = mysql_query($get_instruction_values_query);
          $get_instruction_values_count = mysql_num_rows($get_instruction_values_run);
          $yes_count = 0;
          $no_count  = 0; 
          
            if($get_instruction_values_count > 0){
              $instvalues  = "['NOT DONE', 100],['DONE', 0]";
            while ($row = mysql_fetch_array($get_instruction_values_run)) {
              $responsevalue    = $row['ResponseRequiredStatus'];
              $prescno          = $row['PrescriptionNo'];
              $rowno            = $row['RowNo'];
              if($responsevalue == "Y"){
                $yes_count++;
              } else {
                $no_count++;
              }
            }
            $uniquevalue        = "inst".$plancode.$prescno.$rowno;
            $instvalue          = $no_count."~~".$yes_count;
            $instvalues         = "['NOT DONE', $no_count],['DONE', $yes_count]";
            //echo $instvalues;
            $instgraph_values[$uniquevalue] = $instvalues;
            $instgraph_value[$uniquevalue] = $instvalue;
            $instructionselect .= "<option value='$uniquevalue'>$instructionname</option>";
            $count++;
            $realgraphcount++;
           }
        }
    }
     
  } else {

  }
if(isset($_REQUEST['hidden_value'])){
  $userarray = array();
  $userarray = $_REQUEST['magicsuggest'];
  foreach ($userarray as $user) {
    if($user != ""){
      header("location:client_dashboard.php?id=$user");
    }
  }

}
$report_given_by = $logged_firstname." ".$logged_lastname;
if(isset($_REQUEST['hidden_value_upload'])){
    if(!empty($_FILES)) // [START FILE UPLOADED]
{
$validation_type = 1;
if($validation_type == 1)
{
   $mime = array('image/gif' => 'gif','image/jpeg' => 'jpeg','image/png' => 'png','image/jpg' => 'jpg','application/pdf' => 'pdf');
}
// Check for a correct extension. The image file hasn't an extension? Add one

   if($validation_type == 1)
   {
      $report_upload        = (empty($_FILES['report_upload']['name'])) ? ''    : $_FILES['report_upload']['name'];
        /*GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/
    $country_id         = "";   $country_code="";   $mobile_no="";
    $get_folder_name    = "select substr(MobileNo,1,5) as CountryCode,substr(MobileNo,6) as MobileNo from USER_ACCESS where UserID='$id'";
    $get_folder_name_qry= mysql_query($get_folder_name);
    $get_count          = mysql_num_rows($get_folder_name_qry);
    if($get_count == 1)
    {
        $row            = mysql_fetch_array($get_folder_name_qry);
        $country_code   = $row['CountryCode'];
            if($country_code)
            {
                $country_id  = mysql_result(mysql_query("select substr(CountryID,1,2) from COUNTRY_DETAILS where CountryCode='$country_code'"), 0);
            }
        $mobile_no      = $row['MobileNo'];
    //echo $country_id.$mobile_no;
    }
    /*END OF GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/

    $path               = "uploads/folder/$country_id$mobile_no/";

        //echo $path;exit;
        if(!is_dir($path)){
            mkdir($path);
          }
          if($report_upload){
              $date          = round(microtime(true)*1000).mt_rand(1000000,9999999);
              $randid2       = round(microtime(true)*1000).mt_rand(1000000,9999999);
              $no            = rand();
              $imgtype       = explode('.', $report_upload);
              $ext           = end($imgtype);
              $filename      = $imgtype[0];
              $report_upload = str_replace(" ","_",$report_upload);
              $fullfilename  = $date.'~_'.$report_upload;
             // $onlyfilename  = $date.'~_'.$filename;
              $fullpath      = $path . $date.'~_'.$report_upload;
              move_uploaded_file($_FILES['report_upload']['tmp_name'], $fullpath);
          }
        
        $insert_report      = "insert into MYFOLDER_PARENT (ID,UserID, ReportName, ReportGivenBy, ReportCreatedOn, TypeOfReport, SourceOfUpload, CreatedDate, CreatedBy) VALUES ('$randid2','$id','$report_upload','$report_given_by',now(),'2','D',now(),'$logged_userid')";
        $insert_qry         = mysql_query($insert_report);
        /*Rimith*/
        $check_insert       = mysql_affected_rows();
        /*SEND NOTIFICATION TO USER*/
        if($check_insert)
        {
        $paid = "";
          $get_user    =   "select UserID,OSType,DeviceID,PaidUntil from USER_ACCESS where UserID='$id'";
          //echo $get_user;exit;
          $get_user_qry  = mysql_query($get_user);
          $get_user_count= mysql_num_rows($get_user_qry);
            if($get_user_count)
            {//echo 123;exit;
            $msg = "$logged_companyname has sent you a report. Tap here to load it on your phone.";
              while($user_rows = mysql_fetch_array($get_user_qry))
              {
              $user_id        = $user_rows['UserID'];
              $user_os_type   = $user_rows['OSType'];
              $user_device_id = $user_rows['DeviceID'];
              $paid_until     = $user_rows['PaidUntil'];
              if($paid_until==NULL || $paid_until=='0000-00-00')
              {
                 $paid = "N";
              }
              elseif($paid_until!=NULL && $paid_until!='0000-00-00' && $paid_until!="")
              {
                $check = strtotime($current_date) - strtotime($paid_until);
                //echo $check;exit; 
                if($check<0)
                {
                    $paid = "Y";
                }
                else
                {
                     $paid = "N";
                }
              }
              //echo $paid = "N";
              if($paid=='Y')
              {
                if($user_id)
                {
                  //Push notification for Android and IOS
                  if($user_os_type=='A' && $user_device_id!='')
                  {
                  $regId          = $user_device_id;
                  $res['message'] = $msg;
                  $res['userid']  = $id;
                  $res['flag']    = "report";
                  $message        = json_encode($res); 
                  include("gcm_server_php/send_message.php");
                  }
                  else if($user_os_type=='I' && $user_device_id!='')
                  {
                  $deviceToken= $user_device_id;
                  //echo "<br>";
                  $userid     = $id;
                  //echo "<br>";exit;
                  $flag       = "report";
                  $message    = $msg;
                  include("apple/local/push.php");
                  //include("apple/production/push.php");
                  }
                }
              }
              }
            }
        }
        /*End of sending notification to user*/
        /*Rimith*/
        $report_id          = $randid2;
        $insert_image_info      = "insert into MYFOLDER_CHILD
                                (ID,MyFolderParentID,FileName,DisplayName,Status,CreatedDate,CreatedBy) VALUES
                                ('$date','$report_id','$fullfilename','$filename','A',now(),'$logged_userid')";
        $insert_image_info_qry  = mysql_query($insert_image_info);

         header("location:client_dashboard.php?id=$id");
      }
   }
}

?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Plan Piper - Dashboard</title>
      <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
      <link rel="stylesheet" type="text/css" href="css/jquery.bxslider.css">
      <link rel="stylesheet" type="text/css" href="css/magicsuggest.css">
      <link rel="stylesheet" type="text/css" href="css/planpiper.css">
      <link rel="stylesheet" type="text/css" href="fonts/font.css">
      <link rel="stylesheet" type="text/css" href="css/c3.css">
      <style type="text/css">
      .panel-default > .panel-heading{
          color: #000;
          background-color: #DEDEDE;
          border-color: #DEDEDE;
      }
      </style>
          <style type="text/css">
      .ms-ctn .ms-sel-item {
        background: #004f35;
        color: #fff;
        border: 1px solid #004f35;
        height: 35px;
        line-height: 35px;
      }
      .ms-ctn .ms-trigger{
        display: none;
      }
    </style>
    <link rel="shortcut icon" href="images/planpipe_logo.png"/>     
    <script type="text/javascript">
    function keychk(event) {
    //alert(123)
      if(event.keyCode==13){
        var id = "<?php echo $id;?>";
        //alert(id);
        var query = $('#search_report').val();
       // alert(query);
       window.location.href = "client_dashboard.php?id="+id+"&rq="+query;
      }
    }
  </script>  

      <script type="text/javascript"
          src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }">
          </script>
    <script type="text/javascript">
      function fullScreen(theURL) {
        window.open(theURL, '', 'fullscreen=yes, scrollbars=auto');
      }
    </script>  
    <script type="text/javascript">
        var imageflag = 0;
        var flag = "arrow1";
        var flag2 = 0;
          function changeimage(id){
            imageflag = "arrow"+id;
            if(flag != 0){
              if(imageflag == flag) {
              document.getElementById(imageflag).src = "images/down.png";
              flag2 = 1;
            } else if(imageflag != flag){
               document.getElementById(flag).src = "images/down.png";
               document.getElementById(imageflag).src = "images/up.png";
                } 
          } else {
            document.getElementById(imageflag).src = "images/up.png";
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
  <body style="overflow:hidden;">
  <div id="planpiper_wrapper">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
       <div class="col-sm-2 paddingrl0" id="sidebargrid">
        
        <div class="sidebar-nav" style="height:100%;">
      <div class="navbar navbar-default" role="navigation" style="height: 100%;">
        <div class="navbar-header">
        <div align="left">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        </div>
        <div class="navbar-collapse collapse sidebar-navbar-collapse">
          <ul class="nav navbar-nav sidebarsmallscreen">
            <li id="navusername" style="text-transform:uppercase;" class="hidden-xs">
              <div style="width:100%;" align="center">
              <?php 
              $userdp = "";
                if(($logged_userdp != "")&&($logged_userdp != NULL)){
                  $userdp = "uploads/profile_pictures/".$logged_userdp;
                }else {
                  $userdp = "images/dp.png";
                }
              ?>
                <div id="dpdiv" align="center"><img src="<?php echo $userdp;?>"  style="width:100%;height:auto;"></div>
              </div>
              <div id="namediv" align="center">
                <?php echo $logged_firstname." ".$logged_lastname;?>
              </div>
            </li>
                
                <li id="dashboard" class="navbar_li"><a href="dashboard.php" class="navbar_href">DASHBOARD</a></li>
                <li id="user" class="navbar_li" style="text-transform:uppercase;"><a class="navbar_href" style="background-color:#01422A;color:#fff;text-align:left;border-bottom:1px solid #e0e0e0;"><?php echo $firstname;?>'s plans :-</a></li>
                <li id="clientplan" style="background-color:#01422A;"><div id="planlistinsidebar">
                  <?php
                  $count = 0;
                  //GET ALL PLANS ASSIGNED TO THIS USER
                  $get_all_plans = "select PlanCode, PlanName, PlanDescription,PlanCoverImagePath from USER_PLAN_HEADER where UserID='$id' and MerchantID='$logged_merchantid'";
                  //echo $get_all_plans;exit;
                  $get_all_plans_run    = mysql_query($get_all_plans);
                  $get_all_plans_count  = mysql_num_rows($get_all_plans_run);
                  if($get_all_plans_count > 0){
                    while ($plan_row = mysql_fetch_array($get_all_plans_run)) {
                      $count++;
                      $plancode   = $plan_row['PlanCode'];
                      $planname   = stripslashes($plan_row['PlanName']);
                      if($pc == $plancode){
                        ?>
                          <div style="text-align:left;color:#fff;padding-left:30px;font-size:1.1em;padding-bottom:5px;"><a href="client_dashboard.php?id=<?php echo $id;?>&pc=<?php echo $plancode;?>" style="color:#fff;"><img src="images/dot.png" style="height:5px;margin-right:10px;"><u><?php echo $planname;?></u></a></div>
                        <?php
                      } else {
                        ?>
                          <div style="text-align:left;color:#fff;padding-left:30px;font-size:1.1em;padding-bottom:5px;"><a href="client_dashboard.php?id=<?php echo $id;?>&pc=<?php echo $plancode;?>" style="color:#fff;"><img src="images/dott.png" style="height:5px;margin-right:10px;"><?php echo $planname;?></a></div>
                        <?php
                      }
                    }
                  }
                 ?>  
                </div></li>
                <li id="plans" class="navbar_li"><a href="plan_list.php" class="navbar_href">PLAN MANAGEMENT</a></li>
                <?php
                if(($logged_usertype!='I'))
                {
                ?>
                <li id="medicine" class="navbar_li"><a href="medicine_directory.php" class="navbar_href">MEDICINE DIRECTORY</a></li>
                <?php }?>
                <?php
                if(($logged_usertype=='I') && ($logged_roleid !=4) && ($logged_roleid !=3)){
                ?>
                <?php
                } else {
                ?>
                <li id="users" class="navbar_li"><a href="user_list.php" class="navbar_href">HEALTHCARE PROVIDERS</a></li>
                <?php
                }
                ?>
                <?php
                if(($logged_usertype=='I') && ($logged_roleid !=4) && ($logged_roleid !=3)){
                ?>
                <li id="planusers" class="navbar_li"><a href="plan_users.php" class="navbar_href">FAMILY MEMBERS</a></li>
                <?php
                }
                else {
                ?>
                <li id="planusers" class="navbar_li"><a href="plan_users.php" class="navbar_href">PATIENTS</a></li>
                <?php
                }
                ?>
                <li id="assign" class="navbar_li"><a href="assign_plan.php" class="navbar_href">ASSIGN PLANS</a></li>
                <li id="notification" class="navbar_li"><a href="push_notification.php" class="navbar_href">SEND NOTIFICATION</a></li>
                <?php if(($logged_roleid !=4) && ($logged_roleid !=3)){?><li id="profile" class="navbar_li"><a href="profile.php" class="navbar_href">PROFILE MANAGEMENT</a></li><?php }?>
                <!-- <li id="reports" class="navbar_li"><a href="reports.php" class="navbar_href">REPORTS</a></li> -->
                <li id="logout" class="navbar_li"><a href="logout.php" class="navbar_href">LOGOUT</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </div>

     
     </div>
     <div class="col-sm-10 paddingrl0" id="content_wrapper" style="overflow-x:hidden;overflow-y:auto;">
      <?php include_once('top_header.php');?>
        
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="plantitle" style="height:70px;padding-left:2px;">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left">
                <form name="frm_search_user" id="frm_search_user" method="POST">
                  <div id="magicsuggest" name="magicsuggest" style="width:100%;"></div>
                  <input type="hidden" name="hidden_value" id="hidden_value" value="1">
                </form>
              </div>
               <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 paddingr0" style="background-color: #DFDFDF;height: 52px;margin-top: 1px;margin-left: -5px;">
                <span><img src="images/find.png" style="height:30px;cursor:pointer;margin-top:5px;" class="searchbutton" name="searchbutton" id="searchbutton"></span>
              </div>
               <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 paddingr0" align="center" style="font-family: RalewayRegular;font-size: 0.6em;">
                <span style="font-size:1.5em;text-transform:capitalize;"><strong><?php echo $firstname." ".$lastname;?></strong></span>
                <div style="font-size:0.8em;"><?php echo $gender."".$age?></div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingr0" align="left" style="font-family: RalewayRegular;font-size: 0.5em;text-align:right;">
                <div>&nbsp;<img src="images/cdphone.png" style="height:20px;width:auto;">&nbsp;<?php echo $mobileno;?></div>
                <div><img src="images/cdemail.png" style="height:10px;width:auto;">&nbsp;<?php echo $emailid;?></div>
                <div><span style="border:1px solid #fff;border-radius:10px;padding-right:5px;cursor:pointer;font-size:0.9em;" id="viewmoredetails"><img src="images/info.png" style="height:16px;width:auto;margin-top:-3px;">&nbsp;Review/Notes</span></div>
              </div>
              <!-- <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingr0" align="left">
                <div><img src="images/cdaddress.png" style="height:20px;width:auto;">&nbsp;<?php echo $addline1;?></div>
                <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $addline2;?></div>
              </div> -->
          </div>
          <div>
          <?php
          $start_date = "";
          $end_date = "";
            if($count == "0"){
              echo "<div align='center' style='font-size:1.5em;font-family:RalewayRegular;'>No Plan Assigned to this patient.</div>";
            } else {
              //print_r($plancodearray);
              //$current_plan_code = $plancodearray[0];
              if($pc == ""){
                $pc = $plancodearray[0];
              }
              $current_plan_code = $pc;
              $num_of_plancodes = sizeof($plancodearray);
              $get_each_plans = "select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t1.UserStartOrUpdateDateTime, t1.PlanEndDate, t2.CategoryName from USER_PLAN_HEADER as t1, CATEGORY_MASTER as t2 where UserID='$id' and MerchantID='$logged_merchantid' and PlanCode='$current_plan_code'and t1.CategoryID = t2.CategoryID";
              //echo $get_each_plans;exit;
              $get_each_plans_run    = mysql_query($get_each_plans);
              $get_each_plans_count  = mysql_num_rows($get_each_plans_run);
              if($get_each_plans_count > 0){
                while ($plan_each = mysql_fetch_array($get_each_plans_run)) {
                  $eachplancode   = $plan_each['PlanCode'];
                  $eachplanname   = stripslashes($plan_each['PlanName']);
                  $eachplandesc   = stripslashes(substr($plan_each['PlanDescription'], 0, 30));
                  if(strlen($eachplandesc)  >= 30){
                    $eachplandesc = $eachplandesc."...";
                  }
                  $eachplanimg    = "";
                  if(($plan_each['PlanCoverImagePath'] != "")&&($plan_each['PlanCoverImagePath'] != NULL)){
                    $eachplanimg    = "uploads/planheader/".$plan_each['PlanCoverImagePath'];
                  } else {
                    $eachplanimg    = "uploads/planheader/default.jpg";
                  }
                  $plancatname     = $plan_each['CategoryName'];
                  $plancatid       = $plan_each['CategoryID'];
                  $start_date      = $plan_each['UserStartOrUpdateDateTime'];
                  $end_date        = $plan_each['PlanEndDate'];
                }
              }
            ?>
            <div>
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div>
                 <div class="smallplanbox">
                    <img src="<?php echo $eachplanimg;?>" class="planboximg">
                    <div class="blackoverlay"></div>
                    <div class="planboxname"><?php echo $eachplanname;?></div>
                    <div class="planboxcatg"><?php echo $plancatname;?></div>
                    <div class="planboxdesc"><?php echo $eachplandesc;?></div>
                    <div class="planboxdate"><?php if(($start_date != "")&&($start_date != NULL)){
                       $start_date = date('jS M Y',strtotime($start_date));
                        $end_date  =  date('jS M Y',strtotime($end_date));
                      echo "<span style ='font-size:0.6em;'> ( $start_date - $end_date)</span>";
                     }?></div>
                  </div>
                </div>
                <div class="panel-group activitylist" id="accordion" role="tablist" aria-multiselectable="true" style="  overflow: scroll;overflow-x: hidden;">
              <?php 
                $get_medication = "select PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate from USER_MEDICATION_HEADER where UserID = '$id' and PlanCode = '$current_plan_code' limit 1";
                //echo $get_medication;exit;
                $get_medication_run   = mysql_query($get_medication);
                $get_medication_count = mysql_num_rows($get_medication_run);
                //echo $get_medication_count;
                if($get_medication_count > 0){
                  ?>
                  
                  <div class="panel panel-default">
                    <div class="" role="tab" id="headingOne">
                      <h4 class="panel-title">
                        <div class="dashboardheadings"><img src="images/editad.png" style="height:20px;margin-top:10px;margin-left:10px;cursor:pointer;" align="left" id="<?php echo $id."~~".$current_plan_code."~~med"; ?>" class="gotocustomize">
                          
                            <span style="color:#fff;">Medications</span>
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><img src="images/up.png" style="height:10px;width:auto;margin:5px;margin-top:15px;" align="right" id="arrow1" onclick='changeimage("1");'>
                          </a>
                        </div>
                      </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                      <div class="panel-body nopadding">
                      <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true" style="margin-bottom:0px;">
                        <?php
                        while ($med_head = mysql_fetch_array($get_medication_run)) {
                          $prescno      = $med_head['PrescriptionNo'];
                          $presname     = stripslashes($med_head['PrescriptionName']);
                          $docname      = stripslashes($med_head['DoctorsName']);
                          $createddate  = date('jS M Y',strtotime($med_head['CreatedDate']));
                          ?>
                            <div class="panel panel-default" style="border:transparent;">
                              <div class="panel-heading" role="tab" id="headingOne<?php echo $prescno;?>">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion2" href="#collapseOne<?php echo $prescno;?>" aria-expanded="true" aria-controls="collapseOne<?php echo $prescno;?>">
                                    <strong><?php echo $presname;?><div align="right" style="margin-top:-15px;">(<?php echo $createddate;?>)</div></strong>
                                  </a>
                                </h4>
                              </div>
                              <div id="collapseOne<?php echo $prescno;?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne<?php echo $prescno;?>">
                                <div style="padding:10px;">
                                  <?php
                                $med_details = mysql_query("select t1.MedicineName, t1.When, t1.SpecificTime, t2.ShortHand from USER_MEDICATION_DETAILS as t1, MASTER_DOCTOR_SHORTHAND as t2 where PlanCode = '$current_plan_code' and PrescriptionNo='$prescno' and UserID='$id' and t1.When = t2.ID");
                                $med_details_count = mysql_num_rows($med_details);
                                $count = 1;
                                if($med_details_count > 0){
                                  while ($med_row   = mysql_fetch_array($med_details)) {
                                    $medicine_name  = stripslashes($med_row['MedicineName']);
                                    $fortitle       = stripslashes($med_row['MedicineName']);
                                    $length         = strlen($medicine_name);
                                    if($length > 20){
                                      $medicine_name   = substr($medicine_name,0,20);
                                      $medicine_name   = $medicine_name."...";
                                    }
                                    $medicine_wid     = $med_row['When'];
                                    $medicine_when    = $med_row['ShortHand'];
                                    $medicine_time    = $med_row['SpecificTime'];
                                    $fortitletime     = $med_row['SpecificTime'];
                                    $timelength       = strlen($medicine_time);
                                    if($timelength > 10){
                                      $medicine_time   = substr($medicine_time,0,10);
                                      $medicine_time   = $medicine_time."...";
                                    }
                                    echo "<div style='border-bottom:1px solid #dedede;'>".$count.". ";
                                    echo "<span title='$fortitle'>".$medicine_name."</span>";
                                    if($medicine_wid != "16"){

                                        echo "<div align='right' style='margin-top:-15px;'>".$medicine_when."</div>";
                                    } else {
                                        echo "<div align='right' style='margin-top:-15px;' title='$fortitletime'>".$medicine_time."</div>";
                                    }
                                    echo "</div>";
                                     $count++;
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
                        ?>   
                        </div>                    
                      </div>
                    </div>
                  </div>

                  <?php
                }
                //$get_appointments = "select AppointmentDate from USER_APPOINTMENT_HEADER where UserID = '$id' and PlanCode = '$current_plan_code'";
                $get_appointments    = "select  t1.UserID, t1.PlanCode, t1.AppointmentShortName,
              t1.DoctorsName, t1.AppointmentDate, t1.AppointmentTime from USER_APPOINTMENT_DETAILS as t1,USER_PLAN_HEADER as t2 
              where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.PlanCode='$current_plan_code' and t1.UserID='$id'";
                //echo $get_appointments;exit;
                $get_appointments_run   = mysql_query($get_appointments);
                $get_appointments_count = mysql_num_rows($get_appointments_run);
                //echo $get_appointments_count;
                if($get_appointments_count > 0){
                  ?>
                  
                  <div class="panel panel-default" >
                    <div class="" role="tab" id="headingTwo">
                      <h4 class="panel-title">
                        <div class="dashboardheadings"><img src="images/editad.png" style="height:20px;margin-top:10px;margin-left:10px;cursor:pointer;" align="left" id="<?php echo $id."~~".$current_plan_code."~~appo"; ?>" class="gotocustomize">
                          <span style="color:#fff;">Appointments</span>
                          <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"><img src="images/down.png" style="height:10px;width:auto;margin:5px;margin-top:15px;" align="right" id="arrow2" onclick='changeimage("2");'></a>
                        </div>
                        
                      </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                      <div style="padding:10px;">
                        <?php
                        $count = 1;
                        while ($appo_head = mysql_fetch_array($get_appointments_run)) {
                          $appotime    = stripslashes($appo_head['AppointmentTime']);
                          $apponame    = stripslashes($appo_head['AppointmentShortName']);
                          $appodoc     = stripslashes($appo_head['DoctorsName']);
                          $appodate    = date('jS M Y',strtotime($appo_head['AppointmentDate']));
                          //echo $appodate;
                          echo "<div style='border-bottom:1px solid #dedede;'>".$count.". ";
                          echo $apponame;
                          echo "<div align='right' style='margin-top:-17px;'>".$appodate."</div>";
                          echo "</div>";
                           $count++;
                         }
                        ?>                       
                      </div>
                    </div>
                  </div>

                  <?php
                }
                $get_instruction = "select PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate from USER_INSTRUCTION_HEADER where UserID = '$id' and PlanCode = '$current_plan_code' limit 1";
                //echo $get_instruction;exit;
                $get_instruction_run   = mysql_query($get_instruction);
                $get_instruction_count = mysql_num_rows($get_instruction_run);
                //echo $get_instruction_count;
                if($get_instruction_count > 0){
                  ?>
                  <div class="panel panel-default">
                    <div class="" role="tab" id="headingThree" style="margin-bottom:0px;">
                      <h4 class="panel-title">
                        <div class="dashboardheadings"><img src="images/editad.png" style="height:20px;margin-top:10px;margin-left:10px;cursor:pointer;" align="left" id="<?php echo $id."~~".$current_plan_code."~~inst"; ?>" class="gotocustomize">
                         
                            <span style="color:#fff;">Instructions</span>
                             <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree"><img src="images/down.png" style="height:10px;width:auto;margin:5px;margin-top:15px;" align="right" id="arrow3" onclick='changeimage("3");'></a>
                        </div>
                        
                      </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                    <?php
                        while ($inst_head = mysql_fetch_array($get_instruction_run)) {
                          $prescno1    = $inst_head['PrescriptionNo'];
                          $presname1   = stripslashes($inst_head['PrescriptionName']);
                          $docname1    = stripslashes($inst_head['DoctorsName']);
                          $createddate1 = date('jS M Y',strtotime($inst_head['CreatedDate']));
                          ?>
                      <div class="panel-body nopadding">
                             <div class="panel panel-default">
                              <div class="panel-heading" role="tab" id="headingThree<?php echo $prescno1;?>">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion2" href="#collapseThree<?php echo $prescno1;?>" aria-expanded="true" aria-controls="collapseThree<?php echo $prescno1;?>">
                                    <strong><?php echo $presname1;?><div align="right" style="margin-top:-15px;">(<?php echo $createddate1;?>)</div></strong>
                                  </a>
                                </h4>
                              </div>
                              <div id="collapseThree<?php echo $prescno1;?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree<?php echo $prescno1;?>">
                                <div class="panel-body">
                                  <?php
                                $med_details = mysql_query("select t1.MedicineName, t1.When, t1.SpecificTime, t2.ShortHand from USER_INSTRUCTION_DETAILS as t1, MASTER_DOCTOR_SHORTHAND as t2 where PlanCode = '$current_plan_code' and PrescriptionNo='$prescno' and UserID='$id' and t1.When = t2.ID");
                                $med_details_count = mysql_num_rows($med_details);
                                $count = 1;
                                if($med_details_count > 0){
                                  while ($med_row = mysql_fetch_array($med_details)) {
                                    $medicine_name = stripslashes($med_row['MedicineName']);
                                    $fortitle     = stripslashes($med_row['MedicineName']);
                                    $length       = strlen($medicine_name);
                                    if($length > 20){
                                      $medicine_name   = substr($medicine_name,0,20);
                                      $medicine_name   = $medicine_name."...";
                                    }
                                    $medicine_wid     = $med_row['When'];
                                    $medicine_when    = $med_row['ShortHand'];
                                    $medicine_time    = $med_row['SpecificTime'];
                                    $fortitletime     = $med_row['SpecificTime'];
                                    $timelength       = strlen($medicine_time);
                                    if($timelength > 10){
                                      $medicine_time   = substr($medicine_time,0,10);
                                      $medicine_time   = $medicine_time."...";
                                    }
                                    echo "<div style='border-bottom:1px solid #dedede;'>".$count.". ";
                                    echo "<span title='$fortitle'>".$medicine_name."</span>";
                                    if($medicine_wid != "16"){

                                        echo "<div align='right' style='margin-top:-15px;'>".$medicine_when."</div>";
                                    } else {
                                        echo "<div align='right' style='margin-top:-15px;' title='$fortitletime'>".$medicine_time."</div>";
                                    }
                                    echo "</div>";
                                     $count++;
                                  }
                                } else {
                                  echo "No Instructions found";
                                }
                                  ?>
                                </div>
                              </div>
                             </div>                    
                      </div>
                    </div>
                  </div>
                  <?php
                }
                $get_goals    = "select distinct UserID, PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where UserID = '$id' and PlanCode = '$current_plan_code'";
                //echo $get_goals;exit;
                $get_goals_run   = mysql_query($get_goals);
                $get_goals_count = mysql_num_rows($get_goals_run);
                //echo $get_goals_count;
                if($get_goals_count > 0){
                  ?>
                  
                  <div class="panel panel-default" >
                    <div class="" role="tab" id="headingFour">
                      <h4 class="panel-title">
                        <div class="dashboardheadings"><img src="images/editad.png" style="height:20px;margin-top:10px;margin-left:10px;cursor:pointer;" align="left" id="<?php echo $id."~~".$current_plan_code."~~goal"; ?>" class="gotocustomize">
                          <span style="color:#fff;">Goals</span>
                          <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour"><img src="images/down.png" style="height:10px;width:auto;margin:5px;margin-top:15px;" align="right" id="arrow4" onclick='changeimage("4");'></a>
                        </div>
                        
                      </h4>
                    </div>
                    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                      <div style="padding:10px;">
                        <?php
                        $count = 1;
                        while ($goal_head = mysql_fetch_array($get_goals_run)) {
                          $goal2tabstext = "";
                        $tabnames = array("1" => "Medication", "2" => "Appointment", "3-2" => "Lab Test", "8" => "Instruction", "3-1" => "Self Test");
                        $goaldesc             = (empty($goal_head['GoalDescription']))           ? '-' : $goal_head['GoalDescription'];
                        $goasdisp             = (empty($goal_head['DisplayedWith']))           ? '-' : $goal_head['DisplayedWith'];
                        $tabexplode     = explode(",", $goasdisp);
                          foreach ($tabexplode as $tab) {
                            if($tab != ""){
                              $goal2tabstext .= $tabnames[$tab].", ";
                            }
                          }

                          //echo $appodate;
                          echo "<div style='border-bottom:1px solid #dedede;'>".$count.". ";
                          echo $goaldesc;
                          echo "</div>";
                           $count++;
                         }
                        ?>                       
                      </div>
                    </div>
                  </div>
                <?php
              }
            }
          }
              ?>
              </div>            
              </div>
              <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 paddingrl0">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0 graphs">
                  <div class="slider4" style="width:100%;height:300px;">
                  <?php if($medvalues!=""){
                    ?>
                     <div class="slide" style="width:100%"><strong style="font-size:18px;">Medication Complaince</strong></br>
                     <select name="medgraphs" id="medgraphs" class="dashboardselect">
                       <?php echo $medicineselect;?>
                     </select>
                     <div id="chart" style="width:100%"></div>
                     <?php 
                     foreach ($medgraph_value as $key => $value) {
                       echo "<input type='hidden' value=".$value." id='$key'>";
                     }
                     ?>
                     </div>
                    <?php
                    }?>
                    <?php if($instvalues!=""){
                    ?>
                     <div class="slide" style="width:100%"><strong style="font-size:18px;">Instruction Complaince</strong></br>
                     <select name="instgraphs" id="instgraphs" class="dashboardselect">
                       <?php echo $instructionselect;?>
                     </select>
                     <div id="chart2" style="width:100%"></div>
                     <?php 
                     foreach ($instgraph_value as $key => $value) {
                       echo "<input type='hidden' value=".$value." id='$key'>";
                     }
                     ?>
                     </div>
                    <?php
                    }?>
                    <?php 
                      $count = 0;
                      $graphvalues = "";
                      $graphdates = "";
                      $graphcount = 3;
                      $get_distinct_selftests = mysql_query("select distinct PlanCode, SelfTestID, RowNo from USER_SELF_TEST_DATA_FROM_CLIENT where UserID = '$userid' and PlanCode = '$current_plan_code'");
                        $get_selftest_count = mysql_num_rows($get_distinct_selftests);
                        if($get_selftest_count > 0){
                          while ($distinctselftests = mysql_fetch_array($get_distinct_selftests)) {
                            $plancode     = $distinctselftests['PlanCode'];
                            $selftestid   = $distinctselftests['SelfTestID'];
                            $rowno        = $distinctselftests['RowNo'];
                            $graphvalues  = "";
                              $graphdates = "";
                            $get_selftest_query = mysql_query("select SNo, ResponseDataName, ResponseDataValue, DateTime from USER_SELF_TEST_DATA_FROM_CLIENT where UserID = '$userid' and PlanCode = '$plancode' and SelfTestID = '$selftestid' and RowNo = '$rowno' and PlanCode REGEXP '^[A-Za-z]'");
                            $self_instruction = "";
                            $get_instructionid_query = mysql_query("select t2.Instruction from USER_SELF_TEST_DETAILS as t1, INSTRUCTION_MASTER as t2 where t1.UserID = '$userid' and t1.PlanCode = '$plancode' and t1.SelfTestID = '$selftestid' and t1.RowNo = '$rowno' and t1.InstructionID = t2.InstructionID");
                            $self_instruction = mysql_result($get_instructionid_query, 0);
                            //echo "select SNo, ResponseDataName, ResponseDataValue, DateTime from USER_SELF_TEST_DATA_FROM_CLIENT where UserID = '$userid' and PlanCode = '$plancode' and SelfTestID = '$selftestid' and RowNo = '$rowno'";exit;
                                while ($values = mysql_fetch_array($get_selftest_query)) {
                                  $slno           = $values['SNo'];
                                  $respdataname   = stripslashes($values['ResponseDataName']);
                                  $respdataname   = $respdataname." - ".$self_instruction;
                                  $respdatavalue  = stripslashes($values['ResponseDataValue']);
                                  $respdatadate   = date('Y-m-d-H-i',strtotime($values['DateTime']));
                                  $graphvalues    .= intval($respdatavalue).",";
                                  $graphdates     .= $respdatadate.",";
                                }
                            $count++;
                            //echo $graphcount;
                            $graphvalues = rtrim($graphvalues, ",");
                            ?>
                            <div class="slide" style="width:100%"><strong style="font-size:18px;">Self Test Values</strong></br>
                            <div id="chart<?php echo $graphcount;?>" style="width:95%"><input type="hidden" id="chartvalue<?php echo $graphcount;?>" value="<?php echo $graphvalues;?>"><input type="hidden" id="chartdates<?php echo $graphcount;?>" value="<?php echo $graphdates;?>"><input type="hidden" id="chartname<?php echo $graphcount;?>" value="<?php echo $respdataname;?>"></div></div>
                            <?php
                            $graphcount++;
                            $realgraphcount++;
                            }

                          }
                          if($realgraphcount == 0){
                            echo "<div id='grad1' align='center'><img src='images/noreports.png' style='margin-top:14%;height:70px;width:auto;'></div>";
                          }
                    ?> 
                  </div>              
                </div>
                <!--<a href="javascript:void(0);" onclick="fullScreen('http://www.appmantras.com/abc.pdf');">123444</a>
                <object data="http://www.appmantras.com/abc.pdf" type="application/pdf">
                    <embed src="http://www.appmantras.com/abc.pdf" type="application/pdf" />
                </object>-->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 paddingrl0" style="border:2px solid #004f35;border-radius:8px;">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="reportlist" style="overflow:scroll;overflow-x:hidden;">
                    <div style="padding:5px;background-color:#5c5c5c;color:#fff;border-radius:5px;height:35px;text-align:center;">
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        Report Title
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" align="center">
                        Date
                      </div>
                      <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" align="center">
                        
                      </div>
                    </div>
                      <?php 
                        $get_reports = "select substr(t2.FileName,INSTR(t2.FileName,'~_')+2) as FileName1, t2.FileName, t1.ReportCreatedOn, t2.DisplayName, t1.SourceOfUpload,  t1.ReportGivenBy, t2.CreatedDate, t2.MyFolderParentID, t2.ID from MYFOLDER_PARENT as t1, MYFOLDER_CHILD as t2 where t1.ID = t2.MyFolderParentID and t1.Status = 'A' and t2.Status = 'A' and t1.UserID = '$id'";
                        if(isset($_REQUEST['rq'])){
                          $search = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['rq'])));
                          $get_reports .= " and t2.FileName like '%$search%'";
                        }
                        $get_reports.= "  order by t1.UpdatedDate desc";
                       // echo $get_reports;exit;
                        $get_reports_run    = mysql_query($get_reports);
                        $get_reports_count  = mysql_num_rows($get_reports_run);
                        $foldername         = "";
                        $firstreport        = "";
                        $firstreportname    = "";
                        $report_count       = 0;
                        if($get_reports_count > 0){
                          ?>
                          <table class="table">
                          <?php
                          while ($reports_row = mysql_fetch_array($get_reports_run)) {
                            $report_count++;
                              $reportname   = $reports_row['FileName1'];
                              $reportname2  = $reports_row['FileName'];
                              $displayname  = stripslashes($reports_row['DisplayName']);
                              $reportname = $displayname;
                              $fortitle     = stripslashes($reports_row['DisplayName']);
                              $length       = strlen($displayname);
                              if($length > 15){
                                $displayname   = substr($displayname,0,15);
                                $displayname   = $displayname."...";
                              }
                              $parentid     = $reports_row['MyFolderParentID'];
                              $childid      = $reports_row['ID'];
                              $source       = $reports_row['SourceOfUpload'];
                              /*GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/
                              $country_id         = "";   $country_code="";   $mobile_no="";
                              $get_folder_name    = "select substr(MobileNo,1,5) as CountryCode,substr(MobileNo,6) as MobileNo from USER_ACCESS where UserID='$id'";
                              $get_folder_name_qry= mysql_query($get_folder_name);
                              $get_count          = mysql_num_rows($get_folder_name_qry);
                              if($get_count == 1)
                              {
                                  $row            = mysql_fetch_array($get_folder_name_qry);
                                  $country_code   = $row['CountryCode'];
                                      if($country_code)
                                      {
                                        //echo "select substr(CountryID,1,2) from COUNTRY_DETAILS where CountryCode='$country_code'";
                                          $country_id  = mysql_result(mysql_query("select substr(CountryID,1,2) from COUNTRY_DETAILS where CountryCode='$country_code'"), 0);
                                      }
                                  $mobile_no      = $row['MobileNo'];
                              //echo $country_id.$mobile_no;
                              }
                              /*END OF GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/
                              $reportpath   = "uploads/folder/$country_id$mobile_no/".$reportname2;
                              //$reportpath  = "uploads/folder/".$id."/";

                              if($report_count == 1){
                                $firstreportname = $displayname;
                                $firstreport = $reportpath;
                              
                              $date       = date("jS M Y",strtotime($reports_row['ReportCreatedOn']));
                              if($source == "D"){
                                echo "<tr class='reportnamestr selected'><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;'>$report_count</td><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;' title='$fortitle'>$displayname</td><td style='width:120px;cursor:pointer;' class='reportnames' id='$reportpath~~$reportname'>$date</td><td title='Delete this report' id='$childid' class='deletereports'>X</td></tr>";
                              } else {
                                echo "<tr class='reportnamestr selected'><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;'>$report_count</td><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;' title='$fortitle'>$displayname</td><td style='width:120px;cursor:pointer;' class='reportnames' id='$reportpath~~$reportname'>$date</td><td></td></tr>";
                              }
                            } else {
                              $date       = date("jS M Y",strtotime($reports_row['ReportCreatedOn']));
                              if($source == "D"){
                                echo "<tr class='reportnamestr'><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;'>$report_count</td><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;' title='$fortitle'>$displayname</td><td style='width:120px;cursor:pointer;' class='reportnames' id='$reportpath~~$reportname'>$date</td><td title='Delete this report' id='$childid' class='deletereports'>X</td></tr>";
                              } else {
                                echo "<tr class='reportnamestr'><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;'>$report_count</td><td class='reportnames' id='$reportpath~~$reportname' style='cursor:pointer;' title='$fortitle'>$displayname</td><td style='width:120px;cursor:pointer;' class='reportnames' id='$reportpath~~$reportname'>$date</td><td></td></tr>";
                              }
                            }
                          }
                          ?>
                          </table>
                          <?php
                        } else {
                          echo "<div align='center' style='font-size:16px;'>No Records</div>";
                        }
                      ?>                     
                    </div>
                    <?php if((!isset($_REQUEST['rq']))||($_REQUEST['rq'] == "")){
                      ?>
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
                        <div style="height:45px;width:100%;bottom:0;background-color:#004f35;border-radius:15px;">
                            <div>
                            <div  class="col-lg-11 col-md-11 col-sm-11 col-xs-11 paddingrl0" align="center">
                            <?php 
                            $search = "";
                            if(isset($_REQUEST['rq'])){
                            $search = $_REQUEST['rq'];
                          }
                            ?>
                              <input type="text" placeholder="Search By Report Name" name="search_report" id="search_report" class="forminputs2" onkeypress='keychk(event)' value="<?php echo $search;?>">
                            </div>
                               <div class="image-upload col-lg-1 col-md-1 col-sm-1 col-xs-1 paddingrl0" align="right" style="background-color:#004f35;height:45px;padding-top:12px;text-align:center;" >
                              <form name="frm_upload_report" id="frm_upload_report" action="client_dashboard.php?id=<?php echo $id;?>" method="POST"  enctype="multipart/form-data">
                                   <label for="report_upload">
                                      <img src="images/upload.png" style="width:20px;height:auto;cursor:pointer" title="Upload Report" />
                                    </label>
                                    <input id="report_upload" name="report_upload" type="file" accept="application/pdf, image/*"/>
                                    <input type="hidden" name="hidden_value_upload" value="1" id="hidden_value_upload">
                                </form>
                              </div>
                            </div>
                        </div>
                      </div>                      
                      <?php
                      } else {
                        ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
                          <div style="height:45px;width:100%;bottom:0;background-color:#004f35;border-radius:15px;">
                              <div>
                              <div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" align="center" style="cursor:pointer;font-size:18px;color:#000;line-height:45px;">
                                <a href="client_dashboard.php?id=<?php echo $id;?>" style="color:#fff;"><u>Back To All Reports</u></a>
                              </div>
<!--                                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 paddingrl0" align="right" style="background-color:#004f35;height:50px;padding-top:12px;text-align:center;cursor:pointer;" >
                                  <img src="images/findw.png" style="width:20px;height:auto;" id="showsearchbar">
                                </div> -->
                              </div>
                          </div>
                        </div>                        
                        <?php
                        }?>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 paddingrl0" id="reportpreview">
                  <?php if($firstreport != ""){?>
                    <div style="padding:5px;background-color:#5c5c5c;color:#fff;border-radius:5px;height:35px;"><?php echo "$firstreportname";?><span><img src="images/cdfullscreen.png" align="right" class='reportfullscreen' id="<?php echo $firstreport;?>" style="float:right;"><span style="background-color:#fff;color:#004f35;padding:2px;border-radius:5px;float:right;margin-right:5px;cursor:pointer;" id="<?php echo $firstreport;?>"><img src="images/email.png" style="width:25px;height:auto;padding-right:5px;">Email This Report</span></span></div>
                       <object data="<?php echo $firstreport;?>" id="pdfobject" style="width:100%;height:325px;">
                          <embed src="<?php echo $firstreport;?>"  id="pdfembed" style="width:100%;">
                      </object> 
                      <?php } else {
                        echo "<div id='grad1' align='center' style='margin:5px;width:96%;'><img src='images/noreports.png' style='margin-top:30%;height:70px;margin-bottom:30%;width:auto;'></div>";
                        }?>                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>    
  </div><!-- planpiper_wrapper ends -->
          <div class="printable" style="display:none;">
      <?php echo $display_print;?>
    </div>
           <!--SHOW PLAN DETAILS MODAL WINDOW-->
            <div class="modal" id="userdetailsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" style="width:900px;">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h5 class="modal-title" id="modalheadings">Review & Visit Notes</h5>
                    <div class="modal-body">
                    <div style="padding-bottom:150px;font-size:17px;color:#000;padding-top:20px;">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="right">
                        Planpiper Email ID :
                      </div>
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" align="left" style="  font-family: none;">
                        <?php echo $planpiper_email;?>
                      </div> 
                    </div>
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="right">
                        Address :
                      </div>
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" align="left">
                        <?php echo $address;?>
                      </div>  
                      </div>
                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                    
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="right">
                        Disease :
                      </div>
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" align="left">
                        <?php echo $disease_name;?>
                      </div> 
                      </div>  
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                    
                      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="right">
                        Medical Record :
                      </div>
                      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" align="left">
                        <?php echo $patientsummary;?>
                      </div> 
                      </div>                     
                    </div>
                    <div class="col-xs-12">
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
                        <th>#</th>
                        <th>Date</th>
                        <th>Review</th>
                      </tr>
                      <?php 
                        for($r=0;$r<$patient_review_flag;$r++){
                          $pvrc = $r+1;
                          echo "<tr><td>$pvrc</td><td>$CreatedDate3[$r]</td><td>$Review[$r]</td></tr>";
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
                          echo "<tr><td>$pvdc</td><td>$CreatedDate[$i]</td><td>$Height[$i]</td><td>$Weight[$i]</td><td>$BloodPressure[$i]</td><td>$Temperature[$i]</td></tr>";
                        }
                      ?>
                    </table>
                    <?php
                  }
                  ?>
                    <div class="form-group col-xs-12" style="margin-bottom:0px;">
                    <label for="inputEmail3" class="col-sm-4 control-label">Height</label>
                    <div class="col-sm-8" style="margin-bottom: 5px;">
                    <div class="input-group">
                      <input type="text" class="form-control" id="patientheight" placeholder="Height" maxlength="10" name="patientheight">
                      <div class="input-group-addon" style="width:100px;">Cm</div>
                     </div>
                    </div>
                  </div>
                  <div class="form-group col-xs-12" style="margin-bottom:0px;">
                    <label for="inputEmail3" class="col-sm-4 control-label">Weight</label>
                    <div class="col-sm-8" style="margin-bottom: 5px;">
                    <div class="input-group">
                      <input type="text" class="form-control" id="patientweight" placeholder="Weight" name="patientweight" maxlength="10">
                      <div class="input-group-addon" style="width:100px;">Kg</div>
                     </div>
                    </div>
                  </div>
                  <div class="form-group col-xs-12" style="margin-bottom:1px;">
                    <label for="inputEmail3" class="col-sm-4 control-label">Blood Pressure</label>
                    <div class="col-sm-8" style="margin-bottom: 5px;">
                    <div class="input-group">
                      <input type="text" class="form-control" id="patientpressure"  name="patientpressure" placeholder="Blood Pressure" maxlength="20">
                      <div class="input-group-addon"  style="width:100px;">mmHg</div>
                     </div>
                    </div>
                  </div>
                  <div class="form-group col-xs-12" style="margin-bottom:0px;">
                    <label for="inputEmail3" class="col-sm-4 control-label">Temperature</label>
                    <div class="col-sm-8" style="margin-bottom: 5px;">
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
                        <th>#</th>
                        <th>Date</th>
                        <th>Notes</th>
                      </tr>
                      <?php 
                        for($i=0;$i<$patient_notes_flag;$i++){
                          $pvnc = $i+1;
                          echo "<tr><td>$pvnc</td><td>$CreatedDate2[$i]</td><td>$Notes[$i]</td></tr>";
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
                  <input type="hidden" name="plancode_for_current_plan" id="plancode_for_current_plan" value="<?php echo $pc;?>">
                  <input type="hidden" name="userid_for_current_plan" id="userid_for_current_plan" value="<?php echo $id;?>">
                  <input type="hidden" name="logged_merchantid" id="logged_merchantid" value="<?php echo $logged_merchantid;?>">
                  <input type="hidden" name="current_date" id="current_date" value="<?php echo date('d-M-Y h:i:s');?>">
                  <button class="smallbutton" id="patientdetailsentered">Done</button>
                  <button class="smallbutton" id="print_button">Print</button>
                  </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
    <!--END OF PLAN DETAILS MODAL WINDOW-->    
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/magicsuggest.js"></script>
    <script type="text/javascript" src="js/jquery.bxslider.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/modernizr.js"></script>
    <script type="text/javascript" src="js/jquery.print.js"></script>
    <script type="text/javascript" src="js/placeholders.min.js"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/c3.js"></script>
    <script type="text/javascript" src="js/d3.v3.min.js" charset="utf-8"></script>
  <script type="text/javascript">
  $(document).ready(function() {
        var w = window.innerWidth;
        var h = window.innerHeight;
        var total = h - 200;
        var each = total/12;
        $('.navbar_li').height(each);
        $('.navbar_href').height(each/2);
        $('.navbar_href').css('padding-top', each/2.8);
        
        var currentpage = "dashboard";
        $('#'+currentpage).addClass('active');
        var username = "<?php echo $firstname." ".$lastname." - Dashboard";?>";
        $('#plapiper_pagename').html("");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
        // alert(available_height);
        available_height = available_height-$('.graphs').height() + 40 - 10 - 60;
        //alert(available_height);
        $('#reportlist').height(available_height + 20);
        var activitylist = h-150-$('.smallplanbox').height();
        $('.activitylist').height(activitylist);
        $('.smallplanbox').hover(function(){
          $('.onhoveroptions').hide();
          $('.onhoveroptions', $(this)).show(); 
        });
        $('#viewmoredetails').click(function(){
        $('#userdetailsmodal').modal('show');
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
                  $('#userdetailsmodal').modal('hide');
                } else {
                  $('#userdetailsmodal').modal('hide');
                }
              },
            error: function(error)
            {

            }
          });
        
      });
        $('#magicsuggest').magicSuggest({
            allowDuplicates: false,
            allowFreeEntries: false,
            name: 'magicsuggest',
            cls: 'custom',
            data: 'ajax_get_clients.php',
            placeholder : 'Search for a patient',
            maxSelection : 1,
            ajaxConfig: {
                xhrFields: {
                withCredentials: true,
                }
            }
        });
        $('.deletereports').click(function(){
          var childid = $(this).attr("id");
          //alert(childid);
          var dltconfirm = confirm("This report will be deleted. Click OK to continue");
          if(dltconfirm == true){
            //alert(1);
            var userid          = '<?php echo $id;?>';
            var report_given_by = '<?php echo $report_given_by;?>';
            var logged_companyname = '<?php echo $logged_companyname;?>';
            //alert(userid);
            window.location.href = "ajax_validation.php?type=delete_report&userid="+userid+"&child="+childid+"&report_given_by="+report_given_by+"&logged_companyname="+logged_companyname;
          } else {
            //alert(0);
          }
        });
        $('#medgraphs').on('change', function(){
          var medicinename = $('#medgraphs').val();
          var value = $('#'+medicinename).val();
          //alert(value);
          var arr = value.split('~~');
              var chart = c3.generate({
              bindto: '#chart',
              data: {
                      // iris data from R
                      columns: [
                          ['NOT TAKEN', arr[0]],['TAKEN', arr[1]]
                      ],
                      type : 'pie',
                  }
            });
        });
         $('#instgraphs').on('change', function(){
          var instructioname = $('#instgraphs').val();
          var value = $('#'+instructioname).val();
          //alert(value);
          var arr = value.split('~~');
              var chart = c3.generate({
              bindto: '#chart2',
              data: {
                      // iris data from R
                      columns: [
                          ['NOT DONE', arr[0]],['DONE', arr[1]]
                      ],
                      type : 'pie',
                  }
            });
        });
        $('#searchbutton').click(function(){
          if(!$('div.ms-sel-item').length){
            alert("Please select a patient to continue");  
            return false;
          }
          $('#frm_search_user').submit();
          //window.location.href = "selectuser.php?query="+searchuser;
        });
        $('.gotocustomize').click(function(){
          var id = $(this).attr("id");
          //alert(id);
          var idarray = id.split('~~');
          //alert(idarray[0]);
          var userid    = idarray[0];
          var plancode  = idarray[1];
          var page      = idarray[2];
          window.location.href = "ajax_validation.php?type=edit_assigned_plan_from_dashboard&userid="+userid+"&plancode="+plancode+"&page="+page;
        });
        $('.reportnames').click(function(){
          var id = $(this).attr("id");
          var pdfarray = id.split('~~');
          //alert(idarray[0]);
          var path    = pdfarray[0];
          //alert(path);
          $('.reportnamestr').removeClass('selected');
          $(this).parents("tr").addClass("selected");
          var name    = pdfarray[1];
          var filelength = name.length;
          var shortname =  name;
          if(filelength > 20){
             shortname = name.substring(0, 20);
             shortname = shortname+"...";
          }
         
          $('#reportpreview').html("<div style='padding:5px;background-color:#5c5c5c;color:#fff;border-radius:5px;height:35px;'>"+shortname+"<span><img src='images/cdfullscreen.png' align='right' id='"+path+"' class='reportfullscreen' style='float:right;'><span style='background-color:#fff;color:#004f35;padding:2px;border-radius:5px;float:right;margin-right:5px;cursor:pointer;' id='"+path+"'><img src='images/email.png' style= 'width:25px;height:auto;padding-right:5px;'>Email This Report</span></span></div><object data='"+path+"' id='pdfobject' style='width:100%;height:"+available_height+"px;'><embed src='"+path+"'  id='pdfembed' style='width:100%;'></object> ");
        });
        $(document).on('click', '.reportfullscreen', function () {
          var id = $(this).attr("id");
          //alert(id);
          window.open(id, '', 'fullscreen=yes, scrollbars=auto');
        });
          var current_plan_code = '<?php echo $current_plan_code;?>';
         // alert(current_plan_code);
          var userid = '<?php echo $id;?>';
         //alert(userid);
              var dataString  = "userid="+userid+"&type=get_selftest_values";
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

              });
            },
            error: function(){

            }
         });
          var chart = c3.generate({
          bindto: '#chart',
          data: {
                  // iris data from R
                  columns: [
                      <?php echo reset($medgraph_values);?>
                  ],
                  type : 'pie',
              }
        });
          var chart = c3.generate({
          bindto: '#chart2',
          data: {
                  // iris data from R
                  columns: [
                      <?php echo reset($instgraph_values);?>
                  ],
                  type : 'pie',
              }
        });
          var realgraphcount = <?php echo $graphcount;?>;
          for (i = 3; i < realgraphcount; i++) { 
            var name      = $('#chartname'+i).val();
            var value     = $('#chartvalue'+i).val();
            var dates     = $('#chartdates'+i).val();
            //alert(dates);
            var darr      = dates.split(',');
            var graphdate = [];
            var arr       = value.split(',');
            var graphvalue = [];
            var datavalue = "";
            for (j = 0; j < arr.length; j++) {
                 if(arr[j] != ""){
                 graphvalue.push(parseInt(arr[j]));
                 }
              }
            for (k = 0; k < darr.length; k++) {
                 if(darr[k] != ""){
                 graphdate.push(darr[k].toString());
                 }
              }
              var obj = { 
               "data1": graphvalue,
               "data2": graphdate
                }
              var chart = c3.generate({
                  bindto: '#chart'+i,
                  data: {
                    x : 'date',
                    xFormat: '%Y-%m-%d-%H-%M', // 'xFormat' can be used as custom format of 'x'
                    columns: [
                      ['date'].concat(obj.data2),
                      [name].concat(obj.data1)
                    ]
                  },
             grid: {
              x: {
                  show: true
              },
              y: {
                  show: true
              }
            } ,
            axis: {
              x: {
                type: 'timeseries',
                label: {
                    text: 'Date',
                    position: 'outer-center'
                    },
                    tick: {
                      fit: true,
                      //format: '%Y-%m-%d %H:%M',
                      format: '%d-%b',
                       //culling:true
                      //  rotate: -45,
                       //  centered: true
                //        culling: {
                //     max: 4 // the number of tick texts will be adjusted to less than this value
                // }
                rotate: -45,
                multiline: false
                    },
                    height: 40
              },
              y: {
                label: {
                    text: name,
                    position: 'outer-middle'
                  }
              }
            },
            tooltip: {
                      format: {
                        title: function (d) { 
                          var format = d3.time.format("%d-%m-%Y, %H:%M:%S");
                          return format(d); },
                        value: function (value, ratio, id) {
                            return value;
                        }
                    }
              },
              zoom: {
                  enabled: true
              }
              });
            }
          $('.slider4').bxSlider({
            minSlides: 1,
            maxSlides: 3,
            moveSlides: 1,
            slideMargin: 1,
            infiniteLoop: false,
            hideControlOnEnd: true
          });
        var fileTypes = ['jpg', 'jpeg', 'png', 'pdf'];  //acceptable file types
        function readURL(input) {
            if (input.files && input.files[0]) {
                var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
                isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
                if (isSuccess) {
                var reader = new FileReader();        
                reader.onload = function (e) {
                    $('#frm_upload_report').submit(); //To upload to server directly
                }          
                reader.readAsDataURL(input.files[0]);
            } else {
                alert("Report should be in pdf,jpg, png or jpeg format.");
                return false;
            }
        }
        }
       $("#report_upload").change(function(){
            readURL(this);
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