<?php
include('include/configinc.php');

$id 			= (empty($_REQUEST['user_id']))    	? '' : mysql_real_escape_string(trim($_REQUEST['user_id']));
$from_email 	= (empty($_REQUEST['from_email'])) 	? '' : mysql_real_escape_string(trim($_REQUEST['from_email']));
$count 			= (empty($_REQUEST['count'])) 		? '' : mysql_real_escape_string(trim($_REQUEST['count']));
$report_text 	= ($count > 1) 						? 'reports' : 'report';
$text 			= ($count > 1) 						? 'them' 	: 'it';

if($count>0)
{
	/*As soon as a row gets inserted in MYFOLDER_PARENT Table (Source: Email from Labs) execute below script*/
	/*Send Notification to the user*/
	if($id!="")
	{
	$get_user    	=   "select UserID,OSType,DeviceID from USER_ACCESS where UserID='$id'";
	//echo $get_user;exit;
	$get_user_qry  	= mysql_query($get_user);
	$get_user_count = mysql_num_rows($get_user_qry);
		if($get_user_count)
		{
		$msg = "$from_email has sent you $count $report_text. Tap here to load $text on your phone.";
			while($user_rows= mysql_fetch_array($get_user_qry))
			{
			$user_id        = $user_rows['UserID'];
			$user_os_type   = $user_rows['OSType'];
			$user_device_id = $user_rows['DeviceID'];

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
					$deviceToken 	= $user_device_id;
					$userid   		= $id;
					$flag     		= "report";
					$message  		= $msg;
					//include("apple/local/push.php");
					include("apple/production/push.php");
					}
				}
			}
		}
	}
	/*End of sending notification to user*/
}
?>