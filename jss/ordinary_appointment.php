<?php
// session_start();
// date_default_timezone_set('Asia/Kolkata');
// include('include/session.php');
// include('include/configinc.php');
// include('include/functions.php');

// $user 				=	"000919880932490";
// $section_id 			= "2";
// $logged_userid 		= "123123123";
// $user_plan_code  	= 'IN00000000012';
// $user_appo_date 		= '2015-07-30';
// $user_appo_time 		= '12:00:00';

$current_date       	= date('Y-m-d H:i:s');
$parameters 			= array();

$user_plan_code 		= (empty($user_ad_plan_code))	? '' : $user_ad_plan_code;
$user_appo_date			= (empty($user_ad_appo_date))	? '' : $user_ad_appo_date;
$user_appo_time			= (empty($user_ad_appo_time))   ? '' : $user_ad_appo_time; 			 				

/*BEGIN CALCULATION*/
if($user!="" && $user_plan_code!="" && $user_appo_date!="" && $user_appo_time!="")
{
	$all_dates 			= array();

	$three_days_before 	= date('Y-m-d H:i:s',strtotime("-3 days", strtotime($user_appo_date." ".$user_appo_time)));
	$one_day_before 	= date('Y-m-d H:i:s',strtotime("-1 day",  strtotime($user_appo_date." ".$user_appo_time)));
	$one_hour_before 	= date('Y-m-d H:i:s',strtotime("-1 hour", strtotime($user_appo_date." ".$user_appo_time)));
	$actual_date_time 	= $user_appo_date." ".$user_appo_time;
	array_push($all_dates,$three_days_before,$one_day_before,$one_hour_before,$actual_date_time);
	//echo "<pre>";print_r($all_dates);
	foreach($all_dates as $val)
	{
		$date_time 			= validateDateTime($val);
		if($date_time)
		{
			$datetime 	= new DateTime($date_time);
			$date 		= $datetime->format('Y-m-d');
			$time 		= $datetime->format('H:i:s');

			$parameters[]   	= "('$user','$user_plan_code','$section_id','$date','$time',now(),'$logged_userid')";
		}	
	}
	//echo "<pre>";print_r($parameters);  
/*END CALCULATION*/

	$insert = "insert into ACTIVITES_ORDINARY_USER (UserID,PlanCode,SectionID,ActivityDate,ActivityTime,CreatedDate,CreatedBy) values ";
	$insert.= implode(',', $parameters);
	$insert.= ";";
	if(!empty($parameters))
	{
	    //echo $insert."<br>";
	    mysql_query($insert);
	}
}

?>