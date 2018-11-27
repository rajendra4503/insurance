<?php
$dbhost1 		= 'localhost';
$dbuser1 		= 'root';
$dbpass1 		= '';
$connection1 	= mysql_connect($dbhost1, $dbuser1, $dbpass1);
$db_name1 		= mysql_select_db('planpiper_intl',$connection1) or die(mysql_error());

/*if($db_name2)
{
	echo "YES";
}
else
{
	echo "NO";
}*/


function table_exists($tablename, $database = false) {
    if(!$database) {
    $res = mysql_query("SELECT DATABASE()");
    $database = mysql_result($res, 0);
    }
    $res = mysql_query("
    SELECT COUNT(*) AS count 
    FROM information_schema.tables 
    WHERE table_schema = '$database' 
    AND table_name = '$tablename'
    ");
    return mysql_result($res, 0) == 1;
  }

  function curPageName() {
    return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);  
  }

?>
