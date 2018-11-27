<?php
function get_machine_ips()
{
$ips = array();
exec("ifconfig", $catch);
	foreach($catch as $line)
	{
		//echo $line."<br>";
		echo "<pre>";print_r($line);
		if (eregi('ether', $line))
		{
		$line = str_replace(" ", ":", $line);
		$line = explode(":", $line);
		$line = array_filter($line);
			foreach ($line as $v)
			{
				if (ip2long($v))
				{
				$ips[] = $v;
				}
			}
		}
	}
return $ips;
}
get_machine_ips();
?>