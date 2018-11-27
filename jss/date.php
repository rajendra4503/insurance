<?php

/**
 * Calculate the difference between two dates using date_diff()
 * 
 */
//creating a date object
$date1 = date_create('10-01-2013');
//creating a date object
$date2 = date_create('02-01-2013');
//calculating the difference between dates
//the dates must be provided to the function as date objects that's why we were setting them as 
//date objects and not just as strings
//date_diff returns an date object wich can be accessed as seen below
$diff12 = date_diff($date2, $date1);

//accesing days
$days = $diff12->d;
//accesing months
$months = $diff12->m;
//accesing years
$years = $diff12->y;
echo '<center>';
echo '<br /><div style="background-color:green;color:#fff;padding:10px;width:600px;font-size:16px">
<b>
The difference between 10-01-2013 and 02-01-2013 <br />is: ' . $days . ' day(s), ' . $months . ' month(s),
' . $years . ' year(s)</b>
</div><br />';
echo '</center>';

//creating a date object
$date3 = date_create('18-10-2012 12:20:12');
//creating a date object
$date4 = date_create('13-01-2014 10:12:34');
//calculating the difference between dates
//the dates must be provided to the function as date objects that's why we were setting them 
//as date objects and not just as strings
//date_diff returns an date object wich can be accessed as seen below
$diff34 = date_diff($date4, $date3);

//accesing days
$days = $diff34->d;
//accesing months
$months = $diff34->m;
//accesing years
$years = $diff34->y;
//accesing hours
$hours=$diff34->h;
//accesing minutes
$minutes=$diff34->i;
//accesing seconds
$seconds=$diff34->s;

echo '<center>';
echo '<br /><div style="background-color:green;color:#fff;padding:10px;width:600px;font-size:16px">
<b>The difference between 13-01-2014 10:12:34 and 18-10-2012 12:20:12 
<br />is: ' . $days . ' day(s), ' . $months . ' month(s), ' . $years . ' year(s), '.$hours.' hour(s),
'.$minutes.' minute(s), '.$seconds.' second(s) </b>
</div><br />';
echo '</center>';
?>