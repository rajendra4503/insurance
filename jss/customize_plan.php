<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>";print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php'); 
$plan_to_customize = $_SESSION['plancode_for_current_plan'];
$assigned_to_user  = $_SESSION['userid_for_current_plan'];

//GET USER DETAILS
$user_id = $_SESSION['view_active_plans_userid'];
$get_details = "select t1.FirstName, t1.LastName, t2.EmailID, t2.MobileNo from USER_DETAILS as t1, USER_ACCESS as t2 where 
t1.UserID = t2.UserID and t1.UserID = '$user_id'";
$get_details_query = mysql_query($get_details);
$details_num_rows = mysql_num_rows($get_details_query);
if($details_num_rows > 0){
  while ($details = mysql_fetch_array($get_details_query)) {
    $firstname    = $details['FirstName'];
    $lastname     = $details['LastName'];
    $fullname     = $firstname." ".$lastname;
    $emailid      = $details['EmailID'];
    $mobileno     = $details['MobileNo'];
  }
}

$page_back_from_customize_page = "assignedplans.php";
if(isset($_SESSION['page_back_from_customize_page'])){
$page_back_from_customize_page = $_SESSION['page_back_from_customize_page'];
} else {
  $page_back_from_customize_page = "assignedplans.php";
}
if(isset($_REQUEST['hidden_value'])){
    //echo $_REQUEST['hidden_value'];exit;
    //echo "<pre>";print_r($_FILES);exit;



    if(!empty($_FILES)) // [START FILE UPLOADED]
{//echo 123;exit;
//include 'config.php';
/* 
1 = Check if the file uploaded is actually an image no matter what extension it has
2 = The uploaded files must have a specific image extension
*/

$validation_type = 1;

if($validation_type == 1)
{
   $mime = array('image/gif' => 'gif','image/jpeg' => 'jpeg','image/png' => 'png','image/jpg' => 'jpg');
}
// Check for a correct extension. The image file hasn't an extension? Add one

   if($validation_type == 1)
   {
    //echo 123;exit;
   $file_info = getimagesize($_FILES['cover_image']['tmp_name']);

      if(empty($file_info)) // No Image?
      {//echo "INVALID";exit;
      //$error .= "The uploaded file doesn't seem to be an image.";
      ?>
      <script type="text/javascript">
            alert('Sorry, we only accept valid PNG,JPG and JPEG images');
            window.history.back();

            //$("#imageerror").fadeIn();
            //$("#imageerror").text("Sorry, we only accept valid PNG,JPG and JPEG images'");
            //$("#imageerror").fadeOut(1000);

        </script>
      <?php
      }
      else // An Image?
      {//echo "VALID";exit;
        $cover_image        = (empty($_FILES['cover_image']['name'])) ? ''    : $_FILES['cover_image']['name'];
        $path               = "uploads/planheader/";
        if(!is_dir($path)){
            mkdir($path);
          }
          if($cover_image){
              $no            = rand();
              $imgtype       = explode('.', $cover_image);
              $ext           = end($imgtype);
              $fullfilename  = $no . '.' . $ext;
              $fullpath      = $path . $no . '.' . $ext;
              move_uploaded_file($_FILES['cover_image']['tmp_name'], $fullpath);
          }
      $update_plan_image  = "update USER_PLAN_HEADER set PlanCoverImagePath = '$fullfilename' where PlanCode='$plan_to_customize' and UserID = '$assigned_to_user'";
      //echo $update_profile_dp;exit;
        $update_qry     = mysql_query($update_plan_image);
          header("Location:customize_plan.php");
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
		<title>Plan Piper - Customize Plan</title>
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
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
		 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
		 	<div id="plantitle"><span style="padding-left:0px;">Plans assigned to <?php echo $fullname;?></span></div>
			</div>
		 	<section>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="mainplanlistdiv">
		 			<?php 
          $display_print = "<div id='printtable' style='border:1px solid #004F35;width:100%;background-color: #004F35;color:#fff;height:40px;text-align:center;line-height:40px;font-size:26px;    font-family:RalewayBold;'>PATIENT INFORMATION</div>";
          $get_profile_details1 = "select t1.FirstName, t1.LastName, t1.Gender, t1.DOB, t1.BloodGroup, t1.CountryCode, t1.StateID, t1.CityID, t2.MobileNo, t2.EmailID, t1.AddressLine1, t1.AddressLine2, t1.PinCode, t1.AreaCode, t1.Landline, t1.MobilePhoneType, t1.LanguageID,t1.SupportPersonName,t1.SupportPersonMobileNo from USER_DETAILS as t1, USER_ACCESS as t2 where t1.UserID = t2.UserID and t1.UserID = '$assigned_to_user'";
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

		 				$get_plan_details = "select t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from USER_PLAN_HEADER as t1, CATEGORY_MASTER as t2 where t1.PlanCode = '$plan_to_customize' and t1.CategoryID = t2.CategoryID and t1.UserID = '$assigned_to_user'";
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
								 				?>
                  <form name="frm_upload_image" id="frm_upload_image" method="POST" enctype="multipart/form-data">
								 	<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:2%;margin-bottom:2%;">
									<div class="bigplanbox">
										<img src="<?php echo $plandet_path;?>" class="planboximg">
										<div class="blackoverlay"></div>
										<div class="planboxname"><?php echo $plandet_name; ?></div>
										<div class="planboxcatg"><?php echo $plandet_catg; ?></div>
										<div class="planboxdesc"><?php echo $plandet_desc;?></div>
										<div class="planboxedit">
                                            <div class="image-upload">
                                            <label for="cover_image">
                                                <img src="images/edit2.png" style="height:40px;width:auto;cursor:pointer;" title="Upload Cover Image" />
                                            </label>
                                            <input id="cover_image" name="cover_image" type="file" multiple accept='image/*'/>
                                            <input type="hidden" name="hidden_value" id="hidden_value" value="1">
                                        </div>
                                        </div>
									</div>
								</div>
                                </form>
								 <?php 
                                    }
								 		} else {
								 			?>
								 			<script type="text/javascript">
												
											</script>
											<?php
								 		}

				//GET MEDICATION DETAILS
                $get_medication = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.SpecificTime,t2.RowNo,t3.ShortHand,t2.Instruction,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString
                                    from USER_MEDICATION_HEADER as t1,USER_MEDICATION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plan_to_customize' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t1.UserID = '$assigned_to_user'";
                //echo $get_medication;exit;
                $get_medication_qry     = mysql_query($get_medication);
                $get_medication_count   = mysql_num_rows($get_medication_qry);
                if($get_medication_count)
                {
                  $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>MEDICATION</div>";
                	?>
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
		 	<div id="pageheading">MEDICATION<a href="cust_med_new.php"><span title="Click to edit plan medication" align="right" style="position:absolute;top:0px;right:5px;color:#004535;cursor:pointer;"><u>Edit</u></span></a></div>
			</div>
			 <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <table class="table table-bordered">
              <tr class="tableheadings">
                <th>Medicine Name</th>
                <th>Shorthand</th>
                <th>Instruction</th>
                <th>Frequency</th>
              </tr>
                	<?php
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

                   		?>
                   		<tr class="tablecontents">
			                <td><?php echo $medname;?></td>
			                <td><?php echo $medshort;?></td>
			                <td><?php echo $medinstr;?></td>
			                <td><?php echo $medfreq;?></td>
			              </tr>
                   		<?php

                    }
                    $display_print .= "</table>";
                    ?>
                    </table>
						</div>
                    <?php
                }
                //END OF GET MEDICATION DETAILS
                //GET APPOINTMENT DETAILS
                    $get_appointment= "select distinct t2.AppointmentDate,t2.AppointmentTime,t2.AppointmentShortName,t2.DoctorsName,t2.AppointmentRequirements
                                      from USER_APPOINTMENT_HEADER as t1,USER_APPOINTMENT_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.PlanCode='$plan_to_customize' and t1.UserID = t2.UserID and t1.UserID = '$assigned_to_user'";
                    //echo $get_appointment.'<br>';
                    $get_appointment_qry    = mysql_query($get_appointment);
                    $get_appointment_count  = mysql_num_rows($get_appointment_qry);
                    if($get_appointment_count)
                    {
                      $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>APPOINTMENTS</div>";
                    	?>
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
				 	<div id="pageheading">APPOINTMENTS <a href="cust_appo_new.php"><span title="Click to edit plan appointments" align="right" style="position:absolute;top:0px;right:5px;color:#004535;cursor:pointer;"><u>Edit</u></span></a></div>
					</div>
					 <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
		              <table class="table table-bordered">
		              <tr class="tableheadings">
		                <th>Appointment Name</th>
		                <th>Date</th>
		                <th>Time</th>
		                <th>Requirements</th>
		              </tr>
		                	<?php
                       $display_print .= "<table style='width:100%;border-collapse:collapse;border:1px solid #D3D3D3;'>";
                        while($appointment_rows = mysql_fetch_array($get_appointment_qry))
                        {
                            $appodate       = (empty($appointment_rows['AppointmentDate']))         ? '' : date('d-M-Y',strtotime($appointment_rows['AppointmentDate']));
                            $appotime       = (empty($appointment_rows['AppointmentTime']))         ? '' : date('h:i A',strtotime($appointment_rows['AppointmentTime']));
                            $apponame   	= (empty($appointment_rows['AppointmentShortName']))    ? '' : $appointment_rows['AppointmentShortName'];
                            $appodoc        = (empty($appointment_rows['DoctorsName']))             ? '' : $appointment_rows['DoctorsName'];
                            $apporeq 		= (empty($appointment_rows['AppointmentRequirements'])) ? '' : $appointment_rows['AppointmentRequirements']; 
                            $display_print .= "<tr><td style='width:24%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$apponame</td><td style='width:17%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$appodate</td><td style='width:15%;padding-left:5px;border:1px solid #D3D3D3;height:35px;'>$appotime</td><td style='width:40%;padding-left:5px;border:1px solid #D3D3D3;'>$apporeq</td></tr>";
                            ?>
                   		<tr class="tablecontents">
			                <td><?php echo $apponame;?></td>
			                <td><?php echo $appodate;?></td>
			                <td><?php echo $appotime;?></td>
			                <td><?php echo $apporeq;?></td>
			              </tr>
                   		<?php
                         }
                         $display_print .= "</table>";
                         ?>
                         </table>
						</div>
                         <?php
                    }
                    //END OF APPOINTMENT DETAILS
                    //SELF TEST DETAILS
                    $get_self_test = "select distinct t1.SelfTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.TestDescription,t2.InstructionID,
                                      t2.Frequency,t2.HowLong,t2.HowLongType,t2.ResponseRequired,t2.StartFlag,t2.NoOfDaysAfterPlanStarts,
                                      t2.SpecificDate,t2.FrequencyString
                                      from USER_SELF_TEST_HEADER as t1,USER_SELF_TEST_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.SelfTestID=t2.SelfTestID and t1.PlanCode='$plan_to_customize' and t1.UserID = t2.UserID and t1.UserID = '$assigned_to_user'";
                    //echo $get_self_test;exit;
                    $get_self_test_qry  = mysql_query($get_self_test);
                    $get_self_test_count= mysql_num_rows($get_self_test_qry);
                    if($get_self_test_count)
                    {
                      $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>SELF TEST</div>";

                    	?>
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
				 	<div id="pageheading">SELF TEST <a href="cust_self_new.php"><span title="Click to edit plan self tests" align="right" style="position:absolute;top:0px;right:5px;color:#004535;cursor:pointer;"><u>Edit</u></span></a></div>
					</div>
					 <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
		              <table class="table table-bordered">
		              <tr class="tableheadings">
		                <th>Self Test Name</th>
		                <th>Frequency</th>
		              </tr>
		                	<?php
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


                            ?>
                   		<tr class="tablecontents">
			                <td><?php echo $selftestname;?></td>
			                <td><?php echo $selftestfreq;?></td>
			              </tr>
                   		<?php
                        }   
                        $display_print .= "</table>";
                        ?>
                        </table>
						</div>
                        <?php
                    }
                    //END OF SELF TEST DETAILS
                    //GET LAB TEST DETAILS
                    $get_lab_test = "select distinct t1.LabTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.LabTestRequirements,t2.LabTestDate,t2.LabTestTime from USER_LAB_TEST_HEADER1 as t1,USER_LAB_TEST_DETAILS1 as t2
                                      where t1.PlanCode=t2.PlanCode and t1.LabTestID=t2.LabTestID and t1.PlanCode='$plan_to_customize' and t1.UserID = t2.UserID and t1.UserID = '$assigned_to_user' and t2.LabTestDate is null";
                    $get_lab_test_qry  = mysql_query($get_lab_test);
                    $get_lab_test_count= mysql_num_rows($get_lab_test_qry);
                    if($get_lab_test_count)
                    {
                      $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>LAB TESTS</div>";
                   ?>
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
				 	<div id="pageheading">LAB TEST <a href="cust_lab_new.php"><span title="Click to edit plan lab tests" align="right" style="position:absolute;top:0px;right:5px;color:#004535;cursor:pointer;"><u>Edit</u></span></a></div>
					</div>
					 <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
		              <table class="table table-bordered">
		              <tr class="tableheadings">
		                <th>Lab Test Name</th>
		                <th>Doctors Name</th>
		                <th>Requirements</th>
		              </tr>
		                	<?php
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
                            ?>
                   		<tr class="tablecontents">
			                <td><?php echo $labtestname;?></td>
			                <td><?php echo $labtestdocname;?></td>
			                <td><?php echo $labtestreq;?></td>
			              </tr>
                   		<?php
                        }   
                        $display_print .= "</table>";
                        ?>
                        </table>
						</div>
                        <?php
                    }
                    //END OF LAB TEST DETAILS
                    //GET INSTRUCTION DETAILS
                $get_instruction = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.SpecificTime,t2.RowNo,t3.ShortHand,t2.Instruction,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString
                                    from USER_INSTRUCTION_HEADER as t1,USER_INSTRUCTION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plan_to_customize' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t1.UserID = '$assigned_to_user'";
                //echo $get_instruction;exit;
                $get_instruction_qry     = mysql_query($get_instruction);
                $get_instruction_count   = mysql_num_rows($get_instruction_qry);
                if($get_instruction_count)
                {
                  $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>INSTRUCTIONS</div>";
                    ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
            <div id="pageheading">INSTRUCTION <a href="cust_inst_new.php"><span title="Click to edit plan instruction" align="right" style="position:absolute;top:0px;right:5px;color:#004535;cursor:pointer;"><u>Edit</u></span></a></div>
            </div>
             <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <table class="table table-bordered">
              <tr class="tableheadings">
                <th>Instruction</th>
                <th>Shorthand</th>
                <th>Instruction</th>
                <th>Frequency</th>
              </tr>
                    <?php
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

                        ?>
                        <tr class="tablecontents">
                            <td><?php echo $medname;?></td>
                            <td><?php echo $medshort;?></td>
                            <td><?php echo $medinstr;?></td>
                            <td><?php echo $medfreq;?></td>
                          </tr>
                        <?php
                        
                    }
                    $display_print .= "</table>";
                    ?>
                    </table>
                        </div>
                    <?php
                }
                //END OF GET INSTRUCTION DETAILS
                //GET GOAL DETAILS
                $get_goals = "select distinct UserID, PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where UserID = '$assigned_to_user' and PlanCode = '$plan_to_customize'";
                //echo $get_goals;exit;
                $get_goals_qry     = mysql_query($get_goals);
                $get_goals_count   = mysql_num_rows($get_goals_qry);
                $tabnames = array("1" => "Medication", "2" => "Appointment", "3-2" => "Lab Test", "8" => "Instruction", "3-1" => "Self Test");
                if($get_goals_count)
                {
                  $display_print .= "<div style='border-bottom:1px solid #004F35;width:100%;background-color: #fff;color:#004F35;height:35px;text-align:center;line-height:35px;font-size:20px;font-family:RalewayBold;'>GOALS</div>";
                    ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
            <div id="pageheading">GOALS <a href="cust_goals.php"><span title="Click to edit plan instruction" align="right" style="position:absolute;top:0px;right:5px;color:#004535;cursor:pointer;"><u>Edit</u></span></a></div>
            </div>
             <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <table class="table table-bordered">
              <tr class="tableheadings">
                <th>Goal</th>
                <th>Displayed With</th>
              </tr>
                    <?php
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
                          //$goal2tabstext = rtrim($goal2tabstext, ",");
                        ?>
                        <tr class="tablecontents">
                            <td><?php echo $goaldesc;?></td>
                            <td><?php echo $goal2tabstext;?></td>
                          </tr>
                        <?php

                    }
                    $display_print .= "</table>";
                    ?>
                    </table>
                        </div>
                    <?php
                }


        //GET PATIENT DATAS
        $patient_data_flag = 0;
        $get_patient_data   = "select ID, UserID, MerchantID, Height, Weight, BloodPressure, Temperature, CreatedDate from VISIT_DATA where UserID='$assigned_to_user' and MerchantID = '$logged_merchantid'";
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
        $get_patient_notes    = "select ID, UserID, MerchantID, Notes, CreatedDate from VISIT_NOTES where UserID='$assigned_to_user' and MerchantID = '$logged_merchantid'";
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
        //Get Patient History
        $UserHistory = "";
        $get_patient_history    = "select UserHistory from USER_DETAILS where UserID='$assigned_to_user'";
        //echo $get_patient_history;exit;
        $patient_history_run  = mysql_query($get_patient_history);
        $patient_history_count  = mysql_num_rows($patient_history_run);
        if($patient_history_count > 0){
          while ($patient_hist = mysql_fetch_array($patient_history_run)) {
            $UserHistory      = $patient_hist['UserHistory'];
          }
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



                //END OF GET GOAL DETAILS
		 			?>
		 		</div>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="addClientActionBar" align="center">
			        <a href="<?php echo $page_back_from_customize_page;?>"><button id="registerbutton" class="formbuttonsmall">DONE</button></a>
              <button id="notificationbutton" class="formbuttonsmall" value="notification" style="display:none">SEND NOTIFICATION</button>
              <button id="printbutton" class="formbuttonsmall">PRINT</button>
              <img src="images/loader.gif" style="display:none;" id="loader">
				</div>
		 	</section>
		 </div>
		 </div>
     <div class="print_content" style="display:none; width:100%;margin:0;">
       <?php echo $display_print;?>
     </div>
		 </div>
     
        <?php
        $check_last_updated_time    = "select UserStartOrUpdateDateTime,PlanUpdatedDate from USER_PLAN_HEADER where PlanUpdatedDate > UserStartOrUpdateDateTime
                                        and UserID='$assigned_to_user' and PlanCode='$plan_to_customize' and Synchronised='Y'";
        //echo $check_last_updated_time;exit;
        $check_query                = mysql_query($check_last_updated_time);                             
        $check_count                = mysql_num_rows($check_query);
        ?>
         
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/magicsuggest.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/jquery.print.js"></script>
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
			var currentpage = "assign";
	    	$('#'+currentpage).addClass('active');
	    	var windowheight = h;
       		var available_height = h - 150;
        	$('#mainplanlistdiv').height(available_height);

            /*Check whether any activities has been added,updated or deleted and if so then display Send Notification Button*/
            var check_count = "<?php echo $check_count;?>";
            //bootbox.alert(check_count);

            if(check_count>=1){
                $("#notificationbutton").show();
            }
            else {
                $("#notificationbutton").hide();
            }
            /*END of Check whther any activities has been added,updated or deleted and if so the display Send Notification Button*/

            $("#notificationbutton").click(function(){
                $("#loader").show();
              $.post("send_notification.php", function(){
                $("#loader").hide();
                bootbox.alert("Notification sent Successfully!");
              });
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
        $('#printbutton').click(function(){
           $( ".print_content" ).print();
             return( false );
          });
       var fileTypes = ['jpg', 'jpeg', 'png'];  //acceptable file types
            function readURL(input) {
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
            if (isSuccess) {
            var reader = new FileReader();        
            reader.onload = function (e) {
                $('#planboximg').attr('src', e.target.result);
                $('#frm_upload_image').submit(); //To upload to server directly
            }          
            reader.readAsDataURL(input.files[0]);
        } else {
            alert("Plan Cover Image should be in jpg, png or jpeg format.");
            return false;
        }
    }
    }
   $("#cover_image").change(function(){
        readURL(this);
    });
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	   });
	</script>
	</body>
	</html>