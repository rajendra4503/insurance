<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
//include('include/session.php');
date_default_timezone_set("Asia/Kolkata");
$host_server = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
$currenttime = date("Y-m-d G:i:s ");

$type 	     = (empty($_REQUEST['type'])) ? '' : mysql_real_escape_string($_REQUEST['type']);

//echo $type;exit;
//**********************************************Generate Plan Codes**********************************
if($type=="get_plancode")
{
//$plan_code = substr(str_shuffle(md5(time())),0,$length);
  $country_id = $logged_companycountryid;
  $get_last_plancode = mysql_query("select PlanCode from PLAN_HEADER where PlanCode like '$country_id%' order by PlanCode desc limit 1");
  $plancode_count = mysql_num_rows($get_last_plancode);
  if($plancode_count > 0){
    while ($plancodelast = mysql_fetch_array($get_last_plancode)) {
      $lastplancode = $plancodelast['PlanCode'];
    }
    $lastplancode = substr($lastplancode, 2);
    $lastplancode = $lastplancode  +1;
    $lastplancode = sprintf('%010d', $lastplancode);
    $plan_code     = $country_id.$lastplancode;
  } else {
    $plan_code = $country_id."0000000001";
  }
}
?>