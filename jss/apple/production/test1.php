<?php

// My device token here (without spaces):
//$deviceToken = ' e8a679ebcd03f54fafdee15d65bc839cc73d79b7c7ce4b1b8b298522793fe84c ';

// My private key's passphrase here:
//$passphrase = '1234';

$device = 'cf3668b16beda6eaf8113ceea8034323744c511ec03da00d658b0c38ddf2f1c1';
$payload['aps'] = array('alert' => 'Hello I am testing the server code ....', 'badge' => 1, 'sound' => 'default');
$payload = json_encode($payload);

$options = array('ssl' => array(
  'local_cert' =>'planpiper.pem'
));



$streamContext = stream_context_create();
stream_context_set_option($streamContext, $options);
$apns = stream_socket_client('ssl://gateway.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);

$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
fwrite($apns, $apnsMessage);
fclose($apns);
?>