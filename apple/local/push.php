<?php
//$passphrase = '12345';
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'apple/local/planpiperlatest.pem');
//stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
    exit("Failed to connect: $err $errstr" . PHP_EOL);
echo 'Connected to APNS' . PHP_EOL;
$body['aps'] = array(
		'alert' 	=> $message,
		'userid' 	=> $userid,
		'report_id'	=> $report_id,
		'flag'  	=> $flag,
		'sound' 	=> 'default'
	);

$payload = json_encode($body);
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
$result = fwrite($fp, $msg, strlen($msg));

	if (!$result)
	{
	//echo "Nopz";
	'Message not delivered' . PHP_EOL;
	}	 
	else
	{
	//echo "Yup";
	//echo "<pre>";print_r($result);
	'Message successfully delivered' . PHP_EOL;
	}
fclose($fp);
?>
