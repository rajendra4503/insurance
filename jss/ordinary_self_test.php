<?php
// session_start();
// date_default_timezone_set('Asia/Kolkata');
// include('include/session.php');
// include('include/configinc.php');
// include('include/functions.php');

// $user 		=	"000919880932490";
// $section_id = "3-1";

// $logged_userid 			= "123123123";
// $user_plan_code  		= 'IN00000000012';
// $user_how_long		 	= "6"; 						
// $user_howlong_type		= "Weeks"; 						
// $user_startflag		 	= "PS"; 					
// $user_no_days		 	=  ""; 						
// $user_specific_date		= "2015-07-01";				
// $user_presc_no   		= 1;
// $user_row_no		 	= 1;
// $user_frequency 		= "Weekly"; 					
// $user_instruction		= "3"; 			
// $user_freq_string		= "Sun,Mon"; 				
// //$user_freq_string		= "11,12"; 


$current_date       	= date('Y-m-d H:i:s');
$parameters 			= array();
$user_plan_code 		= (empty($user_sd_plan_code))       ? '' : $user_sd_plan_code;
$user_how_long		 	= (empty($user_sd_howlong))        	? '' : $user_sd_howlong; 	
$user_howlong_type		= (empty($user_sd_howlong_type))	? '' : $user_sd_howlong_type;						
$user_startflag		 	= (empty($user_sd_start_flag))      ? '' : $user_sd_start_flag;					
$user_no_days		 	= (empty($user_sd_no_of_days))      ? '' : $user_sd_no_of_days; 						
$user_specific_date		= (empty($user_sd_specific_date))   ? '' : $user_sd_specific_date;			
$user_presc_no   		= (empty($user_sd_selftest_id))     ? '' : $user_sd_selftest_id;
$user_row_no		 	= (empty($user_sd_self_row))        ? '' : $user_sd_self_row;
$user_frequency 		= (empty($user_sd_frequency))       ? '' : $user_sd_frequency; 										
$user_instruction		= (empty($user_sd_instruction))     ? '' : $user_sd_instruction;			
$user_freq_string		= (empty($user_sd_freq_string))     ? '' : $user_sd_freq_string;
//$user_when 			= (empty($user_md_when))          	? '' : $user_md_when;
//$user_sptime 			= (empty($user_md_sptime))          ? '' : $user_md_sptime;				
					
/*When and Specific Time does not come in Self Test*/
//$user_when 			= "16";						
//$user_sptime 			= "01:00 AM,08:00 AM,9:00 PM,06:00 PM,"; 

/*BEGIN PLAN CALCULATION*/
//Calculate Plan Start Date
if($user_startflag=='PS')
{
	$plan_start_date 		= date('Y-m-d H:i:s');
}
elseif($user_startflag=='ND')
{
	$plan_start_date 	= date('Y-m-d H:i:s');
	$user_no_days	= (empty($user_no_days))		? 	'' : strtolower($user_no_days);
	$plan_start_date 	= date('Y-m-d', strtotime($plan_start_date. " + $user_no_days days"));
}
elseif($user_startflag=='SD')
{
	//$user_specific_date	= (empty($user_specific_date))	? 	'' : $user_specific_date;
	$plan_start_date		= (empty($user_specific_date))	? 	'' : $user_specific_date;
}
//End of calculating Plan Start Date

//Calculate Plan End Date
if($plan_start_date && $user_how_long!=0)
{
$user_how_long		= (empty($user_how_long))		? 	'' : $user_how_long;
$user_howlong_type	= (empty($user_howlong_type))	? 	'' : $user_howlong_type;
$plan_end_date 		= PlanEndDate($plan_start_date,$user_how_long,$user_howlong_type);
}
else
{
$plan_end_date 			= $plan_start_date;
}
//End of calculating Plan End Date

//$plan_start_date 		= "2015-07-01";
//$plan_end_date		= "2015-08-30";

// echo $plan_start_date;
// echo "<br>";
// echo $plan_end_date;
// exit;

/*Whether Once,Daily,Weekly,Monthly*/
if($user_frequency=='Daily' || $user_frequency=='Weekly' || $user_frequency=='Once')
{	
	if($user_frequency=='Daily')
	{
		$days  = getDates($plan_start_date,$plan_end_date);
	}
	elseif($user_frequency=='Weekly')
	{
		$days  	= getDatesOfWeek($plan_start_date,$plan_end_date,$user_freq_string);
	}
	elseif($user_frequency=='Once')
	{
		$days  	= getDates($plan_start_date,$plan_end_date);
	}
	sort($days);

	if($user_instruction=="20")	/*Specific Time*/
	{
		/*As of Now specific time is not considered in self test*/
	}
	else
	{
		//echo 123;exit;
		foreach($days as $day_key => $day_value)
		{
        $time           = getActivityTime($user_instruction,"",$user); 
        $date_time 		= validateDateTime($day_value." ".$time);
        if($date_time)
        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$day_value','$time',now(),'$logged_userid')"; 
	    }
	}
}
elseif($user_frequency=='Monthly')
{
	$months  	= getMonths($plan_start_date,$plan_end_date);
	//echo "<pre>";print_r($months);exit;
	$get_year   = explode("-",$plan_start_date);
    $year       = $get_year[0];
    //echo $year."<br>";
    foreach($months as $key => $value)
    {
        //echo $key."-".$value."<br>";
        $nm = date("m", strtotime("$value-$year"));/*Get Numeric Month based on textual month ie; 11 for November,01 for January etc*/
        $days_in_month = explode(",",$user_freq_string);
        foreach($days_in_month as $d)
        { 
            // append 0 to month incase of single digit like 01,02 etc
            $d 	= str_pad($d, 2, '0', STR_PAD_LEFT);

        // condition to remove past dates if any and also dates greater than plan end date if any
        if((strtotime($current_date) < strtotime("$year-$nm-$d")) && (strtotime($plan_end_date." "."23:59:59") > strtotime("$year-$nm-$d")))
            {
                $time           = getActivityTime($user_instruction,"",$user); 
                $date_time 		= validateDateTime("$year-$nm-$d"." ".$time);
        		if($date_time)
                $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$year-$nm-$d','$time',now(),'$logged_userid')";
            }
        }
        if($nm=='12')
        {
            $year = $year+1;
        }
    }
}
$insert = "insert into ACTIVITES_ORDINARY_USER 
		(UserID,PlanCode,SectionID,PrescNo_SelfTestID,RowNo,ActivityDate,ActivityTime,CreatedDate,CreatedBy) values ";
	$insert.= implode(',', $parameters);
	$insert.= ";";
	if(!empty($parameters))
	{
	    //echo $insert."<br>";
	    mysql_query($insert);
	}
?>