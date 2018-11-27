<?php
$title_regex = '%<title>(.+)<\/title>%i';
$page = file_get_contents("http://www.planpiper.com");
$matches = array();
 if (preg_match($title_regex, $page, $matches) && isset($matches[1]))
$title = $matches[1];
else
$title = "Not Found";
echo $title;
?>