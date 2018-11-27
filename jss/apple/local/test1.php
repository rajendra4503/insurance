<?php

// My device token here (without spaces):
//$deviceToken = '7bc45c52ff9d726f4ac9296e12944659589429ac0bbae3e245e2d9610afef912';

// My private key's passphrase here:
$passphrase = '';

$device = 'cf3668b16beda6eaf8113ceea8034323744c511ec03da00d658b0c38ddf2f1c1';
$payload['aps'] = array('alert' => 'asdfgf', 'badge' => 1, 'sound' => 'default');
$payload = json_encode($payload);

$options = array('ssl' => array(
  'local_cert' =>'planpiper.pem'
));



$streamContext = stream_context_create();
stream_context_set_option($streamContext, $options);
$apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);

$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
$check = fwrite($apns, $apnsMessage);
fclose($apns);
?>