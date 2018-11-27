<?php
//header('Content-type: application/json');
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
include_once('include/session.php');
$users = array();
$get_clients      = "select distinct t1.UserID, concat(t2.FirstName,' ',t2.Lastname,' - ',t1.EmailID,' - +', TRIM(LEADING '0' FROM t1.MobileNo)) as name from USER_ACCESS as t1, USER_DETAILS as t2, 
USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID and t2.UserID = t3.UserID and t3.RoleID = '5' and t3.Status = 'A' and t1.UserStatus = 'A' and t3.MerchantID = '$logged_merchantid' and t1.OSType IN ('A','I')  and t1.DeviceID <> '' order by t2.FirstName";
//echo $get_clients;exit;
$get_clients_qry	= mysql_query($get_clients);
$get_user_count	= mysql_num_rows($get_clients_qry);
if($get_user_count)
{
	while($rows = mysql_fetch_array($get_clients_qry))
	{
		$res['id'] 		= $rows['UserID'];
		$res['name'] 	= $rows['name'];
	array_push($users,$res);
	}
echo json_encode($users);
}

?>