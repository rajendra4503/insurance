<?php
// Put your device token here (without spaces):
//$deviceToken = "9da81c53b4b81a5852afb0c0ce364e09603c820cb05d04528547b0e311ec0dda";

// // Put your private key's passphrase here:
//$passphrase = '';

// $message 	= "Hi Vishnu...";
// $userid 	= 123;
// $flag 		= "plan_update";
// $report_id 	= "";
	
	$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', 'apple/local/planpiper.pem');
			//stream_context_set_option($ctx, 'ssl', 'local_cert', '/usr/local/www/apache24/data/appmantras/planpiperv1/apple/local/PlanPiperFinalCert.pem');
			//stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
			
			// Open a connection to the APNS server
			$fp = stream_socket_client(
				'ssl://gateway.sandbox.push.apple.com:2195', $err,
				$errstr, 120, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			if (!$fp)
				exit("Failed to connect: $err $errstr" . PHP_EOL);
			
			'Connected to APNS' . PHP_EOL;
			
			// Create the payload body
			$body['aps'] = array(
				'alert' 	=> $message,
				'userid' 	=> $userid,
				'report_id'	=> $report_id,
				'flag'  	=> $flag,
				'sound' 	=> 'default'
				);
			//echo "<pre>";print_r($body);exit;
			// Encode the payload as JSON
			$payload = json_encode($body);
			
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));
			
			if (!$result)
			{
				//echo 0;
				'Message not delivered' . PHP_EOL;
			} 
			else
			{
				//echo 1;
				'Message successfully delivered' . PHP_EOL;
			}
				 
			//echo "{".json_encode('BNI_NOTIFICATION').':'.json_encode("1")."}";
			// Close the connection to the server
			fclose($fp);
	

?>
