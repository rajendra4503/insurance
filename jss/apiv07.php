<?php
ini_set('display_errors',0);
date_default_timezone_set('Asia/Kolkata');
include('include/configinc.php');

// $dbhost1        = '10.2.1.20';
// $dbuser1        = 'root';
// $dbpass1        = 'seoy3400';
// $connection1    = mysql_connect($dbhost1, $dbuser1, $dbpass1);
// $db_name1       = mysql_select_db('planpiper_intl',$connection1) or die(mysql_error()) ;

include('include/functions.php');
include('SMTP/PHPMailerAutoload.php');
include('SMTP/class.phpmailer.php');
include('SMTP/class.smtp.php');

$host_server    = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

$url            = $host_server."/assign_default_plan.php";
$originalpath   = "uploads/profile_picture/original/";
$compressedpath = "uploads/profile_picture/compressed/";
//echo $host_server;exit;


function post_to_url($url, $data) {
$fields = '';
foreach($data as $key => $value) { 
  $fields .= $key . '=' . $value . '&'; 
}
rtrim($fields, '&');

$post = curl_init();

curl_setopt($post, CURLOPT_URL, $url);
curl_setopt($post, CURLOPT_POST, count($data));
curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($post);

curl_close($post);
}

$plan_header        = "uploads/planheader/";
$reduced_plan_header= "uploads/reducedplanheader/";

$currentdatetime    = date("Y-m-d G:i:s ");
$current_date       = date('Y-m-d');
//$_REQUEST['RequestType'] ="";  
$offset = strtotime("+2 minutes 23 seconds"); 
//$now    = date("Y-m-d H:i:s",$offset);

$get_databasetime   = mysql_query("select now() as dbtime");
$row                = mysql_fetch_array($get_databasetime);
$now                = $row['dbtime'];
//echo $now;exit;
$type               = $_REQUEST['RequestType'];
$request_types      = array("activities","settings_backup_restore","delete_plan","save_analytics","patient_contacts",
    "file_upload","reports","add_edit_reports","update_display_filename","self_plans","profile_settings","settings_backup");
if(in_array($type,$request_types))
{
    $get_user_id         = (empty($_REQUEST['user_id']))          ? '' : $_REQUEST['user_id'];
    if($get_user_id=="")
    {
        $get_user_id    = (empty($_REQUEST['userid']))          ? '' : $_REQUEST['userid'];
    }
    //echo $get_user_id;
    //$get_user_id        = "00091530027645997048";
    $check_paid_status      = "select if(PaidUntil>now(),'Y','N') as Paid from USER_ACCESS where UserID='$get_user_id'";
    //echo $check_paid_status;exit;
    //$payment_status     = mysql_result(mysql_query($check_paid_status),0);
    $pay_status             = mysql_query($check_paid_status);
    $status_count           = mysql_num_rows($pay_status);
    //echo $status_count;
    if($status_count)
    {
        $status_row=mysql_fetch_array($pay_status);
        $payment_status     = $status_row['Paid']; 
    }
    //echo "Resp: ".$payment_status."<br>";exit;
    if($payment_status=='Y')
    {
        $paid_status    = '"PLANPIPER_PAID_STATUS":"'.$payment_status.'",';
    }
    else
    {
        $paid_status    = '"PLANPIPER_PAID_STATUS":"'.$payment_status.'",';
    }
}
else
{
    $paid_status        = "";  
}


//*****************************GET COUNTRIES********************************************************************
if($_REQUEST['RequestType']=="get_countries")
{
    $countries              = array();

    $get_country_codes      = "select t1.CountryCode,t1.CountryName from COUNTRY_DETAILS as t1 
                                where CountryCode!='' order by CountryName";
    $get_country_codes_qry  = mysql_query($get_country_codes);
    $get_country_codes_count= mysql_num_rows($get_country_codes_qry);
    if($get_country_codes_count>0)
    {
        while($row = mysql_fetch_array($get_country_codes_qry))
        {
            $res['CountryCode']    =$row['CountryCode'];
            $res['CountryName']    =$row['CountryName'];
        array_push($countries,$res);
        }
        echo "{".json_encode('PLANPIPER_COUNTRIES').':'.json_encode($countries)."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_COUNTRIES').':'.json_encode($countries)."}";
    }
}
//*****************************END OF COUNTRIES*************************************************************

//*****************************GET STATES**************************************************************************************************
elseif($_REQUEST['RequestType']=="get_states" && $_REQUEST['country_code']!="")
{
    $states             = array();
    $country_code       = urlencode($_REQUEST['country_code']);
    $get_states         = "select StateID,CountryCode,StateName from STATE_DETAILS where CountryCode='$country_code' order by StateName";
    //echo $get_states;exit;
    $get_states_qry     = mysql_query($get_states);
    $get_states_count   = mysql_num_rows($get_states_qry);
    if($get_states_count>0)
    {
        while($row = mysql_fetch_array($get_states_qry))
        {
            $res['StateID']     = $row['StateID'];
            $res['CountryCode'] = $row['CountryCode'];
            $res['StateName']   = $row['StateName'];
            array_push($states,$res);
        }
        echo "{".json_encode('PLANPIPER_STATES').':'.json_encode($states)."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_STATES').':'.json_encode($states)."}";
    }
}
//*****************************END OF STATES*********************************************************************************************

//*****************************GET CITIES**************************************************************************************************
elseif($_REQUEST['RequestType']=="get_cities" && $_REQUEST['state_id']!="")
{
    $cities             = array();
    $state_id           = urlencode($_REQUEST['state_id']);
    $get_cities         = "select CityID,StateID,CityName from CITY_DETAILS where StateID='$state_id' order by CityName";
    //echo $get_states;exit;
    $get_cities_qry     = mysql_query($get_cities);
    $get_cities_count   = mysql_num_rows($get_cities_qry);
    if($get_cities_count>0)
    {
        while($row = mysql_fetch_array($get_cities_qry))
        {
            $res['CityID']      = $row['CityID'];
            $res['StateID']     = $row['StateID'];
            $res['CityName']    = $row['CityName'];
            array_push($cities,$res);
        }
        echo "{".json_encode('PLANPIPER_CITIES').':'.json_encode($cities)."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_CITIES').':'.json_encode($cities)."}";
    }
}
//*****************************END OF CITIES*********************************************************************************************

//SIGN UP 
if($_REQUEST['RequestType']=="signup" && $_REQUEST['email_id']!="" && $_REQUEST['mobile_no']!="" && $_REQUEST['country_code']!="")
{
    $country_code   = (empty($_REQUEST['country_code']))    ? '' : mysql_real_escape_string(trim($_REQUEST['country_code']));
    $email_id       = (empty($_REQUEST['email_id']))        ? '' : mysql_real_escape_string(trim($_REQUEST['email_id']));
    $mobile_no      = (empty($_REQUEST['mobile_no']))       ? '' : mysql_real_escape_string(trim($_REQUEST['mobile_no']));
    $first_name     = (empty($_REQUEST['first_name']))      ? '' : mysql_real_escape_string(trim($_REQUEST['first_name']));
    $last_name      = (empty($_REQUEST['last_name']))       ? '' : mysql_real_escape_string(trim($_REQUEST['last_name']));
    $mobile_type    = (empty($_REQUEST['mobile_type']))     ? '' : mysql_real_escape_string(trim($_REQUEST['mobile_type']));
    
    $check_duplicate= "select EmailID,MobileNo from USER_ACCESS where EmailID = '$email_id' or MobileNo = '$mobile_no' and UserStatus='A'";
    //echo $check_duplicate;exit;
    $check_query     = mysql_query($check_duplicate);
    $duplicate_count = mysql_num_rows($check_query);
    if($duplicate_count==0)
    {
        $i  = 0;
        $tmp = mt_rand(1,9);
        do {
            $tmp .= mt_rand(0, 9);
        } while(++$i < 14);
        //echo $tmp;

        /*Create email id(ajax_validation1.php)*/

            /*GET TWO DIGIT COUNTRY CODE FROM COUNTRY_DETAILS TABLE BASED ON FIVE DIGIT COUNTRY CODE*/
            $get_countrycode_query  = mysql_query("select substr(CountryID,1,2) from COUNTRY_DETAILS where CountryCode='$country_code'");
            $twodigit_country_code  = mysql_result($get_countrycode_query, 0);
            // //echo $country_code."<br>";
            // /*END OF GET TWO DIGIT COUNTRY CODE FROM COUNTRY_DETAILS TABLE BASED ON FIVE DIGIT COUNTRY CODE*/
            $planpiper_email        = $twodigit_country_code.$mobile_no."@planpiper.com";
        //echo $planpiper_email;exit;
        /*End of Create email id*/

        $userid = $country_code.$tmp;
        $mobilenumber = $country_code.$mobile_no;
        $insert_user_access = "insert into USER_ACCESS (UserID, MobileNo, EmailID, PlanpiperEmailID, Password, PasswordStatus, UserStatus, CreatedDate) values 
            ('$userid','$mobilenumber','$email_id','$planpiper_email','planpiper','0','A',now())";
        $insert_user_access_run = mysql_query($insert_user_access);
        $insert_user_details = "insert into USER_DETAILS (UserID, FirstName, LastName, CountryCode,MobilePhoneType, CreatedDate) values 
            ('$userid', '$first_name', '$last_name' , '$country_code','$mobile_type', now())";
        $insert_user_details_run = mysql_query($insert_user_details);

        $check_insert = mysql_affected_rows();

        $insert_default_settings = "insert into USER_PHONE_SETTINGS 
                            (UserID, WakeUp, Morning, BeforeBreakfast, WithBreakfast, AfterBreakfast, MorningSnack, BeforeLunch, WithLunch, AfterLunch, Afternoon, EveningSnack, BeforeTea, WithTea, AfterTea, Evening, BeforeDinner, WithDinner, AfterDinner, BeforeSleeping, NormalVolume, CriticalVolume, NormalMusicFileName, CriticalMusicFileName, CreatedDate)
                            select '$userid', WakeUp, Morning, BeforeBreakfast, WithBreakfast, AfterBreakfast, MorningSnack, BeforeLunch, WithLunch, AfterLunch, Afternoon, EveningSnack, BeforeTea, WithTea, AfterTea, Evening, BeforeDinner, WithDinner, AfterDinner, BeforeSleeping, NormalVolume, CriticalVolume, NormalMusicFileName, CriticalMusicFileName,now() from USER_PHONE_SETTINGS where UserID='111'";
        $insert_default_query    = mysql_query($insert_default_settings);

        if($email_id != "" && $first_name!="" && $check_insert)
        {
            function mailresetlink($useremail,$username)
            {//echo $email;exit;
            
            //Create a new PHPMailer instance
            $mail = new PHPMailer();
            // Set PHPMailer to use the sendmail transport
            $mail->isSMTP();
            //Set who the message is to be sent from
            $mail->setFrom('support@planpiper.com', 'Admin');
            //Set who the message is to be sent to
            $mail->addAddress($useremail,$username);
            //Set the subject line
            $mail->Subject = 'Planpiper - Welcome Email';
            
            $message = "
            <html>
            <head>
            <title> Welcome Email</title>
            </head>
            <body>
            <p>Hi $username,</p>
            <p>Thank you for registering with Plan Piper. Your account has been created.</p>
            <p>You can login to the app with the following details-</p>
            <p><b>Login id - $useremail</b></p>
            <p><b>Password - planpiper</b></p>
            <p>This is a one time use password that you will be asked to change when you login for the first time. Please create a suitable password with 6-15 characters which contains atleast one numeric digit and one alphabet that you can remember.</p>
            <p>A demo plan has been assigned to you. This plan will download on your phone when you login.</p>
            <p>Once the plan is downloaded, the app will remind you with a notification message and sound to take appropriate medication (as has been predefined in the demo-plan by the doctor). In the demo plan, some events have been marked as 'critical'. This means that, when those reminders get activated, the phone will ring loudly to attract your attention. You will also be asked to enter a response to questions such as 'Have you taken the medicine' or 'Enter the glucometer reading'. This data will be used to track compliance to the medical protocol.</p>
            <p>If you wish to use this software for your patients, please email us at <i><u><a href='mailto:support@planpiper.com'>support@planpiper.com</a></u></i> and we will share the commercials with you and set you up for that.</p>
            <br>
            <p>Best Regards,</p>
            <p>Planpiper Team</p>
            </body>
            </html>
            ";  
                    
            $mail->msgHTML($message, dirname(__FILE__));
            
            //Replace the plain text body with one created manually
            $mail->AltBody = 'This is a plain-text message body';
            
            //send the message, check for errors
                if(!$mail->send())
                {
                    
                } else
                {
                    
                }
            }
            //echo $email." ".$token." ".$name;exit;
            mailresetlink($email_id,$first_name);
            }

            /*ASSIGN PLAN TO USER*/
            $demo_plan_code     = "IN0000000002";
            $request            = "?userid=$userid&plancodeselected=$demo_plan_code&planpiper_email=$planpiper_email";
            //echo $request;exit;
            $hit_url            = $url.$request;
            //echo $hit_url;exit;
            //file_get_contents($hit_url);
            header("Location:assign_default_plan.php$request");
            //header("Location:api.php");
            /*END OF ASSIGN PLAN TO USER*/     
    }
    else
    {
        echo "{".json_encode('PLANPIPER_SIGNUP').':'.json_encode("2")."}";
    }
}
//*************************END OF SIGNUP********************************************************************************

//*************************LOGIN********************************************************************************************
if($_REQUEST['RequestType']=="login" && $_REQUEST['username']!="" && $_REQUEST['password']!="" && $_REQUEST['country_code']!="")
{//echo 123;exit;
$plan_list      = array();
$assigned_plans = array();
$plan_details   = array();
$plan           = "";
$p_timestamp    = "";
$country_code   = (empty($_REQUEST['country_code']))    ? '' : mysql_real_escape_string(trim($_REQUEST['country_code']));
$username       = $_REQUEST['username'];
$password       = $_REQUEST['password'];
$ostype         = (empty($_REQUEST['ostype']))          ? '' : $_REQUEST['ostype'];
$deviceid       = (empty($_REQUEST['deviceid']))        ? '' : $_REQUEST['deviceid'];
$timestamp      = (empty($_REQUEST['timestamp']))       ? '' : $_REQUEST['timestamp'];
$os_version     = (empty($_REQUEST['os_version']))      ? '' : $_REQUEST['os_version'];
$app_version    = (empty($_REQUEST['app_version']))     ? '' : $_REQUEST['app_version'];
$model          = (empty($_REQUEST['model']))           ? '' : $_REQUEST['model'];/*Details about device like manufacturer*/
$installed_date = (empty($_REQUEST['installed_date']))  ? '' : $_REQUEST['installed_date'];
$restore        = (empty($_REQUEST['restore']))         ? '' : $_REQUEST['restore'];

//echo $timestamp;exit;
//$timestamp      = '2015-01-21 17:36:35';
// To protect MySQL injection
$username       = stripslashes($username);
$password       = stripslashes($password);
$username       = mysql_real_escape_string($username);
$password       = mysql_real_escape_string($password);

if($timestamp=="")
{
    $timestamp_query = "";
    $plan_timestamp  = "";
}
else
{
    $timestamp_query    = " and t2.UpdatedDate>='$timestamp'";
    //$timestamp_query    = "";
    $plan_timestamp     = " and t1.PlanUpdatedDate>='$timestamp' ";
    //$plan_timestamp  = "";
}

$validate_user  =  "select t1.UserID, t1.EmailID, substr(t1.MobileNo,1,5) as CountryCode,substr(t1.MobileNo,6) as MobileNo,
                    if(t1.PaidUntil>now(),'Y','N') as Paid,t1.PlanpiperEmailID,
                    t1.PasswordStatus, t2.MerchantID,t4.CompanyName,t2.RoleID,t2.Status,t3.FirstName,t3.LastName,
                    t3.LanguageID,t3.SupportPersonName,t3.SupportPersonEmailID,t3.SupportPersonMobileNo,t1.PlanpiperEmailID,
                    t3.Gender,if((t3.DOB='1970-01-01'|| t3.DOB='0000-00-00'),'',t3.DOB) as DOB,t3.BloodGroup,t3.CountryCode as CountryCode1,t3.StateID,t3.CityID,t3.AddressLine1,t3.AddressLine2,t3.PinCode,
                    t3.AreaCode,t3.Landline,t3.ProfilePicture,t3.AdPaymentStatus,t3.AdStartDate,t3.AdEndDate
                    from USER_ACCESS as t1, USER_MERCHANT_MAPPING as t2, USER_DETAILS as t3, MERCHANT_DETAILS as t4
                    where (t1.EmailID = '$username' or  substr(t1.MobileNo,6)='$username') and t1.Password = '$password' 
                    and t1.UserID=t2.UserID and t2.UserID=t3.UserID and t3.CountryCode='$country_code'
                    and t2.MerchantID=t4.MerchantID and t2.Status='A' and t2.RoleID IN ('5','2')";
//echo $validate_user;exit;
$validate_query =  mysql_query($validate_user);
$user_count     = mysql_num_rows($validate_query);
//echo $user_count;exit;
    if($user_count)
    {     
    $user_info      = array();   
        if($user_row = mysql_fetch_array($validate_query))
        {
        $userid                     = (empty($user_row['UserID']))                  ? '' : $user_row['UserID'];
        //echo $userid;exit;
        $p['UserID']                = $userid;

        if($userid)
        {
            $check_paid_status      = "select FreePlan from USER_PLAN_HEADER where UserID='$userid' and FreePlan in ('N')";
            //echo $check_paid_status;exit;
            //$payment_status     = mysql_result(mysql_query($check_paid_status),0);
            $pay_status             = mysql_query($check_paid_status);
            $status_count           = mysql_num_rows($pay_status);
            //echo $status_count;
            if($status_count>0)
            {
                $paid = "Y";
            }
            else
            {
                $paid = "N";
            }
        $p['PLANPIPER_PAID_STATUS'] = $paid;
        }
//echo $paid;exit;
        //$paid                       = (empty($user_row['Paid']))                    ? '' : $user_row['Paid'];
        //$p['PLANPIPER_PAID_STATUS'] = $paid;
        $p['FirstName']             = (empty($user_row['FirstName']))               ? '' : $user_row['FirstName'];
        $p['LastName']              = (empty($user_row['LastName']))                ? '' : $user_row['LastName'];
        $p['AdPaymentStatus']       = (empty($user_row['AdPaymentStatus']))         ? '' : $user_row['AdPaymentStatus'];
        $p['AdStartDate']           = (empty($user_row['AdStartDate']))             ? '' : $user_row['AdStartDate'];
        $p['AdEndDate']             = (empty($user_row['AdEndDate']))               ? '' : $user_row['AdEndDate'];
        $e                          = (empty($user_row['EmailID']))                 ? '' : $user_row['EmailID'];
        $p['EmailID']               = $e;
        $m                          = (empty($user_row['MobileNo']))                ? '' : $user_row['MobileNo'];
        $p['MobileNo']              = $m;
        $countrycode                = (empty($user_row['CountryCode']))             ? '' : $user_row['CountryCode'];
        $split_country_code         = substr($countrycode,0,5);
       // echo $split_country_code;exit;
        $get_countrycode_query      = mysql_query("select substr(CountryID,1,2) from COUNTRY_DETAILS 
                                                where CountryCode='$split_country_code'");
        $c_code                     = mysql_result($get_countrycode_query, 0);
        //echo $c_code;exit;
        /*If User is paid user then create a planpiper email id (if not created earlier) and Update the same in USER_ACCESS Table*/
        $planpiper_email_id         = (empty($user_row['PlanpiperEmailID']))        ? '' : $user_row['PlanpiperEmailID'];
        $p['PlanpiperEmailID']      = $planpiper_email_id;
        if($paid=='Y' && ($planpiper_email_id=="" || $planpiper_email_id==NULL))
        {
            $new_email              = $c_code.$m."@planpiper.com";
            //echo $new_email."<br>";exit;
            //CHECK FOR DUPLICATE MOBILE NUMBER
            //  $create_email          = mysql_query("insert into `virtual_users` 
            //                                     (`domain_id`,`password`,`email`) VALUES 
            //                                     ('4', ENCRYPT('fgh(12)!artc', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$new_email')");
            // $check_insert           = mysql_affected_rows();
            // if($check_insert)
            // {
            //     mysql_query("update USER_ACCESS set PlanpiperEmailID='$new_email' where UserID='$userid'");
            // }
        }
        /*End If User is paid user then create a planpiper email id (if not created earlier) and Update the same in USER_ACCESS Table*/
       
        $p['CountryCode']           = (empty($countrycode))                     ? '' : '+'.ltrim($countrycode,0);
        $p['UserCountryCode']       = (empty($countrycode))                     ? '' : $countrycode;
        $p['Gender']                = (empty($user_row['Gender']))              ? '' : $user_row['Gender'];
        $p['DOB']                   = (empty($user_row['DOB']))                 ? '' : $user_row['DOB'];
        $p['BloodGroup']            = (empty($user_row['BloodGroup']))          ? '' : $user_row['BloodGroup'];
        $country_id                 = (empty($user_row['CountryCode1']))        ? '' : $user_row['CountryCode1'];
        $state_id                   = (empty($user_row['StateID']))             ? '' : $user_row['StateID'];
        $city_id                    = (empty($user_row['CityID']))              ? '' : $user_row['CityID'];

        /*Get Country Name*/
        if($country_id!="" && $country_id!=0)
        {
            $get_country_name         = mysql_fetch_object(mysql_query("select CountryName from COUNTRY_DETAILS where CountryCode='$country_id'"));
            $country_name             = $get_country_name->CountryName;
        }
        else
        {
            $country_name             = "";
        }

        /*Get State Name*/
        if($state_id!="" && $state_id!=0)
        {
            $get_state_name         = mysql_fetch_object(mysql_query("select StateName from STATE_DETAILS where StateID='$state_id'"));
            $state_name             = $get_state_name->StateName;
        }
        else
        {
            $state_name             = "";
        }
        
        /*Get City Name*/
        if($city_id!="" && $city_id!=0)
        {
            $get_city_name         = mysql_fetch_object(mysql_query("select CityName from CITY_DETAILS where CityID='$city_id'"));
            $city_name             = $get_city_name->CityName;
        }
        else
        {
            $city_name             = "";
        }

        $p['CountryName']           = (empty($country_name))  ? '' : $country_name;
        $p['StateID']               = $state_id;
        $p['StateName']             = (empty($state_name))  ? '' : $state_name;
        $p['CityID']                = $city_id;
        $p['CityName']              = (empty($city_name))   ? '' : $city_name;

        $p['AddressLine1']          = (empty($user_row['AddressLine1']))        ? '' : $user_row['AddressLine1'];
        $p['AddressLine2']          = (empty($user_row['AddressLine2']))        ? '' : $user_row['AddressLine2'];
        $p['PinCode']               = (empty($user_row['PinCode']))             ? '' : $user_row['PinCode'];
        $p['AreaCode']              = (empty($user_row['AreaCode']))            ? '' : $user_row['AreaCode'];
        $p['Landline']              = (empty($user_row['Landline']))            ? '' : $user_row['Landline']; 
        $p['ProfilePicture']        = (empty($user_row['ProfilePicture']))      ? '' : $host_server."/uploads/profile_picture/compressed/".$user_row['ProfilePicture'];
        $p['SupportPersonName']     = (empty($user_row['SupportPersonName']))   ? '' : $user_row['SupportPersonName'];
        $p['SupportPersonEmailID']  = (empty($user_row['SupportPersonEmailID']))? '' : $user_row['SupportPersonEmailID'];
        $p['SupportPersonMobileNo'] = (empty($user_row['SupportPersonMobileNo']))? '' : $user_row['SupportPersonMobileNo'];
        $password_status            = $user_row['PasswordStatus'];

            if($timestamp=="" && $password_status==0)
            {
                $loginstatus="2";
            }
            elseif($timestamp=="" && $password_status==1)
            {
                $loginstatus="1";
            }
            elseif($timestamp!="" && $password_status==0)
            {
                $loginstatus="2";
            }
            elseif($timestamp!="" && $password_status==1)
            {
                $loginstatus="3";
            }
             
            if($userid)
            {

                /*Get Plan Timestamp(By Default send current timestamp. Iff plan gets customized then send PLanUpdatedDate from UserPlanHeader)*/
                $get_timestamp      = "select max(PlanUpdatedDate) as PlanUpdatedDateTime 
                                        from USER_PLAN_HEADER where UserID='$userid' and PlanStatus='A'";
                //echo $get_timestamp;exit;
                $get_timestamp_qry  = mysql_query($get_timestamp);
                $get_timestamp_row  = mysql_num_rows($get_timestamp_qry);
                //echo $get_timestamp_row;exit;
                if($get_timestamp_row==1)
                {
                    while($timestamp_row = mysql_fetch_array($get_timestamp_qry))
                    {
                        $p_timestamp    = $timestamp_row['PlanUpdatedDateTime'];
                        //echo $p_timestamp;exit;
                        if($p_timestamp=='0000-00-00 00:00:00' || $p_timestamp==NULL )
                        {
                            $p_timestamp = $now;
                        }
                        else
                        {
                            $p_timestamp = $p_timestamp;
                        }
                        //echo $p_timestamp;exit;
                    }
                }
                else
                {
                    $p_timestamp = $now;
                }
                //echo $p_timestamp;exit;
                /*End of Get Plan Timestamp(By Default send current timestamp. Iff plan gets customized then send PLanUpdatedDate from UserPlanHeader)*/

                //CHECKING FOR A SAME DEVICE ID FOR MULTIPLE PERSONS
                if($deviceid!="" && $ostype!="")
                {
                $get_duplicate_device_ids       = "select UserID,DeviceID from USER_ACCESS 
                                                    where DeviceID='$deviceid' and (EmailID!='$e' or  MobileNo!='$m')";
                //echo $get_duplicate_device_ids;exit;
                $get_duplicate_device_ids_qry   = mysql_query($get_duplicate_device_ids);
                    if($get_duplicate_device_ids_qry)
                    {
                        while($duplicate_rows = mysql_fetch_array($get_duplicate_device_ids_qry))
                        {
                            $duplicate_user_id      = $duplicate_rows['UserID'];
                            $duplicate_device_id    = $duplicate_rows['DeviceID'];

                            mysql_query("update USER_ACCESS set DeviceID='',OSType='',OSVersion='',AppVersion='',Model='',DateOfInstallation='' where UserID='$duplicate_user_id'");
                        }
                    }
                }
                 //END OF CHECKING FOR A SAME DEVICE ID FOR MULTIPLE PERSONS

                //UPDATE DEVICE ID AND OSTYPE EACH TIME DURING USER LOGIN
                
                    if($ostype=='I' )
                    {       //echo "U".$user_ids."<br>";
                        
                            mysql_query("update USER_ACCESS set OSType='$ostype',DeviceID='$deviceid',
                                        OSVersion='$os_version',AppVersion='$app_version',Model='$model',DateOfInstallation='$installed_date' where UserID='$userid'");
                    }
                    else if($ostype=='A')
                    {
                        mysql_query("update USER_ACCESS set OSType='$ostype',DeviceID='$deviceid',
                                    OSVersion='$os_version',AppVersion='$app_version',Model='$model',DateOfInstallation='$installed_date' where UserID='$userid'");
                    }
                    //END OF UPDATE DEVICE ID AND OSTYPE EACH TIME DURING USER LOGIN      
            }

            //GET MERCHANTS
            if($p['UserID'])
            {
            $get_assigned_plans =   "select t1.PlanCode,t2.DependencyID,
                                    IF(t2.DependencyID>0,(select concat(FirstName,' ',LastName) from USER_DEPENDENCIES where DependencyID=t2.DependencyID), '') as Name,
                                    t1.MerchantID,PlanEndDate
                                    from USER_PLAN_HEADER as t1,USER_PLAN_MAPPING as t2
                                    where t2.UserID='$userid' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                                    and t2.Status='A' and t1.PlanStatus='A'
                                    $plan_timestamp";
            //echo $get_assigned_plans;exit;
            $get_assigned_plans_qry = mysql_query($get_assigned_plans);
            $get_assigned_plans_count=mysql_num_rows($get_assigned_plans_qry);
                if($get_assigned_plans_count>0)
                {
                    $r = array();
                    while($assigned_plan_rows = mysql_fetch_array($get_assigned_plans_qry))
                    {
                        $plan_end_date   = $assigned_plan_rows['PlanEndDate'];

                        if($restore=='Y')
                        {
                        //echo "R:".$restore;
                           if(($plan_end_date<date('Y-m-d')) && $plan_end_date!="" )
                            {
                                $plancodes       = $assigned_plan_rows['PlanCode'];
                                array_push($assigned_plans,$plancodes);
                            }
                            else
                            {
                                
                            } 
                        }
                        else if(($plan_end_date>date('Y-m-d')) || $plan_end_date=="")
                        {
                        //echo "L:".$restore;
                            $plancodes       = $assigned_plan_rows['PlanCode'];
                            array_push($assigned_plans,$plancodes);
                        }
                        //exit;
                    }
                    $plan_list = implode(",",$assigned_plans);
                }
            }
        array_push($user_info,$p);
        }

        $medical_tests          = array();
        $get_medical_tests      = "select ID,TestName,Status from MEDICAL_TESTS order by ID";
        $get_medical_tests_query= mysql_query($get_medical_tests);
        $get_medical_tests_count= mysql_num_rows($get_medical_tests_query);
            if($get_medical_tests_count > 0)
            {
                while($medical_tests_row = mysql_fetch_array($get_medical_tests_query))
                {
                    $med['ID']          = $medical_tests_row['ID'];
                    $med['TestName']    = $medical_tests_row['TestName'];
                    $med['Status']      = $medical_tests_row['Status'];
                array_push($medical_tests,$med);
                }
            }



        $settings           = array();
        $directory          = array();
        $self_plans         = array();
        $reports            = array();


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
        $path       = "/uploads/folder/$country_id$mobile_no/"; 
        /*END OF GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/

                 

        
            /*GET USER SETTINGS IF TIMESTAMP IS EMPTY*/
            $get_settings       = "select * from USER_PHONE_SETTINGS where UserID='$userid'";
           //echo $get_settings;exit;
            $get_settings_query = mysql_query($get_settings);
            $get_row_count      = mysql_num_rows($get_settings_query);
            if($get_row_count==1)
            {
                while($settings_row = mysql_fetch_assoc($get_settings_query))
                {   
                    $settings[] = $settings_row;
                }
            //echo "<pre>";print_r($settings);
            }
            else
            {

            }
            /*END OF GET USER SETTINGS IF TIMESTAMP IS EMPTY*/

            if($paid=='Y')
            {
                //echo 123;exit;
                /*GET DIRECTORY(PATIENT CONTACTS)*/
                $get_directory       = "select ID, MobileUniqueID, UserID, Type, FirstName, MiddleName, LastName,
                                        EmailID, 
                                        concat('+',trim(leading '0' from MobileCountryCode )) as MobileCountryCode, MobileNo,
                                        concat('+',trim(leading '0' from LandlineCountryCode )) as LandlineCountryCode,
                                        LandlineAreaCode,LandlineNo, Tag, Status, CreatedDate, CreatedBy, UpdatedDate, UpdatedBy
                                        from PATIENT_CONTACTS where UserID='$userid'";
               //echo $get_directory;exit;
                $get_directory_query = mysql_query($get_directory);
                $get_directory_count = mysql_num_rows($get_directory_query);
                if($get_directory_count>0)
                {
                    while($directory_row = mysql_fetch_assoc($get_directory_query))
                    {   
                        $directory[] = $directory_row;
                    }
                //echo "<pre>";print_r($directory);
                }
                /*END OF GET DIRECTORY(PATIENT CONTACTS)*/

                /*SELF PLANS*/
                $get_self_plans     =   "select ID,UserID,MerchantID,PlanCode,PlanName,Status
                                        from USER_SELF_PLAN_HEADER 
                                        where UserID='$userid' and Status='A'";
                //echo $get_self_plans;exit;
                $get_self_plans_qry = mysql_query($get_self_plans);
                $get_self_plan_count= mysql_num_rows($get_self_plans_qry);
                if($get_self_plan_count>0)
                {
                    while($self_plan_rows   = mysql_fetch_array($get_self_plans_qry))
                    {
                        $self_plan_code                = $self_plan_rows['PlanCode'];
                        $rs1['UserID']                 = $self_plan_rows['UserID'];
                        $rs1['MerchantID']             = $self_plan_rows['MerchantID'];
                        $rs1['PlanCode']               = $self_plan_code;
                        $rs1['PlanName']               = $self_plan_rows['PlanName'];
                        $rs1['PlanStatus']             = $self_plan_rows['Status'];
                        $activities         = array();
                        if($self_plan_code)
                        {
                            $get_activities =   "select `ActivityID`,`UserID`,`PlanCode`,`SectionID`,`PrescriptionNo`,`RowNo`,
                                                `DoctorsName`,`Name`,`Description`,`MedicineCount`,`MedicineType`,`ActionText`,
                                                `When`,`Instruction`,`Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,
                                                `IsCritical`,`ResponseRequired`,`Date`,`Time`, `AppointmentDuration`, `AppointmentPlace`, `SelfTestID`, `RemindBefore`, `Status`
                                                from USER_SELF_PLAN_ACTIVITIES where UserID='".$rs1['UserID']."' 
                                                and PlanCode = '".$rs1['PlanCode']."' and Status='A'";
                           // echo $get_activities."<br>";exit;
                            $get_act_qry    = mysql_query($get_activities);
                            $get_act_count  = mysql_num_rows($get_act_qry);
                            if($get_act_count>0)
                            {
                                while($activity_rows   = mysql_fetch_assoc($get_act_qry))
                                {
                                $rs2['ActivityID']         = (empty($activity_rows['ActivityID']))     ? '': $activity_rows['ActivityID'];
                                $rs2['UserID']             = (empty($activity_rows['UserID']))         ? '': $activity_rows['UserID'];
                                $rs2['PlanCode']           = (empty($activity_rows['PlanCode']))       ? '': $activity_rows['PlanCode'];
                                $rs2['SectionID']          = (empty($activity_rows['SectionID']))      ? '': $activity_rows['SectionID'];
                                $rs2['PrescriptionNo']     = (empty($activity_rows['PrescriptionNo'])) ? '': $activity_rows['PrescriptionNo'];
                                $rs2['RowNo']              = (empty($activity_rows['RowNo']))          ? '': $activity_rows['RowNo'];
                                $rs2['DoctorsName']        = (empty($activity_rows['DoctorsName']))     || $activity_rows['DoctorsName']    =='null'    ? '': $activity_rows['DoctorsName'];
                                $rs2['Name']               = (empty($activity_rows['Name']))            || $activity_rows['Name']           =='null'    ? '': $activity_rows['Name'];
                                $rs2['Description']        = (empty($activity_rows['Description']))     || $activity_rows['Description']    =='null'    ? '': $activity_rows['Description'];
                                //$rs2['MedicineCount']      = (empty($activity_rows['MedicineCount']))   || $activity_rows['MedicineCount']  =='nu'      ? '': $activity_rows['MedicineCount'];
                                $rs2['MedicationCount']      = (empty($activity_rows['MedicineCount']))   || $activity_rows['MedicineCount']  =='nu'      ? '': $activity_rows['MedicineCount'];
                                //$rs2['MedicineType']       = (empty($activity_rows['MedicineType']))    || $activity_rows['MedicineType']   =='null'    ? '': $activity_rows['MedicineType'];
                                $rs2['MedicationType']       = (empty($activity_rows['MedicineType']))    || $activity_rows['MedicineType']   =='null'    ? '': $activity_rows['MedicineType'];
                                $rs2['ActionText']         = (empty($activity_rows['ActionText']))      || $activity_rows['ActionText']     =='null'    ? '': $activity_rows['ActionText'];
                                $rs2['When']               = (empty($activity_rows['When']))            || $activity_rows['When']           =='null'    ? '': $activity_rows['When'];
                                $rs2['Instruction']        = (empty($activity_rows['Instruction']))     || $activity_rows['Instruction']    =='null'    ? '': $activity_rows['Instruction'];
                                $rs2['Frequency']          = (empty($activity_rows['Frequency']))       || $activity_rows['Frequency']      =='null'    ? '': $activity_rows['Frequency'];
                                $rs2['FrequencyString']    = (empty($activity_rows['FrequencyString'])) || $activity_rows['FrequencyString']=='null'    ? '': $activity_rows['FrequencyString'];
                                $rs2['HowLong']            = (empty($activity_rows['HowLong']))         || $activity_rows['HowLong']        =='null'    ? '': $activity_rows['HowLong'];
                                $rs2['HowLongType']        = (empty($activity_rows['HowLongType']))     || $activity_rows['HowLongType']    =='null'    ? '': $activity_rows['HowLongType'];
                                $rs2['IsCritical']         = (empty($activity_rows['IsCritical']))      || $activity_rows['IsCritical']     =='null'    ? '': $activity_rows['IsCritical'];
                                $rs2['ResponseRequired']   = (empty($activity_rows['ResponseRequired']))|| $activity_rows['ResponseRequired']=='null'   ? '': $activity_rows['ResponseRequired'];
                                $rs2['Date']               = (empty($activity_rows['Date']))            || $activity_rows['Date']           =='null'    ? '': $activity_rows['Date'];
                                $rs2['Time']               = (empty($activity_rows['Time']))            || $activity_rows['Time']           =='null'    ? '': $activity_rows['Time'];
                                $rs2['AppointmentDuration']               = (empty($activity_rows['AppointmentDuration']))            || $activity_rows['AppointmentDuration']           =='null'    ? '': $activity_rows['AppointmentDuration'];
                                $rs2['AppointmentPlace']               = (empty($activity_rows['AppointmentPlace']))            || $activity_rows['AppointmentPlace']           =='null'    ? '': $activity_rows['AppointmentPlace'];
                                $rs2['SelfTestID']         = (empty($activity_rows['SelfTestID']))      || $activity_rows['SelfTestID']     =='null'    ? '': $activity_rows['SelfTestID'];
                                $rs2['RemindBefore']       = (empty($activity_rows['RemindBefore']))    || $activity_rows['RemindBefore']   =='null'    ? '': $activity_rows['RemindBefore'];
                                $rs2['Status']             = (empty($activity_rows['Status']))          || $activity_rows['Status']         =='null'    ? '': $activity_rows['Status'];
                                array_push($activities,$rs2);
                                }
                            }
                        }
                        $rs1['Activities'] = $activities;
                    array_push($self_plans,$rs1); 
                    }
                }
            // }
            // else
            // {
            //     $directory = array();
            //     $self_plans = array();
            // }  
            /*END OF SELF PLANS*/

            $report_timestamp      = mysql_result(mysql_query("select max(UpdatedDate) from MYFOLDER_PARENT 
                                                                where UserID='$userid'"),0);

            $get_reports            =   "select ID,ReportName,ReportGivenBy,ReportCreatedOn,TypeOfReport,CreatedDate,Status
                                        from MYFOLDER_PARENT where UserID='$userid' and Status='A' 
                                        order by CreatedDate desc";
            //echo $get_reports;exit;
        }
        else
        {
            $report_timestamp      = mysql_result(mysql_query("select max(UpdatedDate) from MYFOLDER_PARENT 
                                                                where UserID='$userid' and SourceofUpload!='M'"),0);

            $get_reports            =   "select ID,ReportName,ReportGivenBy,ReportCreatedOn,TypeOfReport,CreatedDate,Status
                                        from MYFOLDER_PARENT where UserID='$userid' and Status='A' and SourceofUpload!='M'
                                        order by CreatedDate desc";
            //echo $get_reports;exit;    
        }

        if(empty($report_timestamp))
        {
            $report_timestamp = "";
        }

        $get_reports_qry    = mysql_query($get_reports);
        $get_reports_count  = mysql_num_rows($get_reports_qry);
        if($get_reports_count>0)
        {
            //echo 123;exit;
            while($analytics_row = mysql_fetch_array($get_reports_qry))
            {
                $id                     = (empty($analytics_row['ID']))             ? '' : $analytics_row['ID'];
                $rep['ReportID']        = $id;
                $rep['ReportName']      = (empty($analytics_row['ReportName']))     ? '' : stripslashes($analytics_row['ReportName']);
                $rep['ReportGivenBy']   = (empty($analytics_row['ReportGivenBy']))  ? '' : stripslashes($analytics_row['ReportGivenBy']);
                $rep['ReportCreatedOn'] = (empty($analytics_row['ReportCreatedOn']))? '' : date('Y-m-d',strtotime($analytics_row['ReportCreatedOn']));
                $rep['TypeOfReport']    = (empty($analytics_row['TypeOfReport']))   ? '' : $analytics_row['TypeOfReport'];
                $rep['Status']          = (empty($analytics_row['Status']))         ? '' : $analytics_row['Status'];
                $rep['CreatedDate']     = (empty($analytics_row['CreatedDate']))    ? '' : $analytics_row['CreatedDate'];
                $image_urls = array();
                if($id!="")
                {
                    $get_image_urls     = "select ID,FileName,DisplayName,Status from MYFOLDER_CHILD where MyFolderParentID='$id' and Status='A'";
                    $get_image_urls_qry = mysql_query($get_image_urls);
                    $get_image_urls_count=mysql_num_rows($get_image_urls_qry);
                    
                    if($get_image_urls_count>0)
                    {
                    
                        while($image_rows = mysql_fetch_array($get_image_urls_qry))
                        {
                            $img['ReportChildID']   = $image_rows['ID'];
                            $img['FileName']        = "http://".$host_server.$path.$image_rows['FileName'];
                            $img['DisplayName']     = stripslashes($image_rows['DisplayName']);
                            $img['Status']          = $image_rows['Status'];
                        array_push($image_urls,$img);
                        }
                    }
                    if(!empty($image_urls))
                    {
                        $rep['FileNames']     = $image_urls;
                    }
                    else
                    {
                       $rep['FileNames']     = $image_urls; 
                    }
                    
                }
                else
                {
                $rep['FileNames']     = $image_urls;
                }
                
            array_push($reports,$rep);
            //echo "<pre>";print_r($reports);
            }
        }
        
        if($paid=='N')
        {
            $reports = array();
        }

//print_r($plan_list);exit;


        foreach($assigned_plans as $plancode)
        {
            //mysql_query("update USER_PLAN_HEADER set UserStartOrUpdateDateTime='$now' where PlanCode='$plancode' 
                        //and UserID = '$userid'");


            $result             = array();
            $social_media_info  = array();
            //$res5['INDIVIDUAL_PLANS']  = $plancode;
            $get_plan_info  = "select distinct t1.PlanCode,t1.MerchantID,t1.CategoryID,t4.CategoryName,t1.PlanName,t1.PlanDescription,t1.PlanStatus,
                                t1.PlanCurrencyCode,t1.PlanCost,t1.PlanCoverImagePath,t1.CreatedDate,t2.RoleID,t2.Status,
                                t3.CompanyName,t3.CompanyEmailID,t3.CompanyMobileNo,t3.CompanyAddressLine1,t3.CompanyAddressLine2,
                                t3.CompanyPinCode,t5.CityName as CompanyCityName,t6.StateName as CompanyStateName,
                                t7.CountryName as CompanyCountryName
                                from USER_PLAN_HEADER as t1,USER_MERCHANT_MAPPING as t2,MERCHANT_DETAILS as t3,CATEGORY_MASTER as t4,
                                CITY_DETAILS as t5,STATE_DETAILS as t6,COUNTRY_DETAILS as t7
                                where t1.MerchantID=t2.MerchantID and t2.MerchantID=t3.MerchantID and t2.UserID='$userid' 
                                and t1.CategoryID=t4.CategoryID and t1.PlanCode='$plancode' and t3.CompanyCountryCode=t7.CountryCode 
                                and t3.CompanyStateID=t6.StateID and t3.CompanyCityID=t5.CityID and t2.Status='A' 
                                and t1.UserID='$userid' and t2.RoleID=5";
            //echo $get_plan_info;exit;
            $get_plan_info_qry= mysql_query($get_plan_info);
            $get_count       = mysql_num_rows($get_plan_info_qry);
            if($get_count)
            {
            $plan_info          = array();
            $social_media_info  = array();
            $analytics          = array();
                while($plan_info_rows = mysql_fetch_array($get_plan_info_qry))
                { 
                    $pcode                      = (empty($plan_info_rows['PlanCode']))              ? '' : $plan_info_rows['PlanCode'];
                    $res['PlanCode']            = $pcode;
                    $res['CategoryID']          = (empty($plan_info_rows['CategoryID']))            ? '' : $plan_info_rows['CategoryID'];
                    $res['CategoryName']        = (empty($plan_info_rows['CategoryName']))          ? '' : $plan_info_rows['CategoryName'];
                    $res['PlanName']            = (empty($plan_info_rows['PlanName']))              ? '' : stripslashes($plan_info_rows['PlanName']);
                    $res['PlanDescription']     = (empty($plan_info_rows['PlanDescription']))       ? '' : stripslashes($plan_info_rows['PlanDescription']);
                    $plan_cover_image_name      = (empty($plan_info_rows['PlanCoverImagePath']))    ? '' : $plan_info_rows['PlanCoverImagePath'];                   
                    $files                      = "http://".$host_server.'/'.$plan_header.$plan_cover_image_name;

                        /*if(getimagesize($files) !== false)
                        {
                            //echo "yes";
                            reduce_image_size($reduced_plan_header,$plan_cover_image_name,$files);
                        }*/
                        /*
                        $check_valid_url = valid_url($files);
                        if($check_valid_url==1)
                        {
                            if(getimagesize($files) !== false)
                            {
                                //echo $files."<br>";
                                reduce_image_size($reduced_plan_header,$plan_cover_image_name,$files);
                            }
                            $res['PlanCoverImagePath']  = $host_server.'/'.$reduced_plan_header.$plan_info_rows['PlanCoverImagePath'];
                        }
                        else
                        {
                            $res['PlanCoverImagePath']  = $host_server.'/'.$plan_header.$plan_info_rows['PlanCoverImagePath'];
                        }
                        */
                    $res['PlanCoverImagePath']  = $host_server.'/'.$plan_header.$plan_info_rows['PlanCoverImagePath'];   
                    
                    $merchant_id                = (empty($plan_info_rows['MerchantID']))            ? '' : $plan_info_rows['MerchantID'];
                    $res['MerchantID']          = $merchant_id;
                    $res['CompanyName']         = (empty($plan_info_rows['CompanyName']))           ? '' : stripslashes($plan_info_rows['CompanyName']);
                    $res['CompanyEmailID']      = (empty($plan_info_rows['CompanyEmailID']))        ? '' : $plan_info_rows['CompanyEmailID'];
                    $country_code               = (empty($plan_info_rows['CompanyCountryCode']))    ? '' : '+'.ltrim($plan_info_rows['CompanyCountryCode'],0);
                    $res['CompanyMobileNo']     = (empty($plan_info_rows['CompanyMobileNo']))       ? '' : $country_code.$plan_info_rows['CompanyMobileNo'];

                    $addressline1               = (empty($plan_info_rows['CompanyAddressLine1']))   ? '' : stripslashes($plan_info_rows['CompanyAddressLine1']);
                    $addressline2               = (empty($plan_info_rows['CompanyAddressLine2']))   ? '' : stripslashes($plan_info_rows['CompanyAddressLine2']);
                    $pincode                    = (empty($plan_info_rows['CompanyPinCode']))        ? '' : $plan_info_rows['CompanyPinCode'];

                    $cityname                   = (empty($plan_info_rows['CompanyCityName']))       ? '' : $plan_info_rows['CompanyCityName'];

                    $statename                  = (empty($plan_info_rows['CompanyStateName']))      ? '' : $plan_info_rows['CompanyStateName'];

                    $countryname                = (empty($plan_info_rows['CompanyCountryName']))    ? '' : $plan_info_rows['CompanyCountryName'];

                    $res['CompanyAddressLine1'] = $addressline1;
                    $res['CompanyAddressLine2'] = $addressline2;
                    $res['CompanyPinCode']      = $pincode;
                    $res['CompanyCityName']     = $cityname;
                    $res['CompanyStateName']    = $statename;
                    $res['CompanyCountryName']  = $countryname;

                    $addressline1       = (empty($addressline1))    ? '' : $addressline1;
                    $addressline2       = (empty($addressline2))    ? '' : ', '.$addressline2;
                    $pincode            = (empty($pincode))         ? '' : ', '.$pincode;
                    $cityname           = (empty($cityname))        ? '' : ', '.$cityname;
                    $statename          = (empty($statename))       ? '' : ', '.$statename;
                    $countryname        = (empty($countryname))     ? '' : ', '.$countryname;

                    $res['CompanyFullAddress']  = $addressline1.$addressline2.$pincode.$cityname.$statename.$countryname;

                array_push($plan_info,$res);
                }
                $goal_info          = array();
                   //Get GOAL DETAILS
                    $get_goal = "select PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where PlanCode='$plancode' and UserID = '$userid'";
                      //echo $get_goal;exit;
                      $get_goal_run = mysql_query($get_goal);
                      $get_goal_count = mysql_num_rows($get_goal_run);
                      if($get_goal_count){
                        while ($goals = mysql_fetch_array($get_goal_run)) {
                            $resgoals['GoalNo']             = $goals['GoalNo'];
                            $resgoals['GoalDescription']    = $goals['GoalDescription'];
                            $resgoals['DisplayedWith']      = rtrim($goals['DisplayedWith'],",");
                            array_push($goal_info,$resgoals);
                        }
                      }
                 //Social Media Information
                $get_social_media       = "select MerchantID,SocialMediaName,SocialMediaLink from MERCHANT_SOCIAL_MEDIA 
                                            where MerchantID='$merchant_id'";
                //echo $get_social_media;exit;
                $get_social_media_query = mysql_query($get_social_media);
                $count_of_social_media  = mysql_num_rows($get_social_media_query);
                if($count_of_social_media > 0)
                {
                    while($media = mysql_fetch_array($get_social_media_query))
                    {
                        $r['MerchantID']        = $merchant_id;
                        $r['SocialMediaName']   = $media['SocialMediaName'];
                        $r['SocialMediaLink']   = $media['SocialMediaLink'];
                    array_push($social_media_info,$r);
                    }
                }

                /*CODE TO CHECK WHETHER PLAN WAS SYNCED EARLIER OR NOT*/
                $edit   = mysql_fetch_array(mysql_query("select UserStartOrUpdateDateTime from USER_PLAN_HEADER where UserID='$userid' and PlanCode='$pcode' and MerchantID='$merchant_id'"));
                $plan_synced_date =  $edit['UserStartOrUpdateDateTime'];
                //echo $plan_synced_date;exit;
               /*END OF CODE TO CHECK WHETHER PLAN WAS SYNCED EARLIER OR NOT*/

                 /*GET ANALYTICS FOR EXTERNAL PLAN(if plan was synced earlier then there may be analytics data that was entered which is to be returned in case user lost his phone)*/
                if($timestamp=="" && $plan_synced_date!=NULL && $plan_synced_date!='0000-00-00 00:00:00')
                {
                $get_analytics = "select '1' as SectionID, t1.PlanCode, t1.PrescriptionNo as PrescriptionNo_SelfTestID,t1.RowNo,Date(t1.DateTime) as Date,
                    Time(t1.DateTime) as Time,t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,
                    t2.MedicineName as MedName_TestName,t3.MerchantID 
                    from USER_MEDICATION_DATA_FROM_CLIENT as t1,USER_MEDICATION_DETAILS as t2,USER_PLAN_HEADER as t3 
                    where t1.PlanCode='$pcode' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                    and t1.UserID=t2.UserID and t1.PlanCode=t3.PlanCode and t1.UserID=t3.UserID 
                    and t1.PrescriptionNo=t2.PrescriptionNo and t1.RowNo=t2.RowNo
                    union
                    select '6' as SectionID, t1.PlanCode, t1.PrescriptionNo,t1.RowNo,Date(t1.DateTime) as Date,
                    Time(t1.DateTime) as Time,
                    t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,t2.MedicineName as MedName_TestName,
                    t3.MerchantID 
                    from USER_INSTRUCTION_DATA_FROM_CLIENT as t1,USER_INSTRUCTION_DETAILS as t2,USER_PLAN_HEADER as t3 
                    where t1.PlanCode='$pcode' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                    and t1.UserID=t2.UserID and t1.PlanCode=t3.PlanCode and t1.UserID=t3.UserID
                    and t1.PrescriptionNo=t2.PrescriptionNo and t1.RowNo=t2.RowNo 
                    union
                    select '3-1' as SectionID, t7.PlanCode, t7.SelfTestID,t7.RowNo,Date(t7.DateTime) as Date,Time(t7.DateTime) as Time,
                    t7.DateTime,convert(t7.ResponseDataValue,char) as Value, t7.ResponseDataName,t9.MerchantID 
                    from USER_SELF_TEST_DATA_FROM_CLIENT as t7,USER_SELF_TEST_DETAILS as t8,USER_PLAN_HEADER as t9 
                    where t7.PlanCode='$pcode' and t7.UserID='$userid' and t7.PlanCode=t8.PlanCode 
                    and t7.UserID=t8.UserID and t7.PlanCode=t9.PlanCode and t7.UserID=t9.UserID 
                    and t7.SelfTestID=t8.SelfTestID and t7.RowNo=t8.RowNo
                    order by DateTime";
                //echo $get_analytics;exit;
                $get_analytics_query = mysql_query($get_analytics);
                $get_analytics_count = mysql_num_rows($get_analytics_query);
                    if($get_analytics_count > 0)
                    {
                        while($analytics_row = mysql_fetch_array($get_analytics_query))
                        {
                            $a['PlanType']                  = 'external';/*External Plans*/
                            $a['MerchantID']                = $merchant_id;
                            $a['PlanCode']                  = $pcode;
                            $a['UserID']                    = $userid;
                            $a['SectionID']                 = $analytics_row['SectionID'];
                            $a['PrescriptionNo_SelfTestID'] = (empty($analytics_row['PrescriptionNo_SelfTestID']))  ? '' : trim($analytics_row['PrescriptionNo_SelfTestID']);
                            $a['RowNo']                     = (empty($analytics_row['RowNo']))                      ? '' : trim($analytics_row['RowNo']);
                            $a['MedName_TestName']          = (empty($analytics_row['MedName_TestName']))           ? '' : trim($analytics_row['MedName_TestName']);
                            $a['Date']                      = (empty($analytics_row['Date']))                       ? '' : trim($analytics_row['Date']);
                            $a['Time']                      = (empty($analytics_row['Time']))                       ? '' : trim($analytics_row['Time']);
                            $a['DateTime']                  = (empty($analytics_row['DateTime']))                   ? '' : trim($analytics_row['DateTime']);
                            $a['Value']                     = (empty($analytics_row['Value']))                      ? '' : trim($analytics_row['Value']);
                        array_push($analytics,$a);
                        }
                    }
                }
                /*END OF GET ANALYTICS*/

                /*GET ANALYTICS FOR SELF PLAN(if plan was synced earlier then there may be analytics data that was entered which is to be returned in case user lost his phone)*/
                
                $get_self_plan_analytics = "select '1' as SectionID, t1.PlanCode, t1.PrescriptionNo as PrescriptionNo_SelfTestID,t1.RowNo,Date(t1.DateTime) as Date,
                    Time(t1.DateTime) as Time,t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,
                    t2.Name as MedName_TestName,t2.PlanCode as MerchantID 
                    from USER_MEDICATION_DATA_FROM_CLIENT as t1,USER_SELF_PLAN_ACTIVITIES as t2 
                    where t1.PlanCode=t2.PlanCode and t1.UserID='$userid'
                    and t1.UserID=t2.UserID and t2.SectionID='1' and t1.PlanCode REGEXP '^[0-9]' 
                    and t1.PrescriptionNo=t2.PrescriptionNo and t1.RowNo=t2.RowNo
                    union
                    select '3-1' as SectionID, t7.PlanCode, t7.SelfTestID,t7.RowNo,Date(t7.DateTime) as Date,Time(t7.DateTime) as Time,
                    t7.DateTime,convert(t7.ResponseDataValue,char) as Value, t7.ResponseDataName,t8.PlanCode as MerchantID 
                    from USER_SELF_TEST_DATA_FROM_CLIENT as t7,USER_SELF_PLAN_ACTIVITIES as t8
                    where t7.PlanCode=t8.PlanCode and t7.UserID='$userid'
                    and t7.UserID=t8.UserID and t8.SectionID='3-1' and t7.PlanCode REGEXP '^[0-9]'
                    and t7.SelfTestID=t8.SelfTestID and t7.RowNo=t8.RowNo
                    order by DateTime";
                //echo $get_analytics;exit;
                $get_self_plan_analytics_query = mysql_query($get_self_plan_analytics);
                $get_self_plan_analytics_count = mysql_num_rows($get_self_plan_analytics_query);
                    if($get_self_plan_analytics_count > 0)
                    {
                        while($self_plan_analytics_row = mysql_fetch_array($get_self_plan_analytics_query))
                        {
                            $b['PlanType']                  = 'self';/*Self Plans*/
                            $b['MerchantID']                = (empty($self_plan_analytics_row['MerchantID']))                   ? '' : trim($self_plan_analytics_row['MerchantID']);
                            $b['PlanCode']                  = (empty($self_plan_analytics_row['PlanCode']))                     ? '' : trim($self_plan_analytics_row['PlanCode']);
                            $b['UserID']                    = $userid;
                            $b['SectionID']                 = $self_plan_analytics_row['SectionID'];
                            $b['PrescriptionNo_SelfTestID'] = (empty($self_plan_analytics_row['PrescriptionNo_SelfTestID']))  ? '' : trim($self_plan_analytics_row['PrescriptionNo_SelfTestID']);
                            $b['RowNo']                     = (empty($self_plan_analytics_row['RowNo']))                      ? '' : trim($self_plan_analytics_row['RowNo']);
                            $b['MedName_TestName']          = (empty($self_plan_analytics_row['MedName_TestName']))           ? '' : trim($self_plan_analytics_row['MedName_TestName']);
                            $b['Date']                      = (empty($self_plan_analytics_row['Date']))                       ? '' : trim($self_plan_analytics_row['Date']);
                            $b['Time']                      = (empty($self_plan_analytics_row['Time']))                       ? '' : trim($self_plan_analytics_row['Time']);
                            $b['DateTime']                  = (empty($self_plan_analytics_row['DateTime']))                   ? '' : trim($self_plan_analytics_row['DateTime']);
                            $b['Value']                     = (empty($self_plan_analytics_row['Value']))                      ? '' : trim($self_plan_analytics_row['Value']);
                        array_push($analytics,$b);
                        }
                    }
                /*END OF GET ANALYTICS FOR SELF PLAN*/


                //GET MEDICATION DETAILS
                $get_medication = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.MedicineCount,t2.MedicineTypeID,t4.MedicineType,t4.Action,t2.RowNo,t2.SpecificTime,t3.ShortHand,t2.Instruction,t2.When,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString,t2.Status,t2.Link,t2.CreatedDate
                                    from USER_MEDICATION_HEADER as t1,USER_MEDICATION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3,
                                    MEDICINE_TYPES as t4
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t2.MedicineTypeID=t4.SNo and t1.UserID = '$userid' $timestamp_query";
                //echo $get_medication;exit;
                $get_medication_qry     = mysql_query($get_medication);
                $get_medication_count   = mysql_num_rows($get_medication_qry);
                if($get_medication_count)
                {
                    while($medication_rows=mysql_fetch_array($get_medication_qry))
                    {
                        $wheninput                      = $medication_rows['When'];
                        if($wheninput == '16'){
                            $specifictimebundle           = (empty($medication_rows['SpecificTime']))             ? '' : $medication_rows['SpecificTime'];
                            $starray = array();
                            $starray = explode(",",$specifictimebundle);
                            foreach ($starray as $st) {
                       if($st != ""){
                        $st = date('H:i:s',strtotime($st));
                        $res1['SectionID']              = '1';
                        $res1['PrescriptionNo']         = (empty($medication_rows['PrescriptionNo']))           ? '' : $medication_rows['PrescriptionNo'];
                        $res1['PrescriptionName']       = (empty($medication_rows['PrescriptionName']))         ? '' : stripslashes($medication_rows['PrescriptionName']);
                        $res1['DoctorsName']            = (empty($medication_rows['DoctorsName']))              ? '' : stripslashes($medication_rows['DoctorsName']);
                        $res1['MedicineName']           = (empty($medication_rows['MedicineName']))             ? '' : stripslashes($medication_rows['MedicineName']);
                        $medication_count               = (empty($medication_rows['MedicineCount']))            ? '' : $medication_rows['MedicineCount'];
                        $medicinetypeid                 = (empty($medication_rows['MedicineTypeID']))           ? '' : $medication_rows['MedicineTypeID'];
                        if($medication_count<=1 || $medicinetypeid==4 || $medicinetypeid==5)
                        {
                            $text = "";
                        }
                        else
                        {
                            $text = "s";
                        }
                        //echo 123;
                        $res1['MedicationCount']        = $medication_count;
                        $res1['MedicationType']         = (empty($medication_rows['MedicineType']))             ? '' : $medication_rows['MedicineType'].$text;
                        $res1['ActionText']             = (empty($medication_rows['Action']))                   ? '' : $medication_rows['Action'];
                        $res1['When']                   = (empty($medication_rows['ShortHand']))                ? '' : $medication_rows['ShortHand'];
                        $res1['RowNo']                  = (empty($medication_rows['RowNo']))                    ? '' : $medication_rows['RowNo'];
                        $res1['Instruction']            = (empty($medication_rows['Instruction']))              ? '' : $medication_rows['Instruction'];
                        if($res1['Instruction'] == "NA"){
                            $res1['Instruction'] = "With Food";
                        }
                        //$res1['Instruction']            = ("NA") ? 'With Food' : $medication_rows['Instruction'];
                         //echo $res1['Instruction'];exit;
                        $res1['Frequency']              = (empty($medication_rows['Frequency']))                ? '' : $medication_rows['Frequency'];
                        $res1['FrequencyString']        = (empty($medication_rows['FrequencyString']))          ? '' : $medication_rows['FrequencyString'];
                        $howlong                        = (empty($medication_rows['HowLong']))                  ? '' : $medication_rows['HowLong'];
                        $howlongtype                    = (empty($medication_rows['HowLongType']))              ? '' : $medication_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res1['HowLong']                = $howlong;
                        $res1['HowLongType']            = $howlongtype;
                        $res1['IsCritical']             = (empty($medication_rows['IsCritical']))               ? '' : $medication_rows['IsCritical'];
                        $res1['ResponseRequired']       = (empty($medication_rows['ResponseRequired']))         ? '' : $medication_rows['ResponseRequired'];
                        $res1['StartFlag']              = (empty($medication_rows['StartFlag']))                ? '' : $medication_rows['StartFlag'];
                        $res1['NoOfDaysAfterPlanStarts']= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '' : $medication_rows['NoOfDaysAfterPlanStarts'];
                        $res1['SpecificDate']           = (empty($medication_rows['SpecificDate']))             ? '' : $medication_rows['SpecificDate'];
                        $res1['SpecificTime']           = $st;
                        $res1['Status']                 = (empty($medication_rows['Status']))                   ? '' : $medication_rows['Status'];
                        $res1['Link']                   = (empty($medication_rows['Link']))                     ? '' : $medication_rows['Link'];
                        $res1['AppointmentDate']        = "";   $res1['AppointmentTime']        = "";   $res1['AppointmentShortName']   = "";
                        $res1['AppointmentRequirements']= "";   $res1['LabTestDate']            = "";   $res1['LabTestTime']            = "";
                        $res1['TestName']               = "";   $res1['SelfTestID']             = "";   $res1['TestDescription']        = "";   
                        $res1['DietNo']                 = "";   $res1['DietPlanName']           = "";   $res1['AdvisorName']            = "";   
                        $res1['DietDurationDays']       = "";   $res1['DayNo']                  = "";   $res1['MealID']                 = "";   
                        $res1['MealDescription']        = "";   $res1['ExercisePlanNo']         = "";   $res1['ExercisePlanName']       = "";  
                        $res1['ExerciseDurationDays']   = "";   $res1['ExerciseSNo']            = "";   $res1['ExerciseDescription']    = "";   
                        $res1['ExerciseInstruction']    = "";   $res1['ExerciseNoOfReps']       = "";   $res1['ExerciseDuration']       = "";   
                        $res1['LabTestID']              = "";   $res1['LabTestRequirements']    = "";
                        $res1['AppointmentDuration']    = "";   $res1['AppointmentPlace']       = "";
                        $res1['SpecialType']       = "";      
                        $res1['SpecialDuration']       = "";        
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res1);
                        }*/
                    array_push($result,$res1);
                    }
                            }
                        } else {
                        $res1['SectionID']              = '1';
                        $res1['PrescriptionNo']         = (empty($medication_rows['PrescriptionNo']))           ? '' : $medication_rows['PrescriptionNo'];
                        $res1['PrescriptionName']       = (empty($medication_rows['PrescriptionName']))         ? '' : stripslashes($medication_rows['PrescriptionName']);
                        $res1['DoctorsName']            = (empty($medication_rows['DoctorsName']))              ? '' : stripslashes($medication_rows['DoctorsName']);
                        $res1['MedicineName']           = (empty($medication_rows['MedicineName']))             ? '' : stripslashes($medication_rows['MedicineName']);
                        $medication_count               = (empty($medication_rows['MedicineCount']))            ? '' : $medication_rows['MedicineCount'];
                        $medicinetypeid                 = (empty($medication_rows['MedicineTypeID']))           ? '' : $medication_rows['MedicineTypeID'];
                        if($medication_count<=1 || $medicinetypeid==4 || $medicinetypeid==5)
                        {
                            $text = "";
                        }
                        else
                        {
                            $text = "s";
                        }
                        //echo 123;
                        $res1['MedicationCount']        = $medication_count;
                        $res1['MedicationType']         = (empty($medication_rows['MedicineType']))             ? '' : $medication_rows['MedicineType'].$text;
                        $res1['ActionText']             = (empty($medication_rows['Action']))                   ? '' : $medication_rows['Action'];
                        $res1['When']                   = (empty($medication_rows['ShortHand']))                ? '' : $medication_rows['ShortHand'];
                        $res1['RowNo']                  = (empty($medication_rows['RowNo']))                    ? '' : $medication_rows['RowNo'];
                        $res1['Instruction']            = (empty($medication_rows['Instruction']))              ? '' : $medication_rows['Instruction'];
                        if($res1['Instruction'] == "NA"){
                            $res1['Instruction'] = "With Food";
                        }
                        //$res1['Instruction']            = ("NA") ? 'With Food' : $medication_rows['Instruction'];
                         //echo $res1['Instruction'];exit;
                        $res1['Frequency']              = (empty($medication_rows['Frequency']))                ? '' : $medication_rows['Frequency'];
                        $res1['FrequencyString']        = (empty($medication_rows['FrequencyString']))          ? '' : $medication_rows['FrequencyString'];
                        $howlong                        = (empty($medication_rows['HowLong']))                  ? '' : $medication_rows['HowLong'];
                        $howlongtype                    = (empty($medication_rows['HowLongType']))              ? '' : $medication_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res1['HowLong']                = $howlong;
                        $res1['HowLongType']            = $howlongtype;
                        $res1['IsCritical']             = (empty($medication_rows['IsCritical']))               ? '' : $medication_rows['IsCritical'];
                        $res1['ResponseRequired']       = (empty($medication_rows['ResponseRequired']))         ? '' : $medication_rows['ResponseRequired'];
                        $res1['StartFlag']              = (empty($medication_rows['StartFlag']))                ? '' : $medication_rows['StartFlag'];
                        $res1['NoOfDaysAfterPlanStarts']= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '' : $medication_rows['NoOfDaysAfterPlanStarts'];
                        $res1['SpecificDate']           = (empty($medication_rows['SpecificDate']))             ? '' : $medication_rows['SpecificDate'];
                        $res1['SpecificTime']           = (empty($medication_rows['SpecificTime']))             ? '' : $medication_rows['SpecificTime'];
                        $res1['Status']                 = (empty($medication_rows['Status']))                   ? '' : $medication_rows['Status'];
                        $res1['Link']                   = (empty($medication_rows['Link']))                     ? '' : $medication_rows['Link'];
                        $res1['AppointmentDate']        = "";   $res1['AppointmentTime']        = "";   $res1['AppointmentShortName']   = "";
                        $res1['AppointmentRequirements']= "";   $res1['LabTestDate']            = "";   $res1['LabTestTime']            = "";
                        $res1['TestName']               = "";   $res1['SelfTestID']             = "";   $res1['TestDescription']        = "";   
                        $res1['DietNo']                 = "";   $res1['DietPlanName']           = "";   $res1['AdvisorName']            = "";   
                        $res1['DietDurationDays']       = "";   $res1['DayNo']                  = "";   $res1['MealID']                 = "";   
                        $res1['MealDescription']        = "";   $res1['ExercisePlanNo']         = "";   $res1['ExercisePlanName']       = "";  
                        $res1['ExerciseDurationDays']   = "";   $res1['ExerciseSNo']            = "";   $res1['ExerciseDescription']    = "";   
                        $res1['ExerciseInstruction']    = "";   $res1['ExerciseNoOfReps']       = "";   $res1['ExerciseDuration']       = "";   
                        $res1['LabTestID']              = "";   $res1['LabTestRequirements']    = "";
                        $res1['AppointmentDuration']    = "";   $res1['AppointmentPlace']       = "";  
                        $res1['SpecialType'] = "";
                        $res1['SpecialDuration'] = "";
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res1);
                        }*/
                    array_push($result,$res1);
                    }
                    //exit;
                        /*If Howlong ie; duration is 0 then dont send*/

                        /*End of Checking value of Howlong*/
                    //array_push($result,$res1);
                    }
                }
                //END OF GET MEDICATION DETAILS

                 //GET APPOINTMENT DETAILS
                    $get_appointment= "select distinct t2.AppointmentDate, t2.AppointmentTime, t2.AppointmentShortName, t2.DoctorsName, t2.AppointmentDuration, t2.AppointmentPlace,
                                        t2.AppointmentRequirements, t2.Status, t2.CreatedDate
                                      from USER_APPOINTMENT_HEADER as t1,USER_APPOINTMENT_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.PlanCode='$plancode' and t1.UserID = t2.UserID 
                                      and t1.UserID = '$userid' $timestamp_query";
                   //echo $get_appointment.'<br>';exit;
                    $get_appointment_qry    = mysql_query($get_appointment);
                    $get_appointment_count  = mysql_num_rows($get_appointment_qry);
                    if($get_appointment_count)
                    {
                        while($appointment_rows = mysql_fetch_array($get_appointment_qry))
                        {
                            $res2['SectionID']              = '2';
                            $res2['AppointmentDate']        = (empty($appointment_rows['AppointmentDate']))         ? '' : $appointment_rows['AppointmentDate'];
                            $res2['AppointmentTime']        = (empty($appointment_rows['AppointmentTime']))         ? '' : $appointment_rows['AppointmentTime'];
                            $res2['AppointmentShortName']   = (empty($appointment_rows['AppointmentShortName']))    ? '' : stripslashes($appointment_rows['AppointmentShortName']);
                            $res2['DoctorsName']            = (empty($appointment_rows['DoctorsName']))             ? '' : stripslashes($appointment_rows['DoctorsName']);
                            $res2['AppointmentRequirements']= (empty($appointment_rows['AppointmentRequirements'])) ? '' : stripslashes($appointment_rows['AppointmentRequirements']); 
                            $res2['Status']                 = (empty($appointment_rows['Status']))                  ? '' : $appointment_rows['Status'];
                            $res2['AppointmentDuration']                 = (empty($appointment_rows['AppointmentDuration']))                  ? '' : $appointment_rows['AppointmentDuration'];
                            $res2['AppointmentDuration'] = substr($res2['AppointmentDuration'], 0, 5);
                            $res2['AppointmentPlace']                 = (empty($appointment_rows['AppointmentPlace']))                  ? '' : $appointment_rows['AppointmentPlace'];
                            $res2['PrescriptionNo']         = "";   $res2['PrescriptionName']       = "";       $res2['MedicineName']           = "";
                            $res2['When']                   = "";   $res2['Instruction']            = "";       $res2['Frequency']              = "";
                            $res2['FrequencyString']        = "";   $res2['HowLong']                = "";       $res2['HowLongType']            = "";
                            $res2['IsCritical']             = "";   $res2['ResponseRequired']       = "";       $res2['StartFlag']              = "";
                            $res2['NoOfDaysAfterPlanStarts']= "";   $res2['SpecificDate']           = "";       $res2['LabTestDate']            = "";
                            $res2['LabTestTime']            = "";   $res2['TestName']               = "";       $res2['SelfTestID']             = "";
                            $res2['RowNo']                  = "";   $res2['TestDescription']        = "";       $res2['DietNo']                 = "";
                            $res2['DietPlanName']           = "";   $res2['AdvisorName']            = "";       $res2['DietDurationDays']       = "";
                            $res2['DayNo']                  = "";   $res2['MealID']                 = "";       $res2['MealDescription']        = "";
                            $res2['SpecificTime']           = "";   $res2['ExercisePlanNo']         = "";       $res2['ExercisePlanName']       = "";
                            $res2['ExerciseDurationDays']   = "";   $res2['ExerciseSNo']            = "";       $res2['ExerciseDescription']    = "";
                            $res2['ExerciseInstruction']    = "";   $res2['ExerciseNoOfReps']       = "";       $res2['ExerciseDuration']       = "";
                            $res2['Link']                   = "";   $res2['LabTestID']              = "";       $res2['LabTestRequirements']    = "";
                             $res2['SpecialType'] = "";
                            $res2['SpecialDuration'] = "";
                        array_push($result,$res2);
                        }
                    }
                    //END OF APPOINTMENT DETAILS

                    //SELF TEST DETAILS
                    $get_self_test = "select distinct t1.SelfTestID,t2.RowNo,t2.MedicalTestID,t2.TestName,t2.DoctorsName,t2.TestDescription,t2.InstructionID,
                                      t2.Frequency,t2.HowLong,t2.HowLongType,t2.ResponseRequired,t2.StartFlag,t2.NoOfDaysAfterPlanStarts,
                                      t2.SpecificDate,t2.FrequencyString,t2.Status,t2.Link,t2.CreatedDate
                                      from USER_SELF_TEST_HEADER as t1,USER_SELF_TEST_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.SelfTestID=t2.SelfTestID and t1.PlanCode='$plancode' 
                                      and t1.UserID = t2.UserID and t1.UserID = '$userid' $timestamp_query";
                    //echo $get_self_test;exit;
                    $get_self_test_qry  = mysql_query($get_self_test);
                    $get_self_test_count= mysql_num_rows($get_self_test_qry);
                    if($get_self_test_count)
                    {
                        while($self_test_rows=mysql_fetch_array($get_self_test_qry))
                        {
                            $res31['SectionID']                 = '3-1';
                            $res31['SelfTestID']                = (empty($self_test_rows['SelfTestID']))                ? '' : $self_test_rows['SelfTestID'];
                            $res31['RowNo']                     = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
                            $res31['TestName']                  = (empty($self_test_rows['TestName']))                  ? '' : stripslashes($self_test_rows['TestName']);
                            $MedicalTestID                      = (empty($self_test_rows['MedicalTestID']))                  ? '' : stripslashes($self_test_rows['MedicalTestID']);
                            if($MedicalTestID == "5"){
                                $res31['SpecialType'] = "P";
                                $res31['SpecialDuration'] = "01:30";
                            } else {
                                $res31['SpecialType'] = "";
                                $res31['SpecialDuration'] = "";
                            }
                            $res31['DoctorsName']               = (empty($self_test_rows['DoctorsName']))               ? '' : stripslashes($self_test_rows['DoctorsName']); 
                            $res31['TestDescription']           = (empty($self_test_rows['TestDescription']))           ? '' : stripslashes($self_test_rows['TestDescription']);
                            $res31['Instruction']               = (empty($self_test_rows['InstructionID']))             ? '' : $self_test_rows['InstructionID']; 
                            $res31['Frequency']                 = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
                            $res31['FrequencyString']           = (empty($self_test_rows['FrequencyString']))           ? '' : $self_test_rows['FrequencyString'];
                           
                            $howlong                            = (empty($self_test_rows['HowLong']))                  ? '' : $self_test_rows['HowLong'];
                            $howlongtype                        = (empty($self_test_rows['HowLongType']))              ? '' : $self_test_rows['HowLongType'];

                            if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                            {
                            //echo 123;exit;
                            $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                            $howlongtype        = "Days";
                            }

                            $res31['HowLong']                   = $howlong;
                            $res31['HowLongType']               = $howlongtype;

                            $res31['ResponseRequired']          = (empty($self_test_rows['ResponseRequired']))          ? '' : $self_test_rows['ResponseRequired'];
                            $res31['StartFlag']                 = (empty($self_test_rows['StartFlag']))                 ? '' : $self_test_rows['StartFlag']; 
                            $res31['NoOfDaysAfterPlanStarts']   = (empty($self_test_rows['NoOfDaysAfterPlanStarts']))   ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
                            $res31['SpecificDate']              = (empty($self_test_rows['SpecificDate']))              ? '' : $self_test_rows['SpecificDate'];
                            $res31['Status']                    = (empty($self_test_rows['Status']))                    ? '' : $self_test_rows['Status'];
                            $res31['Link']                      = (empty($self_test_rows['Link']))                      ? '' : $self_test_rows['Link'];
                            $res31['PrescriptionNo']            = "";   $res31['PrescriptionName']          = "";   $res31['MedicineName']              = "";
                            $res31['When']                      = "";   $res31['IsCritical']                = "";   $res31['AppointmentDate']           = "";
                            $res31['AppointmentTime']           = "";   $res31['AppointmentShortName']      = "";   $res31['AppointmentRequirements']   = "";
                            $res31['LabTestDate']               = "";   $res31['LabTestTime']               = "";   $res31['DietNo']                    = "";
                            $res31['DietPlanName']              = "";   $res31['AdvisorName']               = "";   $res31['DietDurationDays']          = "";
                            $res31['DayNo']                     = "";   $res31['MealID']                    = "";   $res31['MealDescription']           = "";
                            $res31['SpecificTime']              = "";   $res31['ExercisePlanNo']            = "";   $res31['ExercisePlanName']          = "";
                            $res31['ExerciseDurationDays']      = "";   $res31['ExerciseSNo']               = "";   $res31['ExerciseDescription']       = "";
                            $res31['ExerciseInstruction']       = "";   $res31['ExerciseNoOfReps']          = "";   $res31['ExerciseDuration']          = "";
                            $res31['LabTestID']                 = "";   $res31['LabTestRequirements']       = "";
                            $res31['AppointmentDuration']    = "";   $res31['AppointmentPlace']       = "";  
                        array_push($result,$res31);
                             if($MedicalTestID == "5"){
                                //POSTPRANDIAL INSTRUCTION
                                //$st = date('H:i:s',strtotime($st));
                                $res8['SectionID']              = '8';
                                $res8['PrescriptionNo']         = (empty($self_test_rows['SelfTestID']))                     ? '' : $self_test_rows['SelfTestID']; 
                                $res8['PrescriptionName']       = "Diet Instruction";
                                $res8['DoctorsName']            = (empty($self_test_rows['DoctorsName']))               ? '' : stripslashes($self_test_rows['DoctorsName']);
                                
                                if($self_test_rows['InstructionID'] == "5"){
                                     $res8['When']                   = "1-0-0-0";
                                     $res8['MedicineName']           = "Have a nutritious Breakfast";
                                } else if($self_test_rows['InstructionID'] == "9"){
                                     $res8['When']                   = "0-1-0-0";
                                     $res8['MedicineName']           = "Have a nutritious Lunch";
                                }
                                 else if($self_test_rows['InstructionID'] == "18"){
                                     $res8['When']                   = "0-0-1-0";
                                     $res8['MedicineName']           = "Have a nutritious Dinner";
                                } else {
                                    $res8['When']                   = "1-0-0-0";
                                    $res8['MedicineName']           = "Have a nutritious meal";
                                }
                                $res8['RowNo']                  = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
                                $res8['Instruction']            = "With Food"; 
                                $res8['Frequency']              = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
                                $res8['ActionText']             = "Diet";
                                $res8['FrequencyString']        = (empty($self_test_rows['FrequencyString']))                 ? '' : $self_test_rows['FrequencyString'];
                                $howlong                        = (empty($self_test_rows['HowLong']))                  ? '' : $self_test_rows['HowLong'];
                                $howlongtype                    = (empty($self_test_rows['HowLongType']))              ? '' : $self_test_rows['HowLongType'];

                                if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                                {
                                //echo 123;exit;
                                $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                                $howlongtype        = "Days";
                                }

                                $res8['HowLong']                = $howlong;
                                $res8['HowLongType']            = $howlongtype;
                                $res8['IsCritical']             = "N";
                                $res8['ResponseRequired']       = "N";
                                $res8['StartFlag']              = (empty($self_test_rows['StartFlag']))                ? '' : $self_test_rows['StartFlag'];
                                $res8['NoOfDaysAfterPlanStarts']= (empty($self_test_rows['NoOfDaysAfterPlanStarts']))  ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
                                $res8['SpecificDate']           = (empty($self_test_rows['SpecificDate']))             ? '' : $self_test_rows['SpecificDate'];
                                $res8['SpecificTime']           = "";
                                $res8['Status']                 = (empty($self_test_rows['Status']))                   ? '' : $self_test_rows['Status'];
                                $res8['Link']                   = (empty($self_test_rows['Link']))                     ? '' : $self_test_rows['Link'];
                                $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                                $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                                $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                                $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                                $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                                $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                                $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                                $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                                $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                                $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = "";
                                $res8['SpecialType']       = "";      
                                $res8['SpecialDuration']       = "";                    
                                /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                                {

                                }
                                else
                                {
                                    array_push($result,$res8);
                                }*/
                            array_push($result,$res8);
                            }
                        }   
                    }
                    //END OF SELF TEST DETAILS

                    //GET LAB TEST DETAILS
                    $get_lab_test = "select distinct t1.LabTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.LabTestRequirements,
                                    t2.LabTestDate,t2.LabTestTime,t2.Status,t2.CreatedDate
                                    from USER_LAB_TEST_HEADER1 as t1,USER_LAB_TEST_DETAILS1 as t2
                                    where t1.PlanCode=t2.PlanCode and t1.LabTestID=t2.LabTestID and t1.PlanCode='$plancode' 
                                    and t1.UserID = t2.UserID and t1.UserID = '$userid' $timestamp_query";
                    $get_lab_test_qry  = mysql_query($get_lab_test);
                    $get_lab_test_count= mysql_num_rows($get_lab_test_qry);
                    if($get_lab_test_count)
                    {
                        while($lab_test_rows=mysql_fetch_array($get_lab_test_qry))
                        {
                            $res32['SectionID']                 = '3-2';
                            $res32['LabTestID']                 = (empty($lab_test_rows['LabTestID']))                  ? '' : $lab_test_rows['LabTestID'];
                            $res32['RowNo']                     = (empty($lab_test_rows['RowNo']))                      ? '' : $lab_test_rows['RowNo']; 
                            $res32['TestName']                  = (empty($lab_test_rows['TestName']))                   ? '' : stripslashes($lab_test_rows['TestName']);
                            $res32['DoctorsName']               = (empty($lab_test_rows['DoctorsName']))                ? '' : stripslashes($lab_test_rows['DoctorsName']); 
                            $res32['LabTestRequirements']       = (empty($lab_test_rows['LabTestRequirements']))        ? '' : stripslashes($lab_test_rows['LabTestRequirements']);
                            $labtest_date                       = (empty($lab_test_rows['LabTestDate']))                ? '' : $lab_test_rows['LabTestDate']; 
                            $labtest_time                       = (empty($lab_test_rows['LabTestTime']))                ? '' : $lab_test_rows['LabTestTime'];
                            $res32['LabTestDate']               = $labtest_date;
                            $res32['LabTestTime']               = $labtest_time;
                            $res32['Status']                    = (empty($lab_test_rows['Status']))                     ? '' : $lab_test_rows['Status'];
                            $res32['PrescriptionNo']            = "";   $res32['PrescriptionName']          = "";   $res32['MedicineName']              = "";
                            $res32['When']                      = "";   $res32['HowLong']                   = "";   $res32['HowLongType']               = "";
                            $res32['IsCritical']                = "";   $res32['StartFlag']                 = "";   $res32['NoOfDaysAfterPlanStarts']   = "";
                            $res32['SpecificDate']              = "";   $res32['AppointmentDate']           = "";   $res32['AppointmentTime']           = "";
                            $res32['AppointmentShortName']      = "";   $res32['AppointmentRequirements']   = "";   $res32['SelfTestID']                = "";
                            $res32['TestDescription']           = "";   $res32['Frequency']                 = "";   $res32['ResponseRequired']          = "";
                            $res32['FrequencyString']           = "";   $res32['DietNo']                    = "";   $res32['DietPlanName']              = "";
                            $res32['AdvisorName']               = "";   $res32['DietDurationDays']          = "";   $res32['DayNo']                     = "";
                            $res32['MealID']                    = "";   $res32['MealDescription']           = "";   $res32['SpecificTime']              = "";
                            $res32['ExercisePlanNo']            = "";   $res32['ExercisePlanName']          = "";   $res32['ExerciseDurationDays']      = "";
                            $res32['ExerciseSNo']               = "";   $res32['ExerciseDescription']       = "";   $res32['ExerciseInstruction']       = "";
                            $res32['ExerciseNoOfReps']          = "";   $res32['ExerciseDuration']          = "";   $res32['Link']                      = "";
                            $res32['Instruction']               = "";   
                            $res32['AppointmentDuration']    = "";   $res32['AppointmentPlace']       = "";  
                             $res32['SpecialType'] = "";
                            $res32['SpecialDuration'] = "";
                            /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $labtest_date!="")
                            {

                            }
                            else
                            {
                                array_push($result,$res32);
                            }*/
                        array_push($result,$res32);
                        }   
                    }
                    //END OF LAB TEST DETAILS

                    //GET DIET DETAILS
                    $get_diet_detials       = "select distinct t1.DietNo,t1.DietPlanName,t1.AdvisorName,t1.DietDurationDays,t2.DayNo,t2.InstructionID,t2.MealDescription,
                                            t2.SpecificTime,t2.Status,t2.Link,t2.CreatedDate
                                            from USER_DIET_HEADER as t1,USER_DIET_DETAILS as t2
                                            where t1.PlanCode=t2.PlanCode and t1.DietNo=t2.DietNo and t1.PlanCode='$plancode' 
                                            and t1.UserID = t2.UserID and t1.UserID = '$userid' $timestamp_query";
                                            //echo $get_diet_detials;exit;
                    $get_diet_detials_qry   = mysql_query($get_diet_detials);
                    $get_diet_detials_count = mysql_num_rows($get_diet_detials_qry);
                    $dietresult = array();
                    if($get_diet_detials_count)
                    {
                        while($diet_rows=mysql_fetch_array($get_diet_detials_qry))
                        {
                            $res4['SectionID']                  = '4';
                            $res4['DietNo']                     = (empty($diet_rows['DietNo']))             ? '' : $diet_rows['DietNo'];
                            $res4['DietPlanName']               = (empty($diet_rows['DietPlanName']))       ? '' : $diet_rows['DietPlanName'];
                            $res4['AdvisorName']                = (empty($diet_rows['AdvisorName']))        ? '' : $diet_rows['AdvisorName'];
                            $res4['DietDurationDays']           = (empty($diet_rows['DietDurationDays']))   ? '' : $diet_rows['DietDurationDays'];
                            $resd['DayNo']                      = (empty($diet_rows['DayNo']))              ? '' : $diet_rows['DayNo'];
                            $resd['MealID']                     = (empty($diet_rows['InstructionID']))      ? '' : $diet_rows['InstructionID'];
                            $resd['MealDescription']            = (empty($diet_rows['MealDescription']))    ? '' : $diet_rows['MealDescription'];
                            $resd['SpecificTime']               = (empty($diet_rows['SpecificTime']))       ? '' : $diet_rows['SpecificTime'];
                            $res4['Status']                     = (empty($diet_rows['Status']))             ? '' : $diet_rows['Status'];
                            $res4['Link']                       = (empty($diet_rows['Link']))               ? '' : $diet_rows['Link'];
                            array_push($dietresult,$resd);
                            
                        }
                            $res4['plan_info']                  = $dietresult;
                            $res4['LabTestDate']                = "";   $res4['LabTestTime']                = "";   $res4['TestName']                   = "";
                            $res4['DoctorsName']                = "";   $res4['Instruction']                = "";   $res4['ResponseRequired']           = "";
                            $res4['PrescriptionNo']             = "";   $res4['PrescriptionName']           = "";   $res4['MedicineName']               = "";
                            $res4['When']                       = "";   $res4['HowLong']                    = "";   $res4['HowLongType']                = "";
                            $res4['IsCritical']                 = "";   $res4['StartFlag']                  = "";   $res4['NoOfDaysAfterPlanStarts']    = "";
                            $res4['SpecificDate']               = "";   $res4['AppointmentDate']            = "";   $res4['AppointmentTime']            = "";
                            $res4['AppointmentShortName']       = "";   $res4['AppointmentRequirements']    = "";   $res4['SelfTestID']                 = "";
                            $res4['RowNo']                      = "";   $res4['TestDescription']            = "";   $res4['Frequency']                  = "";
                            $res4['FrequencyString']            = "";   $res4['ExercisePlanNo']             = "";   $res4['ExercisePlanName']           = "";
                            $res4['ExerciseDurationDays']       = "";   $res4['ExerciseSNo']                = "";   $res4['ExerciseDescription']        = "";
                            $res4['ExerciseInstruction']        = "";   $res4['ExerciseNoOfReps']           = "";   $res4['ExerciseDuration']           = "";
                            $res4['LabTestID']                  = "";   $res4['LabTestRequirements']        = "";
                            $res4['AppointmentDuration']    = "";   $res4['AppointmentPlace']       = "";
                             $res4['SpecialType'] = "";
                                $res4['SpecialDuration'] = "";  
                        array_push($result,$res4);
                           
                    }
                    //END OF DIET DETAILS

/*                    //GET EXERCISE DETAILS
                    $get_exercise_detials       = "select distinct t1.ExercisePlanNo,t1.ExercisePlanName,t1.AdvisorName,t1.ExerciseDurationDays,
                                                t2.DayNo,t2.ExerciseSNo,t2.ExerciseDescription,t2.ExerciseInstruction,t2.ExerciseNoOfReps,
                                                t2.ExerciseDuration,t2.Link,t2.CreatedDate
                                                from USER_EXERCISE_HEADER as t1,USER_EXERCISE_DETAILS as t2
                                                where t1.PlanCode=t2.PlanCode and t1.ExercisePlanNo=t2.ExercisePlanNo and t1.PlanCode='$plancode' and t1.UserID = t2.UserID and t1.UserID = '$userid'";
                    $get_exercise_detials_qry   = mysql_query($get_exercise_detials);
                    $get_exercise_detials_count = mysql_num_rows($get_exercise_detials_qry);
                    $exerciserepo = array();
                    if($get_exercise_detials_count)
                    {
                        while($exercise_rows=mysql_fetch_array($get_exercise_detials_qry))
                        {
                            $res5['SectionID']                  = '5';
                            $res5['ExercisePlanNo']             = (empty($exercise_rows['ExercisePlanNo']))         ? '' : $exercise_rows['ExercisePlanNo'];
                            $res5['ExercisePlanName']           = (empty($exercise_rows['ExercisePlanName']))       ? '' : $exercise_rows['ExercisePlanName'];
                            $res5['AdvisorName']                = (empty($exercise_rows['AdvisorName']))            ? '' : $exercise_rows['AdvisorName'];
                            $res5['ExerciseDurationDays']       = (empty($exercise_rows['ExerciseDurationDays']))   ? '' : $exercise_rows['ExerciseDurationDays'];
                            $res6['DayNo']                      = (empty($exercise_rows['DayNo']))                  ? '' : $exercise_rows['DayNo'];
                            $res5['ExerciseSNo']                = (empty($exercise_rows['ExerciseSNo']))            ? '' : $exercise_rows['ExerciseSNo'];
                            $res6['ExerciseDescription']        = (empty($exercise_rows['ExerciseDescription']))    ? '' : $exercise_rows['ExerciseDescription'];
                            $res6['ExerciseInstruction']        = (empty($exercise_rows['ExerciseInstruction']))    ? '' : $exercise_rows['ExerciseInstruction'];
                            $res6['ExerciseNoOfReps']           = (empty($exercise_rows['ExerciseNoOfReps']))       ? '' : $exercise_rows['ExerciseNoOfReps'];
                            $res6['ExerciseDuration']           = (empty($exercise_rows['ExerciseDuration']))       ? '' : $exercise_rows['ExerciseDuration'];
                            $res6['Link']                       = (empty($exercise_rows['Link']))                   ? '' : $exercise_rows['Link'];
                            array_push($exerciserepo,$res6);
                            }
                            $res5['exercise_info']              = $exerciserepo;
                            $res5['DietNo']                     = "";   $res5['DietPlanName']               = "";   $res5['DietDurationDays']           = "";
                            $res5['MealID']                     = "";   $res5['MealDescription']            = "";   $res5['SpecificTime']               = "";
                            $res5['LabTestDate']                = "";   $res5['LabTestTime']                = "";   $res5['TestName']                   = "";
                            $res5['DoctorsName']                = "";   $res5['Instruction']                = "";   $res5['ResponseRequired']           = "";
                            $res5['PrescriptionNo']             = "";   $res5['PrescriptionName']           = "";   $res5['MedicineName']               = "";
                            $res5['When']                       = "";   $res5['HowLong']                    = "";   $res5['HowLongType']                = "";
                            $res5['IsCritical']                 = "";   $res5['StartFlag']                  = "";   $res5['NoOfDaysAfterPlanStarts']    = "";
                            $res5['SpecificDate']               = "";   $res5['AppointmentDate']            = "";   $res5['AppointmentTime']            = "";
                            $res5['AppointmentShortName']       = "";   $res5['AppointmentRequirements']    = "";   $res5['SelfTestID']                 = "";
                            $res5['RowNo']                      = "";   $res5['TestDescription']            = "";   $res5['Frequency']                  = "";
                            $res5['FrequencyString']            = "";   $res5['LabTestID']                  = "";   $res5['LabTestRequirements']        = ""; 
                        array_push($result,$res5);
                    }
                    //END OF EXERCISE DETAILS
*/
                    // //Get GOAL DETAILS
                    // $get_goal = "select PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where PlanCode='$plancode' and UserID = '$userid' $timestamp_query";
                    //   //echo $get_presc;exit;
                    //   $get_goal_run = mysql_query($get_goal);
                    //   $get_goal_count = mysql_num_rows($get_goal_run);
                    //   if($get_goal_count){
                    //     while ($goals = mysql_fetch_array($get_goal_run)) {
                    //         $resgoals['GoalNo']             = $goals['GoalNo'];
                    //         $resgoals['GoalDescription']    = $goals['GoalDescription'];
                    //         $resgoals['DisplayedWith']      = $goals['DisplayedWith'];
                    //         array_push($result,$resgoals);
                    //     }
                    //   }

                 //GET INSTRUCTION DETAILS
                $get_instruction = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName, t4.InstructionType, t2.RowNo,t2.SpecificTime,t3.ShortHand,t2.Instruction,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,t2.When,t2.StartTime,t2.EndTime,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString,t2.Link,t2.CreatedDate,t2.StudyStatus
                                    from USER_INSTRUCTION_HEADER as t1,USER_INSTRUCTION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3, INSTRUCTION_TYPE as t4
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t2.InstructionTypeID = t4.InstructionTypeID and t1.UserID = '$userid' $timestamp_query";
                //echo $get_instruction;exit;
                $get_instruction_qry     = mysql_query($get_instruction);
                $get_instruction_count   = mysql_num_rows($get_instruction_qry);
                if($get_instruction_count)
                {
                    while($instruction_rows=mysql_fetch_array($get_instruction_qry))
                    {

        $wheninput = $instruction_rows['When'];
        if($wheninput == '16'){
       $specifictimebundle = (empty($instruction_rows['SpecificTime']))?'':$instruction_rows['SpecificTime'];

        $starray = array();
        $starray = explode(",",$specifictimebundle);
                            foreach ($starray as $st) {
                       if($st != ""){
                        $st = date('H:i:s',strtotime($st));
                        $res8['SectionID']              = '6';
                        $res8['PrescriptionNo']         = (empty($instruction_rows['PrescriptionNo']))           ? '' : $instruction_rows['PrescriptionNo'];
                        $res8['PrescriptionName']       = (empty($instruction_rows['PrescriptionName']))         ? '' : stripslashes($instruction_rows['PrescriptionName']);
                        $res8['DoctorsName']            = (empty($instruction_rows['DoctorsName']))              ? '' : stripslashes($instruction_rows['DoctorsName']);
                        $res8['MedicineName']           = (empty($instruction_rows['MedicineName']))             ? '' : stripslashes($instruction_rows['MedicineName']);
                        $res8['When']                   = (empty($instruction_rows['ShortHand']))                ? '' : $instruction_rows['ShortHand'];
                        $res8['RowNo']                  = (empty($instruction_rows['RowNo']))                    ? '' : $instruction_rows['RowNo'];
                        $res8['Instruction']            = (empty($instruction_rows['Instruction']))              ? '' : $instruction_rows['Instruction'];
                        if($res8['Instruction'] == "NA"){
                            $res8['Instruction'] = "With Food";
                        }
                        //$res8['Instruction']            = ("NA") ? 'With Food' : $instruction_rows['Instruction'];
                         //echo $res8['Instruction'];exit;
                        $res8['Frequency']              = (empty($instruction_rows['Frequency']))                ? '' : $instruction_rows['Frequency'];
                        $res8['ActionText']             = (empty($instruction_rows['InstructionType']))                ? '' : $instruction_rows['InstructionType'];
                        $res8['FrequencyString']        = (empty($instruction_rows['FrequencyString']))          ? '' : $instruction_rows['FrequencyString'];
                        $howlong                        = (empty($instruction_rows['HowLong']))                  ? '' : $instruction_rows['HowLong'];
                        $howlongtype                    = (empty($instruction_rows['HowLongType']))              ? '' : $instruction_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res8['HowLong']                = $howlong;
                        $res8['HowLongType']            = $howlongtype;
                        $res8['IsCritical']             = (empty($instruction_rows['IsCritical']))               ? '' : $instruction_rows['IsCritical'];
                        $res8['ResponseRequired']       = (empty($instruction_rows['ResponseRequired']))         ? '' : $instruction_rows['ResponseRequired'];
                        $res8['StartFlag']              = (empty($instruction_rows['StartFlag']))                ? '' : $instruction_rows['StartFlag'];
                        $res8['NoOfDaysAfterPlanStarts']= (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '' : $instruction_rows['NoOfDaysAfterPlanStarts'];
                        $res8['SpecificDate']           = (empty($instruction_rows['SpecificDate']))             ? '' : $instruction_rows['SpecificDate'];


                        $st = date('H:i:s',strtotime($instruction_rows['StartTime']));
                        
$res8['SpecificTime']=(empty($instruction_rows['StartTime']))? '' : date('H:i:s',strtotime($instruction_rows['StartTime']));

$res8['JssStartTime']=(empty($instruction_rows['StartTime']))? '' : date('H:i:s',strtotime($instruction_rows['StartTime']));

$res8['JssEndTime']=(empty($instruction_rows['EndTime']))? '' : date('H:i:s',strtotime($instruction_rows['EndTime']));

$res8['StudyStatus']=(empty($instruction_rows['StudyStatus']))? '' :$instruction_rows['StudyStatus'];


                        $res8['Status']                 = (empty($instruction_rows['Status']))                   ? '' : $instruction_rows['Status'];
                        $res8['Link']                   = (empty($instruction_rows['Link']))                     ? '' : $instruction_rows['Link'];
                        $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                        $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                        $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                        $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                        $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                        $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                        $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                        $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                        $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                        $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = "";
                         $res8['SpecialType'] = "";
                        $res8['SpecialDuration'] = "";          
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res8);
                        }*/
                    array_push($result,$res8);
                    }
                            }
                        } else {
                        $res8['SectionID']              = '6';
                        $res8['PrescriptionNo']         = (empty($instruction_rows['PrescriptionNo']))           ? '' : $instruction_rows['PrescriptionNo'];
                        $res8['PrescriptionName']       = (empty($instruction_rows['PrescriptionName']))         ? '' : stripslashes($instruction_rows['PrescriptionName']);
                        $res8['DoctorsName']            = (empty($instruction_rows['DoctorsName']))              ? '' : stripslashes($instruction_rows['DoctorsName']);
                        $res8['MedicineName']           = (empty($instruction_rows['MedicineName']))             ? '' : stripslashes($instruction_rows['MedicineName']);
                        $res8['ActionText']             = (empty($instruction_rows['InstructionType']))             ? '' : stripslashes($instruction_rows['InstructionType']);
                        $res8['When']                   = (empty($instruction_rows['ShortHand']))                ? '' : $instruction_rows['ShortHand'];
                        $res8['RowNo']                  = (empty($instruction_rows['RowNo']))                    ? '' : $instruction_rows['RowNo'];
                        $res8['Instruction']            = (empty($instruction_rows['Instruction']))              ? '' : $instruction_rows['Instruction'];
                        if($res8['Instruction'] == "NA"){
                            $res8['Instruction'] = "With Food";
                        }
                        //$res8['Instruction']            = ("NA") ? 'With Food' : $instruction_rows['Instruction'];
                         //echo $res8['Instruction'];exit;
                        $res8['Frequency']              = (empty($instruction_rows['Frequency']))                ? '' : $instruction_rows['Frequency'];
                        $res8['FrequencyString']        = (empty($instruction_rows['FrequencyString']))          ? '' : $instruction_rows['FrequencyString'];
                        $howlong                        = (empty($instruction_rows['HowLong']))                  ? '' : $instruction_rows['HowLong'];
                        $howlongtype                    = (empty($instruction_rows['HowLongType']))              ? '' : $instruction_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res8['HowLong']                = $howlong;
                        $res8['HowLongType']            = $howlongtype;
                        $res8['IsCritical']             = (empty($instruction_rows['IsCritical']))               ? '' : $instruction_rows['IsCritical'];
                        $res8['ResponseRequired']       = (empty($instruction_rows['ResponseRequired']))         ? '' : $instruction_rows['ResponseRequired'];
                        $res8['StartFlag']              = (empty($instruction_rows['StartFlag']))                ? '' : $instruction_rows['StartFlag'];
                        $res8['NoOfDaysAfterPlanStarts']= (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '' : $instruction_rows['NoOfDaysAfterPlanStarts'];
                $res8['SpecificDate']           = (empty($instruction_rows['SpecificDate']))             ? '' : $instruction_rows['SpecificDate'];

$res8['SpecificTime']=(empty($instruction_rows['StartTime']))? '' : date('H:i:s',strtotime($instruction_rows['StartTime']));

$res8['JssStartTime']=(empty($instruction_rows['StartTime']))? '' : date('H:i:s',strtotime($instruction_rows['StartTime']));

$res8['JssEndTime']=(empty($instruction_rows['EndTime']))? '' : date('H:i:s',strtotime($instruction_rows['EndTime']));

$res8['StudyStatus']=(empty($instruction_rows['StudyStatus']))? '' :$instruction_rows['StudyStatus'];


                        $res8['Status']                 = (empty($instruction_rows['Status']))                   ? '' : $instruction_rows['Status'];
                        $res8['Link']                   = (empty($instruction_rows['Link']))                     ? '' : $instruction_rows['Link'];
                        $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                        $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                        $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                        $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                        $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                        $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                        $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                        $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                        $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                        $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = ""; 
                         $res8['SpecialType'] = "";
                        $res8['SpecialDuration'] = ""; 
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res8);
                        }*/
                    array_push($result,$res8);
                    }
                    //exit;
                        /*If Howlong ie; duration is 0 then dont send*/

                        /*End of Checking value of Howlong*/
                    //array_push($result,$res8);
                    }
                }
                //END OF GET INSTRUCTION DETAILS
                $plan['PLANPIPER_PLAN_INFO']            = $plan_info;
                $plan['PLANPIPER_PLAN_GOALS']           = $goal_info;
                $plan['PLANPIPER_MERCHANT_SOCIAL_MEDIA']= $social_media_info;
                $plan['ANALYTICS']                      = $analytics;
                $plan['PLANPIPER_ACTIVITIES']           = $result;
            }
           
        array_push($plan_details,$plan);

        //UPDATE UserStartOrUpdateDateTime ie;PLAN START DATE IN USER_PLAN_HEADER TABLE 
        //$now = date('Y-m-d H:i:s');
        }//end of foreach

        
    
    echo "{".json_encode('PLANPIPER_LOGIN').':'.json_encode($loginstatus).","
            .json_encode('PLANPIPER_ASSIGNED_PLANS').':'.json_encode($plan_list).","
            .json_encode('PLANS').':'.json_encode($plan_details).","
            .json_encode('TIMESTAMP').':'.json_encode($p_timestamp).","
            .json_encode('SETTINGS').':'.json_encode($settings).","
            .json_encode('DIRECTORY').':'.json_encode($directory).","
            .json_encode('SELF_PLANS').':'.json_encode($self_plans).","
            .json_encode('MEDICAL_TESTS').':'.json_encode($medical_tests).","
            .json_encode('REPORTS').':'.json_encode($reports).","
            .json_encode('REPORTS_TIMESTAMP').':'.json_encode($report_timestamp).","
            .json_encode('PLANPIPER_USER_INFO').':'.json_encode($user_info)."}";
    }
    else
    {
        $loginstatus = "0"; $plan_list  = "";   $plan_details = "";   $user_info = "";  $now = "";    $settings = "";   
        $directory   = "";  $self_plans = "";   $medical_tests= "";   $reports    = ""; $report_timestamp = "";
        echo "{".json_encode('PLANPIPER_LOGIN').':'.json_encode($loginstatus).","
            .json_encode('PLANPIPER_ASSIGNED_PLANS').':'.json_encode($plan_list).","
            .json_encode('PLANS').':'.json_encode($plan_details).","
            .json_encode('TIMESTAMP').':'.json_encode($p_timestamp).","
            .json_encode('SETTINGS').':'.json_encode($settings).","
            .json_encode('DIRECTORY').':'.json_encode($directory).","
            .json_encode('SELF_PLANS').':'.json_encode($self_plans).","
            .json_encode('MEDICAL_TESTS').':'.json_encode($medical_tests).","
            .json_encode('REPORTS').':'.json_encode($reports).","
            .json_encode('REPORTS_TIMESTAMP').':'.json_encode($report_timestamp).","
            .json_encode('PLANPIPER_USER_INFO').':'.json_encode($user_info)."}";
    }
}
//*********************************END OF LOGIN**************************************************************************

//*********************************GET ACTIVITIES UNDER PLANCODE*********************************************************
elseif($_REQUEST['RequestType']=="activities" && $_REQUEST['plan_code']!="" && $_REQUEST['userid']!="")
{
    $plancode           = $_REQUEST['plan_code'];
    $userid             = $_REQUEST['userid'];
    $timestamp          = (empty($_REQUEST['timestamp']))       ? '' : $_REQUEST['timestamp'];
    if($timestamp=="")
    {
        $timestamp_query = "";
        $plan_timestamp  = "";
    }
    else
    {
        $timestamp_query    = " and t2.UpdatedDate>='$timestamp'";
        //$timestamp_query    = "";
        $plan_timestamp     = " and t1.PlanUpdatedDate>='$timestamp' ";
        //$plan_timestamp  = "";
    }

    $plan_details       = array();
    if($plancode)
    {
            $result             = array();
            $social_media_info  = array();
            //$res5['INDIVIDUAL_PLANS']  = $plancode;
            $get_plan_info  = "select distinct t1.PlanCode,t1.MerchantID,t1.CategoryID,t4.CategoryName,t1.PlanName,t1.PlanDescription,t1.PlanStatus,
                                t1.PlanCurrencyCode,t1.PlanCost,t1.PlanCoverImagePath,t1.CreatedDate,t2.RoleID,t2.Status,
                                t3.CompanyName,t3.CompanyEmailID,t3.CompanyMobileNo,t3.CompanyAddressLine1,t3.CompanyAddressLine2,
                                t3.CompanyPinCode,t5.CityName as CompanyCityName,t6.StateName as CompanyStateName,
                                t7.CountryName as CompanyCountryName
                                from USER_PLAN_HEADER as t1,USER_MERCHANT_MAPPING as t2,MERCHANT_DETAILS as t3,CATEGORY_MASTER as t4,
                                CITY_DETAILS as t5,STATE_DETAILS as t6,COUNTRY_DETAILS as t7
                                where t1.MerchantID=t2.MerchantID and t2.MerchantID=t3.MerchantID and t2.UserID='$userid' 
                                and t1.CategoryID=t4.CategoryID and t1.PlanCode='$plancode' and t3.CompanyCountryCode=t7.CountryCode 
                                and t3.CompanyStateID=t6.StateID and t3.CompanyCityID=t5.CityID and t2.Status='A' 
                                and t1.UserID='$userid' and t2.RoleID=5";
            //echo $get_plan_info;exit;
            $get_plan_info_qry= mysql_query($get_plan_info);
            $get_count       = mysql_num_rows($get_plan_info_qry);
            if($get_count)
            {
            $plan_info          = array();
            $social_media_info  = array();
            $analytics          = array();
                while($plan_info_rows = mysql_fetch_array($get_plan_info_qry))
                { 
                    $pcode                      = (empty($plan_info_rows['PlanCode']))              ? '' : $plan_info_rows['PlanCode'];
                    $res['PlanCode']            = $pcode;
                    $res['CategoryID']          = (empty($plan_info_rows['CategoryID']))            ? '' : $plan_info_rows['CategoryID'];
                    $res['CategoryName']        = (empty($plan_info_rows['CategoryName']))          ? '' : $plan_info_rows['CategoryName'];
                    $res['PlanName']            = (empty($plan_info_rows['PlanName']))              ? '' : stripslashes($plan_info_rows['PlanName']);
                    $res['PlanDescription']     = (empty($plan_info_rows['PlanDescription']))       ? '' : stripslashes($plan_info_rows['PlanDescription']);
                    $plan_cover_image_name      = (empty($plan_info_rows['PlanCoverImagePath']))    ? '' : $plan_info_rows['PlanCoverImagePath'];                   
                    $files                      = "http://".$host_server.'/'.$plan_header.$plan_cover_image_name;

                        /*if(getimagesize($files) !== false)
                        {
                            //echo "yes";
                            reduce_image_size($reduced_plan_header,$plan_cover_image_name,$files);
                        }*/
                        /*
                        $check_valid_url = valid_url($files);
                        if($check_valid_url==1)
                        {
                            if(getimagesize($files) !== false)
                            {
                                //echo $files."<br>";
                                reduce_image_size($reduced_plan_header,$plan_cover_image_name,$files);
                            }
                            $res['PlanCoverImagePath']  = $host_server.'/'.$reduced_plan_header.$plan_info_rows['PlanCoverImagePath'];
                        }
                        else
                        {
                            $res['PlanCoverImagePath']  = $host_server.'/'.$plan_header.$plan_info_rows['PlanCoverImagePath'];
                        }
                        */
                    $res['PlanCoverImagePath']  = $host_server.'/'.$plan_header.$plan_info_rows['PlanCoverImagePath'];   
                    
                    $merchant_id                = (empty($plan_info_rows['MerchantID']))            ? '' : $plan_info_rows['MerchantID'];
                    $res['MerchantID']          = $merchant_id;
                    $res['CompanyName']         = (empty($plan_info_rows['CompanyName']))           ? '' : stripslashes($plan_info_rows['CompanyName']);
                    $res['CompanyEmailID']      = (empty($plan_info_rows['CompanyEmailID']))        ? '' : $plan_info_rows['CompanyEmailID'];
                    $country_code               = (empty($plan_info_rows['CompanyCountryCode']))    ? '' : '+'.ltrim($plan_info_rows['CompanyCountryCode'],0);
                    $res['CompanyMobileNo']     = (empty($plan_info_rows['CompanyMobileNo']))       ? '' : $country_code.$plan_info_rows['CompanyMobileNo'];

                    $addressline1               = (empty($plan_info_rows['CompanyAddressLine1']))   ? '' : stripslashes($plan_info_rows['CompanyAddressLine1']);
                    $addressline2               = (empty($plan_info_rows['CompanyAddressLine2']))   ? '' : stripslashes($plan_info_rows['CompanyAddressLine2']);
                    $pincode                    = (empty($plan_info_rows['CompanyPinCode']))        ? '' : $plan_info_rows['CompanyPinCode'];

                    $cityname                   = (empty($plan_info_rows['CompanyCityName']))       ? '' : $plan_info_rows['CompanyCityName'];

                    $statename                  = (empty($plan_info_rows['CompanyStateName']))      ? '' : $plan_info_rows['CompanyStateName'];

                    $countryname                = (empty($plan_info_rows['CompanyCountryName']))    ? '' : $plan_info_rows['CompanyCountryName'];

                    $res['CompanyAddressLine1'] = $addressline1;
                    $res['CompanyAddressLine2'] = $addressline2;
                    $res['CompanyPinCode']      = $pincode;
                    $res['CompanyCityName']     = $cityname;
                    $res['CompanyStateName']    = $statename;
                    $res['CompanyCountryName']  = $countryname;

                    $addressline1       = (empty($addressline1))    ? '' : $addressline1;
                    $addressline2       = (empty($addressline2))    ? '' : ', '.$addressline2;
                    $pincode            = (empty($pincode))         ? '' : ', '.$pincode;
                    $cityname           = (empty($cityname))        ? '' : ', '.$cityname;
                    $statename          = (empty($statename))       ? '' : ', '.$statename;
                    $countryname        = (empty($countryname))     ? '' : ', '.$countryname;

                    $res['CompanyFullAddress']  = $addressline1.$addressline2.$pincode.$cityname.$statename.$countryname;

                array_push($plan_info,$res);
                }

                 //Social Media Information
                $get_social_media       = "select MerchantID,SocialMediaName,SocialMediaLink from MERCHANT_SOCIAL_MEDIA 
                                            where MerchantID='$merchant_id'";
                //echo $get_social_media;exit;
                $get_social_media_query = mysql_query($get_social_media);
                $count_of_social_media  = mysql_num_rows($get_social_media_query);
                if($count_of_social_media > 0)
                {
                    while($media = mysql_fetch_array($get_social_media_query))
                    {
                        $r['MerchantID']        = $merchant_id;
                        $r['SocialMediaName']   = $media['SocialMediaName'];
                        $r['SocialMediaLink']   = $media['SocialMediaLink'];
                    array_push($social_media_info,$r);
                    }
                }

                /*CODE TO CHECK WHETHER PLAN WAS SYNCED EARLIER OR NOT*/
                $edit   = mysql_fetch_array(mysql_query("select UserStartOrUpdateDateTime from USER_PLAN_HEADER where UserID='$userid' and PlanCode='$pcode' and MerchantID='$merchant_id'"));
                $plan_synced_date =  $edit['UserStartOrUpdateDateTime'];
                //echo $plan_synced_date;exit;
               /*END OF CODE TO CHECK WHETHER PLAN WAS SYNCED EARLIER OR NOT*/

                 /*GET ANALYTICS(if plan was synced earlier then there may be analytics data that was entered which is to be returned in case user lost his phone)*/
                if($timestamp=="" && $plan_synced_date!=NULL && $plan_synced_date!='0000-00-00 00:00:00')
                {
                $get_analytics = "select '1' as SectionID, t1.PrescriptionNo as PrescriptionNo_SelfTestID,t1.RowNo,Date(t1.DateTime) as Date,
                    Time(t1.DateTime) as Time,t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,
                    t2.MedicineName as MedName_TestName,t3.MerchantID 
                    from USER_MEDICATION_DATA_FROM_CLIENT as t1,USER_MEDICATION_DETAILS as t2,USER_PLAN_HEADER as t3 
                    where t1.PlanCode='$pcode' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                    and t1.UserID=t2.UserID and t1.PlanCode=t3.PlanCode and t1.UserID=t3.UserID 
                    and t1.PrescriptionNo=t2.PrescriptionNo and t1.RowNo=t2.RowNo
                    union
                    select '6' as SectionID, t1.PrescriptionNo,t1.RowNo,Date(t1.DateTime) as Date,Time(t1.DateTime) as Time,
                    t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,t2.MedicineName as MedName_TestName,
                    t3.MerchantID 
                    from USER_INSTRUCTION_DATA_FROM_CLIENT as t1,USER_INSTRUCTION_DETAILS as t2,USER_PLAN_HEADER as t3 
                    where t1.PlanCode='$pcode' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                    and t1.UserID=t2.UserID and t1.PlanCode=t3.PlanCode and t1.UserID=t3.UserID
                    and t1.PrescriptionNo=t2.PrescriptionNo and t1.RowNo=t2.RowNo 
                    union
                    select '3-1' as SectionID, t7.SelfTestID,t7.RowNo,Date(t7.DateTime) as Date,Time(t7.DateTime) as Time,
                    t7.DateTime,convert(t7.ResponseDataValue,char) as Value, t7.ResponseDataName,t9.MerchantID 
                    from USER_SELF_TEST_DATA_FROM_CLIENT as t7,USER_SELF_TEST_DETAILS as t8,USER_PLAN_HEADER as t9 
                    where t7.PlanCode='$pcode' and t7.UserID='$userid' and t7.PlanCode=t8.PlanCode 
                    and t7.UserID=t8.UserID and t7.PlanCode=t9.PlanCode and t7.UserID=t9.UserID 
                    and t7.SelfTestID=t8.SelfTestID and t7.RowNo=t8.RowNo
                    order by DateTime";
                //echo $get_analytics;exit;
                $get_analytics_query = mysql_query($get_analytics);
                $get_analytics_count = mysql_num_rows($get_analytics_query);
                    if($get_analytics_count > 0)
                    {
                        while($analytics_row = mysql_fetch_array($get_analytics_query))
                        {
                            $a['MerchantID']                = $merchant_id;
                            $a['PlanCode']                  = $pcode;
                            $a['UserID']                    = $userid;
                            $a['SectionID']                 = $analytics_row['SectionID'];
                            $a['PrescriptionNo_SelfTestID'] = (empty($analytics_row['PrescriptionNo_SelfTestID']))  ? '' : trim($analytics_row['PrescriptionNo_SelfTestID']);
                            $a['RowNo']                     = (empty($analytics_row['RowNo']))                      ? '' : trim($analytics_row['RowNo']);
                            $a['MedName_TestName']          = (empty($analytics_row['MedName_TestName']))           ? '' : trim($analytics_row['MedName_TestName']);
                            $a['Date']                      = (empty($analytics_row['Date']))                       ? '' : trim($analytics_row['Date']);
                            $a['Time']                      = (empty($analytics_row['Time']))                       ? '' : trim($analytics_row['Time']);
                            $a['DateTime']                  = (empty($analytics_row['DateTime']))                   ? '' : trim($analytics_row['DateTime']);
                            $a['Value']                     = (empty($analytics_row['Value']))                      ? '' : trim($analytics_row['Value']);
                        array_push($analytics,$a);
                        }
                    }
                }
                /*END OF GET ANALYTICS*/
                $goal_info          = array();
                   //Get GOAL DETAILS
                    $get_goal = "select PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from USER_GOAL_DETAILS where PlanCode='$plancode' and UserID = '$userid'";
                      //echo $get_goal;exit;
                      $get_goal_run = mysql_query($get_goal);
                      $get_goal_count = mysql_num_rows($get_goal_run);
                      if($get_goal_count){
                        while ($goals = mysql_fetch_array($get_goal_run)) {
                            $resgoals['GoalNo']             = $goals['GoalNo'];
                            $resgoals['GoalDescription']    = $goals['GoalDescription'];
                            $resgoals['DisplayedWith']      = rtrim($goals['DisplayedWith'],",");
                            array_push($goal_info,$resgoals);
                        }
                      }
                //GET MEDICATION DETAILS
                $get_medication = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.MedicineCount,t2.MedicineTypeID,t4.MedicineType,t4.Action,t2.RowNo,t2.SpecificTime,t3.ShortHand,t2.Instruction,t2.When,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString,t2.Status,t2.Link,t2.CreatedDate
                                    from USER_MEDICATION_HEADER as t1,USER_MEDICATION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3,
                                    MEDICINE_TYPES as t4
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t2.MedicineTypeID=t4.SNo and t1.UserID = '$userid' $timestamp_query";
               //echo $get_medication;exit;
                $get_medication_qry     = mysql_query($get_medication);
                $get_medication_count   = mysql_num_rows($get_medication_qry);
                if($get_medication_count)
                {
                    while($medication_rows=mysql_fetch_array($get_medication_qry))
                    {
                        $wheninput                      = $medication_rows['When'];
                        if($wheninput == '16'){
                            $specifictimebundle           = (empty($medication_rows['SpecificTime']))             ? '' : $medication_rows['SpecificTime'];
                            $starray = array();
                            $starray = explode(",",$specifictimebundle);
                            foreach ($starray as $st) {
                       if($st != ""){
                        $st = date('H:i:s',strtotime($st));
                        $res1['SectionID']              = '1';
                        $res1['PrescriptionNo']         = (empty($medication_rows['PrescriptionNo']))           ? '' : $medication_rows['PrescriptionNo'];
                        $res1['PrescriptionName']       = (empty($medication_rows['PrescriptionName']))         ? '' : stripslashes($medication_rows['PrescriptionName']);
                        $res1['DoctorsName']            = (empty($medication_rows['DoctorsName']))              ? '' : stripslashes($medication_rows['DoctorsName']);
                        $res1['MedicineName']           = (empty($medication_rows['MedicineName']))             ? '' : stripslashes($medication_rows['MedicineName']);
                        $medication_count               = (empty($medication_rows['MedicineCount']))            ? '' : $medication_rows['MedicineCount'];
                        $medicinetypeid                 = (empty($medication_rows['MedicineTypeID']))           ? '' : $medication_rows['MedicineTypeID'];
                        if($medication_count<=1 || $medicinetypeid==4 || $medicinetypeid==5)
                        {
                            $text = "";
                        }
                        else
                        {
                            $text = "s";
                        }
                        //echo 123;
                        $res1['MedicationCount']        = $medication_count;
                        $res1['MedicationType']         = (empty($medication_rows['MedicineType']))             ? '' : $medication_rows['MedicineType'].$text;
                        $res1['ActionText']             = (empty($medication_rows['Action']))                   ? '' : $medication_rows['Action'];
                        $res1['When']                   = (empty($medication_rows['ShortHand']))                ? '' : $medication_rows['ShortHand'];
                        $res1['RowNo']                  = (empty($medication_rows['RowNo']))                    ? '' : $medication_rows['RowNo'];
                        $res1['Instruction']            = (empty($medication_rows['Instruction']))              ? '' : $medication_rows['Instruction'];
                        if($res1['Instruction'] == "NA"){
                            $res1['Instruction'] = "With Food";
                        }
                        //$res1['Instruction']            = ("NA") ? 'With Food' : $medication_rows['Instruction'];
                         //echo $res1['Instruction'];exit;
                        $res1['Frequency']              = (empty($medication_rows['Frequency']))                ? '' : $medication_rows['Frequency'];
                        $res1['FrequencyString']        = (empty($medication_rows['FrequencyString']))          ? '' : $medication_rows['FrequencyString'];
                        $howlong                        = (empty($medication_rows['HowLong']))                  ? '' : $medication_rows['HowLong'];
                        $howlongtype                    = (empty($medication_rows['HowLongType']))              ? '' : $medication_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res1['HowLong']                = $howlong;
                        $res1['HowLongType']            = $howlongtype;
                        $res1['IsCritical']             = (empty($medication_rows['IsCritical']))               ? '' : $medication_rows['IsCritical'];
                        $res1['ResponseRequired']       = (empty($medication_rows['ResponseRequired']))         ? '' : $medication_rows['ResponseRequired'];
                        $res1['StartFlag']              = (empty($medication_rows['StartFlag']))                ? '' : $medication_rows['StartFlag'];
                        $res1['NoOfDaysAfterPlanStarts']= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '' : $medication_rows['NoOfDaysAfterPlanStarts'];
                        $res1['SpecificDate']           = (empty($medication_rows['SpecificDate']))             ? '' : $medication_rows['SpecificDate'];
                        $res1['SpecificTime']           = $st;
                        $res1['Status']                 = (empty($medication_rows['Status']))                   ? '' : $medication_rows['Status'];
                        $res1['Link']                   = (empty($medication_rows['Link']))                     ? '' : $medication_rows['Link'];
                        $res1['AppointmentDate']        = "";   $res1['AppointmentTime']        = "";   $res1['AppointmentShortName']   = "";
                        $res1['AppointmentRequirements']= "";   $res1['LabTestDate']            = "";   $res1['LabTestTime']            = "";
                        $res1['TestName']               = "";   $res1['SelfTestID']             = "";   $res1['TestDescription']        = "";   
                        $res1['DietNo']                 = "";   $res1['DietPlanName']           = "";   $res1['AdvisorName']            = "";   
                        $res1['DietDurationDays']       = "";   $res1['DayNo']                  = "";   $res1['MealID']                 = "";   
                        $res1['MealDescription']        = "";   $res1['ExercisePlanNo']         = "";   $res1['ExercisePlanName']       = "";  
                        $res1['ExerciseDurationDays']   = "";   $res1['ExerciseSNo']            = "";   $res1['ExerciseDescription']    = "";   
                        $res1['ExerciseInstruction']    = "";   $res1['ExerciseNoOfReps']       = "";   $res1['ExerciseDuration']       = "";   
                        $res1['LabTestID']              = "";   $res1['LabTestRequirements']    = "";
                         $res1['AppointmentDuration']    = "";   $res1['AppointmentPlace']       = "";
                          $res1['SpecialType'] = "";
                        $res1['SpecialDuration'] = "";         
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res1);
                        }*/
                    array_push($result,$res1);
                    }
                            }
                        } else {
                        $res1['SectionID']              = '1';
                        $res1['PrescriptionNo']         = (empty($medication_rows['PrescriptionNo']))           ? '' : $medication_rows['PrescriptionNo'];
                        $res1['PrescriptionName']       = (empty($medication_rows['PrescriptionName']))         ? '' : stripslashes($medication_rows['PrescriptionName']);
                        $res1['DoctorsName']            = (empty($medication_rows['DoctorsName']))              ? '' : stripslashes($medication_rows['DoctorsName']);
                        $res1['MedicineName']           = (empty($medication_rows['MedicineName']))             ? '' : stripslashes($medication_rows['MedicineName']);
                        $medication_count               = (empty($medication_rows['MedicineCount']))            ? '' : $medication_rows['MedicineCount'];
                        $medicinetypeid                 = (empty($medication_rows['MedicineTypeID']))           ? '' : $medication_rows['MedicineTypeID'];
                        if($medication_count<=1 || $medicinetypeid==4 || $medicinetypeid==5)
                        {
                            $text = "";
                        }
                        else
                        {
                            $text = "s";
                        }
                        //echo 123;
                        $res1['MedicationCount']        = $medication_count;
                        $res1['MedicationType']         = (empty($medication_rows['MedicineType']))             ? '' : $medication_rows['MedicineType'].$text;
                        $res1['ActionText']             = (empty($medication_rows['Action']))                   ? '' : $medication_rows['Action'];
                        $res1['When']                   = (empty($medication_rows['ShortHand']))                ? '' : $medication_rows['ShortHand'];
                        $res1['RowNo']                  = (empty($medication_rows['RowNo']))                    ? '' : $medication_rows['RowNo'];
                        $res1['Instruction']            = (empty($medication_rows['Instruction']))              ? '' : $medication_rows['Instruction'];
                        if($res1['Instruction'] == "NA"){
                            $res1['Instruction'] = "With Food";
                        }
                        //$res1['Instruction']            = ("NA") ? 'With Food' : $medication_rows['Instruction'];
                         //echo $res1['Instruction'];exit;
                        $res1['Frequency']              = (empty($medication_rows['Frequency']))                ? '' : $medication_rows['Frequency'];
                        $res1['FrequencyString']        = (empty($medication_rows['FrequencyString']))          ? '' : $medication_rows['FrequencyString'];
                        $howlong                        = (empty($medication_rows['HowLong']))                  ? '' : $medication_rows['HowLong'];
                        $howlongtype                    = (empty($medication_rows['HowLongType']))              ? '' : $medication_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res1['HowLong']                = $howlong;
                        $res1['HowLongType']            = $howlongtype;
                        $res1['IsCritical']             = (empty($medication_rows['IsCritical']))               ? '' : $medication_rows['IsCritical'];
                        $res1['ResponseRequired']       = (empty($medication_rows['ResponseRequired']))         ? '' : $medication_rows['ResponseRequired'];
                        $res1['StartFlag']              = (empty($medication_rows['StartFlag']))                ? '' : $medication_rows['StartFlag'];
                        $res1['NoOfDaysAfterPlanStarts']= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '' : $medication_rows['NoOfDaysAfterPlanStarts'];
                        $res1['SpecificDate']           = (empty($medication_rows['SpecificDate']))             ? '' : $medication_rows['SpecificDate'];
                        $res1['SpecificTime']           = (empty($medication_rows['SpecificTime']))             ? '' : $medication_rows['SpecificTime'];
                        $res1['Status']                 = (empty($medication_rows['Status']))                   ? '' : $medication_rows['Status'];
                        $res1['Link']                   = (empty($medication_rows['Link']))                     ? '' : $medication_rows['Link'];
                        $res1['AppointmentDate']        = "";   $res1['AppointmentTime']        = "";   $res1['AppointmentShortName']   = "";
                        $res1['AppointmentRequirements']= "";   $res1['LabTestDate']            = "";   $res1['LabTestTime']            = "";
                        $res1['TestName']               = "";   $res1['SelfTestID']             = "";   $res1['TestDescription']        = "";   
                        $res1['DietNo']                 = "";   $res1['DietPlanName']           = "";   $res1['AdvisorName']            = "";   
                        $res1['DietDurationDays']       = "";   $res1['DayNo']                  = "";   $res1['MealID']                 = "";   
                        $res1['MealDescription']        = "";   $res1['ExercisePlanNo']         = "";   $res1['ExercisePlanName']       = "";  
                        $res1['ExerciseDurationDays']   = "";   $res1['ExerciseSNo']            = "";   $res1['ExerciseDescription']    = "";   
                        $res1['ExerciseInstruction']    = "";   $res1['ExerciseNoOfReps']       = "";   $res1['ExerciseDuration']       = "";   
                        $res1['LabTestID']              = "";   $res1['LabTestRequirements']    = "";
                        $res1['AppointmentDuration']    = "";   $res1['AppointmentPlace']       = "";  
                         $res1['SpecialType'] = "";
                        $res1['SpecialDuration'] = "";
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res1);
                        }*/
                    array_push($result,$res1);
                    }
                    //exit;
                        /*If Howlong ie; duration is 0 then dont send*/

                        /*End of Checking value of Howlong*/
                    //array_push($result,$res1);
                    }
                }
                //END OF GET MEDICATION DETAILS

                 //GET APPOINTMENT DETAILS
                    $get_appointment= "select distinct t2.AppointmentDate,t2.AppointmentTime,t2.AppointmentShortName,t2.DoctorsName, t2.AppointmentPlace, t2.AppointmentDuration,
                                        t2.AppointmentRequirements,t2.Status,t2.CreatedDate
                                      from USER_APPOINTMENT_HEADER as t1,USER_APPOINTMENT_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.PlanCode='$plancode' and t1.UserID = t2.UserID 
                                      and t1.UserID = '$userid' $timestamp_query";
                   //echo $get_appointment.'<br>';exit;
                    $get_appointment_qry    = mysql_query($get_appointment);
                    $get_appointment_count  = mysql_num_rows($get_appointment_qry);
                    if($get_appointment_count)
                    {
                        while($appointment_rows = mysql_fetch_array($get_appointment_qry))
                        {
                            $res2['SectionID']              = '2';
                            $res2['AppointmentDate']        = (empty($appointment_rows['AppointmentDate']))         ? '' : $appointment_rows['AppointmentDate'];
                            $res2['AppointmentTime']        = (empty($appointment_rows['AppointmentTime']))         ? '' : $appointment_rows['AppointmentTime'];
                            $res2['AppointmentShortName']   = (empty($appointment_rows['AppointmentShortName']))    ? '' : stripslashes($appointment_rows['AppointmentShortName']);
                            $res2['DoctorsName']            = (empty($appointment_rows['DoctorsName']))             ? '' : stripslashes($appointment_rows['DoctorsName']);
                            $res2['AppointmentRequirements']= (empty($appointment_rows['AppointmentRequirements'])) ? '' : stripslashes($appointment_rows['AppointmentRequirements']); 
                            $res2['Status']                 = (empty($appointment_rows['Status']))                  ? '' : $appointment_rows['Status'];
                            $res2['AppointmentDuration']    = (empty($appointment_rows['AppointmentDuration']))                  ? '' : $appointment_rows['AppointmentDuration'];
                            $res2['AppointmentDuration']    = substr($res2['AppointmentDuration'], 0, 5);
                            $res2['AppointmentPlace']       = (empty($appointment_rows['AppointmentPlace']))                  ? '' : $appointment_rows['AppointmentPlace'];
                            $res2['PrescriptionNo']         = "";   $res2['PrescriptionName']       = "";       $res2['MedicineName']           = "";
                            $res2['When']                   = "";   $res2['Instruction']            = "";       $res2['Frequency']              = "";
                            $res2['FrequencyString']        = "";   $res2['HowLong']                = "";       $res2['HowLongType']            = "";
                            $res2['IsCritical']             = "";   $res2['ResponseRequired']       = "";       $res2['StartFlag']              = "";
                            $res2['NoOfDaysAfterPlanStarts']= "";   $res2['SpecificDate']           = "";       $res2['LabTestDate']            = "";
                            $res2['LabTestTime']            = "";   $res2['TestName']               = "";       $res2['SelfTestID']             = "";
                            $res2['RowNo']                  = "";   $res2['TestDescription']        = "";       $res2['DietNo']                 = "";
                            $res2['DietPlanName']           = "";   $res2['AdvisorName']            = "";       $res2['DietDurationDays']       = "";
                            $res2['DayNo']                  = "";   $res2['MealID']                 = "";       $res2['MealDescription']        = "";
                            $res2['SpecificTime']           = "";   $res2['ExercisePlanNo']         = "";       $res2['ExercisePlanName']       = "";
                            $res2['ExerciseDurationDays']   = "";   $res2['ExerciseSNo']            = "";       $res2['ExerciseDescription']    = "";
                            $res2['ExerciseInstruction']    = "";   $res2['ExerciseNoOfReps']       = "";       $res2['ExerciseDuration']       = "";
                            $res2['Link']                   = "";   $res2['LabTestID']              = "";       $res2['LabTestRequirements']    = "";
                             $res2['SpecialType'] = "";
                        $res2['SpecialDuration'] = "";
   
                        array_push($result,$res2);
                        }
                    }
                    //END OF APPOINTMENT DETAILS

                    //SELF TEST DETAILS
                    $get_self_test = "select distinct t1.SelfTestID,t2.RowNo,t2.MedicalTestID,t2.TestName,t2.DoctorsName,t2.TestDescription,t2.InstructionID,
                                      t2.Frequency,t2.HowLong,t2.HowLongType,t2.ResponseRequired,t2.StartFlag,t2.NoOfDaysAfterPlanStarts,
                                      t2.SpecificDate,t2.FrequencyString,t2.Status,t2.Link,t2.CreatedDate
                                      from USER_SELF_TEST_HEADER as t1,USER_SELF_TEST_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.SelfTestID=t2.SelfTestID and t1.PlanCode='$plancode' 
                                      and t1.UserID = t2.UserID and t1.UserID = '$userid' $timestamp_query";
                    //echo $get_self_test;exit;
                    $get_self_test_qry  = mysql_query($get_self_test);
                    $get_self_test_count= mysql_num_rows($get_self_test_qry);
                    if($get_self_test_count)
                    {
                        while($self_test_rows=mysql_fetch_array($get_self_test_qry))
                        {
                            $res31['SectionID']                 = '3-1';
                            $res31['SelfTestID']                = (empty($self_test_rows['SelfTestID']))                ? '' : $self_test_rows['SelfTestID'];
                            $res31['RowNo']                     = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
                            $res31['TestName']                  = (empty($self_test_rows['TestName']))                  ? '' : stripslashes($self_test_rows['TestName']);
                            $MedicalTestID                      = (empty($self_test_rows['MedicalTestID']))             ? '' : stripslashes($self_test_rows['MedicalTestID']);
                            if($MedicalTestID == "5"){
                                $res31['SpecialType'] = "P";
                                $res31['SpecialDuration'] = "01:30";
                            } else {
                                $res31['SpecialType'] = "";
                                $res31['SpecialDuration'] = "";
                            }
                            $res31['DoctorsName']               = (empty($self_test_rows['DoctorsName']))               ? '' : stripslashes($self_test_rows['DoctorsName']); 
                            $res31['TestDescription']           = (empty($self_test_rows['TestDescription']))           ? '' : stripslashes($self_test_rows['TestDescription']);
                            $res31['Instruction']               = (empty($self_test_rows['InstructionID']))             ? '' : $self_test_rows['InstructionID']; 
                            $res31['Frequency']                 = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
                            $res31['FrequencyString']           = (empty($self_test_rows['FrequencyString']))           ? '' : $self_test_rows['FrequencyString'];
                           
                            $howlong                            = (empty($self_test_rows['HowLong']))                  ? '' : $self_test_rows['HowLong'];
                            $howlongtype                        = (empty($self_test_rows['HowLongType']))              ? '' : $self_test_rows['HowLongType'];

                            if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                            {
                            //echo 123;exit;
                            $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                            $howlongtype        = "Days";
                            }

                            $res31['HowLong']                   = $howlong;
                            $res31['HowLongType']               = $howlongtype;

                            $res31['ResponseRequired']          = (empty($self_test_rows['ResponseRequired']))          ? '' : $self_test_rows['ResponseRequired'];
                            $res31['StartFlag']                 = (empty($self_test_rows['StartFlag']))                 ? '' : $self_test_rows['StartFlag']; 
                            $res31['NoOfDaysAfterPlanStarts']   = (empty($self_test_rows['NoOfDaysAfterPlanStarts']))   ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
                            $res31['SpecificDate']              = (empty($self_test_rows['SpecificDate']))              ? '' : $self_test_rows['SpecificDate'];
                            $res31['Status']                    = (empty($self_test_rows['Status']))                    ? '' : $self_test_rows['Status'];
                            $res31['Link']                      = (empty($self_test_rows['Link']))                      ? '' : $self_test_rows['Link'];
                            $res31['PrescriptionNo']            = "";   $res31['PrescriptionName']          = "";   $res31['MedicineName']              = "";
                            $res31['When']                      = "";   $res31['IsCritical']                = "";   $res31['AppointmentDate']           = "";
                            $res31['AppointmentTime']           = "";   $res31['AppointmentShortName']      = "";   $res31['AppointmentRequirements']   = "";
                            $res31['LabTestDate']               = "";   $res31['LabTestTime']               = "";   $res31['DietNo']                    = "";
                            $res31['DietPlanName']              = "";   $res31['AdvisorName']               = "";   $res31['DietDurationDays']          = "";
                            $res31['DayNo']                     = "";   $res31['MealID']                    = "";   $res31['MealDescription']           = "";
                            $res31['SpecificTime']              = "";   $res31['ExercisePlanNo']            = "";   $res31['ExercisePlanName']          = "";
                            $res31['ExerciseDurationDays']      = "";   $res31['ExerciseSNo']               = "";   $res31['ExerciseDescription']       = "";
                            $res31['ExerciseInstruction']       = "";   $res31['ExerciseNoOfReps']          = "";   $res31['ExerciseDuration']          = "";
                            $res31['LabTestID']                 = "";   $res31['LabTestRequirements']       = "";
                            $res31['AppointmentDuration']       = "";   $res31['AppointmentPlace']          = "";  
                        array_push($result,$res31);
                               if($MedicalTestID == "5"){
                                //POSTPRANDIAL INSTRUCTION
                                //$st = date('H:i:s',strtotime($st));
                                $res8['SectionID']              = '8';
                                $res8['PrescriptionNo']         = (empty($self_test_rows['SelfTestID']))                     ? '' : $self_test_rows['SelfTestID']; 
                                $res8['PrescriptionName']       = "Diet Instruction";
                                $res8['DoctorsName']            = (empty($self_test_rows['DoctorsName']))               ? '' : stripslashes($self_test_rows['DoctorsName']);
                                
                                if($self_test_rows['InstructionID'] == "5"){
                                     $res8['When']                   = "1-0-0-0";
                                     $res8['MedicineName']           = "Have a nutritious Breakfast";
                                } else if($self_test_rows['InstructionID'] == "9"){
                                     $res8['When']                   = "0-1-0-0";
                                     $res8['MedicineName']           = "Have a nutritious Lunch";
                                }
                                 else if($self_test_rows['InstructionID'] == "18"){
                                     $res8['When']                   = "0-0-1-0";
                                     $res8['MedicineName']           = "Have a nutritious Dinner";
                                } else {
                                    $res8['When']                   = "1-0-0-0";
                                    $res8['MedicineName']           = "Have a nutritious meal";
                                }
                                $res8['RowNo']                  = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
                                $res8['Instruction']            = "With Food"; 
                                $res8['Frequency']              = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
                                $res8['ActionText']             = "Diet";
                                $res8['FrequencyString']        = (empty($self_test_rows['FrequencyString']))                 ? '' : $self_test_rows['FrequencyString'];
                                $howlong                        = (empty($self_test_rows['HowLong']))                  ? '' : $self_test_rows['HowLong'];
                                $howlongtype                    = (empty($self_test_rows['HowLongType']))              ? '' : $self_test_rows['HowLongType'];

                                if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                                {
                                //echo 123;exit;
                                $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                                $howlongtype        = "Days";
                                }

                                $res8['HowLong']                = $howlong;
                                $res8['HowLongType']            = $howlongtype;
                                $res8['IsCritical']             = "N";
                                $res8['ResponseRequired']       = "N";
                                $res8['StartFlag']              = (empty($self_test_rows['StartFlag']))                ? '' : $self_test_rows['StartFlag'];
                                $res8['NoOfDaysAfterPlanStarts']= (empty($self_test_rows['NoOfDaysAfterPlanStarts']))  ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
                                $res8['SpecificDate']           = (empty($self_test_rows['SpecificDate']))             ? '' : $self_test_rows['SpecificDate'];
                                $res8['SpecificTime']           = "";
                                $res8['Status']                 = (empty($self_test_rows['Status']))                   ? '' : $self_test_rows['Status'];
                                $res8['Link']                   = (empty($self_test_rows['Link']))                     ? '' : $self_test_rows['Link'];
                                $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                                $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                                $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                                $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                                $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                                $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                                $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                                $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                                $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                                $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = "";          
                                 $res8['SpecialType'] = "";
                                $res8['SpecialDuration'] = "";
                                /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                                {

                                }
                                else
                                {
                                    array_push($result,$res8);
                                }*/
                            array_push($result,$res8);
                            }
                        }   
                    }
                    //END OF SELF TEST DETAILS

                    //GET LAB TEST DETAILS
                    $get_lab_test = "select distinct t1.LabTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.LabTestRequirements,
                                    t2.LabTestDate,t2.LabTestTime,t2.Status,t2.CreatedDate
                                    from USER_LAB_TEST_HEADER1 as t1,USER_LAB_TEST_DETAILS1 as t2
                                    where t1.PlanCode=t2.PlanCode and t1.LabTestID=t2.LabTestID and t1.PlanCode='$plancode' 
                                    and t1.UserID = t2.UserID and t1.UserID = '$userid' $timestamp_query";
                    $get_lab_test_qry  = mysql_query($get_lab_test);
                    $get_lab_test_count= mysql_num_rows($get_lab_test_qry);
                    if($get_lab_test_count)
                    {
                        while($lab_test_rows=mysql_fetch_array($get_lab_test_qry))
                        {
                            $res32['SectionID']                 = '3-2';
                            $res32['LabTestID']                 = (empty($lab_test_rows['LabTestID']))                  ? '' : $lab_test_rows['LabTestID'];
                            $res32['RowNo']                     = (empty($lab_test_rows['RowNo']))                      ? '' : $lab_test_rows['RowNo']; 
                            $res32['TestName']                  = (empty($lab_test_rows['TestName']))                   ? '' : stripslashes($lab_test_rows['TestName']);
                            $res32['DoctorsName']               = (empty($lab_test_rows['DoctorsName']))                ? '' : stripslashes($lab_test_rows['DoctorsName']); 
                            $res32['LabTestRequirements']       = (empty($lab_test_rows['LabTestRequirements']))        ? '' : stripslashes($lab_test_rows['LabTestRequirements']);
                            $labtest_date                       = (empty($lab_test_rows['LabTestDate']))                ? '' : $lab_test_rows['LabTestDate']; ;
                            $labtest_time                       = (empty($lab_test_rows['LabTestTime']))                ? '' : $lab_test_rows['LabTestTime'];;
                            $res32['LabTestDate']               = $labtest_date;
                            $res32['LabTestTime']               = $labtest_time;
                            $res32['Status']                    = (empty($lab_test_rows['Status']))                     ? '' : $lab_test_rows['Status'];
                            $res32['PrescriptionNo']            = "";   $res32['PrescriptionName']          = "";   $res32['MedicineName']              = "";
                            $res32['When']                      = "";   $res32['HowLong']                   = "";   $res32['HowLongType']               = "";
                            $res32['IsCritical']                = "";   $res32['StartFlag']                 = "";   $res32['NoOfDaysAfterPlanStarts']   = "";
                            $res32['SpecificDate']              = "";   $res32['AppointmentDate']           = "";   $res32['AppointmentTime']           = "";
                            $res32['AppointmentShortName']      = "";   $res32['AppointmentRequirements']   = "";   $res32['SelfTestID']                = "";
                            $res32['TestDescription']           = "";   $res32['Frequency']                 = "";   $res32['ResponseRequired']          = "";
                            $res32['FrequencyString']           = "";   $res32['DietNo']                    = "";   $res32['DietPlanName']              = "";
                            $res32['AdvisorName']               = "";   $res32['DietDurationDays']          = "";   $res32['DayNo']                     = "";
                            $res32['MealID']                    = "";   $res32['MealDescription']           = "";   $res32['SpecificTime']              = "";
                            $res32['ExercisePlanNo']            = "";   $res32['ExercisePlanName']          = "";   $res32['ExerciseDurationDays']      = "";
                            $res32['ExerciseSNo']               = "";   $res32['ExerciseDescription']       = "";   $res32['ExerciseInstruction']       = "";
                            $res32['ExerciseNoOfReps']          = "";   $res32['ExerciseDuration']          = "";   $res32['Link']                      = "";
                            $res32['Instruction']               = "";   
                            $res32['AppointmentDuration']       = "";   $res32['AppointmentPlace']          = "";  
                             $res32['SpecialType'] = "";
                            $res32['SpecialDuration'] = "";
                            /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $labtest_date!="")
                            {

                            }
                            else
                            {
                                array_push($result,$res32);
                            }*/
                        array_push($result,$res32);
                        }   
                    }
                    //END OF LAB TEST DETAILS

                    //GET DIET DETAILS
                    $get_diet_detials       = "select distinct t1.DietNo,t1.DietPlanName,t1.AdvisorName,t1.DietDurationDays,t2.DayNo,t2.InstructionID,t2.MealDescription,
                                            t2.SpecificTime,t2.Status,t2.Link,t2.CreatedDate
                                            from USER_DIET_HEADER as t1,USER_DIET_DETAILS as t2
                                            where t1.PlanCode=t2.PlanCode and t1.DietNo=t2.DietNo and t1.PlanCode='$plancode' 
                                            and t1.UserID = t2.UserID and t1.UserID = '$userid' $timestamp_query";
                                            //echo $get_diet_detials;exit;
                    $get_diet_detials_qry   = mysql_query($get_diet_detials);
                    $get_diet_detials_count = mysql_num_rows($get_diet_detials_qry);
                    $dietresult = array();
                    if($get_diet_detials_count)
                    {
                        while($diet_rows=mysql_fetch_array($get_diet_detials_qry))
                        {
                            $res4['SectionID']                  = '4';
                            $res4['DietNo']                     = (empty($diet_rows['DietNo']))             ? '' : $diet_rows['DietNo'];
                            $res4['DietPlanName']               = (empty($diet_rows['DietPlanName']))       ? '' : $diet_rows['DietPlanName'];
                            $res4['AdvisorName']                = (empty($diet_rows['AdvisorName']))        ? '' : $diet_rows['AdvisorName'];
                            $res4['DietDurationDays']           = (empty($diet_rows['DietDurationDays']))   ? '' : $diet_rows['DietDurationDays'];
                            $resd['DayNo']                      = (empty($diet_rows['DayNo']))              ? '' : $diet_rows['DayNo'];
                            $resd['MealID']                     = (empty($diet_rows['InstructionID']))      ? '' : $diet_rows['InstructionID'];
                            $resd['MealDescription']            = (empty($diet_rows['MealDescription']))    ? '' : $diet_rows['MealDescription'];
                            $resd['SpecificTime']               = (empty($diet_rows['SpecificTime']))       ? '' : $diet_rows['SpecificTime'];
                            $res4['Status']                     = (empty($diet_rows['Status']))             ? '' : $diet_rows['Status'];
                            $res4['Link']                       = (empty($diet_rows['Link']))               ? '' : $diet_rows['Link'];
                            array_push($dietresult,$resd);
                            
                        }
                            $res4['plan_info']                  = $dietresult;
                            $res4['LabTestDate']                = "";   $res4['LabTestTime']                = "";   $res4['TestName']                   = "";
                            $res4['DoctorsName']                = "";   $res4['Instruction']                = "";   $res4['ResponseRequired']           = "";
                            $res4['PrescriptionNo']             = "";   $res4['PrescriptionName']           = "";   $res4['MedicineName']               = "";
                            $res4['When']                       = "";   $res4['HowLong']                    = "";   $res4['HowLongType']                = "";
                            $res4['IsCritical']                 = "";   $res4['StartFlag']                  = "";   $res4['NoOfDaysAfterPlanStarts']    = "";
                            $res4['SpecificDate']               = "";   $res4['AppointmentDate']            = "";   $res4['AppointmentTime']            = "";
                            $res4['AppointmentShortName']       = "";   $res4['AppointmentRequirements']    = "";   $res4['SelfTestID']                 = "";
                            $res4['RowNo']                      = "";   $res4['TestDescription']            = "";   $res4['Frequency']                  = "";
                            $res4['FrequencyString']            = "";   $res4['ExercisePlanNo']             = "";   $res4['ExercisePlanName']           = "";
                            $res4['ExerciseDurationDays']       = "";   $res4['ExerciseSNo']                = "";   $res4['ExerciseDescription']        = "";
                            $res4['ExerciseInstruction']        = "";   $res4['ExerciseNoOfReps']           = "";   $res4['ExerciseDuration']           = "";
                            $res4['LabTestID']                  = "";   $res4['LabTestRequirements']        = "";
                            $res4['AppointmentDuration']        = "";   $res4['AppointmentPlace']          = "";  
                             $res4['SpecialType'] = "";
                        $res4['SpecialDuration'] = "";
                        array_push($result,$res4);
                           
                    }
                    //END OF DIET DETAILS

/*                    //GET EXERCISE DETAILS
                    $get_exercise_detials       = "select distinct t1.ExercisePlanNo,t1.ExercisePlanName,t1.AdvisorName,t1.ExerciseDurationDays,
                                                t2.DayNo,t2.ExerciseSNo,t2.ExerciseDescription,t2.ExerciseInstruction,t2.ExerciseNoOfReps,
                                                t2.ExerciseDuration,t2.Link,t2.CreatedDate
                                                from USER_EXERCISE_HEADER as t1,USER_EXERCISE_DETAILS as t2
                                                where t1.PlanCode=t2.PlanCode and t1.ExercisePlanNo=t2.ExercisePlanNo and t1.PlanCode='$plancode' and t1.UserID = t2.UserID and t1.UserID = '$userid'";
                    $get_exercise_detials_qry   = mysql_query($get_exercise_detials);
                    $get_exercise_detials_count = mysql_num_rows($get_exercise_detials_qry);
                    $exerciserepo = array();
                    if($get_exercise_detials_count)
                    {
                        while($exercise_rows=mysql_fetch_array($get_exercise_detials_qry))
                        {
                            $res5['SectionID']                  = '5';
                            $res5['ExercisePlanNo']             = (empty($exercise_rows['ExercisePlanNo']))         ? '' : $exercise_rows['ExercisePlanNo'];
                            $res5['ExercisePlanName']           = (empty($exercise_rows['ExercisePlanName']))       ? '' : $exercise_rows['ExercisePlanName'];
                            $res5['AdvisorName']                = (empty($exercise_rows['AdvisorName']))            ? '' : $exercise_rows['AdvisorName'];
                            $res5['ExerciseDurationDays']       = (empty($exercise_rows['ExerciseDurationDays']))   ? '' : $exercise_rows['ExerciseDurationDays'];
                            $res6['DayNo']                      = (empty($exercise_rows['DayNo']))                  ? '' : $exercise_rows['DayNo'];
                            $res5['ExerciseSNo']                = (empty($exercise_rows['ExerciseSNo']))            ? '' : $exercise_rows['ExerciseSNo'];
                            $res6['ExerciseDescription']        = (empty($exercise_rows['ExerciseDescription']))    ? '' : $exercise_rows['ExerciseDescription'];
                            $res6['ExerciseInstruction']        = (empty($exercise_rows['ExerciseInstruction']))    ? '' : $exercise_rows['ExerciseInstruction'];
                            $res6['ExerciseNoOfReps']           = (empty($exercise_rows['ExerciseNoOfReps']))       ? '' : $exercise_rows['ExerciseNoOfReps'];
                            $res6['ExerciseDuration']           = (empty($exercise_rows['ExerciseDuration']))       ? '' : $exercise_rows['ExerciseDuration'];
                            $res6['Link']                       = (empty($exercise_rows['Link']))                   ? '' : $exercise_rows['Link'];
                            array_push($exerciserepo,$res6);
                            }
                            $res5['exercise_info']              = $exerciserepo;
                            $res5['DietNo']                     = "";   $res5['DietPlanName']               = "";   $res5['DietDurationDays']           = "";
                            $res5['MealID']                     = "";   $res5['MealDescription']            = "";   $res5['SpecificTime']               = "";
                            $res5['LabTestDate']                = "";   $res5['LabTestTime']                = "";   $res5['TestName']                   = "";
                            $res5['DoctorsName']                = "";   $res5['Instruction']                = "";   $res5['ResponseRequired']           = "";
                            $res5['PrescriptionNo']             = "";   $res5['PrescriptionName']           = "";   $res5['MedicineName']               = "";
                            $res5['When']                       = "";   $res5['HowLong']                    = "";   $res5['HowLongType']                = "";
                            $res5['IsCritical']                 = "";   $res5['StartFlag']                  = "";   $res5['NoOfDaysAfterPlanStarts']    = "";
                            $res5['SpecificDate']               = "";   $res5['AppointmentDate']            = "";   $res5['AppointmentTime']            = "";
                            $res5['AppointmentShortName']       = "";   $res5['AppointmentRequirements']    = "";   $res5['SelfTestID']                 = "";
                            $res5['RowNo']                      = "";   $res5['TestDescription']            = "";   $res5['Frequency']                  = "";
                            $res5['FrequencyString']            = "";   $res5['LabTestID']                  = "";   $res5['LabTestRequirements']        = ""; 
                        array_push($result,$res5);
                    }
                    //END OF EXERCISE DETAILS
*/
                 //GET INSTRUCTION DETAILS
                $get_instruction = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName, t4.InstructionType, t2.RowNo,t2.SpecificTime,t3.ShortHand,t2.Instruction,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,t2.When,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString,t2.Link,t2.CreatedDate
                                    from USER_INSTRUCTION_HEADER as t1,USER_INSTRUCTION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3, INSTRUCTION_TYPE as t4
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t2.InstructionTypeID = t4.InstructionTypeID and t1.UserID = '$userid' $timestamp_query";
                //echo $get_medication;exit;
                $get_instruction_qry     = mysql_query($get_instruction);
                $get_instruction_count   = mysql_num_rows($get_instruction_qry);
                if($get_instruction_count)
                {
                    while($instruction_rows=mysql_fetch_array($get_instruction_qry))
                    {
                        $wheninput                      = $instruction_rows['When'];
                        if($wheninput == '16'){
                            $specifictimebundle           = (empty($instruction_rows['SpecificTime']))             ? '' : $instruction_rows['SpecificTime'];
                            $starray = array();
                            $starray = explode(",",$specifictimebundle);
                            foreach ($starray as $st) {
                       if($st != ""){
                        $st = date('H:i:s',strtotime($st));
                        $res8['SectionID']              = '6';
                        $res8['PrescriptionNo']         = (empty($instruction_rows['PrescriptionNo']))           ? '' : $instruction_rows['PrescriptionNo'];
                        $res8['PrescriptionName']       = (empty($instruction_rows['PrescriptionName']))         ? '' : stripslashes($instruction_rows['PrescriptionName']);
                        $res8['DoctorsName']            = (empty($instruction_rows['DoctorsName']))              ? '' : stripslashes($instruction_rows['DoctorsName']);
                        $res8['MedicineName']           = (empty($instruction_rows['MedicineName']))             ? '' : stripslashes($instruction_rows['MedicineName']);
                        $res8['When']                   = (empty($instruction_rows['ShortHand']))                ? '' : $instruction_rows['ShortHand'];
                        $res8['RowNo']                  = (empty($instruction_rows['RowNo']))                    ? '' : $instruction_rows['RowNo'];
                        $res8['Instruction']            = (empty($instruction_rows['Instruction']))              ? '' : $instruction_rows['Instruction'];
                        if($res8['Instruction'] == "NA"){
                            $res8['Instruction'] = "With Food";
                        }
                        //$res8['Instruction']            = ("NA") ? 'With Food' : $instruction_rows['Instruction'];
                         //echo $res8['Instruction'];exit;
                        $res8['Frequency']              = (empty($instruction_rows['Frequency']))                ? '' : $instruction_rows['Frequency'];
                        $res8['ActionText']             = (empty($instruction_rows['InstructionTypeID']))                ? '' : $instruction_rows['InstructionTypeID'];
                        $res8['FrequencyString']        = (empty($instruction_rows['FrequencyString']))          ? '' : $instruction_rows['FrequencyString'];
                        $howlong                        = (empty($instruction_rows['HowLong']))                  ? '' : $instruction_rows['HowLong'];
                        $howlongtype                    = (empty($instruction_rows['HowLongType']))              ? '' : $instruction_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res8['HowLong']                = $howlong;
                        $res8['HowLongType']            = $howlongtype;
                        $res8['IsCritical']             = (empty($instruction_rows['IsCritical']))               ? '' : $instruction_rows['IsCritical'];
                        $res8['ResponseRequired']       = (empty($instruction_rows['ResponseRequired']))         ? '' : $instruction_rows['ResponseRequired'];
                        $res8['StartFlag']              = (empty($instruction_rows['StartFlag']))                ? '' : $instruction_rows['StartFlag'];
                        $res8['NoOfDaysAfterPlanStarts']= (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '' : $instruction_rows['NoOfDaysAfterPlanStarts'];
                        $res8['SpecificDate']           = (empty($instruction_rows['SpecificDate']))             ? '' : $instruction_rows['SpecificDate'];
                        $res8['SpecificTime']           = $st;
                        $res8['Status']                 = (empty($instruction_rows['Status']))                   ? '' : $instruction_rows['Status'];
                        $res8['Link']                   = (empty($instruction_rows['Link']))                     ? '' : $instruction_rows['Link'];
                        $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                        $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                        $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                        $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                        $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                        $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                        $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                        $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                        $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                        $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']          = "";
                         $res8['SpecialType'] = "";
                        $res8['SpecialDuration'] = "";        
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res8);
                        }*/
                    array_push($result,$res8);
                    }
                            }
                        } else {
                        $res8['SectionID']              = '6';
                        $res8['PrescriptionNo']         = (empty($instruction_rows['PrescriptionNo']))           ? '' : $instruction_rows['PrescriptionNo'];
                        $res8['PrescriptionName']       = (empty($instruction_rows['PrescriptionName']))         ? '' : stripslashes($instruction_rows['PrescriptionName']);
                        $res8['DoctorsName']            = (empty($instruction_rows['DoctorsName']))              ? '' : stripslashes($instruction_rows['DoctorsName']);
                        $res8['MedicineName']           = (empty($instruction_rows['MedicineName']))             ? '' : stripslashes($instruction_rows['MedicineName']);
                        $res8['ActionText']             = (empty($instruction_rows['InstructionTypeID']))                ? '' : $instruction_rows['InstructionTypeID'];
                        $res8['When']                   = (empty($instruction_rows['ShortHand']))                ? '' : $instruction_rows['ShortHand'];
                        $res8['RowNo']                  = (empty($instruction_rows['RowNo']))                    ? '' : $instruction_rows['RowNo'];
                        $res8['Instruction']            = (empty($instruction_rows['Instruction']))              ? '' : $instruction_rows['Instruction'];
                        if($res8['Instruction'] == "NA"){
                            $res8['Instruction'] = "With Food";
                        }
                        //$res8['Instruction']            = ("NA") ? 'With Food' : $instruction_rows['Instruction'];
                         //echo $res8['Instruction'];exit;
                        $res8['Frequency']              = (empty($instruction_rows['Frequency']))                ? '' : $instruction_rows['Frequency'];
                        $res8['FrequencyString']        = (empty($instruction_rows['FrequencyString']))          ? '' : $instruction_rows['FrequencyString'];
                        $howlong                        = (empty($instruction_rows['HowLong']))                  ? '' : $instruction_rows['HowLong'];
                        $howlongtype                    = (empty($instruction_rows['HowLongType']))              ? '' : $instruction_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res8['HowLong']                = $howlong;
                        $res8['HowLongType']            = $howlongtype;
                        $res8['IsCritical']             = (empty($instruction_rows['IsCritical']))               ? '' : $instruction_rows['IsCritical'];
                        $res8['ResponseRequired']       = (empty($instruction_rows['ResponseRequired']))         ? '' : $instruction_rows['ResponseRequired'];
                        $res8['StartFlag']              = (empty($instruction_rows['StartFlag']))                ? '' : $instruction_rows['StartFlag'];
                        $res8['NoOfDaysAfterPlanStarts']= (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '' : $instruction_rows['NoOfDaysAfterPlanStarts'];
                        $res8['SpecificDate']           = (empty($instruction_rows['SpecificDate']))             ? '' : $instruction_rows['SpecificDate'];
                        $res8['SpecificTime']           = (empty($instruction_rows['SpecificTime']))             ? '' : $instruction_rows['SpecificTime'];
                        $res8['Status']                 = (empty($instruction_rows['Status']))                   ? '' : $instruction_rows['Status'];
                        $res8['Link']                   = (empty($instruction_rows['Link']))                     ? '' : $instruction_rows['Link'];
                        $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                        $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                        $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                        $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                        $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                        $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                        $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                        $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                        $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                        $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']          = "";
                         $res8['SpecialType'] = "";
                        $res8['SpecialDuration'] = ""; 
                        /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res8);
                        }*/
                    array_push($result,$res8);
                    }
                    //exit;
                        /*If Howlong ie; duration is 0 then dont send*/

                        /*End of Checking value of Howlong*/
                    //array_push($result,$res8);
                    }
                }
                //END OF GET INSTRUCTION DETAILS
                $plan['PLANPIPER_PLAN_INFO']            = $plan_info;
                $plan['PLANPIPER_MERCHANT_SOCIAL_MEDIA']= $social_media_info;
                $plan['ANALYTICS']                      = $analytics;
                $plan['PLANPIPER_PLAN_GOALS']           = $goal_info;
                $plan['PLANPIPER_ACTIVITIES']           = $result;
            array_push($plan_details,$plan);
            }
           
        

         echo "{".$paid_status.json_encode('PLANS').':'.json_encode($plan_details)."}";

    }
    else
    {
        $result="";$plan_info="";$social_media_info="";
        echo "{".$paid_status.json_encode('PLANPIPER_PLAN_INFO').':'.json_encode($plan_info).","
                .json_encode('PLANPIPER_MERCHANT_SOCIAL_MEDIA').':'.json_encode($social_media_info).","
                .json_encode('PLANPIPER_ACTIVITIES').':'.json_encode($result)."}";  
    }
}
//*********************************END OF GET ACTIVITIES UNDER PLANCODE**************************************************

//*********************************ONE TIME PASSWORD*********************************************************************
elseif($_REQUEST['RequestType']=="change_password" && $_REQUEST['userid']!="" && $_REQUEST['new_password']!="")
{
 //echo "111";
    $user_id        = $_REQUEST['userid'];
    $newpassword    = $_REQUEST['new_password'];
    
    $ok='';
    if($user_id!='' && $newpassword!='')
    {
        $update_otpass  = "update USER_ACCESS set Password='$newpassword',PasswordStatus=1 where UserID='$user_id'";
        //echo "sql".$update_otpass;
        $up_qry         = mysql_query($update_otpass);
        $ok             = mysql_affected_rows();
        //echo "R".$ok;
        if($ok==1)
        {
            echo "{".json_encode('PLANPIPER_ONETIMEPASSWORD').':'.json_encode('1')."}";
        }
        else
        {
            echo "{".json_encode('PLANPIPER_ONETIMEPASSWORD').':'.json_encode('0')."}";
        }
    }
    else
    {
        echo "{".json_encode('PLANPIPER_ONETIMEPASSWORD').':'.json_encode('0')."}";
    }
}
//*****************************END OF ONE TIME PASSWORD***********************************************

//*****************************FORGOT PASSWORD********************************************************
elseif($_REQUEST['RequestType']=="forgot_password" && $_REQUEST['username']!="")
{
    //echo 123;exit;
    function mailresetlink($to,$token,$name)
    {   
    //require 'SMTP/PHPMailerAutoload.php';
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    // Set PHPMailer to use the sendmail transport
    $mail->isSMTP();
    //Set who the message is to be sent from
    $mail->setFrom('support@planpiper.com', 'Admin');
    //Set who the message is to be sent to
    $mail->addAddress($to,$name);
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
            <p>$token</p>
            <p>While you cannot reply to this email, please feel free to write to us with any queries at <a href='mailto:support@planpiper.com'>support@planpiper.com<a></p>   
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
                echo "{".json_encode('PLANPIPER_FORGOT_PASSWORD').':'.json_encode("0")."}";
            } 
            else
            {
                echo "{".json_encode('PLANPIPER_FORGOT_PASSWORD').':'.json_encode("1")."}";
            }
        } 

    $username      = $_REQUEST['username'];
    $username      = stripslashes($username);
    $username      = mysql_real_escape_string($username);
    
    $q  =   "select t1.UserID, t1.EmailID, t3.FirstName, t3.LastName,substr(t1.MobileNo,6) as MobileNo
            from USER_ACCESS as t1, USER_MERCHANT_MAPPING as t2, USER_DETAILS as t3
            where (t1.EmailID = '$username' or  substr(t1.MobileNo,6)='$username') and t1.UserID=t2.UserID and t2.UserID=t3.UserID 
            and t2.Status='A'";
    //echo $q;exit;
    $r  = mysql_query($q);
    $n  = mysql_num_rows($r);
    if($n > 0)
    {
        if($row = mysql_fetch_array($r))
        {
            $userid         = $row['UserID'];  
            $user_email_id  = (empty($row['EmailID']))      ? '' : $row['EmailID'];
            $user_mobile    = (empty($row['MobileNo']))     ? '' : $row['MobileNo'];
            $name           = (empty($row['FirstName']))    ? '' : $row['FirstName'];
        }
    }
    else
    {
        $userid = "";
        $user_email_id  = "";
    }
    if($n==0)
    {
        //echo "Email id is not registered";die();
        echo "{".json_encode('PLANPIPER_FORGOT_PASSWORD').':'.json_encode("2")."}";
    }
    else
    {
    $token=mt_rand();
    $q  = "update USER_ACCESS set Password = '$token',PasswordStatus=0 where UserID='$userid'";   
    mysql_query($q);
    
           
        if(isset($_REQUEST['username']))
        {
            //echo $user_email_id ." ".$user_mobile;exit;
            if($user_email_id=='' && $user_mobile!=""){
                //FETCH SMS PARAMETERS FROM DATABASE
                $get_sms_configuration      = "select MerchantID, URL, UserParam, UserParamValue, PwdParam, PwdParamValue, SenderIDParam, SenderIDParamValue, MessageParam, MobileParam from SMS_GATEWAY_DETAILS where MerchantID='123456789'";
                    $get_sms_configuration_query  = mysql_query($get_sms_configuration);
                    $sms_config_count   = mysql_num_rows($get_sms_configuration_query);
                    if($sms_config_count == 1)
                    {
                      while($row = mysql_fetch_array($get_sms_configuration_query)){
                        $smsurl                 = $row['URL'];
                        $username_parameter     = $row['UserParam'];
                        $username_value         = $row['UserParamValue'];
                        $password_parameter     = $row['PwdParam'];
                        $password_value         = $row['PwdParamValue'];
                        $sender_parameter       = $row['SenderIDParam'];
                        $sender_value           = $row['SenderIDParamValue'];
                        $message_parameter      = $row['MessageParam'];
                        $number_parameter       = $row['MobileParam'];
                      }
                    $message = "One Time Password for your Planpiper Login is: $token";
                    $message  = urlencode($message);
                    $phonenum = urlencode($user_mobile);
                    $request  = "?".$username_parameter."=".$username_value."&".$password_parameter."=".$password_value."&".$message_parameter."=".$message."&".$number_parameter."=".$phonenum."&".$sender_parameter."=".$sender_value;
                    $url      = $smsurl.$request;
                    //echo $url;exit;
                    file_get_contents($url);
                    echo "{".json_encode('PLANPIPER_FORGOT_PASSWORD').':'.json_encode("3")."}";
                    }
                    else{
                      $smsurl=""; $username_parameter=""; $username_value=""; $password_parameter=""; $password_value=""; 
                      $sender_parameter=""; $sender_value=""; $message_parameter=""; $number_parameter="";
                       echo "{".json_encode('PLANPIPER_FORGOT_PASSWORD').':'.json_encode("4")."}";
                    }
                //END OF FETCH SMS PARAMETERS FROM DATABASE
            }
            else{
            mailresetlink($user_email_id,$token,$name);
            }
            
        }
    }
}
//*****************************END OF FORGOT PASSWORD*********************************************

//*****************************LOG ERRORS*********************************************************
elseif($_REQUEST['RequestType']=="errorlog" && $_REQUEST['userid']!="")
{
    $userid     = (empty($_REQUEST['userid']))    ? '' : $_REQUEST['userid'];
    $ostype     = (empty($_REQUEST['ostype']))    ? '' : $_REQUEST['ostype'];
    $deviceinfo = (empty($_REQUEST['deviceinfo']))? '' : $_REQUEST['deviceinfo'];
    $page       = (empty($_REQUEST['page']))      ? '' : $_REQUEST['page'];
    $error      = (empty($_REQUEST['error']))     ? '' : $_REQUEST['error'];
    $time       = (empty($_REQUEST['time']))      ? '' : $_REQUEST['time'];

$msg = "USERID: " . $userid . " OSTYPE: " . $ostype . " DEVICEINFO: " . $deviceinfo . " CUSTOMIZED_ERROR_MESSAGE: " . $page . " SYSTEM_GENERATED_ERROR: ".$error. " PHONE_TIME: ".$time. " SYSTEM_TIME: ".$currentdatetime;
 
    function writeToLogFile($msg)
    {
        $today          = date("Y_m_d"); 
        $logfile        = $today."_log.txt"; 
        $dir            = 'logs';
        $saveLocation   = $dir. '/' .$logfile;
        if(!$handle     = fopen($saveLocation, "a"))
        {
             exit;
        }
        else
        {
            if (fwrite($handle,"$msg\r\n") === FALSE)
            {
                exit;
            }
        fclose($handle);
        }
    }

//CALL LOG FUNCTION
writeToLogFile($msg);
}
//*****************************END OF LOG ERRORS**********************************************

//*****************************PROFILE SETTINGS***********************************************
elseif($_REQUEST['RequestType']=="profile_settings" && $_REQUEST['user_id']!="")
{
$userid                 = $_REQUEST['user_id'];
$type                   = (empty($_REQUEST['Type']))        ? '' : $_REQUEST['Type']; /*tab1(user profile) or tab2(support person)*/
$first_name             = (empty($_REQUEST['first_name']))  ? '' : mysql_real_escape_string(trim($_REQUEST['first_name']));
$last_name              = (empty($_REQUEST['last_name']))   ? '' : mysql_real_escape_string(trim($_REQUEST['last_name']));
$gender                 = (empty($_REQUEST['gender']))      ? '' : mysql_real_escape_string(trim($_REQUEST['gender']));
$dob                    = (empty($_REQUEST['dob']))         ? '' : mysql_real_escape_string(trim($_REQUEST['dob']));
$dob                    = date('Y-m-d', strtotime($dob));
$blood_group            = (empty($_REQUEST['blood_group'])) ? '' : mysql_real_escape_string(trim(urlencode($_REQUEST['blood_group'])));
$country_code           = (empty($_REQUEST['cc']))          ? '' : mysql_real_escape_string(trim($_REQUEST['cc']));
$state_code             = (empty($_REQUEST['sc']))          ? '' : mysql_real_escape_string(trim($_REQUEST['sc']));
$city_code              = (empty($_REQUEST['ci']))          ? '' : mysql_real_escape_string(trim($_REQUEST['ci']));
$addressline1           = (empty($_REQUEST['address1']))    ? '' : mysql_real_escape_string(trim($_REQUEST['address1']));
$addressline2           = (empty($_REQUEST['address2']))    ? '' : mysql_real_escape_string(trim($_REQUEST['address2']));
$pincode                = (empty($_REQUEST['pincode']))     ? '' : mysql_real_escape_string(trim($_REQUEST['pincode']));
$landline_country_code  = (empty($_REQUEST['landline_cc'])) ? '' : mysql_real_escape_string(trim($_REQUEST['landline_cc']));
$landline_area_code     = (empty($_REQUEST['landline_ac'])) ? '' : mysql_real_escape_string(trim($_REQUEST['landline_ac']));
$landline               = (empty($_REQUEST['landline']))    ? '' : mysql_real_escape_string(trim($_REQUEST['landline']));
$mobile_cc              = (empty($_REQUEST['mobile_cc']))   ? '' : mysql_real_escape_string(trim($_REQUEST['mobile_cc']));
$display_picture        = (empty($_REQUEST['dp']))          ? '' : mysql_real_escape_string(trim($_REQUEST['dp'])); /*R - If Profile pic has been deleted by the User*/ 

$email_id               = (empty($_REQUEST['email_id']))    ? '' : mysql_real_escape_string(trim($_REQUEST['email_id']));

$mobile_no              = (empty($_REQUEST['mobile_no']))   ? '' : mysql_real_escape_string(trim($_REQUEST['mobile_no']));
$mobile_no              = $country_code.$mobile_no;
$support_name           = (empty($_REQUEST['support_name']))? '' : mysql_real_escape_string(trim($_REQUEST['support_name']));
$support_mobile         = (empty($_REQUEST['support_mobile']))? '' : mysql_real_escape_string(trim($_REQUEST['support_mobile']));

    
    /*Check Duplicate Email or Mobile No*/
    $return             = "0"; 
    $duplicate_mobile   = 0;
    $duplicate_email    = 0;
    if($type=='tab2'){
       $check_count = "0";
    } else {
        $check_duplicate_query  = mysql_query("select EmailID,MobileNo from USER_ACCESS 
                                            where (EmailID='$email_id' || MobileNo='$mobile_no') and UserStatus='A' 
                                            and UserID!='$userid'");
    $check_count            = mysql_num_rows($check_duplicate_query);
    }
    
    if($check_count=='0')
    {
        //echo 123;
        $update_profile = "";

        $update_profile .= "update USER_DETAILS as t1";

        if($type=='tab2')
        $update_profile .= " set t1.SupportPersonName='$support_name', t1.SupportPersonMobileNo='$support_mobile' where t1.UserID='$userid'";  
        else
        {
        $update_profile .= " ,USER_ACCESS as t2 set t1.FirstName='$first_name', t1.LastName='$last_name', t1.Gender='$gender',
        t1.DOB='$dob', t1.BloodGroup='$blood_group', t1.CountryCode='$country_code', t1.StateID='$state_code', t1.CityID='$city_code', t1.AddressLine1='$addressline1', t1.AddressLine2='$addressline2', 
        t1.PinCode='$pincode', t1.AreaCode='$landline_area_code', t1.Landline='$landline', t2.MobileNo='$mobile_no',t2.EmailID='$email_id'";
        
        if($display_picture=='R')
        {
        $update_profile .= ",t1.ProfilePicture='' ";

            $select_image           = "select ProfilePicture from USER_DETAILS where UserID='$userid'";
            //echo $select_image;exit;
            $select_image_qry       = mysql_query($select_image);
            $select_image_count     = mysql_num_rows($select_image_qry);
            if($select_image_count)
            {
                //echo $select_image_count;exit;
                if($rows = mysql_fetch_array($select_image_qry))
                {
                    $image      = (empty($rows['ProfilePicture']))         ? ''    : $rows['ProfilePicture'];
                    //echo $image;exit;
                    if($image)
                    {
                        if(file_exists($originalpath.$image)){
                            //echo 123;exit;
                            unlink($originalpath.$image);
                        }
                        if(file_exists($compressedpath.$image)){
                            unlink($compressedpath.$image);
                        }
                    
                    }
                }
            }
            //END OF REMOVE PROFILE PICTURE OF USER FROM SERVER
        }

        $update_profile .= " where t1.UserID='$userid' and t1.UserID=t2.UserID ";
        }
        //echo $update_profile;exit;
        $update_profile_qry= mysql_query($update_profile);
        $check_update      = mysql_affected_rows();
        if($check_update)
        $return = 1;
        else
        $return = 1;//Changed to 1 temperorily because of dp issue. was 0. 
    }
    else
    {
        //echo 456;
        $profile_rows   = mysql_fetch_array($check_duplicate_query);
        $mobile         = $profile_rows['MobileNo'];
        $email          = $profile_rows['EmailID'];
        if($mobile == $mobile_no)
        $duplicate_mobile   = 1;
        if($email == $email_id)
        $duplicate_email    = 1;

        if($duplicate_mobile == 1 && $duplicate_email == 0)
        $return = 2;
        elseif ($duplicate_mobile == 0 && $duplicate_email == 1) 
        $return = 3;
        elseif ($duplicate_mobile == 1 && $duplicate_email == 1)
        $return = 4;
    }   
echo "{".$paid_status.json_encode('PLANPIPER_PROFILE_SETTINGS').':'.json_encode("$return")."}";
}
//*****************************END OF PROFILE SETTINGS***************************************

//*****************************ADD PROFILE PICTURE OF USER************************************************************************************************
elseif($_REQUEST['RequestType']=="add_profile_picture" && $_REQUEST['user_id']!="")
{
    //echo 123;exit;
    $fullfilename       = "";
    $user_id            = $_REQUEST['user_id'];
    $picture            = (empty($_FILES['filename']['name']))  ? '' : $_FILES['filename']['name'];
    $fullfilename       = "";

        $select_image           = "select ProfilePicture from USER_DETAILS where UserID='$user_id'";
            //echo $select_image;exit;
        $select_image_qry       = mysql_query($select_image);
        $select_image_count     = mysql_num_rows($select_image_qry);
        if($select_image_count)
        {
            if($rows = mysql_fetch_array($select_image_qry))
            {
                $image      = (empty($rows['ProfilePicture']))         ? ''    : $rows['ProfilePicture'];
                if($image)
                {
                    if(file_exists($originalpath.$image)){
                        unlink($originalpath.$image);
                    }
                    if(file_exists($compressedpath.$image)){
                        unlink($compressedpath.$image);
                    }
                
                }
            }
        }
        //END OF REMOVE PROFILE PICTURE OF USER FROM SERVER
    
    if ($picture)
    {
        $no             = rand();
        $imgtype        = explode('.', $picture);
        $ext            = end($imgtype);
        $fullfilename   = $no . '.' . $ext;
        $f              = $no . '.' . $ext;
        $fullpath       = $originalpath . $no . '.' . $ext;
        move_uploaded_file($_FILES['filename']['tmp_name'], $fullpath);
        mysql_query("update USER_DETAILS set ProfilePicture='$fullfilename' where UserID='$user_id'");
        
        $fulloriginalpath           = $host_server."/".$originalpath.$fullfilename;

        $files                      = "http://".$fulloriginalpath;
        //echo $files;exit;

        if(getimagesize($files) !== false)
        {
            //echo "yes";
            reduce_image_size($compressedpath,$f,$files);
        }


    echo "{".json_encode('PLANPIPER_PROFILE_PICTURE').':'.json_encode("1").","
                .json_encode('PLANPIPER_UPLOADED_PICTURE_NAME').':'.json_encode($f)."}";  
    }
    else
    {
    echo "{".json_encode('PLANPIPER_PROFILE_PICTURE').':'.json_encode("0").","
                .json_encode('PLANPIPER_UPLOADED_PICTURE_NAME').':'.json_encode($f)."}";
    }   
}
//*****************************END OF ADD PROFILE PICTURE OF USER****************************************************************************************

//*****************************BACKUP PHONE SETTINGS******************************************
elseif($_REQUEST['RequestType']=="settings_backup" && $_REQUEST['userid']!="")
{
$userid                 = $_REQUEST['userid'];
$type                   = (empty($_REQUEST['Type']))    ? '' : $_REQUEST['Type']; /*tab1(Time Settings) or tab2(Volume Setttings and Volume File Name)*/
    if($userid!="")
    {
        $wake_up                = (empty($_REQUEST['wake_up']))             ? '' : $_REQUEST['wake_up'];
        $morning                = (empty($_REQUEST['morning']))             ? '' : $_REQUEST['morning'];
        $before_breakfast       = (empty($_REQUEST['before_breakfast']))    ? '' : $_REQUEST['before_breakfast'];
        $with_breakfast         = (empty($_REQUEST['with_breakfast']))      ? '' : $_REQUEST['with_breakfast'];
        $after_breakfast        = (empty($_REQUEST['after_breakfast']))     ? '' : $_REQUEST['after_breakfast'];
        $morning_snack          = (empty($_REQUEST['morning_snack']))       ? '' : $_REQUEST['morning_snack'];
        $before_lunch           = (empty($_REQUEST['before_lunch']))        ? '' : $_REQUEST['before_lunch'];
        $with_lunch             = (empty($_REQUEST['with_lunch']))          ? '' : $_REQUEST['with_lunch'];
        $after_lunch            = (empty($_REQUEST['after_lunch']))         ? '' : $_REQUEST['after_lunch'];
        $afternoon              = (empty($_REQUEST['afternoon']))           ? '' : $_REQUEST['afternoon'];
        $evening_snack          = (empty($_REQUEST['evening_snack']))       ? '' : $_REQUEST['evening_snack'];
        $before_tea             = (empty($_REQUEST['before_tea']))          ? '' : $_REQUEST['before_tea'];
        $with_tea               = (empty($_REQUEST['with_tea']))            ? '' : $_REQUEST['with_tea'];
        $after_tea              = (empty($_REQUEST['after_tea']))           ? '' : $_REQUEST['after_tea'];
        $evening                = (empty($_REQUEST['evening']))             ? '' : $_REQUEST['evening'];
        $before_dinner          = (empty($_REQUEST['before_dinner']))       ? '' : $_REQUEST['before_dinner'];
        $with_dinner            = (empty($_REQUEST['with_dinner']))         ? '' : $_REQUEST['with_dinner'];
        $after_dinner           = (empty($_REQUEST['after_dinner']))        ? '' : $_REQUEST['after_dinner'];
        $before_sleeping        = (empty($_REQUEST['before_sleeping']))     ? '' : $_REQUEST['before_sleeping'];

        $normal_volume          = (empty($_REQUEST['normal_volume']))       ? '' : $_REQUEST['normal_volume'];
        $critical_volume        = (empty($_REQUEST['critical_volume']))     ? '' : $_REQUEST['critical_volume'];
        $normal_music_file      = (empty($_REQUEST['normal_music_file']))   ? '' : $_REQUEST['normal_music_file'];
        $critical_music_file    = (empty($_REQUEST['critical_music_file'])) ? '' : $_REQUEST['critical_music_file'];

        $settings_info              = "";

        $check_user_already_exists  = mysql_query("select UserID from USER_PHONE_SETTINGS where UserID='$userid'");
        $check_count                = mysql_num_rows($check_user_already_exists);
        if($check_count)
        {//echo 123;exit;

            $settings_info  .= "update USER_PHONE_SETTINGS set ";

            if($type=="")
            {
            $settings_info  .= " WakeUp='$wake_up',Morning='$morning',BeforeBreakfast='$before_breakfast',
                                WithBreakfast='$with_breakfast',AfterBreakfast='$after_breakfast',MorningSnack='$morning_snack',
                                BeforeLunch='$before_lunch',WithLunch='$with_lunch',AfterLunch='$after_lunch',Afternoon='$afternoon',
                                EveningSnack='$evening_snack',BeforeTea='$before_tea',WithTea='$with_tea',AfterTea='$after_tea',Evening='$evening',
                                BeforeDinner='$before_dinner',WithDinner='$with_dinner',AfterDinner='$after_dinner',BeforeSleeping='$before_sleeping', ";   
            }
            else
            {
            $settings_info  .= " NormalVolume='$normal_volume',CriticalVolume='$critical_volume',NormalMusicFileName='$normal_music_file',
                                CriticalMusicFileName='$critical_music_file', ";    
            }

            $settings_info  .= " UpdatedBy='$userid' where UserID='$userid' ";
        }
        else
        {//echo 456;exit;
            $settings_info  = "insert into USER_PHONE_SETTINGS 
                                (UserID,WakeUp,Morning,BeforeBreakfast,WithBreakfast,AfterBreakfast,MorningSnack,BeforeLunch,WithLunch,AfterLunch,
                                Afternoon,EveningSnack,BeforeTea,WithTea,AfterTea,Evening,BeforeDinner,WithDinner,AfterDinner,BeforeSleeping,
                                NormalVolume,CriticalVolume,NormalMusicFileName,CriticalMusicFileName,CreatedDate,CreatedBy) values 
                                ('$userid','$wake_up','$morning','$before_breakfast','$with_breakfast','$after_breakfast','$morning_snack',
                                '$before_lunch','$with_lunch','$after_lunch','$afternoon','$evening_snack','$before_tea','$with_tea','$after_tea',
                                '$evening','$before_dinner','$with_dinner','$after_dinner','$before_sleeping','$normal_volume',
                                '$critical_volume','$normal_music_file','$critical_music_file',now(),'$userid')";
        }
        //echo $settings_info;exit;
        $settings_info_query= mysql_query($settings_info);
        $check_affected_rows= mysql_affected_rows();
        if($check_affected_rows)
        {   
            echo "{".$paid_status.json_encode('PLANPIPER_SETTINGS_BACKUP').':'.json_encode("1")."}";   
        }
        else
        {
            echo "{".$paid_status.json_encode('PLANPIPER_SETTINGS_BACKUP').':'.json_encode("0")."}";
        }
    }
    else
    {
        echo "{".$paid_status.json_encode('PLANPIPER_SETTINGS_BACKUP').':'.json_encode("0")."}";
    }
}
//*****************************END OF BACKUP PHONE SETTINGS**********************************

//*****************************RESTORE PHONE SETTINGS BACKUP ********************************
elseif($_REQUEST['RequestType']=="settings_backup_restore" && $_REQUEST['userid']!="")
{
    $userid         = (empty($_REQUEST['userid']))              ? '' : $_REQUEST['userid'];
    $plan_list      = array();
    $assigned_plans = array();
    $plan_details   = array();
    $plan           = "";
    $timestamp      = "";
    $timestamp_query= "";
    if($userid)
    {
        /*$get_assigned_plans =   "select t1.PlanCode,t2.DependencyID,
                                IF(t2.DependencyID>0,(select concat(FirstName,' ',LastName) from USER_DEPENDENCIES where DependencyID=t2.DependencyID), '') as Name,
                                t1.MerchantID from PLAN_HEADER as t1,USER_PLAN_MAPPING as t2
                                where t2.UserID='$userid' and t1.PlanCode=t2.PlanCode and t2.Status='A'";
        //echo $get_assigned_plans;exit;
        $get_assigned_plans_qry = mysql_query($get_assigned_plans);
        $get_assigned_plans_count=mysql_num_rows($get_assigned_plans_qry);
            if($get_assigned_plans_count>0)
            {

                $assigned_plans = array();
                while($assigned_plan_rows = mysql_fetch_array($get_assigned_plans_qry))
                {
                    $plancodes       = $assigned_plan_rows['PlanCode'];
                    //echo $plancodes;
                array_push($assigned_plans,$plancodes);
                }
                $plan_list = implode(",",$assigned_plans);
            }
        */


            $get_assigned_plans =   "select t1.PlanCode,t2.DependencyID,
                                    IF(t2.DependencyID>0,(select concat(FirstName,' ',LastName) from USER_DEPENDENCIES where DependencyID=t2.DependencyID), '') as Name,
                                    t1.MerchantID,PlanEndDate
                                    from USER_PLAN_HEADER as t1,USER_PLAN_MAPPING as t2
                                    where t2.UserID='$userid' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode and t2.Status='A' 
                                    ";
            //echo $get_assigned_plans;exit;
            $get_assigned_plans_qry = mysql_query($get_assigned_plans);
            $get_assigned_plans_count=mysql_num_rows($get_assigned_plans_qry);
                if($get_assigned_plans_count>0)
                {
                    $r = array();
                    while($assigned_plan_rows = mysql_fetch_array($get_assigned_plans_qry))
                    {
                        $plan_end_date   = $assigned_plan_rows['PlanEndDate'];
                        if($plan_end_date<date('Y-m-d'))
                        {
                            $plancodes       = $assigned_plan_rows['PlanCode'];
                            array_push($assigned_plans,$plancodes);
                        }
                        else
                        {
                            
                        }
                        
                    }
                    $plan_list = implode(",",$assigned_plans);
                }

        foreach($assigned_plans as $plancode)
        {
            //mysql_query("update USER_PLAN_HEADER set UserStartOrUpdateDateTime='$now' where PlanCode='$plancode' 
                        //and UserID = '$userid'");


            $result             = array();
            $social_media_info  = array();
            //$res5['INDIVIDUAL_PLANS']  = $plancode;
            $get_plan_info  = "select distinct t1.PlanCode,t1.MerchantID,t1.CategoryID,t4.CategoryName,t1.PlanName,t1.PlanDescription,t1.PlanStatus,
                                t1.PlanCurrencyCode,t1.PlanCost,t1.PlanCoverImagePath,t1.CreatedDate,t2.RoleID,t2.Status,
                                t3.CompanyName,t3.CompanyEmailID,t3.CompanyMobileNo,t3.CompanyAddressLine1,t3.CompanyAddressLine2,
                                t3.CompanyPinCode,t5.CityName as CompanyCityName,t6.StateName as CompanyStateName,
                                t7.CountryName as CompanyCountryName
                                from USER_PLAN_HEADER as t1,USER_MERCHANT_MAPPING as t2,MERCHANT_DETAILS as t3,CATEGORY_MASTER as t4,
                                CITY_DETAILS as t5,STATE_DETAILS as t6,COUNTRY_DETAILS as t7
                                where t1.MerchantID=t2.MerchantID and t2.MerchantID=t3.MerchantID and t2.UserID='$userid' 
                                and t1.CategoryID=t4.CategoryID and t1.PlanCode='$plancode' and t3.CompanyCountryCode=t7.CountryCode 
                                and t3.CompanyStateID=t6.StateID and t3.CompanyCityID=t5.CityID and t2.Status='A' 
                                and t1.UserID='$userid' and t2.RoleID=5";
            //echo $get_plan_info;exit;
            $get_plan_info_qry= mysql_query($get_plan_info);
            $get_count       = mysql_num_rows($get_plan_info_qry);
            if($get_count)
            {
            $plan_info          = array();
            $social_media_info  = array();
            $analytics          = array();
                while($plan_info_rows = mysql_fetch_array($get_plan_info_qry))
                { 
                    $pcode                      = (empty($plan_info_rows['PlanCode']))              ? '' : $plan_info_rows['PlanCode'];
                    $res['PlanCode']            = $pcode;
                    $res['CategoryID']          = (empty($plan_info_rows['CategoryID']))            ? '' : $plan_info_rows['CategoryID'];
                    $res['CategoryName']        = (empty($plan_info_rows['CategoryName']))          ? '' : $plan_info_rows['CategoryName'];
                    $res['PlanName']            = (empty($plan_info_rows['PlanName']))              ? '' : $plan_info_rows['PlanName'];
                    $res['PlanDescription']     = (empty($plan_info_rows['PlanDescription']))       ? '' : $plan_info_rows['PlanDescription'];
                    $plan_cover_image_name      = (empty($plan_info_rows['PlanCoverImagePath']))    ? '' : $plan_info_rows['PlanCoverImagePath'];                   
                    $files                      = "http://".$host_server.'/'.$plan_header.$plan_cover_image_name;

                    /*
                        if(getimagesize($files) !== false)
                        {
                            //echo "yes";
                            reduce_image_size($reduced_plan_header,$plan_cover_image_name,$files);
                        }
                        
                    $res['PlanCoverImagePath']  = $host_server.'/'.$reduced_plan_header.$plan_info_rows['PlanCoverImagePath'];
                    */

                    $res['PlanCoverImagePath']  = $host_server.'/'.$plan_header.$plan_info_rows['PlanCoverImagePath'];

                    $merchant_id                = (empty($plan_info_rows['MerchantID']))            ? '' : $plan_info_rows['MerchantID'];
                    $res['MerchantID']          = $merchant_id;
                    $res['CompanyName']         = (empty($plan_info_rows['CompanyName']))           ? '' : $plan_info_rows['CompanyName'];
                    $res['CompanyEmailID']      = (empty($plan_info_rows['CompanyEmailID']))        ? '' : $plan_info_rows['CompanyEmailID'];
                    $country_code               = (empty($plan_info_rows['CompanyCountryCode']))    ? '' : '+'.ltrim($plan_info_rows['CompanyCountryCode'],0);
                    $res['CompanyMobileNo']     = (empty($plan_info_rows['CompanyMobileNo']))       ? '' : $country_code.$plan_info_rows['CompanyMobileNo'];

                    $addressline1               = (empty($plan_info_rows['CompanyAddressLine1']))   ? '' : $plan_info_rows['CompanyAddressLine1'];
                    $addressline2               = (empty($plan_info_rows['CompanyAddressLine2']))   ? '' : $plan_info_rows['CompanyAddressLine2'];
                    $pincode                    = (empty($plan_info_rows['CompanyPinCode']))        ? '' : $plan_info_rows['CompanyPinCode'];

                    $cityname                   = (empty($plan_info_rows['CompanyCityName']))       ? '' : $plan_info_rows['CompanyCityName'];

                    $statename                  = (empty($plan_info_rows['CompanyStateName']))      ? '' : $plan_info_rows['CompanyStateName'];

                    $countryname                = (empty($plan_info_rows['CompanyCountryName']))    ? '' : $plan_info_rows['CompanyCountryName'];

                    $res['CompanyAddressLine1'] = $addressline1;
                    $res['CompanyAddressLine2'] = $addressline2;
                    $res['CompanyPinCode']      = $pincode;
                    $res['CompanyCityName']     = $cityname;
                    $res['CompanyStateName']    = $statename;
                    $res['CompanyCountryName']  = $countryname;

                    $addressline1       = (empty($addressline1))    ? '' : $addressline1;
                    $addressline2       = (empty($addressline2))    ? '' : ', '.$addressline2;
                    $pincode            = (empty($pincode))         ? '' : ', '.$pincode;
                    $cityname           = (empty($cityname))        ? '' : ', '.$cityname;
                    $statename          = (empty($statename))       ? '' : ', '.$statename;
                    $countryname        = (empty($countryname))     ? '' : ', '.$countryname;

                    $res['CompanyFullAddress']  = $addressline1.$addressline2.$pincode.$cityname.$statename.$countryname;

                array_push($plan_info,$res);
                }

                 //Social Media Information
                $get_social_media       = "select MerchantID,SocialMediaName,SocialMediaLink from MERCHANT_SOCIAL_MEDIA 
                                            where MerchantID='$merchant_id'";
                //echo $get_social_media;exit;
                $get_social_media_query = mysql_query($get_social_media);
                $count_of_social_media  = mysql_num_rows($get_social_media_query);
                if($count_of_social_media > 0)
                {
                    while($media = mysql_fetch_array($get_social_media_query))
                    {
                        $r['MerchantID']        = $merchant_id;
                        $r['SocialMediaName']   = $media['SocialMediaName'];
                        $r['SocialMediaLink']   = $media['SocialMediaLink'];
                    array_push($social_media_info,$r);
                    }
                }

                /*CODE TO CHECK WHETHER PLAN WAS SYNCED EARLIER OR NOT*/
                $edit   = mysql_fetch_array(mysql_query("select UserStartOrUpdateDateTime from USER_PLAN_HEADER where UserID='$userid' and PlanCode='$pcode' and MerchantID='$merchant_id'"));
                $plan_synced_date =  $edit['UserStartOrUpdateDateTime'];
                //echo $plan_synced_date;exit;
               /*END OF CODE TO CHECK WHETHER PLAN WAS SYNCED EARLIER OR NOT*/

                 /*GET ANALYTICS(if plan was synced earlier then there may be analytics data that was entered which is to be returned in case user lost his phone)*/
                if($plan_synced_date!=NULL && $plan_synced_date!='0000-00-00 00:00:00')
                {
                $get_analytics = "select '1' as SectionID, t1.PrescriptionNo as PrescriptionNo_SelfTestID,t1.RowNo,Date(t1.DateTime) as Date,
                    Time(t1.DateTime) as Time,t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,
                    t2.MedicineName as MedName_TestName,t3.MerchantID 
                    from USER_MEDICATION_DATA_FROM_CLIENT as t1,USER_MEDICATION_DETAILS as t2,USER_PLAN_HEADER as t3 
                    where t1.PlanCode='$pcode' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                    and t1.UserID=t2.UserID and t1.PlanCode=t3.PlanCode and t1.UserID=t3.UserID 
                    union
                    select '6' as SectionID, t1.PrescriptionNo,t1.RowNo,Date(t1.DateTime) as Date,Time(t1.DateTime) as Time,
                    t1.DateTime,convert(t1.ResponseRequiredStatus,char) as Value,t2.MedicineName as MedName_TestName,
                    t3.MerchantID 
                    from USER_INSTRUCTION_DATA_FROM_CLIENT as t1,USER_INSTRUCTION_DETAILS as t2,USER_PLAN_HEADER as t3 
                    where t1.PlanCode='$pcode' and t1.UserID='$userid' and t1.PlanCode=t2.PlanCode 
                    and t1.UserID=t2.UserID and t1.PlanCode=t3.PlanCode and t1.UserID=t3.UserID 
                    union
                    select '3-1' as SectionID, t7.SelfTestID,t7.RowNo,Date(t7.DateTime) as Date,Time(t7.DateTime) as Time,
                    t7.DateTime,convert(t7.ResponseDataValue,char) as Value, t7.ResponseDataName,t9.MerchantID 
                    from USER_SELF_TEST_DATA_FROM_CLIENT as t7,USER_SELF_TEST_DETAILS as t8,USER_PLAN_HEADER as t9 
                    where t7.PlanCode='$pcode' and t7.UserID='$userid' and t7.PlanCode=t8.PlanCode 
                    and t7.UserID=t8.UserID and t7.PlanCode=t9.PlanCode and t7.UserID=t9.UserID order by DateTime";
                //echo $get_analytics;exit;
                $get_analytics_query = mysql_query($get_analytics);
                $get_analytics_count = mysql_num_rows($get_analytics_query);
                    if($get_analytics_count > 0)
                    {
                        while($analytics_row = mysql_fetch_array($get_analytics_query))
                        {
                            $a['MerchantID']                = $merchant_id;
                            $a['PlanCode']                  = $pcode;
                            $a['UserID']                    = $userid;
                            $a['SectionID']                 = $analytics_row['SectionID'];
                            $a['PrescriptionNo_SelfTestID'] = (empty($analytics_row['PrescriptionNo_SelfTestID']))  ? '' : trim($analytics_row['PrescriptionNo_SelfTestID']);
                            $a['RowNo']                     = (empty($analytics_row['RowNo']))                      ? '' : trim($analytics_row['RowNo']);
                            $a['MedName_TestName']          = (empty($analytics_row['MedName_TestName']))           ? '' : trim($analytics_row['MedName_TestName']);
                            $a['Date']                      = (empty($analytics_row['Date']))                       ? '' : trim($analytics_row['Date']);
                            $a['Time']                      = (empty($analytics_row['Time']))                       ? '' : trim($analytics_row['Time']);
                            $a['DateTime']                  = (empty($analytics_row['DateTime']))                   ? '' : trim($analytics_row['DateTime']);
                            $a['Value']                     = (empty($analytics_row['Value']))                      ? '' : trim($analytics_row['Value']);
                        array_push($analytics,$a);
                        }
                    }
                }
                /*END OF GET ANALYTICS*/

                //GET MEDICATION DETAILS
                $get_medication = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName,t2.MedicineCount,t2.MedicineTypeID,t4.MedicineType,t4.Action,t2.RowNo,t2.SpecificTime,t3.ShortHand,t2.Instruction,t2.When,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString,t2.Status,t2.Link,t2.CreatedDate
                                    from USER_MEDICATION_HEADER as t1,USER_MEDICATION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3,
                                    MEDICINE_TYPES as t4
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t2.MedicineTypeID=t4.SNo and t1.UserID = '$userid'";
                                    //$timestamp_query
               //echo $get_medication;exit;
                $get_medication_qry     = mysql_query($get_medication);
                $get_medication_count   = mysql_num_rows($get_medication_qry);
                if($get_medication_count)
                {
                    while($medication_rows=mysql_fetch_array($get_medication_qry))
                    {
                        $wheninput                      = $medication_rows['When'];
                        if($wheninput == '16'){
                            $specifictimebundle           = (empty($medication_rows['SpecificTime']))             ? '' : $medication_rows['SpecificTime'];
                            $starray = array();
                            $starray = explode(",",$specifictimebundle);
                            foreach ($starray as $st) {
                       if($st != ""){
                        $st = date('H:i:s',strtotime($st));
                        $res1['SectionID']              = '1';
                        $res1['PrescriptionNo']         = (empty($medication_rows['PrescriptionNo']))           ? '' : $medication_rows['PrescriptionNo'];
                        $res1['PrescriptionName']       = (empty($medication_rows['PrescriptionName']))         ? '' : $medication_rows['PrescriptionName'];
                        $res1['DoctorsName']            = (empty($medication_rows['DoctorsName']))              ? '' : $medication_rows['DoctorsName'];
                        $res1['MedicineName']           = (empty($medication_rows['MedicineName']))             ? '' : $medication_rows['MedicineName'];
                        $medication_count               = (empty($medication_rows['MedicineCount']))            ? '' : $medication_rows['MedicineCount'];
                        $medicinetypeid                 = (empty($medication_rows['MedicineTypeID']))           ? '' : $medication_rows['MedicineTypeID'];
                        if($medication_count<=1 || $medicinetypeid==4 || $medicinetypeid==5)
                        {
                            $text = "";
                        }
                        else
                        {
                            $text = "s";
                        }
                        //echo 123;
                        $res1['MedicationCount']        = $medication_count;
                        $res1['MedicationType']         = (empty($medication_rows['MedicineType']))             ? '' : $medication_rows['MedicineType'].$text;
                        $res1['ActionText']             = (empty($medication_rows['Action']))                   ? '' : $medication_rows['Action'];
                        $res1['When']                   = (empty($medication_rows['ShortHand']))                ? '' : $medication_rows['ShortHand'];
                        $res1['RowNo']                  = (empty($medication_rows['RowNo']))                    ? '' : $medication_rows['RowNo'];
                        $res1['Instruction']            = (empty($medication_rows['Instruction']))              ? '' : $medication_rows['Instruction'];
                        if($res1['Instruction'] == "NA"){
                            $res1['Instruction'] = "With Food";
                        }
                        //$res1['Instruction']            = ("NA") ? 'With Food' : $medication_rows['Instruction'];
                         //echo $res1['Instruction'];exit;
                        $res1['Frequency']              = (empty($medication_rows['Frequency']))                ? '' : $medication_rows['Frequency'];
                        $res1['FrequencyString']        = (empty($medication_rows['FrequencyString']))          ? '' : $medication_rows['FrequencyString'];
                        $howlong                        = (empty($medication_rows['HowLong']))                  ? '' : $medication_rows['HowLong'];
                        $howlongtype                    = (empty($medication_rows['HowLongType']))              ? '' : $medication_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res1['HowLong']                = $howlong;
                        $res1['HowLongType']            = $howlongtype;
                        $res1['IsCritical']             = (empty($medication_rows['IsCritical']))               ? '' : $medication_rows['IsCritical'];
                        $res1['ResponseRequired']       = (empty($medication_rows['ResponseRequired']))         ? '' : $medication_rows['ResponseRequired'];
                        $res1['StartFlag']              = (empty($medication_rows['StartFlag']))                ? '' : $medication_rows['StartFlag'];
                        $res1['NoOfDaysAfterPlanStarts']= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '' : $medication_rows['NoOfDaysAfterPlanStarts'];
                        $res1['SpecificDate']           = (empty($medication_rows['SpecificDate']))             ? '' : $medication_rows['SpecificDate'];
                        $res1['SpecificTime']           = $st;
                        $res1['Status']                 = (empty($medication_rows['Status']))                   ? '' : $medication_rows['Status'];
                        $res1['Link']                   = (empty($medication_rows['Link']))                     ? '' : $medication_rows['Link'];
                        $res1['AppointmentDate']        = "";   $res1['AppointmentTime']        = "";   $res1['AppointmentShortName']   = "";
                        $res1['AppointmentRequirements']= "";   $res1['LabTestDate']            = "";   $res1['LabTestTime']            = "";
                        $res1['TestName']               = "";   $res1['SelfTestID']             = "";   $res1['TestDescription']        = "";   
                        $res1['DietNo']                 = "";   $res1['DietPlanName']           = "";   $res1['AdvisorName']            = "";   
                        $res1['DietDurationDays']       = "";   $res1['DayNo']                  = "";   $res1['MealID']                 = "";   
                        $res1['MealDescription']        = "";   $res1['ExercisePlanNo']         = "";   $res1['ExercisePlanName']       = "";  
                        $res1['ExerciseDurationDays']   = "";   $res1['ExerciseSNo']            = "";   $res1['ExerciseDescription']    = "";   
                        $res1['ExerciseInstruction']    = "";   $res1['ExerciseNoOfReps']       = "";   $res1['ExerciseDuration']       = "";   
                        $res1['LabTestID']              = "";   $res1['LabTestRequirements']    = "";
                        $res1['AppointmentDuration']    = "";   $res1['AppointmentPlace']          = "";
                         $res1['SpecialType'] = "";
                        $res1['SpecialDuration'] = "";         
                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res1);
                        }}
                            }
                        } else {
                        $res1['SectionID']              = '1';
                        $res1['PrescriptionNo']         = (empty($medication_rows['PrescriptionNo']))           ? '' : $medication_rows['PrescriptionNo'];
                        $res1['PrescriptionName']       = (empty($medication_rows['PrescriptionName']))         ? '' : $medication_rows['PrescriptionName'];
                        $res1['DoctorsName']            = (empty($medication_rows['DoctorsName']))              ? '' : $medication_rows['DoctorsName'];
                        $res1['MedicineName']           = (empty($medication_rows['MedicineName']))             ? '' : $medication_rows['MedicineName'];
                        $medication_count               = (empty($medication_rows['MedicineCount']))            ? '' : $medication_rows['MedicineCount'];
                        $medicinetypeid                 = (empty($medication_rows['MedicineTypeID']))           ? '' : $medication_rows['MedicineTypeID'];
                        if($medication_count<=1 || $medicinetypeid==4 || $medicinetypeid==5)
                        {
                            $text = "";
                        }
                        else
                        {
                            $text = "s";
                        }
                        //echo 123;
                        $res1['MedicationCount']        = $medication_count;
                        $res1['MedicationType']         = (empty($medication_rows['MedicineType']))             ? '' : $medication_rows['MedicineType'].$text;
                        $res1['ActionText']             = (empty($medication_rows['Action']))                   ? '' : $medication_rows['Action'];
                        $res1['When']                   = (empty($medication_rows['ShortHand']))                ? '' : $medication_rows['ShortHand'];
                        $res1['RowNo']                  = (empty($medication_rows['RowNo']))                    ? '' : $medication_rows['RowNo'];
                        $res1['Instruction']            = (empty($medication_rows['Instruction']))              ? '' : $medication_rows['Instruction'];
                        if($res1['Instruction'] == "NA"){
                            $res1['Instruction'] = "With Food";
                        }
                        //$res1['Instruction']            = ("NA") ? 'With Food' : $medication_rows['Instruction'];
                         //echo $res1['Instruction'];exit;
                        $res1['Frequency']              = (empty($medication_rows['Frequency']))                ? '' : $medication_rows['Frequency'];
                        $res1['FrequencyString']        = (empty($medication_rows['FrequencyString']))          ? '' : $medication_rows['FrequencyString'];
                        $howlong                        = (empty($medication_rows['HowLong']))                  ? '' : $medication_rows['HowLong'];
                        $howlongtype                    = (empty($medication_rows['HowLongType']))              ? '' : $medication_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res1['HowLong']                = $howlong;
                        $res1['HowLongType']            = $howlongtype;
                        $res1['IsCritical']             = (empty($medication_rows['IsCritical']))               ? '' : $medication_rows['IsCritical'];
                        $res1['ResponseRequired']       = (empty($medication_rows['ResponseRequired']))         ? '' : $medication_rows['ResponseRequired'];
                        $res1['StartFlag']              = (empty($medication_rows['StartFlag']))                ? '' : $medication_rows['StartFlag'];
                        $res1['NoOfDaysAfterPlanStarts']= (empty($medication_rows['NoOfDaysAfterPlanStarts']))  ? '' : $medication_rows['NoOfDaysAfterPlanStarts'];
                        $res1['SpecificDate']           = (empty($medication_rows['SpecificDate']))             ? '' : $medication_rows['SpecificDate'];
                        $res1['SpecificTime']           = (empty($medication_rows['SpecificTime']))             ? '' : $medication_rows['SpecificTime'];
                        $res1['Status']                 = (empty($medication_rows['Status']))                   ? '' : $medication_rows['Status'];
                        $res1['Link']                   = (empty($medication_rows['Link']))                     ? '' : $medication_rows['Link'];
                        $res1['AppointmentDate']        = "";   $res1['AppointmentTime']        = "";   $res1['AppointmentShortName']   = "";
                        $res1['AppointmentRequirements']= "";   $res1['LabTestDate']            = "";   $res1['LabTestTime']            = "";
                        $res1['TestName']               = "";   $res1['SelfTestID']             = "";   $res1['TestDescription']        = "";   
                        $res1['DietNo']                 = "";   $res1['DietPlanName']           = "";   $res1['AdvisorName']            = "";   
                        $res1['DietDurationDays']       = "";   $res1['DayNo']                  = "";   $res1['MealID']                 = "";   
                        $res1['MealDescription']        = "";   $res1['ExercisePlanNo']         = "";   $res1['ExercisePlanName']       = "";  
                        $res1['ExerciseDurationDays']   = "";   $res1['ExerciseSNo']            = "";   $res1['ExerciseDescription']    = "";   
                        $res1['ExerciseInstruction']    = "";   $res1['ExerciseNoOfReps']       = "";   $res1['ExerciseDuration']       = "";   
                        $res1['LabTestID']              = "";   $res1['LabTestRequirements']    = "";
                        $res1['AppointmentDuration']    = "";   $res1['AppointmentPlace']       = ""; 
                         $res1['SpecialType'] = "";
                        $res1['SpecialDuration'] = "";
                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res1);
                        }
                        }
                    //exit;
                        /*If Howlong ie; duration is 0 then dont send*/

                        /*End of Checking value of Howlong*/
                    //array_push($result,$res1);
                    }
                }
                //END OF GET MEDICATION DETAILS

                 //GET APPOINTMENT DETAILS
                    $get_appointment= "select distinct t2.AppointmentDate,t2.AppointmentTime,t2.AppointmentShortName,t2.DoctorsName, t2.AppointmentDuration, t2.AppointmentPlace,
                                        t2.AppointmentRequirements, t2.Status,t2.CreatedDate
                                      from USER_APPOINTMENT_HEADER as t1,USER_APPOINTMENT_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.PlanCode='$plancode' and t1.UserID = t2.UserID 
                                      and t1.UserID = '$userid'";
                    //echo $get_appointment.'<br>';
                    $get_appointment_qry    = mysql_query($get_appointment);
                    $get_appointment_count  = mysql_num_rows($get_appointment_qry);
                    if($get_appointment_count)
                    {
                        while($appointment_rows = mysql_fetch_array($get_appointment_qry))
                        {
                            $res2['SectionID']              = '2';
                            $res2['AppointmentDate']        = (empty($appointment_rows['AppointmentDate']))         ? '' : $appointment_rows['AppointmentDate'];
                            $res2['AppointmentTime']        = (empty($appointment_rows['AppointmentTime']))         ? '' : $appointment_rows['AppointmentTime'];
                            $res2['AppointmentShortName']   = (empty($appointment_rows['AppointmentShortName']))    ? '' : $appointment_rows['AppointmentShortName'];
                            $res2['DoctorsName']            = (empty($appointment_rows['DoctorsName']))             ? '' : $appointment_rows['DoctorsName'];
                            $res2['AppointmentRequirements']= (empty($appointment_rows['AppointmentRequirements'])) ? '' : $appointment_rows['AppointmentRequirements']; 
                            $res2['Status']                 = (empty($appointment_rows['Status']))                  ? '' : $appointment_rows['Status'];
                            $res2['AppointmentDuration']                 = (empty($appointment_rows['AppointmentDuration']))                  ? '' : $appointment_rows['AppointmentDuration'];
                            $res2['AppointmentDuration'] = substr($res2['AppointmentDuration'], 0, 5);
                            $res2['AppointmentPlace']                 = (empty($appointment_rows['AppointmentPlace']))                  ? '' : $appointment_rows['AppointmentPlace'];
                            $res2['PrescriptionNo']         = "";   $res2['PrescriptionName']       = "";       $res2['MedicineName']           = "";
                            $res2['When']                   = "";   $res2['Instruction']            = "";       $res2['Frequency']              = "";
                            $res2['FrequencyString']        = "";   $res2['HowLong']                = "";       $res2['HowLongType']            = "";
                            $res2['IsCritical']             = "";   $res2['ResponseRequired']       = "";       $res2['StartFlag']              = "";
                            $res2['NoOfDaysAfterPlanStarts']= "";   $res2['SpecificDate']           = "";       $res2['LabTestDate']            = "";
                            $res2['LabTestTime']            = "";   $res2['TestName']               = "";       $res2['SelfTestID']             = "";
                            $res2['RowNo']                  = "";   $res2['TestDescription']        = "";       $res2['DietNo']                 = "";
                            $res2['DietPlanName']           = "";   $res2['AdvisorName']            = "";       $res2['DietDurationDays']       = "";
                            $res2['DayNo']                  = "";   $res2['MealID']                 = "";       $res2['MealDescription']        = "";
                            $res2['SpecificTime']           = "";   $res2['ExercisePlanNo']         = "";       $res2['ExercisePlanName']       = "";
                            $res2['ExerciseDurationDays']   = "";   $res2['ExerciseSNo']            = "";       $res2['ExerciseDescription']    = "";
                            $res2['ExerciseInstruction']    = "";   $res2['ExerciseNoOfReps']       = "";       $res2['ExerciseDuration']       = "";
                            $res2['Link']                   = "";   $res2['LabTestID']              = "";       $res2['LabTestRequirements']    = "";
                             $res2['SpecialType'] = "";
                        $res2['SpecialDuration'] = "";
                        array_push($result,$res2);
                        }
                    }
                    //END OF APPOINTMENT DETAILS

                    //SELF TEST DETAILS
                    $get_self_test = "select distinct t1.SelfTestID,t2.RowNo,t2.MedicalTestID,t2.TestName,t2.DoctorsName,t2.TestDescription,t2.InstructionID,
                                      t2.Frequency,t2.HowLong,t2.HowLongType,t2.ResponseRequired,t2.StartFlag,t2.NoOfDaysAfterPlanStarts,
                                      t2.SpecificDate,t2.FrequencyString,t2.Status,t2.Link,t2.CreatedDate
                                      from USER_SELF_TEST_HEADER as t1,USER_SELF_TEST_DETAILS as t2
                                      where t1.PlanCode=t2.PlanCode and t1.SelfTestID=t2.SelfTestID and t1.PlanCode='$plancode' 
                                      and t1.UserID = t2.UserID and t1.UserID = '$userid'";
                    //echo $get_self_test;exit;
                    $get_self_test_qry  = mysql_query($get_self_test);
                    $get_self_test_count= mysql_num_rows($get_self_test_qry);
                    if($get_self_test_count)
                    {
                        while($self_test_rows=mysql_fetch_array($get_self_test_qry))
                        {
                            $res31['SectionID']                 = '3-1';
                            $res31['SelfTestID']                = (empty($self_test_rows['SelfTestID']))                ? '' : $self_test_rows['SelfTestID'];
                            $res31['RowNo']                     = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
                            $res31['TestName']                  = (empty($self_test_rows['TestName']))                  ? '' : $self_test_rows['TestName'];
                            $MedicalTestID                      = (empty($self_test_rows['MedicalTestID']))                  ? '' : $self_test_rows['MedicalTestID'];
                            if($MedicalTestID == "5"){
                                $res31['SpecialType'] = "P";
                                $res31['SpecialDuration'] = "01:30";
                            } else {
                                $res31['SpecialType'] = "";
                                $res31['SpecialDuration'] = "";
                            }
                            $res31['DoctorsName']               = (empty($self_test_rows['DoctorsName']))               ? '' : $self_test_rows['DoctorsName']; 
                            $res31['TestDescription']           = (empty($self_test_rows['TestDescription']))           ? '' : $self_test_rows['TestDescription'];
                            $res31['Instruction']               = (empty($self_test_rows['InstructionID']))             ? '' : $self_test_rows['InstructionID']; 
                            $res31['Frequency']                 = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
                            $res31['FrequencyString']           = (empty($self_test_rows['FrequencyString']))           ? '' : $self_test_rows['FrequencyString'];
                           
                            $howlong                            = (empty($self_test_rows['HowLong']))                  ? '' : $self_test_rows['HowLong'];
                            $howlongtype                        = (empty($self_test_rows['HowLongType']))              ? '' : $self_test_rows['HowLongType'];

                            if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                            {
                            //echo 123;exit;
                            $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                            $howlongtype        = "Days";
                            }

                            $res31['HowLong']                   = $howlong;
                            $res31['HowLongType']               = $howlongtype;

                            $res31['ResponseRequired']          = (empty($self_test_rows['ResponseRequired']))          ? '' : $self_test_rows['ResponseRequired'];
                            $res31['StartFlag']                 = (empty($self_test_rows['StartFlag']))                 ? '' : $self_test_rows['StartFlag']; 
                            $res31['NoOfDaysAfterPlanStarts']   = (empty($self_test_rows['NoOfDaysAfterPlanStarts']))   ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
                            $res31['SpecificDate']              = (empty($self_test_rows['SpecificDate']))              ? '' : $self_test_rows['SpecificDate'];
                            $res31['Status']                    = (empty($self_test_rows['Status']))                    ? '' : $self_test_rows['Status'];
                            $res31['Link']                      = (empty($self_test_rows['Link']))                      ? '' : $self_test_rows['Link'];
                            $res31['PrescriptionNo']            = "";   $res31['PrescriptionName']          = "";   $res31['MedicineName']              = "";
                            $res31['When']                      = "";   $res31['IsCritical']                = "";   $res31['AppointmentDate']           = "";
                            $res31['AppointmentTime']           = "";   $res31['AppointmentShortName']      = "";   $res31['AppointmentRequirements']   = "";
                            $res31['LabTestDate']               = "";   $res31['LabTestTime']               = "";   $res31['DietNo']                    = "";
                            $res31['DietPlanName']              = "";   $res31['AdvisorName']               = "";   $res31['DietDurationDays']          = "";
                            $res31['DayNo']                     = "";   $res31['MealID']                    = "";   $res31['MealDescription']           = "";
                            $res31['SpecificTime']              = "";   $res31['ExercisePlanNo']            = "";   $res31['ExercisePlanName']          = "";
                            $res31['ExerciseDurationDays']      = "";   $res31['ExerciseSNo']               = "";   $res31['ExerciseDescription']       = "";
                            $res31['ExerciseInstruction']       = "";   $res31['ExerciseNoOfReps']          = "";   $res31['ExerciseDuration']          = "";
                            $res31['LabTestID']                 = "";   $res31['LabTestRequirements']       = "";
                            $res31['AppointmentDuration']       = "";   $res31['AppointmentPlace']          = ""; 
                        array_push($result,$res31);
                             if($MedicalTestID == "5"){
                                //POSTPRANDIAL INSTRUCTION
                                //$st = date('H:i:s',strtotime($st));
                                $res8['SectionID']              = '8';
                                $res8['PrescriptionNo']         = (empty($self_test_rows['SelfTestID']))                     ? '' : $self_test_rows['SelfTestID']; 
                                $res8['PrescriptionName']       = "Diet Instruction";
                                $res8['DoctorsName']            = (empty($self_test_rows['DoctorsName']))               ? '' : stripslashes($self_test_rows['DoctorsName']);
                                
                                if($self_test_rows['InstructionID'] == "5"){
                                     $res8['When']                   = "1-0-0-0";
                                     $res8['MedicineName']           = "Have a nutritious Breakfast";
                                } else if($self_test_rows['InstructionID'] == "9"){
                                     $res8['When']                   = "0-1-0-0";
                                     $res8['MedicineName']           = "Have a nutritious Lunch";
                                }
                                 else if($self_test_rows['InstructionID'] == "18"){
                                     $res8['When']                   = "0-0-1-0";
                                     $res8['MedicineName']           = "Have a nutritious Dinner";
                                } else {
                                    $res8['When']                   = "1-0-0-0";
                                    $res8['MedicineName']           = "Have a nutritious meal";
                                }
                                $res8['RowNo']                  = (empty($self_test_rows['RowNo']))                     ? '' : $self_test_rows['RowNo']; 
                                $res8['Instruction']            = "With Food"; 
                                $res8['Frequency']              = (empty($self_test_rows['Frequency']))                 ? '' : $self_test_rows['Frequency'];
                                $res8['ActionText']             = "Diet";
                                $res8['FrequencyString']        = (empty($self_test_rows['FrequencyString']))                 ? '' : $self_test_rows['FrequencyString'];
                                $howlong                        = (empty($self_test_rows['HowLong']))                  ? '' : $self_test_rows['HowLong'];
                                $howlongtype                    = (empty($self_test_rows['HowLongType']))              ? '' : $self_test_rows['HowLongType'];

                                if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                                {
                                //echo 123;exit;
                                $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                                $howlongtype        = "Days";
                                }

                                $res8['HowLong']                = $howlong;
                                $res8['HowLongType']            = $howlongtype;
                                $res8['IsCritical']             = "N";
                                $res8['ResponseRequired']       = "N";
                                $res8['StartFlag']              = (empty($self_test_rows['StartFlag']))                ? '' : $self_test_rows['StartFlag'];
                                $res8['NoOfDaysAfterPlanStarts']= (empty($self_test_rows['NoOfDaysAfterPlanStarts']))  ? '' : $self_test_rows['NoOfDaysAfterPlanStarts'];
                                $res8['SpecificDate']           = (empty($self_test_rows['SpecificDate']))             ? '' : $self_test_rows['SpecificDate'];
                                $res8['SpecificTime']           = "";
                                $res8['Status']                 = (empty($self_test_rows['Status']))                   ? '' : $self_test_rows['Status'];
                                $res8['Link']                   = (empty($self_test_rows['Link']))                     ? '' : $self_test_rows['Link'];
                                $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                                $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                                $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                                $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                                $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                                $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                                $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                                $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                                $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                                $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = "";          
                                 $res8['SpecialType'] = "";
                                $res8['SpecialDuration'] = "";
                                /*if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                                {

                                }
                                else
                                {
                                    array_push($result,$res8);
                                }*/
                            array_push($result,$res8);
                            }
                        }   
                    }
                    //END OF SELF TEST DETAILS

                    //GET LAB TEST DETAILS
                    $get_lab_test = "select distinct t1.LabTestID,t2.RowNo,t2.TestName,t2.DoctorsName,t2.LabTestRequirements,
                                    t2.LabTestDate,t2.LabTestTime,t2.Status,t2.CreatedDate
                                    from USER_LAB_TEST_HEADER1 as t1,USER_LAB_TEST_DETAILS1 as t2
                                    where t1.PlanCode=t2.PlanCode and t1.LabTestID=t2.LabTestID and t1.PlanCode='$plancode' 
                                    and t1.UserID = t2.UserID and t1.UserID = '$userid' ";
                    $get_lab_test_qry  = mysql_query($get_lab_test);
                    $get_lab_test_count= mysql_num_rows($get_lab_test_qry);
                    if($get_lab_test_count)
                    {
                        while($lab_test_rows=mysql_fetch_array($get_lab_test_qry))
                        {
                            $res32['SectionID']                 = '3-2';
                            $res32['LabTestID']                 = (empty($lab_test_rows['LabTestID']))                  ? '' : $lab_test_rows['LabTestID'];
                            $res32['RowNo']                     = (empty($lab_test_rows['RowNo']))                      ? '' : $lab_test_rows['RowNo']; 
                            $res32['TestName']                  = (empty($lab_test_rows['TestName']))                   ? '' : $lab_test_rows['TestName'];
                            $res32['DoctorsName']               = (empty($lab_test_rows['DoctorsName']))                ? '' : $lab_test_rows['DoctorsName']; 
                            $res32['LabTestRequirements']       = (empty($lab_test_rows['LabTestRequirements']))        ? '' : $lab_test_rows['LabTestRequirements'];
                            $labtest_date                       = (empty($lab_test_rows['LabTestDate']))                ? '' : $lab_test_rows['LabTestDate']; ;
                            $labtest_time                       = (empty($lab_test_rows['LabTestTime']))                ? '' : $lab_test_rows['LabTestTime'];;
                            $res32['LabTestDate']               = $labtest_date;
                            $res32['LabTestTime']               = $labtest_time;
                            $res32['Status']                    = (empty($lab_test_rows['Status']))                     ? '' : $lab_test_rows['Status'];
                            $res32['PrescriptionNo']            = "";   $res32['PrescriptionName']          = "";   $res32['MedicineName']              = "";
                            $res32['When']                      = "";   $res32['HowLong']                   = "";   $res32['HowLongType']               = "";
                            $res32['IsCritical']                = "";   $res32['StartFlag']                 = "";   $res32['NoOfDaysAfterPlanStarts']   = "";
                            $res32['SpecificDate']              = "";   $res32['AppointmentDate']           = "";   $res32['AppointmentTime']           = "";
                            $res32['AppointmentShortName']      = "";   $res32['AppointmentRequirements']   = "";   $res32['SelfTestID']                = "";
                            $res32['TestDescription']           = "";   $res32['Frequency']                 = "";   $res32['ResponseRequired']          = "";
                            $res32['FrequencyString']           = "";   $res32['DietNo']                    = "";   $res32['DietPlanName']              = "";
                            $res32['AdvisorName']               = "";   $res32['DietDurationDays']          = "";   $res32['DayNo']                     = "";
                            $res32['MealID']                    = "";   $res32['MealDescription']           = "";   $res32['SpecificTime']              = "";
                            $res32['ExercisePlanNo']            = "";   $res32['ExercisePlanName']          = "";   $res32['ExerciseDurationDays']      = "";
                            $res32['ExerciseSNo']               = "";   $res32['ExerciseDescription']       = "";   $res32['ExerciseInstruction']       = "";
                            $res32['ExerciseNoOfReps']          = "";   $res32['ExerciseDuration']          = "";   $res32['Link']                      = "";
                            $res32['Instruction']               = "";   
                            $res32['AppointmentDuration']       = "";   $res32['AppointmentPlace']          = ""; 
                             $res32['SpecialType'] = "";
                            $res32['SpecialDuration'] = "";
                            if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $labtest_date!="")
                            {

                            }
                            else
                            {
                                array_push($result,$res32);
                            }
                        //array_push($result,$res32);
                        }   
                    }
                    //END OF LAB TEST DETAILS

                    //GET DIET DETAILS
                    $get_diet_detials       = "select distinct t1.DietNo,t1.DietPlanName,t1.AdvisorName,t1.DietDurationDays,t2.DayNo,t2.InstructionID,t2.MealDescription,
                                            t2.SpecificTime,t2.Status,t2.Link,t2.CreatedDate
                                            from USER_DIET_HEADER as t1,USER_DIET_DETAILS as t2
                                            where t1.PlanCode=t2.PlanCode and t1.DietNo=t2.DietNo and t1.PlanCode='$plancode' 
                                            and t1.UserID = t2.UserID and t1.UserID = '$userid' ";
                                            //echo $get_diet_detials;exit;
                    $get_diet_detials_qry   = mysql_query($get_diet_detials);
                    $get_diet_detials_count = mysql_num_rows($get_diet_detials_qry);
                    $dietresult = array();
                    if($get_diet_detials_count)
                    {
                        while($diet_rows=mysql_fetch_array($get_diet_detials_qry))
                        {
                            $res4['SectionID']                  = '4';
                            $res4['DietNo']                     = (empty($diet_rows['DietNo']))             ? '' : $diet_rows['DietNo'];
                            $res4['DietPlanName']               = (empty($diet_rows['DietPlanName']))       ? '' : $diet_rows['DietPlanName'];
                            $res4['AdvisorName']                = (empty($diet_rows['AdvisorName']))        ? '' : $diet_rows['AdvisorName'];
                            $res4['DietDurationDays']           = (empty($diet_rows['DietDurationDays']))   ? '' : $diet_rows['DietDurationDays'];
                            $resd['DayNo']                      = (empty($diet_rows['DayNo']))              ? '' : $diet_rows['DayNo'];
                            $resd['MealID']                     = (empty($diet_rows['InstructionID']))      ? '' : $diet_rows['InstructionID'];
                            $resd['MealDescription']            = (empty($diet_rows['MealDescription']))    ? '' : $diet_rows['MealDescription'];
                            $resd['SpecificTime']               = (empty($diet_rows['SpecificTime']))       ? '' : $diet_rows['SpecificTime'];
                            $res4['Status']                     = (empty($diet_rows['Status']))             ? '' : $diet_rows['Status'];
                            $res4['Link']                       = (empty($diet_rows['Link']))               ? '' : $diet_rows['Link'];
                            array_push($dietresult,$resd);
                            
                        }
                            $res4['plan_info']                  = $dietresult;
                            $res4['LabTestDate']                = "";   $res4['LabTestTime']                = "";   $res4['TestName']                   = "";
                            $res4['DoctorsName']                = "";   $res4['Instruction']                = "";   $res4['ResponseRequired']           = "";
                            $res4['PrescriptionNo']             = "";   $res4['PrescriptionName']           = "";   $res4['MedicineName']               = "";
                            $res4['When']                       = "";   $res4['HowLong']                    = "";   $res4['HowLongType']                = "";
                            $res4['IsCritical']                 = "";   $res4['StartFlag']                  = "";   $res4['NoOfDaysAfterPlanStarts']    = "";
                            $res4['SpecificDate']               = "";   $res4['AppointmentDate']            = "";   $res4['AppointmentTime']            = "";
                            $res4['AppointmentShortName']       = "";   $res4['AppointmentRequirements']    = "";   $res4['SelfTestID']                 = "";
                            $res4['RowNo']                      = "";   $res4['TestDescription']            = "";   $res4['Frequency']                  = "";
                            $res4['FrequencyString']            = "";   $res4['ExercisePlanNo']             = "";   $res4['ExercisePlanName']           = "";
                            $res4['ExerciseDurationDays']       = "";   $res4['ExerciseSNo']                = "";   $res4['ExerciseDescription']        = "";
                            $res4['ExerciseInstruction']        = "";   $res4['ExerciseNoOfReps']           = "";   $res4['ExerciseDuration']           = "";
                            $res4['LabTestID']                  = "";   $res4['LabTestRequirements']        = "";
                            $res4['AppointmentDuration']        = "";   $res4['AppointmentPlace']          = ""; 
                             $res4['SpecialType'] = "";
                            $res4['SpecialDuration'] = "";
                        array_push($result,$res4);
                           
                    }
                    //END OF DIET DETAILS

/*                    //GET EXERCISE DETAILS
                    $get_exercise_detials       = "select distinct t1.ExercisePlanNo,t1.ExercisePlanName,t1.AdvisorName,t1.ExerciseDurationDays,
                                                t2.DayNo,t2.ExerciseSNo,t2.ExerciseDescription,t2.ExerciseInstruction,t2.ExerciseNoOfReps,
                                                t2.ExerciseDuration,t2.Link,t2.CreatedDate
                                                from USER_EXERCISE_HEADER as t1,USER_EXERCISE_DETAILS as t2
                                                where t1.PlanCode=t2.PlanCode and t1.ExercisePlanNo=t2.ExercisePlanNo and t1.PlanCode='$plancode' and t1.UserID = t2.UserID and t1.UserID = '$userid'";
                    $get_exercise_detials_qry   = mysql_query($get_exercise_detials);
                    $get_exercise_detials_count = mysql_num_rows($get_exercise_detials_qry);
                    $exerciserepo = array();
                    if($get_exercise_detials_count)
                    {
                        while($exercise_rows=mysql_fetch_array($get_exercise_detials_qry))
                        {
                            $res5['SectionID']                  = '5';
                            $res5['ExercisePlanNo']             = (empty($exercise_rows['ExercisePlanNo']))         ? '' : $exercise_rows['ExercisePlanNo'];
                            $res5['ExercisePlanName']           = (empty($exercise_rows['ExercisePlanName']))       ? '' : $exercise_rows['ExercisePlanName'];
                            $res5['AdvisorName']                = (empty($exercise_rows['AdvisorName']))            ? '' : $exercise_rows['AdvisorName'];
                            $res5['ExerciseDurationDays']       = (empty($exercise_rows['ExerciseDurationDays']))   ? '' : $exercise_rows['ExerciseDurationDays'];
                            $res6['DayNo']                      = (empty($exercise_rows['DayNo']))                  ? '' : $exercise_rows['DayNo'];
                            $res5['ExerciseSNo']                = (empty($exercise_rows['ExerciseSNo']))            ? '' : $exercise_rows['ExerciseSNo'];
                            $res6['ExerciseDescription']        = (empty($exercise_rows['ExerciseDescription']))    ? '' : $exercise_rows['ExerciseDescription'];
                            $res6['ExerciseInstruction']        = (empty($exercise_rows['ExerciseInstruction']))    ? '' : $exercise_rows['ExerciseInstruction'];
                            $res6['ExerciseNoOfReps']           = (empty($exercise_rows['ExerciseNoOfReps']))       ? '' : $exercise_rows['ExerciseNoOfReps'];
                            $res6['ExerciseDuration']           = (empty($exercise_rows['ExerciseDuration']))       ? '' : $exercise_rows['ExerciseDuration'];
                            $res6['Link']                       = (empty($exercise_rows['Link']))                   ? '' : $exercise_rows['Link'];
                            array_push($exerciserepo,$res6);
                            }
                            $res5['exercise_info']              = $exerciserepo;
                            $res5['DietNo']                     = "";   $res5['DietPlanName']               = "";   $res5['DietDurationDays']           = "";
                            $res5['MealID']                     = "";   $res5['MealDescription']            = "";   $res5['SpecificTime']               = "";
                            $res5['LabTestDate']                = "";   $res5['LabTestTime']                = "";   $res5['TestName']                   = "";
                            $res5['DoctorsName']                = "";   $res5['Instruction']                = "";   $res5['ResponseRequired']           = "";
                            $res5['PrescriptionNo']             = "";   $res5['PrescriptionName']           = "";   $res5['MedicineName']               = "";
                            $res5['When']                       = "";   $res5['HowLong']                    = "";   $res5['HowLongType']                = "";
                            $res5['IsCritical']                 = "";   $res5['StartFlag']                  = "";   $res5['NoOfDaysAfterPlanStarts']    = "";
                            $res5['SpecificDate']               = "";   $res5['AppointmentDate']            = "";   $res5['AppointmentTime']            = "";
                            $res5['AppointmentShortName']       = "";   $res5['AppointmentRequirements']    = "";   $res5['SelfTestID']                 = "";
                            $res5['RowNo']                      = "";   $res5['TestDescription']            = "";   $res5['Frequency']                  = "";
                            $res5['FrequencyString']            = "";   $res5['LabTestID']                  = "";   $res5['LabTestRequirements']        = ""; 
                        array_push($result,$res5);
                    }
                    //END OF EXERCISE DETAILS
*/
                 //GET INSTRUCTION DETAILS
                $get_instruction = "select distinct t1.PrescriptionNo,t1.PrescriptionName,t1.DoctorsName,t2.MedicineName, t4,InstructionType, t2.RowNo,t2.SpecificTime,t3.ShortHand,t2.Instruction,
                                    t2.Frequency,t2.HowLong,t2.HowLongType,t2.IsCritical,t2.ResponseRequired,t2.StartFlag,t2.When,
                                    t2.NoOfDaysAfterPlanStarts,t2.SpecificDate,t2.FrequencyString,t2.Link,t2.CreatedDate
                                    from USER_INSTRUCTION_HEADER as t1,USER_INSTRUCTION_DETAILS as t2,MASTER_DOCTOR_SHORTHAND as t3, INSTRUCTION_TYPE as t4
                                    where t1.PlanCode=t2.PlanCode and t1.PrescriptionNo=t2.PrescriptionNo and t1.PlanCode='$plancode' 
                                    and t2.When=t3.ID and t1.UserID = t2.UserID and t2.InstructionTypeID = t4.InstructionTypeID and t1.UserID = '$userid' ";
                //echo $get_medication;exit;
                $get_instruction_qry     = mysql_query($get_instruction);
                $get_instruction_count   = mysql_num_rows($get_instruction_qry);
                if($get_instruction_count)
                {
                    while($instruction_rows=mysql_fetch_array($get_instruction_qry))
                    {
                        $wheninput                      = $instruction_rows['When'];
                        if($wheninput == '16'){
                            $specifictimebundle           = (empty($instruction_rows['SpecificTime']))             ? '' : $instruction_rows['SpecificTime'];
                            $starray = array();
                            $starray = explode(",",$specifictimebundle);
                            foreach ($starray as $st) {
                       if($st != ""){
                        $st = date('H:i:s',strtotime($st));
                        $res8['SectionID']              = '6';
                        $res8['PrescriptionNo']         = (empty($instruction_rows['PrescriptionNo']))           ? '' : $instruction_rows['PrescriptionNo'];
                        $res8['PrescriptionName']       = (empty($instruction_rows['PrescriptionName']))         ? '' : $instruction_rows['PrescriptionName'];
                        $res8['DoctorsName']            = (empty($instruction_rows['DoctorsName']))              ? '' : $instruction_rows['DoctorsName'];
                        $res8['MedicineName']           = (empty($instruction_rows['MedicineName']))             ? '' : $instruction_rows['MedicineName'];
                        $res8['When']                   = (empty($instruction_rows['ShortHand']))                ? '' : $instruction_rows['ShortHand'];
                        $res8['RowNo']                  = (empty($instruction_rows['RowNo']))                    ? '' : $instruction_rows['RowNo'];
                        $res8['Instruction']            = (empty($instruction_rows['Instruction']))              ? '' : $instruction_rows['Instruction'];
                        if($res8['Instruction'] == "NA"){
                            $res8['Instruction'] = "With Food";
                        }
                        //$res8['Instruction']            = ("NA") ? 'With Food' : $instruction_rows['Instruction'];
                         //echo $res8['Instruction'];exit;
                        $res8['Frequency']              = (empty($instruction_rows['Frequency']))                ? '' : $instruction_rows['Frequency'];
                        $res8['ActionText']             = (empty($instruction_rows['InstructionType']))                ? '' : $instruction_rows['InstructionType'];
                        $res8['FrequencyString']        = (empty($instruction_rows['FrequencyString']))          ? '' : $instruction_rows['FrequencyString'];
                        $howlong                        = (empty($instruction_rows['HowLong']))                  ? '' : $instruction_rows['HowLong'];
                        $howlongtype                    = (empty($instruction_rows['HowLongType']))              ? '' : $instruction_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res8['HowLong']                = $howlong;
                        $res8['HowLongType']            = $howlongtype;
                        $res8['IsCritical']             = (empty($instruction_rows['IsCritical']))               ? '' : $instruction_rows['IsCritical'];
                        $res8['ResponseRequired']       = (empty($instruction_rows['ResponseRequired']))         ? '' : $instruction_rows['ResponseRequired'];
                        $res8['StartFlag']              = (empty($instruction_rows['StartFlag']))                ? '' : $instruction_rows['StartFlag'];
                        $res8['NoOfDaysAfterPlanStarts']= (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '' : $instruction_rows['NoOfDaysAfterPlanStarts'];
                        $res8['SpecificDate']           = (empty($instruction_rows['SpecificDate']))             ? '' : $instruction_rows['SpecificDate'];
                        $res8['SpecificTime']           = $st;
                        $res8['Status']                 = (empty($instruction_rows['Status']))                   ? '' : $instruction_rows['Status'];
                        $res8['Link']                   = (empty($instruction_rows['Link']))                     ? '' : $instruction_rows['Link'];
                        $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                        $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                        $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                        $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                        $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                        $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                        $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                        $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                        $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                        $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = "";
                        $res8['SpecialType']            = "";   $res8['SpecialDuration']        = "";        
                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res8);
                        }}
                            }
                        } else {
                        $res8['SectionID']              = '6';
                        $res8['PrescriptionNo']         = (empty($instruction_rows['PrescriptionNo']))           ? '' : $instruction_rows['PrescriptionNo'];
                        $res8['PrescriptionName']       = (empty($instruction_rows['PrescriptionName']))         ? '' : $instruction_rows['PrescriptionName'];
                        $res8['DoctorsName']            = (empty($instruction_rows['DoctorsName']))              ? '' : $instruction_rows['DoctorsName'];
                        $res8['MedicineName']           = (empty($instruction_rows['MedicineName']))             ? '' : $instruction_rows['MedicineName'];
                        $res8['ActionText']             = (empty($instruction_rows['InstructionType']))                ? '' : $instruction_rows['InstructionType'];
                        $res8['When']                   = (empty($instruction_rows['ShortHand']))                ? '' : $instruction_rows['ShortHand'];
                        $res8['RowNo']                  = (empty($instruction_rows['RowNo']))                    ? '' : $instruction_rows['RowNo'];
                        $res8['Instruction']            = (empty($instruction_rows['Instruction']))              ? '' : $instruction_rows['Instruction'];
                        if($res8['Instruction'] == "NA"){
                            $res8['Instruction'] = "With Food";
                        }
                        //$res8['Instruction']            = ("NA") ? 'With Food' : $instruction_rows['Instruction'];
                         //echo $res8['Instruction'];exit;
                        $res8['Frequency']              = (empty($instruction_rows['Frequency']))                ? '' : $instruction_rows['Frequency'];
                        $res8['FrequencyString']        = (empty($instruction_rows['FrequencyString']))          ? '' : $instruction_rows['FrequencyString'];
                        $howlong                        = (empty($instruction_rows['HowLong']))                  ? '' : $instruction_rows['HowLong'];
                        $howlongtype                    = (empty($instruction_rows['HowLongType']))              ? '' : $instruction_rows['HowLongType'];

                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00')
                        {
                        //echo 123;exit;
                        $howlong            = diff($plan_synced_date,$current_date,$howlong,$howlongtype);
                        $howlongtype        = "Days";
                        }

                        $res8['HowLong']                = $howlong;
                        $res8['HowLongType']            = $howlongtype;
                        $res8['IsCritical']             = (empty($instruction_rows['IsCritical']))               ? '' : $instruction_rows['IsCritical'];
                        $res8['ResponseRequired']       = (empty($instruction_rows['ResponseRequired']))         ? '' : $instruction_rows['ResponseRequired'];
                        $res8['StartFlag']              = (empty($instruction_rows['StartFlag']))                ? '' : $instruction_rows['StartFlag'];
                        $res8['NoOfDaysAfterPlanStarts']= (empty($instruction_rows['NoOfDaysAfterPlanStarts']))  ? '' : $instruction_rows['NoOfDaysAfterPlanStarts'];
                        $res8['SpecificDate']           = (empty($instruction_rows['SpecificDate']))             ? '' : $instruction_rows['SpecificDate'];
                        $res8['SpecificTime']           = (empty($instruction_rows['SpecificTime']))             ? '' : $instruction_rows['SpecificTime'];
                        $res8['Status']                 = (empty($instruction_rows['Status']))                   ? '' : $instruction_rows['Status'];
                        $res8['Link']                   = (empty($instruction_rows['Link']))                     ? '' : $instruction_rows['Link'];
                        $res8['AppointmentDate']        = "";   $res8['AppointmentTime']        = "";   $res8['AppointmentShortName']   = "";
                        $res8['AppointmentRequirements']= "";   $res8['LabTestDate']            = "";   $res8['LabTestTime']            = "";
                        $res8['TestName']               = "";   $res8['SelfTestID']             = "";   $res8['TestDescription']        = "";   
                        $res8['DietNo']                 = "";   $res8['DietPlanName']           = "";   $res8['AdvisorName']            = "";   
                        $res8['DietDurationDays']       = "";   $res8['DayNo']                  = "";   $res8['MealID']                 = "";   
                        $res8['MealDescription']        = "";   $res8['ExercisePlanNo']         = "";   $res8['ExercisePlanName']       = "";  
                        $res8['ExerciseDurationDays']   = "";   $res8['ExerciseSNo']            = "";   $res8['ExerciseDescription']    = "";   
                        $res8['ExerciseInstruction']    = "";   $res8['ExerciseNoOfReps']       = "";   $res8['ExerciseDuration']       = "";   
                        $res8['LabTestID']              = "";   $res8['LabTestRequirements']    = "";
                        $res8['AppointmentDuration']    = "";   $res8['AppointmentPlace']       = ""; 
                         $res8['SpecialType'] = "";
                        $res8['SpecialDuration'] = "";
                        if($plan_synced_date!="" && $plan_synced_date!='0000-00-00 00:00:00' && $howlong==0)
                        {

                        }
                        else
                        {
                            array_push($result,$res8);
                        }
                        }
                    //exit;
                        /*If Howlong ie; duration is 0 then dont send*/

                        /*End of Checking value of Howlong*/
                    //array_push($result,$res8);
                    }
                }
                //END OF GET INSTRUCTION DETAILS
                $plan['PLANPIPER_PLAN_INFO']            = $plan_info;
                $plan['PLANPIPER_MERCHANT_SOCIAL_MEDIA']= $social_media_info;
                $plan['ANALYTICS']                      = $analytics;
                $plan['PLANPIPER_ACTIVITIES']           = $result;
            }
           
        array_push($plan_details,$plan);

        //UPDATE UserStartOrUpdateDateTime ie;PLAN START DATE IN USER_PLAN_HEADER TABLE 
        //$now = date('Y-m-d H:i:s');
        }//end of foreach
    
    //echo "{".json_encode('PLANS').':'.json_encode($plan_details).","
           // .json_encode('PLANPIPER_SETTINGS_BACKUP_RESTORE').':'.json_encode($restore)."}";

     echo "{".$paid_status.json_encode('PLANPIPER_ASSIGNED_PLANS').':'.json_encode($plan_list).","
            .json_encode('PLANS').':'.json_encode($plan_details)."}";
    }
    else
    {
        $plan_list = ""; $plan_details = "";
        echo "{".$paid_status.json_encode('PLANPIPER_ASSIGNED_PLANS').':'.json_encode($plan_list).","
            .json_encode('PLANS').':'.json_encode($plan_details)."}";
    }
}

//*****************************END OF RESTORE PHONE SETTINGS BACKUP*****************************************

//*****************************DELETE PLAN******************************************************************
elseif($_REQUEST['RequestType']=="delete_plan" && $_REQUEST['userid']!="" && $_REQUEST['plan_code']!="")
{
    $plancode           = $_REQUEST['plan_code']; //Can be comma seperated values if in case multiple plans should be deleted
    $userid             = $_REQUEST['userid'];
    $resp = "0";
    if($plancode != "")
    {
        $plancodearray = array();
        $plancodearray = explode(",", $plancode);
        foreach ($plancodearray as $plancodedelete)
        {
            if($plancodedelete != "")
            {
                if($plancodedelete && $userid)
                {
                //echo $plancodedelete;exit;
                    /*Self Plans have Numeric PlanCodes and External Plans have AlphaNumeric Plan Codes*/
                    if(is_numeric($plancodedelete))
                    {

                        $delete_plan        = "update USER_SELF_PLAN_HEADER set Status='I' where UserID='$userid' and PlanCode='$plancodedelete';";
                    }
                    else
                    {
                        $delete_plan        = "update USER_PLAN_HEADER set PlanStatus='I' where UserID='$userid' and PlanCode='$plancodedelete';";
                    }
                    //echo $delete_plan."<br>";
                    $delete_plan_query  = mysql_query($delete_plan);
                    $check_update       = mysql_affected_rows();
                    if($check_update)
                    {
                        $resp = "1";
                    }
                    else
                    {
                        
                    }
                }
                else
                {
                    $resp = "0";
                }
            }
        }
    } 
    else
    {
        $resp = "0";
    }
    //echo $resp;
    if($resp == "1")
    {
        echo "{".$paid_status.json_encode('PLANPIPER_DELETE_PLAN').':'.json_encode("1")."}";
    }
    else
    {
        echo "{".$paid_status.json_encode('PLANPIPER_DELETE_PLAN').':'.json_encode("0")."}";
    }
}
//*****************************END OF DELETE PLAN**********************************************************

//*****************************USER RESPONSE FOR SELF TEST*************************************************
elseif($_REQUEST['RequestType']=="user_response" && $_REQUEST['userid']!="" && $_REQUEST['plan_code']!="")
{
$userid             = $_REQUEST['userid'];
$plancode           = $_REQUEST['plan_code'];
$self_test_id       = $_REQUEST['self_test_id'];
$row_no             = $_REQUEST['row_no'];
$test_name          = $_REQUEST['test_name'];
$test_value         = $_REQUEST['test_value'];
$get_max_sno = mysql_query("select max(SNo) from USER_SELF_TEST_DATA_FROM_CLIENT where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $row_no");
        $get_max_count = mysql_num_rows($get_max_sno);
            if($get_max_count > 0){
                while ($maxcount = mysql_fetch_array($get_max_sno)) {
                    $max_sno = $maxcount['max(SNo)'];
                }
            } else {
                $max_sno = 0;
        }

    if($userid && $plancode)
    {
    $max_sno++;
    $insert_user_response = "insert into USER_SELF_TEST_DATA_FROM_CLIENT 
                            (UserID,PlanCode,SelfTestID,RowNo,SNo,ResponseDataName,ResponseDataValue,CreatedDate,CreatedBy) values 
                            ('$userid','$plancode','$self_test_id','$row_no','$max_sno','$test_name','$test_value',now(),'$userid')";
    //echo $insert_user_response;exit;
    $insert_response_query= mysql_query($insert_user_response);
    $check_insert         = mysql_affected_rows();
        if($check_insert)
        {
            echo "{".json_encode('PLANPIPER_USER_RESPONSE').':'.json_encode('1')."}";  
        }
        else
        {
            echo "{".json_encode('PLANPIPER_USER_RESPONSE').':'.json_encode("0")."}";
        }
    }
}
//*****************************END OF USER RESPONSE*********************************************************

//*****************************UPDATE PLAN DOWNLOADED DATE/TIME AND SYNCHRONISED STATUS********************
elseif($_REQUEST['RequestType']=="plan_synced_status" && $_REQUEST['userid']!="")
{
//echo 123;exit;
$userid             = $_REQUEST['userid'];
$synchronised       = 'Y';
$json               = urldecode(stripslashes($_REQUEST['response']));
$json               = json_decode($json,true);
//echo "<pre>";print_r($json);
    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($plancode);
            foreach ($num as $key => $val)
            {
                //echo "<pre>";print_r($val); 
                $plancode       = $val[0];
                //$difference     = $val[1];
                $difference     = 1;
                $start_date     = date('Y-m-d');
                $end_date   = date('Y-m-d', strtotime($start_date. " + $difference day"));

                $update_sync_status = "update USER_PLAN_HEADER set UserStartOrUpdateDateTime=current_timestamp,Synchronised='$synchronised', PlanEndDate = '$end_date',UpdatedBy='$userid'
                                        where PlanCode='$plancode' and UserID = '$userid';";
                //echo $update_sync_status;
                $update_sync_query  = mysql_query($update_sync_status);
            }
        }
    }

    /*Get Max of Plan End Date and Update in USER_ACCESS Table(Update PaidUntil column in USER_ACCESS Table)*/
    $get_max_plan_date  = mysql_query("select max(PlanEndDate) from USER_PLAN_HEADER where UserID='$userid' 
                                        and FreePlan='N' and PlanStatus='A'");
    $max_plan_date      = mysql_result($get_max_plan_date,0);

    $update             = mysql_query("update USER_ACCESS set PaidUntil='$max_plan_date' where UserID='$userid'");

    if($max_plan_date=="" || $max_plan_date==NULL)
    {
        echo "{".json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("N")."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("Y")."}";
    }
}
//*****************************END OF USER RESPONSE**********************************************************




//*****************************ACKNOWLEDGEMENT(MEDICATION)(ie;Done or not Done)******************************
elseif($_REQUEST['RequestType']=="medication_acknowledgement" && $_REQUEST['userid']!="" && $_REQUEST['response']!="")
{
$userid             = $_REQUEST['userid'];
$json               = urldecode(stripslashes($_REQUEST['response']));
//$json = str_replace('&quot;', '"', $json);
//echo urldecode(stripslashes($json));exit;
//echo "<pre>";print_r($json);exit;
$json               = json_decode($json,true);
//echo "<pre>";print_r($json);exit;
//echo $count = count($json);
    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($role);
             foreach ($num as $key => $val)
            {
                // echo "<pre>";print_r($val); 
                $plancode           = $val[0];
                $prescription_no    = $val[1];
                $row_no             = $val[2];
                $date               = $val[3];
                $time               = $val[4];
                $status             = $val[5];

                $get_max_sno    = mysql_query("select max(SNo) from USER_MEDICATION_DATA_FROM_CLIENT where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $row_no");
                $get_max_count  = mysql_num_rows($get_max_sno);
                    if($get_max_count > 0)
                    {
                        while ($maxcount = mysql_fetch_array($get_max_sno))
                        {
                            $max_sno = $maxcount['max(SNo)'];
                        }
                    }
                    else
                    {
                        $max_sno = 0;
                    }

                    /*Code to Check whether Medication Data is to be inserted or updated in Database */

                    $check_duplicate        = "select * from USER_MEDICATION_DATA_FROM_CLIENT where `PlanCode` = '$plancode' 
                                                and `UserID` = '$userid' and `RowNo` = $row_no and `Date`='$date' and `Time`='$time'";
                    $check_duplicate_qry    = mysql_query($check_duplicate);
                    $check_duplicate_count  = mysql_num_rows($get_max_sno);
                    if($check_duplicate_count > 0)
                    {
                        $update      = "update USER_MEDICATION_DATA_FROM_CLIENT set ResponseRequiredStatus='$status' where `PlanCode` = '$plancode' 
                                        and `UserID` = '$userid' and `RowNo` = $row_no and `Date`='$date' and `Time`='$time'";
                        $update_query= mysql_query($update);
                    }
                    else
                    {
                        if($userid && $plancode)
                        {
                        $max_sno++;
                        $insert_acknowledgement      = "insert into USER_MEDICATION_DATA_FROM_CLIENT 
                                                        (UserID,PlanCode,PrescriptionNo,RowNo,SNo,Date,Time,ResponseRequiredStatus,CreatedDate,CreatedBy) values 
                                                        ('$userid','$plancode','$prescription_no','$row_no','$max_sno','$date','$time','$status',now(),'$userid')";
                        $insert_acknowledgement_query= mysql_query($insert_acknowledgement);
                        $check_insert                = mysql_affected_rows();
                        }
                    }

                    /*if($check_insert || $update_query)
                    {
                        $success = "1";
                    }
                    else
                    {
                        $success = "0";
                    }*/
            }
        }
    }
    /*End of Code to Check whether Medication Data is to be inserted or updated in Database */   
}
//*****************************END OF USER RESPONSE*********************************************************

//*****************************ACKNOWLEDGEMENT(INSTRUCTION)(ie;Done or not Done)******************************
elseif($_REQUEST['RequestType']=="instruction_acknowledgement" && $_REQUEST['userid']!="" && $_REQUEST['plan_code']!="")
{
$userid             = $_REQUEST['userid'];
$json               = urldecode(stripslashes($_REQUEST['response']));
//$json = str_replace('&quot;', '"', $json);
//echo urldecode(stripslashes($json));exit;
//echo "<pre>";print_r($json);exit;
$json               = json_decode($json,true);
//echo "<pre>";print_r($json);exit;
//echo $count = count($json);
    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($role);
             foreach ($num as $key => $val)
            {
                // echo "<pre>";print_r($val); 
                $plancode           = $val[0];
                $prescription_no    = $val[1];
                $row_no             = $val[2];
                $date               = $val[3];
                $time               = $val[4];
                $status             = $val[5];

                $get_max_sno    = mysql_query("select max(SNo) from USER_INSTRUCTION_DATA_FROM_CLIENT where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $row_no");
                $get_max_count  = mysql_num_rows($get_max_sno);
                    if($get_max_count > 0)
                    {
                        while ($maxcount = mysql_fetch_array($get_max_sno))
                        {
                            $max_sno = $maxcount['max(SNo)'];
                        }
                    }
                    else
                    {
                        $max_sno = 0;
                    }

                    /*Code to Check whether Medication Data is to be inserted or updated in Database */

                    $check_duplicate        = "select * from USER_INSTRUCTION_DATA_FROM_CLIENT where `PlanCode` = '$plancode' 
                                                and `UserID` = '$userid' and `RowNo` = $row_no and `Date`='$date' and `Time`='$time'";
                    $check_duplicate_qry    = mysql_query($check_duplicate);
                    $check_duplicate_count  = mysql_num_rows($get_max_sno);
                    if($check_duplicate_count > 0)
                    {
                        $update      = "update USER_INSTRUCTION_DATA_FROM_CLIENT set ResponseRequiredStatus='$status' where `PlanCode` = '$plancode' 
                                        and `UserID` = '$userid' and `RowNo` = $row_no and `Date`='$date' and `Time`='$time'";
                        $update_query= mysql_query($update);
                    }
                    else
                    {
                        if($userid && $plancode)
                        {
                        $max_sno++;
                        $insert_acknowledgement      = "insert into USER_INSTRUCTION_DATA_FROM_CLIENT 
                                                        (UserID,PlanCode,PrescriptionNo,RowNo,SNo,Date,Time,ResponseRequiredStatus,CreatedDate,CreatedBy) values 
                                                        ('$userid','$plancode','$prescription_no','$row_no','$max_sno','$date','$time','$status',now(),'$userid')";
                        $insert_acknowledgement_query= mysql_query($insert_acknowledgement);
                        $check_insert                = mysql_affected_rows();
                        }
                    }

                    /*if($check_insert || $update_query)
                    {
                        $success = "1";
                    }
                    else
                    {
                        $success = "0";
                    }*/
            }
        }
    }
    /*End of Code to Check whether Medication Data is to be inserted or updated in Database */ 
}
//*****************************END OF USER RESPONSE*********************************************************

//*****************************SAVE ANALYTICS DATA ******************************************
elseif($_REQUEST['RequestType']=="save_analytics" && $_REQUEST['userid']!=""){
    $userid             = $_REQUEST['userid'];
    $success            = "1";
    $json               = urldecode(stripslashes($_REQUEST['response']));
    //$json = str_replace('&quot;', '"', $json);
    //echo urldecode(stripslashes($json));exit;
    //echo "<pre>";print_r($json);exit;
    $json               = json_decode($json,true);
    //echo "<pre>";print_r($json);exit;
    //echo $count = count($json);
    //echo $payment_status;exit;
    /*Get Name of User*/
    $name   = mysql_result(mysql_query("select concat(FirstName,' ',LastName) from USER_DETAILS where UserID='$userid'"),0);
    /*End of Get Name of User*/

    $medi               = array();
    $inst               = array();
    $merchants_med      = array();
    $merchants_inst     = array();
    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($num);exit;
            $tablename="";
            $values     = array();
            foreach ($num as $key => $val)
            {
               //echo "<pre>";print_r($val);
                $merchant_id        = $val[0];
                $section_id         = $val[1];/*1 is Medication,3-2 is Self Test,6 is Instruction*/
                $plancode           = $val[2];
                $presc_self_lab     = $val[3];/*If Medication/Instruction-Prescription Number and Self Test-Self Test ID */
                $rowno              = $val[4];/*If Medication/Instruction-Row Number and Self Test-Row Number */
                $medname_testname   = $val[5];/*If Medication/Instruction-MedicineName/InstructionName and Self Test-Test Name */
                $response           = $val[6];/*If Medication/Instruction-Response('Y','N','SMS','IVR') and Self Test- Response(Blood Pressure,Blood Sugar etc) */
                $date_time          = $val[7];/*If Medication/Instruction/Self Test - Date and Time of Activity */        

                if($section_id=="1")
                {
                    $tablename = " USER_MEDICATION_DATA_FROM_CLIENT ";
                    $tablename1= " USER_MEDICATION_DETAILS ";
                }
                elseif($section_id=="6")
                {
                    $tablename = " USER_INSTRUCTION_DATA_FROM_CLIENT ";
                    $tablename1= " USER_INSTRUCTION_DETAILS ";
                }

                /*Medication Or Instruction*/
                if($section_id=="1" || $section_id=="6")
                {
                    //echo 123;exit;
                $threshold_limit    = "";
                    /*Fetch Threshold Limit for Medication and Instruction*/
                    $threshold_value    = "select ThresholdLimit FROM $tablename1 
                                                    where UserID = '$userid' and PlanCode='$plancode' 
                                                    and PrescriptionNo='$presc_self_lab'
                                                    and RowNo='$rowno' and ThresholdLimit <>''";
                    //echo $threshold_value;exit;
                    $threshold_value_qry= mysql_query($threshold_value);
                    $threshold_count    = mysql_num_rows($threshold_value_qry);

                    if($threshold_count==1)
                    {
                        $threshold_row  = mysql_fetch_array($threshold_value_qry);
                        $threshold_limit= $threshold_row['ThresholdLimit']; 
                    }
                    else
                    {
                        $threshold_limit= "";
                    }
                    //echo "Limit: ".$threshold_limit;exit;
                    /*End of Fetch Threshold Limit for Medication and Instruction*/

                    $get_max_sno    = "select max(SNo) from $tablename where `PlanCode` = '$plancode' 
                                        and `UserID` = '$userid' and `RowNo` = $rowno";
                    //echo $get_max_sno."<br>";
                    $get_max_sno_qry=mysql_query($get_max_sno);
                    $m_get_max_count  = mysql_num_rows($get_max_sno_qry);
                    //echo $m_get_max_count;
                        if($m_get_max_count > 0)
                        {
                            while ($m_maxcount = mysql_fetch_array($get_max_sno_qry))
                            {
                                $m_max_sno = $m_maxcount['max(SNo)'];
                                //echo $m_max_sno;
                            }
                        }
                        else
                        {
                            $m_max_sno = 0;
                        }
                        //echo $m_max_sno;
                        /*Code to Check whether Medication/Instruction Data is to be inserted or updated in Database */

                        $check_duplicate        = "select * from $tablename where `PlanCode` = '$plancode' 
                                                    and `UserID` = '$userid' and `RowNo` = '$rowno' and `DateTime`='$date_time'";
                       //echo $check_duplicate;
                        $check_duplicate_qry    = mysql_query($check_duplicate);
                        $check_duplicate_count  = mysql_num_rows($check_duplicate_qry);
                        if($check_duplicate_count > 0)
                        {
                            $update      = "update $tablename set ResponseRequiredStatus='$response' 
                            where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = '$rowno' 
                            and `DateTime`='$date_time'";
                            $update_query= mysql_query($update);
                            $check_update= mysql_affected_rows();
                            
                        }
                        else
                        {
                            if($userid && $plancode)
                            {
                            $m_max_sno++;
                            $insert_acknowledgement      = "insert into $tablename 
                                                            (UserID,PlanCode,PrescriptionNo,RowNo,SNo,DateTime,ResponseRequiredStatus,CreatedDate,CreatedBy) values 
                                                            ('$userid','$plancode','$presc_self_lab','$rowno','$m_max_sno','$date_time','$response',now(),'$userid')";
                            $insert_acknowledgement_query= mysql_query($insert_acknowledgement);
                            $check_insert                = mysql_affected_rows();
                            }
                        }

                    /*INTIMATING DOCTOR BASED ON THRESHOLD LIMIT*/
                    if($threshold_limit>0)
                    {
                    $get_response_status    = "select t1.PlanCode from $tablename as t1 where t1.UserID = '$userid' 
                                                and t1.PlanCode='$plancode' and t1.PrescriptionNo='$presc_self_lab'
                                                and t1.RowNo='$rowno' and t1.ResponseRequiredStatus='N' 
                                                and t1.ConsideredForNotification='N'";
                    //echo $get_response_status;exit;
                    $get_response_status_qry= mysql_query($get_response_status);
                    $response_status_count  = mysql_num_rows($get_response_status_qry);
                    //echo "R: ".$response_status_count."<br>";
                   // echo "T: ".$threshold_limit."<br>";
                    //exit;
                        if($response_status_count >= $threshold_limit)
                        {                    
                            if($section_id=="1")
                            {
                                $med = "update $tablename set ConsideredForNotification='Y' where UserID = '$userid' 
                                        and PlanCode='$plancode' and PrescriptionNo='$presc_self_lab'
                                        and RowNo='$rowno' and ResponseRequiredStatus='N'";
                                $med_query          = mysql_query($med);
                                $check_med_update   = mysql_affected_rows();
                                if($check_med_update)
                                {
                                   $get_merchant_id_qry_med     = "select MerchantID from USER_PLAN_HEADER where PlanCode='$plancode'";
                                    //echo $get_merchant_id_qry."<br>";
                                    $get_merchant_id_med        = mysql_query($get_merchant_id_qry_med); 
                                    $get_mer_count_med          = mysql_num_rows($get_merchant_id_med);
                                    if($get_mer_count_med>0)
                                    {
                                        while($merchant_rows_med = mysql_fetch_array($get_merchant_id_med))
                                        {
                                            $merchant_id_med   = $merchant_rows_med['MerchantID'];
                                            if(!in_array($merchant_id_med,$merchants_med))
                                            {
                                                array_push($merchants_med,$merchant_id_med);
                                            }  
                                        }
                                    //echo "<pre>";print_r($merchants_med);
                                    }
                                }
                            }
                            elseif($section_id=="6")
                            {
                                $ins = "update $tablename set ConsideredForNotification='Y' where UserID = '$userid' 
                                        and PlanCode='$plancode' and PrescriptionNo='$presc_self_lab'
                                        and RowNo='$rowno' and ResponseRequiredStatus='N'";
                                $ins_query          = mysql_query($ins);
                                $check_ins_update   = mysql_affected_rows();
                                if($check_ins_update)
                                {
                                    $get_merchant_id_inst   = mysql_query("select MerchantID from USER_PLAN_HEADER where PlanCode='$plancode'");
                                    $get_mer_count_inst     = mysql_num_rows($get_merchant_id_inst);
                                        if($get_mer_count_inst>0)
                                        {
                                            //echo "123";
                                            $merchant_rows_inst = mysql_fetch_array($get_merchant_id_inst);
                                            $merchant_id_inst        = $merchant_rows_inst['MerchantID'];
                                            if(!in_array($merchant_id_inst,$merchants_inst))
                                            {
                                                array_push($merchants_inst,$merchant_id_inst);
                                            }          
                                        }
                                    //echo "<pre>";print_r($merchants_inst);
                                }                         
                            }    
                        }
                    }
                    
                    /*INTIMATING DOCTOR BASED ON THRESHOLD LIMIT*/
                }
                elseif($section_id=="3-1")
                {
                    $get_max_sno = mysql_query("select max(SNo) from USER_SELF_TEST_DATA_FROM_CLIENT where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $rowno");
                    $get_max_count = mysql_num_rows($get_max_sno);
                        if($get_max_count > 0){
                            while ($maxcount = mysql_fetch_array($get_max_sno)) {
                                $max_sno = $maxcount['max(SNo)'];
                            }
                        }
                        else{
                            $max_sno = 0;
                        }
                        if($userid && $plancode)
                        {
                        $max_sno++;
                        $insert_user_response = "insert into USER_SELF_TEST_DATA_FROM_CLIENT 
                                                (UserID,PlanCode,SelfTestID,RowNo,SNo,ResponseDataName,ResponseDataValue,DateTime,CreatedDate,CreatedBy) values 
                                                ('$userid','$plancode','$presc_self_lab','$rowno','$max_sno','$medname_testname','$response','$date_time',now(),'$userid')";
                        //echo $insert_user_response;exit;
                        $insert_response_query= mysql_query($insert_user_response);
                        }
                }
                elseif($section_id=="3-2")
                {
                    $update_labtest = mysql_query("update USER_LAB_TEST_DETAILS1 set LabTestDate='$date_time', LabTestTime='$response' where PlanCode = '$plancode' and UserID = '$userid' and LabTestID='$presc_self_lab' and RowNo = '$rowno'");
                    $check_insert   = mysql_affected_rows();
                }
            }
        }
    }
    
    //echo "<pre>";print_r($merchants_med);
    //echo "<pre>";print_r($merchants_inst);

$combined = array_unique(array_merge($merchants_med,$merchants_inst));
//echo "<pre>";print_r($combined);
//echo "<pre>";print_r($combined);
$msg = "";
foreach($combined as $res)
{
    if(in_array($res,$merchants_med) && !in_array($res,$merchants_inst))
    {
        $msg = "$name has not taken medication";
    }
    elseif(in_array($res,$merchants_inst) && !in_array($res,$merchants_med))
    {
        $msg = "$name has not followed instruction";
    }
    elseif(in_array($res,$combined))
    {
        $msg = "$name has not taken medication and not followed instruction";
    }
//echo $msg."<br>";

    if($payment_status=='Y')
    {
    $insert_dektop_notification = mysql_query("insert into DESKTOP_NOTIFICATION (MerchantID,UserID,Message,DisplayStatus,CreatedDate,CreatedBy) values 
                                                            ('$res','$userid','$msg','N',now(),'SYSTEM')");
    }
}

    if($success == "1"){
        echo "{".$paid_status.json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("1")."}";
    } else {
        echo "{".$paid_status.json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("0")."}";
    }
}
//*****************************END OF ANALYTICS DATA*********************************************************

//*****************************SAVE ANALYTICS DATA ******************************************
elseif($_REQUEST['RequestType']=="save_analytics123" && $_REQUEST['userid']!=""){
$userid             = $_REQUEST['userid'];
    $success            = "1";
    $json               = urldecode(stripslashes($_REQUEST['response']));
    //$json = str_replace('&quot;', '"', $json);
    //echo urldecode(stripslashes($json));exit;
    //echo "<pre>";print_r($json);exit;
    $json               = json_decode($json,true);
    //echo "<pre>";print_r($json);exit;
    //echo $count = count($json);



    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($num);exit;
            $tablename="";
            foreach ($num as $key => $val)
            {
               //echo "<pre>";print_r($val);
                $merchant_id        = $val[0];
                $section_id         = $val[1];/*1 is Medication,3-2 is Self Test,6 is Instruction*/
                $plancode           = $val[2];
                $presc_self_lab     = $val[3];/*If Medication/Instruction-Prescription Number and Self Test-Self Test ID */
                $rowno              = $val[4];/*If Medication/Instruction-Row Number and Self Test-Row Number */
                $medname_testname   = $val[5];/*If Medication/Instruction-MedicineName/InstructionName and Self Test-Test Name */
                $response           = $val[6];/*If Medication/Instruction-Response('Y','N','SMS','IVR') and Self Test- Response(Blood Pressure,Blood Sugar etc) */
                $date_time          = $val[7];/*If Medication/Instruction/Self Test - Date and Time of Activity */        

                if($section_id=="1")
                {
                    $tablename = " USER_MEDICATION_DATA_FROM_CLIENT ";
                }
                elseif($section_id=="6")
                {
                    $tablename = " USER_INSTRUCTION_DATA_FROM_CLIENT ";
                }

                /*Medication Or Instruction*/
                if($section_id=="1" || $section_id=="6")
                {
                    //echo 123;exit;
                    $get_max_sno    = "select max(SNo) from $tablename where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $rowno";
                    //echo $get_max_sno;exit;
                    $get_max_sno_qry=mysql_query($get_max_sno);
                    $m_get_max_count  = mysql_num_rows($get_max_sno_qry);
                    //echo $m_get_max_count;
                        if($m_get_max_count > 0)
                        {
                            while ($m_maxcount = mysql_fetch_array($get_max_sno_qry))
                            {
                                $m_max_sno = $m_maxcount['max(SNo)'];
                                //echo $m_max_sno;
                            }
                        }
                        else
                        {
                            $m_max_sno = 0;
                        }
                        //echo $m_max_sno;
                        /*Code to Check whether Medication/Instruction Data is to be inserted or updated in Database */

                        $check_duplicate        = "select * from $tablename where `PlanCode` = '$plancode' 
                                                    and `UserID` = '$userid' and `RowNo` = '$rowno' and `DateTime`='$date_time'";
                       //echo $check_duplicate;
                        $check_duplicate_qry    = mysql_query($check_duplicate);
                        $check_duplicate_count  = mysql_num_rows($check_duplicate_qry);
                        if($check_duplicate_count > 0)
                        {
                            $update      = "update $tablename set ResponseRequiredStatus='$response' where `PlanCode` = '$plancode' 
                                            and `UserID` = '$userid' and `RowNo` = '$rowno' and `DateTime`='$date_time'";
                            $update_query= mysql_query($update);
                            $check_update= mysql_affected_rows();
                            
                        }
                        else
                        {
                            if($userid && $plancode)
                            {
                            $m_max_sno++;
                            $insert_acknowledgement      = "insert into $tablename 
                                                            (UserID,PlanCode,PrescriptionNo,RowNo,SNo,DateTime,ResponseRequiredStatus,CreatedDate,CreatedBy) values 
                                                            ('$userid','$plancode','$presc_self_lab','$rowno','$m_max_sno','$date_time','$response',now(),'$userid')";
                            $insert_acknowledgement_query= mysql_query($insert_acknowledgement);
                            $check_insert                = mysql_affected_rows();
                            }
                        }
                }
                elseif($section_id=="3-1")
                {
                    $get_max_sno = mysql_query("select max(SNo) from USER_SELF_TEST_DATA_FROM_CLIENT where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $rowno");
                    $get_max_count = mysql_num_rows($get_max_sno);
                        if($get_max_count > 0){
                            while ($maxcount = mysql_fetch_array($get_max_sno)) {
                                $max_sno = $maxcount['max(SNo)'];
                            }
                        }
                        else{
                            $max_sno = 0;
                        }
                        if($userid && $plancode)
                        {
                        $max_sno++;
                        $insert_user_response = "insert into USER_SELF_TEST_DATA_FROM_CLIENT 
                                                (UserID,PlanCode,SelfTestID,RowNo,SNo,ResponseDataName,ResponseDataValue,DateTime,CreatedDate,CreatedBy) values 
                                                ('$userid','$plancode','$presc_self_lab','$rowno','$max_sno','$medname_testname','$response','$date_time',now(),'$userid')";
                        //echo $insert_user_response;exit;
                        $insert_response_query= mysql_query($insert_user_response);
                        }
                }
                elseif($section_id=="3-2")
                {
                    $update_labtest = mysql_query("update USER_LAB_TEST_DETAILS1 set LabTestDate='$date_time', LabTestTime='$response' where PlanCode = '$plancode' and UserID = '$userid' and LabTestID='$presc_self_lab' and RowNo = '$rowno'");
                    $check_insert   = mysql_affected_rows();
                }
            }
        }
    }
    if($success == "1"){
        echo "{".$paid_status.json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("1")."}";
    } else {
        echo "{".$paid_status.json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("0")."}";
    }
}
//*****************************END OF ANALYTICS DATA*********************************************************
//*****************************SAVE ANALYTICS DATA ******************************************
elseif($_REQUEST['RequestType']=="save_analytics_old" && $_REQUEST['userid']!=""){
    $userid             = $_REQUEST['userid'];
    $success            = "1";
    $json               = urldecode(stripslashes($_REQUEST['response']));
    //$json = str_replace('&quot;', '"', $json);
    //echo urldecode(stripslashes($json));exit;
    //echo "<pre>";print_r($json);exit;
    $json               = json_decode($json,true);
    //echo "<pre>";print_r($json);exit;
    //echo $count = count($json);
    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($num);exit;
            $tablename="";
            foreach ($num as $key => $val)
            {
               //echo "<pre>";print_r($val);
                $section_id     = $val[0];/*1 is Medication,3-1 is Lab Test,3-2 is Self Test,6 is Instruction*/
                $plancode       = $val[1];
                $presc_self_lab = $val[2];/*Prescription Number or Self Test ID or Lab Test ID*/
                $rowno          = $val[3];
                $date_testname  = $val[4];/*If Self Test then Test Name,if lab test then test date,if medication or instrucion then Date of Medication*/
                $time_testvalue = $val[5];/*If Self Test then Test Value,if lab test then test time,if medication or instrucion then Time of Medication*/
                
               if($section_id=='3-1')
               {
                $status         = "";
               }
               else
               {
                $status         = $val[6];/*If Medication then status can be Y,N,IVR or SMS. For other sections status will be empty*/
               }

                if($section_id=="1")
                {
                    $tablename = " USER_MEDICATION_DATA_FROM_CLIENT ";
                }
                elseif($section_id=="6")
                {
                    $tablename = " USER_INSTRUCTION_DATA_FROM_CLIENT ";
                }

                /*Medication Or Instruction*/
                if($section_id=="1" || $section_id=="6")
                {
                    //echo 123;exit;
                    $get_max_sno    = "select max(SNo) from $tablename where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $rowno";
                    //echo $get_max_sno;exit;
                    $get_max_sno_qry=mysql_query($get_max_sno);
                    $m_get_max_count  = mysql_num_rows($get_max_sno_qry);
                    //echo $m_get_max_count;
                        if($m_get_max_count > 0)
                        {
                            while ($m_maxcount = mysql_fetch_array($get_max_sno_qry))
                            {
                                $m_max_sno = $m_maxcount['max(SNo)'];
                                //echo $m_max_sno;
                            }
                        }
                        else
                        {
                            $m_max_sno = 0;
                        }
                        //echo $m_max_sno;
                        /*Code to Check whether Medication/Instruction Data is to be inserted or updated in Database */

                        $check_duplicate        = "select * from $tablename where `PlanCode` = '$plancode' 
                                                    and `UserID` = '$userid' and `RowNo` = $rowno and `Date`='$date_testname' and `Time`='$time_testvalue'";
                       //echo $check_duplicate;
                        $check_duplicate_qry    = mysql_query($check_duplicate);
                        $check_duplicate_count  = mysql_num_rows($check_duplicate_qry);
                        if($check_duplicate_count > 0)
                        {
                            $update      = "update $tablename set ResponseRequiredStatus='$status' where `PlanCode` = '$plancode' 
                                            and `UserID` = '$userid' and `RowNo` = $rowno and `Date`='$date_testname' and `Time`='$time_testvalue'";
                            $update_query= mysql_query($update);
                            $check_update= mysql_affected_rows();
                            
                        }
                        else
                        {
                            if($userid && $plancode)
                            {
                            $m_max_sno++;
                            $insert_acknowledgement      = "insert into $tablename 
                                                            (UserID,PlanCode,PrescriptionNo,RowNo,SNo,Date,Time,ResponseRequiredStatus,CreatedDate,CreatedBy) values 
                                                            ('$userid','$plancode','$presc_self_lab','$rowno','$m_max_sno','$date_testname','$time_testvalue','$status',now(),'$userid')";
                            $insert_acknowledgement_query= mysql_query($insert_acknowledgement);
                            $check_insert                = mysql_affected_rows();
                            }
                        }
                }
                elseif($section_id=="3-1")
                {
                    $get_max_sno = mysql_query("select max(SNo) from USER_SELF_TEST_DATA_FROM_CLIENT where `PlanCode` = '$plancode' and `UserID` = '$userid' and `RowNo` = $rowno");
                    $get_max_count = mysql_num_rows($get_max_sno);
                        if($get_max_count > 0){
                            while ($maxcount = mysql_fetch_array($get_max_sno)) {
                                $max_sno = $maxcount['max(SNo)'];
                            }
                        }
                        else{
                            $max_sno = 0;
                        }
                        if($userid && $plancode)
                        {
                        $max_sno++;
                        $insert_user_response = "insert into USER_SELF_TEST_DATA_FROM_CLIENT 
                                                (UserID,PlanCode,SelfTestID,RowNo,SNo,ResponseDataName,ResponseDataValue,CreatedDate,CreatedBy) values 
                                                ('$userid','$plancode','$presc_self_lab','$rowno','$max_sno','$date_testname','$time_testvalue',now(),'$userid')";
                        //echo $insert_user_response;exit;
                        $insert_response_query= mysql_query($insert_user_response);
                        }
                }
                elseif($section_id=="3-2")
                {
                    $update_labtest = mysql_query("update USER_LAB_TEST_DETAILS1 set LabTestDate='$date_testname', LabTestTime='$time_testvalue' where PlanCode = '$plancode' and UserID = '$userid' and LabTestID='$presc_self_lab' and RowNo = '$rowno'");
                    $check_insert   = mysql_affected_rows();
                }
            }
        }
    }
    if($success == "1"){
        echo "{".json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("1")."}";
    } else {
        echo "{".json_encode('PLANPIPER_ANALYTICS_RESPONSE').':'.json_encode("0")."}";
    }
}
//*****************************END OF ANALYTICS DATA*********************************************************
//*****************************SAVE LAB TEST APPOINTMENT DATE AND TIME ******************************************
elseif($_REQUEST['RequestType']=="labtest_datetime" && $_REQUEST['userid']!=""){
    $userid             = $_REQUEST['userid'];
    $success = "0";
    $json               = urldecode(stripslashes($_REQUEST['response']));
    //$json = str_replace('&quot;', '"', $json);
    //echo urldecode(stripslashes($json));exit;
    //echo "<pre>";print_r($json);exit;
    $json               = json_decode($json,true);
    //echo "<pre>";print_r($json);exit;
    //echo $count = count($json);
    foreach($json as $each_value)
    {
        //echo "<pre>";print_r($each_value);exit;
        foreach ($each_value as $num)
        {
            //echo "<pre>";print_r($role);
             foreach ($num as $key => $val)
            {
                // echo "<pre>";print_r($val); 
                $plan_code   = $val[0];
                $labtestid   = $val[1];
                $rowno       = $val[2];
                $testdate    = $val[3];
                $testtime    = $val[4];
                $testdur     = $val[5];
                $testvenue   = $val[6];
                //echo $first_value;exit;    
                //$update_labtest = mysql_query("update USER_LAB_TEST_DETAILS1 set LabTestDate='$testdate', LabTestTime='$testtime' where PlanCode = '$plan_code' and UserID = '$userid' and LabTestID='$labtestid' and RowNo = '$rowno'");
                $update_labtest = mysql_query("update USER_LAB_TEST_DETAILS1 set LabTestDate='$testdate', LabTestTime='$testtime', AppointmentDuration = '$testdur', AppointmentPlace = '$testvenue' where PlanCode = '$plan_code' and UserID = '$userid' and LabTestID='$labtestid' and RowNo = '$rowno'");
                $check_insert         = mysql_affected_rows();
                if($check_insert)
                {
                    $success = "1";
                }
                else
                {
                    
                }
            }
        }
    }
    
    if($success == "1"){
        echo "{".json_encode('PLANPIPER_LABTEST_DATETIME_RESPONSE').':'.json_encode("1")."}";
    } else {
        echo "{".json_encode('PLANPIPER_LABTEST_DATETIME_RESPONSE').':'.json_encode("0")."}";
    }
}
//*****************************END OF ANALYTICS DATA*********************************************************

//*****************************IVR CALL TO SUPPORT PERSON****************************************************
elseif($_REQUEST['RequestType']=="contact_support" && $_REQUEST['userid']!="" && $_REQUEST['plan_code']!=""  && $_REQUEST['support_mobile']!="" )
{
set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.0');
include('Net/SFTP.php');

$user_id                = (empty($_REQUEST['userid']))                  ? '' : $_REQUEST['userid'];
$section_id             = (empty($_REQUEST['section_id']))              ? '' : $_REQUEST['section_id'];/*1 is Medication,3-2 is Self Test,6 is Instruction*/
$plancode               = (empty($_REQUEST['plan_code']))               ? '' : $_REQUEST['plan_code'];
// $prescno_or_selftestid  = (empty($_REQUEST['prescno_or_selftestid']))   ? '' : $_REQUEST['prescno_or_selftestid'];/*Prescription Number or Self Test ID*/
$prescno_or_selftestid  = (empty($_REQUEST['prescription_no']))         ? '' : $_REQUEST['prescription_no'];/*Prescription Number or Self Test ID*/
$row_no                 = (empty($_REQUEST['row_no']))                  ? '' : $_REQUEST['row_no'];
//$date_or_testname       = (empty($_REQUEST['date_or_testname']))        ? '' : $_REQUEST['date_or_testname'];/*If Self Test then Test Name and if medication or instrucion then Date of Medication*/
$date_or_testname       = (empty($_REQUEST['date']))                    ? '' : $_REQUEST['date'];/*If Self Test then Test Name and if medication or instrucion then Date of Medication*/
//$time_or_testvalue      = (empty($_REQUEST['time_or_testvalue']))       ? '' : $_REQUEST['time_or_testvalue'];/*If Self Test then Test Value and if medication or instrucion then Time of Medication*/
$time_or_testvalue      = (empty($_REQUEST['time']))                    ? '' : $_REQUEST['time'];
$support_name           = (empty($_REQUEST['support_name']))            ? '' : $_REQUEST['support_name'];
$support_mobile         = (empty($_REQUEST['support_mobile']))          ? '' : $_REQUEST['support_mobile'];
$status                 = 'IVR';
$random_id              = mt_rand(10,1000);
$uuid                   = $user_id.":".$section_id.":".$plancode.":".$prescno_or_selftestid.":".$row_no.":".$date_or_testname.":".$time_or_testvalue.":".$random_id;

$text           = (empty($_REQUEST['text']))            ? '' : $_REQUEST['text'];
//$text = "Chinni Krishna didn't have Paracetamol tablets today at 10am. Medicine was prescribed by Dr Somsekhar";

        // Convert Words (text) to Speech (MP3)
        $words = $text;
        // Google Translate API cannot handle strings > 100 characters
        $words = substr($words, 0, 100 );
     
        // Replace the non-alphanumeric characters
        // The spaces in the sentence are replaced with the Plus symbol
        $words = urlencode($words);
     
        // Name of the MP3 file generated using the MD5 hash
        $file1  = $support_mobile."_".date('Ymd_Gis');
        $file  = "$file1.wav";

        // Save the WAV file in this folder with the .wav extension
        $destination      = "audio/$file";

        // If the WAV file exists, do not create a new request
        if (!file_exists($destination)) {
        $wav = file_get_contents("http://translate.google.com/translate_tts?sl=hi&q=$words&tl=hi");
        file_put_contents($destination, $wav);

        /*LOCATION TO COPY FILES*/
        $local_directory  = 'audio/';
        $remote_directory = '/var/lib/asterisk/sounds/';
         


       //  /* Add the correct SFTP credentials below */
       //  $sftp = new Net_SFTP('202.138.98.152');
       //  if (!$sftp->login('appmantra', 'app@123')) 
       //  {
       //      exit('Login Failed');
       //  } 

       //  /*COPY WAV FILE FROM LOCAL SERVER TO REMOTE SERVER*/
       //  $success = $sftp->put($remote_directory . $file, $local_directory . $file, NET_SFTP_LOCAL_FILE);

       //  $data = array(
       // "uuid"       => $uuid,
       // "callerid"   => $support_mobile,
       // "voicefile"  => $file1,
       // "campaignid" => "tfr0000138c0001"
       //  );

       //  /*CALL IVR API HERE BY PASSING NECESSARY PARAMETERS*/
       //  post_to_url($url, $data);                                                                                                                                                                                                                                                                                                                               
    }
    echo "{".json_encode('PLANPIPER_CONTACT_SUPPORT_ACKNOWLEDGEMENT').':'.json_encode("1")."}";
}
//*****************************END OF IVR CALL TO SUPPORT PERSON**********************************************

//*****************************IVR CALLS MADE TO SUPPORT PERSON************************************************
elseif($_REQUEST['RequestType']=="ivr_logs" && $_REQUEST['uuid']!="" && $_REQUEST['callerid']!="")
{
$uuid                   = $_REQUEST['uuid'];
$uuid                   = explode(":",$uuid);

$user_id                = (empty($uuid[0]))                 ? '' : $uuid[0];
$section_id             = (empty($uuid[1]))                 ? '' : $uuid[1];/*1 is Medication,3-2 is Self Test,6 is Instruction*/
$plancode               = (empty($uuid[2]))                 ? '' : $uuid[2];
$prescno_or_selftestid  = (empty($uuid[3]))                 ? '' : $uuid[3];/*Prescription Number or Self Test ID*/
$row_no                 = (empty($uuid[4]))                 ? '' : $uuid[4];
$date_or_testname       = (empty($uuid[5]))                 ? '' : $uuid[5];/*If Self Test then Test Name and if medication or instrucion then Date of Medication*/
$time_or_testvalue      = (empty($uuid[6]))                 ? '' : $uuid[6];/*If Self Test then Test Value and if medication or instrucion then Time of Medication*/
$callerid               = (empty($_REQUEST['callerid']))    ? '' : $_REQUEST['callerid'];
$status                 = (empty($_REQUEST['status']))      ? '' : $_REQUEST['status'];/*Whether call was successfull or not (Y or N)*/

    $insert_logs    = "insert into IVR_CALL_LOGS
                        (Support_Mobile,UserID,PlanCode,SectionID,PrescriptionNo,RowNo,Date,Time,Response,CreatedDate) values
                        ('$callerid','$user_id','$plancode','$section_id','$prescno_or_selftestid','$row_no','$date_or_testname','$time_or_testvalue','$status',now())";
    $insert_logs_qry= mysql_query($insert_logs);
    $check_insert   = mysql_affected_rows();
    if($check_insert)
    {
        echo "{".json_encode('PLANPIPER_IVR_LOGS').':'.json_encode('1')."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_IVR_LOGS').':'.json_encode('0')."}";
    } 
}
//*****************************END OF IVR CALL LOG************************************************

//*****************************ADD/EDIT/DELETE PATIENT CONTACTS************************************************
elseif($_REQUEST['RequestType']=="patient_contacts" && $_REQUEST['userid']!="" && $_REQUEST['response']!="")
{
$userid = (empty($_REQUEST['userid']))  ? '' : $_REQUEST['userid'];
//$json   = $_REQUEST['response'];
$json   = stripslashes($_REQUEST['response']);
$obj    = json_decode($json,true);
//echo "<pre>";print_r($obj);exit;

    foreach($obj as $item)
    {
        $check_duplicate        = mysql_query("select MobileUniqueID from PATIENT_CONTACTS where MobileUniqueID='".$item['uniqueid']."'");
        $check_duplicate_count  = mysql_num_rows($check_duplicate);
        //echo $check_duplicate_count;exit;
        $item['landline_cc'] = trim_leading_zeros_special_symbols($item['landline_cc']);
        $item['mobile_cc']   = trim_leading_zeros_special_symbols($item['mobile_cc']);
        if($check_duplicate_count==1)
        {
            //echo 123;
                 $q="update PATIENT_CONTACTS set Type='".$item['type']."',FirstName='".$item['first_name']."',
                        MiddleName='".$item['middle_name']."',LastName='".$item['last_name']."',EmailID='".$item['emailid']."',
                        MobileCountryCode='".$item['mobile_cc']."',MobileNo='".$item['mobile']."',LandlineCountryCode='".$item['landline_cc']."',
                        LandlineAreaCode='".$item['landline_area_cc']."',LandlineNo='".$item['landline']."',Tag='".$item['tag']."',
                        Status='".$item['status']."',UpdatedDate=now(),UpdatedBy='$userid'
                        where MobileUniqueID='".$item['uniqueid']."'"; 
        }
        else
        {
            //echo 456;
        //$item['landline_cc'] = trim_leading_zeros_special_symbols("$item['landline_cc']");
        //$item['mobile_cc']   = trim_leading_zeros_special_symbols("$item['mobile_cc']");
            $q = "insert into PATIENT_CONTACTS 
                (MobileUniqueID,UserID,Type,FirstName,MiddleName,LastName,EmailID,MobileCountryCode,MobileNo,LandlineCountryCode,LandlineAreaCode,LandlineNo,Tag,Status,CreatedDate,CreatedBy) values 
                ('".$item['uniqueid']."','$userid','".$item['type']."','".$item['first_name']."','".$item['middle_name']."','".$item['last_name']."','".$item['emailid']."','".$item['mobile_cc']."','".$item['mobile']."','".$item['landline_cc']."','".$item['landline_area_cc']."','".$item['landline']."','".$item['tag']."','".$item['status']."',now(),'$userid')";
            //echo $q;exit;
        }
        //echo $q;exit;
        //exit;
    mysql_query($q);
    }
    $check_insert_or_update = mysql_affected_rows();
    if($check_insert_or_update)
    {
        echo "{".$paid_status.json_encode('PLANPIPER_PATIENT_CONTACTS').':'.json_encode('1')."}";
    }
   else
   {
        echo "{".$paid_status.json_encode('PLANPIPER_PATIENT_CONTACTS').':'.json_encode('0')."}";
   }
}
//*****************************END OF ADD/EDIT/DELETE PATIENT CONTACTS************************************************

/******************************ADD REPORT**************************************************************************/
elseif($_REQUEST['RequestType']=="add_folder" && $_REQUEST['user_id']!="")
{
    $userid             = (empty($_REQUEST['user_id']))             ? '' : $_REQUEST['user_id'];
    $report_name        = (empty($_REQUEST['report_name']))         ? '' : $_REQUEST['report_name'];
    $report_given_by    = (empty($_REQUEST['report_given_by']))     ? '' : $_REQUEST['report_given_by'];
    $report_created_on  = (empty($_REQUEST['report_created_on']))   ? '' : $_REQUEST['report_created_on'];
    $type_of_report     = (empty($_REQUEST['type_of_report']))      ? '' : $_REQUEST['type_of_report'];/*1-Prescription,2-Lab Report*/

    $insert_report      = "insert into MYFOLDER_PARENT 
                            (UserID,ReportName,ReportGivenBy,ReportCreatedOn,TypeOfReport,CreatedDate,CreatedBy) VALUES
                            ('$userid','$report_name','$report_given_by','$report_created_on','$type_of_report',now(),'$userid')";
    $insert_qry         = mysql_query($insert_report);
    $report_id          = mysql_insert_id();
    $check_insert       = mysql_affected_rows();

    $report_timestamp   = mysql_result(mysql_query("select max(UpdatedDate) from MYFOLDER_PARENT where UserID='$userid'"),0);

    if($check_insert)
    {
        echo "{".json_encode('PLANPIPER_ADD_REPORT').':'.json_encode("1").","
                .json_encode('PLANPIPER_REPORT_TIMESTAMP').':'.json_encode($report_timestamp).","
                .json_encode('PLANPIPER_ADDED_REPORT_ID').':'.json_encode($report_id)."}";
    }
    else
    {
        $last_insert_id = ""; $report_timestamp = "";
        echo "{".json_encode('PLANPIPER_ADD_REPORT').':'.json_encode("0").","
                .json_encode('PLANPIPER_REPORT_TIMESTAMP').':'.json_encode($report_timestamp).","
                .json_encode('PLANPIPER_ADDED_REPORT_ID').':'.json_encode($last_insert_id)."}";
    }
}
/******************************END OF ADD REPORT********************************************************************/

/******************************UPLOAD FILE***************************************************************************/
elseif($_REQUEST['RequestType']=="file_upload" && $_REQUEST['user_id']!=""  && $_REQUEST['report_id']!="" && $_REQUEST['image_name']!="")
{
    $userid             = (empty($_REQUEST['user_id']))         ? '' : $_REQUEST['user_id'];
    $report_id          = (empty($_REQUEST['report_id']))       ? '' : $_REQUEST['report_id'];
    $image_name         = (empty($_REQUEST['image_name']))      ? '' : $_REQUEST['image_name'];
    $file               = (empty($_FILES['filename']['name']))  ? '' : $_FILES['filename']['name'];

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
    $path1              = "uploads/folder/";
    $path2              = "uploads/folder/$country_id$mobile_no/";

    if(!is_dir($path1)){
    mkdir($path1, 0777, true);
    }
    if(!is_dir($path2)){
    mkdir($path2, 0777, true);
    }
    
    if ($file && is_dir($path2)){
        //Image Name generated by server
        /*$random_no      = mt_rand();
        $split_filename = explode('.', $file);
        $file_name      = reset($split_filename);
        $file_extension = end($split_filename);
        $full_filename  = $random_no."~_".$file_name.'.'.$file_extension;
        $file_path      = $path2.$full_filename; */  

        //Image Name generated by mobile
        $file_path      = $path2.$image_name;

        move_uploaded_file($_FILES['filename']['tmp_name'], $file_path);

        /*Insert data into MyFolder_Child table*/
       /* $insert_image_info      = "insert into MYFOLDER_CHILD
                                (MyFolderParentID,FileName,Status,CreatedDate,CreatedBy) VALUES
                                ('$report_id','$image_name','A',now(),'$userid')";
        $insert_image_info_qry  = mysql_query($insert_image_info);
        $check_insert           = mysql_affected_rows();
        if($check_insert)
        {
             echo "{".json_encode('PLANPIPER_ADD_FOLDER').':'.json_encode("1")."}";
        }
        else
        {
             echo "{".json_encode('PLANPIPER_ADD_FOLDER').':'.json_encode("0")."}";
        }
        */   
        echo "{".$paid_status.json_encode('PLANPIPER_ADD_FOLDER').':'.json_encode("1")."}";
    }
    else
    {
        echo "{".$paid_status.json_encode('PLANPIPER_ADD_FOLDER').':'.json_encode("0")."}";  
    }
}
/******************************END OF UPLOAD FILE****************************************************************/

/******************************GET REPORTS***************************************************************************/
elseif($_REQUEST['RequestType']=="reports" && $_REQUEST['user_id']!="" )
{
    $userid     = (empty($_REQUEST['user_id']))     ? '' : $_REQUEST['user_id'];
    $report_id  = (empty($_REQUEST['report_id']))   ? '' : $_REQUEST['report_id'];
    $timestamp  = (empty($_REQUEST['timestamp']))   ? '' : $_REQUEST['timestamp'];

    /*GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/
    $country_id         = "";   $country_code="";   $mobile_no="";
    $get_folder_name    = "select substr(MobileNo,1,5) as CountryCode,substr(MobileNo,6) as MobileNo,
                            if(PaidUntil>now(),'Y','N') as Paid from USER_ACCESS where UserID='$userid'";
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
        $paid           = (empty($row['Paid']))     ? '' : $row['Paid'];
    //echo $country_id.$mobile_no;
    }
    /*END OF GET USER'S MOBILE NUMBER and COUNTRYID AND CREATE FOLDER SO THAT ALL UPLOADS FROM WEB and MOBILE are uploaded here*/

    $path       = "/uploads/folder/$country_id$mobile_no/";  
    $reports    = array();

    /*GET INDIVIDUAL REPORT in Edit Page*/
    if($report_id!="")
    {
        $append_query1 = " and ID='$report_id'";
    }
    else
    {
        $append_query1 = "";
    }

    /*GET ONLY THOSE REPORTS WHOSE TIMESTAMP ARE greater than $timestamp*/
    if($timestamp!="")
    {
        $append_query2 = " and UpdatedDate>'$timestamp'";
    }
    else
    {
        $append_query2 = "";
    }

    $max_timestamp      = mysql_result(mysql_query("select max(UpdatedDate) from MYFOLDER_PARENT where UserID='$userid'"),0);

    /*$get_reports        =   "select ID,ReportName,ReportGivenBy,ReportCreatedOn,TypeOfReport,CreatedDate,Status
                            from MYFOLDER_PARENT where UserID='$userid' and Status='A' $append_query1 $append_query2
                            order by CreatedDate desc";*/
    $get_reports        =   "select ID,ReportName,ReportGivenBy,ReportCreatedOn,TypeOfReport,CreatedDate,Status
                            from MYFOLDER_PARENT where UserID='$userid' $append_query1 $append_query2
                            order by CreatedDate desc";
                            /*
    Removed "and Status='A'" from the query coz vishnu wanted inactive reports also.
                            */
    //echo $get_reports;exit;
    $get_reports_qry    = mysql_query($get_reports);
    $get_reports_count  = mysql_num_rows($get_reports_qry);
    if($get_reports_count>0)
    {
        while($analytics_row = mysql_fetch_array($get_reports_qry))
        {
            $id                     = (empty($analytics_row['ID']))             ? '' : $analytics_row['ID'];
            $res['ReportID']        = $id;
            $res['ReportName']      = (empty($analytics_row['ReportName']))     ? '' : $analytics_row['ReportName'];
            $res['ReportGivenBy']   = (empty($analytics_row['ReportGivenBy']))  ? '' : $analytics_row['ReportGivenBy'];
            $res['ReportCreatedOn'] = (empty($analytics_row['ReportCreatedOn']))? '' : date('Y-m-d',strtotime($analytics_row['ReportCreatedOn']));
            $res['TypeOfReport']    = (empty($analytics_row['TypeOfReport']))   ? '' : $analytics_row['TypeOfReport'];
            $res['Status']          = (empty($analytics_row['Status']))         ? '' : $analytics_row['Status'];
            $res['CreatedDate']     = (empty($analytics_row['CreatedDate']))    ? '' : $analytics_row['CreatedDate'];
            $image_urls = array();
            if($id!="")
            {
                $get_image_urls     = "select ID,FileName,DisplayName,Status from MYFOLDER_CHILD where MyFolderParentID='$id' and Status='A'";
                $get_image_urls_qry = mysql_query($get_image_urls);
                $get_image_urls_count=mysql_num_rows($get_image_urls_qry);
                
                if($get_image_urls_count>0)
                {
                    while($image_rows = mysql_fetch_array($get_image_urls_qry))
                    {
                        $img['ReportChildID']   = $image_rows['ID'];
                        $img['FileName']        = "http://".$host_server.$path.$image_rows['FileName'];
                        $img['DisplayName']     = $image_rows['DisplayName'];
                        $img['Status']          = $image_rows['Status'];
                    array_push($image_urls,$img);
                    }
                }
                if(!empty($image_urls))
                {
                    $res['FileNames']     = $image_urls;
                }
                else
                {
                   $res['FileNames']     = $image_urls; 
                }
                
            }
            else
            {
            $res['FileNames']     = $image_urls;
            }
            
        array_push($reports,$res);
        //echo "<pre>";print_r($reports);
        }
        //echo $payment_status;exit;
        if($payment_status=='Y')
        {
            echo "{".$paid_status.json_encode('PLANPIPER_REPORTS').':'.json_encode($reports).","
            .json_encode('PLANPIPER_REPORTS_TIMESTAMP').':'.json_encode($max_timestamp)."}";
        }
        else
        {
            $max_timestamp = ""; unset($reports);
            echo "{".$paid_status.json_encode('PLANPIPER_REPORTS').':'.json_encode($reports).","
            .json_encode('PLANPIPER_REPORTS_TIMESTAMP').':'.json_encode($max_timestamp)."}";
        }
    }
    else
    {
    $max_timestamp = "";
    echo "{".$paid_status.json_encode('PLANPIPER_REPORTS').':'.json_encode($reports).","
            .json_encode('PLANPIPER_REPORTS_TIMESTAMP').':'.json_encode($max_timestamp)."}"; 
    }

}
/******************************END OF GET REPORTS****************************************************************/

//*****************************ADD/EDIT/DELETE REPORTS************************************************
elseif($_REQUEST['RequestType']=="add_edit_reports" && $_REQUEST['user_id']!="" && $_REQUEST['response']!="")
{
$userid = (empty($_REQUEST['user_id']))  ? '' : $_REQUEST['user_id'];
$json   = stripslashes($_REQUEST['response']);
//echo "<pre>";print_r($json);exit;
$obj    = json_decode($json,true);
//echo "<pre>";print_r($obj);exit;


    if($payment_status=='Y')
    {
        if(!empty($obj))
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



            foreach($obj as $report)
            {
                $check_duplicate        = mysql_query("select ID from MYFOLDER_PARENT where ID='".$report['ParentID']."'");
                $check_duplicate_count  = mysql_num_rows($check_duplicate);
                //echo $check_duplicate;exit;
                if($check_duplicate_count==1)
                {   
                    $parent_query = "update MYFOLDER_PARENT set ReportName='".$report['ReportName']."',
                                    ReportGivenBy='".$report['ReportGivenBy']."',ReportCreatedOn='".$report['ReportCreatedOn']."',
                                    TypeOfReport='".$report['TypeOfReport']."',Status='".$report['Status']."',
                                    SourceofUpload='M',UpdatedDate=now(),UpdatedBy='$userid'
                                    where ID='".$report['ParentID']."'";

                    if($report['Status']=='I')
                    {
                        $update_child           = "update MYFOLDER_CHILD set Status='I',UpdatedBy='$userid' where MyFolderParentID='".$report['ParentID']."'";
                        $update_child_query     = mysql_query($update_child);
                        $check_update           = mysql_affected_rows();
                        if($check_update)
                        {
                        mysql_query("update MYFOLDER_PARENT set UpdatedDate=current_timestamp where ID='".$report['ParentID']."'");
                        }

                        $get_file_names         = "select FileName from MYFOLDER_CHILD where MyFolderParentID='".$report['ParentID']."' and Status='I'";
                        $get_file_names_qry     = mysql_query($get_file_names);
                        $get_file_names_count   = mysql_num_rows($get_file_names_qry);
                        if($get_file_names_count>0)
                        {
                            while($file_names_row = mysql_fetch_row($get_file_names_qry))
                            {
                                foreach($file_names_row as $val)
                                {
                                    if(file_exists($path.$val)){
                                        unlink($path.$val);
                                    }
                                }
                            }
                        }   

                        if(is_dir($path))
                        {
                            if (count(glob("$path/*")) === 0 )
                            {
                                 rmdir($path);
                            }
                        }
                        //mysql_query("update MYFOLDER_PARENT set UpdatedDate=current_timestamp where ID='$report['ParentID']'");
                    }
                    else
                    {
                        foreach($report['Files'] as $file_info)
                        {
                            //echo "<pre>";print_r($abc);
                            //echo $item['SelfPlanHeaderID'];exit;
                            $check_duplicate_act        = mysql_query("select ID from MYFOLDER_CHILD where ID='".$file_info['ChildID']."'");
                            $check_duplicate_act_count  = mysql_num_rows($check_duplicate_act);
                            //echo $check_duplicate;exit;
                            if($check_duplicate_act_count==1)
                            {
                            $child_query = "update MYFOLDER_CHILD set FileName='".$file_info['FileName']."',
                                        DisplayName='".$file_info['DisplayName']."',Status='".$file_info['Status']."',
                                        UpdatedDate=now(),UpdatedBy='$userid'
                                        where ID='".$file_info['ChildID']."' and MyFolderParentID='".$report['ParentID']."'";

                                /*Delete Files from Folder*/
                                //echo $path.$val."<br>";
                                if($file_info['Status']=='I')
                                {
                                    if(file_exists($path.$file_info['FileName'])){
                                        unlink($path.$file_info['FileName']);
                                    }

                                    if(is_dir($path))
                                    {
                                        if (count(glob("$path/*")) === 0 )
                                        {
                                             rmdir($path);
                                        }
                                    }
                                } 
                                /*End of Delete Files from Folder*/
                            }
                            else
                            {
                                //echo $item['Activities']['ActivityID'];
                            $child_query = "insert into MYFOLDER_CHILD 
                                        (ID,MyFolderParentID,FileName,DisplayName,Status,CreatedDate,CreatedBy) values 
                                        ('".$file_info['ChildID']."','".$report['ParentID']."','".$file_info['FileName']."','".$file_info['DisplayName']."','".$file_info['Status']."',now(),'$userid')";
                            }
                        mysql_query($child_query);
                        $check_update    = mysql_affected_rows();
                            if($check_update)
                            {
                            mysql_query("update MYFOLDER_PARENT set UpdatedDate=current_timestamp where ID='".$report['ParentID']."'");
                            }
                        }
                    }    
                mysql_query($parent_query);
                }
                else
                {       
                    $parent_query = "insert into MYFOLDER_PARENT 
                                (ID,UserID,ReportName,ReportGivenBy,ReportCreatedOn,TypeOfReport,Status,SourceofUpload,CreatedDate,CreatedBy) values 
                                ('".$report['ParentID']."','$userid','".$report['ReportName']."','".$report['ReportGivenBy']."','".$report['ReportCreatedOn']."','".$report['TypeOfReport']."','".$report['Status']."','M',now(),'$userid')";
                    
                    foreach($report['Files'] as $file_info)
                    {
                    //echo $item['Activities']['ActivityID'];
                    $child_query = "insert into MYFOLDER_CHILD 
                                (ID,MyFolderParentID,FileName,DisplayName,Status,CreatedDate,CreatedBy) values 
                                ('".$file_info['ChildID']."','".$report['ParentID']."','".$file_info['FileName']."','".$file_info['DisplayName']."','".$file_info['Status']."',now(),'$userid')";
                    mysql_query($child_query);
                    }
                mysql_query($parent_query);
                }
            //echo $plan_query."<br>";
            }
            echo "{".$paid_status.json_encode('PLANPIPER_REPORTS').':'.json_encode("1")."}";
        }
    }
    else
    {
        echo "{".$paid_status.json_encode('PLANPIPER_REPORTS').':'.json_encode("0")."}";
    }
}
//*****************************END OF ADD/EDIT/DELETE REPORTS************************************************

/*******UPDATE DISPLAY FILE NAME ie; DisplayFileName Column in Databse when there is Internet Connection*****/
elseif($_REQUEST['RequestType']=="update_display_filename" && $_REQUEST['user_id']!=""  && $_REQUEST['report_id']!="" 
&& $_REQUEST['report_child_id']!="")
{
    $userid                 = (empty($_REQUEST['user_id']))             ? '' : $_REQUEST['user_id'];
    $report_id              = (empty($_REQUEST['report_id']))           ? '' : $_REQUEST['report_id'];
    $report_child_id        = (empty($_REQUEST['report_child_id']))     ? '' : $_REQUEST['report_child_id'];
    $display_name           = (empty($_REQUEST['display_name']))        ? '' : $_REQUEST['display_name'];

    $update_display_name    = "update MYFOLDER_CHILD set DisplayName='$display_name', UpdatedBy='$userid' where 
                                ID='$report_child_id' and MyFolderParentID='$report_id'";
    //echo $update_display_name;exit;
    $update_display_name_qry= mysql_query($update_display_name);
    $check_update           = mysql_affected_rows();
    if($check_update)
    {
        echo "{".$paid_status.json_encode('PLANPIPER_UPDATE_DISPLAY_FILENAME').':'.json_encode("1")."}";
    }
    else
    {
        echo "{".$paid_status.json_encode('PLANPIPER_UPDATE_DISPLAY_FILENAME').':'.json_encode("0")."}";
    }
}
/****END OF UPDATE DISPLAY FILE NAME ie; DisplayFileName Column in Databse when there is Internet Connection****/

/******************************DELETE UPLOADED FILE FROM DB AND SERVER*******************************************/
elseif($_REQUEST['RequestType']=="update_folder" && $_REQUEST['user_id']!=""  && $_REQUEST['report_id']!="" && $_REQUEST['image_names']!="")
{
    $userid             = (empty($_REQUEST['user_id']))             ? '' : $_REQUEST['user_id'];
    $report_id          = (empty($_REQUEST['report_id']))           ? '' : $_REQUEST['report_id'];
    $report_name        = (empty($_REQUEST['report_name']))         ? '' : $_REQUEST['report_name'];
    $report_given_by    = (empty($_REQUEST['report_given_by']))     ? '' : $_REQUEST['report_given_by'];
    $report_created_on  = (empty($_REQUEST['report_created_on']))   ? '' : $_REQUEST['report_created_on'];
    $type_of_report     = (empty($_REQUEST['type_of_report']))      ? '' : $_REQUEST['type_of_report'];/*1-Prescription,2-Lab Report*/
    $image_names        = (empty($_REQUEST['image_names']))         ? '' : $_REQUEST['image_names'];
    $explode_image_name = explode(",",$image_names);
    //$image_names        = array("123.jpeg","456.jpeg"); 
   // echo "<pre>";print_r($explode_image_name);exit;

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
    //echo $c= sizeof($image_names);
    foreach($explode_image_name as $val)
    {
        //echo $val;
    $update         = "update MYFOLDER_CHILD set Status='I',UpdatedBy='$userid' where MyFolderParentID='$report_id' and FileName='$val';";
    //echo $update;exit;
    $update_query   = mysql_query($update);
    //echo $path.$val."<br>";
        if(file_exists($path.$val)){
            unlink($path.$val);
        }
    }

    //exit;

    if(is_dir($path))
    {
        if (count(glob("$path/*")) === 0 )
        {
             rmdir($path);
        }
    }

    $update_report =    "update MYFOLDER_PARENT set ReportName='$report_name',ReportGivenBy='$report_given_by',
                        ReportCreatedOn='$report_created_on',TypeOfReport='$type_of_report',UpdatedBy='$userid',
                        UpdatedDate=current_timestamp
                        where ID='$report_id'";
    $update_report_qry = mysql_query($update_report);
    if($update_report_qry || $update_query)
    {
        echo "{".json_encode('PLANPIPER_REPORTS').':'.json_encode("1")."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_REPORTS').':'.json_encode("0")."}";
    }
}
/******************************END OF DELETE UPLOADED FILE FROM DB AND SERVER*******************************************/



/******************************DELETE REPORT(both parent and child tables) AND ASSOCIATED FILES*************************/
elseif($_REQUEST['RequestType']=="delete_folder" && $_REQUEST['user_id']!=""  && $_REQUEST['report_id']!="")
{
    $userid             = (empty($_REQUEST['user_id']))             ? '' : $_REQUEST['user_id'];
    $report_id          = (empty($_REQUEST['report_id']))           ? '' : $_REQUEST['report_id'];
    //echo "<pre>";print_r($image_names);exit;

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

    $update_child       = "update MYFOLDER_CHILD set Status='I',UpdatedBy='$userid' where MyFolderParentID='$report_id'";
    $update_child_query = mysql_query($update_child);

    $update_parent      = "update MYFOLDER_PARENT set Status='I',UpdatedBy='$userid' where ID='$report_id'";
    $update_parent_query= mysql_query($update_parent);

    $get_file_names         = "select FileName from MYFOLDER_CHILD where MyFolderParentID='$report_id' and Status='I'";
    $get_file_names_qry     = mysql_query($get_file_names);
    $get_file_names_count   = mysql_num_rows($get_file_names_qry);
    if($get_file_names_count>0)
    {
        while($file_names_row = mysql_fetch_row($get_file_names_qry))
        {
            foreach($file_names_row as $val)
            {
                if(file_exists($path.$val)){
                    unlink($path.$val);
                }
            }
        }
    }   

    if(is_dir($path))
    {
        if (count(glob("$path/*")) === 0 )
        {
             rmdir($path);
        }
    }

    if($update_child_query || $update_parent_query)
    {
        echo "{".json_encode('PLANPIPER_DELETE_REPORT').':'.json_encode("1")."}";
    }
    else
    {
        echo "{".json_encode('PLANPIPER_DELETE_REPORT').':'.json_encode("0")."}";
    }
}
/******************************END OF DELETE REPORT FROM DB AND SERVER*******************************************/

/******************************Getting User Self Plan******************************************************************/
elseif($_REQUEST['RequestType']=="self_plans" && $_REQUEST['userid']!="")
{
    $userid             = (empty($_REQUEST['userid']))             ? '' : $_REQUEST['userid'];
    $self_plans         = array();
    $get_self_plans     =   "select ID,UserID,MerchantID,PlanCode,PlanName,Status
                            from USER_SELF_PLAN_HEADER 
                            where UserID='$userid' and Status='A'";
    //echo $get_self_plans;exit;
    $get_self_plans_qry = mysql_query($get_self_plans);
    $get_self_plan_count= mysql_num_rows($get_self_plans_qry);
    if($get_self_plan_count>0)
    {
        while($self_plan_rows   = mysql_fetch_array($get_self_plans_qry))
        {
            $self_plan_code                 = $self_plan_rows['PlanCode'];
            $res1['UserID']                 = $self_plan_rows['UserID'];
            $res1['MerchantID']             = $self_plan_rows['MerchantID'];
            $res1['PlanCode']               = $self_plan_code;
            $res1['PlanName']               = $self_plan_rows['PlanName'];
            $res1['PlanStatus']             = $self_plan_rows['Status'];
            $activities         = array();
            if($self_plan_code)
            {
                $get_activities =   "select `ActivityID`,`UserID`,`PlanCode`,`SectionID`,`PrescriptionNo`,`RowNo`,
                                    `DoctorsName`,`Name`,`Description`,`MedicineCount`,`MedicineType`,`ActionText`,
                                    `When`,`Instruction`,`Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,
                                    `IsCritical`,`ResponseRequired`,`Date`,`Time`,`AppointmentDuration`,`AppointmentPlace`,`SelfTestID`,`RemindBefore`,`Status`
                                    from USER_SELF_PLAN_ACTIVITIES where UserID='".$res1['UserID']."' 
                                    and PlanCode = '".$res1['PlanCode']."' and Status='A'";
               // echo $get_activities."<br>";exit;
                $get_act_qry    = mysql_query($get_activities);
                $get_act_count  = mysql_num_rows($get_act_qry);
                if($get_act_count>0)
                {
                    while($activity_rows   = mysql_fetch_assoc($get_act_qry))
                    {
                    $res2['ActivityID']         = (empty($activity_rows['ActivityID']))     ? '': $activity_rows['ActivityID'];
                    $res2['UserID']             = (empty($activity_rows['UserID']))         ? '': $activity_rows['UserID'];
                    $res2['PlanCode']           = (empty($activity_rows['PlanCode']))       ? '': $activity_rows['PlanCode'];
                    $res2['SectionID']          = (empty($activity_rows['SectionID']))      ? '': $activity_rows['SectionID'];
                    $res2['PrescriptionNo']     = (empty($activity_rows['PrescriptionNo'])) ? '': $activity_rows['PrescriptionNo'];
                    $res2['RowNo']              = (empty($activity_rows['RowNo']))          ? '': $activity_rows['RowNo'];
                    $res2['DoctorsName']        = (empty($activity_rows['DoctorsName']))    ? '': $activity_rows['DoctorsName'];
                    $res2['Name']               = (empty($activity_rows['Name']))           ? '': $activity_rows['Name'];
                    $res2['Description']        = (empty($activity_rows['Description']))    ? '': $activity_rows['Description'];
                   // $res2['MedicineCount']      = (empty($activity_rows['MedicineCount']))  ? '': $activity_rows['MedicineCount'];
                    $res2['MedicationCount']      = (empty($activity_rows['MedicineCount']))  ? '': $activity_rows['MedicineCount'];
                    //$res2['MedicineType']       = (empty($activity_rows['MedicineType']))   ? '': $activity_rows['MedicineType'];
                    $res2['MedicationType']       = (empty($activity_rows['MedicineType']))   ? '': $activity_rows['MedicineType'];
                    $res2['ActionText']         = (empty($activity_rows['ActionText']))     ? '': $activity_rows['ActionText'];
                    $res2['When']               = (empty($activity_rows['When']))           ? '': $activity_rows['When'];
                    $res2['Instruction']        = (empty($activity_rows['Instruction']))    ? '': $activity_rows['Instruction'];
                    $res2['Frequency']          = (empty($activity_rows['Frequency']))      ? '': $activity_rows['Frequency'];
                    $res2['FrequencyString']    = (empty($activity_rows['FrequencyString']))? '': $activity_rows['FrequencyString'];
                    $res2['HowLong']            = (empty($activity_rows['HowLong']))        ? '': $activity_rows['HowLong'];
                    $res2['HowLongType']        = (empty($activity_rows['HowLongType']))    ? '': $activity_rows['HowLongType'];
                    $res2['IsCritical']         = (empty($activity_rows['IsCritical']))     ? '': $activity_rows['IsCritical'];
                    $res2['ResponseRequired']   = (empty($activity_rows['ResponseRequired']))? '': $activity_rows['ResponseRequired'];
                    $res2['Date']               = (empty($activity_rows['Date']))           ? '': $activity_rows['Date'];
                    $res2['Time']               = (empty($activity_rows['Time']))           ? '': $activity_rows['Time'];
                     $res2['AppointmentDuration']               = (empty($activity_rows['AppointmentDuration']))           ? '': $activity_rows['AppointmentDuration'];
                    $res2['AppointmentPlace']               = (empty($activity_rows['AppointmentPlace']))           ? '': $activity_rows['AppointmentPlace'];
                    $res2['SelfTestID']         = (empty($activity_rows['SelfTestID']))     ? '': $activity_rows['SelfTestID'];
                    $res2['RemindBefore']       = (empty($activity_rows['RemindBefore']))   ? '': $activity_rows['RemindBefore'];
                    $res2['Status']             = (empty($activity_rows['Status']))         ? '': $activity_rows['Status'];
                    array_push($activities,$res2);
                    }
                }
            }
            $res1['Activities'] = $activities;
        array_push($self_plans,$res1); 
        }
    //echo "<pre>";print_r($self_plans);
    echo "{".$paid_status.json_encode('PLANPIPER_SELFPLANS').':'.json_encode($self_plans)."}";
    }
    else
    {
    $self_plans = "";
        echo "{".$paid_status.json_encode('PLANPIPER_SELFPLANS').':'.json_encode($self_plans)."}";
    }
}
/******************************End of getting User Self Plan************************************************************/

//*****************************ADD/EDIT/DELETE SELF PLANS************************************************
elseif($_REQUEST['RequestType']=="add_edit_self_plan" && $_REQUEST['userid']!="" && $_REQUEST['response']!="")
{
$userid = (empty($_REQUEST['userid']))  ? '' : $_REQUEST['userid'];
$json   = stripslashes($_REQUEST['response']);
//echo "<pre>";print_r($json);exit;
$obj    = json_decode($json,true);
//echo "<pre>";print_r($obj);exit;

    foreach($obj as $plan)
    {
        $check_duplicate        = mysql_query("select PlanCode from USER_SELF_PLAN_HEADER where PlanCode='".$plan['PlanCode']."' and UserID='".$plan['UserID']."'");
        $check_duplicate_count  = mysql_num_rows($check_duplicate);
        //echo $check_duplicate;exit;
        if($check_duplicate_count==1)
        {
            $plan_query = "update USER_SELF_PLAN_HEADER set PlanName='".$plan['PlanName']."',
                            Status='".$plan['PlanStatus']."',UpdatedDate=now(),UpdatedBy='$userid'
                            where PlanCode='".$plan['PlanCode']."' and UserID='".$plan['UserID']."'";

            foreach($plan['Activities'] as $act)
            {
                //echo "<pre>";print_r($abc);
                //echo $item['SelfPlanHeaderID'];exit;
                $check_duplicate_act        = mysql_query("select PlanCode from USER_SELF_PLAN_ACTIVITIES 
                                                where PlanCode='".$act['PlanCode']."' and UserID='".$act['UserID']."'
                                                and ActivityID='".$act['ActivityID']."'");
                $check_duplicate_act_count  = mysql_num_rows($check_duplicate_act);
                //echo $check_duplicate;exit;
                if($check_duplicate_act_count==1)
                {
                    $activity_qry    = "";
                    $activity_qry.="update USER_SELF_PLAN_ACTIVITIES set ";
                    if($act['SectionID']=='1')
                    {
                        // $activity_qry.= " PrescriptionNo='".$act['PrescriptionNo']."',RowNo='".$act['RowNo']."',
                        //                 DoctorsName='".$act['DoctorsName']."',Name='".$act['Name']."',
                        //                 Description='".$act['Description']."',MedicineCount='".$act['MedicineCount']."',
                        //                 MedicineType='".$act['MedicineType']."',ActionText='".$act['ActionText']."',
                        //                 `When`='".$act['When']."',Instruction='".$act['Instruction']."',
                        //                 Frequency='".$act['Frequency']."',FrequencyString='".$act['FrequencyString']."',
                        //                 HowLong='".$act['HowLong']."',HowLongType='".$act['HowLongType']."',
                        //                 IsCritical='".$act['IsCritical']."',ResponseRequired='".$act['ResponseRequired']."',
                        //                 Date='".$act['Date']."',Time='".$act['Time']."'";
                     $activity_qry.= " PrescriptionNo='".$act['PrescriptionNo']."',RowNo='".$act['RowNo']."',
                                        DoctorsName='".$act['DoctorsName']."',Name='".$act['Name']."',
                                        Description='".$act['Description']."',MedicineCount='".$act['MedicationCount']."',
                                        MedicineType='".$act['MedicationType']."',ActionText='".$act['ActionText']."',
                                        `When`='".$act['When']."',Instruction='".$act['Instruction']."',
                                        Frequency='".$act['Frequency']."',FrequencyString='".$act['FrequencyString']."',
                                        HowLong='".$act['HowLong']."',HowLongType='".$act['HowLongType']."',
                                        IsCritical='".$act['IsCritical']."',ResponseRequired='".$act['ResponseRequired']."',
                                        Date='".$act['Date']."',Time='".$act['Time']."'";
                    }
                    elseif($act['SectionID']=='3-1')
                    {
                        $activity_qry.= " SelfTestID='".$act['SelfTestID']."',RowNo='".$act['RowNo']."',
                                        Name='".$act['Name']."',Instruction='".$act['Instruction']."',
                                        Frequency='".$act['Frequency']."',FrequencyString='".$act['FrequencyString']."',HowLong='".$act['HowLong']."',
                                        HowLongType='".$act['HowLongType']."', Date='".$act['Date']."',Time='".$act['Time']."' ";
                    }
                    elseif($act['SectionID']=='2')
                    {
                        // $activity_qry.= " Date='".$act['Date']."',Time='".$act['Time']."',
                        //                 DoctorsName='".$act['DoctorsName']."',Name='".$act['Name']."',
                        //                 Description='".$act['Description']."'";
                        $activity_qry.= " Date='".$act['Date']."',Time='".$act['Time']."',
                                        DoctorsName='".$act['DoctorsName']."',Name='".$act['Name']."',
                                        Description='".$act['Description']."',AppointmentDuration='".$act['AppointmentDuration']."',AppointmentPlace='".$act['AppointmentPlace']."'";
                    }
                    elseif($act['SectionID']=='7')
                    {
                        $activity_qry.= " Name='".$act['Name']."',Frequency='".$act['Frequency']."',
                                        FrequencyString='".$act['FrequencyString']."',
                                        HowLong='".$act['HowLong']."',HowLongType='".$act['HowLongType']."',
                                        IsCritical='".$act['IsCritical']."',Description='".$act['Description']."',
                                        Date='".$act['Date']."',Time='".$act['Time']."'";
                    }
                    $activity_qry.=",Status='".$act['Status']."',UpdatedDate=now(),UpdatedBy='$userid'
                                    where PlanCode='".$plan['PlanCode']."' and UserID='".$plan['UserID']."' 
                                    and ActivityID='".$act['ActivityID']."'";
                //echo $activity_qry."<br>";
                }
                else
                {
                //echo $item['Activities']['ActivityID'];
                $activity_qry = "insert into USER_SELF_PLAN_ACTIVITIES 
                (`ActivityID`,`UserID`,`PlanCode`,`SectionID`,`PrescriptionNo`,`RowNo`,
                `DoctorsName`,`Name`,`Description`,`MedicineCount`,`MedicineType`,
                `ActionText`,`When`,`Instruction`,`Frequency`,`FrequencyString`,`HowLong`,
                `HowLongType`,`IsCritical`,`ResponseRequired`,`Date`,`Time`,`SelfTestID`,
                `Status`,CreatedDate,CreatedBy) 
                values 
                ('".$act['ActivityID']."','".$plan['UserID']."','".$plan['PlanCode']."','".$act['SectionID']."',
                '".$act['PrescriptionNo']."','".$act['RowNo']."','".$act['DoctorsName']."','".$act['Name']."',
                '".$act['Description']."','".$act['MedicationCount']."','".$act['MedicationType']."',
                '".$act['ActionText']."','".$act['When']."','".$act['Instruction']."','".$act['Frequency']."',
                '".$act['FrequencyString']."','".$act['HowLong']."','".$act['HowLongType']."','".$act['IsCritical']."',
                '".$act['ResponseRequired']."','".$act['Date']."','".$act['Time']."','".$act['SelfTestID']."',
                '".$act['Status']."',now(),'$userid')";
                }
            mysql_query($activity_qry);
            }
        mysql_query($plan_query);
        }
        else
        {       
            $plan_query = "insert into USER_SELF_PLAN_HEADER 
            (UserID,MerchantID,PlanCode,PlanName,Status,CreatedDate,CreatedBy) values 
            ('".$plan['UserID']."','".$plan['MerchantID']."','".$plan['PlanCode']."','".$plan['PlanName']."','".$plan['PlanStatus']."',now(),'$userid')";
            foreach($plan['Activities'] as $act)
            {
            //echo $item['Activities']['ActivityID'];
            $activity_qry = "insert into USER_SELF_PLAN_ACTIVITIES 
            (`ActivityID`,`UserID`,`PlanCode`,`SectionID`,`PrescriptionNo`,`RowNo`,
            `DoctorsName`,`Name`,`Description`,`MedicineCount`,`MedicineType`,
            `ActionText`,`When`,`Instruction`,`Frequency`,`FrequencyString`,`HowLong`,
            `HowLongType`,`IsCritical`,`ResponseRequired`,`Date`,`Time`,`SelfTestID`,
            `Status`,CreatedDate,CreatedBy) 
            values 
            ('".$act['ActivityID']."','".$plan['UserID']."','".$plan['PlanCode']."','".$act['SectionID']."',
            '".$act['PrescriptionNo']."','".$act['RowNo']."','".$act['DoctorsName']."','".$act['Name']."',
            '".$act['Description']."','".$act['MedicationCount']."','".$act['MedicationType']."',
            '".$act['ActionText']."','".$act['When']."','".$act['Instruction']."','".$act['Frequency']."',
            '".$act['FrequencyString']."','".$act['HowLong']."','".$act['HowLongType']."','".$act['IsCritical']."',
            '".$act['ResponseRequired']."','".$act['Date']."','".$act['Time']."','".$act['SelfTestID']."',
            '".$act['Status']."',now(),'$userid')";
            mysql_query($activity_qry);
            }
        mysql_query($plan_query);
        }
    //echo $plan_query."<br>";
    }
   echo "{".json_encode('PLANPIPER_SELF_PLANS').':'.json_encode('1')."}";
}
//*****************************END OF ADD/EDIT/DELETE SELF PLANS************************************************

//******************************GET MEDICAL TESTS*****************************************************************
elseif($_REQUEST['RequestType']=="medical_tests")
{
$timestamp              = (empty($_REQUEST['timestamp']))       ? '' : $_REQUEST['timestamp'];
if($timestamp)
{
    $append_query           = " where UpdatedDate > $timestamp";
}
else
{
    $append_query           = "";
}

$max_timestamp          = mysql_result(mysql_query("select max(UpdatedDate) from MEDICAL_TESTS"), 0);
$medical_tests          = array();
$get_medical_tests      = "select ID,TestName,Status from MEDICAL_TESTS $append_query order by ID";
$get_medical_tests_query= mysql_query($get_medical_tests);
$get_medical_tests_count= mysql_num_rows($get_medical_tests_query);
    if($get_medical_tests_count > 0)
    {
        while($medical_tests_row = mysql_fetch_array($get_medical_tests_query))
        {
            $med['ID']          = $medical_tests_row['ID'];
            $med['TestName']    = $medical_tests_row['TestName'];
            $med['Status']      = $medical_tests_row['Status'];
        array_push($medical_tests,$med);
        } 
    echo "{".json_encode('PLANPIPER_MEDICAL_TESTS').':'.json_encode($medical_tests).","
            .json_encode('PLANPIPER_MEDICAL_TESTS_TIMESTAMP').':'.json_encode($max_timestamp)."}";
    }
    else
    {
    $medical_tests = "";
    echo "{".json_encode('PLANPIPER_MEDICAL_TESTS').':'.json_encode($medical_tests).","
            .json_encode('PLANPIPER_MEDICAL_TESTS_TIMESTAMP').':'.json_encode($max_timestamp)."}";
    }
}
//******************************End of Medical Tests*************************************************************
elseif($_REQUEST['RequestType']=="notification_details" && $_REQUEST['notification_id']!="")
{
   // $user_id              = (empty($_REQUEST['user_id']))             ? '' : $_REQUEST['user_id'];
    $notification_id      = (empty($_REQUEST['notification_id']))     ? '' : $_REQUEST['notification_id'];
    $notification_details         = array();
    $get_notification_details     = "select NotificationID, NotificationTitle, NotificationContent,DocumentName, Link from MERCHANT_NOTIFICATIONS_DETAILS where NotificationID='$notification_id'";
    //echo $get_notification_details;exit;
    $get_notification_details_qry = mysql_query($get_notification_details);
    $get_notification_detail_count= mysql_num_rows($get_notification_details_qry);
    if($get_notification_detail_count>0)
    {
        while($notification_detail_rows   = mysql_fetch_array($get_notification_details_qry))
        {
            $notification_details['Status']                 = "1";
            $notification_details['NotificationID']         = (empty($notification_detail_rows['NotificationID']))    ? '': $notification_detail_rows['NotificationID'];
            $notification_details['NotificationTitle']      = (empty($notification_detail_rows['NotificationTitle']))    ? '': $notification_detail_rows['NotificationTitle'];
            $notification_details['NotificationContent']    = (empty($notification_detail_rows['NotificationContent']))    ? '': $notification_detail_rows['NotificationContent'];
            $notification_details['DocumentName']           = (empty($notification_detail_rows['DocumentName']))    ? '': $host_server."/uploads/generalnotification/".$notification_detail_rows['DocumentName'];
            $notification_details['Link']                   = (empty($notification_detail_rows['Link']))    ? '': $notification_detail_rows['Link'];
          //array_push($notification_details,$res);
        }
       // echo "{".json_encode('PLANPIPER_NOTIFICATION_DETAILS').':'.json_encode($notification_details)."}";
        echo json_encode($notification_details);
    }
    else
    {
        //echo "{".json_encode('PLANPIPER_NOTIFICATION_DETAILS').':'.json_encode($notification_details)."}";
            $notification_details['Status']                     = "0";
            $notification_details['NotificationID']             = "";
            $notification_details['NotificationTitle']          = "";
            $notification_details['NotificationContent']        = "";
            $notification_details['DocumentName']               = "";
            $notification_details['Link']                       = "";
            echo json_encode($notification_details);
    }
}
//******************************End of Notification Details*************************************************************
elseif($_REQUEST['RequestType']=="adpayment_details" && $_REQUEST['user_id']!="")
{
    $user_id              = (empty($_REQUEST['user_id']))             ? '' : $_REQUEST['user_id'];
    $adpayment_details         = array();
    $get_adpayment_details     = "select AdPaymentStatus, AdStartDate, AdEndDate from USER_DETAILS where UserID='$user_id'";
    //echo $get_adpayment_details;exit;
    $get_adpayment_details_qry = mysql_query($get_adpayment_details);
    $get_adpayment_detail_count= mysql_num_rows($get_adpayment_details_qry);
    if($get_adpayment_detail_count>0)
    {
        while($adpayment_detail_rows   = mysql_fetch_array($get_adpayment_details_qry))
        {
            $adpayment_details['Status']           = "1";
            $adpayment_details['AdPaymentStatus']  = (empty($adpayment_detail_rows['AdPaymentStatus']))    ? '': $adpayment_detail_rows['AdPaymentStatus'];
            $adpayment_details['AdStartDate']      = (empty($adpayment_detail_rows['AdStartDate']))    ? '': $adpayment_detail_rows['AdStartDate'];
            $adpayment_details['AdEndDate']        = (empty($adpayment_detail_rows['AdEndDate']))    ? '': $adpayment_detail_rows['AdEndDate'];

          //array_push($adpayment_details,$res);
        }
       // echo "{".json_encode('PLANPIPER_NOTIFICATION_DETAILS').':'.json_encode($adpayment_details)."}";
        echo json_encode($adpayment_details);
    }
    else
    {
        //echo "{".json_encode('PLANPIPER_NOTIFICATION_DETAILS').':'.json_encode($adpayment_details)."}";
            $adpayment_details['Status']                        = "0";
            $adpayment_details['AdPaymentStatus']               = "";
            $adpayment_details['AdStartDate']                   = "";
            $adpayment_details['AdEndDate']                     = "";
            echo json_encode($adpayment_details);
    }
}

elseif($_REQUEST['RequestType']=="plan_education_status" && $_REQUEST['user_id']!="" && $_REQUEST['plan_code']!="" && $_REQUEST['row_no']!="" && $_REQUEST['specific_date']!=""){


	$user_id = (empty($_REQUEST['user_id'])) ? '' : $_REQUEST['user_id'];

	$plan_code = (empty($_REQUEST['plan_code'])) ? '' : $_REQUEST['plan_code'];

	$row_no = (empty($_REQUEST['row_no'])) ? '' : $_REQUEST['row_no'];

	$specific_date = (empty($_REQUEST['specific_date'])) ? '' : $_REQUEST['specific_date'];

    $query_0 ="select * from USER_INSTRUCTION_DETAILS where UserID='$user_id' and  PlanCode = '$plan_code'  and SpecificDate = '$specific_date' and RowNo ='$row_no'";


    $result_0 = mysql_query($query_0);

    if(mysql_num_rows($result_0) > 0){
		    $data_0 = mysql_fetch_assoc($result_0);
		    $UserID = $data_0['UserID'];
		    $PrescriptionNo = $data_0['PrescriptionNo'];
		    $MedicineName = $data_0['MedicineName'];
		    $InstructionTypeID = $data_0['InstructionTypeID'];
		    $When = $data_0['When'];
		    $Instruction = $data_0['Instruction'];
		    $Frequency = $data_0['Frequency'];
		    $FrequencyString = $data_0['FrequencyString'];
		    $HowLong = $data_0['HowLong'];
		    $HowLongType = $data_0['HowLongType'];
		    $IsCritical = $data_0['IsCritical'];
		    $StartFlag = $data_0['StartFlag'];
		    $ResponseRequired = $data_0['ResponseRequired'];
		    $NoOfDaysAfterPlanStarts = $data_0['NoOfDaysAfterPlanStarts'];
		    $ThresholdLimit = $data_0['ThresholdLimit'];
		    $Status = $data_0['Status'];
		    $OriginalFileName = $data_0['OriginalFileName'];
		    $CreatedBy = $data_0['CreatedBy'];
		    $SpecificTime = $data_0['SpecificTime'];
		    $OldStartTime = $data_0['StartTime'];
		    $OldEndTime   = $data_0['EndTime'];
		    $time1 = strtotime($OldStartTime);
		    $time2 = strtotime($OldEndTime);
		    if($time2 < $time1) {
		        $time2 += 24 * 60 * 60;
		    }
		    $diff =  ($time2 - $time1) / 60;

			$i = 1;

		   while($i < 90){

			$newDate =  date('Y-m-d', strtotime($specific_date. ' + '.$i.' days'));

			$query="Select SEC_TO_TIME(SUM(TIME_TO_SEC(timediff(`EndTime`,`StartTime`)))) AS totalhours FROM USER_INSTRUCTION_DETAILS WHERE `SpecificDate` = '$newDate' and UserID='$user_id'";

			$result = mysql_query($query);

			$date= mysql_fetch_array($result);

			$totalhours = $date['totalhours'];

			if($totalhours != '' && $totalhours < '04:00:00'){


		    $query_1="select MAX(RowNo) from USER_INSTRUCTION_DETAILS where UserID='$user_id' and  SpecificDate = '$newDate'";

    		$result_1 = mysql_query($query_1);

    		$rowno = mysql_fetch_array($result_1);

    		$RowId = $rowno[0];

    		$query_2 ="select * from USER_INSTRUCTION_DETAILS where UserID='$user_id' and SpecificDate = '$newDate' and RowNo ='$RowId'";

    $result_2 = mysql_query($query_2);

    $data = mysql_fetch_assoc($result_2);

    $PlanCode = $data['PlanCode'];

    $StartTime = $data['StartTime'];

    $EndTime = $data['EndTime'];

    $NewEndTime = date("h:i A", strtotime($EndTime)+($diff*60));

    $nwerowid = $RowId + 1;

    if($PlanCode != '' && $StartTime !='' && $NewEndTime !='' && $newDate !=''){

    $delete_query = "DELETE FROM `USER_INSTRUCTION_DETAILS` WHERE UserID='$user_id' and  PlanCode = '$plan_code'  and SpecificDate = '$specific_date' and RowNo ='$row_no'";
    $delete_query_1 = mysql_query( $delete_query );

    $query_3 = "INSERT INTO `USER_INSTRUCTION_DETAILS` (`UserID`, `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `InstructionTypeID`, `When`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `ThresholdLimit`, `SpecificDate`, `Status`,`OriginalFileName`, `StartTime`, `EndTime`, `CreatedDate`, `CreatedBy`, `UpdatedDate`, `SpecificTime`, `ModifiedDate`) VALUES ('$UserID', '$PlanCode', '$PrescriptionNo','$nwerowid','$MedicineName', '$InstructionTypeID', '$When', '$Instruction','$Frequency','$FrequencyString','$HowLong','$HowLongType','$IsCritical','$ResponseRequired','$StartFlag','$NoOfDaysAfterPlanStarts','$ThresholdLimit','$newDate','$Status','$OriginalFileName','$EndTime','$NewEndTime',now(), '$CreatedBy',now(), '$SpecificTime',now())";

		        $result_3 = mysql_query( $query_3 );
				if($result_3){
				   echo '{
				            "MESSAGE" : "Successfully Update."
				        }';
				}
				else {
				   echo '{
				            "MESSAGE" : "Something Wrong Try Again"
				         }';
				}
				exit;
			  } else{

			  }
		    } else{
               
		    }
			$i++;
		}
    } else{
    	echo '{
				   "MESSAGE" : "Something Wrong Try Again"
			  }';
    }	

}

elseif($_REQUEST['RequestType']=="study_status" && $_REQUEST['user_id']!="" && $_REQUEST['plan_code']!="" && $_REQUEST['row_no']!="" && $_REQUEST['specific_date']!="" && $_REQUEST['education_status']){


	$user_id = (empty($_REQUEST['user_id'])) ? '' : $_REQUEST['user_id'];

	$plan_code = (empty($_REQUEST['plan_code'])) ? '' : $_REQUEST['plan_code'];

	$row_no = (empty($_REQUEST['row_no'])) ? '' : $_REQUEST['row_no'];

	$specific_date = (empty($_REQUEST['specific_date'])) ? '' : $_REQUEST['specific_date'];

	$education_status = (empty($_REQUEST['education_status'])) ? '' : $_REQUEST['education_status'];

    $user_plan_header="UPDATE USER_INSTRUCTION_DETAILS SET UpdatedDate = now() , StudyStatus ='$education_status'  WHERE UserID = '$user_id' AND PlanCode = '$plan_code' AND RowNo = '$row_no' AND SpecificDate = '$specific_date'";

    $plan_header_res = mysql_query( $user_plan_header );

    if( $plan_header_res > 0 ){
    	echo '{
                "MESSAGE" : "Study  Status Update Successfully";
            }';
    } else{

    	echo '{
                "MESSAGE" : "Somthin Wrong!.";
            }';
    }

}	

?>