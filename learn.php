<?php
date_default_timezone_set('Asia/Kolkata');
$myvalue = '28-1-2011 14:32:55';

$datetime = new DateTime($myvalue);

$date = $datetime->format('Y-m-d');
$time = $datetime->format('H:i:s');
echo $date;
echo "***";
echo $time;
?>