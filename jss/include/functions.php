<?php
//FUNCTION TO FORMAT PHONE NUMBERS
function formatPhone($num)
{
$num = preg_replace('/[^0-9]/', '', $num);
$len = strlen($num);

	if($len<=6)
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})/', ' $1 $2', $num);	
	}
	else if(($len>6)&&($len<=9))
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{1})/', ' $1 $2 $3', $num);	
	}
	else if($len == 10)
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', ' $1 $2 $3', $num);
	}
	else if($len>10)
	{
		$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{1})/', ' $1 $2 $3 $4', $num);	
	}
return $num;
}
//END OF FUNCTION TO FORMAT PHONE NUMBERS

//FUNCTION TO REDUCE IMAGE SIZE
function reduce_image_size($dest_folder,$image_name,$files)
{
    //REDUCE IMAGE RESOLUTION
    if($files)
    {
        //echo 123;exit;
        $dest   = $dest_folder.$image_name;
        $width  = 300;
        $height = 300;
        list($width_orig, $height_orig) = getimagesize($files);
        $ratio_orig = $width_orig/$height_orig;
        if ($width/$height > $ratio_orig)
        {
           $width = $height*$ratio_orig;
        }
        else
        {
           $height = $width/$ratio_orig;
        }
        $image_p    = imagecreatetruecolor($width, $height);
        $image      = imagecreatefromjpeg($files);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        imagejpeg($image_p,$dest, 100);
        ImageDestroy ($image_p);
    }
    //END OF REDUCING IMAGE RESOLUTION
}
//END OF FUNCTION TO REDUCE IMAGE SIZE
function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = '+'.$countryCode.' '.$areaCode.' '.$nextThree.' '.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = ''.$areaCode.' '.$nextThree.' '.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.' '.$lastFour;
    }

    return $phoneNumber;
}

/*FUNCTION TO CONVERT WEEKS OR MONTHS INTO DAYS*/
function diff($from,$to,$howlong,$howlongtype)
{
$duration_in_days = "";
    if($howlongtype=='Weeks')
    {
       $duration_in_days    = $howlong * 7;
    }
    else if($howlongtype=='Months')
    {
       $duration_in_days    = $howlong * 30;
    }
    else if($howlongtype=='Days')
    {
       $duration_in_days    = $howlong;
    }

$diff           = abs(strtotime($to) - strtotime($from));/*Plan Synced Date - Current Date*/
$diff           = floor($diff/(60*60*24));
$diff_in_days   = max(0,floor($duration_in_days-$diff));

return $diff_in_days;
}
/*END OF FUNCTION*/


function trim_leading_zeros_special_symbols($countrycode)
{
$trimmed_country_code   = ltrim(preg_replace('/[^0-9]/','',$countrycode),0);
$countrycode_length     = strlen($trimmed_country_code);
//return $countrycode_length;
    if($countrycode_length==0)
    {
        $leading_zeros  = "";
    }
    elseif($countrycode_length==1)
    {
        $leading_zeros  = "0000";
    }
    elseif($countrycode_length==2)
    {
        $leading_zeros  = "000";
    }
    elseif($countrycode_length==3)
    {
        $leading_zeros  = "00";
    }
    elseif($countrycode_length==4)
    {
        $leading_zeros  = "0";
    }
    else
    {
        $leading_zeros  = "00000";
    }
return $trimmed_code = $leading_zeros.$trimmed_country_code;
}

function valid_url($url)
{
    $handle = curl_init($url);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

    /* Get the HTML or whatever is linked in $url. */
    $response = curl_exec($handle);

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if($httpCode == 404) {
        /* Handle 404 here. */
        return 0;
    }
    else
    {
        return 1;
    }
    curl_close($handle);

    /* Handle $response here. */
}

function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    else
    {
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    }
    //echo "B: ".$bname."<br>";

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    //echo "i: ".$i."<br>";
    if($i==0)
    {
        $version = 11;
    }
    elseif ($i != 1 && $i!=0) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    //echo "V: ".$version;exit;

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'   => $pattern
    );
}

/****************Following functions are related to Plan Calculation for Ordinary Phone User*********/

/*FUNCTION TO CALCULATE PLAN END DATE BASED ON PLAN START DATE,DURATION(1 or 15 or 30) AND DURATION TYPE(days,week,month,year)*/
function PlanEndDate($start_date,$duration,$durationtype)
{

    $startdate = strtotime("+".$duration." ".$durationtype, strtotime($start_date));
    return  date("Y-m-d", $startdate);
}
/*END OF FUNCTION*/

function getDates($plan_start_date, $plan_end_date)
{
    $start              = strtotime($plan_start_date);
    $end                = strtotime($plan_end_date);
    $dateArr            = array();
    while($start <= $end)
    {
        $dateArr[]  = date("Y-m-d", $start);
        $start      = strtotime("+1 day", $start);
    }
//echo "<pre>";print_r($dateArr);
return $dateArr;
}

function getDatesOfWeek($plan_start_date, $plan_end_date,$weekdays)
{
    $start              = strtotime($plan_start_date);
    $end                = strtotime($plan_end_date);

    $arr_weekdays       = explode(",", $weekdays);
    //echo "<pre>";print_r($arr_weekdays);
    $dateArr            = array();
        foreach ($arr_weekdays as $value)
        {
            $startday   = strtotime($value, $start);
            while($startday <= $end)
            {
                $dateArr[] = date("Y-m-d", $startday);
                $startday = strtotime("+1 week", $startday);
            }
        }
    //echo "<pre>";print_r($dateArr);
return $dateArr;
}

