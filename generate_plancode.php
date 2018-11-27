<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
unset($_SESSION['plancode_for_current_plan']);
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
		  //echo $plan_code;
		  $_SESSION['plancode_for_current_plan'] = $plan_code;
		  header("Location:plan_med_new.php");
?>
