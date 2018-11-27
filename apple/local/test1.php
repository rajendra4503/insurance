<?php

// My device token here (without spaces):
//$deviceToken = '7bc45c52ff9d726f4ac9296e12944659589429ac0bbae3e245e2d9610afef912';

// My private key's passphrase here:
// $passphrase = '';

// $device = 'cf3668b16beda6eaf8113ceea8034323744c511ec03da00d658b0c38ddf2f1c1';
// $payload['aps'] = array('alert' => 'asdfgf', 'badge' => 1, 'sound' => 'default');
// $payload = json_encode($payload);

// $options = array('ssl' => array(
//   'local_cert' =>'planpiper.pem'
// ));


// $streamContext = stream_context_create();
// stream_context_set_option($streamContext, $options);
// $apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);

// $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
// $check = fwrite($apns, $apnsMessage);
// fclose($apns);

//new test

// $device = 'bd155a3ba74e73ec9c1d6ed1839c0e99dee8b2b9f1a1d33762a0773cab6cc967';

// $payload['aps'] = array('alert' => 'Hello I am testing the server code ....', 'badge' => 1, 'sound' => 'default');

// $payload = json_encode($payload);



// $options = array('ssl' => array(
//   'local_cert' =>'planpiperlatest.pem'
// ));


// $streamContext = stream_context_create();

// stream_context_set_option($streamContext, $options);

// $apns = stream_socket_client('ssl://gateway.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);


// if (!$apns) {
//     print "Failed to connect $err $errstr\n";
//     return;
// } else {
//     print "Connection OK<br>";
// }

// $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;

// $result = fwrite($apns, $apnsMessage);

// fclose($apns);

// if (!$result)
// 			echo 'Message not delivered' . PHP_EOL;
// 		else
// 			echo 'Message successfully delivered' . PHP_EOL;

//test another


$deviceToken = 'bd155a3ba74e73ec9c1d6ed1839c0e99dee8b2b9f1a1d33762a0773cab6cc967';

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


// $body['aps'] = array(
//     'alert' => array(
//         'body' => $message,
//         'action-loc-key' => 'Bango App',
//     ),
//     'badge' => 2,
//     'sound' => 'default',
//     );


$payload['aps'] = array('alert' => 'Hello I am testing the server code ....', 'badge' => 1, 'sound' => 'default');

$payload = json_encode($payload);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;


$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
    echo 'Message not delivered' . PHP_EOL;
else
    echo 'Message successfully delivered' . PHP_EOL;

fclose($fp);

?>
