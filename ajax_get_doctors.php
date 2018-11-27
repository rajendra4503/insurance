<?php
//header('Content-type: application/json');
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
include_once('include/session.php');

//echo json_encode(array(array('id'=>12, 'name'=> 'php')));
$users = array();
$get_users      = "select SNo, Name from MERCHANT_STAFF where MerchantID = '$logged_merchantid' and Status = 'A'";
// /echo $get_users;exit;
$get_users_qry	= mysql_query($get_users);
$get_user_count	= mysql_num_rows($get_users_qry);
if($get_user_count)
{
	while($rows = mysql_fetch_array($get_users_qry))
	{
		$res['id'] 		= $rows['Name'];
		$res['name'] 	= $rows['Name'];
	array_push($users,$res);
	}
echo json_encode($users);
}

?>