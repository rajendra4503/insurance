<html>

<head>
  <title>Automatic time zone detection using JavaScript</title>
  <!-- Script by Josh Fraser (http://www.onlineaspect.com) -->
  <script language='javascript' src='get_user_timezone.js'></script>
</head>

<body>

<a href="index.php">Back</a>
<br /><br />

<center>

<h3>Automatic time zone detection using JavaScript</h3>

<select name='timezone' id='timezone' style='width:350px'>
	<option value='-12:00,0'>(-12:00) International Date Line West</option>
	<option value='-11:00,0'>(-11:00) Midway Island, Samoa</option>
	<option value='-10:00,0'>(-10:00) Hawaii</option>
	<option value='-09:00,1'>(-09:00) Alaska</option>
	<option value='-08:00,1'>(-08:00) Pacific Time (US & Canada)</option>
	<option value='-07:00,0'>(-07:00) Arizona</option>
	<option value='-07:00,1'>(-07:00) Mountain Time (US & Canada)</option>
	<option value='-06:00,0'>(-06:00) Central America, Saskatchewan</option>
	<option value='-06:00,1'>(-06:00) Central Time (US & Canada), Guadalajara, Mexico city</option>
	<option value='-05:00,0'>(-05:00) Indiana, Bogota, Lima, Quito, Rio Branco</option>
	<option value='-05:00,1'>(-05:00) Eastern time (US & Canada)</option>
	<option value='-04:00,1'>(-04:00) Atlantic time (Canada), Manaus, Santiago</option>
	<option value='-04:00,0'>(-04:00) Caracas, La Paz</option>
	<option value='-03:30,1'>(-03:30) Newfoundland</option>
	<option value='-03:00,1'>(-03:00) Greenland, Brasilia, Montevideo</option>
	<option value='-03:00,0'>(-03:00) Buenos Aires, Georgetown</option>
	<option value='-02:00,1'>(-02:00) Mid-Atlantic</option>
	<option value='-01:00,1'>(-01:00) Azores</option>
	<option value='-01:00,0'>(-01:00) Cape Verde Is.</option>
	<option value='00:00,0'>(00:00) Casablanca, Monrovia, Reykjavik</option>
	<option value='00:00,1'>(00:00) GMT: Dublin, Edinburgh, Lisbon, London</option>
	<option value='+01:00,1'>(+01:00) Amsterdam, Berlin, Rome, Vienna, Prague, Brussels</option>
	<option value='+01:00,0'>(+01:00) West Central Africa</option>
	<option value='+02:00,1'>(+02:00) Amman, Athens, Istanbul, Beirut, Cairo, Jerusalem</option>
	<option value='+02:00,0'>(+02:00) Harare, Pretoria</option>
	<option value='+03:00,1'>(+03:00) Baghdad, Moscow, St. Petersburg, Volgograd</option>
	<option value='+03:00,0'>(+03:00) Kuwait, Riyadh, Nairobi, Tbilisi</option>
	<option value='+03:30,0'>(+03:30) Tehran</option>
	<option value='+04:00,0'>(+04:00) Abu Dhadi, Muscat</option>
	<option value='+04:00,1'>(+04:00) Baku, Yerevan</option>
	<option value='+04:30,0'>(+04:30) Kabul</option>
	<option value='+05:00,1'>(+05:00) Ekaterinburg</option>
	<option value='+05:00,0'>(+05:00) Islamabad, Karachi, Tashkent</option>
	<option value='+05:30,0'>(+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
	<option value='+05:45,0'>(+05:45) Kathmandu</option>
	<option value='+06:00,0'>(+06:00) Astana, Dhaka</option>
	<option value='+06:00,1'>(+06:00) Almaty, Nonosibirsk</option>
	<option value='+06:30,0'>(+06:30) Yangon (Rangoon)</option>
	<option value='+07:00,1'>(+07:00) Krasnoyarsk</option>
	<option value='+07:00,0'>(+07:00) Bangkok, Hanoi, Jakarta</option>
	<option value='+08:00,0'>(+08:00) Beijing, Hong Kong, Singapore, Taipei</option>
	<option value='+08:00,1'>(+08:00) Irkutsk, Ulaan Bataar, Perth</option>
	<option value='+09:00,1'>(+09:00) Yakutsk</option>
	<option value='+09:00,0'>(+09:00) Seoul, Osaka, Sapporo, Tokyo</option>
	<option value='+09:30,0'>(+09:30) Darwin</option>
	<option value='+09:30,1'>(+09:30) Adelaide</option>
	<option value='+10:00,0'>(+10:00) Brisbane, Guam, Port Moresby</option>
	<option value='+10:00,1'>(+10:00) Canberra, Melbourne, Sydney, Hobart, Vladivostok</option>
	<option value='+11:00,0'>(+11:00) Magadan, Solomon Is., New Caledonia</option>
	<option value='+12:00,1'>(+12:00) Auckland, Wellington</option>
	<option value='+12:00,0'>(+12:00) Fiji, Kamchatka, Marshall Is.</option>
	<option value='+13:00,0'>(+13:00) Nuku'alofa</option>
</select>

</center>


</body>

</html>