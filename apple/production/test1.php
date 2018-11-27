<?php

// My device token here (without spaces):
//$deviceToken = ' e8a679ebcd03f54fafdee15d65bc839cc73d79b7c7ce4b1b8b298522793fe84c ';

// My private key's passphrase here:
//$passphrase = '1234';



$device = '497e10c788820dd20e78d424a678250b63c72421d9d3840b2fd46cfc32771986';

$payload['aps'] = array('alert' => 'Hello I am testing the server code ....', 'badge' => 1, 'sound' => 'default');

$payload = json_encode($payload);



$options = array('ssl' => array(
  'local_cert' =>'planpiperlatest.pem'
));


$streamContext = stream_context_create();

stream_context_set_option($streamContext, $options);

$apns = stream_socket_client('ssl://gateway.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);


if (!$apns) {
    print "Failed to connect $err $errstr\n";
    return;
} else {
    print "Connection OK<br>";
}

$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;

$result = fwrite($apns, $apnsMessage);

fclose($apns);

if (!$result)
			echo 'Message not delivered' . PHP_EOL;
		else
			echo 'Message successfully delivered' . PHP_EOL;

//test another


$deviceToken = '497e10c788820dd20e78d424a678250b63c72421d9d3840b2fd46cfc32771986';

$passphrase = '12345';

$message = 'Your message';


$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'planpiperlatest.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
    exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;


$body['aps'] = array(
    'alert' => array(
        'body' => $message,
        'action-loc-key' => 'Bango App',
    ),
    'badge' => 2,
    'sound' => 'oven.caf',
    );

$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;


$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
    echo 'Message not delivered' . PHP_EOL;
else
    echo 'Message successfully delivered' . PHP_EOL;

fclose($fp);


?>