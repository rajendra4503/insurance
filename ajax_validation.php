<?php
session_start();
ini_set("display_errors","0");
include('include/configinc.php');
date_default_timezone_set("Asia/Kolkata");
$host_server = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
$currenttime = date("Y-m-d G:i:s ");
$current_date       = date('Y-m-d');

//FUNCTION TO FORMAT PHONE NUMBERS
function formatPhone($num)
{
$num = preg_replace('/[^0-9]/', '', $num);
$len = strlen($num);

	if($len<=6)
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})/', ' $1 $2', $num);	
	}
	else if(($len>6)&&($len<=9))
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{1})/', ' $1 $2 $3', $num);	
	}
	else if($len == 10)
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', ' $1 $2 $3', $num);
	}
	else if($len>10)
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/', ' $1 $2 $3 $4', $num);	
	}
return $num;
}
//END OF FUNCTION TO FORMAT PHONE NUMBERS

$type 	=(empty($_REQUEST['type'])) ? '' : mysql_real_escape_string($_REQUEST['type']);

//echo $type;exit;
if($type == "incomplete_registration" )
{
	$userid = $_REQUEST['userid'];
	if($userid!="")
	{
		mysql_query("delete from USER_ACCESS where UserID='$userid'");
		mysql_query("delete from USER_DETAILS where UserID='$userid'");
		$check_delete = mysql_affected_rows();
	}
header('Location:login.php');
}

//echo $type;exit;
//****************LOGIN FUNCTIONALITY(Parameters are Email ID/Mobile Number And Password. Remember Me option also included)***********
if($type=="login")
{
$rem 		= $_REQUEST['remember_me'];
$message	= array();
$country 	= $_REQUEST['country'];
//$country 	= "91";
//echo "<pre>";print_r($_REQUEST);exit;
	if(isset($_REQUEST['username']) && !empty($_REQUEST['username']))
	{
	    $username=mysql_real_escape_string($_REQUEST['username']);
		$_SESSION['username']	= $username;
	}
	else
	{
	    $message[]='Please enter Email/Mobile No.';
	}
	//echo $username ;
	if(isset($_REQUEST['password']) && !empty($_REQUEST['password']))
	{
	    $password=mysql_real_escape_string($_REQUEST['password']);
	}
	else
	{
	    $message[]='Please enter Password';
	}

	$countError=count($message);

	if($countError == 1)
	{
	     for($i=0;$i<$countError;$i++)
		 {
	              echo ucwords($message[$i]);
	     }
	}
	elseif($countError == 2)
	{
		echo ucwords("Please enter Email/Mobile No. and Password");
	}
	else
	{
	    $query 		= "select t1.UserID, t1.EmailID, t1.MobileNo, t1.PasswordStatus, t1.PaidUntil, t2.MerchantID, t2.RoleID, t2.Type, t2.Status, t3.FirstName,
	    				t3.LastName, t3.ProfilePicture
	    				from USER_ACCESS as t1, USER_MERCHANT_MAPPING as t2, USER_DETAILS as t3
	    				where (t1.EmailID = '$username' or  substr(t1.MobileNo,6)='$username') and t1.Password = '$password' 
	    				and t1.UserID=t2.UserID and t2.UserID=t3.UserID  and t3.CountryCode=$country and t2.Status='A' and t2.RoleID != '5'";
		//echo $query;exit;
		$res		= mysql_query($query);
	    $checkUser	= mysql_num_rows($res);
	    //echo $checkUser;exit;
	    if($checkUser == 1)
		{
			while($row = mysql_fetch_array($res))
			{
				$logged_userid 			= $row['UserID'];
				$logged_mobile 			= $row['MobileNo'];
				$logged_email 			= $row['EmailID'];
				$logged_passwordstatus  = $row['PasswordStatus'];
				$logged_firstname 		= $row['FirstName'];
				$logged_lastname        = $row['LastName'];
				$logged_userdp          = $row['ProfilePicture'];

				//echo $logged_userid;exit;
				if($logged_userid)
				{
					$check_for_multiple_merchants 	= "select t1.MerchantID,t1.RoleID,t1.Status,t1.Type,t2.CompanyName,t2.CompanyCountryCode,t3.RoleName,t4.CountryID from USER_MERCHANT_MAPPING as t1,MERCHANT_DETAILS as t2, ROLE_MASTER as t3, COUNTRY_DETAILS as t4 where t1.MerchantID=t2.MerchantID and t1.RoleId=t3.RoleId and t1.UserID='$logged_userid' and t1.Status='A' and t1.RoleID != '5' and t4.CountryCode = t2.CompanyCountryCode and t4.Timezone!=''";
					//echo $check_for_multiple_merchants;exit;
					$check_multiple_merchants_qry	= mysql_query($check_for_multiple_merchants);
					$merchant_count 				= mysql_num_rows($check_multiple_merchants_qry); 
					if($merchant_count==1)
					{
						if($rows = mysql_fetch_array($check_multiple_merchants_qry))
						{
							$logged_merchantid      	= $rows['MerchantID'];
							$logged_companyname     	= $rows['CompanyName'];
							$logged_usertype         	= $rows['Type'];
							$logged_roleid          	= $rows['RoleID'];
							$logged_rolename 			= $rows['RoleName'];
							$logged_userstatus      	= $rows['Status'];
							$logged_companycountry 		= $rows['CompanyCountryCode'];
							//$logged_companycountryid 	= $rows['CountryID'];
							$logged_companycountryid 	= substr($rows['CountryID'],0,2);

							$_SESSION['logged_merchantid'] 				= $logged_merchantid;
							$_SESSION['logged_companyname'] 			= $logged_companyname;
							$_SESSION['logged_usertype'] 				= $logged_usertype;
							$_SESSION['logged_roleid'] 					= $logged_roleid;
							$_SESSION['logged_rolename'] 				= $logged_rolename;
							$_SESSION['logged_userstatus'] 				= $logged_userstatus;
							$_SESSION['logged_companycountry'] 			= $logged_companycountry;
							$_SESSION['logged_companycountryid'] 		= $logged_companycountryid;
						}
						$response['multiple_merchants'] = 0; 
					}
					else
					{
						$response['multiple_merchants'] = 1; 
					}
				}

				$year = time() + 31536000; 
				if($rem == "true")
				{
				setcookie('remember_me', $_REQUEST['username'], $year);
				}
				else
				{
					if(isset($_COOKIE['remember_me']))
					{
						$past = time() - 100;
						setcookie('remember_me',$_REQUEST['username'], $past);
					}
				}
			$_SESSION['logged_userid'] 			= $logged_userid;
			$_SESSION['logged_mobile'] 			= $logged_mobile;
			$_SESSION['logged_email'] 			= $logged_email;
			$_SESSION['logged_passwordstatus'] 	= $logged_passwordstatus;
			$_SESSION['logged_firstname'] 		= $logged_firstname;
			$_SESSION['logged_lastname'] 		= $logged_lastname;
			$_SESSION['logged_userdp']			= $logged_userdp;
			//echo "<pre>";print_r($_SESSION);exit;
			}
	    //echo 'success';
		$response['login']			= 1;
		$response['userid'] 		= $logged_userid;
		$response['passwordstatus'] = $logged_passwordstatus;
	    }
	    else
		{
		//echo 'failure';
	    $response['login'] 	= 0;
	    $response['multiple_merchants'] = 0;
	    }
	}
//echo "<pre>";print_r($response);exit;
echo json_encode($response);
}

//CHANGE PASSWORD
elseif ($type == "change_password")
{
	$userid 						= $_REQUEST['changepassword_userid'];
	//$changepassword_passwordstatus 	= $_REQUEST['changepassword_passwordstatus'];
	$password 						= $_REQUEST['password1'];
	$password 						= str_replace(' ', '', $password);
	$update							= "update USER_ACCESS set Password = '$password',PasswordStatus=1 where UserID='$userid'";
	$update_qry   					= mysql_query($update);
	if($update_qry)
	{
		$response['success'] 		= true;
		$response['cp_status'] 		= 1;
	}
	else
	{
		$response['success'] 		= false;
		$response['cp_status'] 		= 0;
	}
echo json_encode($response);
}

//FORGOT PASSWORD
elseif($type=='forgot_password')
{
include ('SMTP/PHPMailerAutoload.php');
include ('SMTP/class.phpmailer.php');
include ('SMTP/class.smtp.php');

$username=mysql_real_escape_string(trim($_REQUEST['email']));
	if($username)
	{
		//echo 123;exit;
		$q1		= "select t1.UserID, t1.EmailID, t3.FirstName, t3.LastName
            from USER_ACCESS as t1, USER_MERCHANT_MAPPING as t2, USER_DETAILS as t3
            where (t1.EmailID = '$username' or  t1.MobileNo='$username') and t1.UserID=t2.UserID and t2.UserID=t3.UserID and t2.Status='A' and t2.RoleID != '5'";
		//echo $q1;exit;
		$r1 	= mysql_query($q1);
		$n1		= mysql_num_rows($r1);
		if($n1 == 0) 
		{ 
			echo 2;
			exit;
		}
	
		
		$q2		= "select t1.UserID, t1.EmailID, t1.MobileNo, t2.MerchantID, t2.RoleID, t2.Status, t3.FirstName, t3.LastName
	    			from USER_ACCESS as t1, USER_MERCHANT_MAPPING as t2, USER_DETAILS as t3
					where t1.EmailID = '$username' and t1.UserID=t2.UserID and t2.UserID=t3.UserID 
					and t2.Status='A' and t2.RoleID != '5'";
		
		//echo $q2;exit;
		$r2 	= mysql_query($q2);
		$n2		= mysql_num_rows($r2);
		if($n2 > 0) 
		{
		$token	= mt_rand();
		//$token = "jvv";
			if($row2		= mysql_fetch_array($r2))
			{
				$userid 		= (empty($row2['UserID'])) 		? '' : $row2['UserID'];
				$email 			= (empty($row2['EmailID'])) 	? '' : $row2['EmailID'];
				$name 			= (empty($row2['FirstName'])) 	? '' : $row2['FirstName'];
			}
		if($email !='')
		{
		$update	= "update USER_ACCESS set Password = '$token',PasswordStatus=0 where UserID='$userid'";
		//echo $q;
		
		$update_qry   = mysql_query($update);
		
			function mailresetlink($email,$token,$name)
			{//echo $to;exit;
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			// Set PHPMailer to use the sendmail transport
			$mail->isSMTP();
			//Set who the message is to be sent from
			$mail->setFrom('support@planpiper.com', 'Admin');
			//Set who the message is to be sent to
			$mail->addAddress($email,$name);
			//Set the subject line
			$mail->Subject = 'Forgot Password for Planpiper';
			
			$message = "
			<html>
			<head>
			<title>Planpiper Password Reset</title>
			</head>
			<body>
			<p>Hi $name,</p>
			<p>You have requested for a change of password. Please use the one time password below to log in. You shall be prompted to change your password afterward.</p>
			<p><b>Password : $token</b></p>
			<p>While you cannot reply to this email, please feel free to write to us with any queries at <i><u><a href='mailto:support@planpiper.com'>support@planpiper.com</a></u></i></p>	
			</body>
			</html>
			";	
					
			$mail->msgHTML($message, dirname(__FILE__));
			
			//Replace the plain text body with one created manually
			$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.gif');
			
			//send the message, check for errors
				if(!$mail->send())
				{
					"Mailer Error: " . $mail->ErrorInfo;
				} else
				{
					"We have sent the password reset link to your  email id <b>".$email."</b>"; 
				}
			}
			//echo $email." ".$token." ".$name;exit;
			mailresetlink($email,$token,$name);
		}
		//echo "success";
		echo 1;
		}
		else
		{
	         //echo ucwords('Please enter a valid Email and Password');
			echo 0;
	    }	
	}
}

//MULTIPLE MERCHANTS
elseif ($type == "get_multiple_merchants"){
	$user_id 			= $_REQUEST['user_id'];
	$result 			= array();
	$multiple_community_select = "select t1.CommunityID,t1.UserID,t1.ResidenceID, t1.RoleID, t1.WorkerCategoryID, t1.ResidentType,t1.UserStatus, t2.CommunityName from COMMUNITY_USER_MAPPING as t1,COMMUNITY_MASTER as t2 where t1.UserID='$user_id' and t1.CommunityID=t2.CommunityID and t1.RoleID!=(1 or 7 or 8)";
	$check_multiple_community_qry	= mysql_query($multiple_community_select);
	$community_count 				= mysql_num_rows($check_multiple_community_qry); 
	$switch = array();
		if($community_count>0) {
				while($row = mysql_fetch_array($check_multiple_community_qry)){
					$res['CommunityName'] 		= $row['CommunityName'];
					$res['CommunityID'] 		= $row['CommunityID'];
					array_push($switch,$res);
			}
			echo $_REQUEST['jsoncallback'] . '(' . json_encode($switch) . ');';					
		}
			echo $_REQUEST['jsoncallback'] . '(' . json_encode($switch) . ');';	
}

elseif ($type == "check_duplicate_email"){
	$mailid 			= $_REQUEST['mailid'];
	//CHECK FOR DUPLICATE EMAIL ID
	$duplicate_email_check = mysql_query("select UserID from USER_ACCESS where  EmailID = '$mailid'");
	$email_exist  = mysql_num_rows($duplicate_email_check);
	if($email_exist >= 1){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);
}

