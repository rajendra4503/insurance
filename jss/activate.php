<?php
session_start();
ini_set("display_errors","0");
include_once('include/configinc.php');
if((isset($_REQUEST['c']))&&(isset($_REQUEST['id']))&&(!empty($_REQUEST['c']))&&(!empty($_REQUEST['id']))){
	$country = $_REQUEST['c'];
	$id      = $_REQUEST['id'];
	$userid  = $country.$id;
	$activate_user = mysql_query("update USER_ACCESS set UserStatus='A' where UserID='$userid'");
	$activate_merchant = mysql_query("update USER_MERCHANT_MAPPING set Status='A' where UserID='$userid'");
	header("Location:login.php");
} else {
	header("Location:login.php");
}
?>