function getMonths($date1, $date2)
{
    $time1 = strtotime($date1);
    $time2 = strtotime($date2);
    $my = date('mY', $time2);
   
    $months = array(date('F', $time1));
   
    while($time1 < $time2) {
        $time1 = strtotime(date('Y-m-d', $time1).' +1 month');
        if(date('mY', $time1) != $my && ($time1 < $time2))
        $months[] = date('F', $time1);
    }
   
    $months[] = date('F', $time2);
    $months = array_unique($months);
   // echo "<pre>";print_r($months);
return $months;
}

function validateDateTime($date_time)
{
    $current_date    = date('Y-m-d H:i:s'); 
    $x               = strtotime($current_date);
    $y               = strtotime($date_time);

    if($x < $y)
    {
        $date_time = $date_time;
    }
    else
    {
        $date_time = "";
    }
return $date_time;
}

function getActivityTime($text,$part_of_day,$user)
{

if($user!='')
{
    $user = $user;
}
else
{
    $user = '111';
}
$get_user_phone_settings = mysql_query("select WakeUp, Morning, BeforeBreakfast, WithBreakfast, AfterBreakfast, MorningSnack, BeforeLunch, WithLunch, AfterLunch, Afternoon, EveningSnack, BeforeTea, WithTea, AfterTea, Evening, BeforeDinner, WithDinner, AfterDinner, BeforeSleeping from USER_PHONE_SETTINGS where UserID='$user'");
$get_phone_settings_count= mysql_num_rows($get_user_phone_settings);
    if($get_phone_settings_count>0)
    {
        $user_settings_row  = mysql_fetch_array($get_user_phone_settings);
        $wakeup             = $user_settings_row['WakeUp'];
        $morning            = $user_settings_row['Morning'];
        $before_breakfast   = $user_settings_row['BeforeBreakfast'];
        $with_breakfast     = $user_settings_row['WithBreakfast'];
        $after_breakfast    = $user_settings_row['AfterBreakfast'];
        $morning_snack      = $user_settings_row['MorningSnack'];
        $before_lunch       = $user_settings_row['BeforeLunch'];
        $with_lunch         = $user_settings_row['WithLunch'];
        $after_lunch        = $user_settings_row['AfterLunch'];
        $afternoon          = $user_settings_row['Afternoon'];
        $evening_snack      = $user_settings_row['EveningSnack'];
        $before_tea         = $user_settings_row['BeforeTea'];
        $with_tea           = $user_settings_row['WithTea'];
        $after_tea          = $user_settings_row['AfterTea'];
        $evening            = $user_settings_row['Evening'];
        $before_dinner      = $user_settings_row['BeforeDinner'];
        $with_dinner        = $user_settings_row['WithDinner'];
        $after_dinner       = $user_settings_row['AfterDinner'];
        $before_sleeping    = $user_settings_row['BeforeSleeping'];
    }
    /*Self Test*/
    if($part_of_day=="")
    {
        //echo $text;
        if($text == '1')        { $time = $wakeup;          }
        elseif($text == '2')    { $time = $morning;         }
        elseif($text == '3')    { $time = $before_breakfast;}
        elseif($text == '4')    { $time = $with_breakfast;  }
        elseif($text == '5')    { $time = $after_breakfast; }
        elseif($text == '6')    { $time = $morning_snack;   }
        elseif($text == '7')    { $time = $before_lunch;    }
        elseif($text == '8')    { $time = $with_lunch;      } 
        elseif($text == '9')    { $time = $after_lunch;     }
        elseif($text == '10')   { $time = $afternoon;       }
        elseif($text == '11')   { $time = $evening_snack;   }
        elseif($text == '12')   { $time = $before_tea;      }
        elseif($text == '13')   { $time = $with_tea;        }
        elseif($text == '14')   { $time = $after_tea;       }
        elseif($text == '15')   { $time = $evening;         }
        elseif($text == '16')   { $time = $before_dinner;   }
        elseif($text == '17')   { $time = $with_dinner;     }
        elseif($text == '18')   { $time = $after_dinner;    }
        elseif($text == '19')   { $time = $before_sleeping; }            
    }
    /*Medication and Instruction*/
    else
    {
        //echo 123;
        if($part_of_day=="morning")
        {
            if($text=='Before Food')
                $time = $before_breakfast;
            elseif($text=='With Food')
                $time = $with_breakfast;
            elseif($text=='After Food')
                $time = $after_breakfast;
            else
                $time = "08:00:00";
        }
        elseif($part_of_day=="afternoon")
        {
            if($text=='Before Food')
                $time = $before_lunch;
            elseif($text=='With Food')
                $time = $with_lunch;
            elseif($text=='After Food')
                $time = $after_lunch;
            else
                $time = "01:00:00";
        }
        elseif($part_of_day=="evening")
        {
            if($text=='Before Food')
                $time = $before_tea;
            elseif($text=='With Food')
                $time = $with_tea;
            elseif($text=='After Food')
                $time = $after_tea;
            else
                $time = "16:00:00";
        }
        elseif($part_of_day=="night")
        {
            if($text=='Before Food')
                $time = $before_dinner;
            elseif($text=='With Food')
                $time = $with_dinner;
            elseif($text=='After Food')
                $time = $after_dinner;
            else
                $time = "20:00:00";
        }
    }
return $time;
}
/***************End of Plan Calculation Functions for Ordinary Phone User*****************************/
?>