elseif ($type == "check_count"){
	$merchant_id 			= $_REQUEST['merchantid'];
	//CHECK FOR Count
	$family_row = mysql_fetch_array(mysql_query("select count(*) as member_count from USER_MERCHANT_MAPPING where  MerchantID = '$merchant_id' and Status='A'"));
	$count  	= $family_row['member_count'];
	//echo $count;exit;
	if($count>5){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);
}
elseif ($type == "check_duplicate_mobile"){
	$mobile 			= $_REQUEST['mobile'];
	//echo $mobile;exit;
	//CHECK FOR DUPLICATE MOBILE NUMBER
	$duplicate_mobile_check = mysql_query("select UserID from USER_ACCESS where  MobileNo = '$mobile'");
	$mobile_exist  = mysql_num_rows($duplicate_mobile_check);
	//echo $mobile_exist;exit;
	if($mobile_exist >= 1){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);
}
elseif ($type == "get_plan_details") {
	$plancode 	= $_REQUEST['plancode'];
	$details 	= array();
	if($plancode != ""){
		$get_plan_details = "select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2  where PlanCode = '$plancode' and t1.CategoryID = t2.CategoryID and t1.PlanStatus = 'A'";
	//echo $get_plan_details;exit;
	$get_plan_details_run = mysql_query($get_plan_details);
	$get_plan_details_count = mysql_num_rows($get_plan_details_run);
			 		if($get_plan_details_count > 0){
			 			while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
			 				$res['PlanCode'] 			= $plan_details['PlanCode'];
			 				$res['PlanName'] 			= $plan_details['PlanName'];
			 				$plandet_desc 				= substr($plan_details['PlanDescription'], 0, 120);
			 				if(strlen($plandet_desc) 	>= 120){
			 					$plandet_desc = $plandet_desc."...";
			 				}
			 				$res['PlanDescription']    	= $plandet_desc;
			 				if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
								$res['PlanCoverImagePath'] 	= "uploads/planheader/".$plan_details['PlanCoverImagePath'];
			 				} else {
			 					$res['PlanCoverImagePath'] 	= "uploads/planheader/default.jpg";
			 				}
			 				
			 				$res['CategoryName'] 		= $plan_details['CategoryName'];
			 				$res['CategoryID']			= $plan_details['CategoryID'];
			 				array_push($details,$res);
			 				}
			 		} else {

			 		}
	}
	echo $_REQUEST['jsoncallback'] . '(' . json_encode($details) . ');';			
}
elseif ($type == "get_user_details") {
	$userid 	= $_REQUEST['userid'];
	$details 	= array();
	if($userid != ""){
		$get_user_details = "select t1.FirstName, t1.LastName, t2.MobileNo, t2.EmailID from USER_DETAILS as t1, USER_ACCESS as t2 where t1.UserID='$userid' and t1.UserID = t2.UserID;";
	//echo $get_user_details;exit;
	$get_user_details_run = mysql_query($get_user_details);
	$get_user_details_count = mysql_num_rows($get_user_details_run);
			 		if($get_user_details_count > 0){
			 			while ($user_details = mysql_fetch_array($get_user_details_run)) {
			 				$res['FirstName'] 			= $user_details['FirstName'];
			 				$res['LastName'] 			= $user_details['LastName'];
			 				$res['MobileNo'] 			= $user_details['MobileNo'];
			 				$res['EmailID']				= $user_details['EmailID'];
			 				array_push($details,$res);
			 				}
			 		} else {

			 		}
	}
	echo $_REQUEST['jsoncallback'] . '(' . json_encode($details) . ');';			
}
elseif ($type == "publish_plan") {
	$plancode 	= $_REQUEST['plancode'];
	$publish_plan = mysql_query("update PLAN_HEADER set PlanStatus = 'A' where Plancode='$plancode'");
	$plan_published  = mysql_affected_rows();
	if($plan_published){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);		
}
elseif ($type == "deactivate_plan") {
	$plancode 	= $_REQUEST['plancode'];
	$deactivate_plan = mysql_query("update PLAN_HEADER set PlanStatus = 'I' where Plancode='$plancode'");
	$plan_deactivated  = mysql_affected_rows();
	if($plan_deactivated){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);		
} 
elseif ($type == "assign_to_user") {
	$plancode 	= $_REQUEST['plancode'];
	$_SESSION['current_created_plancode'] = $plancode;
	$_SESSION['plancode_for_current_plan'] = $plancode;
	header("location:assign_to_user.php");
} 
elseif ($type == "view_active_users") {
	$plancode 	= $_REQUEST['plancode'];
	$_SESSION['view_active_users_plancode'] = $plancode;
	header("location:view_active_users.php");
}
elseif ($type == "view_active_plans") {
	$userid 	= $_REQUEST['userid'];
	$_SESSION['view_active_plans_userid'] = $userid;
	header("location:view_active_plans.php");
}
elseif ($type == "edit_assigned_plan") {
	$userid 	= $_REQUEST['userid'];
	$plancode 	= $_REQUEST['plancode'];
	$_SESSION['current_assigned_user_id'] = $userid;
	$_SESSION['userid_for_current_plan'] = $userid;
	$_SESSION['current_assigned_plan_code'] = $plancode;
	$_SESSION['plancode_for_current_plan'] = $plancode;
	header("location:customize_plan.php");
}
elseif ($type == "edit_master_plan") {
	$plancode 	= $_REQUEST['plancode'];
	$_SESSION['current_created_plancode'] = $plancode;
	$_SESSION['plancode_for_current_plan'] = $plancode;
		if($plancode != ""){
		$get_plan_details = "select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2  where PlanCode = '$plancode' and t1.CategoryID = t2.CategoryID";
	//echo $get_plan_details;exit;
	$get_plan_details_run = mysql_query($get_plan_details);
	$get_plan_details_count = mysql_num_rows($get_plan_details_run);
			 		if($get_plan_details_count > 0){
			 			while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
			 				$plan_code 			= $plan_details['PlanCode'];
			 				$plan_name 			= $plan_details['PlanName'];
			 				}
			 		} else {

			 		}
	}
	$_SESSION['current_created_plancode'] = $plan_code;
	$_SESSION['plancode_for_current_plan'] = $plancode;
	$_SESSION['current_created_planname'] = $plan_name;
	include('include/session.php');
	//header("location:plan_medication.php");
	 header("location:plan_med_new.php");
	//header("location : $header_url");
	include('include/unset_session.php');
}
elseif ($type == "deactivate_plan_user") {
	$userid 		= $_REQUEST['userid'];
	$merchantid 	= $_REQUEST['merchantid'];
	$deactivate_plan_user = mysql_query("update USER_MERCHANT_MAPPING set Status = 'I' where UserID='$userid' and MerchantID='$merchantid'");
	$plan_user_deactivated  = mysql_affected_rows();
	if($plan_user_deactivated){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);		
} 
elseif ($type == "edit_plan_user") {
	$userid 	= $_REQUEST['userid'];
	$_SESSION['plan_user_to_be_edited'] = $userid;
	header("location:edit_plan_user.php");
}
elseif ($type == "review_user") {
	$userid 	= $_REQUEST['userid'];
	$_SESSION['plan_user_to_be_edited'] = $userid;
	header("location:client_dashboard.php?id=$userid");
}
elseif ($type == "edit_staff_details") {
	$userid 	= $_REQUEST['userid'];
	$_SESSION['staff_to_be_edited'] = $userid;
	header("location:edit_staff_details.php");
}
elseif ($type == "plan_publish_eligibility") {
	$plancode = $_REQUEST['plancode'];
	$numofactivities = 0;
	//Check medication
	$get_medication = mysql_query("select PrescriptionNo, RowNo, MedicineName from MEDICATION_DETAILS where PlanCode='$plancode'");
	$get_med_num = mysql_num_rows($get_medication);


	$get_instruction = mysql_query("select PrescriptionNo, RowNo, MedicineName from INSTRUCTION_DETAILS where PlanCode='$plancode'");
	$get_inst_num = mysql_num_rows($get_instruction);

	//Check appointments
	$get_appointment = mysql_query("select AppointmentDate, AppointmentTime from APPOINTMENT_DETAILS where PlanCode='$plancode'");
	$get_appo_num = mysql_num_rows($get_appointment);

	//Check selftests
	$get_selftests = mysql_query("select SelfTestID, RowNo from SELF_TEST_DETAILS where PlanCode='$plancode'");
	$get_st_num = mysql_num_rows($get_selftests);

	//Check labtests
	$get_labtests = mysql_query("select LabTestID, RowNo from LAB_TEST_DETAILS1 where PlanCode='$plancode'");
	$get_lt_num = mysql_num_rows($get_labtests);

	//Check diet
	$get_diet = mysql_query("select DietNo, DayNo from DIET_DETAILS where PlanCode='$plancode'");
	$get_diet_num = mysql_num_rows($get_diet);

	//Check exercise
	$get_exercise = mysql_query("select ExercisePlanNo, DayNo from EXERCISE_DETAILS where PlanCode='$plancode'");
	$get_exe_num = mysql_num_rows($get_exercise);

	$numofactivities = $get_med_num + $get_appo_num + $get_st_num + $get_lt_num + $get_diet_num + $get_exe_num + $get_inst_num;
	//echo $numofactivities;
	if($numofactivities > 0){
		$response['success'] 		= true;
	} else {
		$response['success'] 		= false;
	}
echo json_encode($response);	
}
//GET COUNTRY CODE BASED ON SELECTED COUNTRY(TIMEZONE)
elseif($type == 'get_country_code')
{
$timezone= $_REQUEST['timezone'];
//echo $center;exit;
$row = "select CountryCode from COUNTRY_DETAILS where Timezone ='$timezone'";
///echo $row;exit;
$row_qry = mysql_query($row);
$row_count = mysql_num_rows($row_qry);
//echo $row_count;exit;
	while($row2 = mysql_fetch_array($row_qry))
	{
	echo $country_code	= $row2['CountryCode'];	
	//echo "<option value='$cent_id'>$cent_name</option>";
	}
//echo $country_code;
}

//GET COUNTRY CODE BASED ON SELECTED COUNTRY(TIMEZONE)
elseif($type == 'get_check')
{
$timezone= $_REQUEST['timezone'];
//echo $center;exit;
$row = "select CountryCode from COUNTRY_DETAILS where Timezone ='$timezone'";
///echo $row;exit;
$row_qry = mysql_query($row);
$row_count = mysql_num_rows($row_qry);
//echo $row_count;exit;
	while($row2 = mysql_fetch_array($row_qry))
	{
	echo $country_code	= $row2['CountryCode'];	
	//echo "<option value='$cent_id'>$cent_name</option>";
	}
//echo $country_code;
}

