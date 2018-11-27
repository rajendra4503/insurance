<?php
$dbhost1 		= 'localhost';
$dbuser1 		= 'root';
$dbpass1 		= '5Em4-M9mq^Wjc9';
$connection1 	= mysql_connect($dbhost1, $dbuser1, $dbpass1);
$db_name1 		= mysql_select_db('just_smart_study',$connection1) or die(mysql_error());

/*if($db_name2)
{
	echo "YES";
}
else
{
	echo "NO";
}*/
?>
