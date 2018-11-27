<?php
session_start();
include_once('include/configinc.php');

$type = $_REQUEST['type'];
//echo $type;exit;
//LOGIN


//GET STATES BASED ON COUNTRY IN DROPDOWN
if($type == 'get_states')
{
$country 		= $_REQUEST['country'];
$selectedstate 	= (empty($_REQUEST['selectedstate'])) 	? '' : $_REQUEST['selectedstate'];
$selectedcity 	= (empty($_REQUEST['selectedcity'])) 	? '' : $_REQUEST['selectedcity'];
//echo $center;exit;
$row = mysql_query("select StateID,StateName from STATE_DETAILS where CountryCode ='$country' order by StateName");
	echo "<option value='0' style='display:none;'>SELECT STATE</option>";
	while($row2 = mysql_fetch_array($row))
	{
	$state_id	= $row2['StateID'];
	$state_name	= $row2['StateName'];
	
		if($selectedstate==$state_id)
		{
			echo "<option value='$state_id' selected>$state_name</option>";
		}
		else
		{
			echo "<option value='$state_id'>$state_name</option>";
		}
		
		
	}
}

//GET CITIES BASED ON STATE IN DROPDOWN
elseif($type == 'get_cities')
{
$state= $_REQUEST['state'];
//echo $center;exit;
$rows = mysql_query("select CityID,CityName from CITY_DETAILS where StateID ='$state' order by CityName");
	echo "<option value='0' style='display:none;'>SELECT CITY</option>";
	while($row3 = mysql_fetch_array($rows))
	{
	$city_id	= $row3['CityID'];
	$city_name	= $row3['CityName'];
	
		
			echo "<option value='$city_id'>$city_name</option>";
		
	}
}

?>

