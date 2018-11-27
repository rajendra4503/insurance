<?php
// session_start();
// date_default_timezone_set('Asia/Kolkata');
// include('include/session.php');
// include('include/configinc.php');
// include('include/functions.php');

// $logged_userid 			= "123123123";
// $user 					=	"000919880932490";
// $section_id 			= "6";

// $user_plan_code  		= 'IN00000000012';
// $user_how_long		 	= "4";
// $user_howlong_type		= "Months";				
// $user_startflag		 	= "SD";				
// $user_no_days		 	=  ""; 						
// $user_specific_date		= "2015-08-01";			
// $user_presc_no   		= 1;
// $user_row_no		 	= 1;

// $user_frequency 		= "Monthly";				
// $user_when 				= "15";			
// $user_instruction		= "Before Food";
// $user_sptime 			= "01:00 AM,08:00 AM,9:00 PM,06:00 PM,";
// //$user_freq_string		= "Sun,Mon"; 				
// $user_freq_string		= "01,02";

if($section_id==1)
{
$user_plan_code 		= (empty($user_md_plan_code))       ? '' : $user_md_plan_code;
$user_how_long		 	= (empty($user_md_how_long))        ? '' : $user_md_how_long; 	
$user_howlong_type		= (empty($user_md_howlong_type))	? '' : $user_md_howlong_type;						
$user_startflag		 	= (empty($user_md_startflag))       ? '' : $user_md_startflag;					
$user_no_days		 	= (empty($user_md_no_days))         ? '' : $user_md_no_days; 						
$user_specific_date		= (empty($user_md_specific_date))   ? '' : $user_md_specific_date;			
$user_presc_no   		= (empty($user_md_presc_no))        ? '' : $user_md_presc_no;
$user_row_no		 	= (empty($user_md_row_no))          ? '' : $user_md_row_no;
$user_frequency 		= (empty($user_md_frequency))       ? '' : $user_md_frequency; 					
$user_when 				= (empty($user_md_when))          	? '' : $user_md_when;					
$user_instruction		= (empty($user_md_instruction))     ? '' : $user_md_instruction;			
$user_sptime 			= (empty($user_md_sptime))          ? '' : $user_md_sptime; 
$user_freq_string		= (empty($user_md_freq_string))     ? '' : $user_md_freq_string; 					
}
elseif($section_id==6)
{
$user_plan_code 		= (empty($user_id_plan_code))       ? '' : $user_id_plan_code;
$user_how_long		 	= (empty($user_id_how_long))        ? '' : $user_id_how_long; 	
$user_howlong_type		= (empty($user_id_howlong_type))	? '' : $user_id_howlong_type;						
$user_startflag		 	= (empty($user_id_startflag))       ? '' : $user_id_startflag;					
$user_no_days		 	= (empty($user_id_no_days))         ? '' : $user_id_no_days; 						
$user_specific_date		= (empty($user_id_specific_date))   ? '' : $user_id_specific_date;			
$user_presc_no   		= (empty($user_id_presc_no))        ? '' : $user_id_presc_no;
$user_row_no		 	= (empty($user_id_row_no))          ? '' : $user_id_row_no;
$user_frequency 		= (empty($user_id_frequency))       ? '' : $user_id_frequency; 					
$user_when 				= (empty($user_id_when))          	? '' : $user_id_when;					
$user_instruction		= (empty($user_id_instruction))     ? '' : $user_id_instruction;			
$user_sptime 			= (empty($user_id_sptime))          ? '' : $user_id_sptime; 
$user_freq_string		= (empty($user_id_freq_string))     ? '' : $user_id_freq_string; 
}


$current_date       		= date('Y-m-d H:i:s');
$parameters 				= array();

