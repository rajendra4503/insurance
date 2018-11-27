<?php
require_once("userip/ip.codehelper.io.php");
require_once("userip/php_fast_cache.php");

$_ip = new ip_codehelper();

$real_client_ip_address = $_ip->getRealIP();
$visitor_location       = $_ip->getLocation($real_client_ip_address);

$guest_ip   = $visitor_location['IP'];
$guest_country = $visitor_location['CountryName'];
$guest_city  = $visitor_location['CityName'];
$guest_state = $visitor_location['RegionName'];

echo "IP Address: ". $guest_ip. "<br/>";
echo "Country: ". $guest_country. "<br/>";
echo "State: ". $guest_state. "<br/>";
echo "City: ". $guest_city. "<br/>";

?>