<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>";print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php'); 
$plan_to_customize 	= $_SESSION['current_assigned_plan_code'];
$assigned_to_user  	= $_SESSION['current_assigned_user_id'];
$plan_name 		 	= $_SESSION['current_assigned_plan_name']; 

if(isset($plan_to_customize))
{
//GET USER TO SEND NOTIFICATION
$get_user    =   "select UserID,OSType,DeviceID from USER_ACCESS where UserID='$assigned_to_user'";
//echo $get_user;exit;
$get_user_qry  = mysql_query($get_user);
$get_user_count= mysql_num_rows($get_user_qry);
	if($get_user_count)
	{//echo 123;exit;
		while($user_rows = mysql_fetch_array($get_user_qry))
		{
		$user_id  		= $user_rows['UserID'];
		$user_os_type  	= $user_rows['OSType'];
		$user_device_id	= $user_rows['DeviceID'];
		//echo $user_id;exit;
			if($user_id)
			{
				//Push notification for Android and IOS
				if($user_os_type=='A' && $user_device_id!='')
				{
				$regId          = $user_device_id;
				$res['message'] = "An Update is available for your plan - $plan_name.";
				$res['userid']  = $assigned_to_user;
				$res['flag']  	= "plan_update";
				$message        = json_encode($res); 
				include("gcm_server_php/send_message.php");
				}
				else if($user_os_type=='I' && $user_device_id!='')
				{
				$deviceToken= $user_device_id;
				//echo "<br>";
				$userid  	= $assigned_to_user;
				//echo "<br>";exit;
				$flag 		= "plan_update";
				$report_id   	= "";
				$message 	= "An Update is available for your plan - $plan_name.";

				//echo "Token: ".$deviceToken."<br>"."UserID: ".$userid."<br>"."Flag: ".$flag."<br>"."Message: ".$message;exit;

				//include("apple/local/push.php");
				include("apple/production/push.php");
				}
			}
		}
	}
//END OF SEND NOTIFICATION
}
?>