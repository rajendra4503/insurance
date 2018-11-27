<?php  
$ipAddress=$_SERVER['REMOTE_ADDR'];
$macAddr=false;

#run the external command, break output into lines
$arp=`arp -a $ipAddress`;
$lines=explode("\n", $arp);
echo "<pre>";print_r($lines);
#look for the output line describing our IP address
foreach($lines as $line)
{
	//echo $line."<br>";
   $cols=preg_split('/\s+/', trim($line));
   if ($cols[0]==$ipAddress)
   {
       echo $macAddr=$cols[1]."<br>";
   }
} 
?>  