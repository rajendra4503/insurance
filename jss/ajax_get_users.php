<?php
//header('Content-type: application/json');
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
include_once('include/session.php');
/*If Individual User then display himself/herself and his/her family members only*/
if($logged_usertype=='I')
{
$is_individual= " and (t3.RoleID = '2' || t3.RoleID = '5') and t3.MerchantID='$logged_merchantid' ";
}
else
{
$is_individual= " and t3.RoleID = '5' ";
}
//echo json_encode(array(array('id'=>12, 'name'=> 'php')));
$users = array();
$get_users      = "select distinct t1.UserID, concat(t2.FirstName,' ',t2.Lastname,' - ',t1.EmailID,' - +', TRIM(LEADING '0' FROM t1.MobileNo)) as name from USER_ACCESS as t1, USER_DETAILS as t2, 
USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID and t2.UserID = t3.UserID  and t3.Status = 'A' and t1.UserStatus = 'A' $is_individual order by t2.FirstName";
//echo $get_users;exit;
$get_users_qry	= mysql_query($get_users);
$get_user_count	= mysql_num_rows($get_users_qry);
if($get_user_count)
{
	while($rows = mysql_fetch_array($get_users_qry))
	{
		$res['id'] 		= $rows['UserID'];
		$res['name'] 	= $rows['name'];
	array_push($users,$res);
	}
echo json_encode($users);
}

?>