elseif($type == 'insert_plan_header'){
	include('include/session.php');
	$plancode = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['code'])));
	$planname = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['title'])));
	$plandesc = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['desc'])));
	$merchantid = $logged_merchantid;
	$userid = $logged_userid;
	$check_plancode = mysql_query("select PlanCode from PLAN_HEADER where PlanCode = '$plancode'");
	$plancode_count = mysql_num_rows($check_plancode);
	if($plancode_count > 0){
		//echo "update";
		$plan_header = mysql_query("update PLAN_HEADER set PlanName='$planname', PlanDescription='$plandesc' where PlanCode='$plancode'");

	}  else {
		//echo "insert";
		$plan_header = "insert into PLAN_HEADER (PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCoverImagePath, CreatedDate, CreatedBy,UpdatedBy) values ('$plancode', '$merchantid', '1', '$planname', '$plandesc', 'P', '', now(), '$userid','')";
		//echo $plan_header;exit;
	}
	$plan_header_run = mysql_query($plan_header);
	$plan_header_count = mysql_affected_rows();
	if($plan_header_count){
		$response['success'] = true;
	} else {
		$response['success'] = false;
	}
	echo json_encode($response);
 }
 elseif($type == 'insert_user_plan_header'){
 	include('include/session.php');
	$plancode = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['code'])));
	$planname = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['title'])));
	$plandesc = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['desc'])));
	$userid =  mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['userid'])));
	//$$userid = "000".$userid;
	$merchantid = $logged_merchantid;
	$loggeduserid = $logged_userid;
	$check_plancode = mysql_query("select PlanCode from USER_PLAN_HEADER where PlanCode = '$plancode' and UserID = '$userid'");
	$plancode_count = mysql_num_rows($check_plancode);
	if($plancode_count > 0){
		//echo "update";
		$plan_header = mysql_query("update USER_PLAN_HEADER set PlanName='$planname', PlanDescription='$plandesc' where PlanCode='$plancode' and UserID = '$userid'");
		$check_update= mysql_affected_rows();
		if($check_update)
		{
			$update_header = mysql_query("update USER_PLAN_HEADER set PlanUpdatedDate = current_timestamp where PlanCode='$plancode' and UserID = '$userid'");
		}
	}  else {
		//echo "insert";
		$plan_header = "insert into USER_PLAN_HEADER (UserID, PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCoverImagePath, CreatedDate, CreatedBy) values ('$userid','$plancode', '$merchantid', '1', '$planname', '$plandesc', 'P', '', now(), '$loggeduserid')";
		//echo $insert_plan_header;exit;
	}
	$plan_header_run = mysql_query($plan_header);
	$plan_header_count = mysql_affected_rows();
	if($plan_header_count){
		$response['success'] = true;
	} else {
		$response['success'] = false;
	}
	echo json_encode($response);
 }
 elseif($type == 'get_master_prescriptions_old'){
 	$plancode = $_REQUEST['plancode'];
 	$prescno  = $_REQUEST['prescno'];
 	$prescs   = array();
 	$get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`, `When`, `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `Link`, `SpecificDate` from MEDICATION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno'";
 	//echo $get_presc;exit;
 	$get_presc_run = mysql_query($get_presc);
 	while ($prescrow = mysql_fetch_array($get_presc_run)) {
		$res['PlanCode'] 					= $prescrow['PlanCode'];
		$res['PrescriptionNo'] 				= $prescrow['PrescriptionNo'];
		$res['RowNo'] 						= $prescrow['RowNo'];
		$res['MedicineName']				= $prescrow['MedicineName'];
		//$res['ThresholdLimit']				= $prescrow['ThresholdLimit'];
		$res['MedicineCount']				= $prescrow['MedicineCount'];
		$res['MedicineTypeID']				= $prescrow['MedicineTypeID'];
		$res['When']						= $prescrow['When'];
		$res['SpecificTime']				= date('h:i A',strtotime($prescrow['SpecificTime']));
		$res['SpecificTimeType'] 				= "type='hidden'";
		if($res['When'] == '16'){
			$res['SpecificTimeType'] = "type='text'";
		}
		$res['Instruction']					= $prescrow['Instruction'];
		$res['Frequency']					= $prescrow['Frequency'];
		$res['FrequencyString']				= (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
		$res['WeeklyType'] 					= "type='hidden'";
		$res['MonthlyType'] 				= "type='hidden'";
		if($res['Frequency'] == "Weekly"){
			$res['WeeklyType'] = "type='text'";
		}
		if($res['Frequency'] == "Monthly"){
			$res['MonthlyType'] = "type='text'";
		}
		$res['HowLong']						= (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
		$res['HowLongType']					= $prescrow['HowLongType'];
		$res['IsCritical']					= $prescrow['IsCritical'];
		if($res['IsCritical'] == "Y"){
			$res['CriticalSelect'] 			= "checked";
		} else {
			$res['CriticalSelect'] 			= "";
		}
		$res['ResponseRequired']			= $prescrow['ResponseRequired'];
		if($res['ResponseRequired'] == "Y"){
			$res['ResponseSelect'] 			= "checked";
		} else {
			$res['ResponseSelect'] 			= "";
		}
		$res['StartFlag']					= $prescrow['StartFlag'];
		$res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";

		if($res['StartFlag'] == "PS"){
			$res['PlanStartRadio'] = "checked";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['SpecificDate']				= $prescrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['NumOfDaysRadio'] = "checked";
			$res['NoOfDaysAfterPlanStarts']		= $prescrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
		
		$res['Link']						= (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
		

		//GET DOCTOR SHORT HAND
		$shorthand_options = "";
		$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
		$shorthand_count = mysql_num_rows($get_shorthand);
		if($shorthand_count > 0){
		  while ($shorthand = mysql_fetch_array($get_shorthand)) {
		    $shorthand_id  = $shorthand['ID'];
		    $shorthandname = $shorthand['ShortHand'];
		    if($shorthand_id == $res['When']){
		    	$shorthand_options .= "<option value='$shorthand_id' selected>$shorthandname</option>";
		    } else {
		    	$shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
		    }    
		  }
		}
		$res['ShortHandOptions']				= $shorthand_options;

		//GET MEDICATION TYPES
		$medicine_type_options = "";
		$get_medicine_types = mysql_query("select SNo, MedicineType from MEDICINE_TYPES where SNo != '0'");
		$type_count = mysql_num_rows($get_medicine_types);
		if($type_count > 0){
		  while ($medtype = mysql_fetch_array($get_medicine_types)) {
		    $medtype_id     = $medtype['SNo'];
		    $medtype_name   = $medtype['MedicineType'];
		    if($medtype_id == $res['MedicineTypeID']){
		    $medicine_type_options .= "<option value='$medtype_id' selected>$medtype_name</option>";
			} else {
				$medicine_type_options .= "<option value='$medtype_id'>$medtype_name</option>";
			  }
		}
		}
		$res['MedicineTypeOptions']				= $medicine_type_options;

		//INSTRUCTION SELECT BOX
				$instruction_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Instruction'] == "Before Food"){
					$instruction_options .= "<option value='Before Food' selected>Before Food</option>";
				} else {
					$instruction_options .= "<option value='Before Food'>Before Food</option>";
				}
				if($res['Instruction'] == "With Food"){
					$instruction_options .= "<option value='With Food' selected>With Food</option>";
				} else {
					$instruction_options .= "<option value='With Food'>With Food</option>";
				}
				if($res['Instruction'] == "After Food"){
					$instruction_options .= "<option value='After Food' selected>After Food</option>";
				} else {
					$instruction_options .= "<option value='After Food'>After Food</option>";
				}
				if($res['Instruction'] == "NA"){
					$instruction_options .= "<option value='NA' selected>Not Applicable</option>";
				} else {
					$instruction_options .= "<option value='NA'>Not Applicable</option>";
				}
			$res['InstructionOptions']				= $instruction_options;	

		//FREQUENCY SELECT BOX
			$frequency_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Frequency'] == "Once"){
					$frequency_options .= "<option value='Once' selected>Once</option>";
				} else {
					$frequency_options .= "<option value='Once'>Once</option>";
				}
				if($res['Frequency'] == "Daily"){
					$frequency_options .= "<option value='Daily' selected>Daily</option>";
				} else {
					$frequency_options .= "<option value='Daily'>Daily</option>";
				}
				if($res['Frequency'] == "Weekly"){
					$frequency_options .= "<option value='Weekly' selected>Weekly</option>";
				} else {
					$frequency_options .= "<option value='Weekly'>Weekly</option>";
				}
				if($res['Frequency'] == "Monthly"){
					$frequency_options .= "<option value='Monthly' selected>Monthly</option>";
				} else {
					$frequency_options .= "<option value='Monthly'>Monthly</option>";
				}
			$res['FrequencyOptions']				= $frequency_options;

			//HOW LONG TYPE SELECT BOX
			$howlongtype_options = "<option value='0' style='display:none;'>select</option>";
				if($res['HowLongType'] == "Days"){
					$howlongtype_options .= "<option value='Days' selected>Days</option>";
				} else {
					$howlongtype_options .= "<option value='Days'>Days</option>";
				}
				if($res['HowLongType'] == "Weeks"){
					$howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
				} else {
					$howlongtype_options .= "<option value='Weeks'>Weeks</option>";
				}
				if($res['HowLongType'] == "Months"){
					$howlongtype_options .= "<option value='Months' selected>Months</option>";
				} else {
					$howlongtype_options .= "<option value='Months'>Months</option>";
				}
			$res['HowLongTypeOptions']				= $howlongtype_options;
		array_push($prescs,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($prescs) . ');';	
 	//echo json_encode($prescs);	
 }
  elseif($type == 'get_assigned_prescriptions_old'){
 	$plancode 	= $_REQUEST['plancode'];
 	$prescno  	= $_REQUEST['prescno'];
 	$userid  	= $_REQUEST['userid'];
 	$prescs   = array();
 	$get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`,  `When`, `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `Link`, `SpecificDate` from USER_MEDICATION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno' and UserID='$userid'";
 	//echo $get_presc;exit;
 	$get_presc_run = mysql_query($get_presc);
 	while ($prescrow = mysql_fetch_array($get_presc_run)) {
		$res['PlanCode'] 					= $prescrow['PlanCode'];
		$res['PrescriptionNo'] 				= $prescrow['PrescriptionNo'];
		$res['RowNo'] 						= $prescrow['RowNo'];
		$res['MedicineName']				= $prescrow['MedicineName'];
		//$res['ThresholdLimit']				= $prescrow['ThresholdLimit'];
		$res['MedicineCount']				= $prescrow['MedicineCount'];
		$res['MedicineTypeID']				= $prescrow['MedicineTypeID'];
		$res['When']						= $prescrow['When'];
		$res['SpecificTime']				= date('h:i A',strtotime($prescrow['SpecificTime']));
		$res['SpecificTimeType'] 				= "type='hidden'";
		if($res['When'] == '16'){
			$res['SpecificTimeType'] = "type='text'";
		}
		$res['Instruction']					= $prescrow['Instruction'];
		$res['Frequency']					= $prescrow['Frequency'];
		$res['FrequencyString']				= (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
		$res['WeeklyType'] 					= "type='hidden'";
		$res['MonthlyType'] 				= "type='hidden'";
		if($res['Frequency'] == "Weekly"){
			$res['WeeklyType'] = "type='text'";
		}
		if($res['Frequency'] == "Monthly"){
			$res['MonthlyType'] = "type='text'";
		}
		$res['HowLong']						= (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
		$res['HowLongType']					= $prescrow['HowLongType'];
		$res['IsCritical']					= $prescrow['IsCritical'];
		if($res['IsCritical'] == "Y"){
			$res['CriticalSelect'] 			= "checked";
		} else {
			$res['CriticalSelect'] 			= "";
		}
		$res['ResponseRequired']			= $prescrow['ResponseRequired'];
		if($res['ResponseRequired'] == "Y"){
			$res['ResponseSelect'] 			= "checked";
		} else {
			$res['ResponseSelect'] 			= "";
		}
		$res['StartFlag']					= $prescrow['StartFlag'];
		$res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";

		if($res['StartFlag'] == "PS"){
			$res['PlanStartRadio'] = "checked";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['SpecificDate']				= $prescrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['NumOfDaysRadio'] = "checked";
			$res['NoOfDaysAfterPlanStarts']		= $prescrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
		
		$res['Link']						= (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
		

		//GET DOCTOR SHORT HAND
		$shorthand_options = "";
		$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
		$shorthand_count = mysql_num_rows($get_shorthand);
		if($shorthand_count > 0){
		  while ($shorthand = mysql_fetch_array($get_shorthand)) {
		    $shorthand_id  = $shorthand['ID'];
		    $shorthandname = $shorthand['ShortHand'];
		    if($shorthand_id == $res['When']){
		    	$shorthand_options .= "<option value='$shorthand_id' selected>$shorthandname</option>";
		    } else {
		    	$shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
		    }    
		  }
		}
		$res['ShortHandOptions']				= $shorthand_options;

		//GET MEDICATION TYPES
		$medicine_type_options = "";
		$get_medicine_types = mysql_query("select SNo, MedicineType from MEDICINE_TYPES where SNo != '0'");
		$type_count = mysql_num_rows($get_medicine_types);
		if($type_count > 0){
		  while ($medtype = mysql_fetch_array($get_medicine_types)) {
		    $medtype_id     = $medtype['SNo'];
		    $medtype_name   = $medtype['MedicineType'];
		    if($medtype_id == $res['MedicineTypeID']){
		    $medicine_type_options .= "<option value='$medtype_id' selected>$medtype_name</option>";
			} else {
				$medicine_type_options .= "<option value='$medtype_id'>$medtype_name</option>";
			  }
		}
		}
		$res['MedicineTypeOptions']				= $medicine_type_options;

		//INSTRUCTION SELECT BOX
				$instruction_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Instruction'] == "Before Food"){
					$instruction_options .= "<option value='Before Food' selected>Before Food</option>";
				} else {
					$instruction_options .= "<option value='Before Food'>Before Food</option>";
				}
				if($res['Instruction'] == "With Food"){
					$instruction_options .= "<option value='With Food' selected>With Food</option>";
				} else {
					$instruction_options .= "<option value='With Food'>With Food</option>";
				}
				if($res['Instruction'] == "After Food"){
					$instruction_options .= "<option value='After Food' selected>After Food</option>";
				} else {
					$instruction_options .= "<option value='After Food'>After Food</option>";
				}
				if($res['Instruction'] == "NA"){
					$instruction_options .= "<option value='NA' selected>Not Applicable</option>";
				} else {
					$instruction_options .= "<option value='NA'>Not Applicable</option>";
				}
			$res['InstructionOptions']				= $instruction_options;	

		//FREQUENCY SELECT BOX
			$frequency_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Frequency'] == "Once"){
					$frequency_options .= "<option value='Once' selected>Once</option>";
				} else {
					$frequency_options .= "<option value='Once'>Once</option>";
				}
				if($res['Frequency'] == "Daily"){
					$frequency_options .= "<option value='Daily' selected>Daily</option>";
				} else {
					$frequency_options .= "<option value='Daily'>Daily</option>";
				}
				if($res['Frequency'] == "Weekly"){
					$frequency_options .= "<option value='Weekly' selected>Weekly</option>";
				} else {
					$frequency_options .= "<option value='Weekly'>Weekly</option>";
				}
				if($res['Frequency'] == "Monthly"){
					$frequency_options .= "<option value='Monthly' selected>Monthly</option>";
				} else {
					$frequency_options .= "<option value='Monthly'>Monthly</option>";
				}
			$res['FrequencyOptions']				= $frequency_options;

			//HOW LONG TYPE SELECT BOX
			$howlongtype_options = "<option value='0' style='display:none;'>select</option>";
				if($res['HowLongType'] == "Days"){
					$howlongtype_options .= "<option value='Days' selected>Days</option>";
				} else {
					$howlongtype_options .= "<option value='Days'>Days</option>";
				}
				if($res['HowLongType'] == "Weeks"){
					$howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
				} else {
					$howlongtype_options .= "<option value='Weeks'>Weeks</option>";
				}
				if($res['HowLongType'] == "Months"){
					$howlongtype_options .= "<option value='Months' selected>Months</option>";
				} else {
					$howlongtype_options .= "<option value='Months'>Months</option>";
				}
			$res['HowLongTypeOptions']				= $howlongtype_options;
		array_push($prescs,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($prescs) . ');';	
 	//echo json_encode($prescs);
 }
 elseif($type == 'get_master_instructions'){
 	$plancode = $_REQUEST['plancode'];
 	$prescno  = $_REQUEST['prescno'];
 	$prescs   = array();
 	$get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`,`MedicineName`, `InstructionTypeID`, `When`, `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`,`ThresholdLimit`, `NoOfDaysAfterPlanStarts`, `Link`, `OriginalFileName`, `SpecificDate` from INSTRUCTION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno'";
 	//echo $get_presc;exit;
 	$get_presc_run = mysql_query($get_presc);
 	while ($prescrow = mysql_fetch_array($get_presc_run)) {
		$res['PlanCode'] 					= $prescrow['PlanCode'];
		$res['PrescriptionNo'] 				= $prescrow['PrescriptionNo'];
		//$res['ThresholdLimit'] 				= $prescrow['ThresholdLimit'];
		$res['ThresholdLimit']          	= (empty($prescrow['ThresholdLimit'])) ? '' : $prescrow['ThresholdLimit'];
		$res['RowNo'] 						= $prescrow['RowNo'];
		$res['MedicineName']				= $prescrow['MedicineName'];
		$res['InstructionTypeID']			= $prescrow['InstructionTypeID'];
		$res['When']						= $prescrow['When'];
		$res['SpecificTime']				= date('h:i A',strtotime($prescrow['SpecificTime']));
		$res['SpecificTimeType'] 				= "type='hidden'";
		if($res['When'] == '16'){
			$res['SpecificTimeType'] = "type='text'";
		}
		$res['Instruction']					= $prescrow['Instruction'];
		$res['Frequency']					= $prescrow['Frequency'];
		$res['FrequencyString']				= (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
		$res['WeeklyType'] 					= "type='hidden'";
		$res['MonthlyType'] 				= "type='hidden'";
		if($res['Frequency'] == "Weekly"){
			$res['WeeklyType'] = "type='text'";
		}
		if($res['Frequency'] == "Monthly"){
			$res['MonthlyType'] = "type='text'";
		}
		$res['CountSelect1']     = "style='width:35px;height:30px;'";
	    $res['CountSelect2']     = "style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;";
	    if($res['Frequency'] == "Once"){
	      $res['CountSelect1']     = "disabled style='width:35px;height:30px;opacity:0.2;'";
	      $res['CountSelect2']     = "disabled style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;opacity:0.2;'";
	    }
		$res['HowLong']						= (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
		$res['HowLongType']					= $prescrow['HowLongType'];
		$res['IsCritical']					= $prescrow['IsCritical'];
		if($res['IsCritical'] == "Y"){
			$res['CriticalSelect'] 			= "checked";
		} else {
			$res['CriticalSelect'] 			= "";
		}
		$res['ResponseRequired']			= $prescrow['ResponseRequired'];
		$res['ThresholdInput']       		= "disabled style='width:35px;height:30px;opacity:0.2;'";
		if($res['ResponseRequired'] == "Y"){
			$res['ResponseSelect'] 			= "checked";
			$res['ThresholdInput']       	= "style='width:35px;height:30px;'";
		} else {
			$res['ResponseSelect'] 			= "";
		}
		$res['StartFlag']					= $prescrow['StartFlag'];
		$res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";
		$res['PSClass'] = "";
		$res['SDClass'] = "";
		$res['NDClass'] = "";
		if($res['StartFlag'] == "PS"){
			$res['PlanStartRadio'] = "checked";
			$res['PSClass'] = "";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "pointernone";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "";
			$res['NDClass'] = "pointernone";
			$res['SpecificDate']				= $prescrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "";
			$res['NumOfDaysRadio'] = "checked";
			$res['NoOfDaysAfterPlanStarts']		= $prescrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
		
		$res['Link']						= (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
		$res['OriginalFileName']			= (empty($prescrow['OriginalFileName']))  ? '' : $prescrow['OriginalFileName'];
		

		//GET DOCTOR SHORT HAND
		$shorthand_options = "";
		$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
		$shorthand_count = mysql_num_rows($get_shorthand);
		if($shorthand_count > 0){
		  while ($shorthand = mysql_fetch_array($get_shorthand)) {
		    $shorthand_id  = $shorthand['ID'];
		    $shorthandname = $shorthand['ShortHand'];
		    if($shorthand_id == $res['When']){
		    	$shorthand_options .= "<option value='$shorthand_id' selected>$shorthandname</option>";
		    } else {
		    	$shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
		    }    
		  }
		}
		$res['ShortHandOptions']				= $shorthand_options;

	
	//GET INSTRUCTION TYPE OPTIONS
	$instruction_type_options = "";
	$get_instruction_types = mysql_query("select InstructionTypeID, InstructionType from INSTRUCTION_TYPE order by InstructionType");
	$type_count = mysql_num_rows($get_instruction_types);
	if($type_count > 0){
	  while ($insttype = mysql_fetch_array($get_instruction_types)) {
	    $insttype_id     = $insttype['InstructionTypeID'];
	    $insttype_name   = $insttype['InstructionType'];
	    if($insttype_id == $res['InstructionTypeID']){
		    	$instruction_type_options .= "<option value='$insttype_id' selected>$insttype_name</option>";
		    } else {
		    	$instruction_type_options .= "<option value='$insttype_id'>$insttype_name</option>";
		    }   
	  }
	}
	$res['InstructionTypeOptions']				= $instruction_type_options;


		//INSTRUCTION SELECT BOX
				$instruction_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Instruction'] == "Before Food"){
					$instruction_options .= "<option value='Before Food' selected>Before Food</option>";
				} else {
					$instruction_options .= "<option value='Before Food'>Before Food</option>";
				}
				if($res['Instruction'] == "With Food"){
					$instruction_options .= "<option value='With Food' selected>With Food</option>";
				} else {
					$instruction_options .= "<option value='With Food'>With Food</option>";
				}
				if($res['Instruction'] == "After Food"){
					$instruction_options .= "<option value='After Food' selected>After Food</option>";
				} else {
					$instruction_options .= "<option value='After Food'>After Food</option>";
				}
				if($res['Instruction'] == "NA"){
					$instruction_options .= "<option value='NA' selected>Not Applicable</option>";
				} else {
					$instruction_options .= "<option value='NA'>Not Applicable</option>";
				}
			$res['InstructionOptions']				= $instruction_options;	

		//FREQUENCY SELECT BOX
			$frequency_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Frequency'] == "Once"){
					$frequency_options .= "<option value='Once' selected>Once</option>";
				} else {
					$frequency_options .= "<option value='Once'>Once</option>";
				}
				if($res['Frequency'] == "Daily"){
					$frequency_options .= "<option value='Daily' selected>Daily</option>";
				} else {
					$frequency_options .= "<option value='Daily'>Daily</option>";
				}
				if($res['Frequency'] == "Weekly"){
					$frequency_options .= "<option value='Weekly' selected>Weekly</option>";
				} else {
					$frequency_options .= "<option value='Weekly'>Weekly</option>";
				}
				if($res['Frequency'] == "Monthly"){
					$frequency_options .= "<option value='Monthly' selected>Monthly</option>";
				} else {
					$frequency_options .= "<option value='Monthly'>Monthly</option>";
				}
			$res['FrequencyOptions']				= $frequency_options;

			//HOW LONG TYPE SELECT BOX
			$howlongtype_options = "<option value='0' style='display:none;'>select</option>";
				if($res['HowLongType'] == "Days"){
					$howlongtype_options .= "<option value='Days' selected>Days</option>";
				} else {
					$howlongtype_options .= "<option value='Days'>Days</option>";
				}
				if($res['HowLongType'] == "Weeks"){
					$howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
				} else {
					$howlongtype_options .= "<option value='Weeks'>Weeks</option>";
				}
				if($res['HowLongType'] == "Months"){
					$howlongtype_options .= "<option value='Months' selected>Months</option>";
				} else {
					$howlongtype_options .= "<option value='Months'>Months</option>";
				}
			$res['HowLongTypeOptions']				= $howlongtype_options;
		array_push($prescs,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($prescs) . ');';	
 	//echo json_encode($prescs);	
 }
  elseif($type == 'get_assigned_instructions'){
 	$plancode 	= $_REQUEST['plancode'];
 	$prescno  	= $_REQUEST['prescno'];
 	$userid  	= $_REQUEST['userid'];
 	$prescs   = array();
 	$get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `InstructionTypeID`, `When`, `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`,`ThresholdLimit`, `NoOfDaysAfterPlanStarts`, `Link`, `OriginalFileName`, `SpecificDate` from USER_INSTRUCTION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno' and UserID='$userid'";
 	//echo $get_presc;exit;
 	$get_presc_run = mysql_query($get_presc);
 	while ($prescrow = mysql_fetch_array($get_presc_run)) {
		$res['PlanCode'] 					= $prescrow['PlanCode'];
		$res['PrescriptionNo'] 				= $prescrow['PrescriptionNo'];
		//$res['ThresholdLimit'] 				= $prescrow['ThresholdLimit'];
		$res['ThresholdLimit']          	= (empty($prescrow['ThresholdLimit'])) ? '' : $prescrow['ThresholdLimit'];
		$res['RowNo'] 						= $prescrow['RowNo'];
		$res['MedicineName']				= $prescrow['MedicineName'];
		$res['InstructionTypeID']			= $prescrow['InstructionTypeID'];
		$res['When']						= $prescrow['When'];
		$res['SpecificTime']				= date('h:i A',strtotime($prescrow['SpecificTime']));
		$res['SpecificTimeType'] 				= "type='hidden'";
		if($res['When'] == '16'){
			$res['SpecificTimeType'] = "type='text'";
		}
		$res['Instruction']					= $prescrow['Instruction'];
		$res['Frequency']					= $prescrow['Frequency'];
		$res['FrequencyString']				= (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
		$res['WeeklyType'] 					= "type='hidden'";
		$res['MonthlyType'] 				= "type='hidden'";
		if($res['Frequency'] == "Weekly"){
			$res['WeeklyType'] = "type='text'";
		}
		if($res['Frequency'] == "Monthly"){
			$res['MonthlyType'] = "type='text'";
		}
		$res['CountSelect1']     = "style='width:35px;height:30px;'";
	    $res['CountSelect2']     = "style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;";
	    if($res['Frequency'] == "Once"){
	      $res['CountSelect1']     = "disabled style='width:35px;height:30px;opacity:0.2;'";
	      $res['CountSelect2']     = "disabled style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;opacity:0.2;'";
	    }
		$res['HowLong']						= (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
		$res['HowLongType']					= $prescrow['HowLongType'];
		$res['IsCritical']					= $prescrow['IsCritical'];
		if($res['IsCritical'] == "Y"){
			$res['CriticalSelect'] 			= "checked";
		} else {
			$res['CriticalSelect'] 			= "";
		}
		$res['ResponseRequired']			= $prescrow['ResponseRequired'];
		$res['ThresholdInput']       		= "disabled style='width:35px;height:30px;opacity:0.2;'";
		if($res['ResponseRequired'] == "Y"){
			$res['ResponseSelect'] 			= "checked";
			$res['ThresholdInput']       	= "style='width:35px;height:30px;'";
		} else {
			$res['ResponseSelect'] 			= "";
		}
		$res['StartFlag']					= $prescrow['StartFlag'];
		$res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";
		$res['PSClass'] = "";
		$res['SDClass'] = "";
		$res['NDClass'] = "";
		if($res['StartFlag'] == "PS"){
			$res['PSClass'] = "";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "pointernone";
			$res['PlanStartRadio'] = "checked";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "";
			$res['NDClass'] = "pointernone";
			$res['SpecificDate']				= $prescrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "";
			$res['NumOfDaysRadio'] = "checked";
			$res['NoOfDaysAfterPlanStarts']		= $prescrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
		
		$res['Link']						= (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
		$res['OriginalFileName']			= (empty($prescrow['OriginalFileName']))  ? '' : $prescrow['OriginalFileName'];
		

		//GET DOCTOR SHORT HAND
		$shorthand_options = "";
		$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
		$shorthand_count = mysql_num_rows($get_shorthand);
		if($shorthand_count > 0){
		  while ($shorthand = mysql_fetch_array($get_shorthand)) {
		    $shorthand_id  = $shorthand['ID'];
		    $shorthandname = $shorthand['ShortHand'];
		    if($shorthand_id == $res['When']){
		    	$shorthand_options .= "<option value='$shorthand_id' selected>$shorthandname</option>";
		    } else {
		    	$shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
		    }    
		  }
		}
		$res['ShortHandOptions']				= $shorthand_options;

				//GET INSTRUCTION TYPE OPTIONS
	$instruction_type_options = "";
	$get_instruction_types = mysql_query("select InstructionTypeID, InstructionType from INSTRUCTION_TYPE order by InstructionType");
	$type_count = mysql_num_rows($get_instruction_types);
	if($type_count > 0){
	  while ($insttype = mysql_fetch_array($get_instruction_types)) {
	    $insttype_id     = $insttype['InstructionTypeID'];
	    $insttype_name   = $insttype['InstructionType'];
	    if($insttype_id == $res['InstructionTypeID']){
		    	$instruction_type_options .= "<option value='$insttype_id' selected>$insttype_name</option>";
		    } else {
		    	$instruction_type_options .= "<option value='$insttype_id'>$insttype_name</option>";
		    }   
	  }
	}
	$res['InstructionTypeOptions']				= $instruction_type_options;

		//INSTRUCTION SELECT BOX
				$instruction_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Instruction'] == "Before Food"){
					$instruction_options .= "<option value='Before Food' selected>Before Food</option>";
				} else {
					$instruction_options .= "<option value='Before Food'>Before Food</option>";
				}
				if($res['Instruction'] == "With Food"){
					$instruction_options .= "<option value='With Food' selected>With Food</option>";
				} else {
					$instruction_options .= "<option value='With Food'>With Food</option>";
				}
				if($res['Instruction'] == "After Food"){
					$instruction_options .= "<option value='After Food' selected>After Food</option>";
				} else {
					$instruction_options .= "<option value='After Food'>After Food</option>";
				}
				if($res['Instruction'] == "NA"){
					$instruction_options .= "<option value='NA' selected>Not Applicable</option>";
				} else {
					$instruction_options .= "<option value='NA'>Not Applicable</option>";
				}
			$res['InstructionOptions']				= $instruction_options;	

		//FREQUENCY SELECT BOX
			$frequency_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Frequency'] == "Once"){
					$frequency_options .= "<option value='Once' selected>Once</option>";
				} else {
					$frequency_options .= "<option value='Once'>Once</option>";
				}
				if($res['Frequency'] == "Daily"){
					$frequency_options .= "<option value='Daily' selected>Daily</option>";
				} else {
					$frequency_options .= "<option value='Daily'>Daily</option>";
				}
				if($res['Frequency'] == "Weekly"){
					$frequency_options .= "<option value='Weekly' selected>Weekly</option>";
				} else {
					$frequency_options .= "<option value='Weekly'>Weekly</option>";
				}
				if($res['Frequency'] == "Monthly"){
					$frequency_options .= "<option value='Monthly' selected>Monthly</option>";
				} else {
					$frequency_options .= "<option value='Monthly'>Monthly</option>";
				}
			$res['FrequencyOptions']				= $frequency_options;

			//HOW LONG TYPE SELECT BOX
			$howlongtype_options = "<option value='0' style='display:none;'>select</option>";
				if($res['HowLongType'] == "Days"){
					$howlongtype_options .= "<option value='Days' selected>Days</option>";
				} else {
					$howlongtype_options .= "<option value='Days'>Days</option>";
				}
				if($res['HowLongType'] == "Weeks"){
					$howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
				} else {
					$howlongtype_options .= "<option value='Weeks'>Weeks</option>";
				}
				if($res['HowLongType'] == "Months"){
					$howlongtype_options .= "<option value='Months' selected>Months</option>";
				} else {
					$howlongtype_options .= "<option value='Months'>Months</option>";
				}
			$res['HowLongTypeOptions']				= $howlongtype_options;
		array_push($prescs,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($prescs) . ');';	
 	//echo json_encode($prescs);
 } elseif($type == "get_master_appointments"){
 	//echo "1";
 	$plancode = $_REQUEST['plancode'];
 	$appotime = date('H:i:s',strtotime($_REQUEST['time']));
 	$appodate = date('Y-m-d',strtotime($_REQUEST['date']));
 	$appos   = array();
 	$get_appo_query = "select  PlanCode, AppointmentShortName, DoctorsName, AppointmentDate, AppointmentTime, AppointmentRequirements, AppointmentDuration, AppointmentPlace from APPOINTMENT_DETAILS where PlanCode = '$plancode' and AppointmentDate='$appodate' and AppointmentTime = '$appotime'";
 	//echo $get_appo_query;exit;
 	$get_appo_run = mysql_query($get_appo_query);
 	$get_appo_count = mysql_num_rows($get_appo_run);
 	if($get_appo_count > 0){
 		while ($appo_row = mysql_fetch_array($get_appo_run)) {
 			$res['PlanCode'] 				= $appo_row['PlanCode'];
 			$res['AppointmentShortName'] 	= $appo_row['AppointmentShortName'];
 			$res['DoctorsName'] 			= $appo_row['DoctorsName'];
 			$res['AppointmentDate'] 		= date('d-M-Y',strtotime($appo_row['AppointmentDate']));
 			$res['AppointmentTime'] 		= $appo_row['AppointmentTime'];
 			$res['AppointmentDuration'] 	= $appo_row['AppointmentDuration'];
 			$dur_array            			= explode(":", $res['AppointmentDuration']);
            $dur_inh              			= $dur_array[0];
            $dur_inm              			= $dur_array[1];
            $houroptions  					= "";
            $houroptions 					.= "<option value='00'";
            if($dur_inh == '00'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >0 Hrs</option>";
        	$houroptions 					.= "<option value='01'";
            if($dur_inh == '01'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >1 Hr</option>";
        	$houroptions 					.= "<option value='02'";
            if($dur_inh == '02'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >2 Hrs</option>";
        	$houroptions 					.= "<option value='03'";
            if($dur_inh == '03'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >3 Hrs</option>";
        	$houroptions 					.= "<option value='04'";
            if($dur_inh == '04'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >4 Hrs</option>";
        	$houroptions 					.= "<option value='05'";
            if($dur_inh == '05'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >5 Hrs</option>";
        	$houroptions 					.= "<option value='06'";
            if($dur_inh == '06'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >6 Hrs</option>";
        	$houroptions 					.= "<option value='07'";
            if($dur_inh == '07'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >7 Hrs</option>";
        	$houroptions 					.= "<option value='08'";
            if($dur_inh == '08'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >8 Hrs</option>";
        	$houroptions 					.= "<option value='09'";
            if($dur_inh == '09'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >9 Hrs</option>";
        	$houroptions 					.= "<option value='10'";
            if($dur_inh == '10'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >10 Hrs</option>";
        	$houroptions 					.= "<option value='11'";
            if($dur_inh == '11'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >11 Hrs</option>";
        	$houroptions 					.= "<option value='12'";
            if($dur_inh == '12'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >12 Hrs</option>";
            $minuteoptions 					= "";
            $minuteoptions 					.= "<option value='00'";
            if($dur_inh == '00'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >0 mins</option>";
        	$minuteoptions 					.= "<option value='05'";
            if($dur_inh == '05'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >5 mins</option>";
        	$minuteoptions 					.= "<option value='10'";
            if($dur_inh == '10'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >10 mins</option>";
        	$minuteoptions 					.= "<option value='15'";
            if($dur_inh == '15'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >15 mins</option>";
        	$minuteoptions 					.= "<option value='20'";
            if($dur_inh == '20'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >20 mins</option>";
        	$minuteoptions 					.= "<option value='25'";
            if($dur_inh == '25'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >25 mins</option>";
        	$minuteoptions 					.= "<option value='30'";
            if($dur_inh == '30'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >30 mins</option>";
        	$minuteoptions 					.= "<option value='35'";
            if($dur_inh == '35'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >35 mins</option>";
        	$minuteoptions 					.= "<option value='40'";
            if($dur_inh == '40'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >40 mins</option>";
        	$minuteoptions 					.= "<option value='45'";
            if($dur_inh == '45'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >45 mins</option>";
        	$minuteoptions 					.= "<option value='50'";
            if($dur_inh == '50'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >50 mins</option>";
        	$minuteoptions 					.= "<option value='55'";
            if($dur_inh == '55'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >55 mins</option>";
 			$res['HourOptions'] 			= $houroptions;
 			$res['MinuteOptions'] 			= $minuteoptions;
 			$res['AppointmentPlace'] 		= $appo_row['AppointmentPlace'];
 			$res['AppointmentRequirements'] = $appo_row['AppointmentRequirements'];
 			array_push($appos,$res);
 		}
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($appos) . ');';	
 }
elseif($type == "get_assigned_appointments"){
 	//echo "1";
 	$plancode = $_REQUEST['plancode'];
 	$userid   = $_REQUEST['userid'];
 	$appotime = date('H:i:s',strtotime($_REQUEST['time']));
 	$appodate = date('Y-m-d',strtotime($_REQUEST['date']));
 	$appos   = array();
 	$get_appo_query = "select  PlanCode, AppointmentShortName, DoctorsName, AppointmentDate, AppointmentTime, AppointmentRequirements, AppointmentDuration, AppointmentPlace from USER_APPOINTMENT_DETAILS where PlanCode = '$plancode' and AppointmentDate='$appodate' and AppointmentTime = '$appotime' and UserID = '$userid'";
 	//echo $get_appo_query;exit;
 	$get_appo_run = mysql_query($get_appo_query);
 	$get_appo_count = mysql_num_rows($get_appo_run);
 	if($get_appo_count > 0){
 		while ($appo_row = mysql_fetch_array($get_appo_run)) {
 			$res['PlanCode'] 				= $appo_row['PlanCode'];
 			$res['AppointmentShortName'] 	= $appo_row['AppointmentShortName'];
 			$res['DoctorsName'] 			= $appo_row['DoctorsName'];
 			$res['AppointmentDate'] 		= date('d-M-Y',strtotime($appo_row['AppointmentDate']));
 			$res['AppointmentTime'] 		= $appo_row['AppointmentTime'];
 			$res['AppointmentDuration'] 	= $appo_row['AppointmentDuration'];
 			$dur_array            			= explode(":", $res['AppointmentDuration']);
            $dur_inh              			= $dur_array[0];
            $dur_inm              			= $dur_array[1];
            $houroptions  					= "";
            $houroptions 					.= "<option value='00'";
            if($dur_inh == '00'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >0 Hrs</option>";
        	$houroptions 					.= "<option value='01'";
            if($dur_inh == '01'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >1 Hr</option>";
        	$houroptions 					.= "<option value='02'";
            if($dur_inh == '02'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >2 Hrs</option>";
        	$houroptions 					.= "<option value='03'";
            if($dur_inh == '03'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >3 Hrs</option>";
        	$houroptions 					.= "<option value='04'";
            if($dur_inh == '04'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >4 Hrs</option>";
        	$houroptions 					.= "<option value='05'";
            if($dur_inh == '05'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >5 Hrs</option>";
        	$houroptions 					.= "<option value='06'";
            if($dur_inh == '06'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >6 Hrs</option>";
        	$houroptions 					.= "<option value='07'";
            if($dur_inh == '07'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >7 Hrs</option>";
        	$houroptions 					.= "<option value='08'";
            if($dur_inh == '08'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >8 Hrs</option>";
        	$houroptions 					.= "<option value='09'";
            if($dur_inh == '09'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >9 Hrs</option>";
        	$houroptions 					.= "<option value='10'";
            if($dur_inh == '10'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >10 Hrs</option>";
        	$houroptions 					.= "<option value='11'";
            if($dur_inh == '11'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >11 Hrs</option>";
        	$houroptions 					.= "<option value='12'";
            if($dur_inh == '12'){$houroptions .= " selected ";	}
        	$houroptions 					.= " >12 Hrs</option>";
            $minuteoptions 					= "";
            $minuteoptions 					.= "<option value='00'";
            if($dur_inh == '00'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >0 mins</option>";
        	$minuteoptions 					.= "<option value='05'";
            if($dur_inh == '05'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >5 mins</option>";
        	$minuteoptions 					.= "<option value='10'";
            if($dur_inh == '10'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >10 mins</option>";
        	$minuteoptions 					.= "<option value='15'";
            if($dur_inh == '15'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >15 mins</option>";
        	$minuteoptions 					.= "<option value='20'";
            if($dur_inh == '20'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >20 mins</option>";
        	$minuteoptions 					.= "<option value='25'";
            if($dur_inh == '25'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >25 mins</option>";
        	$minuteoptions 					.= "<option value='30'";
            if($dur_inh == '30'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >30 mins</option>";
        	$minuteoptions 					.= "<option value='35'";
            if($dur_inh == '35'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >35 mins</option>";
        	$minuteoptions 					.= "<option value='40'";
            if($dur_inh == '40'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >40 mins</option>";
        	$minuteoptions 					.= "<option value='45'";
            if($dur_inh == '45'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >45 mins</option>";
        	$minuteoptions 					.= "<option value='50'";
            if($dur_inh == '50'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >50 mins</option>";
        	$minuteoptions 					.= "<option value='55'";
            if($dur_inh == '55'){$minuteoptions .= " selected ";	}
        	$minuteoptions 					.= " >55 mins</option>";
 			$res['HourOptions'] 			= $houroptions;
 			$res['MinuteOptions'] 			= $minuteoptions;
 			$res['AppointmentPlace'] 		= $appo_row['AppointmentPlace'];
 			$res['AppointmentRequirements'] = $appo_row['AppointmentRequirements'];
 			array_push($appos,$res);
 		}
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($appos) . ');';	
 }
 elseif($type == "get_master_labtests"){
 	//echo "1";
 	$plancode = $_REQUEST['plancode'];
 	$testid =$_REQUEST['testid'];
 	$rowno = $_REQUEST['rowno'];
 	$labs   = array();
 	$get_labs_query = "select PlanCode, LabTestID, RowNo, TestName, DoctorsName, LabTestRequirements from LAB_TEST_DETAILS1 where PlanCode = '$plancode' and LabTestID='$testid' and RowNo = '$rowno'";
 	//echo $get_labs_query;exit;
 	$get_labs_run = mysql_query($get_labs_query);
 	$get_labs_count = mysql_num_rows($get_labs_run);
 	if($get_labs_count > 0){
 		while ($lab_row = mysql_fetch_array($get_labs_run)) {
 			  $res['LabTestID']     		= $lab_row['LabTestID'];
              $res['RowNo']     			= $lab_row['RowNo'];
              $res['TestName']    			= $lab_row['TestName'];
              $res['DoctorsName']     		= $lab_row['DoctorsName'];
              $res['PlanCode']    			= $lab_row['PlanCode'];
              $res['LabTestRequirements']   = $lab_row['LabTestRequirements'];
 			array_push($labs,$res);
 		}
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($labs) . ');';	
 }
elseif($type == "get_assigned_labtests"){
 	//echo "1";
 	$plancode 	= $_REQUEST['plancode'];
 	$testid 	= $_REQUEST['testid'];
 	$rowno 		= $_REQUEST['rowno'];
 	$userid 	= $_REQUEST['userid'];
 	$labs   = array();
 	$get_labs_query = "select PlanCode, LabTestID, RowNo, TestName, DoctorsName, LabTestRequirements from USER_LAB_TEST_DETAILS1 where PlanCode = '$plancode' and LabTestID='$testid' and RowNo = '$rowno' and UserID = '$userid'";
 	//echo $get_labs_query;exit;
 	$get_labs_run = mysql_query($get_labs_query);
 	$get_labs_count = mysql_num_rows($get_labs_run);
 	if($get_labs_count > 0){
 		while ($lab_row = mysql_fetch_array($get_labs_run)) {
 			  $res['LabTestID']     		= $lab_row['LabTestID'];
              $res['RowNo']     			= $lab_row['RowNo'];
              $res['TestName']    			= $lab_row['TestName'];
              $res['DoctorsName']     		= $lab_row['DoctorsName'];
              $res['PlanCode']    			= $lab_row['PlanCode'];
              $res['LabTestRequirements']   = $lab_row['LabTestRequirements'];
 			array_push($labs,$res);
 		}
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($labs) . ');';	
 }
  elseif($type == "get_master_selftests"){
 	//echo "1";
 	$plancode = $_REQUEST['plancode'];
 	$testid =$_REQUEST['testid'];
 	$rowno = $_REQUEST['rowno'];
 	$self   = array();
 	$get_self = "select PlanCode, SelfTestID, RowNo, MedicalTestID, TestName, DoctorsName, TestDescription, Instruction, Frequency, FrequencyString, HowLong, HowLongType, Link, OriginalFileName, ResponseRequired, StartFlag, NoOfDaysAfterPlanStarts, SpecificDate from SELF_TEST_DETAILS where PlanCode='$plancode' and SelfTestID = '$testid' and RowNo = '$rowno'";
  //echo $get_self;exit;
  $get_self_run = mysql_query($get_self);
  $get_self_count = mysql_num_rows($get_self_run);
  //echo $get_self_count;exit;
  while ($selfrow = mysql_fetch_array($get_self_run)) {
    $res['PlanCode']               = $selfrow['PlanCode'];
    $res['SelfTestID']             = $selfrow['SelfTestID'];
    $res['RowNo']                  = $selfrow['RowNo'];
    $res['MedicalTestID']          = $selfrow['MedicalTestID'];
     $res['TestName']               = $selfrow['TestName'];
        $res['TestNameStyle']          = "style='height:40px;width:100%;'";
    if($res['MedicalTestID'] != "0"){
      $res['TestName']               = "";
      $res['TestNameStyle']          = "disabled style='height:40px;width:100%;opacity:0.2;'";
    }
    $res['DoctorsName']            = $selfrow['DoctorsName'];
    $res['TestDescription']        = $selfrow['TestDescription'];
    $res['Instruction']            = $selfrow['Instruction'];
    $res['Frequency']              = $selfrow['Frequency'];
    $res['FrequencyString']      	= (empty($selfrow['FrequencyString']))? '' : $selfrow['FrequencyString'];
    $res['WeeklyType']         = "type='hidden'";
    $res['MonthlyType']        = "type='hidden'";
    if($res['Frequency']== "Weekly"){
      $res['WeeklyType']= "type='text'";
    }
    if($res['Frequency']== "Monthly"){
      $res['MonthlyType']= "type='text'";
    }
    $res['HowLong']          = (empty($selfrow['HowLong']))  ? '' : $selfrow['HowLong'];
    $res['HowLongType']        = $selfrow['HowLongType'];
    $res['StartFlag']          = $selfrow['StartFlag'];
    // $res['NoOfDaysAfterPlanStarts']  = "";
    // $res['SpecificDate']       = "";
    // if($res['StartFlag']== "PS"){
    //   $res['PlanStartRadio']   = "checked";
    // } else {
    //   $res['PlanStartRadio']   = "";
    // }
    // if($res['StartFlag']== "SD"){
    //   $res['SpecificDateRadio']  = "checked";
    //   $res['SpecificDate']   = $selfrow['SpecificDate'];
    //   if(($res['SpecificDate']   == "0000-00-00")||($res['SpecificDate'] == "")){
    //     $res['SpecificDate']   = "";
    //   } else {
    //     $res['SpecificDate'] = date('d-M-Y',strtotime($res['SpecificDate']));
    //   }
    // } else {
    //   $res['SpecificDateRadio']  = "";
    // }
    // if($res['StartFlag'] == "ND"){
    //   $res['NumOfDaysRadio'] = "checked";
    //   $res['NoOfDaysAfterPlanStarts']    = $selfrow['NoOfDaysAfterPlanStarts'];
    // } else {
    //   $res['NumOfDaysRadio'] = "";
    // }
    $res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";
		$res['PSClass'] = "";
		$res['SDClass'] = "";
		$res['NDClass'] = "";
		if($res['StartFlag'] == "PS"){
			$res['PSClass'] = "";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "pointernone";
			$res['PlanStartRadio'] = "checked";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "";
			$res['NDClass'] = "pointernone";
			$res['SpecificDate']				= $selfrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['NumOfDaysRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "";
			$res['NoOfDaysAfterPlanStarts']		= $selfrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
    $res['Link']           		= (empty($selfrow['Link']))  ? '' : $selfrow['Link'];
    $res['OriginalFileName']  = (empty($selfrow['OriginalFileName']))  ? '' : $selfrow['OriginalFileName'];

    //MEDICAL TESTS SELECT BOX
  $MedicalTestOptions = "";
  $get_medical_test = mysql_query("select ID, TestName from MEDICAL_TESTS order by TestName");
  $get_medical_test_count = mysql_num_rows($get_medical_test);
  if($get_medical_test_count > 0){
    while ($medical_test = mysql_fetch_array($get_medical_test)) {
      $medical_test_id  = $medical_test['ID'];
      $medical_test_name = $medical_test['TestName'];
      if($res['MedicalTestID'] == $medical_test_id){
            $MedicalTestOptions .= "<option value='$medical_test_id' selected>$medical_test_name</option>";
      } else {
            $MedicalTestOptions .= "<option value='$medical_test_id'>$medical_test_name</option>";
      }
      
    }
}
  $res['MedicalTestOptions'] = $MedicalTestOptions;

    //INSTRUCTION SELECT BOX
  $InstructionOptions = "";
  $get_instruction = mysql_query("select InstructionID, Instruction from INSTRUCTION_MASTER where InstructionID!='20'");
  $instruction_count = mysql_num_rows($get_instruction);
  if($instruction_count > 0){
    while ($instruction = mysql_fetch_array($get_instruction)) {
      $instruction_id  = $instruction['InstructionID'];
      $instructionname = $instruction['Instruction'];
      if($res['Instruction'] == $instruction_id){
            $InstructionOptions .= "<option value='$instruction_id' selected>$instructionname</option>";
      } else {
            $InstructionOptions .= "<option value='$instruction_id'>$instructionname</option>";
      }
      
    }
}
  if($res['MedicalTestID'] == "5"){
    $InstructionOptions = "";
        if($res['Instruction'] == "5"){
          $InstructionOptions .= "<option value='5' selected>After Breakfast</option>";
        } else {
          $InstructionOptions .= "<option value='5'>After Breakfast</option>";
        }
        if($res['Instruction'] == "9"){
          $InstructionOptions .= "<option value='9' selected>After Lunch</option>";
        } else {
          $InstructionOptions .= "<option value='9'>After Lunch</option>";
        }
        if($res['Instruction'] == "18"){
          $InstructionOptions .= "<option value='18' selected>After Dinner</option>";
        } else {
          $InstructionOptions .= "<option value='18'>After Dinner</option>";
        }
      }
  $res['InstructionOptions'] = $InstructionOptions;
//echo $instruction_id;
    //FREQUENCY SELECT BOX
      $frequency_options = "<option value='0' style='display:none;'>select</option>";
        if($res['Frequency'] == "Once"){
          $frequency_options .= "<option value='Once' selected>Once</option>";
        } else {
          $frequency_options .= "<option value='Once'>Once</option>";
        }
        if($res['Frequency']== "Daily"){
          $frequency_options .= "<option value='Daily' selected>Daily</option>";
        } else {
          $frequency_options .= "<option value='Daily'>Daily</option>";
        }
        if($res['Frequency'] == "Weekly"){
          $frequency_options .= "<option value='Weekly' selected>Weekly</option>";
        } else {
          $frequency_options .= "<option value='Weekly'>Weekly</option>";
        }
        if($res['Frequency'] == "Monthly"){
          $frequency_options .= "<option value='Monthly' selected>Monthly</option>";
        } else {
          $frequency_options .= "<option value='Monthly'>Monthly</option>";
        }
      $FrequencyOptions       = $frequency_options;
      $res['FrequencyOptions'] = $FrequencyOptions;

      //HOW LONG TYPE SELECT BOX
      $howlongtype_options = "<option value='0' style='display:none;'>select</option>";
        if($res['HowLongType']== "Days"){
          $howlongtype_options .= "<option value='Days' selected>Days</option>";
        } else {
          $howlongtype_options .= "<option value='Days'>Days</option>";
        }
        if($res['HowLongType']== "Weeks"){
          $howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
        } else {
          $howlongtype_options .= "<option value='Weeks'>Weeks</option>";
        }
        if($res['HowLongType']== "Months"){
          $howlongtype_options .= "<option value='Months' selected>Months</option>";
        } else {
          $howlongtype_options .= "<option value='Months'>Months</option>";
        }
      $HowLongTypeOptions       = $howlongtype_options;
      $res['HowLongTypeOptions'] = $HowLongTypeOptions;
		array_push($self,$res);
		}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($self) . ');';	
 }
elseif($type == "get_assigned_selftests"){
 	//echo "1";
 	$plancode 	= $_REQUEST['plancode'];
 	$testid 	= $_REQUEST['testid'];
 	$rowno 		= $_REQUEST['rowno'];
 	$userid 	= $_REQUEST['userid'];
 	$self   = array();
 	$get_self = "select PlanCode, SelfTestID, RowNo, TestName,MedicalTestID, DoctorsName, TestDescription, InstructionID, Frequency, FrequencyString, HowLong, HowLongType, Link, OriginalFileName, ResponseRequired, StartFlag, NoOfDaysAfterPlanStarts, SpecificDate from USER_SELF_TEST_DETAILS where PlanCode='$plancode' and SelfTestID = '$testid' and RowNo = '$rowno' and UserID='$userid'";
 //echo $get_self;exit;
  $get_self_run = mysql_query($get_self);
  $get_self_count = mysql_num_rows($get_self_run);
  //echo $get_self_count;exit;
  while ($selfrow = mysql_fetch_array($get_self_run)) {
    $res['PlanCode']               = $selfrow['PlanCode'];
    $res['SelfTestID']             = $selfrow['SelfTestID'];
    $res['RowNo']                  = $selfrow['RowNo'];
    $res['MedicalTestID']          = $selfrow['MedicalTestID'];
    $res['TestName']               = $selfrow['TestName'];
        $res['TestNameStyle']          = "style='height:40px;width:100%;'";
    if($res['MedicalTestID'] != "0"){
      $res['TestName']               = "";
      $res['TestNameStyle']          = "disabled style='height:40px;width:100%;opacity:0.2;'";
    }
    $res['DoctorsName']            = $selfrow['DoctorsName'];
    $res['TestDescription']        = $selfrow['TestDescription'];
    $res['Instruction']            = $selfrow['InstructionID'];
    $res['Frequency']              = $selfrow['Frequency'];
    $res['FrequencyString']      	= (empty($selfrow['FrequencyString']))? '' : $selfrow['FrequencyString'];
    $res['WeeklyType']         = "type='hidden'";
    $res['MonthlyType']        = "type='hidden'";
    if($res['Frequency']== "Weekly"){
      $res['WeeklyType']= "type='text'";
    }
    if($res['Frequency']== "Monthly"){
      $res['MonthlyType']= "type='text'";
    }
    $res['HowLong']          = (empty($selfrow['HowLong']))  ? '' : $selfrow['HowLong'];
    $res['HowLongType']        = $selfrow['HowLongType'];
    $res['StartFlag']          = $selfrow['StartFlag'];
    // $res['NoOfDaysAfterPlanStarts']  = "";
    // $res['SpecificDate']       = "";
    // if($res['StartFlag']== "PS"){
    //   $res['PlanStartRadio']   = "checked";
    // } else {
    //   $res['PlanStartRadio']   = "";
    // }
    // if($res['StartFlag']== "SD"){
    //   $res['SpecificDateRadio']  = "checked";
    //   $res['SpecificDate']   = $selfrow['SpecificDate'];
    //   if(($res['SpecificDate']   == "0000-00-00")||($res['SpecificDate'] == "")){
    //     $res['SpecificDate']   = "";
    //   } else {
    //     $res['SpecificDate'] = date('d-M-Y',strtotime($res['SpecificDate']));
    //   }
    // } else {
    //   $res['SpecificDateRadio']  = "";
    // }
    // if($res['StartFlag'] == "ND"){
    //   $res['NumOfDaysRadio'] = "checked";
    //   $res['NoOfDaysAfterPlanStarts']    = $selfrow['NoOfDaysAfterPlanStarts'];
    // } else {
    //   $res['NumOfDaysRadio'] = "";
    // }
    $res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";
		$res['PSClass'] = "";
		$res['SDClass'] = "";
		$res['NDClass'] = "";
		if($res['StartFlag'] == "PS"){
			$res['PSClass'] = "";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "pointernone";
			$res['PlanStartRadio'] = "checked";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "";
			$res['NDClass'] = "pointernone";
			$res['SpecificDate']				= $selfrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['NumOfDaysRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "";
			$res['NoOfDaysAfterPlanStarts']		= $selfrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
    $res['Link']           = (empty($selfrow['Link']))  ? '' : $selfrow['Link'];
    $res['OriginalFileName']    = (empty($selfrow['OriginalFileName']))  ? '' : $selfrow['OriginalFileName'];
    //MEDICAL TESTS SELECT BOX
  $MedicalTestOptions = "";
  $get_medical_test = mysql_query("select ID, TestName from MEDICAL_TESTS order by TestName");
  $get_medical_test_count = mysql_num_rows($get_medical_test);
  if($get_medical_test_count > 0){
    while ($medical_test = mysql_fetch_array($get_medical_test)) {
      $medical_test_id  = $medical_test['ID'];
      $medical_test_name = $medical_test['TestName'];
      if($res['MedicalTestID'] == $medical_test_id){
            $MedicalTestOptions .= "<option value='$medical_test_id' selected>$medical_test_name</option>";
      } else {
            $MedicalTestOptions .= "<option value='$medical_test_id'>$medical_test_name</option>";
      }
      
    }
}
  $res['MedicalTestOptions'] = $MedicalTestOptions;
    //INSTRUCTION SELECT BOX
  $InstructionOptions = "";
  $get_instruction = mysql_query("select InstructionID, Instruction from INSTRUCTION_MASTER where InstructionID!='20'");
  $instruction_count = mysql_num_rows($get_instruction);
  if($instruction_count > 0){
    while ($instruction = mysql_fetch_array($get_instruction)) {
      $instruction_id  = $instruction['InstructionID'];
      $instructionname = $instruction['Instruction'];
      if($res['Instruction'] == $instruction_id){
            $InstructionOptions .= "<option value='$instruction_id' selected>$instructionname</option>";
      } else {
            $InstructionOptions .= "<option value='$instruction_id'>$instructionname</option>";
      }
      
    }
}
  if($res['MedicalTestID'] == "5"){
    $InstructionOptions = "";
        if($res['Instruction'] == "5"){
          $InstructionOptions .= "<option value='5' selected>After Breakfast</option>";
        } else {
          $InstructionOptions .= "<option value='5'>After Breakfast</option>";
        }
        if($res['Instruction'] == "9"){
          $InstructionOptions .= "<option value='9' selected>After Lunch</option>";
        } else {
          $InstructionOptions .= "<option value='9'>After Lunch</option>";
        }
        if($res['Instruction'] == "18"){
          $InstructionOptions .= "<option value='18' selected>After Dinner</option>";
        } else {
          $InstructionOptions .= "<option value='18'>After Dinner</option>";
        }
      }
  $res['InstructionOptions'] = $InstructionOptions;
//echo $instruction_id;
    //FREQUENCY SELECT BOX
      $frequency_options = "<option value='0' style='display:none;'>select</option>";
        if($res['Frequency'] == "Once"){
          $frequency_options .= "<option value='Once' selected>Once</option>";
        } else {
          $frequency_options .= "<option value='Once'>Once</option>";
        }
        if($res['Frequency']== "Daily"){
          $frequency_options .= "<option value='Daily' selected>Daily</option>";
        } else {
          $frequency_options .= "<option value='Daily'>Daily</option>";
        }
        if($res['Frequency'] == "Weekly"){
          $frequency_options .= "<option value='Weekly' selected>Weekly</option>";
        } else {
          $frequency_options .= "<option value='Weekly'>Weekly</option>";
        }
        if($res['Frequency'] == "Monthly"){
          $frequency_options .= "<option value='Monthly' selected>Monthly</option>";
        } else {
          $frequency_options .= "<option value='Monthly'>Monthly</option>";
        }
      $FrequencyOptions       = $frequency_options;
      $res['FrequencyOptions'] = $FrequencyOptions;

      //HOW LONG TYPE SELECT BOX
      $howlongtype_options = "<option value='0' style='display:none;'>select</option>";
        if($res['HowLongType']== "Days"){
          $howlongtype_options .= "<option value='Days' selected>Days</option>";
        } else {
          $howlongtype_options .= "<option value='Days'>Days</option>";
        }
        if($res['HowLongType']== "Weeks"){
          $howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
        } else {
          $howlongtype_options .= "<option value='Weeks'>Weeks</option>";
        }
        if($res['HowLongType']== "Months"){
          $howlongtype_options .= "<option value='Months' selected>Months</option>";
        } else {
          $howlongtype_options .= "<option value='Months'>Months</option>";
        }
      $HowLongTypeOptions       = $howlongtype_options;
      $res['HowLongTypeOptions'] = $HowLongTypeOptions;
		array_push($self,$res);
		}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($self) . ');';	
 }
 elseif ($type == "edit_assigned_plan_from_dashboard") {
	$userid 	= $_REQUEST['userid'];
	$plancode 	= $_REQUEST['plancode'];
	$page  		= $_REQUEST['page'];
	$_SESSION['current_assigned_user_id'] = $userid;
	$_SESSION['userid_for_current_plan'] = $userid;
	$_SESSION['current_assigned_plan_code'] = $plancode;
	$_SESSION['plancode_for_current_plan'] = $plancode;
	if($page == "appo"){
		header("location:cust_appo_new.php");
	}elseif ($page == "inst") {
		header("location:cust_inst_new.php");
	}
	elseif ($page == "goal") {
		header("location:cust_goals.php");
	}else{
		header("location:cust_med_new.php");
	}
}
elseif ($type == "get_selftest_values") {
	$userid 			= $_REQUEST['userid'];
	$selfgraphs 		= array();
	$selftestarray 		= array();
	$selftestvaluearray = array();
	$selftestvalues = "";
	$get_distinct_selftests = mysql_query("select distinct PlanCode, SelfTestID, RowNo from USER_SELF_TEST_DATA_FROM_CLIENT where UserID = '$userid'");
	$get_selftest_count = mysql_num_rows($get_distinct_selftests);
	$count = 0;
	if($get_selftest_count > 0){
		while ($distinctselftests = mysql_fetch_array($get_distinct_selftests)) {
			$plancode 		= $distinctselftests['PlanCode'];
			$selftestid 	= $distinctselftests['SelfTestID'];
			$rowno   		= $distinctselftests['RowNo'];
			$get_selftest_query = mysql_query("select SNo, ResponseDataName, ResponseDataValue from USER_SELF_TEST_DATA_FROM_CLIENT where UserID = '$userid' and PlanCode = '$plancode' and SelfTestID = '$selftestid' and RowNo = '$rowno'");
			$selftestvalues = array();
					while ($values = mysql_fetch_array($get_selftest_query)) {
						$slno 			= $values['SNo'];
						$respdataname 	= $values['ResponseDataName'];
						$respdatavalue  = $values['ResponseDataValue'];
						$selftestarray[$count]  = $respdataname;
						//$selftestvalues .= "[''~$respdatavalue]~";
						array_push($selftestvalues, $respdatavalue);
					}
					//$selftestvalues = rtrim($selftestvalues,"~");
			$selftestvaluearray[$count]  = $selftestvalues;
			$count++;
			}
		}
		$res['graphcount'] 				= $count;
		$res['selftestarray'] 			= $selftestarray;
		$res['selftestvaluearray']   	= $selftestvaluearray;
		//print_r($selftestarray);
		//print_r($selftestvaluearray);
		array_push($selfgraphs,$res);
		echo $_REQUEST['jsoncallback'] . '(' . json_encode($selfgraphs) . ');';	
	}
elseif ($type == "delete_report") 
{
	$userid 			= $_REQUEST['userid'];
	$childid 			= $_REQUEST['child'];
	$report_given_by 	= $_REQUEST['report_given_by'];
	$logged_companyname	= $_REQUEST['logged_companyname'];
	$parentid 			= "";
	$get_parent_id      = mysql_query("select MyFolderParentID,FileName from MYFOLDER_CHILD where ID='$childid'");
	$get_parent_id_num  = mysql_num_rows($get_parent_id);
	if($get_parent_id_num > 0){
		while ($row = mysql_fetch_array($get_parent_id)) {
			$parentid 	= $row['MyFolderParentID'];
			$filename 	= $row['FileName'];
		}
	}
	//echo $parentid;
	$deactivate_child 		= mysql_query("update MYFOLDER_CHILD set Status = 'I' where ID = '$childid'");
	$check_deactivate		= mysql_affected_rows();
	if($check_deactivate)
	{

		        /*GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/
    $country_id         = "";   $country_code="";   $mobile_no="";
    $get_folder_name    = "select substr(MobileNo,1,5) as CountryCode,substr(MobileNo,6) as MobileNo from USER_ACCESS where UserID='$userid'";
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
				if(file_exists($path.$filename)){
                    unlink($path.$filename);
                }

		/*SEND NOTIFICATION TO USER*/
          $get_user    =   "select UserID,OSType,DeviceID,PaidUntil from USER_ACCESS where UserID='$userid'";
          //echo $get_user;exit;
          $get_user_qry  = mysql_query($get_user);
          $get_user_count= mysql_num_rows($get_user_qry);
            if($get_user_count)
            {//echo 123;exit;
            $msg = "A report wrongly uploaded by $report_given_by of $logged_companyname is being deleted";
            //echo $msg;exit;
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
                  $regId          	= $user_device_id;
                  $res['message'] 	= $msg;
                  $res['userid']  	= $userid;
                  $res['report_id'] = $parentid;
                  $res['filename']	= $filename;
                  $res['flag']    	= "report_delete";
                  $message        	= json_encode($res); 
                  //echo $message;exit;
                  include("gcm_server_php/send_message.php");
                  }
                  else if($user_os_type=='I' && $user_device_id!='')
                  {
                  $deviceToken= $user_device_id;
                  //echo "<br>";
                  $userid   	= $userid;
                  $report_id   	= $parentid;
                  $filename 	= $filename;
                  //echo "<br>";exit;
                  $flag     	= "report_delete";
                  $message  	= $msg;
                  include("apple/local/push.php");
                  //include("apple/production/push.php");
                  }
                }
               }
              }
            }
       }
       /*End of sending notification to user*/
	$get_num_of_children 	= mysql_query("select count(*) from MYFOLDER_CHILD where MyFolderParentID = '$parentid' and Status = 'A'");
	$childrencount 			= mysql_result($get_num_of_children, 0);
	//echo $childrencount;
	if($childrencount == 0){
		//echo "a";
		$deactivate_child 		= mysql_query("update MYFOLDER_PARENT set Status = 'I' where ID = '$parentid'");
	}
	header("location:client_dashboard.php?id=$userid");
}
elseif ($type == "remove_profile_picture"){
	$userid 			= $_REQUEST['userid'];
	//CHECK FOR DUPLICATE EMAIL ID
	$remove_profile_picture = mysql_query("update USER_DETAILS set ProfilePicture = NULL where UserID = '$userid'");
	//echo $remove_profile_picture;exit;
	$_SESSION['logged_userdp']			= "";
	$response['success'] 		= true;

echo json_encode($response);
}
elseif ($type == "remove_cover_image"){
	$plancode 			= $_REQUEST['plancode'];
	//CHECK FOR DUPLICATE EMAIL ID
	$remove_cover_image = mysql_query("update PLAN_HEADER set PlanCoverImagePath = NULL where PlanCode = '$plancode'");
	//echo $remove_cover_image;exit;
	$response['success'] 		= true;

echo json_encode($response);
}
elseif ($type == "check_for_notification"){
	$merchantid 	= $_REQUEST['merchant'];
	$get_notifications = mysql_query("select ID, MerchantID, UserID, Message from DESKTOP_NOTIFICATION where MerchantID = '$merchantid' and DisplayStatus = 'N'");
	//echo $get_notifications;exit;
	$get_notifications_count = mysql_num_rows($get_notifications);
	if($get_notifications_count > 0){
		while ($not_row = mysql_fetch_array($get_notifications)) {
			$not_id     = $not_row['ID'];
			$userid  	= $not_row['UserID'];
			$message  	= $not_row['Message'];
			?>
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
  <script type="text/javascript">
    var message = "<?php echo $message;?>";
    var userid = "<?php echo $userid;?>";
    notifyMe(message,userid);
  <?php include('js/notification.js');?>
  </script>
  <?php
  //$update_notifications = mysql_query("update DESKTOP_NOTIFICATION set DisplayStatus = 'Y' where ID = '$not_id'");

		}
	}
	$response['success'] 		= true;

echo json_encode($response);
}
elseif ($type == "get_notifications"){
$merchantid 	= $_REQUEST['merchant'];
$notifications   = array();
	$get_notifications = mysql_query("select ID, MerchantID, UserID, Message from DESKTOP_NOTIFICATION where MerchantID = '$merchantid' and DisplayStatus = 'N' limit 1");
	//echo $get_notifications;exit;
	$get_notifications_count = mysql_num_rows($get_notifications);
	if($get_notifications_count > 0){
		while ($not_row = mysql_fetch_array($get_notifications)) {
			$res['ID']     		= $not_row['ID'];
			$not_id				= $not_row['ID'];
			$res['UserID']  	= $not_row['UserID'];
			$res['Message'] 	= $not_row['Message'];
			array_push($notifications,$res);
			$update_notifications = mysql_query("update DESKTOP_NOTIFICATION set DisplayStatus = 'Y' where ID = '$not_id'");
			}
	}
			echo $_REQUEST['jsoncallback'] . '(' . json_encode($notifications) . ');';	
}

elseif($type == 'get_master_prescriptions'){
 	$plancode = $_REQUEST['plancode'];
 	$prescno  = $_REQUEST['prescno'];
 	$merchantid = $_REQUEST['merchantid'];
 	$prescs   = array();
 	$get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`,`ThresholdLimit`, `MedicineCount`, `MedicineTypeID`, `When`, `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `Link`, `OriginalFileName`, `SpecificDate` from MEDICATION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno'";
 	//echo $get_presc;exit;
 	$get_presc_run = mysql_query($get_presc);
 	while ($prescrow = mysql_fetch_array($get_presc_run)) {
		$res['PlanCode'] 					= $prescrow['PlanCode'];
		$res['PrescriptionNo'] 				= $prescrow['PrescriptionNo'];
		$res['RowNo'] 						= $prescrow['RowNo'];
		$res['MedicineName']				= $prescrow['MedicineName'];
		//$res['ThresholdLimit']				= $prescrow['ThresholdLimit'];
		$res['ThresholdLimit']          	= (empty($prescrow['ThresholdLimit'])) ? '' : $prescrow['ThresholdLimit'];
		$res['MedicineCount']				= $prescrow['MedicineCount'];
		$res['MedicineTypeID']				= $prescrow['MedicineTypeID'];
		$res['When']						= $prescrow['When'];
		$res['SpecificTime']				= date('h:i A',strtotime($prescrow['SpecificTime']));
		$res['SpecificTimeType'] 				= "type='hidden'";
		if($res['When'] == '16'){
			$res['SpecificTimeType'] = "type='text'";
		}
		$res['Instruction']					= $prescrow['Instruction'];
		$res['Frequency']					= $prescrow['Frequency'];
		$res['FrequencyString']				= (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
		$res['WeeklyType'] 					= "type='hidden'";
		$res['MonthlyType'] 				= "type='hidden'";
		if($res['Frequency'] == "Weekly"){
			$res['WeeklyType'] = "type='text'";
		}
		if($res['Frequency'] == "Monthly"){
			$res['MonthlyType'] = "type='text'";
		}
	    $res['CountSelect1']     = "style='width:35px;height:30px;'";
	    $res['CountSelect2']     = "style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;";
	    if($res['Frequency'] == "Once"){
	      $res['CountSelect1']     = "disabled style='width:35px;height:30px;opacity:0.2;'";
	      $res['CountSelect2']     = "disabled style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;opacity:0.2;'";
	    }
		$res['HowLong']						= (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
		$res['HowLongType']					= $prescrow['HowLongType'];
		$res['IsCritical']					= $prescrow['IsCritical'];
		if($res['IsCritical'] == "Y"){
			$res['CriticalSelect'] 			= "checked";
		} else {
			$res['CriticalSelect'] 			= "";
		}
		$res['ThresholdInput']       		= "disabled style='width:35px;height:30px;opacity:0.2;'";
		$res['ResponseRequired']			= $prescrow['ResponseRequired'];
		if($res['ResponseRequired'] == "Y"){
			$res['ResponseSelect'] 			= "checked";
			$res['ThresholdInput']       	= "style='width:35px;height:30px;'";
		} else {
			$res['ResponseSelect'] 			= "";
		}
		$res['StartFlag']					= $prescrow['StartFlag'];
		$res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";
		$res['PSClass'] = "";
		$res['SDClass'] = "";
		$res['NDClass'] = "";
		if($res['StartFlag'] == "PS"){
			$res['PSClass'] = "";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "pointernone";
			$res['PlanStartRadio'] = "checked";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "";
			$res['NDClass'] = "pointernone";
			$res['SpecificDate']				= $prescrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['NumOfDaysRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "";
			$res['NoOfDaysAfterPlanStarts']		= $prescrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
		
		$res['Link']						= (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
		$res['OriginalFileName']			= (empty($prescrow['OriginalFileName']))  ? '' : $prescrow['OriginalFileName'];
		

		//GET DOCTOR SHORT HAND
		$shorthand_options = "";
		$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
		$shorthand_count = mysql_num_rows($get_shorthand);
		if($shorthand_count > 0){
		  while ($shorthand = mysql_fetch_array($get_shorthand)) {
		    $shorthand_id  = $shorthand['ID'];
		    $shorthandname = $shorthand['ShortHand'];
		    if($shorthand_id == $res['When']){
		    	$shorthand_options .= "<option value='$shorthand_id' selected>$shorthandname</option>";
		    } else {
		    	$shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
		    }    
		  }
		}
		$res['ShortHandOptions']				= $shorthand_options;

//GET MEDICINES
$get_medicines      = mysql_query("select MedicineID, GenericName, BrandName, Quantity, CompanyName, MedicineType from MEDICINE_LIST order by GenericName");
$medicine_count   = mysql_num_rows($get_medicines);
$medicine_options_exist   = "";
if($medicine_count > 0){
while ($medicines = mysql_fetch_array($get_medicines)) {
        $medicine_id        = $medicines['MedicineID'];
        $generic_name       = $medicines['GenericName'];
        $brand_name         = $medicines['BrandName'];
        $company_name       = $medicines['CompanyName'];
        $quantity           = $medicines['Quantity'];
        $medicine_type      = $medicines['MedicineType'];
        $display_name       = $generic_name." - ".$brand_name;
          if($medicine_type == 2){
             $display_name     .= " (".$quantity." )";
          }
      $display_name     .= " - ".$company_name;
    // $medicine_options   .= "<option value='$medicine_id'>$medicine_name</option>";  
      if($res['MedicineName'] == $brand_name){
    $medicine_options_exist   .= "<option value='$brand_name' selected>$display_name</option>";
    }
    else {
        $medicine_options_exist   .= "<option value='$brand_name'>$display_name</option>";       
    } 
}
}

	// //GET MEDICINES
 //    $get_medicines    = mysql_query("select ID, MedicineName from MERCHANT_MEDICINE_LIST where MerchantID in ('0', '$merchantid') order by MedicineName");
 //    $medicine_count   = mysql_num_rows($get_medicines);
 //    $medicine_options_exist   = "";
 //    if($medicine_count > 0){
 //      while ($medicines = mysql_fetch_array($get_medicines)) {
 //        $medicine_id    = $medicines['ID'];
 //        $medicine_name    = $medicines['MedicineName'];
 //        if($res['MedicineName'] == $medicine_name){
 //          $medicine_options_exist   .= "<option value='$medicine_name' selected>$medicine_name</option>";
 //        } else {
 //          $medicine_options_exist   .= "<option value='$medicine_name'>$medicine_name</option>";      
 //        }
 //      }
 //    }
	
	$res['MedicineNameOptions']				= $medicine_options_exist;
		//GET MEDICATION TYPES
		$medicine_type_options = "";
		$get_medicine_types = mysql_query("select SNo, MedicineType from MEDICINE_TYPES where SNo != '0'");
		$type_count = mysql_num_rows($get_medicine_types);
		if($type_count > 0){
		  while ($medtype = mysql_fetch_array($get_medicine_types)) {
		    $medtype_id     = $medtype['SNo'];
		    $medtype_name   = $medtype['MedicineType'];
		    if($medtype_id == $res['MedicineTypeID']){
		    $medicine_type_options .= "<option value='$medtype_id' selected>$medtype_name</option>";
			} else {
				$medicine_type_options .= "<option value='$medtype_id'>$medtype_name</option>";
			  }
		}
		}
		$res['MedicineTypeOptions']				= $medicine_type_options;

		//INSTRUCTION SELECT BOX
				$instruction_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Instruction'] == "Before Food"){
					$instruction_options .= "<option value='Before Food' selected>Before Food</option>";
				} else {
					$instruction_options .= "<option value='Before Food'>Before Food</option>";
				}
				if($res['Instruction'] == "With Food"){
					$instruction_options .= "<option value='With Food' selected>With Food</option>";
				} else {
					$instruction_options .= "<option value='With Food'>With Food</option>";
				}
				if($res['Instruction'] == "After Food"){
					$instruction_options .= "<option value='After Food' selected>After Food</option>";
				} else {
					$instruction_options .= "<option value='After Food'>After Food</option>";
				}
				if($res['Instruction'] == "NA"){
					$instruction_options .= "<option value='NA' selected>Not Applicable</option>";
				} else {
					$instruction_options .= "<option value='NA'>Not Applicable</option>";
				}
			$res['InstructionOptions']				= $instruction_options;	

		//FREQUENCY SELECT BOX
			$frequency_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Frequency'] == "Once"){
					$frequency_options .= "<option value='Once' selected>Once</option>";
				} else {
					$frequency_options .= "<option value='Once'>Once</option>";
				}
				if($res['Frequency'] == "Daily"){
					$frequency_options .= "<option value='Daily' selected>Daily</option>";
				} else {
					$frequency_options .= "<option value='Daily'>Daily</option>";
				}
				if($res['Frequency'] == "Weekly"){
					$frequency_options .= "<option value='Weekly' selected>Weekly</option>";
				} else {
					$frequency_options .= "<option value='Weekly'>Weekly</option>";
				}
				if($res['Frequency'] == "Monthly"){
					$frequency_options .= "<option value='Monthly' selected>Monthly</option>";
				} else {
					$frequency_options .= "<option value='Monthly'>Monthly</option>";
				}
			$res['FrequencyOptions']				= $frequency_options;

			//HOW LONG TYPE SELECT BOX
			$howlongtype_options = "<option value='0' style='display:none;'>select</option>";
				if($res['HowLongType'] == "Days"){
					$howlongtype_options .= "<option value='Days' selected>Days</option>";
				} else {
					$howlongtype_options .= "<option value='Days'>Days</option>";
				}
				if($res['HowLongType'] == "Weeks"){
					$howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
				} else {
					$howlongtype_options .= "<option value='Weeks'>Weeks</option>";
				}
				if($res['HowLongType'] == "Months"){
					$howlongtype_options .= "<option value='Months' selected>Months</option>";
				} else {
					$howlongtype_options .= "<option value='Months'>Months</option>";
				}
			$res['HowLongTypeOptions']				= $howlongtype_options;
		array_push($prescs,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($prescs) . ');';	
 	//echo json_encode($prescs);	
 }
  elseif($type == 'get_assigned_prescriptions'){
 	$plancode 	= $_REQUEST['plancode'];
 	$prescno  	= $_REQUEST['prescno'];
 	$userid  	= $_REQUEST['userid'];
 	$merchantid = $_REQUEST['merchantid'];
 	$prescs   = array();
 	$get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`,`ThresholdLimit`, `MedicineCount`, `MedicineTypeID`,  `When`, `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `Link`, `OriginalFileName`, `SpecificDate` from USER_MEDICATION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno' and UserID='$userid'";
 	//echo $get_presc;exit;
 	$get_presc_run = mysql_query($get_presc);
 	while ($prescrow = mysql_fetch_array($get_presc_run)) {
		$res['PlanCode'] 					= $prescrow['PlanCode'];
		$res['PrescriptionNo'] 				= $prescrow['PrescriptionNo'];
		$res['RowNo'] 						= $prescrow['RowNo'];
		$res['MedicineName']				= $prescrow['MedicineName'];
		//$res['ThresholdLimit']				= $prescrow['ThresholdLimit'];
		$res['ThresholdLimit']          	= (empty($prescrow['ThresholdLimit'])) ? '' : $prescrow['ThresholdLimit'];
		$res['MedicineCount']				= $prescrow['MedicineCount'];
		$res['MedicineTypeID']				= $prescrow['MedicineTypeID'];
		$res['When']						= $prescrow['When'];
		$res['SpecificTime']				= date('h:i A',strtotime($prescrow['SpecificTime']));
		$res['SpecificTimeType'] 				= "type='hidden'";
		if($res['When'] == '16'){
			$res['SpecificTimeType'] = "type='text'";
		}
		$res['Instruction']					= $prescrow['Instruction'];
		$res['Frequency']					= $prescrow['Frequency'];
		$res['FrequencyString']				= (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
		$res['WeeklyType'] 					= "type='hidden'";
		$res['MonthlyType'] 				= "type='hidden'";
		if($res['Frequency'] == "Weekly"){
			$res['WeeklyType'] = "type='text'";
		}
		if($res['Frequency'] == "Monthly"){
			$res['MonthlyType'] = "type='text'";
		}
		$res['CountSelect1']     = "style='width:35px;height:30px;'";
	    $res['CountSelect2']     = "style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;";
	    if($res['Frequency'] == "Once"){
	      $res['CountSelect1']     = "disabled style='width:35px;height:30px;opacity:0.2;'";
	      $res['CountSelect2']     = "disabled style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;opacity:0.2;'";
	    }
		$res['HowLong']						= (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
		$res['HowLongType']					= $prescrow['HowLongType'];
		$res['IsCritical']					= $prescrow['IsCritical'];
		if($res['IsCritical'] == "Y"){
			$res['CriticalSelect'] 			= "checked";
		} else {
			$res['CriticalSelect'] 			= "";
		}
		$res['ThresholdInput']       		= "disabled style='width:35px;height:30px;opacity:0.2;'";
		$res['ResponseRequired']			= $prescrow['ResponseRequired'];
		if($res['ResponseRequired'] == "Y"){
			$res['ResponseSelect'] 			= "checked";
			$res['ThresholdInput']       	= "style='width:35px;height:30px;'";
		} else {
			$res['ResponseSelect'] 			= "";
		}
		$res['StartFlag']					= $prescrow['StartFlag'];
		$res['NoOfDaysAfterPlanStarts'] = "";
		$res['SpecificDate']			= "";
		$res['PSClass'] = "";
		$res['SDClass'] = "";
		$res['NDClass'] = "";
		if($res['StartFlag'] == "PS"){
			$res['PlanStartRadio'] = "checked";
			$res['PSClass'] = "";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "pointernone";
		} else {
			$res['PlanStartRadio'] = "";
		}
		if($res['StartFlag'] == "SD"){
			$res['SpecificDateRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "";
			$res['NDClass'] = "pointernone";
			$res['SpecificDate']				= $prescrow['SpecificDate'];
			if(($res['SpecificDate'] == "0000-00-00")||($res['SpecificDate'] == "")){
				$res['SpecificDate'] = "";
			} else {
				$res['SpecificDate']				= date('d-M-Y',strtotime($res['SpecificDate']));
			}
		} else {
			$res['SpecificDateRadio'] = "";
		}
		if($res['StartFlag'] == "ND"){
			$res['NumOfDaysRadio'] = "checked";
			$res['PSClass'] = "pointernone";
			$res['SDClass'] = "pointernone";
			$res['NDClass'] = "";
			$res['NoOfDaysAfterPlanStarts']		= $prescrow['NoOfDaysAfterPlanStarts'];
		} else {
			$res['NumOfDaysRadio'] = "";
		}
		
		$res['Link']						= (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
		$res['OriginalFileName']			= (empty($prescrow['OriginalFileName']))  ? '' : $prescrow['OriginalFileName'];	

		//GET DOCTOR SHORT HAND
		$shorthand_options = "";
		$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
		$shorthand_count = mysql_num_rows($get_shorthand);
		if($shorthand_count > 0){
		  while ($shorthand = mysql_fetch_array($get_shorthand)) {
		    $shorthand_id  = $shorthand['ID'];
		    $shorthandname = $shorthand['ShortHand'];
		    if($shorthand_id == $res['When']){
		    	$shorthand_options .= "<option value='$shorthand_id' selected>$shorthandname</option>";
		    } else {
		    	$shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
		    }    
		  }
		}
		$res['ShortHandOptions']				= $shorthand_options;

//GET MEDICINES
$get_medicines      = mysql_query("select MedicineID, GenericName, BrandName, Quantity, CompanyName, MedicineType from MEDICINE_LIST order by GenericName");
$medicine_count   = mysql_num_rows($get_medicines);
$medicine_options_exist   = "";
if($medicine_count > 0){
while ($medicines = mysql_fetch_array($get_medicines)) {
        $medicine_id        = $medicines['MedicineID'];
        $generic_name       = $medicines['GenericName'];
        $brand_name         = $medicines['BrandName'];
        $company_name       = $medicines['CompanyName'];
        $quantity           = $medicines['Quantity'];
        $medicine_type      = $medicines['MedicineType'];
        $display_name       = $generic_name." - ".$brand_name;
          if($medicine_type == 2){
             $display_name     .= " (".$quantity." )";
          }
      $display_name     .= " - ".$company_name;
    // $medicine_options   .= "<option value='$medicine_id'>$medicine_name</option>";  
      if($res['MedicineName'] == $brand_name){
    $medicine_options_exist   .= "<option value='$brand_name' selected>$display_name</option>";
    }
    else {
        $medicine_options_exist   .= "<option value='$brand_name'>$display_name</option>";       
    } 
}
}

				//     //GET MEDICINES
    // $get_medicines    = mysql_query("select ID, MedicineName from MERCHANT_MEDICINE_LIST where MerchantID in ('0','$merchantid') order by MedicineName");
    // $medicine_count   = mysql_num_rows($get_medicines);
    // $medicine_options_exist   = "";
    // if($medicine_count > 0){
    //   while ($medicines = mysql_fetch_array($get_medicines)) {
    //     $medicine_id    = $medicines['ID'];
    //     $medicine_name    = $medicines['MedicineName'];
    //     if($res['MedicineName'] == $medicine_name){
    //       $medicine_options_exist   .= "<option value='$medicine_name' selected>$medicine_name</option>";
    //     } else {
    //       $medicine_options_exist   .= "<option value='$medicine_name'>$medicine_name</option>";      
    //     }
    //   }
    // }
$res['MedicineNameOptions']				= $medicine_options_exist;

		//GET MEDICATION TYPES
		$medicine_type_options = "";
		$get_medicine_types = mysql_query("select SNo, MedicineType from MEDICINE_TYPES where SNo != '0'");
		$type_count = mysql_num_rows($get_medicine_types);
		if($type_count > 0){
		  while ($medtype = mysql_fetch_array($get_medicine_types)) {
		    $medtype_id     = $medtype['SNo'];
		    $medtype_name   = $medtype['MedicineType'];
		    if($medtype_id == $res['MedicineTypeID']){
		    $medicine_type_options .= "<option value='$medtype_id' selected>$medtype_name</option>";
			} else {
				$medicine_type_options .= "<option value='$medtype_id'>$medtype_name</option>";
			  }
		}
		}
		$res['MedicineTypeOptions']				= $medicine_type_options;

		//INSTRUCTION SELECT BOX
				$instruction_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Instruction'] == "Before Food"){
					$instruction_options .= "<option value='Before Food' selected>Before Food</option>";
				} else {
					$instruction_options .= "<option value='Before Food'>Before Food</option>";
				}
				if($res['Instruction'] == "With Food"){
					$instruction_options .= "<option value='With Food' selected>With Food</option>";
				} else {
					$instruction_options .= "<option value='With Food'>With Food</option>";
				}
				if($res['Instruction'] == "After Food"){
					$instruction_options .= "<option value='After Food' selected>After Food</option>";
				} else {
					$instruction_options .= "<option value='After Food'>After Food</option>";
				}
				if($res['Instruction'] == "NA"){
					$instruction_options .= "<option value='NA' selected>Not Applicable</option>";
				} else {
					$instruction_options .= "<option value='NA'>Not Applicable</option>";
				}
			$res['InstructionOptions']				= $instruction_options;	

		//FREQUENCY SELECT BOX
			$frequency_options = "<option value='0' style='display:none;'>select</option>";
				if($res['Frequency'] == "Once"){
					$frequency_options .= "<option value='Once' selected>Once</option>";
				} else {
					$frequency_options .= "<option value='Once'>Once</option>";
				}
				if($res['Frequency'] == "Daily"){
					$frequency_options .= "<option value='Daily' selected>Daily</option>";
				} else {
					$frequency_options .= "<option value='Daily'>Daily</option>";
				}
				if($res['Frequency'] == "Weekly"){
					$frequency_options .= "<option value='Weekly' selected>Weekly</option>";
				} else {
					$frequency_options .= "<option value='Weekly'>Weekly</option>";
				}
				if($res['Frequency'] == "Monthly"){
					$frequency_options .= "<option value='Monthly' selected>Monthly</option>";
				} else {
					$frequency_options .= "<option value='Monthly'>Monthly</option>";
				}
			$res['FrequencyOptions']				= $frequency_options;

			//HOW LONG TYPE SELECT BOX
			$howlongtype_options = "<option value='0' style='display:none;'>select</option>";
				if($res['HowLongType'] == "Days"){
					$howlongtype_options .= "<option value='Days' selected>Days</option>";
				} else {
					$howlongtype_options .= "<option value='Days'>Days</option>";
				}
				if($res['HowLongType'] == "Weeks"){
					$howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
				} else {
					$howlongtype_options .= "<option value='Weeks'>Weeks</option>";
				}
				if($res['HowLongType'] == "Months"){
					$howlongtype_options .= "<option value='Months' selected>Months</option>";
				} else {
					$howlongtype_options .= "<option value='Months'>Months</option>";
				}
			$res['HowLongTypeOptions']				= $howlongtype_options;
		array_push($prescs,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($prescs) . ');';	
 	//echo json_encode($prescs);
 }

//GET PRICE BASED ON NO OF MONTHS IN DROPDOWN
elseif($type == 'get_price')
{
$no_of_months= $_REQUEST['no_of_months'];
//echo $center;exit;
$row = mysql_query("select PricingNo,NetPrice from PRICING_DETAILS where NoOfMonths='$no_of_months'");
	while($row2 = mysql_fetch_array($row))
	{
	$pricing_no     = $row2['PricingNo'];
	$net_price		= $row2['NetPrice'];
	}
echo $net_price."~".$pricing_no;
}
elseif($type == 'get_medicine_details'){
 	$medicine_id = $_REQUEST['id'];
 	$details   = array();
 	$get_details = "select MedicineID, GenericName, BrandName, Quantity, CompanyName, MedicineType from MEDICINE_LIST where MedicineID = '$medicine_id'";
 	//echo $get_details;exit;
 	$get_details_run = mysql_query($get_details);
 	while ($detailrow = mysql_fetch_array($get_details_run)) {
		$res['MedicineID'] 					= $detailrow['MedicineID'];
		$res['GenericName'] 				= $detailrow['GenericName'];
		$res['BrandName'] 					= $detailrow['BrandName'];
		$res['Quantity']					= $detailrow['Quantity'];
		$res['CompanyName']					= $detailrow['CompanyName'];
		$res['MedicineType']				= $detailrow['MedicineType'];
		if($res['MedicineType'] != '2'){
			$res['Quantity'] = "";
		}
		array_push($details,$res);
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($details) . ');';	
 }
  elseif($type == 'insert_patient_details'){
	$patientheight 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['patientheight'])));
	$patientweight 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['patientweight'])));
	$patientpressure			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['patientpressure'])));
	$patienttemp 				=  mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['patienttemp'])));
	$userHistory 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['userHistory'])));
	$visit_notes 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['visit_notes'])));
	$review_notes 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['review_notes'])));
	$userid_for_current_plan 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['userid_for_current_plan'])));
	$logged_merchantid 			=  mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['logged_merchantid'])));
	$plancode_for_current_plan 	=  mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['plancode_for_current_plan'])));

	//Update patient history
		$user_details = mysql_query("update USER_DETAILS set UserHistory='$userHistory' where UserID = '$userid_for_current_plan'");

	//Insert patient Data
		if(($patientheight != "")||($patientweight != "")||($patientpressure != "")||($patienttemp != "")){
		$visit_data_insert = mysql_query("insert into `VISIT_DATA` (`UserID`, `MerchantID`, `Height`, `Weight`, `BloodPressure`, `Temperature`, `CreatedDate`, `CreatedBy`, `UpdatedBy`) VALUES ('$userid_for_current_plan', '$logged_merchantid', '$patientheight', '$patientweight', '$patientpressure', '$patienttemp', now(), '$logged_merchantid', '$logged_merchantid')");
		}
	//Insert patient Notes	
		if($visit_notes != ""){
			$visit_notes_insert = mysql_query("insert into `VISIT_NOTES` (`UserID`, `MerchantID`, `Notes`, `CreatedDate`, `CreatedBy`, `UpdatedBy`) VALUES ('$userid_for_current_plan', '$logged_merchantid', '$visit_notes', now(), '$logged_merchantid', '$logged_merchantid')");
		}

		if($review_notes != ""){
			$review_notes_insert = mysql_query("insert into `REVIEW_NOTES` (`UserID`, `MerchantID`, `Notes`, `CreatedDate`, `CreatedBy`, `UpdatedBy`) VALUES ('$userid_for_current_plan', '$logged_merchantid', '$review_notes', now(), '$logged_merchantid', '$logged_merchantid')");
		}
	

	$pcount = 1;
	if($pcount){
		$response['success'] = true;
	} else {
		$response['success'] = false;
	}
	echo json_encode($response);
 }
elseif($type == "get_master_goals"){
 	$plancode 	= $_REQUEST['plancode'];
 	$goalno 	= $_REQUEST['goalno'];
 	$goals   	= array();
 	$get_goal_query = "select  PlanCode, GoalNo, GoalDescription, DisplayedWith from GOAL_DETAILS where PlanCode = '$plancode' and GoalNo='$goalno'";
 	//echo $get_appo_query;exit;
 	$get_goal_run = mysql_query($get_goal_query);
 	$get_goal_count = mysql_num_rows($get_goal_run);
 	if($get_goal_count > 0){
 		while ($goal_row = mysql_fetch_array($get_goal_run)) {
 			$res['PlanCode'] 			= $goal_row['PlanCode'];
 			$res['GoalNo'] 				= $goal_row['GoalNo'];
 			$res['GoalDescription'] 	= $goal_row['GoalDescription'];
 			$res['DisplayedWith'] 		= $goal_row['DisplayedWith'];
 			array_push($goals,$res);
 		}
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($goals) . ');';	
 }
 elseif($type == "get_assigned_goals"){
 	$userid 	= $_REQUEST['userid'];
 	$plancode 	= $_REQUEST['plancode'];
 	$goalno 	= $_REQUEST['goalno'];
 	$goals   	= array();
 	$get_goal_query = "select  PlanCode, GoalNo, GoalDescription, DisplayedWith from USER_GOAL_DETAILS where PlanCode = '$plancode' and GoalNo='$goalno' and UserID = '$userid'";
 	//echo $get_appo_query;exit;
 	$get_goal_run = mysql_query($get_goal_query);
 	$get_goal_count = mysql_num_rows($get_goal_run);
 	if($get_goal_count > 0){
 		while ($goal_row = mysql_fetch_array($get_goal_run)) {
 			$res['PlanCode'] 			= $goal_row['PlanCode'];
 			$res['GoalNo'] 				= $goal_row['GoalNo'];
 			$res['GoalDescription'] 	= $goal_row['GoalDescription'];
 			$res['DisplayedWith'] 		= $goal_row['DisplayedWith'];
 			array_push($goals,$res);
 		}
 	}
 	echo $_REQUEST['jsoncallback'] . '(' . json_encode($goals) . ');';	
 }
?>