if($user_when 			!= "16")
{
	$get_shorthand_type 	= mysql_fetch_object(mysql_query("select ShortHand from MASTER_DOCTOR_SHORTHAND 
								where ID='$user_when'"));
	$shorthand   			= $get_shorthand_type->ShortHand;
}
else
{
	$shorthand   			= "";
}
//echo $shorthand;exit;

/*BEGIN PLAN CALCULATION*/
//Calculate Plan Start Date
if($user_startflag=='PS')
{
	$plan_start_date 		= date('Y-m-d H:i:s');
}
elseif($user_startflag=='ND')
{
	$plan_start_date 	= date('Y-m-d H:i:s');
	$user_no_days		= (empty($user_no_days))		? 	'' : strtolower($user_no_days);
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
	//echo "<pre>";print_r($days);
	// echo date("Y-m-d h:i",strtotime($current_date))."<br>";
	// echo date("Y-m-d h:i",strtotime('now'));
	//  exit;

	if($shorthand=="")
	{
		//echo 123;exit;
		foreach($days as $day_key => $day_value)
		{
			// array_filter used to remove trailing comma from array
			$specific_times = array_filter(explode(",",$user_sptime));
			//echo "<pre>";print_r($specific_times);exit;
			foreach($specific_times as $specific_time_key => $specific_time_value)
			{
				// 12-hour time to 24-hour time
				$specific_time_value 	= date("H:i:s", strtotime($specific_time_value));
				$date_time 				= validateDateTime($day_value." ".$specific_time_value);
        		if($date_time)
				$parameters[]   		= "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$day_value','$specific_time_value',now(),'$logged_userid')";
			}
		}
	}
	else
	{
		//echo 123;exit;
		foreach($days as $day_key => $day_value)
		{
			$sh = explode("-",$shorthand);
	        //echo "<pre>";print_r($sh);exit;
	        
	        foreach ($sh as $key => $value)
	        {
	            //echo $key." ".$value."<br>";
	            if($key=='0' && $value=='1')
	            {
	            $time           = getActivityTime($user_instruction,"morning",$user); 
	            $date_time 		= validateDateTime($day_value." ".$time);
	            if($date_time)
		        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$day_value','$time',now(),'$logged_userid')"; 
	            }
	            elseif($key=='1' && $value=='1')
	            {
	            $time   		= getActivityTime($user_instruction,"afternoon",$user);
	            $date_time 		= validateDateTime($day_value." ".$time);
		        if($date_time)
		        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$day_value','$time',now(),'$logged_userid')";
	            }
	            elseif($key=='2' && $value=='1')
	            {
	            $time   		= getActivityTime($user_instruction,"evening",$user);
	            $date_time 		= validateDateTime($day_value." ".$time);
		        if($date_time)
		        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$day_value','$time',now(),'$logged_userid')";
	            }
	            elseif($key=='3' && $value=='1')
	            {
	            $time   		= getActivityTime($user_instruction,"night",$user);
	           	$date_time 		= validateDateTime($day_value." ".$time);
		        if($date_time)
		        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$day_value','$time',now(),'$logged_userid')";
	            }
	        	//echo "<pre>";print_r(array_unique($parameters));
	        }
		    
	    }
	}
}
elseif($user_frequency=='Monthly')
{
	$months  	= getMonths($plan_start_date,$plan_end_date);
	//echo "<pre>";print_r($months);exit;
	$get_year   = explode("-",$plan_start_date);
    $year       = $get_year[0];

    foreach($months as $key => $value)
    {
        //echo $key."-".$value;
        $nm = date("m", strtotime("$value-$year"));/*Get Numeric Month based on textual month ie; 11 for November,01 for January etc*/
        $days_in_month = explode(",",$user_freq_string);
        foreach($days_in_month as $d)
        { 
            // append 0 to month incase of single digit like 01,02 etc
            $d 	= str_pad($d, 2, '0', STR_PAD_LEFT);

            // condition to remove past dates if any
            if((strtotime($current_date) < strtotime("$year-$nm-$d")) &&
            	 (strtotime($plan_end_date." "."23:59:59") > strtotime("$year-$nm-$d")))
            {
                if($shorthand!="")
                {
                    $sh = explode("-",$shorthand);
                    //echo "<pre>";print_r($sh);exit;
                    foreach ($sh as $key => $value)
                    {
                        //echo $key." ".$value."<br>";
                        if($key=='0' && $value=='1')
                        {
                        $time           = getActivityTime($user_instruction,"morning",$user); 
                        $date_time 		= validateDateTime("$year-$nm-$d"." ".$time);
		        		if($date_time)
                        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$year-$nm-$d','$time',now(),'$logged_userid')";
                        }
                        elseif($key=='1' && $value=='1')
                        {
                        $time           = getActivityTime($user_instruction,"afternoon",$user);
                        $date_time 		= validateDateTime("$year-$nm-$d"." ".$time);
		        		if($date_time)
                        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$year-$nm-$d','$time',now(),'$logged_userid')";
                        }
                        elseif($key=='2' && $value=='1')
                        {
                        $time           = getActivityTime($user_instruction,"evening",$user);
                        $date_time 		= validateDateTime("$year-$nm-$d"." ".$time);
		        		if($date_time)
                        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$year-$nm-$d','$time',now(),'$logged_userid')";
                        }
                        elseif($key=='3' && $value=='1')
                        {
                        $time           = getActivityTime($user_instruction,"night",$user);
                        $date_time 		= validateDateTime("$year-$nm-$d"." ".$time);
		        		if($date_time)
                        $parameters[]   = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$year-$nm-$d','$time',now(),'$logged_userid')";
                        }
                    }
                }
                else
                {
                    $specific_times = array_filter(explode(",",$user_sptime));
                    foreach($specific_times as $specific_time_key => $specific_time_value)
                    {
                        // 12-hour time to 24-hour time
                        $specific_time_value    = date("H:i:s", strtotime($specific_time_value));
                        $date_time 				= validateDateTime("$year-$nm-$d"." ".$specific_time_value);
		        		if($date_time)
                        $parameters[]           = "('$user','$user_plan_code','$section_id','$user_presc_no','$user_row_no','$year-$nm-$d','$specific_time_value',now(),'$logged_userid')";    
                    }
                } 
            }
        }

        if($nm=='12')
        {
            $year = $year+1;
        }
    }
}

//echo "<pre>";print_r($parameters);


$insert = "insert into ACTIVITES_ORDINARY_USER (UserID,PlanCode,SectionID,PrescNo_SelfTestID,RowNo,ActivityDate,ActivityTime,CreatedDate,CreatedBy) values ";
	$insert.= implode(',', $parameters);
	$insert.= ";";
	if(!empty($parameters))
	{
	    //echo $insert."<br>";
	    mysql_query($insert);
	}

?>