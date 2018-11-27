<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
//mail.appmantras.com:3306
$dbhost2 		= '10.2.1.7:3306';
$dbuser2 		= 'root';
$dbpass2 		= 'seoy34950';
$connection2 	= mysql_connect($dbhost2, $dbuser2, $dbpass2);
$db_name2 		= mysql_select_db('mailserver',$connection2) or die("Could not connect to the mail server. Please contact the administrator.") ;


/*Manual Creation of Email*/
// $new_email = 'IN9731312490@planpiper.com';
// $create_email 			= mysql_query("insert into `virtual_users` 
// 						(`domain_id`,`password`,`email`) VALUES 
// 	 					('4', ENCRYPT('fgh(12)!artc', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$new_email')");
// exit;
/*End of Manual creation*/

/*Get List of emails created in Mail server table*/
// $emails 	= array();
// $get_email = mysql_query("select * from virtual_users");
// $get_count = mysql_num_rows($get_email);
// if($get_count>0)
// {
// 	while($row = mysql_fetch_assoc($get_email))
// 	{
// 		$emails[] = $row;
// 	}
// echo "<pre>";print_r($emails);
// }
// exit;

$planpiper_email 			= $_REQUEST['planpiper_email'];


//echo "<pre>";print_r($_SESSION);exit;
// $mobilenumber 			= "IN9035034317";
// $name 					= $_SESSION['name'];
// $logged_companycountryid= $_SESSION['logged_companycountryid'];
// echo $mobilenumber."<br>";
// echo $name."<br>";
// echo $logged_companycountryid;exit;

	$new_email	       		= $planpiper_email;
	//echo $new_email."<br>";exit;

	$check_duplicate 		= mysql_query("select email from virtual_users where email='$new_email'");
	$check_duplicate_count  = mysql_num_rows($check_duplicate);
	if($check_duplicate_count==0)
	{
		$create_email 		= mysql_query("insert into `virtual_users` 
										(`domain_id`,`password`,`email`) VALUES 
	 									('4', ENCRYPT('fgh(12)!artc', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$new_email')");
	 	$check_insert 		= mysql_affected_rows();
	}

if($planpiper_email)
{
    echo "{".json_encode('PLANPIPER_SIGNUP').':'.json_encode("1")."}";
}
else
{
    echo "{".json_encode('PLANPIPER_SIGNUP').':'.json_encode("0")."}";
}
?>