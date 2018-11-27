<?php
include('include/configinc.php');
$by 	= (empty($_REQUEST['by']))    		? 'FirstName' 	: mysql_real_escape_string(trim($_REQUEST['by']));
$order 	= (empty($_REQUEST['order']))    	? 'ASC' 		: mysql_real_escape_string(trim($_REQUEST['order']));

echo $by ." - ".$order;

// order the query
$result = mysql_query("select * from USER_DETAILS order by $by $order");

echo "<table border='1'><tr>";
if($by == 'FirstName' && $order == 'ASC')
{
  echo '<th><a href="?by=FirstName&order=DESC"><img src="images/up1.png" width="20" height="20">First Name</img></th>';
}
else
{
  echo '<th><a href="?by=FirstName&order=ASC"><img src="images/down1.png" width="20" height="20">First Name</img></th>';
}

if($by == 'LastName' && $order == 'ASC')
  echo '<th><a href="?by=LastName&order=DESC"><img src="images/up1.png" width="20" height="20">Last Name</img></th>';
else
  echo '<th><a href="?by=LastName&order=ASC"><img src="images/down1.png" width="20" height="20">Last Name</img></th>';

echo '</tr>';

while($row = mysql_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['FirstName'] . "</td>";
  echo "<td>" . $row['LastName'] . "</td>";
  echo "</tr>";
}
echo "</table>";

?>  