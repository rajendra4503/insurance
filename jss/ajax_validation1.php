<?php
session_start();
ini_set("display_errors","0");
date_default_timezone_set("Asia/Kolkata");

$dbhost2 		= 'localhost:3306';
$dbuser2 		= 'planuser';
$dbpass2 		= 'Seoy@#3498';
$connection2 	= mysql_connect($dbhost2, $dbuser2, $dbpass2);
$db_name2 		= mysql_select_db('mailserver',$connection2) or die("Could not connect to the mail server. Please contact the administrator.");


/*Manual Creation of Email*/
// $new_email = 'IN9731312490@planpiper.com';
// $create_email 			= mysql_query("insert into `virtual_users` 
// 						(`domain_id`,`password`,`email`) VALUES 
// 	 					('4', ENCRYPT('fgh(12)!artc', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$new_email')");
// exit;
/*End of Manual creation*/

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

$country_code 			= $_SESSION['country_code'];
$mobilenumber 			= $_SESSION['mobile_number'];
$name 					= $_SESSION['name'];
$logged_companycountryid= $_SESSION['logged_companycountryid'];
$redirection 			= $_SESSION['redirection'];

//echo "<pre>";print_r($_SESSION);exit;
// $mobilenumber 			= "IN9035034317";
// $name 					= $_SESSION['name'];
// $logged_companycountryid= $_SESSION['logged_companycountryid'];
// echo $mobilenumber."<br>";
// echo $name."<br>";
// echo $logged_companycountryid;exit;

	$new_email	       		= $country_code.$mobilenumber."@planpiper.com";

	$check_duplicate 		= mysql_query("select email from virtual_users where email='$new_email'");
	$check_duplicate_count  = mysql_num_rows($check_duplicate);
	if($check_duplicate_count==0)
	{
		$create_email 			= mysql_query("insert into `virtual_users` 
										(`domain_id`,`password`,`email`) VALUES 
	 									('4', ENCRYPT('fgh(12)!artc', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), '$new_email')");
	 	$check_insert 			= mysql_affected_rows();
	}

	//echo $new_email."<br>";exit;
	//CHECK FOR DUPLICATE MOBILE NUMBER
	 
if($redirection=='V')
{
	header('Location:business_registration.php');
}
else
{
	header('Location:plan_users.php');
}
?>
